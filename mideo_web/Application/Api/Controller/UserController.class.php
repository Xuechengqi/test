<?php
namespace Api\Controller;
use Think\Upload;
/*
1xx 参数错误
2xx 成功
3xx 数据库错误
4xx 资源没找到
5xx 上传错误
6xx 重复操作

*/

class UserController extends SController {

    private function _checkUserFrom($from) {
        return $from == 'facebook' || $from == 'google' || $from == 'twitter' || $from == 'instagram';
    }

    private function _safeUserName($username) {
        return str_replace(' ', "", $username);
    }

    public function addbind($from='', $id='', $name='', $currentid = '') {
        //checkToken();
        if (!$this -> _checkUserFrom($from)) {
            return show_output(101, "from params error");
        }
        if (empty($currentid) || empty($id)) {
            return show_output(101, 'params error');
        }

        //如果已经存在此用户
        $userModel = M('user');
        $where = array(
            'id' => $currentid
        );
        $userInfo = $userModel -> where($where) -> field('id') -> find();
        if ($userInfo !== false) {
            $userInfo[$from] = $id;
            $userInfo[$from."_name"] = $name;
            $ret = $userModel -> save($userInfo);
            if ($ret === false) {
                return show_output(300, "");
            } else {
                return show_output(200, "");
            }
        } else {
            return show_output(400, "");
        }
    }

    public function bindUserDetail($userid = '', $email = "", $age_min = 0, $age_max = 0, $devices = "", $currency = "") {
        if (empty($userid)) {
            return show_output(101, 'params error');
        }

        $userItem = array(
            "userid" => $userid,
            "email" => $email,
            "age_min" => $age_min,
            "age_max" => $age_max,
            "devices" => $devices,
            "currency" => $currency,
            "addtime" => time()
        );

        $userDetailModel = M("user_detail");
        $where = array("userid" => $userid);
        $userInfo = $userDetailModel -> where($where) -> find();
        if ($userInfo == null || $userInfo == false) {
            $ret = $userDetailModel -> add($userItem);
        } else {
            $ret = $userDetailModel -> save($userItem);
        }
        if ($ret === false) {
            return show_output(300, "");
        } else {
            return show_output(200, "");
        }
    }

    private function _handlePhoneNumber($number) {
        $result = str_replace(" ", "", $number);
        $result = str_replace("-", "", $result);
        $result = str_replace("(", "", $result);
        $result = str_replace(")", "", $result);
        $result = str_replace("+", "", $result);
        return $result;
    }

    public function bindphone($currentid = '', $numbers = '') {
        //checkToken();
        if (empty($currentid) || empty($numbers)) {
            return show_output(101, 'params error');
        }

        $phones = array();
        if (strpos($numbers, ",") !== FALSE) {
            $phones = explode(",", $numbers);
        } else {
            $phones[] = $numbers;
        }

        //如果已经存在此用户
        $userphoneModel = M('user_phone');
        $userphoneItems = array();
        for ($i = 0; $i < count($phones); $i++) {
            $phone = $this -> _handlePhoneNumber($phones[$i]);
            $where = array(
                'userid' => $currentid,
                "phonenumber" => $phone
            );
            $userphoneInfo = $userphoneModel -> where($where) -> find();
            if ($userphoneInfo === false || $userphoneInfo === null) {
                $userphoneItems[] = array(
                    'userid' => $currentid,
                    "phonenumber" => $phone,
                    "addtime" => time()
                );
            }
        }
        if (!empty($userphoneItems)) {
            $ret = $userphoneModel -> addAll($userphoneItems);
            if ($ret === false) {
                return show_output(300, "");
            } else {
                return show_output(200, "");
            }
        } else {
            return show_output(600, "");
        }
    }

    public function unbind($id = '', $from = '') {
        //checkToken();
        if (!$this -> _checkUserFrom($from)) {
            return show_output(101, "from params error");
        }
        if (empty($id)) {
            return show_output(101, 'params error');
        }

        //如果已经存在此用户
        $userModel = M('user');
        $where = array(
            'id' => $id
        );
        $userInfo = $userModel -> where($where) -> field('id') -> find();
        if ($userInfo !== false) {
            $userInfo[$from] = '';
            $ret = $userModel -> save($userInfo);
            if ($ret === false) {
                return show_output(300, "");
            } else {
                return show_output(200, "");
            }
        } else {
            return show_output(400, "");
        }
    }

