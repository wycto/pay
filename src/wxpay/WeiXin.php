<?php
/**
 * Alipay 微信宝支付-公众号支付
 * @author : weiyi <294287600@qq.com>
 * Licensed ( http://www.wycto.com )
 * Copyright (c) 2018 http://www.wycto.com All rights reserved.
 */
namespace wycto\pay\wxpay;
class WeiXin
{
  /*配置信息*/
  protected $appid='';//微信支付申请对应的公众号的APPID
  protected $mch_id;//产品中心-开发配置-商户号
  protected $secret;//微信支付申请对应的公众号的APP Key
  protected $key;//帐户设置-安全设置-API安全-API密钥-设置API密钥
  protected $notify_url='';//支付异步通知

  /*订单信息*/
  protected $out_trade_no='';//订单号
  protected $total_amount='';//订单总金额
  protected $subject='';//订单标题
  protected $body='';//订单描述

  public $data = array();

  public function __construct($config=array())
  {
      if(count($config)){
          foreach ($config as $key=>$value){
              if(isset($this->$key)){
                  $this->$key = $value;
              }
          }
      }
  }

  /**
   * 设置订单号 商户网站唯一订单号
   * @param string $out_trade_no
   */
  public function setOutTradeNo($out_trade_no)
  {
      $this->out_trade_no = $out_trade_no;
  }

  /**
   * 设置订单金额
   * @param unknown $payAmount
   */
  public function setTotalAmount($total_amount)
  {
      $this->total_amount = $total_amount;
  }

  /**
   * 设置 商品的标题/交易标题/订单标题/订单关键字等
   * @param string $subject
   */
  public function setSubject($subject)
  {
      $this->subject = $subject;
  }

  /**
   * 设置支付异步通知地址
   * @param unknown $notify_url 异步通知地址
   */
  public function setNotifyUrl($notify_url)
  {
      $this->notify_url = $notify_url;
  }

  /**
   * 统一下单
   * 1.调用【网页授权获取用户信息】接口获取到用户在该公众号下的Openid
   * 2.收款总费用 单位元
   * 3.唯一的订单号
   * 4.订单名称
   * 5.支付结果通知url 不要有问号
   * 6.支付时间
   * @return string
   */
  function pay()
  {
      $timestamp = time();
      $openid = $this->GetOpenid();//获取openid
      $config = array(
          'mch_id' => $this->mch_id,
          'appid' => $this->appid,
          'key' => $this->key,
      );

      $ip = $this->getIp();//获取ip

      $unified = array(
          'appid' => $config['appid'],
          'attach' => 'pay',//商家数据包，原样返回，如果填写中文，请注意转换为utf-8
          'body' => $this->subject,
          'mch_id' => $config['mch_id'],
          'nonce_str' => self::createNonceStr(),
          'notify_url' => $this->notify_url,
          'openid' => $openid,//rade_type=JSAPI，此参数必传
          'out_trade_no' => $this->out_trade_no,
          'spbill_create_ip' => $ip,
          'total_fee' => floatval($this->total_amount * 100),       //单位 转为分
          'trade_type' => 'JSAPI',
      );
      $unified['sign'] = self::getSign($unified, $config['key']);
      $responseXml = self::curlPost('https://api.mch.weixin.qq.com/pay/unifiedorder', self::arrayToXml($unified));
      $unifiedOrder = simplexml_load_string($responseXml, 'SimpleXMLElement', LIBXML_NOCDATA);
      if ($unifiedOrder === false) {
          die('parse xml error');
      }
      if ($unifiedOrder->return_code != 'SUCCESS') {
          die($unifiedOrder->return_msg);
      }
      if ($unifiedOrder->result_code != 'SUCCESS') {
          die($unifiedOrder->err_code);
      }
      $arr = array(
          "appId" => $config['appid'],
          "timeStamp" => "$timestamp",        //这里是字符串的时间戳，不是int，所以需加引号
          "nonceStr" => self::createNonceStr(),
          "package" => "prepay_id=" . $unifiedOrder->prepay_id,
          "signType" => 'MD5',
      );
      $arr['paySign'] = self::getSign($arr, $config['key']);
      return $arr;
  }

