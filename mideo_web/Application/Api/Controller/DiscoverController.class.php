<?php
namespace Api\Controller;

class DiscoverController extends SController {

    public function nointerest($myid = "", $videoid = "") {
        if (empty($videoid) || empty($myid)) {
            return show_output(101, 'params error');
        }
        $video_id=$this->decode_vid($videoid);
        //检查是否已经nointerest过
        $likeModel = M('vlike');
        $vlikeItem = array(
            'videoid' => $video_id,
            'userid' => $myid,
            "relation" => 2
        );
        $likeInfo = $likeModel -> where($vlikeItem) -> find();
        if ($likeInfo == false) {
            //添加
            $vlikeItem['createtime'] = time();
            $likeRet = $likeModel -> add($vlikeItem);
            if ($likeRet !== false) {
                return show_output(200, "");
            } else {
                return show_output(301, "");
            }
        } else {
            return show_output(600, "");
        }
    }

    public function video($myid = "", $country = "") {
        if (!checkCountry($country)) {
            $country = getDefaultCountry();
        }
        if (empty($myid) || !is_numeric($myid) || $myid <= 0) {
            $result = $this -> getVideoNotInTags(0, array(), 10, $country);
            $this->encode_vid_arr($result,"id","uuid");
            return show_output(200, "", buildResourceUrl($result));
        }
        $result = array();
        $likedVideoId = $this -> getLastAttentionVideo($myid);
        //1、判断用户是否喜欢了视频（过去1天）
        if ($likedVideoId !== false) {
            $tagIds = $this -> getVideoTagIds($likedVideoId);
            if (!empty($tagIds)) {
                //2、取两个同标签下的热门视频
                $sameTagVideos = $this -> getVideoByTags($myid, $tagIds, $country);
                if (!empty($sameTagVideos)) {
                    $result = $sameTagVideos;
                }
                //3、取一个不同标签下的热门视频
                $otherTagVideos = $this -> getVideoNotInTags($myid, $tagIds, 10 - count($result), $country);
                if (!empty($otherTagVideos)) {
                    foreach ($otherTagVideos as $videoItem) {
                        $result[] = $videoItem;
                    }
                }
            }
        }
        if (count($result) < 10) {
            $other = $this -> getVideoNotInTags($myid, array(), 10 - count($result), $country);
            foreach ($other as $videoItem) {
                $result[] = $videoItem;
            }
        }
        if (!empty($result)) {
            $attentions = array();
            $userids = '';
            for ($i=0; $i < count($result); $i++) {
                $userids .= $result[$i]['userid'] . ',';
            }
            $attentionModel = M('attention');
            $attentionWhere = array(
                'fromuserid' => $myid,
                'touserid' => array('in', $userids . '0'),
                'relation' => 1
            );
            $attentions = $attentionModel -> where($attentionWhere) -> select();
            $result = $this -> _mergeUserAttentions($result, $attentions);
        }
        $this->encode_vid_arr($result,"id","uuid");
        return show_output(200, "", buildResourceUrl($result));
    }

    private function _mergeUserAttentions($result, $attentions) {
        for ($i=0; $i < count($result); $i++) {
            $result[$i]['attention'] = 0;
            for ($j=0; $j < count($attentions); $j++) {
                if (intval($result[$i]['userid']) == intval($attentions[$j]['touserid'])) {
                    $result[$i]['attention'] = 1;
                }
            }
        }
        return $result;
    }

    private function getVideoNotInTags($myid, $tagIds, $count, $country) {
        $tagIds[] = -1;
        $where = "topicid not in (".implode(",", $tagIds). ") and video.status = 1 and video.country = '{$country}' and video.id not in (select v.videoid from vlike v where v.userid={$myid})";
        $videos = D('VideoView') -> where($where)
                                    -> order("ishot desc,likecount desc,id desc")
                                    -> field("id,avatar,userid,topicid,username,description,thumb,filepath,duration,size,likecount,commentcount,createtime")
                                    -> limit($count)
                                    -> select();
        return $videos;
    }

    private function getVideoByTags($myid, $tagIds, $country) {
        $where = "topicid in (".implode(",", $tagIds). ") and video.status = 1 and video.country = '{$country}' and video.id not in (select v.videoid from vlike v where v.userid={$myid})";
        $videos = D('VideoView') -> where($where)
                                    -> order("ishot desc,likecount desc,id desc")
                                    -> field("id,avatar,userid,topicid,username,description,thumb,filepath,duration,size,likecount,commentcount,createtime")
                                    -> limit(5)
                                    -> select();
        return $videos;
    }

    private function getVideoTagIds($videoid) {
        $tagIds = array();
        $ret = M('tag_relation') -> where(
                                        array(
                                            "video_id" => $videoid,
                                            "topic.status" => 1
                                        )
                                    )
                                 -> join("join topic on topic.id = tag_relation.tag_id")
                                 -> field("tag_id") -> select();
        if (!empty($ret)) {
            foreach ($ret as $tags) {
                foreach ($tags as $id) {
                    $tagIds[] = $id;
                }
            }
        }
        return $tagIds;
    }

    private function getLastAttentionVideo($myid = "") {
        if (empty($myid) || !is_numeric($myid) || $myid <= 0) {
            return false;
        }
        $where = array(
            "userid" => $myid,
            "createtime" => array("gt", time() - 24*60*60),
            "relation" => 1
        );
        $ret = M('vlike') -> where($where) -> order('createtime desc') -> find();
        if ($ret !== null) {
            return $ret['videoid'];
        } else {
            return false;
        }
    }
}

?>