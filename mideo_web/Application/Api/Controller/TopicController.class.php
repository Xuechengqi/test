<?php
namespace Api\Controller;
/**
 * 已经完成uuid改造
 */
class TopicController extends SController {

    public function gettopic_jp($topicid = 0, $cb = "") {
        if (!is_numeric($topicid) || $topicid < 1 || strlen($cb) === 0) {
            if (empty($cb)) {
                $cb = 'error';
            }
            return show_output(101, "params error", array(), "jsonp", $cb);
        }

        $listCache = S(array('prefix'=>'topic_detail'));
        $cacheKey = 'topic_detail_'. $topicid;
        $result = $listCache -> $cacheKey;
        if ($result == false) {
            $topicModel = M('topic');
            $where = array(
                "id" => $topicid
            );
            $result = $topicModel -> where($where)
                                -> field('id, title, content, picture, video')
                                -> find();
            $listCache -> $cacheKey = $result;
        }
        if ($result === false) {
            return show_output(300, "", array(), "jsonp", $cb);
        } else {
            return show_output(200, "", buildSingleTopicResourceUrl($result), "jsonp", $cb);
        }
    }

    public function gettopics_jp($country = 'BR', $cb = '') {
        $page = 1;
        if (empty($country) || empty($cb)) {
            if (empty($cb)) {
                $cb = 'error';
            }
            return show_output(101, "params error", array(), "jsonp", $cb);
        }
        if (!checkCountry($country)) {
            $country = getDefaultCountry();
        }

        $listCache = S(array('prefix'=>'topic'));
        $cacheKey = 'topic_list_'. $page;
        $result = $listCache -> $cacheKey;
        if ($result == false) {
            if ($page == 1) {
                unset($listCache -> $cacheKey);
            }
            $topicModel = M('topic');
            $where = array(
                "_string" => " country = '$country'  OR country = 'AA'"
            );
            $result = $topicModel -> where($where)
                                -> page($page, $this->_getPageSize)
                                -> order('id desc')
                                -> field('id, title, content, picture, video')
                                -> select();
            $listCache -> $cacheKey = $result;
        }
        if ($result === false) {
            return show_output(300, "", array(), "jsonp", $cb);
        } else {
            return show_output(200, "", buildTopicResourceUrl($result), "jsonp", $cb);
        }
    }

    public function getdetail($id = 0 ) {
        //checkToken();
        if (empty($id)) {
            return show_output(101, "params error");
        }

        $listCache = S(array('prefix'=>'topic'));
        $cacheKey = 'topic_detail_'. $id;
        $result = $listCache -> $cacheKey;
        if ($result == false) {
            $topicModel = M('topic');
            $where = array(
                "id" => $id
            );
            $result = $topicModel -> where($where)
                                -> field('id, title, summary, content, picture, video')
                                -> select();
            $listCache -> $cacheKey = $result;
        }
        if ($result === false) {
            return show_output(300, "db error");
        } else {
            $result = buildTopicResourceUrl($result);
            return show_output(200, "", $result[0]);
        }
    }

    public function gettopics($page = 1, $country = 'BR') {
        //checkToken();
        if (!is_numeric($page) || $page < 1 || empty($country)) {
            return show_output(101, "params error");
        }
        if (!checkCountry($country)) {
            $country = getDefaultCountry();
        }

        $listCache = S(array('prefix'=>'topic'));
        $cacheKey = 'topic_list_'. $page;
        $result = $listCache -> $cacheKey;
        if ($result == false) {
            if ($page == 1) {
                unset($listCache -> $cacheKey);
            }
            $topicModel = M('topic');
            $where = array(
                "status"=>1,
                "show"=>1,
                "_string" => " country = '$country'  OR country = 'AA' "
            );
            $result = $topicModel -> where($where)
                                -> page($page, 30)
                                -> order('ord desc')
                                -> field('id, title, summary, content, picture, video')
                                -> select();
            $listCache -> $cacheKey = $result;
        }
        if ($result === false) {
            return show_output(300, "db error");
        } else {
            return show_output(200, "", buildTopicResourceUrl($result));
        }
    }

