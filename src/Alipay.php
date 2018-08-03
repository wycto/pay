<?php
/**
 * Alipay 支付宝支付
 * @author : weiyi <294287600@qq.com>
 * Licensed ( http://www.wycto.com )
 * Copyright (c) 2016~2099 http://www.wycto.com All rights reserved.
 */
namespace wycto\pay;

use wycto\pay\alipay\Wap;
use wycto\pay\alipay\WeiXin;

class Alipay extends PayAbstract
{
    // 全局唯一实例
    private static $_app = null;
    // 配置
    private $_config = array();
    //支付终端
    private $_gateway = 'web';//默认电脑
    //类地图
    private $_class_map = array(
        'web'=>'Web',
        'wap'=>'Wap',
        'weixin' => 'WeiXin'
    );

    private function __construct($config, $gateway)
    {
        $this->_config = $config;
        $this->_gateway = $gateway;
    }

    static function init($config, $gateway)
    {
        if (self::$_app == null) {
            self::$_app = new Alipay($config, $gateway);
        }
        return self::$_app->run();
    }

    /**
     * 调用相关终端
     *
     * @param string $gateway
     *            [web:电脑网站支付;wap:手机网站支付;app:APP支付;scan:扫码支付]
     */
    function run()
    {
        if ($this->_gateway == 'wap') {
            // 手机端
            return new Wap($this->_config);
        } elseif ($this->_gateway == 'weixin') {
            // 微信
            return new WeiXin($this->_config);
        } else {
            // 电脑
        }
    }

    function request()
    {}

    function notify()
    {}

    function response()
    {
        /*
         * array(12) {
              ["total_amount"]=>
              string(4) "0.01"
              ["timestamp"]=>
              string(19) "2018-08-03 14:10:55"
              ["sign"]=>
              string(344) "K5V8+Lk5alfos9rG5CRzTZSh5rFJAbruYtDzApal0D/QqnePcojZ5CiGJyjx96u+q+aimRKSRiuybeLfoPzhvsSZzO7qO9GBlnPYIS+rSB0Ozttmr0R5bKngfriDBC9qVfmwESA6r4qS6Ay9OVnK/Qgmj9A6FqZLL4SpzYUQohG5A8DNkdSgA3TuiXVr1L9TtsWyQk04HYhq1IpxuAgn0jxV0WYlxpIg4TU7nGyU3qpKnkw4Wb5kjnw8X7lLdOJ58/D8kpEIadyvCrgxTkYM0iZh5cD1l2dFOszz7r3PakzpiIDealze1j7EDwkBNap2Q33wQ1B7j+V6h8OxVjghXA=="
              ["trade_no"]=>
              string(28) "2018080321001004670505864343"
              ["sign_type"]=>
              string(4) "RSA2"
              ["auth_app_id"]=>
              string(16) "2018062060400732"
              ["charset"]=>
              string(4) "utf8"
              ["seller_id"]=>
              string(16) "2088721870519422"
              ["method"]=>
              string(27) "alipay.trade.wap.pay.return"
              ["app_id"]=>
              string(16) "2018062060400732"
              ["out_trade_no"]=>
              string(13) "5b63f1d1d17f9"
              ["version"]=>
              string(3) "1.0"
            }
         * */
    }
}

?>
