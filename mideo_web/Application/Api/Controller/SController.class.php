<?php
/**
 * API控制器基类
 */
namespace Api\Controller;
use Think\Controller;
class SController extends Controller {
    protected $_getPageSize = 10;

    //保持单例,节约内存
    static private $_des = null;
    //保持单例,节约内存
    static private $_fcm = null;
    static private $_lang = null;

    public function _initialize(){
        if(isset($_POST['MGO_TOKEN'])&&$_POST['MGO_TOKEN']==="ac9698f65294e3bd935e5d05d360e089"){
            $this->mgo_vid_decode($_POST);
            //后台机器人预留
            return true;
        }
        $jp = substr(ACTION_NAME,-3);
        if($jp=="_jp"){
            return true;
        }else{
            checkToken();
        }
    }

    private function mgo_vid_decode(&$data){
        $vid_arr=array('videoid');
        foreach ($data as $key => $value) {
            if(in_array($key, $vid_arr)){
                $data[$key]=$this->encode_vid($value);
            }
        }

    }

    //获取fcm句柄
    public function lang(){
        if(self::$_lang===null)
            self::$_lang = new \Lang\Lang;
        return self::$_lang;
    }

    //获取fcm句柄
    public function fcm(){
        if(self::$_fcm===null)
            self::$_fcm = new \Lib\FCM;
        return self::$_fcm;
    }

    /**
     * 交互消息推送 4类基本操作
     * @param  [type] $type [消息类型]
     * @param  [type] $userids  [接收方用户ids]
     * @param  [type] $fromid   [发送方用户id]
     * @return [type]       [description]
     */
    public function msg_send($type,$userids,$fromid,$comment=""){
        $userModel = M("user");
        $from_user = $userModel->field('id,avatar,username,country')->where("id={$fromid}")->find();
        if(is_array($userids)){
            $map['id']  = array('in',$userids);
        }else{
            $map['id']  = array('in',(string)$userids);
        }
        $list = $userModel->field('id,token')->where($map)->select();
        $to_ids=array();
        foreach ($list as $v) {
            if(!empty($v['token'])){
                $to_ids[]  = $v['token'];
            }
        }
        if(empty($to_ids)){
             return false;
        }

        $data = array();

        $lang = $this->lang();
        //推送通知消息从服务端转移到安卓端（评论，at，点赞）
        switch ($type) {
            case 'follow':
                $data['title'] = $from_user['username'];
                $data['type'] = "attention";
                $data['content'] = $from_user['username'];
                break;
            case 'comment':
                $data['title'] = $from_user['username'];
                $data['type'] = "comment";
                $data['content'] = $comment;
                break;
            case 'at':
                $data['title'] = $from_user['username'];
                $data['type'] = "at";
                $data['content'] = $from_user['username'];
                break;
            default:
                throw new \Exception('参数错误！');
                break;
        }

        // $fields = array(
        //     'registration_ids' => $to_ids,
        //     'data' => $data,
        // );
        return $this->fcm()->send($to_ids, $data);
    }



    //获取des句柄
    private function des(){
        if(self::$_des===null)
            self::$_des = new \Lib\CryptDes;
        return self::$_des;
    }

    /**
     * encode_vid
     * 加密视频id
     * @return [type] [description]
     */
    public function encode_vid($id){
        if(is_numeric($id))
            return $this->des()->encrypt($id);
        return false;
    }
    /**
     * decode_vid
     * 解密视频id
     * @return [type] [description]
     */
    public function decode_vid($str){
        $id = $this->des()->decrypt($str);
        if(!is_numeric($id))
            return show_output(101, "");
        return $id;
    }

    /**
     * encode_vid_arr
     * 批量加密视频id
     * 大数据 引用传递，无返回值
     * @param [type] $[f_col] [真实的视频id]
     * @param [type] $[v_col] [需要替换的伪 视频id]
     * @return viod
     */
    public function encode_vid_arr(&$arr,$f_col="videoid",$v_col=null){
        $v_col = is_null($v_col)?$f_col:$v_col;
        if($arr){
            foreach ($arr as &$v) {
                $v[$v_col]=$this->encode_vid($v[$f_col]);
            }
        }
    }


    /**
     * 获取随机N个用户id
     * @return [type] [description]
     */
    protected function getRandUserId($num=1){
        $res = array();
        $list = array(34,35,36,37,100,101,102,103,104,105,106,107,108,109,110,112,113,114,115,116,117,118,119,120,121,122,123,127,128,129,130,131,132,195,196,197,198,199,200,201,202,203,206,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224);
        $k = array_rand($list, $num);
        if(is_array($k)){
            foreach ($k as $v) {
                $res[]=$list[$v];
            }
            return $res;
        }else{
            return $list[$k];
        }
    }

}
