<?php
namespace Api\Controller;
/**
 * 已经完成uuid改造
 */
class MessageController extends SController {

    public function getsummary($userid = 0, $country = "US") {
        //checkToken();
        if (!is_numeric($userid) || $userid <= 0) {
            return show_output(101, "");
        }
        $userid = intval($userid);
        if (!checkCountry($country)) {
            $country = getDefaultCountry();
        }
        $result = array();

        $summarySql = 'SELECT eventtype AS msgtype ,COUNT(id) AS count FROM `message` where touserid='.$userid.' and isread=0 and eventtype<>1 and eventtype<>6 GROUP BY eventtype';
        $Model = new \Think\Model();
        try {
            $result = $Model->query($summarySql);
            $result = $this -> _handleSummaryMessage($result);
        } catch(\Exception $e) {
            return show_output(500, "user");
        }

        $systemResult = array();
        $systemSummarySql = "SELECT COUNT(A.id) AS count FROM `system_message` AS A WHERE A.country = '" . $country . "' AND A.id not in (SELECT commentid from message B WHERE A.id=B.commentid AND B.touserid=" . $userid . " AND B.eventtype=1)";
        try {
            $systemResult = $Model -> query($systemSummarySql);
        } catch(\Exception $e) {
            return show_output(500, "system");
        }
        $result = $this -> _mergeSummaryMessage($result, $systemResult);
        return show_output(200, "", $result);
    }

    public function readsystem($userid = 0, $msgid = 0) {
        //checkToken();
        if (!is_numeric($userid) || $userid <= 0) {
            return show_output(101, "userid");
        }
        if (!is_numeric($msgid) || $msgid <= 0) {
            return show_output(101, "msgid");
        }
        $msgModel = M('message');
        $msgItem = array(
            "fromuserid" => 0,
            "touserid" => $userid,
            "eventtype" => 1,
            "commentid" => $msgid,
            "isread" => 1,
            "addtime" => time()
        );
        $ret = $msgModel -> add($msgItem);
        if ($ret === false) {
            return show_output(300, "");
        } else {
            return show_output(200, "");
        }
    }

    public function read($userid = 0, $msgtype = '') {
        //checkToken();
        if (!is_numeric($userid) || $userid <= 0) {
            return show_output(101, "userid");
        }

        $msgtype = str_replace(" ", "", $msgtype);
        $msgNumType = getMessageEventType($msgtype);
        if ($msgNumType ===6 || empty($msgtype)) {
            return show_output(101, "msgtype");
        }

        $Model = new \Think\Model();
        $msgSql = 'update `message` set `isread` = 1 where `eventtype`=' . $msgNumType . ' and `touserid` = ' . $userid;
        try {
            $ret = $Model->execute($msgSql);
        } catch(\Exception $e) {
            $ret = false;
        }
        if ($ret === false) {
            return show_output(300, "");
        } else {
            return show_output(200, "");
        }
    }

    public function delete($userid = 0, $msgid = "") {
        //checkToken();
        if (!is_numeric($userid) || $userid <= 0) {
            return show_output(101, "userid");
        }
        if (empty($msgid)) {
            return show_output(101, "msgid");
        }
        $msgIds = explode(",", $msgid);
        $safeMsgIds = array();
        for ($i=0; $i < count($msgIds) ; $i++) {
            if (intval($msgIds[$i]) > 0) {
                $safeMsgIds[] = intval($msgIds[$i]);
            }
        }
        if (empty($safeMsgIds)) {
            return show_output(101, "msgid");
        }
        $where = array(
            "_string" => " id in (" . implode(",", $safeMsgIds) . ")",
            "touserid" => $touserid
        );
        $msgModel = M('message');
        $msgItem = $msgModel -> where($where) -> delete();
        if ($msgItem === false) {
            return show_output(300, "");
        } else {
            return show_output(200, "");
        }
    }

    public function getsystemmsgs($userid = 0, $page = 1, $country = "BR") {
        $this -> getmsgs($userid, $page, "system", $country);
    }

