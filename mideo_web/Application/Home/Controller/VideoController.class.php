<?php
namespace Home\Controller;
use Think\Upload;
class VideoController extends SController {

    public function index(){

        //$res = $this->msg_send('like',82,461);
        //var_dump($res);

        $videoViewModel = D('VideoView');
        $where = array(
            "status" => 0
        );
        $this->filter_country($where);
        $list = $videoViewModel->where($where)->order('createtime')->page($_GET['p'], $this->_getPageSize)->select();
        $this -> assign('list',$list);
        $count = $videoViewModel->where($where)->count();
        $Page = new \Think\Page($count, $this->_getPageSize);
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $show = $Page->show();
        $this->assign('page',$show);
        $this->display();
    }

    public function hot(){
        $videoViewModel = D('VideoView');
        $where = array(
            "status" => 1,
            "ishot" => 1
        );
        $count = $videoViewModel->where($where)->count();
        $currPage = 1;
        if (isset($_GET['p'])) {
            $currPage = $_GET['p'];
        }

        $order = "showorder desc";
        if (isset($_GET['sort'])) {
            $sort = $_GET['sort'];
            $order = "playcount " . ($sort == "up" ? "" : "desc");
        }
        $where['orderfixed'] = 0;
        $this->filter_country($where);
        $list = $videoViewModel->where($where)->order($order)->page($_GET['p'], $this->_getPageSize)->select();
        if ($currPage == 1) {
            $where['orderfixed'] = array('gt', 0);
            $fixedVideoList = $videoViewModel -> where($where) -> order('orderfixed') -> select();
            if ($fixedVideoList != false) {
                $list = $this -> _mergeFixedVideo($list, $fixedVideoList);
            }
        }
        $R = M("tag_relation");
        foreach ($list as &$row) {
            $tags = $R->alias('r')->where("video_id=".$row["id"])->join("left join topic on topic.id=r.tag_id")->field("title")->select();
            $row['tags']=implode(" <br/> ", array_column($tags,"title"));
        }
        $this -> assign('list',$list);
        $Page = new \Think\Page($count, $this->_getPageSize);
        $Page->setConfig('header','个视频');
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $show = $Page->show();
        $this->assign('page',$show);
        $this -> assign('currPage', $currPage);
        $this->display();
    }

    public function uservideos($userid){
        $videoViewModel = D('VideoView');
        $where = array(
            "status" => 1,
            "userid" => $userid
        );
        $count = $videoViewModel->where($where)->count();
        $currPage = 1;
        if (isset($_GET['p'])) {
            $currPage = $_GET['p'];
        }

        $order = "id desc";
        if (isset($_GET['sort'])) {
            $sort = $_GET['sort'];
            $order = "playcount " . ($sort == "up" ? "" : "desc");
        }
        $this->filter_country($where);
        $list = $videoViewModel->where($where)->order($order)->page($_GET['p'], $this->_getPageSize)->select();
        $R = M("tag_relation");
        foreach ($list as &$row) {
            $tags = $R->alias('r')->where("video_id=".$row["id"])->join("left join topic on topic.id=r.tag_id")->field("title")->select();
            $row['tags']=implode(" <br/> ", array_column($tags,"title"));
        }
        $this -> assign('list',$list);
        $Page = new \Think\Page($count, $this->_getPageSize);
        $Page->setConfig('header','个视频');
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $show = $Page->show();
        $this->assign('page',$show);
        $this -> assign('currPage', $currPage);
        $this->display();
    }



    private function _mergeFixedVideo($list, $fixedVideoList) {
        for ($j = 0; $j < count($fixedVideoList); $j++) {
            array_splice($list, $fixedVideoList[$j]['orderfixed'] - 1, 0,array($fixedVideoList[$j]));
        }
        return $list;
    }

    public function all(){
        $videoViewModel = D('VideoView');
        $where = array(
            "status" => 1
        );
        $this->filter_country($where);

        $order = "createtime desc";
        if (isset($_GET['sort'])) {
            $sort = $_GET['sort'];
            $order = "playcount " . ($sort == "up" ? "" : "desc");
        }
        $list = $videoViewModel->where($where)->order($order)->page($_GET['p'], $this->_getPageSize)->select();

        $R = M("tag_relation");
        foreach ($list as &$row) {
            $tags = $R->alias('r')->where("video_id=".$row["id"])->join("left join topic on topic.id=r.tag_id")->field("title")->select();
            $row['tags']=implode(" <br/> ", array_column($tags,"title"));
        }

        $this -> assign('list',$list);
        $count = $videoViewModel->where($where)->count();
        $Page = new \Think\Page($count, $this->_getPageSize);
        $Page->setConfig('header','个视频');
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $show = $Page->show();
        $this->assign('page',$show);
        $this->display();
    }

