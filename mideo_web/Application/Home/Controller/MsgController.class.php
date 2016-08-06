<?php
namespace Home\Controller;

/*
1、like
2、follow
3、comment
4、system
5、chat
*/
class MsgController extends SController {

    private function _getPageSize() {
        return 25;
    }


    public function index(){
        $messageModel = M('message');
        $where = array(
        );
        $list = $messageModel->where($where)->order('id desc')->page($_GET['p'], $this -> _getPageSize())->select();
        $this -> assign('list',$list);
        $count = $messageModel->where($where)->count();
        $Page = new \Think\Page($count, $this -> _getPageSize());
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $show = $Page->show();
        $this->assign('page',$show);
        $this->display();
    }

    public function system(){
        $messageModel = M('system_message');
        $where = array(
        );
        $list = $messageModel->where($where)->order('id desc')->page($_GET['p'], $this -> _getPageSize())->select();
        $this -> assign('list',$list);
        $count = $messageModel->where($where)->count();
        $Page = new \Think\Page($count, $this -> _getPageSize());
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $show = $Page->show();
        $this->assign('page',$show);
        $this->display();
    }

    public function comment(){
        $messageModel = M('message');
        $where = array(
            "eventtype" => getMessageEventType('comment')
        );
        $list = $messageModel->where($where)->order('id desc')->page($_GET['p'], $this -> _getPageSize())->select();
        $this -> assign('list',$list);
        $count = $messageModel->where($where)->count();
        $Page = new \Think\Page($count, $this -> _getPageSize());
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $show = $Page->show();
        $this->assign('page',$show);
        $this->display();
    }

    public function like(){
        $messageModel = M('message');
        $where = array(
            "eventtype" => getMessageEventType('like')
        );
        $list = $messageModel->where($where)->order('id desc')->page($_GET['p'], $this -> _getPageSize())->select();
        $this -> assign('list',$list);
        $count = $messageModel->where($where)->count();
        $Page = new \Think\Page($count, $this -> _getPageSize());
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $show = $Page->show();
        $this->assign('page',$show);
        $this->display();
    }

    public function attention(){
        $messageModel = M('message');
        $where = array(
            "eventtype" => getMessageEventType('attention')
        );
        $list = $messageModel->where($where)->order('id desc')->page($_GET['p'], $this -> _getPageSize())->select();
        $this -> assign('list',$list);
        $count = $messageModel->where($where)->count();
        $Page = new \Think\Page($count, $this -> _getPageSize());
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $show = $Page->show();
        $this->assign('page',$show);
        $this->display();
    }

    public function at(){
        $messageModel = M('message');
        $where = array(
            "eventtype" => getMessageEventType('at')
        );
        $list = $messageModel->where($where)->order('id desc')->page($_GET['p'], $this -> _getPageSize())->select();
        $this -> assign('list',$list);
        $count = $messageModel->where($where)->count();
        $Page = new \Think\Page($count, $this -> _getPageSize());
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $show = $Page->show();
        $this->assign('page',$show);
        $this->display();
    }

    public function add(){
        $this -> assign('allowCountry', getAllowedCountry());
        $this->display();
    }

    public function action_add($country = "BR", $content = "",$title = "", $actionurl = ""){
        if (empty($country) || empty($content)) {
            $this -> error("参数有误!");
        } else {

            //$content = unicode_decode($content);

            //$content = unicode2utf8('[mideo]\uD83D\uDE04[/mideo]');
            //var_dump($content);die;
            //$content=unicode2utf8(utf8_encode($content));
            //var_dump($content);die;
            $systemMessageModel = M('system_message');
            $params = array(
                "title" => $title,
                "content" => $content,
                "addtime" => time(),
                "click" => empty($actionurl) ? 0 : 1,
                "actionurl" => $actionurl
            );
            $countryArr = explode(",", $country);
            $failCount = 0;
            if (count($countryArr) > 0) {
                for($i = 0; $i < count($countryArr); $i++) {
                    $country = $countryArr[$i];
                    if (!checkCountry($country)) {
                        $country = getDefaultCountry();
                    }
                    $params['country'] = $country;
                    $ret = $systemMessageModel -> add($params);
                    if ($ret === false) {
                        $failCount = $failCount + 1;
                    }
                }
            }
            if ($failCount > 0) {
                $this -> error("部分失败：" . $failCount);
            } else {
                $this -> success("添加成功");
            }
        }
    }

    public function action_delete($id = 0){
        if (!is_numeric($id)) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        }
        if ($id <= 0) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        }
        $model = M('message');
        $ret = $model -> where(array("id" => $id)) -> delete();
        if ($ret === false) {
            $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
        } else {
            $this->ajaxReturn(array('code'=>200,'msg'=>"OK"));
        }
    }


    public function action_delete_system($id = 0){
        if (!is_numeric($id)) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        }
        if ($id <= 0) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        }
        $model = M('system_message');
        $ret = $model -> where(array("id" => $id)) -> delete();
        if ($ret === false) {
            $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
        } else {
            $this->ajaxReturn(array('code'=>200,'msg'=>"OK"));
        }
    }
}