    public function bind($from='', $id='', $username = '', $motto='', $country = "", $gender="unknown") {
        //checkToken();
        if (!$this -> _checkUserFrom($from)) {
            return show_output(101, "from params error");
        }
        if (empty($username) || empty($id)) {
            return show_output(101, 'params error');
        }
        if (!checkCountry($country)) {
            $country = getDefaultCountry();
        }

        //如果已经存在此用户
        $userModel = M('user');
        $where = array(
            $from => $id
        );
        $userInfo = $userModel -> where($where) -> field('id,username,motto,avatar,gender') -> find();
        if ($userInfo != false) {
            if ($userInfo['gender'] != $gender) {
                $userInfo['gender'] = $gender;
                $userModel -> save($userInfo);
            }
            return $this -> get($userInfo['id']);
        }

        $username = $this -> _safeUserName($username);

        //检查用户名是否已经存在
        $userNameWhere = array("username" => $username);
        $isUserExist = $userModel -> where($userNameWhere) -> find();
        if ($isUserExist != false) {
            $username = $username . '_' .getRandomStr(5);
        }

        //新用户
        $params = array(
            $from => $id,
            $from."_name" => $username,
            "username" => $username,
            "motto" => $motto,
            "country" => $country,
            "gender" => $gender,
            "status" => 1
        );

        $thumb = '';
        if (isset($_POST['avatar']) && !empty($_POST['avatar'])) {
            $thumb = $_POST['avatar'];
        }
        if (isset($_FILES['avatar']) && !empty($_FILES['avatar']['name'])) {
            $thumb = $this -> _uploadAvatar();
        }
        $params['avatar'] = $thumb;
        $params['addtime'] = time();
        $params['lastlogintime'] = time();
        $ret = $userModel -> add($params);
        if ($ret !== false) {
            $attentionModel = M('attention');
            $params=array();
            //默认关注自己 36，37,44,51,52
            $params[] = array("fromuserid" => $ret,"touserid" => $ret,"relation" => 1,"createtime" => time());
            $params[] = array("fromuserid" => $ret,"touserid" => '83',"relation" => 1,"createtime" => time());

            // $params[] = array("fromuserid" => $ret,"touserid" => '127',"relation" => 1,"createtime" => time());
            // $params[] = array("fromuserid" => $ret,"touserid" => '128',"relation" => 1,"createtime" => time());
            // $params[] = array("fromuserid" => $ret,"touserid" => '129',"relation" => 1,"createtime" => time());
            // $params[] = array("fromuserid" => $ret,"touserid" => '130',"relation" => 1,"createtime" => time());
            // $params[] = array("fromuserid" => $ret,"touserid" => '131',"relation" => 1,"createtime" => time());
            // $params[] = array("fromuserid" => $ret,"touserid" => '132',"relation" => 1,"createtime" => time());

            $attentionRet = $attentionModel -> addAll($params);
            $newUserData = array(
                "id" => $ret,
                "username" => $username,
                "avatar" => $thumb,
                "motto" => $motto,
                "gender" => $gender,
                "attentioncount" => 0,
                "followcount" => 0,
                "is_bind" => 1,

            );
            return show_output(200, "", buildResourceUrlOne($newUserData));
        } else {
            return show_output(301, "");
        }
    }

    //内部直连
    public function login($id='', $secret='') {

        if (empty($id) || $secret!=="bf1ec5ae11f8fdf9a95b7") {
            return show_output(101, 'params error');
        }

        $userModel = M('user');
        $where = array(
            'id' => $id,
            'status' => 1
        );
        $userInfo = $userModel -> where($where) -> field('id,username,motto,avatar') -> find();
        if ($userInfo != false) {
            return $this -> get($userInfo['id']);
        }else{
            return show_output(404, "no person");
        }
    }

    //绑定fcmtoken
    public function fcmtoken($userid='', $fcm_token='') {
        if (empty($userid) || empty($fcm_token)) {
            return show_output(101, 'params error');
        }
        $userModel = M('user');
        $userItem = $userModel->where('id='.$userid) -> find();
        if ($userItem != false && $userItem != null) {
            if ($userItem['token'] == $fcm_token) {
                return show_output(200, "");
            }
            $userItem['token'] = $fcm_token;
            $res = $userModel -> save($userItem);
            if ($res != false) {
                return show_output(200, "");
            }else{
                return show_output(300, "db error");
            }
        } else {
            return show_output(404, "no person");
        }
    }

    public function update($id = '', $username = '', $motto = '', $gender="") {
        //checkToken();
        if (empty($username) || empty($id)) {
            return show_output(101, 'params error');
        }
        $userModel = M('user');
        //检查用户名是否已经存在
        $userNameWhere = array("username" => $username, '_string' => ' id != ' . $id);
        $isUserExist = $userModel -> where($userNameWhere) -> find();
        if ($isUserExist != false) {
           return show_output(600, "username");
        }

        $where = array('id' => $id, 'status' => 1);
        $userInfo = $userModel -> where($where) -> find();
        if ($userInfo === false) {
            return show_output(400, "");
        } else {
            $userInfo['username'] = $username;
            $userInfo['motto'] = $motto;
            if (!empty($gender)) {
                $userInfo['gender'] = $gender;
            }
            $ret = $userModel -> save($userInfo);
            if ($ret === false) {
                return show_output(300, "");
            } else {
                return show_output(200, "");
            }
        }
    }

    public function updateavatar($id='') {
        //checkToken();
        if (empty($id)) {
            return show_output(101, 'params error');
        }
        if (!isset($_FILES['avatar']) || empty($_FILES['avatar']['name'])) {
            return show_output(101, "avatar");
        }
        $thumb = $this -> _uploadAvatar();
        $userModel = M('user');
        $where = array('id' => $id, 'status' => 1);
        $userInfo = $userModel -> where($where) -> find();
        if ($userInfo === false) {
            return show_output(400, "");
        } else {
            $userInfo['avatar'] = $thumb;
            $ret = $userModel -> save($userInfo);
            if ($ret === false) {
                return show_output(300, "");
            } else {
                return show_output(200, "", buildResourceUrlOne($userInfo)['avatar']);
            }
        }
    }

