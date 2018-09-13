<?php
namespace models;
use PDO;
use libs\Redis;

class Blog extends Base 
{   
    public function delete($id){
        $pdos = self::$pdo->prepare("DELETE FROM blogs where id = ? AND user_id = ?");
        $pdos->execute([
            $id,
            $_SESSION['id']
        ]);
    }
    public function find($id){
        $pdos = self::$pdo->prepare("SELECT * FROM blogs where id = ?");
        $pdos->execute([
            $id
        ]);
        return $pdos->fetch(PDO::FETCH_ASSOC);
    }
    public function update($title,$content,$is_show,$id){
        $pdos  = self::$pdo->prepare("UPDATE blogs SET title = ?,content=?,is_show=? WHERE id = ?");
        $num = $pdos->execute([
            $title,
            $content,
            $is_show,
            $id
        ]);
        return $num;
    }
    public function search_page(){
        
        $where = isset($_SESSION['id'])? "user_id=".$_SESSION['id']:1;
        
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
        $pdos = self::$pdo->query($sql.$where);
        
        $num  = $pdos->fetch(PDO::FETCH_COLUMN);
        $page = 10;
        $limit = " LIMIT ".($nowPage-1)*$page.",{$page}";
        $pages = ceil($num/$page);
        $str = "";
        for($i=1;$i<=$pages;$i++){
            $str .= "<a href=?".$link."nowpage={$i}".">{$i}</a>";
        }
        $sql = "SELECT * FROM blogs WHERE ";
        $pdos = self::$pdo->query($sql.$where.$limit);  
        $arr = $pdos->fetchAll(PDO::FETCH_ASSOC);
        return array($arr,$str);
    }
    
    public function static_content(){
        $pdos  = self::$pdo->query("SELECT * FROM blogs ");
        $arr = $pdos->fetchAll(PDO::FETCH_ASSOC);
        return $arr;
    }
    public function index2htlm(){
        $pdos  = self::$pdo->query("SELECT * FROM blogs WHERE is_show = 1 ORDER BY created_at desc LIMIT 20");
        $arr = $pdos->fetchAll(PDO::FETCH_ASSOC);
        ob_start();
        view('index.index',[
            'blogs'=>$arr,
        ]);
        $str = ob_get_contents();
        file_put_contents(ROOT."public/index.html",$str);
        
    }
    
    public function update_id($id){
        $redis = Redis::getinstance();
           $key = "blog-".$id;
           if($redis->hexists('display_num',$key)){
               $num = $redis->hincrby('display_num',$key,1);
               return $num;
           }else {
                $pdos = self::$pdo->prepare("SELECT display from blogs WHERE id = ?");
                $pdos->execute(array($id));
                $num = $pdos->fetch(PDO::FETCH_COLUMN);
                $num++;
                $redis->hsetnx("display_num",$key,$num);
                return $num;
           }
            
    }
    public function redistomysql(){
        $redis = Redis::getinstance();
        $data = $redis->hgetall("display_num");
        // var_dump ($data);
        foreach($data as $k => $v){
            $arr = explode("-",$k);
            $sql = " UPDATE blogs SET display = $v WHERE id = $arr[1]";
            $num  =self::$pdo->exec($sql);
        }
    }
    public function add($title,$content,$is_show){
        $is_show = $is_show*1;
        $pdos = self::$pdo->prepare("INSERT INTO blogs(title,content,is_show,user_id) VALUES(?,?,?,?)");
        $reg = $pdos->execute([
            $title,
            $content,
            $is_show ,
            $_SESSION['id'],
        ]);
        if(!$reg){
            $info = $pdos->errorInfo();
            echo "<pre>";
            var_dump($info,$is_show);
            // return false;
            exit;
        }
        return self::$pdo->lastInsertId();

        
    }   
    public function makeHtml($id){
        $blog = $this->find($id);
        ob_start();
        view('blogs.content',
        [  'blogs'=>$blog ]
        );
        $str = ob_get_clean();
        file_put_contents(ROOT.'public/contents/'.$blog['id'].".html",$str);
    }
    public function delHtml($id){
        @unlink(ROOT.'public/contents/'.$blog['id'].".html",$str);
    }
    public function getNew(){
        $pdos = self::$pdo->prepare("SELECT * FROM blogs ORDER BY created_at LIMIT 20");
        $pdos->execute([]);
       return  $pdos->fetchAll(PDO::FETCH_ASSOC);
    }
}