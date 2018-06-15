<?php
/**
 * Alipay 支付宝支付
 * @author : weiyi <294287600@qq.com>
 * Licensed ( http://www.wycto.com )
 * Copyright (c) 2016~2099 http://www.wycto.com All rights reserved.
 */
namespace wycto\pay;

class Alipay  extends PayAbstract
{
    static function init($config){

        /* if(null == self::$_app){
            self::$_app = new Alipay($config);
        }

        return self::$_app; */
    }
}

?>