    public function like($videoid = 0, $userid = 0, $country = "") {
        //checkToken();
        if (empty($videoid) || empty($userid)) {
            return show_output(101, 'params error');
        }
        $video_id=$this->decode_vid($videoid);
        //获取视频内容
        $videoModel = M('video');
        $videoInfo = $videoModel -> where(array('id' => $video_id)) -> find();
        if ($videoInfo === false) {
            return show_output(404, "");
        }

        if (!checkCountry($country)) {
            $country = getDefaultCountry();
        }

        //检查是否已经like过
        $likeModel = D('vlike');
        $likeInfo = $likeModel -> where(array('videoid' => $videoInfo['id'], 'userid' => $userid, "relation" => 1)) -> find();
        if ($likeInfo == false) {
            //插入
            $params = array(
                "videoid" => $videoInfo['id'],
                "userid" => $userid,
                "country" => $country,
                "createtime" => time()
            );
            $likeRet = $likeModel -> add($params);
            if ($likeRet !== false) {
                //更新likecount
                $videoInfo['likecount'] = intval($videoInfo['likecount']) + 1;
                $videoRet = $videoModel -> save($videoInfo);
                //不需要给自己发送信息
                if($videoInfo['userid'] != $userid){
                    //添加message 消息对队
                    $messageModel = M('message');
                    $messageItem = array(
                        "fromuserid" => $userid,
                        "touserid" => $videoInfo['userid'],
                        "eventtype" => getMessageEventType("like"),
                        "video_id" => $video_id,
                        "addtime" => time()
                    );
                    $messageModel -> add($messageItem);


                    //发送FCM消息
                    //$this->msg_send('like',$videoInfo['userid'],$userid);

                }

                if ($videoRet !== false) {
                    return show_output(200, "");
                } else {
                    return show_output(300, "");
                }
            } else {
                return show_output(301, "");
            }
        } else {
            //已经like过
            return show_output(600, "");
        }
    }

    public function unlike($videoid = 0, $userid = 0) {
        //checkToken();
        if (empty($videoid) || empty($userid)) {
            return show_output(101, 'params error');
        }
        $video_id=$this->decode_vid($videoid);
        //获取视频内容
        $videoModel = M('video');
        $videoInfo = $videoModel -> where(array('id' => $video_id)) -> find();
        if ($videoInfo === false) {
            return show_output(404, "");
        }

        //检查是否已经like过
        $likeModel = M('vlike');
        $likeInfo = $likeModel -> where(array('videoid' => $videoInfo['id'], 'userid' => $userid, "relation" => 1)) -> find();
        if ($likeInfo != false) {
            //删除
            $where = array(
                "videoid" => $videoInfo['id'],
                "userid" => $userid,
                "relation" => 1
            );
            $likeRet = $likeModel -> where($where) -> delete();
            if ($likeRet !== false) {
                //更新likecount
                $videoRet = true;
                if (intval($videoInfo['likecount']) > 0) {
                    $videoInfo['likecount'] = intval($videoInfo['likecount']) - 1;
                    $videoRet = $videoModel -> save($videoInfo);
                }
                if ($videoRet !== false) {
                    return show_output(200, "");
                } else {
                    return show_output(300, "");
                }
            } else {
                return show_output(301, "");
            }
        } else {
            //没有like过
            return show_output(600, "");
        }
    }

    public function attention($fromuserid = 0, $touserid = 0, $vc = 0) {
        //checkToken();
        if (empty($fromuserid) || empty($touserid) || $touserid == 0 || $fromuserid == 0) {
            return show_output(101, 'params error');
        }

        //检查是否已经attention过
        $attentionModel = M('attention');
        $attentionInfo = $attentionModel -> where(array('fromuserid' => $fromuserid, 'touserid' => $touserid, "relation" => 1)) -> find();
        if ($attentionInfo == false) {
            //插入
            $params = array(
                "fromuserid" => $fromuserid,
                "touserid" => $touserid,
                "relation" => 1,
                "createtime" => time()
            );
            $attentionRet = $attentionModel -> add($params);

            //添加message 消息对队
            $messageModel = M('message');
            $messageItem = array(
                "fromuserid" => $fromuserid,
                "touserid" => $touserid,
                "eventtype" => getMessageEventType("attention"),
                "addtime" => time()
            );
            $messageModel -> add($messageItem);

            //发送FCM消息
            $fcmResult = $this->msg_send('follow',$touserid,$fromuserid);
            //\Think\Log::record("fcmResult:" . $fcmResult);

            //版本号高于16才自动发欢迎消息
            if (is_numeric($vc) && $vc > 16) {
                $userInfo = M('user') -> where(array('id' => $touserid)) -> field("username, avatar, country") -> find();
                if ($userInfo != false) {
                    $im = A("Im");
                    $ret = $im -> sendMessage($touserid, $fromuserid, $this -> getHelloMsg($userInfo['country']), $userInfo['username'], buildResourceUrlOne($userInfo)['avatar']);
                    if ($ret != "success") {
                        \Think\Log::record("RongYun Im sendMessage Error: " . $ret);
                    }
                }
            }

            if ($attentionRet !== false) {
                return show_output(200, "");
            } else {
                return show_output(300, "");
            }
        } else {
            //已经attention过
            return show_output(600, "");
        }
    }

