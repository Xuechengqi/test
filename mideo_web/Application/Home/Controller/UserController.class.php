<?php
namespace Home\Controller;
class UserController extends SController {

    public function index(){
        $userModel = M('user');
        $where = array(
            "status" => 0
        );
        $list = $userModel->where($where)->order('id')->page($_GET['p'], $this->_getPageSize)->select();
        $this -> assign('list',$list);
        $count = $userModel->where($where)->count();
        $Page = new \Think\Page($count, $this->_getPageSize);
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $show = $Page->show();
        $this->assign('page',$show);
        $this->display();
    }

    public function all(){
        $userModel = M('user');
        $where = array(
            "status" => 1
        );
        $list = $userModel->where($where)->order('id desc')->page($_GET['p'], $this->_getPageSize)->select();
        $this -> assign('list',$list);
        $count = $userModel->where($where)->count();
        $Page = new \Think\Page($count, $this->_getPageSize);
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $show = $Page->show();
        $this->assign('page',$show);
        $this->display();
    }

    public function recommend(){
        $userModel = M('user');
        $where = array(
            "status" => 1,
            "isrecommend" => 1
        );
        $list = $userModel->where($where)->order('recommendtime desc')->page($_GET['p'], $this->_getPageSize)->select();
        $this -> assign('list',$list);
        $count = $userModel->where($where)->count();
        $Page = new \Think\Page($count, $this->_getPageSize);
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $show = $Page->show();
        $this->assign('page',$show);
        $this->display();
    }

