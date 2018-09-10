<?php
namespace libs;
class Log {
    private $fy=null;
    public function __construct($filename){
        if($this->fy===null){
            $this->fy = fopen(ROOT."Logs/".$filename.".log",'a');
        }
    }
    public function log($content){
            
        $date = date("Y-m-d H:i:s");    
        $c = $date . "\r\n";
        $c .= str_repeat("=",120)."\r\n";
        $c.=$content."\r\n";
        fwrite($this->fy,$c);
    }
}