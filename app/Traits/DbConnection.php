<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use PDO;

trait DbConnection
{
    private function getBaseConnectionConfig($database) {
        // Request $request
        return [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => $database,
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : []
        ];
    }

    private function getNewConnectionConfig() {
        // Request $request
        return $this->getBaseConnectionConfig('tikate_121');
    }

    private function getOldConnectionConfig() {
        // Request $request
        return $this->getBaseConnectionConfig(env('DB_DATABASE', 'tikate_customerList'));
    }

    private function setNewConnection() {
        DB::purge('mysql');
        Config::set('database.connections.mysql', $this->getNewConnectionConfig());
        DB::setDefaultConnection('mysql');
    }

    private function setOldConnection() {
        DB::purge('mysql');
        Config::set('database.connections.mysql', $this->getOldConnectionConfig());
        DB::setDefaultConnection('mysql');
    }
}
