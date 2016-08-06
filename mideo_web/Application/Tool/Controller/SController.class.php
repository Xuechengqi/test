<?php
/**
 * 后台控制器基类
 * 登入后方可使用
 */
namespace Tool\Controller;
use Think\Controller;
class SController extends Controller {
    public function _initialize(){
        //登录检查
        if (isset($_SESSION['login']) && $_SESSION['login'] == true && $_SESSION['rule'] == 'admin') {
            return true;
        }
        $this -> redirect('Auth/login');
    }
}