    private function getHelloMsg($country = 'br') {
        $msg = array(
            "cn"=> array(
                "嗨，很高兴认识你。\\n别忘了补充你的个人资料、发布视频，让我更了解你哦"
                ),
            "en"=> array(
                "Hi,my friend.Nice to meet you!\\nWhy not complete your personal profile or publish video? I want to know more about you?"
                ),
            "br" => array(
                "Oi. Muito prazer em conhecer você!\\nNão esqueça de preencher os seus dados e postar vídeos para eu te conhecer melhor?"
                ),
            "es" => array(
                "Hola, mucho gusto!\\nNo te olvides llenar tus datos y subir videos para que yo pueda conocerte major?"
                )
        );
        $country = strtolower($country);
        if (!($country == "cn" ||
            $country == "en" ||
            $country == "br" ||
            $country == "es")) {
            $country = 'br';
        }
        return $msg[$country][0];
    }

    public function unattention($fromuserid = 0, $touserid = 0) {
        //checkToken();
        if (empty($fromuserid) || empty($touserid)) {
            return show_output(101, 'params error');
        }

        //检查是否已经attention过
        $attentionModel = M('attention');
        $attentionRet = $attentionModel -> where(array('fromuserid' => $fromuserid, 'touserid' => $touserid)) -> delete();
        if ($attentionRet != false) {
            return show_output(200, "");
        } else {
            return show_output(600, "");
        }
    }

    private function _uploadAvatar(){
        $upload = new upload();// 实例化上传类
        $upload->maxSize   =     1024 * 1024 ;// 设置附件上传大小1024kb
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     './uploads/avatar/'; // 设置附件上传根目录
        $upload->savePath  =     date('Ym'). '/' . date('d') . '/' . date('h') . '/';
        // 上传单个文件
        $info   =   $upload->uploadOne($_FILES['avatar']);
        if(!$info) {// 上传错误提示错误信息
            return show_output(501, "upload avatar error");
        }else{// 上传成功 获取上传文件信息
            return $info['savepath'].$info['savename'];
        }
    }

    public function get($userid = '', $curruserid = '') {
        //checkToken();
        $userid = intval($userid);
        if (empty($userid) || $userid <= 0) {
            return show_output(101, "");
        }
        if (empty($curruserid) || !is_numeric($curruserid)) {
            $curruserid = 0;
        }
        $curruserid = intval($curruserid);
        $userInfoSql = 'SELECT AA.*, COUNT(B.id) AS followcount FROM (SELECT user.id AS id,user.username AS username,user.gender AS gender,user.avatar AS avatar,user.motto AS motto, user.facebook AS f, user.facebook_name AS f_n, user.twitter as t,user.twitter_name as t_n, user.google as g, user.google_name as g_n, user.instagram AS i,user.instagram_name AS i_n ,COUNT(A.id) AS attentioncount FROM user user LEFT JOIN attention A ON user.id=A.fromuserid AND A.`touserid`!=user.`id` WHERE user.id = '.$userid.' AND user.status = 1 LIMIT 1) AS AA LEFT JOIN attention B ON AA.id=B.touserid AND B.`fromuserid`!=AA.`id` AND B.relation=1';
        if (is_numeric($curruserid) && $curruserid > 0) {
            $userInfoSql = 'SELECT CC.*, COUNT(D.id) AS islike FROM (SELECT AA.*, COUNT(B.id) AS followcount FROM (SELECT user.id AS id,user.username AS username,user.gender AS gender,user.avatar AS avatar,user.motto AS motto, user.facebook AS f,user.facebook_name AS f_n, user.twitter as t, user.twitter_name as t_n, user.google as g, user.google_name as g_n, user.instagram AS i,user.instagram_name AS i_n,COUNT(A.id) AS attentioncount FROM user user LEFT JOIN attention A ON user.id=A.fromuserid AND A.`touserid`!=user.`id` WHERE user.id = '.$userid.' AND user.status = 1 LIMIT 1) AS AA LEFT JOIN attention B ON AA.id=B.touserid AND B.`fromuserid` != AA.`id`  AND B.relation=1) AS CC LEFT JOIN attention D ON CC.id=D.touserid AND D.fromuserid='. $curruserid .' AND D.relation = 1';
        }
        $Model = new \Think\Model();
        $userinfo = $Model->query($userInfoSql);
        if ($userinfo !== false ) {
            if (is_array($userinfo) && count($userinfo) == 1) {
                $userinfo = $userinfo[0];
            }
            if($userinfo['id'] == 0)
                return show_output(400, "");
            if (isset($userinfo['islike']) && intval($userinfo['islike']) === 1) {
                $userinfo['attention'] = 1;
            } else {
                $userinfo['attention'] = 0;
            }
            if (isset($userinfo['islike'])) {
                unset($userinfo['islike']);
            }
            $userinfo = $this -> _bindSns($userinfo);
            $userinfo = buildResourceUrlOne($userinfo);
            return show_output(200, "", $userinfo);
        } else {
            return show_output(400, "");
        }
    }

