<?php 
namespace libs;
class Redis 
{
    private static $redis = null;
    private function __construct(){
    }
    private function __clone(){
    }
    public static function getinstance(){
        if(self::$redis===null){
            $rediss = config('redis');
            self::$redis = new \Predis\Client([
                'scheme' => $rediss['scheme'],
                'host'   => $rediss['host'],
                'port'   => $rediss['port'],
                ]);
        }
        return self::$redis;
    }

}