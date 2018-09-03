<?php 
namespace controllers;
use libs\Mail;
class MailController { 
    
    //连接redis   设置socket 永不超时
    public function send(){
        $redis = new \Predis\Client([
            'scheme' => 'tcp',
            'host'   => '127.0.0.1',
            'port'   => 6379,
            ]);
        $mail = new Mail;
        ini_set("default_socket_timeout",-1);
        echo "已经启动了发邮件队列  \r\n";
        while(1){
            $data = $redis->brpop('email',0);
            $message  = json_decode($data[1],true);

            $mail->send($message['title'],$message['content'],$message['from']);
            echo "成功发送一条";
        }
    }
    
}