    public function tagHtml($videoid,$country="BR"){
        $tagModel=M("topic");
        $where = array(
            "country" => $country
        );
        $tag_list = $tagModel->where($where)
                ->field("topic.id,title,r.id as rid")
                ->join("left join tag_relation as r on r.tag_id=topic.id and r.video_id=".$videoid)
                ->order('createtime desc')->select();

        $this->assign('tag_list',$tag_list);
        $content = $this->fetch('taghtml');
        echo $content;die;
    }

    public function tagHtmlRadio($videoid,$country="BR"){
        $tagModel=M("topic");
        $where = array(
            "country" => $country
        );
        $tag_list = $tagModel->where($where)
                ->field("topic.id,title,r.id as rid")
                ->join("left join tag_relation as r on r.tag_id=topic.id and r.video_id=".$videoid)
                ->order('createtime desc')->select();
        $this->assign('tag_list',$tag_list);
        $content = $this->fetch('taghtmlradio');
        echo $content;die;
    }

    public function tag_save_r($videoid,$tag_id){
        $model = M("video");
        $res = $model-> where('id='.$videoid)->setField('topicid',$tag_id);
        if($res)
            $this->ajaxReturn(array('code'=>200,'msg'=>""));
        else
            $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
    }

    public function tag_save($videoid,$tags){
        $R = M("tag_relation");

        $R->where('video_id='.$videoid)->delete();

        $params=array();
        $tags = explode(',', $tags);
        foreach ($tags as $tag_id) {
            $params[] = array("tag_id" => $tag_id,"video_id" => $videoid);
        }
        $res = $R -> addAll($params);
        if($res)
            $this->ajaxReturn(array('code'=>200,'msg'=>""));
        else
            $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
    }

    public function delete(){
        $videoViewModel = D('VideoView');
        $where = array(
            "status" => 2
        );
        $this->filter_country($where);
        $list = $videoViewModel->where($where)->order('createtime desc')->page($_GET['p'], $this->_getPageSize)->select();
        $this -> assign('list',$list);
        $count = $videoViewModel->where($where)->count();
        $Page = new \Think\Page($count, $this->_getPageSize);
        $Page->setConfig('header','个视频');
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $show = $Page->show();
        $this->assign('page',$show);
        $this->display();
    }

    public function report(){
        $videoViewModel = D('VideoReportView');
        $where = array(
            "status" => 1
        );
        $this->filter_country($where);
        $list = $videoViewModel->where($where)->order('reportid desc')->page($_GET['p'], $this->_getPageSize)->select();
        $this -> assign('list',$list);
        $count = $videoViewModel->where($where)->count();
        $Page = new \Think\Page($count, $this->_getPageSize);
        $Page->setConfig('header','个视频');
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $show = $Page->show();
        $this->assign('page',$show);
        $this->display();
    }

    public function add() {
        $__token__ = getRandomStr();
        session('__token__',$__token__);
        $this -> assign('__token__', $__token__);
        $allowedCountry = getAllowedCountry();
        $this->filter_allowed_country($allowedCountry);
        $this -> assign('allowCountry', $allowedCountry);
        $this -> display();
    }

    public function play($id = '') {
        if (!is_numeric($id)) {
            $this -> error();
            exit();
        }
        $videoModel = M('video');
        $where = array('id' => $id);
        $videoInfo = $videoModel -> where($where) -> find();
        if ($videoInfo == false) {
            $this -> error();
            exit();
        }
        $this -> assign('src', getBaseUrl() . '/uploads/' . $videoInfo['filepath']);
        $this -> display();
    }

