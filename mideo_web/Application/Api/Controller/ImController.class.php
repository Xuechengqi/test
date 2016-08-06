<?php

namespace Api\Controller;
use Think\Controller;

class ImController extends Controller {

/**
 * 融云server API 接口 新版 1.0
 * Class ServerAPI
 * @author  caolong
 * @date    2014-12-10  15:30
 * @modify  2015-02-02  10:21
 *
//使用
$p = new ServerAPI('appKey','AppSecret');
$r = $p->getToken('11','22','33');
print_r($r);
 */
    // private $appKey = "pkfcgjstflow8";                //appKey
    // private $appSecret = "jouzDKPnfIakTb";             //secret
    private $appKey = "c9kqb3rdkgoyj";                //appKey
    private $appSecret = "q4LwnKnMWAv43";             //secret
    const   SERVERAPIURL = 'https://api.cn.ronghub.com';    //请求服务地址
    private $format = "json";                //数据格式 json/xml


    /**
     * 参数初始化
     * @param $appKey
     * @param $appSecret
     * @param string $format
     */
    public function __construct(){
    }

    private function token($userid = "", $name = "", $photo = "") {
        try{
            if(empty($userid))
                throw new \Exception('id empty');
            if(empty($name))
                throw new \Exception('name empty');
            if(empty($photo))
                $photo = getDefaultUserPhoto();

            $ret = S('imtoken_'.$userid);
            if($ret==false || strlen($ret) < 20){
                $ret = $this->curl('/user/getToken',array('userId'=>$userid,'name'=>$name,'portraitUri'=>$photo));
                if(empty($ret))
                    throw new \Exception('请求失败');
                S('imtoken_'.$userid,$ret,3600*24);
            }
            return $ret;
        }catch (\Exception $e) {
            \Think\Log::record("RongYun Im getToken Error: " . $e->getMessage());
            return "{}";
        }
    }

    /**
     * 获取 Token 方法
     * @param $userId   用户 Id，最大长度 32 字节。是用户在 App 中的唯一标识码，必须保证在同一个 App 内不重复，重复的用户 Id 将被当作是同一用户。
     * @param $name     用户名称，最大长度 128 字节。用来在 Push 推送时，或者客户端没有提供用户信息时，显示用户的名称。
     * @param $portraitUri  用户头像 URI，最大长度 1024 字节。
     * @return json|xml
     */
    public function gettoken($userid = "", $name = "", $photo = "") {
        $ret = $this -> token($userid, $name, $photo);
        header('Content-type: application/json');
        echo $ret;
        exit();
    }

    /**
     * 创建http header参数
     * @param array $data
     * @return bool
     */
    private function createHttpHeader() {
        $nonce = mt_rand();
        $timeStamp = time();
        $sign = sha1($this->appSecret.$nonce.$timeStamp);
        return array(
            'RC-App-Key:'.$this->appKey,
            'RC-Nonce:'.$nonce,
            'RC-Timestamp:'.$timeStamp,
            'RC-Signature:'.$sign,
        );
    }

    /**
     * 重写实现 http_build_query 提交实现(同名key)key=val1&key=val2
     * @param array $formData 数据数组
     * @param string $numericPrefix 数字索引时附加的Key前缀
     * @param string $argSeparator 参数分隔符(默认为&)
     * @param string $prefixKey Key 数组参数，实现同名方式调用接口
     * @return string
     */
    private function build_query($formData, $numericPrefix = '', $argSeparator = '&', $prefixKey = '') {
        $str = '';
        foreach ($formData as $key => $val) {
            if (!is_array($val)) {
                $str .= $argSeparator;
                if ($prefixKey === '') {
                    if (is_int($key)) {
                        $str .= $numericPrefix;
                    }
                    $str .= urlencode($key) . '=' . urlencode($val);
                } else {
                    $str .= urlencode($prefixKey) . '=' . urlencode($val);
                }
            } else {
                if ($prefixKey == '') {
                    $prefixKey .= $key;
                }
                if (is_array($val[0])) {
                    $arr = array();
                    $arr[$key] = $val[0];
                    $str .= $argSeparator . http_build_query($arr);
                } else {
                    $str .= $argSeparator . $this->build_query($val, $numericPrefix, $argSeparator, $prefixKey);
                }
                $prefixKey = '';
            }
        }
        return substr($str, strlen($argSeparator));
    }

    /**
     * 发起 server 请求
     * @param $action
     * @param $params
     * @param $httpHeader
     * @return mixed
     */
    public function curl($action, $params,$contentType='urlencoded') {
        $action = self::SERVERAPIURL.$action.'.'.$this->format;
        $httpHeader = $this->createHttpHeader();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $action);
        curl_setopt($ch, CURLOPT_POST, 1);
        if ($contentType=='urlencoded') {
            $httpHeader[] = 'Content-Type:application/x-www-form-urlencoded';
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->build_query($params));
        }
        if ($contentType=='json') {
            $httpHeader[] = 'Content-Type:Application/json';
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params) );
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false); //处理http证书问题
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $ret = curl_exec($ch);
        if (false === $ret) {
            $ret =  curl_errno($ch);
        }
        curl_close($ch);
        return $ret;
    }

/*
    public function test() {
        $fromuserid = 413;
        $touserid = 79;
        $userInfo = M('user') -> where(array('id' => $touserid)) -> field("username, avatar") -> find();
        if ($userInfo != false) {
            $ret = $this -> sendMessage($touserid, $fromuserid, "abc", $userInfo['username'], buildResourceUrlOne($userInfo['avatar']));
            echo $ret;
        }
    }
*/

    public function sendMessage($fromUserId = 0, $toUserId = 0, $message = "", $fromUserName="", $fromUserPhoto = "") {
        if (empty($fromUserId))
            return "fromUserId empty";
        if(empty($toUserId))
            return 'toUserId empty';
        if(empty($message))
            return 'message empty';
        if(empty($fromUserName))
            return 'fromUserName empty';
        if(empty($fromUserPhoto))
            $fromUserPhoto = getDefaultUserPhoto();

        $ret = $this->curl('/message/private/publish',
            array(
                'fromUserId'=>$fromUserId,
                'toUserId'=>$toUserId,
                'objectName'=>"RC:TxtMsg",
                "content"=> '{"content":"'.$message.'", "extra": "{\"id\":'.$fromUserId.',\"name\":\"'.$fromUserName.'\",\"portrait\":\"'.$fromUserPhoto.'\"}"}'
            )
        );
        if(empty($ret)) {
            return '请求失败';
        }
        if ($ret == '{"code":200}') {
            return "success";
        } else {
            return $ret;
        }
    }
}

?>