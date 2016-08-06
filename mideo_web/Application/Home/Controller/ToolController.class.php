<?php
namespace Home\Controller;
use Think\Controller;
use Think\Upload;

class ToolController extends SController {
    public function index($country = 'BR',$cid = '') {
        $musicModel = M('music');
        $where = array(
            "country" => $country
        );

        if($cid!==''){
            $where['cid']=$cid;
        }


        $list = $musicModel->where($where)->order('id desc')->select();
        $this -> assign('list',$list);
        $this -> assign('currCountry', $country);


        $model = M('music_cate');
        $where = array("country" => $country);
        $cate_list = $model -> where($where) ->order('id desc')-> select();
        $this -> assign("cate_list", $cate_list);
        $this -> assign('cid', $cid);

        $this -> assign('country', getAllCountry());
        $this->display();
    }

    public function add() {
        $country = getAllCountry();
        unset($country["AA"]);
        $this -> assign('country', $country);

        $c = key($country);
        $model = M('music_cate');
        $where = array("country" => $c);
        $cate_list = $model -> where($where) ->order('id desc')-> select();
        $this -> assign("cate_list", $cate_list);
        $this->display();
    }

    public function edit($id = 0) {
        $musicModel = M('music');
        $where = array("id" => $id);
        $item = $musicModel -> where($where) -> find();
        $this -> assign("item", $item);

        $model = M('music_cate');
        $where = array("country" => $item['country']);
        $cate_list = $model -> where($where) ->order('id desc')-> select();
        $this -> assign("cate_list", $cate_list);

        $country = getAllCountry();
        unset($country["AA"]);
        $this -> assign('country', $country);
        $this -> assign("currCountry", $item['country']);
        $this->display();
    }
    public function mcate($country = 'BR') {
        $allcountry = getAllCountry();
        unset($allcountry["AA"]);
        $this -> assign('country', $country);
        $this -> assign('allcountry', $allcountry);

        $model = M('music_cate');
        $where = array("country" => $country);
        $list = $model -> where($where) ->order('id desc')-> select();
        $this -> assign("list", $list);

        $model = M('music_cate');
        $where = array("country" => $country);
        $cate_list = $model -> where($where) ->order('id desc')-> select();
        $this -> assign("cate_list", $cate_list);

        $this->display("music_cate");
    }

    public function mcate_list($country = 'BR') {
        $model = M('music_cate');
        $where = array("country" => $country);
        $list = $model -> where($where) ->order('id desc')-> select();
        $this->ajaxReturn(array('code'=>200,'msg'=>"OK",'data'=>$list));
    }