    private function _bindSns($result) {
        if (empty($result)) {
            return $result;
        }
        if (isset($result['f'])) {
            if (!empty($result['f'])) {
                $result['f'] = 1;
            } else {
                $result['f'] = 0;
            }
        }
        if (isset($result['t'])) {
            if (!empty($result['t'])) {
                $result['t'] = 1;
            } else {
                $result['t'] = 0;
            }
        }
        if (isset($result['g'])) {
            if (!empty($result['g'])) {
                $result['g'] = 1;
            } else {
                $result['g'] = 0;
            }
        }
        if (isset($result['i'])) {
            if (!empty($result['i'])) {
                $result['i'] = 1;
            } else {
                $result['i'] = 0;
            }
        }
        return $result;
    }

    public function publishvideos($page = 1, $userid = '', $myid = '') {
        //checkToken();
        $pageSize = $this->_getPageSize;
        if (!is_numeric($page)) {
            $page = 1;
        }
        if (!is_numeric($userid)) {
            $userid = 0;
        }
        $myid = empty($myid)?$userid:$myid;

        $listCache = S(array('prefix'=>'user_publish_videos'));
        $cacheKey = 'user_publish_videos_' . $userid .'_'.$myid . '_' . $page;
        $result = $listCache -> $cacheKey;
        try {
            $videoModel = D('VideoView');
            $where = array('status' => 1, 'userid' => $userid);
            $result = $videoModel -> where($where)
                                  -> page($page, $pageSize)
                                  -> order('id desc')
                                  ->join('LEFT JOIN topic AS t ON t.id = video.topicid')
                                  -> field('topicid,t.title as topic_title,t.content as topic_content,t.picture as topic_picture,id,avatar,userid,username,description,thumb,filepath,duration,size,likecount,commentcount,createtime')
                                  -> select();

            if ($result) {

                $RModel = M('tag_relation');
                foreach ($result as &$v) {
                    //获取标签数组
                    $tags = $RModel->alias('r')->where("video_id=".$v["id"])
                          ->join('LEFT JOIN topic AS t ON t.id = r.tag_id')
                          ->field('r.tag_id,t.title,t.content ')
                          ->select();
                    $v['tags'] = $tags ;
                    if($v['topicid'])
                        $v['topic_picture']=getUploadResourceBaseUrl().$v['topic_picture'];
                }

                $this->encode_vid_arr($result,"id","uuid");

                $comments = array();
                if (count($result) > 0) {
                    $comments_sql = array();
                    for ($i=0; $i < count($result); $i++) {
                        $v_id = $result[$i]['id'];
                        $comments_sql[] = "(SELECT id,video_id AS videoid,userid,username,content,createtime FROM`comment` WHERE `video_id`= ".$v_id." AND `status`=1 ORDER BY id DESC LIMIT 2)";
                    }
                    $getCommentSql=implode(' UNION ', $comments_sql);

                    $Model = new \Think\Model();
                    $comments = $Model->query($getCommentSql);
                }
                $result = _mergeVideoComments($result, $comments);

                $listCache -> $cacheKey = $result;

            }
        } catch(\Exception $e) {
            return show_output(500, "");
        }

        if ($result !== false) {
            $attentions = array();
            $likes = array();
            if ($userid > 0) {
                $userids = '';
                for ($i=0; $i < count($result); $i++) {
                    $userids .= $result[$i]['userid'] . ',';
                }
                $attentionModel = M('attention');
                $attentionWhere = array(
                    'fromuserid' => $userid,
                    'touserid' => array('in', $userids . '0'),
                    'relation' => 1
                );
                $attentions = $attentionModel -> where($attentionWhere) -> select();

                $videoIds = '';
                for ($i=0; $i < count($result); $i++) {
                    $videoIds .= $result[$i]['id'] . ',';
                }
                $likeModel = M('vlike');
                $likeWhere = array(
                    'userid' => $myid,
                    'videoid' => array('in', $videoIds . '0'),
                    'relation' => 1
                );
                $likes = $likeModel -> where($likeWhere) -> select();
            }
            $result = _mergeVideoAttentions($result, $attentions);
            $result = _mergeVideoLikes($result, $likes);
            return show_output(200, "", buildResourceUrl($result));
        } else {
            return show_output(400, "");
        }
    }

