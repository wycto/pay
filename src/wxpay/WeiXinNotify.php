<?php
/**
 * Alipay 微信支付-异步通知
 * @author : weiyi <294287600@qq.com>
 * Licensed ( http://www.wycto.com )
 * Copyright (c) 2018 http://www.wycto.com All rights reserved.
 */
namespace wycto\pay\wxpay;
class WeiXinNotify
{
    /*配置信息*/
    protected $appid='';//微信支付申请对应的公众号的APPID
    protected $mch_id='';//产品中心-开发配置-商户号
    protected $key='';//帐户设置-安全设置-API安全-API密钥-设置API密钥

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
     * 查询请求
     * @return array
     */
    public function notify($out_trade_no='')
    {

        $config = array(
            'mch_id' => $this->mch_id,
            'appid' => $this->appid,
            'key' => $this->key,
        );
        $postStr = file_get_contents('php://input');
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($postObj === false) {
            return array('status' => 0, 'message'=>'解析xml失败');
        }
        if ($postObj->return_code != 'SUCCESS') {
            return array('status' => 0, 'message'=>$postObj->return_msg);
        }
        if ($postObj->result_code != 'SUCCESS') {
            return array('status' => 0, 'message'=>$postObj->err_code);
        }
        $arr = (array)$postObj;
        unset($arr['sign']);
        if (self::getSign($arr, $config['key']) == $postObj->sign) {
            echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
            return array('status' => 1, 'message'=>$postObj->err_code,'out_trade_no'=>$postObj->out_trade_no,'transaction_id'=>$postObj->transaction_id,'data'=>$arr);
        }
        return array('status' => 0, 'message'=>"签名错误");
        exit();
    }

    /**
     * 获取签名
     */
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
}
