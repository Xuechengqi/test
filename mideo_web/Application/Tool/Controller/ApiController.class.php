<?php
namespace Tool\Controller;
use Think\Controller;

class ApiController extends SController {
    protected $app_ini_file = 'index/mp4_app_ini.php';

    public function version($code = "") {
        if(!file_exists($this -> app_ini_file)){
            return show_output(400, "");
        }
        $app_ini = require_once($this -> app_ini_file);
        if($app_ini['VERSION']>$code){
            return show_output(200, "", true);
        }else{
            return show_output(200, "", false);
        }

    }
}