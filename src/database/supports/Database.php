<?php

namespace Database\Supports;

use Closure;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
class Database {
    public static function createDatabase()
    {
        return static::a();
    }
    public static function dropDatabase(){
        return static::a(true);
    }
    private static function a($drop = false){
        $action = $drop ? "DROP DATABASE IF EXISTS " : "CREATE DATABASE IF NOT EXISTS ";
        $notificationName = $drop ? "Drop" : "Create";
        static::loopConnectionsShard(
            function($connection) use ($action,$notificationName,$drop){
                $databases = config_shard(null,'database');
                $databases =array_filter($databases,function($item) use ($connection){
                    return $item['host'] == $connection;
                });
                foreach ($databases as $nameDatabase => $value) {
                    $username = env('DB_SHARD_USERNAME','forge');
                    // if(!$drop) DB::connection($connection)->statement("GRANT ALL PRIVILEGES ON *.* TO '".$username."'@'".$connection."'");
                    DB::connection($connection)->statement($action . $nameDatabase);
                    dump("$notificationName table: ".$nameDatabase);
                }
            }
        ,'host');
        return "$notificationName database sharding successfully!";
    }
    public static function createTable(string $nameTable ,Closure $closure){
        static::loopConnectionsShard(
            function($connection) use ($nameTable,$closure){
                $schema = Schema::connection($connection);
                $schema->blueprintResolver(function ($table, $callback) {
                    return new BlueprintExtended($table, $callback);
                });
                $schema->create($nameTable, $closure);
            }
        );
    }
    public static function dropTable(string $nameTable){
        static::loopConnectionsShard(
            function($connection) use ($nameTable){
                Schema::connection($connection)->dropIfExists($nameTable);
            }
        );
    }
    public static function loopConnectionsShard(Closure $callback,$type = 'database'){
        $connections = config_shard(null,$type);
        foreach ($connections as $connection => $value) {
            $callback($connection);
        }
    }
}