    public function likevideos($page = 1, $userid = '', $myid = '') {
        //checkToken();
        $pageSize = $this->_getPageSize;
        if (!is_numeric($page)) {
            $page = 1;
        }
        if (!is_numeric($userid)) {
            $userid = 0;
        }
        $myid = empty($myid)?$userid:$myid;

        $listCache = S(array('prefix'=>'user_like_videos'));
        $cacheKey = 'user_like_videos' . $userid .'_'.$myid. '_' . $page;
        $result = $listCache -> $cacheKey;
        try {
            $videoModel = D('VideoLikeView');
            $where = array('status' => 1, 'userid' => $userid, 'relation' => 1);
            $result = $videoModel -> where($where)
                                  -> page($page, $pageSize)
                                  -> order('liketime desc')
                                  ->join('LEFT JOIN topic AS t ON t.id = video.topicid')
                                  -> field('t.id as topicid,t.title as topic_title,t.content as topic_content,t.picture as topic_picture,id,avatar,userid,username,description,thumb,filepath,duration,size,likecount,commentcount,createtime')
                                  -> select();

            if ($result) {
                $RModel = M('tag_relation');
                foreach ($result as &$v) {
                    //获取标签数组
                    $tags = $RModel->alias('r')->where("video_id=".$v["id"])
                          ->join('LEFT JOIN topic AS t ON t.id = r.tag_id')
                          ->field('r.tag_id,t.title,t.content ')
                          ->select();
                    $v['tags'] = $tags ;
                    if($v['topicid'])
                        $v['topic_picture']=getUploadResourceBaseUrl().$v['topic_picture'];
                }

                $this->encode_vid_arr($result,"id","uuid");

                $comments = array();
                if (count($result) > 0) {
                    $comments_sql = array();
                    for ($i=0; $i < count($result); $i++) {
                        $v_id = $result[$i]['id'];
                        $comments_sql[] = "(SELECT id,video_id AS videoid,userid,username,content,createtime FROM`comment` WHERE `video_id`= ".$v_id." AND `status`=1 ORDER BY id DESC LIMIT 2)";
                    }
                    $getCommentSql=implode(' UNION ', $comments_sql);

                    $Model = new \Think\Model();
                    $comments = $Model->query($getCommentSql);
                }
                $result = _mergeVideoComments($result, $comments);

                // $comments = array();
                // SQL效率太低
                // if (count($result) > 0) {
                //     $videoIds = '';
                //     for ($i=0; $i < count($result); $i++) {
                //         $videoIds .= '\'' . $result[$i]['id'] . '\',';
                //     }
                //     $getCommentSql = "select id, video_id as videoid,userid,username,content,createtime from comment a where 2>(select count(*) from comment where video_id=a.video_id and id>a.id  and video_id in ($videoIds '0') and status=1) and a.video_id in ($videoIds '0')  and a.status=1 order by a.video_id,a.id desc";
                //     $Model = new \Think\Model();
                //     $comments = $Model->query($getCommentSql);
                // }
                // $result = _mergeVideoComments($result, $comments);

                $listCache -> $cacheKey = $result;

            }

        } catch(\Exception $e) {
            return show_output(500, "");
        }

        if ($result !== false) {
            $attentions = array();
            $likes = array();
            if ($userid > 0) {
                $userids = '';
                for ($i=0; $i < count($result); $i++) {
                    $userids .= $result[$i]['userid'] . ',';
                }
                $attentionModel = M('attention');
                $attentionWhere = array(
                    'fromuserid' => $userid,
                    'touserid' => array('in', $userids . '0'),
                    'relation' => 1
                );
                $attentions = $attentionModel -> where($attentionWhere) -> select();

                $videoIds = '';
                for ($i=0; $i < count($result); $i++) {
                    $videoIds .= $result[$i]['id'] . ',';
                }
                $likeModel = M('vlike');
                $likeWhere = array(
                    'userid' => $myid,
                    'videoid' => array('in', $videoIds . '0'),
                    'relation' => 1
                );
                $likes = $likeModel -> where($likeWhere) -> select();
            }
            $result = _mergeVideoAttentions($result, $attentions);
            $result = _mergeVideoLikes($result, $likes);
            return show_output(200, "", buildResourceUrl($result));
        } else {
            return show_output(400, "");
        }
    }

    public function getfans($myid = 0, $userid = 0, $page = 1) {
        //checkToken();
        //$pageSize = $this->_getPageSize;
        $pageSize = 20;
        if (!is_numeric($page)) {
            $page = 1;
        }
        if (!is_numeric($userid)) {
            $userid = 0;
        }

        $result = array();
        try {
            // $attentionModel = D('FansView');
            // $where = array('touserid' => $userid, 'relation' => 1);
            // $result = $attentionModel -> where($where)
            //                       -> page($page, $pageSize)
            //                       -> order('id desc')
            //                       -> field('userid,username,avatar')
            //                       -> select();
            $getFansSql = "select B.id,B.username,B.avatar,C.relation as attention from attention A inner join user B on A.fromuserid = B.id AND B.id != $userid left join attention C ON C.touserid = B.id AND C.relation = 1 AND C.fromuserid = $myid where A.touserid=$userid and A.relation = 1 group by B.id order by B.id desc limit " . (($page - 1) * $pageSize) . ',' . ($pageSize);
            $Model = new \Think\Model();
            $result = $Model->query($getFansSql);
        } catch(\Exception $e) {
            return show_output(500, "");
        }
        if ($result !== false) {
            for ($i = 0; $i < count($result); $i++) {
                if ($result[$i]['attention'] !== '1') {
                    $result[$i]['attention'] = 0;
                } else {
                    $result[$i]['attention'] = intval($result[$i]['attention']);
                }
            }
            return show_output(200, "", buildResourceUrl($result));
        } else {
            return show_output(400, "");
        }
    }

