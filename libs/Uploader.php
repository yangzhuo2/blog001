<?php 
namespace libs;
class Uploader {
    private static $obj = null;
    private function __constrcut(){
    }
    private function __clone(){
    }
    public static function make(){

        if(self::$obj===null){
             self::$obj = new self;
        }
        return self::$obj;
    }
    private $extArr = ['image/jpeg','image/jpg','image/ejpeg','image/png','image/gif','image/bmp'];
    private $file;
    private $root = ROOT."public/uploads/";
    private $maxSize = 1024*1024*1.8;
    private $subDir;
    public function uploader($name,$subDir){
        $this->file = $_FILES[$name];
        var_dump($this->file);
        $this->subDir = $subDir;
        if($this->checkType){
            die('此格式参数不支持');
        }
        if($this->checkSize){
            die('文件太大');
        }
        $name = $this->getNewName();
        $dir = $this->getDir();
        move_uploaded_file($this->file['tmp_name'],$this->root.$dir.$name);
        return $dir.$name;

    }
    private function getNewName(){
        $ext = strrchr($this->file['name'] , '.');
        $name = md5(time()+$date).$ext;   
        return $name;
    } 
    private function getDir(){
        $dir =$this->subDir.'/'.date('Ymd');
        if(!is_dir($this->root.$this->subDir)){
            mkdir($this->root.$dir,777,true); 
        }
        return $dir."/";            
    }
    private function checkType(){
       return in_array($this->file['type'],$this->extArr);
    }
    private function checkSize(){
        return $this->file['size'] <$this->maxSize;
    }

    

}