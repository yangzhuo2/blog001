<?php
define("ROOT",dirname(__FILE__).'/../');
var_dump(ROOT);
function autoload($class){
    // var_dump($class);
    $path = str_replace('\\',"/",ROOT.$class.".php");
    require $path;
}
spl_autoload_register('autoload');

if(isset($_SERVER["PATH_INFO"])){
    $arr = explode("/",$_SERVER['PATH_INFO']);
    $controller = ucfirst($arr[1]."Controller");
    $action = $arr[2];   
}
else {
    $controller = "IndexController";
    $action = "index";
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