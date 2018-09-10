<?php 
namespace controllers;
use libs\Mail;
use PDO;
use libs\Log;
class TestController{
    public function test(){
         $mailer = new Mail;
         $user = ['754522173@qq.com','754522173'];
         $mailer->send("王一凡可真是个狗鸡儿",'王一凡吃屎，不是个好东西',$user);
    }
    public function testlog(){
        $cc = config('mail');
        
        if($cc['mode']=='debug'){

            $log = new Log('email');
            $log->log("日志内容~");
        }
    }
    public function user(){
        $pdo = new PDO("mysql:host=localhost;dbname=blog","root","7545");
        $pdo->exec("set NAMES utf8");
        $pdo->exec('TRUNCATE user');
        for($i=0;$i<20;$i++){
            $email = rand(10000,99999999)."@163.com";
            $pwd= md5('123456');
            $pdo->exec("INSERT INTO user (email,password) VALUES('$email','$pwd')");
            
        }
    }
    public function blog(){
        $pdo = new PDO("mysql:host=localhost;dbname=blog","root","7545");
        $pdo->exec("set NAMES utf8");
        $pdo->exec('TRUNCATE blogs');
        for($i=0;$i<200;$i++){
            $title = $this->getChar(rand(50,200));
            $content= $this->getChar(rand(500,2000));
            $display = rand(2,500);
            $is_show = rand(0,1);
            $user_id = rand(1,20); 
            $date = rand(968238773,1536232373);
            $date = date("Y-m-d H:i:s",$date);

            $a =  $pdo->exec("INSERT INTO blogs (id,title,content,created_at,display,is_show,user_id) VALUES(null,'$title','$content','$date',$display,$is_show,$user_id)");
            var_dump($a);
        }

    }
    function getChar($num)  // $num为生成汉字的数量
   {
       $b = '';
       for ($i=0; $i<$num; $i++) {
           // 使用chr()函数拼接双字节汉字，前一个chr()为高位字节，后一个为低位字节
           $a = chr(mt_rand(0xB0,0xD0)).chr(mt_rand(0xA1, 0xF0));
           // 转码
           $b .= iconv('GB2312', 'UTF-8', $a);
       }
       return $b;
   }
}