<?php
namespace models;
use PDO;
class Order extends Base
{
    public function created($money){
        $flase = new \libs\Snowflake(1000);
            $pdos = self::$pdo->prepare("INSERT INTO orders(user_id,money,sn)  VALUES(?,?,?)  ");
            $num = $pdos->execute([
                ($_SESSION['id']*1),
                ($money*1),
                $flase->nextId()
            ]);
           
    }
    public function search(){
        $where = isset($_SESSION['id'])?" user_id = ".$_SESSION['id'] : 1 ;

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
        $ziduan = "created_at";
        $orderBy = "asc";
        
        $nowPage = isset($_GET['nowpage'])?($_GET['nowpage'])*1:1;
        $link = "nowpage";
        $link =  getUrlParms(["$link"]);
        $pdos = self::$pdo->prepare("SELECT COUNT(*) FROM orders WHERE ".$where);
        $pdos->execute(); 
        $num  = $pdos->fetch(PDO::FETCH_COLUMN);
        $page = 10;
        $limit = " LIMIT ".($nowPage-1)*$page.",{$page}";
        $pages = ceil($num/$page);
        $str = "";
        for($i=1;$i<=$pages;$i++){
            $str .= "<a href=?".$link."nowpage={$i}".">{$i}</a>";
        }
        $pdos = self::$pdo->prepare("SELECT * FROM orders WHERE ".$where." ORDER BY ".$ziduan."  ".$orderBy .$limit);  
        $pdos->execute();
        $arr = $pdos->fetchAll(PDO::FETCH_ASSOC);

        return array($arr,$str);
    }
    public function  findBySn($sn){
        $pdos = self::$pdo->prepare("SELECT * FROM orders WHERE sn = ?");
        $pdos->execute([$sn]);
        return $pdos->fetch(PDO::FETCH_ASSOC);
    }
    public function setPidBySn($sn){
        $pdos = self::$pdo->prepare("UPDATE orders SET status=1,pay_time=now() WHERE sn=?");
        $num = $pdos->execute([$sn]);
        return $num;
    }

}