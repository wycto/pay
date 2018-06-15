<?php
/**
 * LoginAbstract 类定义了支付接口的工厂类
 * @author : weiyi <294287600@qq.com>
 * Licensed ( http://www.wycto.com )
 * Copyright (c) 2016~2099 http://www.wycto.com All rights reserved.
 */
namespace wycto\pay;
abstract class Pay
{
    const ALIPAY = 'alipay';

    const WXPAY = 'wxpay';

    static function getApp($pay = self::ALIPAY, array $config = array()) {

        if (strtolower($pay) == self::ALIPAY) {
            $app = Alipay::init($config);
        }
        else if (strtolower($pay) == self::WXPAY) {
            $app = WxPay::init($config);
        }
        else {
            return false;
        }

        return $app;
    }
}

?>