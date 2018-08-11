# 支付接口类库

目前支付宝支付可用，微信支付还未完善

==此版本文档适用于 0.1.6==

==请使用>=0.1.4版本的代码，之前的代码作为调试，不可用==

简单配置，轻松使用

封装了工厂类 **PayFactory**

---


需要支付类型直接传参：
PayFactory::getApp('alipay',config);

参数一：支付种类【alipay：支付宝；weixin：微信】

参数二：支付的配置【app_id、private_key、......】


```
$pay = PayFactory::getApp('alipay',$config)->gateway('weixin')->meta();//微信公众号

或者

$pay = PayFactory::getApp('alipay')->setConfig($config)->gateway('wap')->meta();//手机端

或者

$pay = PayFactory::getApp('alipay',$config)->gateway('web')->meta();//电脑端

或者

$pay = PayFactory::getApp('alipay')->setConfig($config)->gateway('query')->meta();//查询

支付：$pay->pay();
查询：$pay->query();
```
gatewap() 方法:设置支付终端，即网关，默认为电脑网站支付

web:电脑网站支付

wap：手机网站支付

weixin：公众号支付

query：订单查询


---
## 使用方法：
### 1.支付

```
use wycto\pay\PayFactory;
protected $_payconfig = array(
		'app_id'=>"2018062060400732",
		'private_key'=>"",//生成平台公钥的时候对应的私钥
		'apjs_src' => '/static/js/ap.js',
		'jump_url' => "http://www.wycto.cn",
		'return_url'=> 'http://www.wycto.cn',
		'notify_url'=> 'http://www.wycto.cn'
);

//支付宝支付
$out_trade_no = $order->number;     //你自己的商品订单号
$total_amount = $order->price;//付款金额，单位:元
$subject = $order->subject;    //订单标题

$this->config['return_url'] = 'http://' . $_SERVER['HTTP_HOST'] . url('wap::payment/return');
$this->config['notify_url'] = 'http://' . $_SERVER['HTTP_HOST'] . url('wap::payment/notify');
$this->config['jump_url'] = 'http://' . $_SERVER['HTTP_HOST'] . url('wap::payment/weixin');

//使用工厂类
$aliPay = PayFactory::getApp('alipay', $this->config)->gateway('weixin')->meta();//微信传参weixin，手机网站wap，电脑网站web
$aliPay->setSubject($subject);
$body = "全栈小子-" . $order->subject?$order->subject:"本次支付" . $order->price . "元";
$aliPay->setBody($body);
$aliPay->setTotalAmount($total_amount);
$aliPay->setOutTradeNo($out_trade_no);
$aliPay->Pay();
```

### 跳转地址处理：

```
var_dump($_GET);

array(12) {
  ["total_amount"]=>
  string(4) "0.01"
  ["timestamp"]=>
  string(19) "2018-08-04 15:48:19"
  ["sign"]=>
  string(344) "cFs+YT+Xl6c6u9jnz3VSB8wcro/2haiUMelU+23s2JCr8MJhJqsNXQnY36qVKUcffbkONPKJtZKMjtnBYjXBBRLgWVhrYUpH7zOODL9OILQJ2FNY+XyTAxXBrMliXlZ/KsGqRV+79YlO1uvfCMcJcKXdKJgT7gzvAQOsRxwhhTHNVWaMO5QdLe3Ve2RHZcwbedoF+4lBr7A9JZ5NMKZRKGgpR18jlDYhnpvIH9INahAcBKoZUqx6L8Xj5ddr3XIdJfZnGhBLGle66V+DROvJX6OkzRABP5uEp0Q4D1ZquKqmS4gHTt4wk/xgNZJ5VCd+5WeZoBhpYgpVWLKhmVatcw=="
  ["trade_no"]=>
  string(28) "2018080421001004670513036032"
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
  string(13) "5b655a3ab7041"
  ["version"]=>
  string(3) "1.0"
}
```

### 异步通知处理：

```
var_dump($_POST);
array(24) {
    gmt_create=>2018-08-04 15:55:09
	charset=>utf8,
	seller_email=>52645446@qq.com,
	subject=>支付测试,
	sign=>ePutXvBuc2gsFaTPVFUJOmOUuCGwylDcwITYirNI+nH7bW2biA9hfIGtU8hYy2w4uHwxC0qi9pXqoCzv4gKeB69vrQmgwyO0ZGCyBQUXHwYUSAxfH5fpTO/s993bRFO3jEODW9xb0pW+Zg1ycTtDTtrhMvL657iXJekrDyUpshEN5K+dHlNbGYkFiGDjEcQaSVqzTnwcxFWIxlMwGq+p1hMIqCZcxom1iCnHH/I4h7nwtW/9FBZ8eTP4u/sRJKU0KdWOR1CnHwP1QzvFvm0KdstitWW+Iam1NrdbiHYdqRmwSrAR3x89UdfqGFl3q9G79La7w11hxSZZUKxrxYo7Vg==,
	buyer_id=>2088802533010673,
	invoice_amount=>0.01,
	notify_id=>1ef231b418c5bd03a972c491da3bf6cl69,
	fund_bill_list=>[{"amount":"0.01","fundChannel":"ALIPAYACCOUNT"}],
	notify_type=>trade_status_sync,
	trade_status=>TRADE_SUCCESS,
	receipt_amount=>0.01,
	buyer_pay_amount=>0.01,
	app_id=>2018062060400732,
	sign_type=>RSA2,
	seller_id=>2088721870519422,
	gmt_payment=>2018-08-04 15:55:11,
	notify_time=>2018-08-04 15:55:11,
	version=>1.0,
	out_trade_no=>5b655bd759e66,
	total_amount=>0.01,
	trade_no=>2018080421001004670510094404,
	auth_app_id=>2018062060400732,
	buyer_logon_id=>294***@qq.com,
	point_amount=>0.00
}
```

> 根据接收到的数据，进行支付后的业务处理，订单号：out_trade_no；
>
> 注意，尽可能多的进行参数验收，比如验证：app_id、total_amount

### 查询

```
$pay = PayFactory::getApp('alipay')->setConfig($config)->gateway('query')->meta();//查询

$pay->query();
```
