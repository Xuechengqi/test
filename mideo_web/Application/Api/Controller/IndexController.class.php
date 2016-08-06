<?php
namespace Api\Controller;
//use Think\Controller;

/*

1 utf-8
$gbkData = "接口";
$utf8Data = iconv("GBK", "UTF-8", $gbkData);

2 format
{
    "code": 200,
    "message": "",
    "data": {
        "id": 1,
        "name": "jim"
    }
}
*/
/**
 * 已经完成uuid改造
 */
class IndexController extends SController {
    protected $_getPageSize = 10;

    public function hot_jp($country = "BR", $cb = '') {
        if (strlen($cb) === 0) {
            $cb = 'error';
            return show_output(101, "", array(), "jsonp", $cb);
        }
        $pageSize = $this->_getPageSize;
        $page = 1;
        if (!checkCountry($country)) {
            $country = getDefaultCountry();
        }

        $listCache = S(array('prefix'=>'list_hot_jp'));
        $cacheKey = 'list_jp_' . $country . '_' . $page;
        $result = $listCache -> $cacheKey;
        if ($result == false) {
            if ($page == 1) {
                unset($listCache -> $cacheKey);
            }
            try {
                $videoModel = D('VideoView');
                $where = array('status' => 1, 'ishot' => 1, 'country' => $country, 'orderfixed' => 0);
                $result = $videoModel -> where($where)
                                      -> page($page, $pageSize)
                                      -> order('showorder desc')
                                      -> field('id,userid,thumb,username,avatar,description,likecount')
                                      -> select();
                if ($result) {
                    if ($page == 1) {
                        $where['orderfixed'] = array('gt', 0);
                        $fixedVideoList = $videoModel -> where($where)
                                                      -> order('orderfixed')
                                                      -> field('id,userid,thumb,username,avatar,description,likecount,orderfixed')
                                                      -> select();
                        if ($fixedVideoList != false) {
                            $result = $this -> _mergeFixedVideo($result, $fixedVideoList);
                        }
                    }
                    $this->encode_vid_arr($result,"id");
                    $listCache -> $cacheKey = $result;
                }
            } catch(\Exception $e) {
                return show_output(500, "", array(), "jsonp", $cb);
            }
        }
        if ($result !== false) {

            return show_output(200, "", buildResourceUrl($result), "jsonp", $cb);
        } else {
            return show_output(400, "", array(), "jsonp", $cb);
        }
    }

    //page<=10
    public function hot($page = 1, $country = "BR", $userid = 0) {
        //checkToken();
        if($page>10){
            return $this->hot_more($page, $country, $userid);
        }

        $pageSize = $this->_getPageSize;
        if (!is_numeric($page)) {
            $page = 1;
        }
        if (!is_numeric($userid)) {
            $userid = 0;
        }
        if (!checkCountry($country)) {
            $country = getDefaultCountry();
        }
        $listCache = S(array('prefix'=>'list_hot'));
        $cacheKey = 'list_' . $country;
        $result = $listCache -> $cacheKey;
        if ($result == false) {
            try {
                //前2页面可能有锁定数据
                $videoModel = D('VideoView');
                //锁定热门数据
                $where = array('status' => 1, 'ishot' => 1, 'country' => $country);
                $where['orderfixed'] = array('gt', 0);
                $fixedVideoList = $videoModel ->where($where)
                                ->order('orderfixed')
                                ->join('LEFT JOIN topic AS t ON t.id = video.topicid')
                                ->field('id,avatar,userid,topicid,t.title as topic_title,t.content as topic_content,t.picture as topic_picture,t.hot_pic as topic_hot_pic,username,description,thumb,filepath,duration,size,likecount,commentcount,createtime,orderfixed,istopic')
                                ->select();

                //非锁定热门数据
                $where['orderfixed'] = array('eq', 0);
                $hotVideoList = $videoModel -> where($where)
                                ->limit(100)
                                ->order('showorder desc')
                                ->join('LEFT JOIN topic AS t ON t.id = video.topicid')
                                ->field('id,avatar,userid,topicid,t.title as topic_title,t.content as topic_content,t.picture as topic_picture,t.hot_pic as topic_hot_pic,username,description,thumb,filepath,duration,size,likecount,commentcount,createtime,orderfixed,istopic')
                                ->select();
                //非热门数据
                unset($where['orderfixed']);
                $where['ishot'] = array('eq', 0);
                $otherVideoList = $videoModel -> where($where)
                                ->limit(100)
                                ->order('createtime desc')
                                ->join('LEFT JOIN topic AS t ON t.id = video.topicid')
                                ->field('id,avatar,userid,topicid,t.title as topic_title,t.content as topic_content,t.picture as topic_picture,t.hot_pic as topic_hot_pic,username,description,thumb,filepath,duration,size,likecount,commentcount,createtime,orderfixed,istopic')
                                ->select();
                //前20个最新视频同一个人的视频只取一个
                $other_10 = array_splice($otherVideoList,0,20);
                $filter = array();
                foreach ($other_10 as $k=>$v) {
                    if( in_array($v['userid'], $filter) ){
                        unset($other_10[$k]);
                        continue;
                    }else{
                        $filter[]=$v['userid'];
                    }
                }
                $otherVideoList = array_merge($other_10,$otherVideoList);


                $result = array();
                for ($i=0; $i < 100; $i++) {
                    if(count($result)>100){
                        break;
                    }
                    if(empty($hotVideoList)&&empty($otherVideoList)){
                        break;
                    }
                    if($i%2==0){
                        //填充热门数据
                        if(empty($hotVideoList)){
                            $result[]=array_shift($otherVideoList);
                        }else{
                            $result[]=array_shift($hotVideoList);
                        }
                    }else{
                        //填充普通数据
                        if(empty($otherVideoList)){
                            $result[]=array_shift($hotVideoList);
                        }else{
                            $result[]=array_shift($otherVideoList);
                        }
                    }
                }

                //前10个随机排序
                $result_10 = array_splice($result,0,10);
                shuffle($result_10);
                $result = array_merge($result_10,$result);


                //置换锁定数据
                foreach ($fixedVideoList as $value) {
                    $offset = $value['orderfixed']-1;
                    array_splice($result, $offset, 0, array($value));
                    //$result[$offset] = $value;
                }


                $this->encode_vid_arr($result,"id","uuid");

                foreach ($result as &$v) {
                    if($v['topic_picture'])
                        $v['topic_picture']=getUploadResourceBaseUrl().$v['topic_picture'];
                    if($v['topic_hot_pic'])
                        $v['topic_hot_pic']=getUploadResourceBaseUrl().$v['topic_hot_pic'];

                }

                $listCache -> $cacheKey = $result;
            } catch(\Exception $e) {
                return show_output(500, "");
            }
        }

        //分页
        $p_start = ($page-1)*$pageSize;
        $result = array_slice($result,$p_start,$pageSize);
        if ($result !== false) {
            return show_output(200, "", buildResourceUrl($result));
        } else {
            return show_output(400, "");
        }
    }

