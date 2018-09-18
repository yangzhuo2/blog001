<?php 
namespace controllers;
use models\User;
use models\Order;
use libs\Redis;
use Intervention\Image\ImageManagerStatic as Images;
class UserController
{
   public function regist(){
       view('user.regist');
   }
   public function zhuce(){
       $email = $_POST['email'];
       $pwd = $_POST['pwd'];
       $code = md5(rand(5,999999));
       
       $redis = Redis::getinstance();
       $value = [
           "email"=>$email,
           "password"=>$pwd
       ];
       $value = json_encode($value);
       $key = "temp_user:".$code;
       $redis->setex($key,300,$value);
       
       $name = explode("@",$email);
       $from  = [$email,$name[0]];
       $message =[
           "title"=>"激活智聊账号",
           "content"=>"<a href='http://localhost:9999/user/active_hao?code='".$code.">点击激活</a> 如果不让跳转 请复制以下链接点击激活  htttp://localhost:9999/user/active_hao?code={$code}  ",
           'from'=>$from
       ];
       $message = json_encode($message);
       $redis->lpush("email",$message);
       echo "ok";
   }
   public function active_hao(){
       $code = $_GET['code'];
       $redis = Redis::getinstance();
       $key = "temp_user:".$code;
       $data = $redis->get($key);
       var_dump($data);
       if($data){
            $data = json_decode($data,true);
            $user = new User;
            $pwd = md5($data['password']);
            var_dump($pwd);
            $reg = $user->add($data['email'], $pwd);
            if($reg){
                $redis->del($key);
                message("激活成功",'/blog/index',2);
                
            }
       }else {
           echo "激活失败";
       }
   }
   public function login(){
       view('user.login');
   }
   public function dologin(){
      $email=  $_POST['email'];
      $pwd = $_POST['password'];
      $pwd = md5($pwd);
      $user = new User;
      $result = $user->login($email,$pwd);
      if($result){
            message("登录成功",'/blog/index',2);
      }else {
            message("登录失败",'/user/login',1);
          
      }
   }
   public function logout()
    {
        $_SESSION = [];
        message('退出成功！','/blog/index',2);
    }
   public function charge(){
       view('user.charge');
   }
   public function docharge(){
       $money = $_POST['money'];
       $order = new Order;
       $order->created($money);
       message('下单成功','/user/orders',2);    
   }
   public function orders(){
    $order = new Order;
    $data = $order->search();
       view('user.order',[
           'orders'=>$data[0],
           'str'=>$data[1]
       ]);
    }
   public function money(){
        $id = $_SESSION['id'];
        $user = new User;
        $money = $user->getMoney($id);
        $_SESSION['money'] = $money;
        echo $money;
        return $money;
    }
    public function face(){

        view('user.face');
    }
    public function setface(){
        
        $upload = \libs\Uploader::make();
        $sb = $upload->uploader('face','face');
        $path = "/uploads/".$sb;

        $images = Images::make(ROOT."public".$path);
        $images->crop((int)$_POST['x'],(int)$_POST['y'],(int)$_POST['w'],(int)$_POST['h']);
        $images->save(ROOT."public".$path);

        $user  = new User;
        $user->uploadFace($path);
        @unlink(ROOT.'public'.$_SESSION['avatar']);
        $_SESSION['avatar'] = $path;
        message('设置成功','/blog/index',2);

    }
}