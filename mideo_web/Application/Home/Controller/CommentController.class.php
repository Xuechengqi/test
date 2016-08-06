<?php
namespace Home\Controller;
class CommentController extends SController {
    private function _getPageSize() {
        return 25;
    }

    public function index(){
        $commentViewModel = D('CommentView');
        $where = array(
            "status" => 0,
            "videostatus" => 1
        );
        $list = $commentViewModel->where($where)->order('createtime')->page($_GET['p'], $this -> _getPageSize())->select();
        $this -> assign('list',$list);
        $count = $commentViewModel->where($where)->count();
        $Page = new \Think\Page($count, $this -> _getPageSize());
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $show = $Page->show();
        $this->assign('page',$show);
        $this->display();
    }

    public function all(){
        $commentViewModel = D('CommentView');
        $where = array(
            "status" => 1,
            "videostatus" => 1
        );
        $list = $commentViewModel->where($where)->order('createtime desc')->page($_GET['p'], $this -> _getPageSize())->select();
        $this -> assign('list',$list);
        $count = $commentViewModel->where($where)->count();
        $Page = new \Think\Page($count, $this -> _getPageSize());
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $show = $Page->show();
        $this->assign('page',$show);
        $this->display();
    }

    public function delete(){
        $commentViewModel = D('CommentView');
        $where = array(
            "status" => 2,
            "videostatus" => 1
        );
        $list = $commentViewModel->where($where)->order('createtime desc')->page($_GET['p'], $this -> _getPageSize())->select();
        $this -> assign('list',$list);
        $count = $commentViewModel->where($where)->count();
        $Page = new \Think\Page($count, $this -> _getPageSize());
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $show = $Page->show();
        $this->assign('page',$show);
        $this->display();
    }

    public function report(){
        $commentViewModel = D('CommentReportView');
        $where = array(
            "status" => 1,
            "videostatus" => 1
        );
        $list = $commentViewModel->where($where)->order('id desc')->page($_GET['p'], $this -> _getPageSize())->select();
        $this -> assign('list',$list);
        $count = $commentViewModel->where($where)->count();
        $Page = new \Think\Page($count, $this -> _getPageSize());
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
            $commentModel = M('comment');
            $commentInfo = $commentModel -> where($where) -> find();
            if ($commentInfo != false) {
                $commentInfo['status'] = 1;
                $ret = $commentModel -> save($commentInfo);
                if ($ret != false) {
                    $this->ajaxReturn(array('code'=>200,'msg'=>""));
                } else {
                    $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
                }
            } else {
                $this->ajaxReturn(array('code'=>404,'msg'=>"评论不存在！"));
            }
        }
    }

    public function action_delete($id = '') {
        if (!is_numeric($id)) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        } else {
            $where = array('id' => $id);
            $commentModel = M('comment');
            $commentInfo = $commentModel -> where($where) -> find();
            if ($commentInfo != false) {
                $commentInfo['status'] = 2;
                $ret = $commentModel -> save($commentInfo);
                if ($ret != false) {
                    $this->ajaxReturn(array('code'=>200,'msg'=>""));
                } else {
                    $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
                }
            } else {
                $this->ajaxReturn(array('code'=>404,'msg'=>"评论不存在！"));
            }
        }
    }

    public function action_delete_report($id = '') {
        if (!is_numeric($id)) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        } else {
            $where = array('id' => $id);
            $videoModel = M('report_comment');
            $ret = $videoModel -> where($where) -> delete();
            if ($ret != false) {
                $this->ajaxReturn(array('code'=>200,'msg'=>""));
            } else {
                $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
            }
        }
    }
}