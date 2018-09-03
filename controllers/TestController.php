<?php 
namespace controllers;
use libs\Mail;
class TestController{
    public function test(){
         $mailer = new Mail;
         $user = ['754522173@qq.com','754522173'];
         $mailer->send("王一凡可真是个狗鸡儿",'王一凡吃屎，不是个好东西',$user);
    }
}