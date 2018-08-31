<?php

namespace controllers ;
use PDO;
class BlogController 
{
    public function index(){
        $pdo = new PDO("mysql:host=127.0.0.1;dbname=blog","root","7545");
        $pdo->exec("SET NAMES utf8");
      
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
            // var_dump($canshu,$_GET);
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
        echo $sql.$where;
        $pdos = $pdo->query($sql.$where);
        $num  = $pdos->fetch(PDO::FETCH_COLUMN);
        $page = 15;
        $limit = " LIMIT ".($nowPage-1)*$page.",{$page}";

        $pages = ceil($num/$page);

        $str = "";
        for($i=1;$i<$pages;$i++){
            $str .= "<a href=?".$link."nowpage={$i}".">{$i}</a>";
        }
        // var_dump($str);
        $sql = "SELECT * FROM blogs WHERE ";
       
        $pdos = $pdo->query($sql.$where.$limit);
        $arr = $pdos->fetchAll(PDO::FETCH_ASSOC);
       
       
        view("blogs.index",[
            "blog"=>$arr,
            "str"=>$str
            
        ]);
    }




    public function insert_sql(){
        $pdo = new PDO("mysql:host=127.0.0.1;dbname=blog","root","7545");
        $pdo->exec("SET NAMES utf8");
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
       
        
        for($i=0;$i<100;$i++){
            $title = getChar(rand(10,20));
            $content = getChar(rand(50,300));
            $created_at = date('Y-m-d H:i:s',rand(1035611552,1535611552));
            $dispaly  = rand(0,600);
            $pdo->exec("INSERT INTO blogs(id,title,content,created_at,update_at,display,is_show) VALUES(null,'{$title}','{$content}','{$created_at}',now(),'{$dispaly}',1)");
        }


    }
    public function staticHtml(){
        $pdo = new PDO("mysql:host=127.0.0.1;dbname=blog","root","7545");
        $pdo->exec("SET NAMES utf8");
        $pdos  = $pdo->query("SELECT * FROM blogs ");
        $arr = $pdos->fetchAll(PDO::FETCH_ASSOC);
        // var_dump($arr);
        ob_start();
        foreach($arr as $v){

            view("blogs.content",[
                'blogs'=>$v,
            ]);
            $str = ob_get_contents();
            file_put_contents(ROOT.'/public/contents/'.$v['id'].".html",$str);
            ob_clean();

        }



    }
}