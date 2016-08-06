<?php
namespace Api\Controller;
use Think\Upload;
/**
 * 已经完成uuid改造
 */
class VideoController extends SController {

    public function get_jp($id = '', $cb="") {
        $id = $this->decode_vid($id); //改造uuid算法 可逆算法

        if (strlen($cb) > 0) {
            $detailCache = S(array('prefix' => 'detail'));
            $cacheKey = 'detail_' . $id;
            $result = $detailCache -> $cacheKey;
            if ($result == false) {
                try {
                    $videoModel = D('VideoView');
                    $where = array('id' => $id, "status" => 1);
                    $result = $videoModel -> where($where)
                                          -> field('id,avatar,userid,username,description,thumb,filepath,likecount,commentcount,createtime')
                                          -> find();
                    if ($result) {
                        $result['id']=$this->encode_vid($result['id']);
                        $detailCache -> $cacheKey = $result;
                    }
                } catch(\Exception $e) {
                    return show_output(500, "", array(), "jsonp", $cb);
                }
            }
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

    //uuid fixed
    public function get($id='', $userid='') {
        //checkToken();
        if (!is_numeric($userid)) {
            $userid = 0;
        }
        $id = $this->decode_vid($id); //改造uuid算法 可逆算法
        $detailCache = S(array('prefix' => 'detail'));
        $cacheKey = 'detail_' . $id;
        $result = $detailCache -> $cacheKey;
        if ($result == false) {
            try {
                $videoModel = D('VideoView');
                $where = array('id' => $id, "status" => 1);
                $result = $videoModel -> where($where)
                                      ->join('LEFT JOIN topic AS t ON t.id = video.topicid')
                                      -> field('id,avatar,userid,topicid,t.title as topic_title,t.content as topic_content,t.picture as topic_picture,username,description,thumb,filepath,duration,size,likecount,commentcount,createtime')
                                      -> find();
                if ($result) {
                    $result['uuid']=$this->encode_vid($result['id']);
                    $detailCache -> $cacheKey = $result;
                }
            } catch(\Exception $e) {
                return show_output(500, "");
            }
        }
        if ($result !== false && $result !== null) {
            //获取标签数组
            $RModel = M('tag_relation');
            $tags = $RModel->alias('r')->where("video_id=".$result["id"])
                  ->join('LEFT JOIN topic AS t ON t.id = r.tag_id')
                  ->field('r.tag_id,t.title,t.content ')
                  ->select();
            $result['tags'] = $tags ;


            $attentions = array();
            $likes = array();
            if ($userid > 0) {
                $attentionModel = M('attention');
                $attentionWhere = array(
                    'fromuserid' => $userid,
                    'touserid' => $result['userid'],
                    'relation' => 1
                );
                $attentions = $attentionModel -> where($attentionWhere) -> select();

                $likeModel = M('vlike');
                $likeWhere = array(
                    'userid' => $userid,
                    'videoid' => $result['id'],
                    "relation" => 1
                );
                $likes = $likeModel -> where($likeWhere) -> select();
            }
            $result = $this -> _mergeSingleVideoAttentions($result, $attentions);
            $result = $this -> _mergeSingleVideoLikes($result, $likes);
            return show_output(200, "", buildResourceUrlOne($result));
        } else {
            return show_output(400, "");
        }
    }

    public function getcomments_jp($id = '', $cb = '') {
        $id = $this->decode_vid($id);
        if (strlen($cb) > 0) {
            $pageSize = 6;
            $page = 1;
            $commentModel = D('CommentView');
            $commentWhere = array('video_id' => $id, 'status' => 1);
            $result = $commentModel -> where($commentWhere)
                                    -> page($page, $pageSize)
                                    -> order('createtime desc')
                                    -> field('id,video_id as videoid,userid,username,content,createtime,avatar')
                                    -> select();
            if ($result === false) {
                return show_output(300, "", array(), "jsonp", $cb);
            } else {
                $this->encode_vid_arr($result);
                $result = buildResourceUrl($result);
                return show_output(200, "", buildResourceUrlOne($result), "jsonp", $cb);
            }
        } else {
            if (empty($cb)) {
                $cb = "error";
            }
            return show_output(101, "", array(), "jsonp", $cb);
        }
    }

    public function getcomments($id='', $page = 1) {
        //checkToken();
        $id = $this->decode_vid($id); //改造uuid算法 可逆算法
        $pageSize = $this->_getPageSize;
        if (!is_numeric($page)) {
            $page = 1;
        }
        $commentModel = D('CommentView');
        $commentWhere = array('video_id' => $id, 'status' => 1);
        $result = $commentModel -> where($commentWhere)
                                -> page($page, $pageSize)
                                -> order('createtime desc')
                                -> field('id,video_id as videoid,userid,username,content,createtime,avatar')
                                -> select();
        if ($result === false) {
            return show_output(300, "");
        } else {
            $this->encode_vid_arr($result);
            $result = buildResourceUrl($result);
            return show_output(200, "", buildResourceUrlOne($result));
        }
    }

    //获取视频点赞列表
    public function getlikeusers($id='',$myid = 0, $page = 1) {
        $id = $this->decode_vid($id); //改造uuid算法 可逆算法
        $pageSize = 20;
        if (!is_numeric($page)) {
            $page = 1;
        }

        $VModel = M('vlike');
        $where = array('v.videoid' => $id, "v.relation" => 1);
        $result = $VModel ->alias('v')-> where($where)
                          ->page($page, $pageSize)
                          ->join("LEFT JOIN user AS user ON v.userid=user.id")
                          ->join("left join attention C ON C.touserid = user.id AND C.relation = 1 AND C.fromuserid = $myid")
                          ->field('user.id,user.username,user.avatar,v.createtime,C.relation as attention')
                          ->order('v.createtime desc')
                          ->group('user.id')
                          ->select();

        if ($result === false) {
            return show_output(300, "");
        } else {
            for ($i = 0; $i < count($result); $i++) {
                if ($result[$i]['attention'] !== '1') {
                    $result[$i]['attention'] = 0;
                } else {
                    $result[$i]['attention'] = intval($result[$i]['attention']);
                }
            }
            $result = buildResourceUrl($result);
            return show_output(200, "", $result);
        }
    }



    private function _mergeSingleVideoAttentions($result, $attention) {
        if ($attention != false && !empty($attention)) {
            $result['attention'] = 1;
        } else {
            $result['attention'] = 0;
        }
        return $result;
    }

    private function _mergeSingleVideoLikes($result, $likes) {
        if ($likes != false && !empty($likes)) {
            $result['like'] = 1;
        } else {
            $result['like'] = 0;
        }
        unset($result['id']);
        return $result;
    }

    public function report($videoid = 0, $userid = 0, $content = '') {
        //checkToken();
        $videoid = $this->decode_vid($videoid); //改造uuid算法 可逆算法
        if ($videoid === 0 || empty($videoid)) {
            return show_output(101, 'params error');
        }

        if ($userid == 94 ||
            $userid == 82 ||
            $userid == 79 ||
            $userid == 391 ||
            $userid == 414) {
            $this -> _hideVideoFromHot($videoid);
        }

        $reportModel = M('report_video');
        $reportRet = $reportModel -> add(
            array(
                "video_id" => $videoid,
                "userid" => $userid,
                "content" => $content,
                "createtime" => time()
            )
        );
        if ($reportRet == false) {
            return show_output(300, "");
        } else {
            return show_output(200, "");
        }

    }

    private function _hideVideoFromHot($videoid) {
        $videoModel = M('video');
        $ret = $videoModel -> where(array("id" => $videoid)) -> setField('ishot', 2);
        if ($ret != false) {
            return true;
        }
        return false;
    }

    public function delete($videoid = 0, $userid = 0) {
        //checkToken();
        $videoid = $this->decode_vid($videoid); //改造uuid算法 可逆算法
        if ($videoid === 0 || empty($videoid)) {
            return show_output(101, 'params error');
        }

        $where = array('id' => $videoid);
        $videoModel = M('video');
        $videoInfo = $videoModel -> where($where) -> find();
        if ($videoInfo != false) {
            if ($videoInfo['userid'] != $userid) {
                return show_output(401, "");
            }
            $videoInfo['status'] = 2;
            $ret = $videoModel -> save($videoInfo);
            if ($ret !== false) {
                return show_output(200, "");
            }else{
                return show_output(300, "");
            }
        }
        return show_output(400, "");
    }

    /**
     * [_add_vlike description]
     * @param [type] $userid  [用户id]
     * @param [type] $ret     [视频id]
     * @param [type] $country [description]
     */
    private function _add_vlike($userid,$ret,$country){
        if(in_array($userid, array('55','75','86','413'))){
            return ;
        }
        //默认给1个赞
        $sys_userid = $this-> getRandUserId();
        $likeModel = D('vlike');//插入
        $likeparams = array(
            "videoid" => $ret,
            "userid" => $sys_userid,
            "country" => $country,
            "createtime" => time()
        );
        $likeModel -> add($likeparams);

        //(无粉丝)时添加3~10个粉丝
        $attentionModel = M('attention');
        $messageModel = M('message');
        $f_num = $attentionModel->where("relation=1 AND fromuserid != {$userid} AND touserid = {$userid}")->count();
        if($f_num==0){
            $k=rand(2,3);
            $userids = $this-> getRandUserId($k);
            $params=$messageItem=array();
            $eventType = getMessageEventType("attention");
            foreach ($userids as $uid) {
                $params[] = array("fromuserid" => $uid,"touserid" => $userid,"relation" => 1,"createtime" => time());
                $messageItem[] = array("fromuserid" => $uid,"touserid" => $userid,"eventtype" => $eventType,"addtime" => time());
            }
            $attentionRet = $attentionModel -> addAll($params);
            $messageRet = $messageModel -> addAll($messageItem);
        }
    }
    //添加标签
    private function _add_tags($videoid,$tags=""){
        if(empty($videoid)||empty($tags)) return;

        $tags = explode(',', $tags);

        $R = M("tag_relation");
        $params=array();
        foreach ($tags as $tag_id) {
            $params[] = array("tag_id" => $tag_id,"video_id" => $videoid);
        }
        $res = $R -> addAll($params);
    }

    public function add($userid = 0, $cateid = 1, $description = '', $duration = 0, $topicid = '', $country="BR", $status=1) {
        //checkToken();
        $country="BR";
        $createtime = time();
        if($_POST['__token__']){
            //后台防止重复提交
            $__token__=session('__token__');
            session('__token__',null);
            if($__token__ != $_POST['__token__']){
                return show_output(101, 'resubmit');
            }
            //后台上传的视频，调整时间戳
            $createtime = $createtime-rand(1, 1800);
        }
        $params = array(
            "userid" => intval($userid),
            "cateid" => intval($cateid),
            "description" => $description,
            "duration" => intval($duration)
        );
        if ($params['userid'] === 0 ||
            $params['cateid'] === 0 ||
            $params['duration'] === 0) {
            return show_output(101, 'params error');
        }



        $thumb = $this -> _uploadThumb();
        $videoInfo = $this -> _uploadVideo();
        $filePath = $videoInfo['savepath'].$videoInfo['savename'];
        $params['thumb'] = $thumb;
        $params['filepath'] = $filePath;
        $params['size'] = intval(intval($videoInfo['size']) / 1024);
        //$params['uuid'] = gen_uuid();
        //先不审核
        $params['status'] = $status;
        $params['createtime'] = time();
        $params['updatetime'] = time();
        $params['topicid'] = 0; //专题id为0
        $params['likecount'] = 0; //默认给一个赞
        if ($userid == 1361 ||
            $userid == 1362 ||
            $userid == 1363 ||
            $userid == 1364 ||
            $userid == 1365 ||
            $userid == 1366) {
            $params['ishot'] = 2;
        }

        $videoModel = M('video');
        if (stripos($country, ",") === false) {
            if (!checkCountry($country)) {
                $country = getDefaultCountry();
            }
            $params['country'] = $country;
            $ret = $videoModel -> add($params);
            if ($ret !== false) {
                //默认给1个赞
                $this->_add_vlike($userid,$ret,$country);

                $this->_add_tags($ret,$topicid);

                return show_output(200, "", array('id' => $this->encode_vid($ret)));
            } else {
                return show_output(301, "");
            }
        } else {
            $countryArr = explode(",", $country);
            $failCount = 0;
            if (count($countryArr) > 0) {
                for($i = 0; $i < count($countryArr); $i++) {
                    $country = $countryArr[$i];
                    if (!checkCountry($country)) {
                        $country = getDefaultCountry();
                    }
                    $params['country'] = $country;
                    $ret = $videoModel -> add($params);
                    if ($ret === false) {
                        $failCount = $failCount + 1;
                    }else{
                        //默认给1个赞
                        $this->_add_vlike($userid,$ret,$country);

                        $this->_add_tags($ret,$topicid);
                    }
                }
            }
            if ($failCount > 0) {
                return show_output(200, "part fail:" . $failCount);
            } else {
                return show_output(200, "");
            }
        }
    }

    private function _uploadThumb(){
        $upload = new upload();// 实例化上传类
        $upload->maxSize   =     1024 * 1024 ;// 设置附件上传大小1024kb
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     './uploads/'; // 设置附件上传根目录
        $upload->savePath  =     date('Ym'). '/' . date('d') . '/' . date('h') . '/';
        // 上传单个文件
        $info   =   $upload->uploadOne($_FILES['thumb']);
        if(!$info) {// 上传错误提示错误信息
            return show_output(501, "upload thumb error".$upload->getError());
        }else{// 上传成功 获取上传文件信息
            return $info['savepath'].$info['savename'];
        }
    }

    private function _uploadVideo(){
        $upload = new upload();// 实例化上传类
        $upload->maxSize   =     1024 * 1024 * 30 ;// 设置附件上传大小30MB
        $upload->exts      =     array('mp4');// 设置附件上传类型
        $upload->rootPath  =      './uploads/'; // 设置附件上传根目录
        $upload->savePath  =     date('Ym'). '/' . date('d') . '/' . date('h') . '/';
        // 上传单个文件
        $info   =   $upload->uploadOne($_FILES['video']);
        if(!$info) {// 上传错误提示错误信息
            return show_output(501, "upload video error".$upload->getError());
        }else{// 上传成功 获取上传文件信息
            return $info;
        }
    }


}
?>