    public function getfollows($myid = 0, $userid = 0, $page = 1) {
        //checkToken();
        //$pageSize = $this->_getPageSize;
        $pageSize = 20;
        if (!is_numeric($page)) {
            $page = 1;
        }
        if (!is_numeric($userid)) {
            $userid = 0;
        }

        $result = array();
        try {
            // $followModel = D('FollowsView');
            // $where = array('fromuserid' => $userid, 'relation' => 1);
            // $result = $followModel -> where($where)
            //                       -> page($page, $pageSize)
            //                       -> order('id desc')
            //                       -> field('userid,username,avatar')
            //                       -> select();
            $getFollowsSql = "select B.id,B.username,B.avatar,C.relation as attention from attention A inner join user B on A.touserid = B.id  AND B.id != $userid  left join attention C ON C.touserid = B.id AND C.relation = 1 AND C.fromuserid = $myid where A.fromuserid=$userid and A.relation = 1 group by B.id order by B.id desc limit " . (($page - 1) * $pageSize) . ',' . ($pageSize);
            $Model = new \Think\Model();
            $result = $Model->query($getFollowsSql);
        } catch(\Exception $e) {
            return show_output(500, "");
        }
        if ($result !== false) {
            for ($i = 0; $i < count($result); $i++) {
                if ($result[$i]['attention'] !== '1') {
                    $result[$i]['attention'] = 0;
                } else {
                    $result[$i]['attention'] = intval($result[$i]['attention']);
                }
            }
            return show_output(200, "", buildResourceUrl($result));
        } else {
            return show_output(400, "");
        }
    }



    //用户搜索
    public function search($id='', $name='',$myid = 0) {

        if (empty($id) && empty($name)) {
            return show_output(101, 'params error');
        }
        $userModel = M('user');
        $where = array(
            'status' => 1
        );
        if($id)
            $where['user.id'] = $id;
        if($name)
            $where['username'] = array('LIKE',"%$name%");

        $result = $userModel->where($where)
            ->join("left join attention A on A.touserid=user.id and A.fromuserid={$myid} and A.relation=1")
            ->field('user.id,username,motto,avatar,A.relation as attention')->limit(20)->select();
            //print_r($result);
        if ($result != false) {
            for ($i = 0; $i < count($result); $i++) {
                if ($result[$i]['attention'] !== '1') {
                    $result[$i]['attention'] = 0;
                } else {
                    $result[$i]['attention'] = intval($result[$i]['attention']);
                }
            }
            return show_output(200, "", buildResourceUrl($result));
        }else{
            return show_output(404, "no person");
        }
    }

    public function nointerest($fromuserid = 0, $touserid = 0) {
        //checkToken();
        if (empty($fromuserid) || empty($touserid)) {
            return show_output(101, 'params error');
        }

        //检查是否已经attention过
        $attentionModel = M('attention');
        $attentionInfo = $attentionModel -> where(array('fromuserid' => $fromuserid, 'touserid' => $touserid, "relation" => 3)) -> find();
        if ($attentionInfo == false) {
            //插入
            $params = array(
                "fromuserid" => $fromuserid,
                "touserid" => $touserid,
                "relation" => 3,
                "createtime" => time()
            );
            $attentionRet = $attentionModel -> add($params);

            if ($attentionRet !== false) {
                return show_output(200, "");
            } else {
                return show_output(300, "");
            }
        } else {
            //已经nointerest过
            return show_output(600, "");
        }
    }

    //1、推荐用户
    //2、新用户（10分钟内的用户）
    //3、粉丝未互相关注
    //4、活跃用户(跟1重合，暂去掉)
    //5、随机推荐未关注用户
    public function recom_single($myid = 0, $country='BR') {

        if (empty($myid)) {
            return show_output(101, 'params error');
        }

        $pageSize = 10;

        $result = array();
        $result = $this -> _recom_recommend($country, $myid);
        if (count($result) < $pageSize) {
            $newResult = $this -> _recom_newuser($country, $myid);
            for ($i = 0; $i < count($newResult); $i++) {
                array_push($result, $newResult[$i]);
            }
        }
        if (count($result) < $pageSize) {
            $followResult = $this -> _recom_followme($country, $myid);
            for ($i = 0; $i < count($followResult); $i++) {
                array_push($result, $followResult[$i]);
            }
        }
        if (count($result) < $pageSize) {
            $randomResult = $this -> _recom_getrandom($country, $myid, 1, $pageSize - count($result));
            for ($i = 0; $i < count($randomResult); $i++) {
                array_push($result, $randomResult[$i]);
            }
        }
        if (!empty($result) && $result != false) {
            return show_output(200, "", buildResourceUrl($result));
        } else {
            return show_output(404, "no person");
        }
    }

    //1、推荐用户
    //2、新用户（10分钟内的用户）
    //3、粉丝未互相关注
    //4、活跃用户(跟1重合，暂去掉)
    //5、随机推荐未关注用户
    public function recom($myid = 0,$page = 1,$country='BR') {
        $result = array();
        $pageSize = 10;
        if (!is_numeric($page)) {
            $page = 1;
        }
        if (empty($myid)) {
            $result = $this -> _recom_getrandom($country, 0, $page, $pageSize);
            //return show_output(101, 'params error');
        } else {
            if ($page == 1) {
                $result = $this -> _recom_recommend($country, $myid);
            } else if ($page == 2) {
                $result = $this -> _recom_newuser($country, $myid);
            } else if ($page == 3) {
                $result = $this -> _recom_followme($country, $myid);
            } else {
                $result = $this -> _recom_getrandom($country, $myid, $page - 3, $pageSize);
            }
            if ($page < 4 && count($result) < 10) {
                $randomResult = $this -> _recom_getrandom($country, $myid, 1, $pageSize - count($result));
                for ($i = 0; $i < count($randomResult); $i++) {
                    array_push($result, $randomResult[$i]);
                }
            }
        }
        if (!empty($result) && $result != false) {
            return show_output(200, "", buildResourceUrl($result));
        } else {
            return show_output(404, "no person");
        }
    }