    public function action_check($id = '') {
        if (!is_numeric($id)) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        } else {
            $where = array('id' => $id);
            $videoModel = M('video');
            $videoInfo = $videoModel -> where($where) -> find();
            if ($videoInfo != false) {
                $videoInfo['status'] = 1;
                $ret = $videoModel -> save($videoInfo);
                if ($ret != false) {
                    $this->ajaxReturn(array('code'=>200,'msg'=>""));
                } else {
                    $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
                }
            } else {
                $this->ajaxReturn(array('code'=>404,'msg'=>"资源不存在！"));
            }
        }
    }

    public function action_delete($id = '') {
        if (!is_numeric($id)) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        } else {
            $where = array('id' => $id);
            $videoModel = M('video');
            $videoInfo = $videoModel -> where($where) -> find();
            if ($videoInfo != false) {
                $videoInfo['status'] = 2;
                $ret = $videoModel -> save($videoInfo);
                if ($ret != false) {
                    $this->ajaxReturn(array('code'=>200,'msg'=>""));
                } else {
                    $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
                }
            } else {
                $this->ajaxReturn(array('code'=>404,'msg'=>"资源不存在！"));
            }
        }
    }

    public function action_delete_report($id = '') {
        if (!is_numeric($id)) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        } else {
            $where = array('id' => $id);
            $videoModel = M('report_video');
            $ret = $videoModel -> where($where) -> delete();
            if ($ret != false) {
                $this->ajaxReturn(array('code'=>200,'msg'=>""));
            } else {
                $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
            }
        }
    }

    public function action_sethot($id = '',$v=1) {
        if (!is_numeric($id)) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        } else {
            $where = array('id' => $id);
            $videoModel = M('video');
            $videoInfo = $videoModel -> where($where) -> find();
            if ($videoInfo != false) {
                $userid = intval($videoInfo['userid']);
                if ($userid == 1361 ||
                    $userid == 1362 ||
                    $userid == 1363 ||
                    $userid == 1364 ||
                    $userid == 1365 ||
                    $userid == 1366) {
                    $this->ajaxReturn(array('code'=>301,'msg'=>"内置用户，不允许设为热门！"));
                    return;
                }
                $videoInfo['ishot'] = $v;
                $videoInfo['updatetime'] = time();

                if($v==1){
                    $maxOrderWhere = array(
                        'ishot' => 1,
                        'status' => 1,
                        'orderfixed' => 0
                    );
                    $maxOrder = $videoModel -> where($maxOrderWhere) -> max('showorder');
                    $videoInfo['showorder'] = $maxOrder + 1;
                }

                $ret = $videoModel -> save($videoInfo);
                if ($ret != false) {
                    $this->ajaxReturn(array('code'=>200,'msg'=>""));
                } else {
                    $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
                }
            } else {
                $this->ajaxReturn(array('code'=>404,'msg'=>"资源不存在！"));
            }
        }
    }

    // public function action_cancelhot($id = '') {
    //     if (!is_numeric($id)) {
    //         $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
    //     } else {
    //         $where = array('id' => $id);
    //         $videoModel = M('video');
    //         $videoInfo = $videoModel -> where($where) -> find();
    //         if ($videoInfo != false) {
    //             $videoInfo['ishot'] = 0;
    //             $ret = $videoModel -> save($videoInfo);
    //             if ($ret != false) {
    //                 $this->ajaxReturn(array('code'=>200,'msg'=>""));
    //             } else {
    //                 $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
    //             }
    //         } else {
    //             $this->ajaxReturn(array('code'=>404,'msg'=>"资源不存在！"));
    //         }
    //     }
    // }

    public function action_fixed($id = '', $pos = 0) {
        if (!is_numeric($id) || !is_numeric($pos) || $pos < 1 || $pos > 10) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        } else {
            $where = array('id' => $id);
            $videoModel = M('video');
            $videoInfo = $videoModel -> where($where) -> find();
            if ($videoInfo != false) {
                $videoInfo['orderfixed'] = $pos;
                $ret = $videoModel -> save($videoInfo);
                if ($ret != false) {
                    $this->ajaxReturn(array('code'=>200,'msg'=>""));
                } else {
                    $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
                }
            } else {
                $this->ajaxReturn(array('code'=>404,'msg'=>"资源不存在！"));
            }
        }
    }

    public function action_cancelfixed($id = '') {
        if (!is_numeric($id)) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        } else {
            $where = array('id' => $id);
            $videoModel = M('video');
            $videoInfo = $videoModel -> where($where) -> find();
            if ($videoInfo != false) {
                $videoInfo['orderfixed'] = 0;
                $ret = $videoModel -> save($videoInfo);
                if ($ret != false) {
                    $this->ajaxReturn(array('code'=>200,'msg'=>""));
                } else {
                    $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
                }
            } else {
                $this->ajaxReturn(array('code'=>404,'msg'=>"资源不存在！"));
            }
        }
    }

    public function action_topic($id = '',$val=0) {
        if (!is_numeric($id)) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        } else {
            $where = array('id' => $id);
            $videoModel = M('video');
            $videoInfo = $videoModel -> where($where) -> find();
            if ($videoInfo != false) {
                $videoInfo['istopic'] = $val;
                $ret = $videoModel -> save($videoInfo);
                if ($ret != false) {
                    $this->ajaxReturn(array('code'=>200,'msg'=>""));
                } else {
                    $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
                }
            } else {
                $this->ajaxReturn(array('code'=>404,'msg'=>"资源不存在！"));
            }
        }
    }

    public function action_ordertop($id = '') {
        if (!is_numeric($id)) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        } else {
            $maxOrderWhere = array(
                'ishot' => 1,
                'status' => 1
            );
            $videoModel = M('video');
            $maxOrder = $videoModel -> where($maxOrderWhere) -> max('showorder');
            $where = array(
                'ishot' => 1,
                'status' => 1,
                'id' => $id
            );
            $videoInfo = $videoModel -> where($where) -> find();
            if ($videoInfo == false) {
                $this->ajaxReturn(array('code'=>404,'msg'=>"资源不存在！"));
            } else {
                if ($videoInfo['showorder'] == $maxOrder) {
                    $this->ajaxReturn(array('code'=>302,'msg'=>"已在最前端"));
                } else {
                    $videoInfo['showorder'] = $maxOrder + 1;
                    $result =$videoModel -> save($videoInfo);
                    if ($result > 0) {
                        $this->ajaxReturn(array('code'=>200,'msg'=>""));
                    } else {
                        $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
                    }
                }
            }
        }
    }

    public function action_changevideothumb($id='') {
        if (!is_numeric($id)) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        } else {
            $thumbPath = $this -> _uploadThumb();
            if ($thumbPath === false) {
                $this->ajaxReturn(array('code'=>501,'msg'=>"上传失败！"));
            } else {
                $videoModel = M('video');
                $videoInfo = array(
                    "id" => $id,
                    "thumb" => $thumbPath
                );
                $result = $videoModel -> save($videoInfo);
                if ($result == false) {
                    $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
                } else {
                    $this->ajaxReturn(array('code'=>200,'msg'=>""));
                }
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
            return false;
        }else{// 上传成功 获取上传文件信息
            return $info['savepath'].$info['savename'];
        }
    }

    public function getAllTopic() {
        $topicModel = M('topic');
        $where = array('country' => 'BR');
        $list = $topicModel -> where($where) -> field('id,title') -> select();
        echo json_encode($list);
    }

    public function action_changeTopic($id = '', $tid = '') {
        if (!is_numeric($id) || !is_numeric($tid) || $tid < 1) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        } else {
            $videoModel = M('video');
            $videoInfo = array(
                "id" => $id,
                "topicid" => $tid
            );
            $result = $videoModel -> save($videoInfo);
            if ($result == false) {
                $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
            } else {
                $this->ajaxReturn(array('code'=>200,'msg'=>""));
            }
        }
    }

    public function action_addVlike($videoid , $num = 0) {
        if (!is_numeric($videoid) || !is_numeric($num) || $num < 1) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        } else {

            $likeModel = M('vlike');

            $userids=$this->getRandUserId($num);

            if(!is_array($userids))
                $userids = array($userids);

            $inc=0;
            foreach ($userids as $uid) {
                $map = array("videoid" => $videoid,"userid" => $uid,"country" => 'BR');
                $res = $likeModel->where($map)->find();
                if(!$res){
                    $map['createtime']=time();
                    $likeModel->add($map);
                    $inc++;
                }
            }
            $result = M('video')->where('id='.$videoid)->setInc('likecount',$inc);
            if ($result == false) {
                $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
            } else {
                $this->ajaxReturn(array('code'=>200,'msg'=>"成功添加{$inc}个赞",'data'=>$inc));
            }
        }
    }

    public function action_addPlayCount($videoid , $num = 0) {
        if (!is_numeric($videoid) || !is_numeric($num) || $num < 1) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        } else {

            $videoModel = M('video');
            $video = $videoModel -> where(array("id" => $videoid)) -> find();
            if ($video != false) {
                $video['playcount'] = intval($video['playcount']) + $num;
                $result = $videoModel -> save($video);
                if ($result == false) {
                    $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
                } else {
                    $this->ajaxReturn(array('code'=>200,'msg'=>"成功添加{$num}个赞",'data'=>$num));
                }
            } else {
                $this->ajaxReturn(array('code'=>400,'msg'=>"视频不存在!"));
            }
        }
    }


    /**
     * 发现视频
     * @return [type] [description]
     */
    public function discover($search = ""){
        $Model = M();
        if (!empty($search)) {
            $where = "";
            $countWhere = "";
            if (is_numeric($search)) {
                $where = "v.id={$search}";
                $countWhere = "id={$search}";
            } else {
                if (strlen($search) < 4) {
                    echo "名称搜索最少4个字符，当前" . strlen($search);
                    exit();
                }
                $where = "v.description like '%{$search}%'";
                $countWhere = "description like '%{$search}%'";
            }
            $sql = "SELECT
                v.*,
                (SELECT COUNT(*) FROM `record_video` WHERE `video_id`=v.`id` AND `type`=2 ) AS b_num ,  #差时间筛选 播放
                (SELECT COUNT(*) FROM `comment` WHERE `video_id`=v.`id` ) AS p_num ,  #差时间筛选 品论
                (SELECT COUNT(*) FROM `vlike` WHERE `videoid`=v.`id` ) AS z_num,   #差时间筛选  点赞
                (SELECT u.username FROM `user` u WHERE u.`id` = v.`userid`) AS username
            FROM
                `video` v
            WHERE v.`status` = 1 and {$where}";
            $count = M('video')->where("`status` = 1 and {$countWhere}")->count();
        } else {
            $p = $_GET['p']?$_GET['p']:1;
            $offset=($p-1)*$this->_getPageSize;

            $time_sql="";
            if($_GET['stime']){
                $stime = strtotime($_GET['stime']);
                $etime = strtotime($_GET['etime'])+3600*24;
                $time_sql = " AND createtime > {$stime} AND createtime < {$etime}  ";
            }

            $ord_sql = " ORDER BY id DESC  ";
            if($_GET['ord']){
                $ord_sql = " ORDER BY {$_GET['ord']} DESC  ";
            }
            $country_sql="";
            if($_GET['country']){
                $country_sql = " AND v.`country`= '{$_GET['country']}' ";
                $country_count = array('country'=>"{$_GET['country']}");
            }


            $sql = "SELECT
                v.*,
                (SELECT COUNT(*) FROM `record_video` WHERE `video_id`=v.`id` AND `type`=2 {$time_sql} ) AS b_num ,  #差时间筛选 播放
                (SELECT COUNT(*) FROM `comment` WHERE `video_id`=v.`id` {$time_sql} ) AS p_num ,  #差时间筛选 品论
                (SELECT COUNT(*) FROM `vlike` WHERE `videoid`=v.`id` {$time_sql} ) AS z_num,   #差时间筛选  点赞
                (SELECT u.username FROM `user` u WHERE u.`id` = v.`userid`) AS username
            FROM
                `video` v
            WHERE v.`status` = 1
            {$country_sql}
            {$ord_sql}

            LIMIT {$offset},{$this->_getPageSize}";
            $count = M('video')->where($country_count)->where(" `status` = 1")->count();
        }

        $voList = $Model->query($sql);
        $this->assign('list',$voList);

        $Page = new \Think\Page($count, $this->_getPageSize);
        $Page->setConfig('header','个视频');
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $show = $Page->show();
        //var_dump($videoViewModel->getLastSql());
        $this->assign('page',$show);
        $this->assign('allowCountry', getAllowedCountry());
        $this->display();
    }

    /**
     * 同步video表id冗余 以便统计查询
     * @return [type] [description]
     */
    public function sync(){


        echo $en = $this->encode_vid('2');
        echo $this->decode_vid($en);
            $sql = " UPDATE `record_video` t SET t.`video_id` = (SELECT v.`id` FROM `video` v WHERE v.`uuid` = t.`videouuid` ) ";

            // UPDATE `user` u SET avatar = CONCAT(u.`id`,".jpg") WHERE id > 54

    }

}