<?php
namespace models;
use PDO;
class User
{   
    public $pdo;
    public function __construct()
    {
        // 取日志的数据
        $this->pdo = new PDO('mysql:host=127.0.0.1;dbname=blog', 'root', '7545');
        $this->pdo->exec('SET NAMES utf8');
    }
    public function add($email,$pwd){
        $pdos =$this->pdo->prepare("INSERT INTO user(email,password) VALUES(?,?)");
        return $pdos->execute([
                        $email,
                        $pwd
        ]);
    }

}