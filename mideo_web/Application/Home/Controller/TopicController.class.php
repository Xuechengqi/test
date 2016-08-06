<?php
namespace Home\Controller;
use Think\Upload;

class TopicController extends SController {

    // 分页参数 默认10
    protected $_getPageSize = 20;

    public function index($country = "AA"){
        $topicModel = M('topic');
        $where = array("country" => $country,"status"=>1);
        if ($country === "AA") {
            unset($where["country"]);
        }
        $list = $topicModel->where($where)->order('ord desc')->page($_GET['p'], $this->_getPageSize)->select();
        $this -> assign('list',$list);
        $count = $topicModel->where($where)->count();
        $Page = new \Think\Page($count, $this->_getPageSize);
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->parameter["country"] = $country;
        $show = $Page->show();
        $this-> assign('page',$show);
        $this -> assign('currCountry', $country);
        $this -> assign('country', getAllCountry());
        $this->display();
    }

    public function add() {
        $country = getAllCountry();
        unset($country['AA']);
        $this -> assign('country', $country);
        $this->display();
    }

    public function show($id = 0) {
        if ($id > 0) {
            $topicModel = M('topic');
            $where = array("id" => $id);
            $item = $topicModel -> where($where) -> find();
            $this -> assign("item", $item);
        }
        $this->display();
    }

    public function edit($id = 0) {
        $topicModel = M('topic');
        $where = array("id" => $id);
        $item = $topicModel -> where($where) -> find();
        $this -> assign("item", $item);

        $country = getAllCountry();
        unset($country['AA']);
        $this -> assign('country', $country);
        $this -> assign("currCountry", $item['country']);
        $this->display();
    }

