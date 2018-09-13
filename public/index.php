<?php
ini_set("session.save_handler","redis"); //使用reids 保存session
ini_set("session.save_path","tcp://127.0.0.1:6379?database=3"); //设置redis服务器的地址、端口、使用的数据库
ini_set('session.gc_maxlifetime',6000);//设置session保存时间
session_start();
// if($_SERVER['REQUEST_METHOD']=='POST'){

//     if(!isset($_POST['_token'])){
//         die('违规操作');
//     }
//     if($_POST['_token']!= $_SESSION['_token']){
//         die("违规操作");
//     }
// }

define("ROOT",dirname(__FILE__).'/../');
require(ROOT."vendor/autoload.php");//引入composer 自动加载文件


function autoload($class){
    // var_dump($class);
    $path = str_replace('\\',"/",ROOT.$class.".php");
    
    require $path;
}
spl_autoload_register('autoload');

if(php_sapi_name()=="cli"){

    $controller = ucfirst($argv[1])."Controller";
    $action = $argv[2];
}
    
else{ 
    if(isset($_SERVER["PATH_INFO"])){
        $arr = explode("/",$_SERVER['PATH_INFO']);      
        $controller = ucfirst($arr[1]."Controller");
        $action = $arr[2];   
    }
    else {
        $controller = "IndexController";
        $action = "index";
    }
}
$fullcontroller  = "controllers\\".$controller;
$usercontroller = new $fullcontroller;
$usercontroller->$action();

function view($dir,$data=[]){
    extract($data);
    $dir =str_replace(".","/",$dir);
    $path =  ROOT."views/".$dir.".html";
    require $path;
}
function config($name){
    static $config = null;
    if($config===null){
        $config = require (ROOT."config.php");
    }
    return $config[$name];
}   
function redirect($path){
    header("Location:".$path);
    exit;
}
function back(){
    redirect($_SERVER['HTTP_REFERER']);
}

// 提示方式
function message($message,$url,$type,$time = 3){

    if($type == 0 ){
        echo "<script>alert({$message});location.href=".$url."</script>";
        exit;
    }
    else if($type==1){
        view('common.success',[
            "message"=>$message,
            "url"=>$url,
            "time"=>$time,
        ]);
    }
    else if($type==2){
        $_SESSION['_MESS_'] = $message;
        redirect($url);
    }
}
//生成token令牌
function csrf(){

    if(!isset($_SESSION['_token'])){
        $token = md5(mt_rand(60,9995).microtime());
        $_SESSION['_token'] = $token;
    }
    return $_SESSION['_token'];
}
//生成token input 
function csrf_filed(){
    echo "<input name='_token' type='hidden' value='".csrf()."'>";
}