  /**
   * 通过跳转获取用户的openid，跳转流程如下：
   * 1、设置自己需要调回的url及其其他参数，跳转到微信服务器https://open.weixin.qq.com/connect/oauth2/authorize
   * 2、微信服务处理完成之后会跳转回用户redirect_uri地址，此时会带上一些参数，如：code
   * @return 用户的openid
   */
  public function GetOpenid()
  {
      //通过code获得openid
      if (!isset($_GET['code'])){
          //触发微信返回code码
          $scheme = $_SERVER['HTTPS']=='on' ? 'https://' : 'http://';
          $baseUrl = urlencode($scheme.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING']);
          $url = $this->__CreateOauthUrlForCode($baseUrl);
          Header("Location: $url");
          exit();
      } else {
          //获取code码，以获取openid
          $code = $_GET['code'];
          $openid = $this->getOpenidFromMp($code);
          return $openid;
      }
  }

  /**
   * 通过code从工作平台获取openid机器access_token
   * @param string $code 微信跳转回来带上的code
   * @return openid
   */
  public function GetOpenidFromMp($code)
  {
      $url = $this->__CreateOauthUrlForOpenid($code);
      $res = self::curlGet($url);
      //取出openid
      $data = json_decode($res,true);
      $this->data = $data;
      $openid = $data['openid'];
      return $openid;
  }

  /**
   * 构造获取open和access_toke的url地址
   * @param string $code，微信跳转带回的code
   * @return 请求的url
   */
  private function __CreateOauthUrlForOpenid($code)
  {
      $urlObj["appid"] = $this->appid;
      $urlObj["secret"] = $this->secret;
      $urlObj["code"] = $code;
      $urlObj["grant_type"] = "authorization_code";
      $bizString = $this->ToUrlParams($urlObj);
      return "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;
  }

  /**
   * 构造获取code的url连接
   * @param string $redirectUrl 微信服务器回跳的url，需要url编码
   * @return 返回构造好的url
   */
  private function __CreateOauthUrlForCode($redirectUrl)
  {
      $urlObj["appid"] = $this->appid;
      $urlObj["redirect_uri"] = "$redirectUrl";
      $urlObj["response_type"] = "code";
      $urlObj["scope"] = "snsapi_base";
      $urlObj["state"] = "STATE"."#wechat_redirect";
      $bizString = $this->ToUrlParams($urlObj);
      return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
  }

  /**
   * 拼接签名字符串
   * @param array $urlObj
   * @return 返回已经拼接好的字符串
   */
  private function ToUrlParams($urlObj)
  {
      $buff = "";
      foreach ($urlObj as $k => $v)
      {
          if($k != "sign") $buff .= $k . "=" . $v . "&";
      }
      $buff = trim($buff, "&");
      return $buff;
  }

  public static function curlGet($url = '', $options = array())
  {
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT, 30);
      if (!empty($options)) {
          curl_setopt_array($ch, $options);
      }
      //https请求 不验证证书和host
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      $data = curl_exec($ch);
      curl_close($ch);
      return $data;
  }

  public static function curlPost($url = '', $postData = '', $options = array())
  {
      if (is_array($postData)) {
          $postData = http_build_query($postData);
      }
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
      curl_setopt($ch, CURLOPT_TIMEOUT, 30); //设置cURL允许执行的最长秒数
      if (!empty($options)) {
          curl_setopt_array($ch, $options);
      }
      //https请求 不验证证书和host
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      $data = curl_exec($ch);
      curl_close($ch);
      return $data;
  }

  public static function createNonceStr($length = 16)
  {
      $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
      $str = '';
      for ($i = 0; $i < $length; $i++) {
          $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
      }
      return $str;
  }
  public static function arrayToXml($arr)
  {
      $xml = "<xml>";
      foreach ($arr as $key => $val) {
          if (is_numeric($val)) {
              $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
          } else
              $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
      }
      $xml .= "</xml>";
      return $xml;
  }

  public static function getSign($params, $key)
  {
      ksort($params, SORT_STRING);
      $unSignParaString = self::formatQueryParaMap($params, false);
      $signStr = strtoupper(md5($unSignParaString . "&key=" . $key));
      return $signStr;
  }
  protected static function formatQueryParaMap($paraMap, $urlEncode = false)
  {
      $buff = "";
      ksort($paraMap);
      foreach ($paraMap as $k => $v) {
          if (null != $v && "null" != $v) {
              if ($urlEncode) {
                  $v = urlencode($v);
              }
              $buff .= $k . "=" . $v . "&";
          }
      }
      $reqPar = '';
      if (strlen($buff) > 0) {
          $reqPar = substr($buff, 0, strlen($buff) - 1);
      }
      return $reqPar;
  }

  protected function getIp() {
    //strcasecmp 比较两个字符，不区分大小写。返回0，>0，<0。
    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    $res =  preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '127.0.0.1';
    return $res;
  }
}
