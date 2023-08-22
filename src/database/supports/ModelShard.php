<?php

namespace Database\Supports;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Utils\Hash;

abstract class ModelShard  {
    protected static  $table = null;
    protected static  $primaryKey = null;
    protected static $relationships = [
    ];
    public static function find(int|string $id){
        $connection = static::getDatabaseBalancedUsingHash(static::getCurrentUserId());
        return DB::connection($connection)->table(static::getTable())->find($id);
    }
    public static function findOrFail(int|string $id){
        if(static::find($id)) return static::find($id);
        return false;
    }
    public static function create(array $attributes = [],string|int|null $idParent = null,bool $idLocal = true){
        if(!is_array($attributes)) return false;
        $shardId = static::getShardId(static::getCurrentUserId());
        $connection = static::getDatabaseBalancedUsingHash($shardId);
        $typeId = static::getTypeId();
        static::compileData($attributes);
        $instanceId = static::insertGetIdByDBConnection(static::getTable(),$attributes,$connection);
        $instanceIdHashed = Hash::encode($shardId,$typeId,$instanceId) ?? false;
        if(empty(static::$relationships))return $instanceIdHashed;
        foreach (static::$relationships as $key => $relationship) {
            $attributesIntermediate = 
                static::getAttributeOfIntermediate(static::getIdRelationshipIndex0($idParent,$idLocal)
                    ,$instanceIdHashed,$relationship[0],static::class);
            if(static::checkSubClass($relationship[0])) static::insertGetIdByDBConnection($key,$attributesIntermediate,$connection);
            else static::insertGetIdByDBConnection($key,$attributesIntermediate);
        }
        return $instanceIdHashed;
    }
    public static function update(string|int $id,array $attributes){
        return static::updateOrDelete($id,$type='update',$attributes);
    }
    public static function delete(string|int $id){
        return static::updateOrDelete($id);
    }
    private static function updateOrDelete(string|int $id ,$type = 'delete',array $attributes = []){
        if(!is_array($attributes)) return false;
        static::compileData($attributes);
        $nameTable = static::getTable();
        $infoOfInstance =  Hash::decode($id,true);
        $shardIndex = $infoOfInstance['shard'];
        $localId = $infoOfInstance['local'];
        $connection = static::getDatabaseBalancedUsingHash($shardIndex);
        switch ($type) {
            case 'delete':
                return static::deleteDBConnection($localId,$nameTable,$connection);
            case 'delete':
                return static::updateDBConnection($localId,$nameTable,$attributes,$connection);
            default:
                return false;
        }
    }
    private static function getIdRelationshipIndex0(string|int|null $idParent,bool $idLocal){
        if(!$idParent) return static::getCurrentUserId();
        return $idLocal ? $idParent : static::getIdLocalByShard($idParent);
    }
    private static function getIdLocalByShard(string|int $idParent){
        return Hash::decode($idParent,'local')['local'];
    }
    private static function checkSubClass($className){
        return is_subclass_of($className,ModelShard::class);
    }
    private static function insertGetIdByDBConnection( string $nameTable,array $attributes,string|null $connection =null){
        return DB::connection($connection)
        ->table($nameTable)
        ->insertGetId($attributes);
    }
    private static function updateDBConnection(string|int $id,string $nameTable,array $attributes,string|null $connection =null){
        return DB::connection($connection)
        ->table($nameTable)
        ->where('id',$id)
        ->update($attributes);
    }
    private static function deleteDBConnection(string|int $id,string $nameTable,$connection =null){
        return DB::connection($connection)
        ->table($nameTable)
        ->delete($id);
    }
    private static function getCurrentUserId(){
        return Auth::id();
    }
    private static function getAttributeOfIntermediate($id1,$id2,$modelPath1,$modelPath2){
        return [
            static::getSingularBaseNameClass($modelPath1).'_id' => $id1,
            static::getSingularBaseNameClass($modelPath2).'_id' => $id2
        ];
    }
    private static function compileData(&$attributes){
        foreach ($attributes as $key => &$value) {
            if(gettype($value) == 'array') $value = json_encode($value);
        }
    }
    private static function convertDataUpdateToSqlStandard(array $attributes){
        $results = [];
        foreach ($attributes as $key => $value) {
            $results[] = $key ."=".static::convertValueByType($value);
        }
    }
    private static function convertValueByType($value){
        $typeValue = gettype($value);
            $result = "";
            switch ($typeValue) {
                case 'boolean':
                case 'integer':
                case 'double':
                case 'string':
                    $result = $value;
                    break;
                case 'array':
                case 'object':
                    $result = "'". json_encode($value)."'";
                    break;
                default:
                    # code...
                    break;
        return $result;
        }
    }
    private static function convertDataCreateToSqlStandard($data){
        $results = [];
        foreach ($data as $key => $value) {
            $results[] = static::convertValueByType($value);
        }
        return "(".join(', ',$results).")";
    }
    private static function getDatabaseBalancedUsingHash($indexDatabase){
       
        return create_name_database($indexDatabase);
    }
    private static function getTypeId(){
        $type = Str::singular(static::getTable());
        return config('shard.type_id')[$type] ?? false;
    }
    private static function getShardId($id){
        $size = config('shard.database_in_instance') * config('shard.database_in_instance');
        if($size == 0) return;
        return hexdec(substr(md5($id),0,10)) % $size;
    }
    public static function getTable(){
        return static::$table ?? static::getPluralBaseNameClass();
    }
    public static function getTableIntermediate($modelPath){
        return static::getPluralBaseNameClass(class_basename($modelPath));
    }
    public static function getPrimaryKey(){
        return static::$primaryKey ?? 'id';
    }
    private static function getPluralBaseNameClass($nameClass = null){
        $nameClass = $nameClass ?? static::class;
        return Str::snake(Str::pluralStudly(class_basename($nameClass)));
    }
    private static function getSingularBaseNameClass($nameClass = null){
        $nameClass = $nameClass ?? static::class;
        return Str::snake(Str::singular(class_basename($nameClass)));
    }
}