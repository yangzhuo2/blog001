<?php
namespace models;
use PDO;
class Base {
    
    static $pdo= null;
    public function __construct(){

        if(self::$pdo === null ){
            $mysql = config('db');
            self::$pdo = new PDO("mysql:host={$mysql['host']};dbname={$mysql['dbname']}", $mysql['user'], $mysql['password']);
            self::$pdo->exec("SET NAMES {$mysql['charset']}");
        }
    }
    public function startTrans(){
        self::$pdo->exec('start transaction');
    
    }
    public function commit(){
        self::$pdo->exec("commit");
    }
    public function rollback(){
        self::$pdo->exec("rollback");
    }
}