# 支付接口类库
简单配置，轻松使用

封装了工厂类

需要支付类型直接传参

<?php

include 'Loader.php'; // 引入加载器

spl_autoload_register('Loader::autoload'); // 注册自动加载

use wycto\pay\Pay;

$config = array('apjs_src'=>'alipay/weixin/ap.js','jump_url'=>"alipay/weixin/pay.html");

$aliPay = Pay::getApp('alipay',$config,'weixin');

/*** 请填写以下配置信息 ***/

$appid = '2016091300504235';
//https://open.alipay.com 账户中心->密钥管理->开放平台密钥，填写添加了电脑网站支付的应用的APPID

$rsaPrivateKey="MIIEowIBAAKCAQEA4PRvr8RaKaqUZc/eWqvISuclgD/QcSJ/2Z7YpTk9yZeMwy+uX4CJlSWFzXzaKYYsg5h0AUlK2b2wUdcGyJvPhiBkcvoLiQ9lV+CRU7WpvVk4dYQq0tN8L/BkcTMidSPePaz0ZzayXHAwR9m/qWM3Vxr3rU6cHIxA4mZLqMLs0HSn622OYVRwquMNAXWVxXg0G/DbXGe7M8/QJebEN7yUtbN8BaEJESXyMqu/gXtntlejB3syw6b4UONRb2p3ph4N5vfzyDrvkSbaZNsuHj4LvoHE/53CuAH/KJeE8GUcsldQCWe2zXPDBBg3hY8ObNVbdpWr1PZqq8Y7mxqIhr3frwIDAQABAoIBAHLhwkv0LcuLlr+r+bU6d05xX0Bw1oWAheRgb+lpIznZkIR5zEZvgVPO1tdLRKriH8eQyuWBRZ2PdwVEl+1JTSEFV+cz9UIov6uyPuWOJ8JQVzoEpk4GvSxKSzFYWOeTysKamjI/x7TXgoCfHndl+PQeDJDQTX9yzQwSC9+CtKf7hfYfr8hK+mO3JboR/1czFGm0+p29cwbyjDcMElpMqiOPrrMv5U+1VzF6nhWqx+8zGHGjAQY/B+aQvsm8CLf/MJC67LrlE5BNppKHyr7IECjXbeExe8gttADyqTyguiwZNaep3lSsf/ojai+g9PNaCVyeGHbR7an4P4ZZkZ4FmLkCgYEA/QLhR1o3f7TvI1YUghovYCBSosRskVO1UqOpyFYxtaG2u22wzIzxu/9zKVj0WLa2j2fNgp1Z5nH6a6tB2dxZrQwNwH2X6xwOZYVoJUJhPLtYxu2x0m6hOxyodAyGyIoVYNbyGhMAVYm6D0MsH478flhUEf/+WbxR/yVEteVD61sCgYEA45y2TRIPd2RYRSvUKo2QvuWGbjzCnA/1QxEb9+Sj30hp/dDpCPVbOMGVTzppYk9oHFCdx/F+KrHE0qAG2c0KoP/JSplaT7gov+Q+DnIeTm2NKBKlP3pbjjdvfylhtQ3xSVzqDwN51w7gECw2F0nbXfs4jy9xyVun1t0KbJ71UT0CgYEA4lifPXwiRmeRwKUTt8jBNVf1VZQwJFskzgeIrqcd1YYUudzJ3FUDNdK0LftcrbjX3bdZjU5DzPuOsqAFS2fr+fncm6ZAMJ9q6bvNjfeykehw5ZZkDQPXzdA3i4phUirmMTpaYKU7GUsbXugTIzCCBm3y2B+SZqkpGf83VxsCBh0CgYAxraKcb7SwelZJwqcsInnVMIOGy/wt083UNYfFM0IRGd0IaPBz5Blk6duMz1LxAiPXCkFlwm+nIeWzkvnrz7TiLvHgNlhfzfIW79objQzQUVjdxjQLBsm04KSVPJL20XQ4bu8nF7sgFT8SSJQFwTj/6jUOC2zqZfbcDqKX0pn4gQKBgEisZMgl2PekURjo/5x/hZ8ENcHVA8VuQ+WSgucp4I3YVWK1KnukDKtqvOF7X7Upw48OtKobAwhwLVICvET8tXXEkESnPqqMxc1DvYUdIJvgru1cdGgm4jOFqewQFPfqy/a9tLeYg32oe6S4OH7hGDn8b9ees9SGREraX7Bzh4UG";		//商户私钥，填写对应签名算法类型的私钥，如何生成密钥参考：https://docs.open.alipay.com/291/105971和https://docs.open.alipay.com/200/105310

$signType = 'RSA2';			//签名算法类型，支持RSA2和RSA，推荐使用RSA2

$returnUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/alipay/return.php';     //付款成功后的同步回调地址

$notifyUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/alipay/notify.php';     //付款成功后的异步回调地址


$outTradeNo = uniqid();     //你自己的商品订单号

$payAmount = 0.01;          //付款金额，单位:元

$orderName = '支付测试';    //订单标题

/*** 配置结束 ***/

$aliPay->setAppid($appid);

$aliPay->setReturnUrl($returnUrl);

$aliPay->setNotifyUrl($notifyUrl);

$aliPay->setPrivateKey($rsaPrivateKey);

$aliPay->setTotalAmount($payAmount);

$aliPay->setOutTradeNo($outTradeNo);

$aliPay->setSubject($orderName);

$aliPay->setGateWay('https://openapi.alipaydev.com/gateway.do');

$sHtml = $aliPay->pay(true);

//echo $sHtml;

 ?>
