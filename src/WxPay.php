<?php
/**
 * WxPay 微信支付
 * @author : weiyi <294287600@qq.com>
 * Licensed ( http://www.wycto.com )
 * Copyright (c) 2016~2099 http://www.wycto.com All rights reserved.
 */
namespace wycto\pay;

use wycto\pay\wxpay\Wap;
use wycto\pay\wxpay\Web;
use wycto\pay\wxpay\WeiXin;
use wycto\pay\wxpay\WeiXinQuery;
use wycto\pay\wxpay\Payment;
class WxPay extends PayAbstract
{
    // 全局唯一实例
    private static $_app = null;
    // 配置
    private $_config = array();
    //支付终端
    private $_gateway = 'web';//默认电脑
    //构造方法
    private function __construct($config) {
        $this->_config = $config;
    }

    static function init($config){

        if(null == self::$_app){
         self::$_app = new WxPay($config);
        }
        return self::$_app;
    }

    /**
     * 重置配置
     */
    function setConfig($config){
        $this->_config = array_merge($this->_config,$config);
        return $this;
    }

    /**
     * 设置网关
     */
    function gateway($gateway){
        $this->_gateway = $gateway;
        return $this;
    }

    /**
     * 实例化相应的终端类
     */
    function meta()
    {
        if ($this->_gateway == 'wap') {
            return new Wap($this->_config);// 手机端
        }
        elseif ($this->_gateway == 'weixin') {
            return new WeiXin($this->_config);// 微信
        }
        elseif($this->_gateway == 'web') {
            return new Web($this->_config);// 电脑
        }
        elseif($this->_gateway == 'query'){
            return new WeiXinQuery($this->_config);//查询
        }elseif($this->_gateway == 'payment'){
            return new Payment($this->_config);//查询
        }else{
            return null;
        }
    }
}

?>