    //page>10
    public function hot_more($page = 1, $country = "BR", $userid = 0) {
        //checkToken();
        $pageSize = $this->_getPageSize;
        if (!is_numeric($page)) {
            $page = 1;
        }
        if (!is_numeric($userid)) {
            $userid = 0;
        }
        if (!checkCountry($country)) {
            $country = getDefaultCountry();
        }
        $listCache = S(array('prefix'=>'list_hot_more'));
        $cacheKey = 'list_' . $country . '_' . $page;
        $result = $listCache -> $cacheKey;
        if ($result == false) {
            try {
                $videoModel = D('VideoView');
                $where = array('status' => 1, 'country' => $country, 'ishot' => 1);
                //排除前10页数据
                $hotCache = S(array('prefix'=>'list_hot'));
                $hotKey = 'list_' . $country;
                $res = $hotCache -> $hotKey;
                if ($res != false) {
                    $not_ids=array();
                    foreach ($res as  $value) {
                        if($value['id'])
                            $not_ids[]=$value['id'];
                    }
                    if($not_ids)
                        $where['id']  = array('not in',$not_ids);
                }
                $result = $videoModel ->where($where)
                                      ->page($page, $pageSize)
                                      ->order('id desc')
                                      ->join('LEFT JOIN topic AS t ON t.id = video.topicid')
                                      ->field('id,avatar,userid,topicid,t.title as topic_title,t.content as topic_content,t.picture as topic_picture,username,description,thumb,filepath,duration,size,likecount,commentcount,createtime,istopic')
                                      ->select();
                //print_r($videoModel->getLastSql());die;
                if ($result) {
                    $this->encode_vid_arr($result,"id","uuid");
                    foreach ($result as &$v) {
                        if($v['topicid'])
                            $v['topic_picture']=getUploadResourceBaseUrl().$v['topic_picture'];
                    }
                    $listCache -> $cacheKey = $result;
                }
            } catch(\Exception $e) {
                return show_output(500, "");
            }
        }
        if ($result !== false) {
            return show_output(200, "", buildResourceUrl($result));
        } else {
            return show_output(400, "");
        }
    }

    private function _mergeFixedVideo($list, $fixedVideoList) {
        for ($j = 0; $j < count($fixedVideoList); $j++) {
            array_splice($list, $fixedVideoList[$j]['orderfixed'] - 1, 0,array($fixedVideoList[$j]));
        }
        return $list;
    }

