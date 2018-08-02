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
  // 全局唯一实例
	private static $_app = null;

	private $_config = array();

	private function __construct($config) {

		// 自动识别域名
		$this->_notify_url = 'http://' . $_SERVER['HTTP_HOST'] . '/user/payment/alipaynotify';
		$this->_return_url = 'http://' . $_SERVER['HTTP_HOST'] . '/user/payment/alipayresponse';
	}

	static function init($config) {

		if (self::$_app==null) {
			self::$_app = new Alipay($config);
		}
		return self::$_app;
	}

    function request(){

    }

   function notify(){

   }

   function response(){

   }
}

?>
