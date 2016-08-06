<?php
/**
 * @author ivy <guoxuivy@gmail.com>
 * @copyright Copyright &copy; 2013-2017 Ivy Software LLC
 * @license https://github.com/guoxuivy/ivy/
 * @package framework
 * @link https://github.com/guoxuivy/ivy * @since 1.0
 */
namespace Lib;
/* 创建分组
https://android.googleapis.com/gcm/notification
Content-Type:application/json
Authorization:key=API_KEY
project_id:SENDER_ID

{
   "operation": "create",
   "notification_key_name": "appUser-Chris",
   "registration_ids": ["4", "8", "15", "16", "23", "42"]
}

return:
{
   "notification_key": "APA91bGHXQBB...9QgnYOEURwm0I3lmyqzk2TXQ"
}

//添加、移除分组成员  remove
{
   "operation": "add",
   "notification_key_name": "appUser-Chris",
   "notification_key": "APA91bGHXQBB...9QgnYOEURwm0I3lmyqzk2TXQ",
   "registration_ids": ["51"]
}
*/
class FCM {
    //消息类型常量
    // const TYPE_LIKE='like';
    // const TYPE_FOLLOW='follow';
    // const TYPE_COMMENT='comment';
    // const TYPE_AT='at';

    //public $api_key = "AIzaSyAU3wZs9raik-mHQ";
    private $api_key = "AIzaSyBBflrAgEmY3EcUZhuHh6TYHQopdpLI_5M";

    //private $project_id = "1049089021416";

    protected $fields = null;


    public function __construct() {}

    /**
     * 格式化发送字段
     * @param  [type] $ids  [接收者ids]
     * @param  [type] $type [消息类型]
     * @return [type]       [this]
     */
    public function setFields($fields) {
        $this->$fields = $fields;
        return $this;
    }

    /**
     * Sending
     * $fields = array(
            'registration_ids' => array("faGzdBZmzis:APA91bGJGb_Mt4-rgV7ywlP9OVtCv8na1dL3Kv-Ihhf4bndDbAUv9oOqo3nqzpwYcrZsN3O7vhgiOjzERpqtCOt7BqVi8fjHbXCBQe0BSKhhT1VuTN_D1PKu32rYX29ip31seYJ_QNPN"),
            'data' => array('type'=>"like",'title'=>'Mideo' ,'content'=>"one like two"),
        );
     */
    public function send($fields=null) {
        $fields = is_null($fields)?$this->fields:$fields;
        if(is_null($fields)){
            throw new \Exception('参数错误！');
        }

        $url = "https://fcm.googleapis.com/fcm/send";
        $headers = array(
            'Authorization: key=' . $this->api_key,
            'Content-Type: application/json'
        );
        $this->fields = null;
        return $this -> curl($url, $fields, $headers);
    }

    private function curl($action, $params,$headers) {
        $httpHeader = $headers;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $action);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params) );
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

}