    public function action_mcate_add($name="",$country = 'BR') {
        if (empty($country) || empty($name)) {
            $this -> error('参数有误', "add");
            return;
        }
        $params = array(
            "country" => $country,
            "name" => $name
        );
        $model = M('music_cate');
        $ret = $model -> add($params);
        if ($ret === false) {
            $this->ajaxReturn(array('code'=>301,'msg'=>"数据库操作失败"));
        } else {
            $this->ajaxReturn(array('code'=>200,'msg'=>"OK"));
        }
    }
    public function action_mcate_delete($id="",$new_id="") {
        if($id==$new_id){
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        }
        if (empty($id)) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        } else {
            $musicModel = M('music_cate');
            $ret = $musicModel -> where(array("id" => $id)) -> delete();
            M('music') -> where('cid='.$id)->setField('cid',$new_id);
            if ($ret === false) {
                $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
            } else {
                $this->ajaxReturn(array('code'=>200,'msg'=>"OK"));
            }
        }
    }


    public function action_add($country = '', $cid = 0, $title="", $author = '', $duration = 0, $url = "") {
        if (empty($country) || empty($author) || empty($title) || empty($duration)) {
            $this -> error('参数有误', "add");
            return;
        }

        $bakurl = $this -> _uploadMusicBakurl($country);
        $artworkurl = $this -> _uploadMusicArtworkurl($country);

        $params = array(
            "country" => $country,
            "title" => $title,
            "cid" => $cid,
            "author" => $author,
            "duration" => intval($duration),
            "url" => $url,
            "bakurl" => $bakurl,
            "artworkurl" => $artworkurl,
            "addtime" => time()
        );
        $musicModel = M('music');
        $ret = $musicModel -> add($params);
        if ($ret === false) {
            $this -> error('数据库操作失败', "index");
        } else {
            $this -> success('添加成功', 'index');
        }
    }

    private function _uploadMusicBakurl($country){
        //备份地址
        $upload = new upload();// 实例化上传类
        $upload->maxSize   =     1024 * 1024 * 5 ;// 设置附件上传大小5MB
        $upload->exts      =     array("mp3","m4a");// 设置附件上传类型
        $upload->rootPath  =     './uploads/music/'; // 设置附件上传根目录
        $upload->savePath  =     '';
        $upload->autoSub = false;
        // 上传单个文件
        $info   =   $upload->uploadOne($_FILES['bakurl']);
        if(!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        }else{// 上传成功 获取上传文件信息
            return 'music/'.$info['savepath'].$info['savename'];
        }
    }

    private function _uploadMusicArtworkurl($country){
        //封面
        $upload = new upload();// 实例化上传类
        $upload->maxSize   =     1024 * 500 ;// 设置附件上传大小500KB
        $upload->exts      =     array("jpeg", "jpg", "JPG", "JPEG", "png", "bmp");// 设置附件上传类型
        $upload->rootPath  =     './uploads/music/'; // 设置附件上传根目录
        $upload->savePath  =     '';
        $upload->autoSub = false;
        // 上传单个文件
        $info   =   $upload->uploadOne($_FILES['artworkurl']);
        if(!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        }else{// 上传成功 获取上传文件信息
            return 'music/'.$info['savepath'].$info['savename'];
        }
    }


    public function action_edit($id = 0, $cid = 0, $country = '', $title="", $author = '', $duration = 0, $url = "") {
        if (empty($country) || empty($author) || empty($title) || empty($duration) ) {
            //var_dump($country,$author,$title,$duration);die;
            $this -> error('参数有误', "index");
            return;
        }
        $musicModel = M('music');
        $music = $musicModel -> where(array("id" => $id)) -> find();
        if ($music === false) {
            $this -> error('音乐不存在', "index");
            return;
        }

        $params = array(
            "id" => $id,
            "country" => $country,
            "cid" => $cid,
            "title" => $title,
            "author" => $author,
            "duration" => intval($duration),
            "url" => $url,
            "addtime" => time()
        );
        $ret = $musicModel -> save($params);
        if ($ret === false) {
            $this -> error('数据库操作失败', "index");
        } else {
            $this -> success('修改成功', 'index');
        }
    }

    public function action_delete($id = '') {
        if (empty($id)) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        } else {
            $musicModel = M('music');
            $ret = $musicModel -> where(array("id" => $id)) -> delete();
            if ($ret === false) {
                $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
            } else {
                $this->ajaxReturn(array('code'=>200,'msg'=>"OK"));
            }
        }
    }

    public function stickerindex($country = 'US') {
        $musicModel = M('sticker');
        $where = array(
            "country" => $country
        );
        $list = $musicModel->where($where)->order('id desc')->select();
        $this -> assign('list',$list);
        $this -> assign('currCountry', $country);
        $this -> assign('country', getAllCountry());
        $this->display();
    }

    public function stickeradd() {
        $this -> assign('country', getAllCountry());
        $this->display();
    }

    public function stickeredit($id = 0) {
        $musicModel = M('sticker');
        $where = array("id" => $id);
        $item = $musicModel -> where($where) -> find();
        $this -> assign("item", $item);
        $this -> assign('country', getAllCountry());
        $this -> assign("currCountry", $item['country']);
        $this->display();
    }

    public function stickeraction_add($country = '', $title="", $duration = 0) {
        if (empty($country) || empty($title)) {
            $this -> error('参数有误', "stickeradd");
            return;
        }
        $url = $this -> _uploadSticker($country);
        $thumb = $this -> _uploadThumb($country);
        $params = array(
            "country" => $country,
            "title" => $title,
            "thumb" => $thumb,
            "duration" => intval($duration),
            "url" => $url,
            "addtime" => time()
        );
        $musicModel = M('sticker');
        $ret = $musicModel -> add($params);
        if ($ret === false) {
            $this -> error('数据库操作失败', "stickerindex");
        } else {
            $this -> success('添加成功', 'stickerindex');
        }
    }

    private function _uploadSticker($country){
        $upload = new upload();// 实例化上传类
        $upload->maxSize   =     1024 * 1024 * 20 ;// 设置附件上传大小20MB
        $upload->exts      =     array("mp4", "mkv", "jpeg", "jpg", "png", "bmp");// 设置附件上传类型
        $upload->rootPath  =     './uploads/sticker/'; // 设置附件上传根目录
        $upload->savePath  =     $country . '/';
        $upload->autoSub = false;
        // 上传单个文件
        $info   =   $upload->uploadOne($_FILES['sticker']);
        if(!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        }else{// 上传成功 获取上传文件信息
            return $info['savepath'].$info['savename'];
        }
    }

    private function _uploadThumb($country){
        $upload = new upload();// 实例化上传类
        $upload->maxSize   =     1024 * 500 ;// 设置附件上传大小500KB
        $upload->exts      =     array("jpeg", "jpg", "png", "bmp");// 设置附件上传类型
        $upload->rootPath  =     './uploads/sticker/'; // 设置附件上传根目录
        $upload->savePath  =     $country . '/';
        $upload->autoSub = false;
        // 上传单个文件
        $info   =   $upload->uploadOne($_FILES['thumb']);
        if(!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        }else{// 上传成功 获取上传文件信息
            return $info['savepath'].$info['savename'];
        }
    }

    public function stickeraction_edit($id = 0, $country = '', $title="") {
        if (empty($country) || empty($title)) {
            $this -> error('参数有误', "stickerindex");
            return;
        }
        $musicModel = M('sticker');
        $music = $musicModel -> where(array("id" => $id)) -> find();
        if ($music === false) {
            $this -> error('贴纸不存在', "stickerindex");
            return;
        }
        $url = $music['url'];
        $thumb = $music['thumb'];
        if (isset($_FILES['sticker']) && !empty($_FILES['sticker']['name'])) {
            $url = $this -> _uploadSticker($country);
        }
        if (isset($_FILES['thumb']) && !empty($_FILES['thumb']['name'])) {
            $thumb = $this -> _uploadThumb($country);
        }

        $params = array(
            "id" => $id,
            "country" => $country,
            "title" => $title,
            "thumb" => $thumb,
            "duration" => intval($duration),
            "url" => $url,
            "addtime" => time()
        );
        $ret = $musicModel -> save($params);
        if ($ret === false) {
            $this -> error('数据库操作失败', "stickerindex");
        } else {
            $this -> success('修改成功', 'stickerindex');
        }
    }

    public function stickeraction_delete($id = '') {
        if (empty($id)) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        } else {
            $musicModel = M('sticker');
            $ret = $musicModel -> where(array("id" => $id)) -> delete();
            if ($ret === false) {
                $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
            } else {
                $this->ajaxReturn(array('code'=>200,'msg'=>"OK"));
            }
        }
    }

    public function splashindex($country = 'BR') {
        $musicModel = M('splash');
        $where = array(
            "country" => $country
        );
        $list = $musicModel->where($where)->order('id desc')->select();
        $this -> assign('list',$list);
        $this -> assign('currCountry', $country);
        $this -> assign('country', getAllCountry());
        $this->display();
    }

    public function splashadd() {
        $this -> assign('country', getAllCountry());
        $this->display();
    }

    public function splashaction_delete($id) {
        if (empty($id)) {
            $this->ajaxReturn(array('code'=>101,'msg'=>"参数错误！"));
        } else {
            $musicModel = M('splash');
            $ret = $musicModel -> where(array("id" => $id)) -> delete();
            if ($ret === false) {
                $this->ajaxReturn(array('code'=>301,'msg'=>"数据库错误！"));
            } else {
                $this->ajaxReturn(array('code'=>200,'msg'=>"OK"));
            }
        }
    }

    public function splashaction_add($country) {
        if (empty($country)) {
            $this -> error('参数有误', "splashadd");
            return;
        }
        $path = $this -> _uploadSplash($country);
        $params = array(
            "country" => $country,
            "path" => $path,
            "addtime" => time()
        );
        $splashModel = M('splash');
        $ret = $splashModel -> add($params);
        if ($ret === false) {
            $this -> error('数据库操作失败', "splashindex");
        } else {
            $this -> success('添加成功', 'splashindex');
        }
    }

    private function _uploadSplash($country){
        $upload = new upload();// 实例化上传类
        $upload->maxSize   =     1024 * 500 ;// 设置附件上传大小100kb
        $upload->exts      =     array("png");// 设置附件上传类型
        $upload->rootPath  =     './uploads/splash/'; // 设置附件上传根目录
        $upload->savePath  =     $country . '/';
        $upload->autoSub = false;
        // 上传单个文件
        $info   =   $upload->uploadOne($_FILES['path']);
        if(!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        }else{// 上传成功 获取上传文件信息
            return $info['savepath'].$info['savename'];
        }
    }
}