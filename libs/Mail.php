<?php
namespace libs;
class Mail {    
 public $mailer;
 public function __construct(){
    $mail = config('mail');

    // 设置邮件服务器账号
    $transport = (new \Swift_SmtpTransport($mail['host'], $mail['port']))  // 邮件服务器IP地址和端口号
    ->setUsername($mail['name'])       // 发邮件账号
    ->setPassword($mail['pass']);      // 授权码
    // 创建发邮件对象
    $this->mailer = new \Swift_Mailer($transport);

 }
 public function send($title,$content,$to){
        $mail = config('mail');
        $message = new \Swift_Message();
        $message->setSubject($title)
        ->setFrom([$mail['from_email'] => $mail['from_name']])
        ->setTo([$to[0], 
                $to[0] => $to[1] 
                ])
        ->setBody($content,'text/html')
        ;
        if($mail['mode']=='debug'){
            $message = $message->toString();
            $log = new Log('email');
            $log->log($message);
        }
        else {
            $this->mailer->send($message);

        }
    }
    
 }