    public function recommend($page = 1, $country = "BR", $userid = 0) {
        //checkToken();
        $pageSize = $this->_getPageSize;
        if (!is_numeric($page)) {
            $page = 1;
        }
        if (!is_numeric($userid)) {
            $userid = 0;
        }

        if (!checkCountry($country)) {
            $country = getDefaultCountry();
        }

        $listCache = S(array('prefix'=>'recommend'));
        $cacheKey = 'recommend_' . $country . '_' . $page;
        $result = $listCache -> $cacheKey;
        if ($result == false) {
            if ($page == 1) {
                unset($listCache -> $cacheKey);
            }
            try {
                $userModel = M('view_video_user');
                $where = array('status' => 1, 'isrecommend' => 1, 'country' => $country);
                $result = $userModel -> where($where)
                                      -> page($page, $pageSize)
                                      -> order('recommendtime desc,id desc')
                                      -> field('id,id as userid, avatar, username')
                                      -> select();
                if ($result) {
                    $videos = array();
                    if (count($result) > 0) {
                        $userids = '';
                        for ($i=0; $i < count($result); $i++) {
                            $userids .= $result[$i]['id'] . ',';
                        }
                        $getVideoSql = "select id , userid,thumb,filepath ,createtime from video a where 3>(select count(*) from video where userid=a.userid and id>a.id and userid in ($userids 0) and status=1) and a.userid in ($userids 0) and a.status=1 order by a.userid,a.id desc";
                        $Model = new \Think\Model();
                        $videos = $Model->query($getVideoSql);
                        $this->encode_vid_arr($videos,"id","uuid");
                    }
                    $result = $this -> _mergeUserVideoss($result, $videos);
                    $listCache -> $cacheKey = $result;
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
                    $userids .= $result[$i]['id'] . ',';
                }
                $attentionModel = M('attention');
                $attentionWhere = array(
                    'fromuserid' => $userid,
                    'touserid' => array('in', $userids . '0'),
                    'relation' => 1
                );
                $attentions = $attentionModel -> where($attentionWhere) -> select();
            }
            $result = _mergeUserAttentions($result, $attentions);
            return show_output(200, "", $this ->  _buildRecommendResourceUrl($result));
        } else {
            return show_output(400, "");
        }
    }

    public function attention() {
        //checkToken();
        $page = 1;
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
        } else if (isset($_POST['page'])) {
            $page = $_POST['page'];
        }
        $userid = '';
        if (isset($_GET['userid'])) {
            $userid = $_GET['userid'];
        } else if (isset($_POST['userid'])) {
            $userid = $_POST['userid'];
        }
        $pageSize = $this->_getPageSize;
        if (!is_numeric($page)) {
            $page = 1;
        }
        if (!is_numeric($userid)) {
            return show_output(101, "");
        }

        $listCache = S(array('prefix'=>'list_attention'));
        $cacheKey = 'list_'. $page;
        $result = $listCache -> $cacheKey;
        $result = false;//userid 参数
        if ($result == false) {
            try {
                $attentionModel = M('attention');
                $attentionWhere = array(
                    'fromuserid' => $userid,
                    //'touserid' => array('neq',83),
                    'relation' => 1
                );
                $attentions = $attentionModel -> where($attentionWhere) -> select();
                if ($attentions == false || empty($attentions)) {
                    return show_output(200, "");
                }
                $attentionUserIds = '';
                for ($i = 0, $j = count($attentions); $i < $j; $i++) {
                    $attentionUserIds .= $attentions[$i]['touserid'] . ',';
                }

                $videoModel = D('VideoView');
                $where = array(
                    'userid' => array('in', $attentionUserIds. '0'),
                    'status' => 1
                );
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
        }
        if ($result !== false) {
            $likes = array();
            if ($userid > 0) {
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
            $result = _mergeVideoLikes($result, $likes);
            return show_output(200, "", buildResourceUrl($result));
        } else {
            return show_output(400, "");
        }
    }

    private function _mergeUserVideoss($result, $videos) {
        for ($i=0; $i < count($result); $i++) {
            $result[$i]['videos'] = array();
            for ($j=0; $j < count($videos); $j++) {
                if (intval($result[$i]['id']) == intval($videos[$j]['userid'])) {
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


    public function version($code = "") {
        if(!file_exists('index/app_ini.php')){
            return show_output(400, "");
        }
        $app_ini = require_once('index/app_ini.php');
        if($app_ini['VERSION']>$code){
            return show_output(200, "", true);
        }else{
            return show_output(200, "", false);
        }

    }

    public function splash($country = 'BR') {
        $splashModel = M("splash");
        $result = $splashModel -> where(array('country' => $country)) -> order("id desc") -> find();
        if ($result == false || $result === null) {
            return show_output(400, "");
        } else {
            return show_output(200, "", buildSplashResourceUrl($result["path"]));
        }
    }
}