    public function gettags($page = 1, $country = 'BR') {
        //checkToken();
        if (!is_numeric($page) || $page < 1 || empty($country)) {
            return show_output(101, "params error");
        }
        if (!checkCountry($country)) {
            $country = getDefaultCountry();
        }

        $listCache = S(array('prefix'=>'topic'));
        $cacheKey = 'tags_'. $page;
        $result = $listCache -> $cacheKey;
        if ($result == false) {
            if ($page == 1) {
                unset($listCache -> $cacheKey);
            }
            $topicModel = M('topic');
            $where = array(
                "status"=>1,
                "_string" => " country = '$country'  OR country = 'AA' "
            );
            $result = $topicModel -> where($where)
                                -> page($page, 30)
                                -> order('ord desc')
                                -> field('id, title ')
                                -> select();
            $listCache -> $cacheKey = $result;
        }
        if ($result === false) {
            return show_output(300, "db error");
        } else {
            return show_output(200, "", buildTopicResourceUrl($result));
        }
    }

    public function gethotvideos_jp($topicid = 0, $cb = '') {
        $pageSize = $this->_getPageSize;
        $page = 1;
        if (!is_numeric($topicid) || $topicid < 1 || strlen($cb) === 0) {
            if (empty($cb)) {
                $cb = 'error';
            }
            return show_output(100, "params error", array(), "jsonp", $cb);
        }

        $listCache = S(array('prefix'=>'topic_videos_jp'));
        $cacheKey = 'topic_videos_jp_' . $topicid . '_' . $page;
        $result = $listCache -> $cacheKey;
        if ($result == false) {
            if ($page == 1) {
                unset($listCache -> $cacheKey);
            }
            try {
                $videoModel = D('VideoView');
                $where = array('status' => 1, 'topicid' => $topicid, 'ishot' => 1);
                $result = $videoModel -> where($where)
                                      -> page($page, $pageSize)
                                      -> order('id desc')
                                      -> field('id,uuid,avatar,userid,username,description,thumb,likecount,commentcount,createtime')
                                      -> select();
                if ($result) {
                    $this->encode_vid_arr($result,"id","uuid");
                    $listCache -> $cacheKey = $result;
                }
            } catch(\Exception $e) {
                return show_output(500, "", array(), "jsonp", $cb);
            }
        }
        if ($result !== false) {
            for ($i = 0; $i < count($result); $i++) {
                $result[$i]['id'] = $result[$i]['uuid'];
                unset($result[$i]['uuid']);
            }
            return show_output(200, "", buildResourceUrl($result), "jsonp", $cb);
        } else {
            return show_output(400, "", array(), "jsonp", $cb);
        }
    }

