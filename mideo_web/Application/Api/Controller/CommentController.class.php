<?php
namespace Api\Controller;
/**
 * 已经完成uuid改造
 */
class CommentController extends SController {
    public function add($videoid = '', $userid = 0, $username = '', $content = '', $atuserids = "", $country = "") {
        
        //checkToken();
        if ($userid === 0 || empty($videoid) || empty($userid) || empty($content)|| empty($username)) {
            return show_output(101, 'params error');
        }
        if (!checkCountry($country)) {
            $country = getDefaultCountry();
        }


        $commentModel = M('comment');
        $video_id = $this->decode_vid($videoid);
        //先不审核
        $commentRet = $commentModel -> add(
            array(
                "video_id" => $video_id,
                "userid" => $userid,
                "username" => $username,
                "content" => $content,
                "country" => $country,
                "status" => 1,
                "createtime" => time()
            )
        );
        if ($commentRet == false) {
            return show_output(300, "");
        } else {
            //添加message 消息对队
            $videoModel = M('video');
            //$videoItem = $videoModel -> where("id={$video_id} and userid != {$userid}") -> field('id,userid,commentcount') -> find();
            $videoItem = $videoModel -> where("id={$video_id}") -> field('id,userid,commentcount') -> find();
            if ($videoItem !== false ) {
                //commentcount+1
                $videoItem['commentcount'] = (int)$videoItem['commentcount'] + 1;
                $videoRet = $videoModel -> save($videoItem);

                if($videoItem['userid']!=$userid){
                    $messageModel = M('message');
                    $messageItem = array(
                        "fromuserid" => $userid,
                        "touserid" => $videoItem['userid'],
                        "video_id" => $video_id,
                        "commentid" => $commentRet,
                        "eventtype" => getMessageEventType("comment"),
                        "addtime" => time()
                    );
                    $commentMsgRet = $messageModel -> add($messageItem);

                    //发送FCM消息
                    $this->msg_send('comment',$videoItem['userid'],$userid,$content);
                }
            }

            if (!empty($atuserids)) {
                $atuseridArray = explode(",", $atuserids);
                for ($i = 0; $i < count($atuseridArray); $i++) {
                    $messageItem = array(
                        "fromuserid" => $userid,
                        "touserid" => $atuseridArray[$i],
                        "commentid" => $commentRet,
                        "eventtype" => getMessageEventType("at"),
                        "video_id" => $video_id,
                        "addtime" => time()
                    );
                    try {
                        $messageModel = M('message');
                        $atMsgRet = $messageModel -> add($messageItem);
                    } catch(\Exception $e) {
                        //todo
                    }
                }

                //发送FCM消息
                $this->msg_send('at',$atuseridArray,$userid,$content);
            }

            //(无粉丝)时添加3个粉丝
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


            return show_output(200, "", array("id" => $commentRet));
        }
    }

    public function report($commentid = 0, $userid = 0, $content = '') {
        //checkToken();
        if ($commentid === 0 || $userid === 0 || empty($commentid) || empty($userid)) {
            return show_output(101, 'params error');
        }

        $reportModel = M('report_comment');
        $reportRet = $reportModel -> add(
            array(
                "commentid" => $commentid,
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

    public function del($commentid = 0, $userid = 0) {
        //checkToken();
        if ($commentid === 0 || $userid === 0 || empty($commentid) || empty($userid)) {
            return show_output(101, 'params error');
        }

        $Model = M('comment');
        $comm = $Model->find($commentid);
        if($comm['userid']!=$userid)
            return show_output(300, "");

        $comm['status']=2;
        $res = $Model -> save($comm);

        if ($res === false) {
            return show_output(300, "");
        } else {
            M('video')->where('id='.$comm['video_id'])->setDec('commentcount'); 
            return show_output(200, "");
        }

    }
}
?>