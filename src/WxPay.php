<?php
/**
 * WxPay 微信支付
 * @author : weiyi <294287600@qq.com>
 * Licensed ( http://www.wycto.com )
 * Copyright (c) 2016~2099 http://www.wycto.com All rights reserved.
 */
namespace wycto\pay;
use wycto\pay\wxpay\WxPayJsApiPay;
use wycto\pay\wxpay\WxPayUnifiedOrder;
use wycto\pay\wxpay\WxPayApi;
class WxPay extends PayAbstract
{
    // 全局唯一实例
    private static $_app = null;

    private $_config = array();

    private function __construct($config) {

    }

    static function init($config){

        if(null == self::$_app){
         self::$_app = new WxPay($config);
        }

         return self::$_app;
    }

    function weiXinPay($paran){
        //①、获取用户openid
        $tools = new WxPayJsApiPay();
        $openId = $tools->GetOpenid();

        //②、统一下单
        $input = new WxPayUnifiedOrder();
        $input->SetBody($paran['body']);
        $input->SetAttach("attach");
        $input->SetOut_trade_no($paran['out_trade_no']);
        $input->SetTotal_fee($paran['total_fee']*100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("test");

        $notify_url = $this->_notify_url;
        if(isset($paran['notify_url'])&&!empty($paran['notify_url'])){
            $notify_url = trim($paran['notify_url']);
        }
        $input->SetNotify_url($notify_url);

        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = WxPayApi::unifiedOrder($input);

        $jsApiParameters = $tools->GetJsApiParameters($order);

        //获取共享收货地址js函数参数
        $editAddress = $tools->GetEditAddressParameters();
        return array('param'=>$jsApiParameters,'address'=>$editAddress);
    }
}

?>