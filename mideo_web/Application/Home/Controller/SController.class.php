<?php
/**
 * 后台控制器基类
 * 登入后方可使用
 */
namespace Home\Controller;
use Think\Controller;
class SController extends Controller {

    // 分页参数 默认10
    protected $_getPageSize = 25;

    //保持单例,节约内存
    static private $_des = null;
    //保持单例,节约内存
    static private $_fcm = null;
    static private $_lang = null;

    public function _initialize(){
    	//登录检查
        if (isset($_SESSION['login']) && $_SESSION['login'] == true && $_SESSION['rule'] == 'admin') {
            return true;
        }
        if(IS_AJAX)
            $this->ajaxReturn(array('code'=>400,'msg'=>"未登录！"));
        $this -> redirect('Auth/login');
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
     * 交互消息推送  4类基本操作
     * @param  [type] $type [消息类型]
     * @param  [type] $userids  [接收方用户ids]
     * @param  [type] $fromid   [发送方用户id]
     * @return [type]       [description]
     */
    public function msg_send($type,$userids,$fromid,$comment=""){
        $userModel = M("user");
        $from_user = $userModel->field('id,avatar,username,country')->where("id={$fromid}")->find();
        if (isset($from_user['avatar']) && !empty($from_user['avatar'])) {
            if(strtolower(substr($from_user['avatar'],0,4)) != 'http')
                $from_user['avatar'] = getAvatarResourceBaseUrl() . $from_user['avatar'];
            else
                $from_user['avatar'] = $from_user['avatar'];
        }
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

        $data = array('icon'=>$from_user['avatar'],'type'=>"like",'title'=>'Mideo' ,'content'=>"");

        $lang = $this->lang();

        switch ($type) {
            case 'like':
                $data['type'] = "like";
                $data['content'] = $from_user['username'].$lang->get('msg_like',$from_user['country']);
                break;
            case 'follow':
                $data['type'] = "attention";
                $data['content'] = $from_user['username'].$lang->get('msg_follow',$from_user['country']);
                break;
            case 'comment':
                $data['type'] = "comment";
                $data['content'] = $from_user['username'].$lang->get('msg_comment',$from_user['country']).'"'.$comment.'"';
                break;
            case 'at':
                $data['type'] = "at";
                $data['content'] = $from_user['username'].$lang->get('msg_at',$from_user['country']).'"'.$comment.'"';
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
            return false;
        return $id;
    }

    /**
     * 查询条件过滤
     * @param  [type] &$where [description]
     * @return [type]         [description]
     */
    public function filter_country(&$where){
        return ;
        $country = $this->getCountry();
        switch ($country) {
            case 'BR':
                $where['country']="BR";
                break;
            default:
                $where['country'] = array('neq',"BR");
                break;
        }
    }

    /**
     * 页面显示条件过滤
     * @param  [type] &$where [description]
     * @return [type]         [description]
     */
    public function filter_allowed_country(&$list){
        return ;
        $curr_country = $this->getCountry();
        if($curr_country!="BR"){
            $pos = array_search("BR",$list);
            unset($list[$pos]);
        }else{
            $list=array(0=>"BR");
        }

    }

    public function getCountry(){
        $host = getHostInfo();
        $country="";
        if(stripos($host,"br")!==false){
            $country="BR";
        }elseif(stripos($host,"us")!==false){
            $country="US";
        }elseif(stripos($host,"GB")!==false){
            $country="GB";
        }elseif(stripos($host,"KR")!==false){
            $country="KR";
        }elseif(stripos($host,"ES")!==false){
            $country="ES";
        }elseif(stripos($host,"PT")!==false){
            $country="PT";
        }elseif(stripos($host,"CN")!==false){
            $country="CN";
        }elseif(stripos($host,"IN")!==false){
            $country="IN";
        }else{
            $country="NONE";
        }
        return $country;
    }


    /**
     * 获取随机N个僵尸用户id
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