    //获取推荐用户
    private function _recom_recommend($country, $myid) {
        $userModel = M('user');
        $where=" user.country='".$country."' and status=1 and isrecommend=1 and  A.id IS NULL and B.id IS NULL" . $this -> _exclude_inner_user();
        $result = $userModel->where($where)
            ->join("left join attention A on A.touserid=user.id and A.fromuserid={$myid} and A.relation=1")
            ->join("left join attention B on B.touserid=user.id and B.fromuserid={$myid} and B.relation=3")
            ->field('user.id,username,motto,avatar')->order("user.recommendtime desc")->select();
        return $result;
    }

    //获取新注册的
    private function _recom_newuser($country, $myid) {
        $userModel = M('user');
        $where=" user.country='".$country."' and status=1 and isrecommend=0 and user.addtime >= ".$this -> _recom_newuser_timelimit()." and  A.id IS NULL and B.id IS NULL" . $this -> _exclude_inner_user();
        $result = $userModel->where($where)
            ->join("left join attention A on A.touserid=user.id and A.fromuserid={$myid} and A.relation=1")
            ->join("left join attention B on B.touserid=user.id and B.fromuserid={$myid} and B.relation=3")
            ->field('user.id,username,motto,avatar')->order("user.addtime desc")->select();
            //print_r($userModel->getLastSql());
        return $result;
    }

    private function _exclude_inner_user() {
        return " and user.id not in (1361,1362,1363,1364,1365,1366)";
    }

    //获取关注我的我未关注的用户
    private function _recom_followme($country, $myid) {
        $userModel = M('user');
        $followMeUser = M('attention') -> where(array("touserid" => $myid, "relation" => 1)) -> field('fromuserid') -> select();
        $followMeUserIds = "(-1";
        for ($i = 0; $i < count($followMeUser); $i++) {
            $followMeUserIds .= ",". $followMeUser[$i]['fromuserid'];
        }
        $followMeUserIds .= ")";
        $where=" user.country='".$country."' and status=1 and isrecommend=0 and user.id in ".$followMeUserIds." and user.addtime < ".$this -> _recom_newuser_timelimit()." and A.id IS NULL and B.id IS NULL" . $this -> _exclude_inner_user();
        $result = $userModel->where($where)
            ->join("left join attention A on A.touserid=user.id and A.fromuserid={$myid} and A.relation=1")
            ->join("left join attention B on B.touserid=user.id and B.fromuserid={$myid} and B.relation=3")
            ->field('user.id,username,motto,avatar')->order("user.addtime desc")->select();
            //print_r($userModel->getLastSql());
        return $result;
    }

    //随机推荐未关注用户
    private function _recom_getrandom($country, $myid, $page, $pageSize) {
        $userModel = M('user');
        $attentionModel = M('attention');
        $followMeUser = $attentionModel -> where(array("touserid" => $myid, "relation" => 1)) -> field('fromuserid') -> select();
        $followMeUserIds = "(-1";
        for ($i = 0; $i < count($followMeUser); $i++) {
            $followMeUserIds .= ",". $followMeUser[$i]['fromuserid'];
        }

        $iFollowUser = $attentionModel -> where(array("fromuserid" => $myid, "relation" => 1)) -> field('touserid') -> select();
        for ($i = 0; $i < count($iFollowUser); $i++) {
            $followMeUserIds .= ",". $iFollowUser[$i]['touserid'];
        }

        $noInstestedUser = $attentionModel -> where(array("fromuserid" => $myid, "relation" => 3)) -> field('touserid') -> select();
        for ($i = 0; $i < count($noInstestedUser); $i++) {
            $followMeUserIds .= ",". $noInstestedUser[$i]['touserid'];
        }
        $followMeUserIds .= ")";

        $where=" user.country='".$country."' and user.status=1 and user.isrecommend=0 and user.id not in ".$followMeUserIds." and user.addtime < ".$this -> _recom_newuser_timelimit() . $this -> _exclude_inner_user();
        $result = $userModel->where($where)
            -> join("join (SELECT RAND() * (SELECT MAX(id) FROM `user`) AS nid) t2 ON user.id > t2.nid")
            ->field('user.id,user.username,user.motto,user.avatar')->page($page, $pageSize)->order("user.id")->select();
            //print_r($userModel->getLastSql());
        return $result;
    }

    private function _recom_newuser_timelimit() {
        return time() - 10 * 60;
    }

    public function getavatar_jp($id = '', $cb="") {
        if (strlen($cb) > 0) {
            $videoModel = M('user');
            $where = array('id' => $id, "status" => 1);
            $result = $videoModel -> where($where)
                                  -> field('avatar')
                                  -> find();
            if ($result !== false && $result !== null) {
                return show_output(200, "", buildResourceUrlOne($result), "jsonp", $cb);
            } else {
                return show_output(400, "", array(), "jsonp", $cb);
            }
        } else {
            if (empty($cb)) {
                $cb = "error";
            }
            return show_output(101, "", array(), "jsonp", $cb);
        }
    }
}
?>