    public function getvideos($topicid = 0, $page = 1, $type="hot", $userid = 0) {
        //checkToken();
        $pageSize = $this->_getPageSize;
        if (!is_numeric($page)) {
            $page = 1;
        }
        if (!is_numeric($userid)) {
            $userid = 0;
        }
        if (!is_numeric($topicid) || $topicid < 1) {
            return show_output(100, "params error");
        }

        $listCache = S(array('prefix'=>'topic_videos'));
        $cacheKey = 'topic_videos_' . $topicid . '_' . $type . '_' . $page;
        $result = $listCache -> $cacheKey;
        if ($result == false) {
            if ($page == 1) {
                unset($listCache -> $cacheKey);
            }
            try {

                $RModel = M('tag_relation');
                $where = array('video.status' => 1, 'r.tag_id' => $topicid);
                $order = "video.id desc";
                if ($type == "hot") {
                    $order = "video.likecount desc";
                    $where['video.likecount'] = array('egt',1);
                }

                $result = $RModel ->alias('r') -> where($where)
                                      -> page($page, $pageSize)
                                      -> order($order)
                                      ->join("LEFT JOIN video AS video ON r.video_id=video.id")
                                      ->join("LEFT JOIN user AS user ON video.userid=user.id")
                                      -> field('video.id,user.avatar,video.userid,user.username,video.description,video.thumb,video.filepath,video.duration,video.size,video.likecount,video.commentcount,video.createtime')
                                      -> select();

                // $videoModel = D('VideoView');
                // $where = array('video.status' => 1, 'r.tag_id' => $topicid);
                // $order = "video.id desc";
                // if ($type == "hot") {
                //     $order = "video.likecount desc";
                //     $where['likecount'] = array('egt',1);
                // }
                // $result = $videoModel -> where($where)
                //                       -> page($page, $pageSize)
                //                       -> order($order)
                //                       ->join("RIGHT JOIN tag_relation AS r ON r.video_id=video.id")
                //                       -> field('id,avatar,userid,username,description,thumb,filepath,duration,size,likecount,commentcount,createtime')
                //                       -> select();
                if ($result) {
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
                    'userid' => $userid,
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

    public function getusers_jp($topicid = 0, $cb = '') {
        $pageSize = 7;
        $page = 1;
        if (!is_numeric($topicid) || $topicid < 1 || strlen($cb) === 0) {
            if (empty($cb)) {
                $cb = 'error';
            }
            return show_output(100, "params error", array(), "jsonp", $cb);
        }

        $listCache = S(array('prefix'=>'topic_users_jp'));
        $cacheKey = 'topic_users_' . $topicid . '_' . $page;
        $result = $listCache -> $cacheKey;
        if ($result == false) {
            if ($page == 1) {
                unset($listCache -> $cacheKey);
            }
            try {

                $RModel = M('tag_relation');
                $where = array('video.status' => 1, 'r.tag_id' => $topicid);
                $order = "video.id desc";
                $userResult = $RModel ->alias('r') -> where($where)
                                      ->page($page, $pageSize)
                                      ->order($order)
                                      ->join("LEFT JOIN video AS video ON r.video_id=video.id")
                                      ->join("LEFT JOIN user AS user ON video.userid=user.id")
                                      ->field('video.userid,user.username,user.avatar,user.addtime as createtime')
                                      ->group('video.userid')
                                      ->select();
                // $videoModel = D('VideoView');
                // $where = array('status' => 1, 'topicid' => $topicid);
                // $userResult = $videoModel -> where($where)
                //                       -> page($page, $pageSize)
                //                       -> order('id desc')
                //                       -> field('userid,username,avatar,createtime')
                //                       -> group('userid')
                //                       -> select();

                if ($userResult !== false) {
                    $count = $RModel->alias('r')->where($where)
                                    ->join("LEFT JOIN video AS video ON r.video_id=video.id")
                                    ->join("LEFT JOIN user AS user ON video.userid=user.id")
                                    ->distinct(true)->field('user.id')->count();


                    $result['count'] = $count;
                    $result['users'] = $userResult;
                    $listCache -> $cacheKey = $result;
                } else {
                    $result = array(
                        "count" => 0,
                        "users" => array()
                    );
                }
            } catch(\Exception $e) {
                return show_output(500, "", array(), "jsonp", $cb);
            }
        }
        if ($result !== false) {
            $result['users'] = buildResourceUrl($result['users']);
            return show_output(200, "", $result, "jsonp", $cb);
        } else {
            return show_output(400, "", array(), "jsonp", $cb);
        }
    }

    public function getusers($topicid = 0, $page = 1) {
        //checkToken();
        $pageSize = 30;
        if (!is_numeric($page)) {
            $page = 1;
        }
        if (!is_numeric($topicid) || $topicid < 1) {
            return show_output(100, "params error");
        }

        $listCache = S(array('prefix'=>'topic_users'));
        $cacheKey = 'topic_users_' . $topicid . '_' . $page;
        $result = $listCache -> $cacheKey;
        if ($result == false) {
            if ($page == 1) {
                unset($listCache -> $cacheKey);
            }
            try {

                $RModel = M('tag_relation');
                $where = array('video.status' => 1, 'r.tag_id' => $topicid);
                $order = "video.id desc";
                $userResult = $RModel ->alias('r') -> where($where)
                                      ->page($page, $pageSize)
                                      ->order($order)
                                      ->join("LEFT JOIN video AS video ON r.video_id=video.id")
                                      ->join("LEFT JOIN user AS user ON video.userid=user.id")
                                      ->field('video.userid,user.username,user.avatar,user.addtime as createtime')
                                      ->group('video.userid')
                                      ->select();

                // $videoModel = D('VideoView');
                // $where = array('status' => 1, 'topicid' => $topicid);
                // $userResult = $videoModel -> where($where)
                //                       -> page($page, $pageSize)
                //                       -> order('id desc')
                //                       -> field('userid,username,avatar,createtime')
                //                       -> group('userid')
                //                       -> select();

                if ($userResult !== false) {
                    // $count = $RModel->alias('r')->where($where)
                    //                 ->join("LEFT JOIN video AS video ON r.video_id=video.id")
                    //                 ->join("LEFT JOIN user AS user ON video.userid=user.id")
                    //                 ->distinct(true)->field('user.id')->select();
                    // if ($count !== false && !empty($count)) {
                    //     $count = count($count);
                    // }

                    $getUserCountSql = "SELECT COUNT(*) AS count FROM (SELECT DISTINCT user.id FROM tag_relation r LEFT JOIN video AS video ON r.video_id = video.id LEFT JOIN user ON video.userid = user.id WHERE video.status = 1 AND r.tag_id = '{$topicid}') a ";
                    $Model = new \Think\Model();
                    $userCount = $Model->query($getUserCountSql);
                    $count = 0;
                    if (!empty($userCount)) {
                        $count = intval($userCount[0]['count']);
                    }
                    $result['count'] = $count;
                    $result['users'] = $userResult;
                    $listCache -> $cacheKey = $result;
                } else {
                    $result = array(
                        "count" => 0,
                        "users" => array()
                    );
                }
            } catch(\Exception $e) {
                return show_output(500, "");
            }
        }
        if ($result !== false) {
            $result['users'] = buildResourceUrl($result['users']);
            return show_output(200, "", $result);
        } else {
            return show_output(400, "");
        }
    }

    public function getusersvideo($page = 1, $topicid = 0, $userid = 0) {
        //checkToken();
        $pageSize = $this->_getPageSize;
        if (!is_numeric($page)) {
            $page = 1;
        }
        if (!is_numeric($userid)) {
            $userid = 0;
        }

        if (!is_numeric($topicid) || $topicid < 1) {
            return show_output(101, "topicid");
        }
        $topicid = intval($topicid);

        $listCache = S(array('prefix'=>'topic_user_video'));
        $cacheKey = 'topic_user_video_' . $topicid . '_' . $page;
        $result = $listCache -> $cacheKey;
        if ($result == false) {
            if ($page == 1) {
                unset($listCache -> $cacheKey);
            }
            try {

                $RModel = M('tag_relation');
                $where = array('video.status' => 1, 'r.tag_id' => $topicid);
                $result = $RModel ->alias('r') -> where($where)
                                      ->page($page, $pageSize)
                                      ->join("LEFT JOIN video AS video ON r.video_id=video.id")
                                      ->join("LEFT JOIN user AS user ON video.userid=user.id")
                                      ->field('video.userid,user.username,user.avatar,user.addtime as createtime')
                                      ->group('video.userid')
                                      ->select();

                // $videoModel = D('VideoView');
                // $where = array('status' => 1, 'topicid' => $topicid);
                // $result = $videoModel -> where($where)
                //                       -> page($page, $pageSize)
                //                       -> order('id desc')
                //                       -> field('userid,username,avatar,createtime')
                //                       -> group('userid')
                //                       -> select();


                if ($result !== false) {
                    $videos = array();
                    if (count($result) > 0) {
                        $userids = '';
                        for ($i=0; $i < count($result); $i++) {
                            $userids .= $result[$i]['userid'] . ',';
                        }
                        $getVideoSql = "select id, userid,thumb,filepath,createtime from video a where 3>(select count(*) from video where userid=a.userid and id>a.id and userid in ($userids 0) and status=1) and a.userid in ($userids 0) and a.status=1 order by a.userid,a.id desc";
                        $Model = new \Think\Model();
                        $videos = $Model->query($getVideoSql);
                        $this->encode_vid_arr($videos,"id","uuid");
                    }
                    $result = $this -> _mergeUserVideoss($result, $videos);
                    $listCache -> $cacheKey = $result;
                } else {
                    $result = array();
                }
            } catch(\Exception $e) {
                return show_output(500, "");
            }
        }
        if ($result !== false) {
            $attentions = array();
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
            }
            $result = _mergeTopicUserAttentions($result, $attentions);
            return show_output(200, "", $this ->  _buildRecommendResourceUrl($result));
        } else {
            return show_output(400, "");
        }
    }

    private function _mergeUserVideoss($result, $videos) {
        for ($i=0; $i < count($result); $i++) {
            $result[$i]['videos'] = array();
            for ($j=0; $j < count($videos); $j++) {
                if (intval($result[$i]['userid']) == intval($videos[$j]['userid'])) {
                    unset($videos[$j]['userid']);
                    $result[$i]['videos'][] = $videos[$j];
                }
            }
        }
        return $result;
    }

    private function _buildRecommendResourceUrl($result) {
        $result = buildResourceUrl($result);
        for ($i = 0; $i < count($result); $i++) {
            $result[$i]['videos'] = buildResourceUrl($result[$i]['videos']);
        }
        return $result;
    }
}
?>