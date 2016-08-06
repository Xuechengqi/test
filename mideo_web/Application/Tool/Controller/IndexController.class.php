<?php
namespace Tool\Controller;
use Think\Controller;
use Think\Upload;

class IndexController extends SController {
    protected $app_ini_file = 'index/mp4_app_ini.php';

    public function index(){
        $app_ini = require_once($this->app_ini_file);
        $this -> assign('app_ini', $app_ini);
        $this -> display();
    }

    public function action_update($version="") {
        if (empty($version)|| !is_numeric($version)) {
            $this -> error('参数有误', "index");
            return;
        }

        $arr=array('VERSION'=>$version);

        $str='<?php return ' . var_export($arr,true) . '; ?>';
        $ret = file_put_contents($this->app_ini_file, $str);

        if ($ret > 0) {
            $this -> success('更新成功'.$appurl, 'index');
        } else {
            $this -> error('更新失败', "index");
        }
    }
}