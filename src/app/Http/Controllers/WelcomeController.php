<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDO;
use Utils\Hash;

class WelcomeController extends Controller
{
    public function index(){
        array_map(function($item){
            $database = $item['range'];
            $connection = $item['master'];
            foreach ($database as $value) {
                $nameDatabase = create_name_database($value);
                return [$connection => [
                    'driver' => 'mysql',
                    'url' => env('DATABASE_URL'),
                    'host' => env('DB1_HOST', '127.0.0.1'),
                    'port' => env('DB1_PORT', '3306'),
                    'database' => $nameDatabase,
                    'username' => env('DB1_USERNAME', 'forge'),
                    'password' => env('DB1_PASSWORD', ''),
                    'unix_socket' => env('DB1_SOCKET', ''),
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                    'prefix_indexes' => true,
                    'strict' => true,
                    'engine' => null,
                    'options' => extension_loaded('pdo_mysql') ? array_filter([
                        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
                    ]) : [],
                ]];
            }

        },config('shard.default'));
    }
}
