<?php
namespace Lib;

class FCM {
    public function index(){
        $token = array("feqj0C6Wwqk:APA91bHDeqSBtjdavfwe-N0WpXbFXSsioX07J2WyLV6DHavlbnogV6KFnNp5ZRoZph6x_-Jtw_a0LffLlvIgtAAortRUIHwNim-i_juFw1Hcj_narYh1DU7R5kL-cfst-PvPW8ICNbMs","eDEL0g5DSdc:APA91bFdjGH62i7pJUpDwTIGoGcLzYxhWt31M_6DOwOyyQZ_9n9L3-gdaseAbE8gHco5jwREi_KWQND0jx3eFfxPHdl4aZGI9ak97M7XL4TKlfpn5JiFx1JjTo91dAPSQjELMjJzYUE-");
        $message = array(
            'type'=>"like",
            'title'=>'Mideo' ,
            'content'=>"one like two"
        );
        $this -> sendmsg($token, $message);
    }

    public function send($tokens, $message) {
        $url = "https://fcm.googleapis.com/fcm/send";
        $fields = array(
            "registration_ids" => $tokens,
            "data" => $message
        );

        $headers = array(
            'Authorization:key = AIzaSyBsVmx3IRhYYBvPMFZvyW2BqyY4F1U32tI',
            'Content-Type: application/json'
        );

        $ret = $this -> curl($url, $fields, $headers);
        return $ret;
    }

    /**
     * 发起 server 请求
     * @param $action
     * @param $params
     * @param $httpHeader
     * @return mixed
     */
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
?>