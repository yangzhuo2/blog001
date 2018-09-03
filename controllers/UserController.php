<?php 
namespace controllers;
use models\User;
class UserController
{
   public function regist(){
       view('users.regist');
   }
   public function zhuce(){
       $email = $_POST['email'];
       $pwd = $_POST['pwd'];
       $user=  new User;
       $reg = $user->add($email,$pwd);
       if(!$reg){
            die("注册失败");
       }
       $name = explode("@",$email);
        //    var_dump($name);
       $from  = [$email,$name[0]];
       $message =[
           "title"=>"王一凡吃屎",
           "content"=>"<a href=''>点击让王一凡吃屎，才能激活账号哟</a>",
           'from'=>$from
       ];
       $message = json_encode($message);
       $redis = new \Predis\Client([
        'scheme' => 'tcp',
        'host'   => '127.0.0.1',
        'port'   => 6379,
        ]);
        $redis->lpush("email",$message);
        echo "OK";
   }
}