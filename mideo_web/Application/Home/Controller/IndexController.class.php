<?php
namespace Home\Controller;
use Think\Controller;
use Think\Upload;
class IndexController extends Controller {
    protected $app_ini_file = 'index/app_ini.php';
    public function index(){
        if (isLogin()) {
            $app_ini = require_once($this->app_ini_file);

            $this -> assign('app_ini', $app_ini);


            $this -> display();
        } else {
            $this -> redirect('Auth/login');
        }
    }

    public function action_search($type, $key) {
        if (!empty($key)) {
            if ($type == 1) {
                $this -> redirect('User/discover', array('search' => $key));
            } else if ($type == 2) {
                $this -> redirect('Video/discover', array('search' => $key));
            } else if ($type == 3) {
                $this -> redirect('Topic/discover', array('search' => $key));
            }
        }
    }


    public function action_update($version="") {
        if (empty($version)|| !is_numeric($version)) {
            $this -> error('参数有误', "index");
            return;
        }

        $appurl = $this -> _uploadApp();

        $arr=array('VERSION'=>$version);

        $str='<?php return ' . var_export($arr,true) . '; ?>';
        $ret = file_put_contents($this->app_ini_file, $str);

        if ($ret > 0) {
            $this -> success('更新成功'.$appurl, 'index');
        } else {
            $this -> error('更新失败', "index");
        }
    }

    private function _uploadApp(){
        //备份地址
        $upload = new upload();// 实例化上传类
        $upload->maxSize   =     1024 * 1024 * 30 ;// 设置附件上传大小30MB
        $upload->exts      =     array("apk");// 设置附件上传类型
        $upload->rootPath  =     './index/'; // 设置附件上传根目录
        $upload->savePath  =     '';
        $upload->replace = true;
        $upload->autoSub = false;
        $upload->saveName = 'android_mideo';

        // 上传单个文件
        $info   =   $upload->uploadOne($_FILES['app']);
        if(!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        }else{// 上传成功 获取上传文件信息
            return ''.$info['savepath'].$info['savename'];
        }
    }

    public function st() {
        $zones = array(
            "America/New_York" =>   "America/New_York",
            "Europe/London" =>      "Europe/London",
            "Europe/Paris" =>       "Europe/Paris",
            "Europe/Berlin" =>      "Europe/Berlin",
            "Asia/Jerusalem" =>     "Asia/Jerusalem",
            "Europe/Moscow" =>      "Europe/Moscow",
            "Asia/Beijing" =>      "Asia/Shanghai",
            "Asia/Tokyo" =>         "Asia/Tokyo",
        );
        $timelist = array();
        foreach ($zones as $key => $value) {
            $d = new \DateTime("now", new \DateTimeZone($value));
            $fd = $d->format('Y-m-d H:i:00');
            $timelist[] = array(
                "name" => $key,
                "time" => $fd
            );
        }
        $this -> assign('timelist', $timelist);
        $this -> display();
    }
}