    public function action_recommendsort($isdown, $ids) {
        if (empty($ids)) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        } else {
            $userModel = M('user');
            $idArr = explode(",", $ids);
            $userItems = array();
            for ($i = 0; $i < count($idArr); $i++) {
                $userItems[] = $userModel -> where(array("id" => $idArr[$i])) -> find();
            }
            $tmpOrd = 0;
            if ($isdown == 1) {
                $tmpOrd = $userItems[count($userItems) - 1]['recommendtime'];
                for ($i = count($userItems) - 1; $i > 0; $i--) {
                    $userItems[$i]['recommendtime'] = $userItems[$i - 1]['recommendtime'];
                }
                $userItems[0]['recommendtime'] = $tmpOrd;
            } else {
                $tmpOrd = $userItems[0]['recommendtime'];
                for ($i = 0; $i < count($userItems); $i++) {
                    if ($i == count($userItems) - 1) {
                        $userItems[$i]['recommendtime'] = $tmpOrd;
                    } else {
                        $userItems[$i]['recommendtime'] = $userItems[$i + 1]['recommendtime'];
                    }
                }
            }
            $ret = 1;
            for ($i = 0; $i < count($userItems); $i++) {
                $ret = $userModel -> save($userItems[$i]);
                if ($ret == false) {
                    break;
                }
            }

            if ($ret != false) {
                $this->ajaxReturn(array('code'=>200,'msg'=>""));
            } else {
                $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
            }
        }
    }

    public function add() {
        $country = getAllCountry();
        unset($country['AA']);
        $this -> assign('country', $country);
        $this->display();
    }

    public function delete(){
        $userModel = M('user');
        $where = array(
            "status" => 2
        );
        $list = $userModel->where($where)->order('id')->page($_GET['p'], $this->_getPageSize)->select();
        $this -> assign('list',$list);
        $count = $userModel->where($where)->count();
        $Page = new \Think\Page($count, $this->_getPageSize);
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $show = $Page->show();
        $this->assign('page',$show);
        $this->display();
    }

    public function action_check($id = '') {
        if (!is_numeric($id)) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        } else {
            $where = array('id' => $id);
            $userModel = M('user');
            $userInfo = $userModel -> where($where) -> find();
            if ($userInfo != false) {
                $userInfo['status'] = 1;
                $ret = $userModel -> save($userInfo);
                if ($ret != false) {
                    $this->ajaxReturn(array('code'=>200,'msg'=>""));
                } else {
                    $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
                }
            } else {
                $this->ajaxReturn(array('code'=>404,'msg'=>"用户不存在！"));
            }
        }
    }

    public function action_delete($id = '') {
        if (!is_numeric($id)) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        } else {
            $where = array('id' => $id);
            $userModel = M('user');
            $userInfo = $userModel -> where($where) -> find();
            if ($userInfo != false) {
                $userInfo['status'] = 2;
                $ret = $userModel -> save($userInfo);
                M('attention') -> where("fromuserid = $id or touserid = $id") -> delete();
                if ($ret != false) {
                    $this->ajaxReturn(array('code'=>200,'msg'=>""));
                } else {
                    $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
                }
            } else {
                $this->ajaxReturn(array('code'=>404,'msg'=>"用户不存在！"));
            }
        }
    }
    public function action_back($id = '') {
        if (!is_numeric($id)) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        } else {
            $where = array('id' => $id);
            $userModel = M('user');
            $userInfo = $userModel -> where($where) -> find();
            if ($userInfo != false) {
                $userInfo['status'] = 1;
                $ret = $userModel -> save($userInfo);
                if ($ret != false) {
                    $this->ajaxReturn(array('code'=>200,'msg'=>""));
                } else {
                    $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
                }
            } else {
                $this->ajaxReturn(array('code'=>404,'msg'=>"用户不存在！"));
            }
        }
    }

    public function action_setrecommend($id = '') {
        if (!is_numeric($id)) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        } else {
            $where = array('id' => $id);
            $userModel = M('user');
            $userInfo = $userModel -> where($where) -> find();
            if ($userInfo != false) {
                $userInfo['isrecommend'] = 1;
                $userInfo['recommendtime'] = time();
                $ret = $userModel -> save($userInfo);
                if ($ret != false) {
                    $this->ajaxReturn(array('code'=>200,'msg'=>""));
                } else {
                    $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
                }
            } else {
                $this->ajaxReturn(array('code'=>404,'msg'=>"用户不存在！"));
            }
        }
    }

    public function action_cancelrecommend($id = '') {
        if (!is_numeric($id)) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        } else {
            $where = array('id' => $id);
            $userModel = M('user');
            $userInfo = $userModel -> where($where) -> find();
            if ($userInfo != false) {
                $userInfo['isrecommend'] = 0;
                $ret = $userModel -> save($userInfo);
                if ($ret != false) {
                    $this->ajaxReturn(array('code'=>200,'msg'=>""));
                } else {
                    $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
                }
            } else {
                $this->ajaxReturn(array('code'=>404,'msg'=>"用户不存在！"));
            }
        }
    }

     /**
     * 发现用户
     * @return [type] [description]
     */
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
                $where = "u.username like '%{$search}%'";
                $countWhere = "username like '%{$search}%'";
            }
            $sql = "SELECT
                u.*,
                (SELECT COUNT(*) FROM `video` t1 WHERE t1.`userid`=u.`id` AND t1.`status`=1  ) AS v_num , #视频数
                (SELECT COUNT(*) FROM `vlike` t2 WHERE t2.`userid`=u.`id` ) AS x_num , #喜欢视频数
                (SELECT COUNT(*) FROM `attention` t3 WHERE t3.`fromuserid`=u.`id` AND t3.`relation`=1 ) AS g_num , #关注人数
                (SELECT COUNT(*) FROM `attention` t4 WHERE t4.`touserid`=u.`id` AND t4.`relation`=1 ) AS f_num , #粉丝数
                (SELECT COUNT(*) FROM `comment` t5 WHERE t5.`userid`=u.`id` AND t5.`status`=1  ) AS p_num  #评论他人数
            FROM
                `user` u
            WHERE u.`status` = 1 and {$where}";
            $count = M('user')->where("`status` = 1 and {$countWhere}")->count();
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
                (SELECT COUNT(*) FROM `video` t1 WHERE t1.`userid`=u.`id` AND t1.`status`=1 {$time_sql} ) AS v_num , #视频数
                (SELECT COUNT(*) FROM `vlike` t2 WHERE t2.`userid`=u.`id` {$time_sql} ) AS x_num , #喜欢视频数
                (SELECT COUNT(*) FROM `attention` t3 WHERE t3.`fromuserid`=u.`id` AND t3.`relation`=1 ) AS g_num , #关注人数
                (SELECT COUNT(*) FROM `attention` t4 WHERE t4.`touserid`=u.`id` AND t4.`relation`=1 ) AS f_num , #粉丝数
                (SELECT COUNT(*) FROM `comment` t5 WHERE t5.`userid`=u.`id` AND t5.`status`=1 {$time_sql} ) AS p_num  #评论他人数
            FROM
                `user` u
            WHERE u.`status` = 1
            {$country_sql}
            {$ord_sql}

            LIMIT {$offset},{$this->_getPageSize}";
            $count = M('user')->where($country_count)->where(" `status` = 1")->count();
        }

        $voList = $Model->query($sql);
        $this->assign('list',$voList);

        $Page = new \Think\Page($count, $this->_getPageSize);
        $Page->setConfig('header','个用户');
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $show = $Page->show();
        //var_dump($videoViewModel->getLastSql());
        $this->assign('page',$show);
        $this->assign('allowCountry', getAllowedCountry());
        $this->display();
    }

    /**
     * 用户关注管理 getfollows
     * @return [type] [description]
     */
    public function follows($userid){
        $model = M('attention');
        $where = array(
            "fromuserid" => $userid,
            "relation" => 1
        );
        $user = M('user')->where(" `id` = {$userid}")->find();
        $list = $model->where($where)->join('LEFT JOIN user AS t ON t.id = touserid')->field('attention.id,t.id as userid,t.username,t.country')->order('id desc')->select();

        $this -> assign('list',$list);
        $this -> assign('user',$user);

        //var_dump($videoViewModel->getLastSql());
        $this->display();
    }

}