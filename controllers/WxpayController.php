<?php 
  namespace controllers;
  use Yansongda\Pay\Pay;
  use Endroid\QrCode\QrCode;
  use models\Order;
  
  class WxpayController {
    protected $config = [
        'app_id' => 'wx426b3015555a46be', // 公众号 APPID
        'mch_id' => '1900009851',
        'key' => '8934e7d15453e97507ef794cf7b0519d',
        'notify_url' => 'http://hgd.tunnel.echomod.cn/wxpay/notify',
    ];

    public function pay()
    {
        $sn = $_POST['sn'];
        $orders = new Order;
        $orderInfo  = $orders->findBySn($sn);
        // var_dump($orderInfo);
        if($orderInfo['status']==0){
            $order = [
                'out_trade_no' => time(),
                'total_fee' => '1', // **单位：分**
                'body' => '智聊充值中心',
            ];
            $pay = Pay::wechat($this->config)->scan($order);
            // echo $pay->return_code , '<hr>';
            // echo $pay->return_msg , '<hr>';
            // echo $pay->appid , '<hr>';
            // echo $pay->result_code , '<hr>';
            // echo $pay->code_url , '<hr>';
            $qrCode = new QrCode($pay->code_url);
             header('Content-Type: '.$qrCode->getContentType());
             echo $qrCode->writeString();
        }else{
            die('此订单已经充值过了，如有充值需要，可以新立订单');
        }
       

    }

    public function notify()
    {
        $pay = Pay::wechat($this->config);

        try{
            $data = $pay->verify(); // 是的，验签就这么简单！

            if($data->result_code == 'SUCCESS' && $data->return_code == 'SUCCESS')
            {
                echo '共支付了：'.$data->total_fee.'分';
                echo '订单ID：'.$data->out_trade_no;
            }

        } catch (Exception $e) {
            var_dump( $e->getMessage() );
        }
        
        $pay->success()->send();
    }
    public function qrcode()
    {
          
    }
    
}