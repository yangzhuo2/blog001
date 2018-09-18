<?php
namespace models;
use PDO;
class User extends Base
{   

    public function add($email,$pwd){
        $pdos = self::$pdo->prepare("INSERT INTO users(email,password) VALUES(?,?)");
        return $pdos->execute([
                        $email,
                        $pwd
        ]);
    }
    public function login($email,$pwd){
        $pdos = self::$pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
        $pdos->execute([$email,$pwd]);
        $data = $pdos->fetch(PDO::FETCH_ASSOC);
        // var_dump($data);die;
        if($data){
            $_SESSION['id'] = $data['id'];
            $_SESSION['email'] = $data['email'];
            $_SESSION['money'] = $data['money'];
            return true;
        }else{
            return false;
        }
    }   
    public function addMoney($money,$userid){
        $pdos = self::$pdo->prepare("UPDATE users SET money = money+? WHERE id = ?");
        return $pdos->execute([
            $money,
            $userid
        ]);
    }
    public function getMoney($id){
        $pdos = self::$pdo->prepare("SELECT money FROM users WHERE id = ?");
        $pdos->execute([$id]);
        return $pdos->fetch(PDO::FETCH_COLUMN);
    }
}