    public function action_topicsort($isdown, $ids) {
        if (empty($ids)) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        } else {
            $topicModel = M('topic');
            $idArr = explode(",", $ids);
            $topicItems = array();
            for ($i = 0; $i < count($idArr); $i++) {
                $topicItems[] = $topicModel -> where(array("id" => $idArr[$i])) -> find();
            }
            $tmpOrd = 0;
            if ($isdown == 1) {
                $tmpOrd = $topicItems[count($topicItems) - 1]['ord'];
                for ($i = count($topicItems) - 1; $i > 0; $i--) {
                    $topicItems[$i]['ord'] = $topicItems[$i - 1]['ord'];
                }
                $topicItems[0]['ord'] = $tmpOrd;
            } else {
                $tmpOrd = $topicItems[0]['ord'];
                for ($i = 0; $i < count($topicItems); $i++) {
                    if ($i == count($topicItems) - 1) {
                        $topicItems[$i]['ord'] = $tmpOrd;
                    } else {
                        $topicItems[$i]['ord'] = $topicItems[$i + 1]['ord'];
                    }
                }
            }
            $ret = 1;
            for ($i = 0; $i < count($topicItems); $i++) {
                $ret = $topicModel -> save($topicItems[$i]);
                if ($ret === false) {
                    break;
                }
            }

            if ($ret !== false) {
                $this->ajaxReturn(array('code'=>200,'msg'=>""));
            } else {
                $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
            }
        }
    }

    public function action_add($country = '', $title="", $content = '', $summary="") {
        if (empty($country) || empty($title)) {
            $this -> error('参数有误', "add");
            return;
        }

        $picture = '';
        $hot_pic = '';
        $video = '';
        if (isset($_FILES['picture']) && !empty($_FILES['picture']['name'])
            || isset($_FILES['hot_pic']) && !empty($_FILES['hot_pic']['name'])
            || isset($_FILES['video']) && !empty($_FILES['video']['name'])) {
            $uploadResult = $this -> _uploadPicture($country);
            if (isset($uploadResult['picture'])) {
                $picture = $uploadResult['picture'];
            }
            if (isset($uploadResult['hot_pic'])) {
                $hot_pic = $uploadResult['hot_pic'];
            }
            if (isset($uploadResult['video'])) {
                $video = $uploadResult['video'];
            }
        }


        $params = array(
            "country" => $country,
            "show" => $_REQUEST['show'],
            "title" => $title,
            "summary" => $summary,
            "content" => $content,
            "picture" => $picture,
            "hot_pic" => $hot_pic,
            "video"   => $video,
            "createtime" => time()
        );
        $topicModel = M('topic');
        $ret = $topicModel -> add($params);
        if ($ret === false) {
            $this -> error('数据库操作失败', "add");
        } else {
            $topicModel->where('id='.$ret)->setField('ord',$ret);
            $this -> success('添加话题成功', 'index');
        }
    }

    public function action_edit($id = '', $country = '', $title="", $content = '', $summary="") {
        if (empty($country) || empty($title) || empty($id)) {
            $this -> error('参数有误', "index");
            return;
        }
        $picture = '';
        $hot_pic = '';
        $video = '';
        if (isset($_FILES['picture']) && !empty($_FILES['picture']['name'])
            || isset($_FILES['hot_pic']) && !empty($_FILES['hot_pic']['name'])
            || isset($_FILES['video']) && !empty($_FILES['video']['name'])) {
            $uploadResult = $this -> _uploadPicture($country);
            if (isset($uploadResult['picture'])) {
                $picture = $uploadResult['picture'];
            }
            if (isset($uploadResult['hot_pic'])) {
                $hot_pic = $uploadResult['hot_pic'];
            }
            if (isset($uploadResult['video'])) {
                $video = $uploadResult['video'];
            }
        }
        $topicModel = M('topic');
        $topic = $topicModel -> where(array("id" => $id)) -> find();
        if ($topic === false) {
            $this -> error('话题不存在', "index");
            return;
        }
        $topic['country'] = $country;
        $topic['show'] = $_REQUEST['show'];
        $topic['title'] = $title;
        $topic['summary'] = $summary;
        $topic['content'] = $content;
        if (!empty($picture)) {
            $topic['picture'] = $picture;
        }
        if (!empty($hot_pic)) {
            $topic['hot_pic'] = $hot_pic;
        }
        if (!empty($video)) {
            $topic['video'] = $video;
        }
        $ret = $topicModel -> save($topic);
        if ($ret === false) {
            $this -> error('数据库操作失败', "add");
        } else {
            $this -> success('修改话题成功', 'index');
        }
    }

    private function _uploadPicture($country){
        $upload = new upload();// 实例化上传类
        $upload->maxSize   =     1024 * 1024 * 1000 ;// 设置附件上传大小1024kb
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg', "mp4", "mkv");// 设置附件上传类型
        $upload->rootPath  =     './uploads/'; // 设置附件上传根目录
        $upload->savePath  =     'topic/' . $country . date('/Ym/');
        $upload->autoSub = false;
        // 上传单个文件
        $info   =   $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        }else{// 上传成功 获取上传文件信息
            $result = array();
            if (isset($info['picture'])) {
                $result['picture'] =  $info['picture']['savepath'].$info['picture']['savename'];
            }
            if (isset($info['hot_pic'])) {
                $result['hot_pic'] =  $info['hot_pic']['savepath'].$info['hot_pic']['savename'];
            }
            if (isset($info['video'])) {
                $result['video'] =  $info['video']['savepath'].$info['video']['savename'];
            }
            return $result;
        }
    }

    public function action_delete($id = '') {
        if (empty($id)) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        } else {
            $topicModel = M('topic');
            $ret = $topicModel -> where(array("id" => $id)) -> setField('status',-1);
            if ($ret === false) {
                $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
            } else {
                $this->ajaxReturn(array('code'=>200,'msg'=>"OK"));
            }
        }
    }

    public function action_show($id = '') {
        if (empty($id)) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        } else {
            $topicModel = M('topic');
            $ret = $topicModel -> where(array("id" => $id)) -> setField('show',1);
            if ($ret === false) {
                $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
            } else {
                $this->ajaxReturn(array('code'=>200,'msg'=>"OK"));
            }
        }
    }

    public function action_hide($id = '') {
        if (empty($id)) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        } else {
            $topicModel = M('topic');
            $ret = $topicModel -> where(array("id" => $id)) -> setField('show',0);
            if ($ret === false) {
                $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
            } else {
                $this->ajaxReturn(array('code'=>200,'msg'=>"OK"));
            }
        }
    }


    //标签视频列表
    public function video_list($id = '') {
        $R = D('tag_relation');
        $where = array(
            "tag_id" => $id
        );
        $count = $R->where($where)->count();
        $currPage = 1;
        if (isset($_GET['p'])) {
            $currPage = $_GET['p'];
        }

        $list = $R->alias('t')->where($where)->order('showorder desc')->page($_GET['p'], $this->_getPageSize)
                ->field("v.*,t.id as rid")
                ->join("left join video as v on v.id=t.video_id ")
                ->select();

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

    public function tag_video_del($id = '') {
        if (empty($id)) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        } else {
            $Model = M('tag_relation');
            $ret = $Model -> where(array("id" => $id)) -> delete();
            if ($ret === false) {
                $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
            } else {
                $this->ajaxReturn(array('code'=>200,'msg'=>"OK"));
            }
        }
    }


    public function discover($search = ""){
        $Model = M();
        if (!empty($search)) {
            $where = "";
            $countWhere = "";
            if (is_numeric($search)) {
                $where = "u.id={$search}";
                $countWhere = "id={$search}";
            } else {
                if (strlen($search) < 4) {
                    echo "名称搜索最少4个字符，当前" . strlen($search);
                    exit();
                }
                $where = "u.title like '%{$search}%'";
                $countWhere = "title like '%{$search}%'";
            }
            $sql = "SELECT
                u.*,

                (SELECT COUNT(*) FROM `tag_relation` r WHERE r.`tag_id`=u.`id`) AS v_num , #视频数

                (SELECT COUNT(DISTINCT t2.`userid`) FROM `tag_relation` r1 left join `video` t2 on t2.`id`=r1.`video_id` WHERE r1.`tag_id`=u.`id`) AS p_num  #参与人数


            FROM
                `topic` u
            WHERE {$where}";
            $count = M("topic")->where($countWhere)->count();
        } else {
            $p = $_GET['p']?$_GET['p']:1;
            $offset=($p-1)*$this->_getPageSize;

            $time_sql="";
            if($_GET['stime']){
                $stime = strtotime($_GET['stime']);
                $etime = strtotime($_GET['etime'])+3600*24;
                $time_sql = " AND u.`createtime` > {$stime} AND u.`createtime` < {$etime}  ";
            }

            $ord_sql = " ORDER BY id DESC  ";
            if($_GET['ord']){
                $ord_sql = " ORDER BY {$_GET['ord']} DESC  ";
            }
            $country_sql="";
            if($_GET['country']){
                $country_sql = " AND u.`country`= '{$_GET['country']}' ";
                $country_count = array('country'=>"{$_GET['country']}");
            }

            //视频数
            //喜欢视频数
            //关注人数
            //粉丝数
            //评论他人数

            $sql = "SELECT
                u.*,

                (SELECT COUNT(*) FROM `tag_relation` r WHERE r.`tag_id`=u.`id`) AS v_num , #视频数

                (SELECT COUNT(DISTINCT t2.`userid`) FROM `tag_relation` r1 left join `video` t2 on t2.`id`=r1.`video_id` WHERE r1.`tag_id`=u.`id`) AS p_num  #参与人数


            FROM
                `topic` u
            WHERE 1 = 1
            {$time_sql}
            {$country_sql}
            {$ord_sql}

            LIMIT {$offset},{$this->_getPageSize}";
            $count = $Model->table(array('topic'=>'u'))->where(" 1 = 1 {$time_sql} {$country_sql}")->count();
        }

        $voList = $Model->query($sql);
        $this->assign('list',$voList);

        $Page = new \Think\Page($count, $this->_getPageSize);
        $Page->setConfig('header','个话题');
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $show = $Page->show();
        //var_dump($videoViewModel->getLastSql());
        $this->assign('page',$show);
        $this->assign('allowCountry', getAllowedCountry());
        $this->display();
    }

}