    public function getmsgs($userid = 0, $page = 1, $msgtype = '', $country = "US") {
        //checkToken();
        if (!is_numeric($userid) || $userid <= 0) {
            return show_output(101, "userid");
        }
        if (!is_numeric($page) || $page <= 0) {
            $page = 1;
        }
        $msgtype = str_replace(" ", "", $msgtype);

        $msgNumType = getMessageEventType($msgtype);
        if ($msgNumType == 6) {
            return show_output(101, "msgtype");
        }
        if (!checkCountry($country)) {
            $country = getDefaultCountry();
        }
        $pageSize = 20;
        $result = array();
        $Model = new \Think\Model();
        if ($msgNumType == 1) {
            //系统消息
            $msgSql = 'SELECT A.id, A.content, A.addtime,A.click,A.actionurl, B.isread,B.video_id FROM `system_message` AS A LEFT JOIN `message` AS B ON A.id = B.commentid AND B.eventtype=1 AND B.touserid=' . $userid . ' WHERE A.country="'.$country.'" GROUP BY A.`id` order by A.id desc limit ' . (($page - 1) * $pageSize) . ',' . ($page * $pageSize);
            $result = $Model->query($msgSql);
            $result = $this -> _handleSystemMessage($result);
        } else if ($msgNumType == 2) {
            //like
            $msgSql = 'select A.id,A.fromuserid,A.isread,A.video_id,A.addtime,B.username,B.avatar,D.thumb from message A inner join user B on A.fromuserid=B.id inner join video D on A.video_id=D.id where A.eventtype='.$msgNumType.' and A.touserid=' . $userid . ' order by A.id desc  limit ' . (($page - 1) * $pageSize) . ',' . ($page * $pageSize);
            $result = $Model->query($msgSql);
            $result = $this -> _handleNormalMessage($result);
        } else if ($msgNumType == 3) {
            //attention
            $msgSql = 'select A.id,A.fromuserid,A.isread,A.video_id,A.addtime,B.username,B.avatar from message A inner join user B on A.fromuserid=B.id where A.eventtype='.$msgNumType.' and A.touserid=' . $userid . ' order by A.id desc  limit ' . (($page - 1) * $pageSize) . ',' . ($page * $pageSize);
            $result = $Model->query($msgSql);
            $result = $this -> _handleNormalMessage($result);

            $ids = array(0);
            for ($i = 0; $i < count($result); $i++) {
                $ids[] = $result[$i]['fromuserid'];
            }
            $isAttentionSql = 'select touserid from attention where touserid in (' . implode(",", $ids) . ') and fromuserid=' . $userid;
            $attentionResult = $Model -> query($isAttentionSql);
            if ($attentionResult !== null && count($attentionResult) > 0) {
                $attentionUserIds = array();
                for ($i = 0; $i < count($attentionResult); $i++) {
                    $attentionUserIds[] = $attentionResult[$i]['touserid'];
                }
                for ($i = 0; $i < count($result); $i++) {
                    if (in_array($result[$i]['fromuserid'], $attentionUserIds)) {
                        $result[$i]['attention'] = 1;
                    } else {
                        $result[$i]['attention'] = 0;
                    }
                }
            }
        } else if ($msgNumType == 4) {
            //comment
            $msgSql = 'select A.id,A.fromuserid,A.isread,A.video_id,A.addtime,B.username,B.avatar,C.content,D.thumb from message A inner join user B on A.fromuserid=B.id inner join comment C on A.commentid=C.id inner join video D on A.video_id=D.id where A.eventtype='.$msgNumType.' and A.touserid=' . $userid . ' order by A.id desc  limit ' . (($page - 1) * $pageSize) . ',' . ($page * $pageSize);
            $result = $Model->query($msgSql);
            $result = $this -> _handleNormalMessage($result);
        } else if ($msgNumType == 5) {
            //at
            $msgSql = 'select A.id,A.fromuserid,A.isread,A.video_id,A.addtime,B.username,B.avatar from message A inner join user B on A.fromuserid=B.id where A.eventtype='.$msgNumType.' and A.touserid=' . $userid . ' order by A.id desc  limit ' . (($page - 1) * $pageSize) . ',' . ($page * $pageSize);
            $result = $Model->query($msgSql);
            $result = $this -> _handleNormalMessage($result);
        }
        $this->encode_vid_arr($result,"video_id");

        if($msgNumType == 1){
            //系统消息直接标记已读
            $msgModel = M('message');
            $msgItem=array();
            foreach ($result as $v) {
                $msgItem[] = array(
                    "fromuserid" => 0,
                    "touserid" => $userid,
                    "eventtype" => 1,
                    "commentid" => $v['id'],
                    "isread" => 1,
                    "addtime" => time()
                );
            }
            if(!empty($msgItem))
                $msgModel -> addAll($msgItem);
        }

        return show_output(200, "", $result);
    }

