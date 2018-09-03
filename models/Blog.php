<?php
namespace models;
use PDO;

class Blog {

    public $pdo;
    
    function __construct(){
        $this->pdo = new PDO("mysql:host=localhost;dbname=blog","root","7545");
        $this->pdo->exec("SET NAMES utf8");
    }

    public function search_page(){
        
        $where = 1;
        if(isset($_GET['keywords']) && $_GET['keywords']){
            $where .= "  AND (title like '%{$_GET['keywords']}%' )  ";
        }
        if(isset($_GET['start_time'])&& $_GET['start_time']){
            $starttime = strtotime($_GET['start_time']."-1-1 0:0:0");
            $where .= "  AND  unix_timestamp(created_at) >= ({$starttime}) ";
        }
        if(isset($_GET['end_time'])&& $_GET['end_time']){
            $endtime = strtotime($_GET['end_time']."-1-1 0:0:0");
            $where .= "  AND   unix_timestamp(created_at)<= ({$endtime})";
        }
        function getUrlParms($canshu = []){
            foreach($canshu as $v){
                unset($_GET[$v]);
            }
            $str = "";
            foreach($_GET as $k=>$v){
                $str .= "{$k}=$v&";
            }
            return $str;
        }
        $nowPage = isset($_GET['nowpage'])?($_GET['nowpage'])*1:1;
        $link = "nowpage";
        $link =  getUrlParms(["$link"]);
        $sql = "SELECT COUNT(*) FROM blogs WHERE  ";
        $pdos = $this->pdo->query($sql.$where);
        $num  = $pdos->fetch(PDO::FETCH_COLUMN);
        $page = 15;
        $limit = " LIMIT ".($nowPage-1)*$page.",{$page}";
        $pages = ceil($num/$page);
        $str = "";
        for($i=1;$i<$pages;$i++){
            $str .= "<a href=?".$link."nowpage={$i}".">{$i}</a>";
        }
        $sql = "SELECT * FROM blogs WHERE ";
        $pdos = $this->pdo->query($sql.$where.$limit);
        $arr = $pdos->fetchAll(PDO::FETCH_ASSOC);
        return array($arr,$str);
    }
    
    public function static_content(){
        $pdos  = $this->pdo->query("SELECT * FROM blogs ");
        $arr = $pdos->fetchAll(PDO::FETCH_ASSOC);
        return $arr;
    }
    public function index2htlm(){
        $pdos  = $this->pdo->query("SELECT * FROM blogs WHERE is_show = 1 ORDER BY created_at desc LIMIT 20");
        $arr = $pdos->fetchAll(PDO::FETCH_ASSOC);
        ob_start();
        view('index.index',[
            'blogs'=>$arr,
        ]);
        $str = ob_get_contents();
        file_put_contents(ROOT."public/index.html",$str);
        
    }
    
    public function update_id($id){
        $redis = new \Predis\Client([
            'scheme' => 'tcp',
            'host'   => '127.0.0.1',
            'port'   => 6379,
            ]);
           $key = "blog-".$id;
           if($redis->hexists('display_num',$key)){
               $num = $redis->hincrby('display_num',$key,1);
               return $num;
           }else {
                $pdos = $this->pdo->prepare("SELECT display from blogs WHERE id = ?");
                $pdos->execute(array($id));
                $num = $pdos->fetch(PDO::FETCH_COLUMN);
                $num++;
                $redis->hsetnx("display_num",$key,$num);
                return $num;
           }
            
    }
    public function redistomysql(){
        $redis = new \Predis\Client([
            'scheme' => 'tcp',
            'host'   => '127.0.0.1',
            'port'   => 6379,
            ]);
        $data = $redis->hgetall("display_num");
        // var_dump ($data);
        foreach($data as $k => $v){
            $arr = explode("-",$k);
            $sql = " UPDATE blogs SET display = $v WHERE id = $arr[1]";
            $num  = $this->pdo->exec($sql);
        }
    }
}