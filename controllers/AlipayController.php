<?php
namespace controllers;
use Yansongda\Pay\Pay;
use models\Order;
use models\User;
class AlipayController {

    public $config = [
        'app_id' => '2016091600527025',
        //通知地址
        'notify_url' => 'http://hgd.tunnel.echomod.cn/alipay/notify',
        //返回地址
        'return_url'=>"http://127.0.0.1:9999/alipay/return",
        //阿里支付公钥
        "ali_public_key"=>"MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAzI7WFu5GBHIhqGI06iXc/HMhpGNqjPWH0ISSk/cz9P9WGDJt1g84KCHeeRBsJswxM0vcRFcMJvKjcPjFBBH7TycMzXZaRJDijCQmQfxTYVqsjEh3aVofy1QBtuoLVk/znw9b70H0fWQo2UX83mIdnyaOTe/71/xywe4VZqf98eUlWvtRVQ9ZAaxoe8iQ/E4wju9FdjGZP32UgYukd6nKCaMtAt3EMcWjo24KqWGyKOhmIpDxYa5raPah7MkvQXUOsZmMMUVlH60AomKN8r71+7m9NPCdDkihkam+0DkrF8c0YoIRxiJeRZXRuhRAcrV4C7UC+IBJCNJS9UD0a7TUnwIDAQAB",
        //商户秘钥
        "private_key"=>"MIIEowIBAAKCAQEA2p8l8QQpgtdqYXsVoyeDiAaeG+7EKv+tbaARiKnQeaiHKgw8+w+CmT5smL8P1HvKDyxI1VhY2noAvnBwcn1cWczl6c1Y1VVItajHO3aMD7wsFmDot1q5Vhpvy+yootdWi4LSOcJkegT6WDyq0zBfd9RFetQMrqa1iNYMa38o4VZ6JaDirfOIfjPZNpHzG8Efct1MFamINNyQ/7rbJUlX76K7hqnPZaC3iIa9/a9cXsRtrRGMcQ8cwzc6zl94tcY8YLU8uNovlbiooYHISTexDS1QQGM+/dkngkAnmQERRWLlbL53gLGQ5hBIZa5V0fkKIc084eOZVKAD4HLPW2RhdQIDAQABAoIBAG/k+t9j2Pc92BXykH2EMN8DPBNh3od/ez1bqv2+pJRP/HY581AwfRCAEccQK0L+5MllShXf9QJFZXITTIpcvVK8+4Px9SCjWOaZbvdxUniPQCVoDlQiHDAEsem2xA0smAApwf2MEC8fsx5MVsZmwMYtIC6gr6kIbGuP2qV+XOcPkIzrbQteaAP+2unxAUbCFkqpMrnHV5xzSlnB4nL6iZEVOIFcKbCRBA2MKxL4XSNCMymwqMtT4r8p77Rh8mUdJI8gRGh7vStuNOPFJZYNFTdPCIZeEEfC/HUgdf50/xjTGTK+aLM6qgzXjd+bAfLKiDZhVwi/TnKPYuArvKiHyFUCgYEA8xWEcJAuZmrhleMDbVswB0k1CTY1cDw/YNMaecxRqRMK5RhN32uHZkdbSlFei6ATppTPtPV+5xEmpAhVvF2IDUsQwukvQFoQPJMUaPf5Cx1ogrInNxlQ5PO8wWNd70I8ozDte1SCdyyuJYohdViSe6ce92EjoHC9TBXfUYQ2txMCgYEA5jzjYbGdFcju6wKwULYvOpubWkHp0G4vhouU++9DtXVpANKIFo8KHgwWZ9uTyQwNsawuj0LeH8yK6G75kqaQ4v/HNHgDXIeQFr8AM5mLjcNZQRASuLZLrPAuN7dODgaFc9HnC7bPCsTrFjurJhRdVksLlbEOzTaaOdYUnGeWblcCgYBf4Wrd42EJ8LqOHn7pkYA+P3f17DXj0T/Gdz4IMLk9EU/I0W9V4toDU74EcFf0hu15VVUgMX0eszsklE0NAW96ntM7rjJ/FYc1/WWdicHnym/ArXieWRP5WtJnDUSt7NhyHghuDwVu8Ga4U+WinY8Zyu+B8ATXceCYtD/jrVzPlQKBgG0BHtY99RU/UH5Lg5Zy6uFgkqik1EIuKKoWo66zlObwc4pEItIrXqrjJih9uPZSkpkv38tL6UY83Pc2s0pPOgF+/51DxLOmKv3Z6AqGA6BWgIdhSLvS8vkprXgLfRT+2WgAVNci8dszZ+nazij1M0uLtCxm648U7ue7B38VPA95AoGBAJ9Ywy8digtfJXqOF7okgFMn4zi1RFIUAIN/6KkzIpURiR1xwrWoLILkxM1YxpMDrg8UCKzpYLC9NA7237SpiPYq/p511UDdSJ1EFEvJUd/gijNfJzwNfvLUTxgOvLFscb8SQlObEJJrrphn+HReb+nzdBuKLB9lAGw3hdy3gpIZ",
        'mode' => 'dev',
        //dopwbc1284@sandbox.com
    ];
    public function pay()
    {
        $sn = $_POST['sn']; 
        $der = new Order;
        $orderArr = $der->findBySn($sn); 
        //如果这个订单还没有支付 就跳转到支付宝
        if($orderArr['status']==0){
                $order = [
                    'out_trade_no' => $orderArr['sn'],    // 本地订单ID
                    'total_amount' => $orderArr['money'],    // 支付金额
                    'subject' => '智聊充值中心', // 支付标题
                ];
                $alipay = Pay::alipay($this->config)->web($order);
                $alipay->send();

        }else{
            die('此订单已经充值过了，如有充值需要，可以新立订单');
        }
    }
    // 支付完成跳回
    public function return()
    {
        $data = Pay::alipay($this->config)->verify(); // 是的，验签就这么简单！
        echo '<h1>支付成功！</h1> <hr>';
        var_dump( $data->all() );
        message("充值成功,即将返回",'/user/orders',2);
    }
    // 接收支付完成的通知
    public function notify()
    {
        $alipay = Pay::alipay($this->config);
        try{
               // 是的，验签就这么简单！
            $data = $alipay->verify(); 
            if($data->trade_status=="TRADE_SUCCESS"||$data->trade_status=="TRADE_FINISHED"){
               $order = new Order;
               $orderInfo =  $order->findBySn($data->out_trade_no);
            //    var_dump($orderInfo);
                // 这里需要对 trade_status 进行判断及其它逻辑进行判断，在支付宝的业务通知中，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
                // 1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号；
                // 2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额）；
                if($orderInfo['status']==0){
                    $order->startTrans();
                        $reg1 = $order->setPidBySn($data->out_trade_no);
                        $user = new User;
                        $reg2 =  $user->addMoney($orderInfo['money'],$orderInfo['user_id']);
                    if($reg1  &&   $reg2){
                        
                        $order->commit();
                    }else {
                        $order->rollback();
                    }
                }
               
            }
        } catch (\Exception $e) {
                echo '11111111';
                var_dump($e->getMessage()) ;
        }
        // 返回响应
        $alipay->success()->send();
    }
}