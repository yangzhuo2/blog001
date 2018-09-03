<?php
namespace libs;
class Mail {    
 public $mailer;
 public function __construct(){
    // 设置邮件服务器账号
    $transport = (new \Swift_SmtpTransport('smtp.163.com', 25))  // 邮件服务器IP地址和端口号
    ->setUsername('13529419714@163.com')       // 发邮件账号
    ->setPassword('yz123456');      // 授权码
    // 创建发邮件对象
    $this->mailer = new \Swift_Mailer($transport);;

 }
 public function send($title,$content,$to){
    $message = new \Swift_Message();
    $message->setSubject($title)
    ->setFrom(['13529419714@163.com' => '全栈'])
    ->setTo([$to[0], 
             $to[0] => $to[1] 
             ])
    ->setBody($content,'text/html')
    ;
    
    // Send the message
     $this->mailer->send($message);
    }
    
 }
