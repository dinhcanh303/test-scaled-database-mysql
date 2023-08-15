<?php

namespace Database\Create;

use Closure;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
class Database {
    public static function create()
    {
        $shardConfig = static::getShardConfigDefault();
        $results = [];
        foreach ($shardConfig as $item) {
            $connection = $item['master'];
            $databases = $item['range'];
            $temp = array_map(function($value) use($connection){
                $nameDatabase = static::createNameDatabase($value);
                return DB::connection($connection)->statement("CREATE DATABASE IF NOT EXISTS " . $nameDatabase);
            },$databases);
            $results[$connection] = $temp;
        }
        return $results;
    }
    public static function createTable(string $nameTable ,Closure $closure){
        $shardConfig = static::getShardConfigDefault();
        foreach ($shardConfig as $value) {
            $connection = $value['master'];
            static::setConfigTemp($value,$connection,function() use ($connection,$nameTable,$closure){
                dump(config("database.connections.$connection.database"));
                Schema::connection($connection)->create($nameTable, $closure);
                dump("Created");
            });
        }
    }
    public static function createTableByDatabase(){
        $shardConfig = static::getShardConfigDefault();
        foreach ($shardConfig as $value) {
            $connection = $value['master'];
            Artisan::call('migrate',['--database' => $connection]);
        }
    }
    public static function dropTable(string $nameTable){
        $shardConfig = static::getShardConfigDefault();
        foreach ($shardConfig as $value) {
            $connection = $value['master'];
            static::setConfigTemp($value,$connection,function() use ($connection,$nameTable){
                Schema::connection($connection)->dropIfExists($nameTable);
            });
        }
    }
    private static function setConfigTemp($value,$connection,Closure $closure){
        
        $databases = $value['range'];
        $databaseNameConfig = config("database.connections.$connection.database");
        array_map(function($index) use($connection,$closure){
            $nameDatabase = static::createNameDatabase($index);
            config(["database.connections.$connection.database" => $nameDatabase]);
            $closure();
            config(["database.connections.$connection.database" => null]);
        },$databases);
    }

    private static function getShardConfigDefault(){
        return config('shard.default');
    }
    private static function createNameDatabase($index){
        return 'db'.sprintf("%05d",$index).'a';
    }
}