    private function _mergeSummaryMessage($result, $systemResult) {
        if (is_array($systemResult)) {
            $systemResult = $systemResult[0];
        }
        $allType = getAllMessageEventType();
        foreach ($allType as $key => $value) {
            if ($key === "unknown") {
                continue;
            }
            if ($key === "system") {
                $systemResult['msgtype'] = "system";
                $result[] = $systemResult;
            }
            $isHasCurrentType = false;
            for ($i = 0; $i < count($result); $i++) {
                if ($result[$i]['msgtype'] === $key) {
                    $isHasCurrentType = true;
                    break;
                }
            }
            if (!$isHasCurrentType) {
                $result[] = array(
                    "msgtype" => $key,
                    "count" => 0
                );
            }
        }
        return $result;
    }

    private function _handleSummaryMessage($summaryResult) {
        if ($summaryResult !== false) {
            if (count($summaryResult) > 0) {
                for ($i = 0; $i < count($summaryResult); $i++) {
                    $summaryResult[$i]['msgtype'] =  getMessageEventName($summaryResult[$i]['msgtype']);
                }
            }
        }
        return $summaryResult;
    }

    private function _handleNormalMessage($msgResult) {
        if ($msgResult !== false) {
            if (count($msgResult) > 0) {
                for ($i = 0; $i < count($msgResult); $i++) {
                    if ($msgResult[$i]['isread'] == null) {
                        $msgResult[$i]['isread'] = 0;
                    } else {
                        $msgResult[$i]['isread'] = 1;
                    }
                    if (isset($msgResult[$i]['avatar']) && !empty($msgResult[$i]['avatar'])) {
                        if(strtolower(substr($msgResult[$i]['avatar'],0,4)) != 'http')
                            $msgResult[$i]['avatar'] = getAvatarResourceBaseUrl() . $msgResult[$i]['avatar'];
                        else
                            $msgResult[$i]['avatar'] = $msgResult[$i]['avatar'];
                        //$msgResult[$i]['avatar'] = getAvatarResourceBaseUrl() . $msgResult[$i]['avatar'];
                    }
                    if (isset($msgResult[$i]['thumb']) && !empty($msgResult[$i]['thumb'])) {
                        $msgResult[$i]['thumb'] = getUploadResourceBaseUrl() . $msgResult[$i]['thumb'];
                    }
                }
            }
        }
        return $msgResult;
    }

    private function _handleSystemMessage($msgResult) {
        if ($msgResult !== false) {
            if (count($msgResult) > 0) {
                for ($i = 0; $i < count($msgResult); $i++) {
                    if ($msgResult[$i]['isread'] == null) {
                        $msgResult[$i]['isread'] = 0;
                    } else {
                        $msgResult[$i]['isread'] = 1;
                    }
                    $msgResult[$i]['baseurl'] = getBaseUrl();
                }
            }
        }
        return $msgResult;
    }

    public function newmsgnum($userid = 0) {
        //checkToken();
        if (!is_numeric($userid)) {
            return show_output(101, "");
        }
        try {
            $msgModel = M('message');
            $msgWhere = array(
                'touserid' => $userid,
                'isread' => 0
            );
            $num = $msgModel -> where($msgWhere) -> count();
            return show_output(200, "", $num);
        } catch(\Exception $e) {
            return show_output(500, "");
        }
    }
}
?>