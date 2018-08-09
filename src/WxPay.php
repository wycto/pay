<?php
/**
 * WxPay 微信支付
 * @author : weiyi <294287600@qq.com>
 * Licensed ( http://www.wycto.com )
 * Copyright (c) 2016~2099 http://www.wycto.com All rights reserved.
 */
namespace wycto\pay;
use wycto\pay\wxpay\WxPayUnifiedOrder;
use wycto\pay\wxpay\WxPayApi;
use wycto\pay\wxpay\JsApiPay;
class WxPay extends PayAbstract
{
    // 人民币元转分比例
    const RMB_YUAN_FEN_RATE = 100;


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
        $tools = new JsApiPay();
        $openId = $tools->GetOpenid();

        //②、统一下单
        $input = new WxPayUnifiedOrder();
        $input->SetBody($paran['body']);
        $input->SetAttach("attach");
        $input->SetOut_trade_no($paran['out_trade_no']);
        // 浮点型计算可能出现丢失精度问题，如 19.9 * 100
        // @todo: 安装相关函数拓展后，推荐使用以下方式对人民币价格单位进行转换
        // $total_fee = (int)bcmul($paran['total_fee'], self::RMB_YUAN_FEN_RATE),
        $total_fee = (string)($paran['total_fee'] * self::RMB_YUAN_FEN_RATE);
        $input->SetTotal_fee($total_fee);
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