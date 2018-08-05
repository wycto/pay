<?php
/**
 * Alipay 支付宝支付-公众号支付
 * @author : weiyi <294287600@qq.com>
 * Licensed ( http://www.wycto.com )
 * Copyright (c) 2018 http://www.wycto.com All rights reserved.
 */
namespace wycto\pay\alipay;
class WeiXin
{

    /*配置信息*/
    protected $app_id='';//appid
    protected $private_key='';//应用私钥，生成的时候保存的，不是平台上的公钥，是公钥对应的
    protected $return_url='';//支付回调地址
    protected $notify_url='';//支付异步通知
    protected $apjs_src = 'ap.js';
    protected $jump_url = "pay.html";

    /*订单信息*/
    protected $out_trade_no='';//订单号
    protected $total_amount='';//订单总金额
    protected $subject='';//订单标题
    protected $body='';//订单描述

    /*默认信息*/
    protected $api_url = 'https://openapi.alipay.com/gateway.do';//https://openapi.alipaydev.com/gateway.do;//沙箱模式
    protected $charset = 'utf8';//字符集，默认utf8
    protected $sign_type = 'RSA2';//签名类型，新建应用只能用RSA2

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
     * 设置appid
     * @param unknown $app_id
     */
    public function setAppid($app_id)
    {
        $this->app_id = $app_id;
    }

    /**
     * 设置支付回调地址
     * @param unknown $return_url 回调地址
     */
    public function setReturnUrl($return_url)
    {
        $this->return_url = $return_url;
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
     * 设置私钥
     * @param unknown $private_key
     */
    public function setPrivateKey($private_key)
    {
        $this->private_key = $private_key;
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
     * 设置订单号 商户网站唯一订单号
     * @param string $out_trade_no
     */
    public function setOutTradeNo($out_trade_no)
    {
        $this->out_trade_no = $out_trade_no;
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
     * 设置 商品的标题/交易标题/订单标题/订单关键字等
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * 设置网关，默认是沙箱测试网管
     * @param string $api_url 接口地址
     */
    public function setApiUrl($api_url)
    {
        $this->api_url = $api_url;
    }

    /**
     * 签名类型
     * @param string $sign_type 默认是RSA2
     */
    public function setSignType($sign_type)
    {
        $this->sign_type = $sign_type;
    }

    /**
     * 微信端的js文件路劲
     * @param unknown $apjs_src 默认根目录的ap.js
     */
    public function setApjsSrc($apjs_src)
    {
        $this->apjs_src = $apjs_src;
    }

    /**
     * 跳转地址的页面提示地址
     * @param unknown $jump_url 默认是根目录的pay.html
     */
    public function setJumpUrl($jump_url)
    {
        $this->jump_url = $jump_url;
    }

    /**
     * 发起订单
     * @param float $total_amount 收款总费用 单位元
     * @param string $out_trade_no 唯一的订单号
     * @param string $subject 订单名称
     * @param string $notify_url 支付结果通知url 不要有问号
     * @param string $timestamp 订单发起时间
     * @return array
     */
    public function pay($now=true)
    {
        //请求参数
        $requestConfigs = array(
            'out_trade_no'=>$this->out_trade_no,
            'product_code'=>'QUICK_WAP_WAY',
            'total_amount'=>$this->total_amount, //单位 元
            'subject'=>$this->subject,  //订单标题
            'body' => $this->body//订单描述
        );
        $commonConfigs = array(
            //公共参数
            'app_id' => $this->app_id,
            'method' => 'alipay.trade.wap.pay',             //接口名称
            'format' => 'JSON',
            'charset'=>$this->charset,
            'sign_type'=>$this->sign_type,
            'timestamp'=>date('Y-m-d H:i:s'),
            'version'=>'1.0',
	    'return_url' => $this->return_url,
            'notify_url' => $this->notify_url,
            'biz_content'=>json_encode($requestConfigs),
        );
        $commonConfigs["sign"] = $this->makerSign($commonConfigs, $commonConfigs['sign_type']);
        //return $commonConfigs;

        //执行
        $queryStr = http_build_query($commonConfigs);
        if($now){
            if($this->isWeixin()){
                //是微信
                header('Content-type:text/html; Charset=utf-8');
                $html = <<<EOF
                <script type="text/javascript" src="{$this->apjs_src}"></script>
                <script>
                var gotoUrl = '{$this->api_url}?{$queryStr}';
                _AP.pay(gotoUrl,'{$this->jump_url}');
                </script>
EOF;
                echo $html;
            }else{
                //不是微信
                header("Location:{$this->api_url}?{$queryStr}");
            }
        }else{
            return $queryStr;
        }
    }

    public function makerSign($params, $signType = "RSA") {
        return $this->sign($this->getSignContent($params), $signType);
    }

    protected function sign($data, $signType = "RSA") {
        $priKey=$this->private_key;
        $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($priKey, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";
        ($res) or die('您使用的私钥格式错误，请检查RSA私钥配置');
        if ("RSA2" == $signType) {
            openssl_sign($data, $sign, $res, version_compare(PHP_VERSION,'5.4.0', '<') ? SHA256 : OPENSSL_ALGO_SHA256); //OPENSSL_ALGO_SHA256是php5.4.8以上版本才支持
        } else {
            openssl_sign($data, $sign, $res);
        }
        $sign = base64_encode($sign);
        return $sign;
    }

    /**
     * 校验$value是否非空
     *  if not set ,return true;
     *    if is null , return true;
     **/
    protected function checkEmpty($value) {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;
        return false;
    }

    public function getSignContent($params) {
        ksort($params);
        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {
                // 转换成目标字符集
                $v = $this->characet($v, $this->charset);
                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }
        unset ($k, $v);
        return $stringToBeSigned;
    }

    /**
     * 转换字符集编码
     * @param $data
     * @param $targetCharset
     * @return string
     */
    function characet($data, $targetCharset) {
        if (!empty($data)) {
            $fileType = $this->charset;
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
            }
        }
        return $data;
    }

    function isWeixin(){
        if ( strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') !== false ) {
            return true;
        }
        return false;
    }
}
