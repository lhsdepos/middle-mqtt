<?php
namespace XjrMiddle\MqttSdk;
/**
 * 新捷睿智能柜专用
 */
class MqttManager
{

	

	const DOMAIN_URL = 'http://api.newgearing.com/drop'; //域名
    
    private static $APPID  = '';
	private static $APP_SECRET = '';

	private static $publishUrl = self::DOMAIN_URL."/mqtt/v1/publish";//消息发布
	private static $getDevUrl = self::DOMAIN_URL."/mqtt/v1/getDevList";//查询设备
	private static $getTypeUrl = self::DOMAIN_URL."/mqtt/v1/getTypeList";//查询设备类型
	private static $addDevUrl = self::DOMAIN_URL."/mqtt/v1/addDev";//添加设备
	private static $delDevsUrl = self::DOMAIN_URL."/mqtt/v1/delDevs";//删除设备
   
    /**
     * [init 初始化配置]
     * @Author   lhs
     * @DateTime 2022-07-13T16:06:26+0800
     * @param    [type]                   $appId     [description]
     * @param    [type]                   $appSecret [description]
     * @return   [type]                              [description]
     */
    public static function init($appId,$appSecret)
    {
    	 self::$APPID = $appId;
    	 self::$APP_SECRET = $appSecret;
    	 return new MqttManager();
    }

    /**
     * [publish 消息推送]
     * @Author   lhs
     * @DateTime 2022-07-09T11:32:10+0800
     * @param    array                    $param [description]
     * @return   [type]                          [description]
     */
    public static function publish($param = [])
    {
    	return self::baseRequert(self::$publishUrl,$param);
    }
    
    /**
     * [publish 获取设备列表]
     * @Author   lhs
     * @DateTime 2022-07-09T11:32:10+0800
     * @param    array                    $param [description]
     * @return   [type]                          [description]
     */
    public static function getDevList($param = [])
    {
    	return self::baseRequert(self::$getDevUrl,$param);
    }
    
    /**
     * [publish 获取类型列表]
     * @Author   lhs
     * @DateTime 2022-07-09T11:32:10+0800
     * @param    array                    $param [description]
     * @return   [type]                          [description]
     */
    public static function getTypeList($param = [])
    {
    	return self::baseRequert(self::$getTypeUrl,$param);
    }
    
    /**
     * [publish 添加设备]
     * @Author   lhs
     * @DateTime 2022-07-09T11:32:10+0800
     * @param    array                    $param [description]
     * @return   [type]                          [description]
     */
    public static function addDev($param = [])
    {
    	return self::baseRequert(self::$addDevUrl,$param);
    }
    
    /**
     * [publish 删除设备]
     * @Author   lhs
     * @DateTime 2022-07-09T11:32:10+0800
     * @param    array                    $param [description]
     * @return   [type]                          [description]
     */
    public static function delDevs($param = [])
    {
    	 return self::baseRequert(self::$delDevsUrl,$param);
    }


    /**
     * [VerifySign 验证签名]
     * @Author   lhs
     * @DateTime 2022-07-09T11:01:52+0800
     * @param    [type]                   $param [description]
     */
    public static function VerifySign($param)
    {
    	if(empty($param['sign'])) return false;
    	$sign = self::MakeSign($param);
        return $sign == $param['sign'];
    }

   
    /**
     * [baseRequert 基础配置]
     * @Author   lhs
     * @DateTime 2022-07-09T11:29:37+0800
     * @param    [type]                   $url   [description]
     * @param    [type]                   $param [description]
     * @return   [type]                          [description]
     */
    private static function baseRequert($url,$param)
    {
    	 $param['appId'] = self::$APPID;
    	 $param['time'] = time();
    	 $param['sign'] = self::MakeSign($param);
    	 return self::mqtt_http_post($url,$param);
    }
    
    /**
    * [MakeSign 生成签名]
    * @Author   lhs
    * @DateTime 2022-07-09T10:59:16+0800
    * @param    array                    $param [description]
    */
    private static function MakeSign($param)
    {
    	$param = self::dataFilter($param);
    	$param = self::argSort($param);
    	$paramStr = self::createLinkstring($param);
    	$paramStr .= "&appSecret=".self::$APP_SECRET;
    	return md5($paramStr);
    }

    /**
	 * 除去数组中的空值和签名参数
	 * @param $data 签名参数组
	 * return 去掉空值与签名参数后的新签名参数组
	 */
	private static function dataFilter($data) {
	    $data_filter = array();
	    foreach ($data as $key => $val) {
	    	if($key == "sign" || $key == "sign_type" || $val == "")continue;
	        else    $data_filter[$key] = $data[$key];
	    }
	    return $data_filter;
	}
	/**
	 * 对数组排序
	 * @param $data 排序前的数组
	 * return 排序后的数组
	 */
	private static function argSort($data) {
	    ksort($data);
	    reset($data);
	    return $data;
	}
	/**
	 * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
	 * @param $data 需要拼接的数组
	 * return 拼接完成以后的字符串
	 */
	private static function createLinkstring($data) {
	    $arg  = "";

	    foreach ($data as $key => $val) {
	    	 $arg.=$key."=".$val."&";
	    }
	   
	    //去掉最后一个&字符
	    $arg = rtrim($arg,'&');
	    //如果存在转义字符，那么去掉转义
	    if(get_magic_quotes_gpc()){
	        $arg = stripslashes($arg);
	    }
	    return $arg;
	}

	/**
	 * http get 请求
	 *
	 * @param string $url    请求地址
	 * @param array  $header 请求头部
	 *
	 * @return array
	 */
	private static function mqtt_http_get($url,  $param = [],$header = [])
	{
	    if (empty($header)) {
	        $header = [
	            "Content-type:application/x-www-form-urlencoded;",
	            "Accept:application/x-www-form-urlencoded"
	        ];
	    }

	    $curl = curl_init();
	    if (is_array($param)) {
	        $param = http_build_query($param);
	        curl_setopt($curl, CURLOPT_URL, $url . '?' . $params);
	    }else{
	    	curl_setopt($curl, CURLOPT_URL, $url);
	    }
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
	    $response = curl_exec($curl);
	    curl_close($curl);
	    $response = json_decode($response, true);

	    return $response;
	}

	/**
	 * http post 请求
	 *
	 * @param string $url    请求地址
	 * @param array  $param  请求参数
	 * @param array  $header 请求头部
	 *
	 * @return array
	 */
	private static function mqtt_http_post($url, $param = [], $header = [])
	{

	    if (empty($header)) {
	        $header = [
	            "Content-type:application/x-www-form-urlencoded;charset='utf-8'",
	            "Accept:application/x-www-form-urlencoded"
	        ];
	    }
         $param = http_build_query($param);
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	    curl_setopt($curl, CURLOPT_POST, 1);
	    curl_setopt($curl,CURLINFO_HEADER_OUT,1);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $param);
	    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    $response = curl_exec($curl);
	    curl_close($curl);
	    $response = json_decode($response, true);
	    return $response;
	}
	
}