<?php
/**
 * Alipay 支付宝支付
 * @author : weiyi <294287600@qq.com>
 * Licensed ( http://www.wycto.com )
 * Copyright (c) 2016~2099 http://www.wycto.com All rights reserved.
 */
namespace wycto\pay;

use wycto\pay\alipay\Wap;
use wycto\pay\alipay\Web;
use wycto\pay\alipay\WeiXin;
use wycto\pay\alipay\AliPayQuery;
class Alipay extends PayAbstract
{
    // 全局唯一实例
    private static $_app = null;
    // 配置
    private $_config = array();
    //支付终端
    private $_gateway = 'web';//默认电脑
    //构造方法
    private function __construct($config){
        $this->_config = $config;
    }

    static function init($config)
    {
        if (self::$_app == null) {
            self::$_app = new Alipay($config);
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
            // 手机端
            return new Wap($this->_config);
        } elseif ($this->_gateway == 'weixin') {
            // 微信
            return new WeiXin($this->_config);
        } elseif($this->_gateway == 'web') {
            // 电脑
            return new Web($this->_config);
        }elseif($this->_gateway == 'query'){
            //查询
            return new AliPayQuery($this->_config);
        }else{
            return null;
        }
    }
}

?>
