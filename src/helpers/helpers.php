<?php

if (! function_exists('create_name_database')) {
    function create_name_database($name,$type = 'a')
    {
        return 'db'.sprintf("%05d",$name).$type;
    }
}
if(!function_exists('config_shard')){
    function config_shard($configShard = null,$type = 'database'){
        $results = [];
        if(!in_array($type,['host','database'])) return [];
        $configShard = $configShard ? $configShard['default'] : config('shard.default');
        if(!$configShard) return [];
        foreach ($configShard as $config) {
            $database = $config['range'];
            $connection = $config['master'];
            if($type == 'host'){
                $results[$connection] = [
                    'driver' => 'mysql',
                    'url' => env('DATABASE_URL'),
                    'host' => $connection ?? env(null, '127.0.0.1'),
                    'port' => env('DB_SHARD_PORT', '3306'),
                    'database' => 'laravel',
                    'username' => env('DB_SHARD_USERNAME', 'forge'),
                    'password' => env('DB_SHARD_PASSWORD', ''),
                    'unix_socket' => env('DB_SHARD_SOCKET', ''),
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                    'prefix_indexes' => true,
                    'strict' => true,
                    'engine' => null,
                    'options' => extension_loaded('pdo_mysql') ? array_filter([
                        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
                    ]) : [],
                ];
            }else{
                foreach ($database as $value) {
                    $nameDatabase = create_name_database($value);
                    $results[$nameDatabase] = [
                        'driver' => 'mysql',
                        'url' => env('DATABASE_URL'),
                        'host' => $connection ?? env('DB'.$value.'_PORT', '127.0.0.1'),
                        'port' => env('DB_SHARD_PORT', '3306'),
                        'database' => $nameDatabase,
                        'username' => env('DB_SHARD_USERNAME', 'forge'),
                        'password' => env('DB_SHARD_PASSWORD', ''),
                        'unix_socket' => env('DB_SHARD_SOCKET', ''),
                        'charset' => 'utf8mb4',
                        'collation' => 'utf8mb4_unicode_ci',
                        'prefix' => '',
                        'prefix_indexes' => true,
                        'strict' => true,
                        'engine' => null,
                        'options' => extension_loaded('pdo_mysql') ? array_filter([
                            PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
                        ]) : [],
                    ];
                }
            }
        }
        return $results;
    }
}

