<?php
namespace Home\Controller;
use Think\Controller;
class AuthController extends Controller {
    public function login() {
        if (isset($_POST['username'])
            && isset($_POST['username'])
            && isset($_POST['token'])) {
            $usr = $_POST['username'];
            $pwd = $_POST['password'];
            $token = $_POST['token'];
            if ($token !== $this -> _getToken()) {
                $this -> error('error');
            }
            if (($usr == 'adminhw' && $pwd == 'hw2016mideo')
                || ($usr == 'xialinwei' && $pwd == 'hw2016xlw')
                || ($usr == 'durongjian' && $pwd == 'hw2016drj')
                || ($usr == 'wuqi' && $pwd == 'hw2016wq')
                || ($usr == 'lixing' && $pwd == 'hw2016lx')
                || ($usr == 'wukeqing' && $pwd == 'hw2016wkq')
                || ($usr == 'guoziqing' && $pwd == 'hw2016gzq')
                || ($usr == 'lilining' && $pwd == 'hw2016lln')
                || ($usr == 'yueao' && $pwd == 'hw2016ya')
                ||($usr == 'zhangyanchao' && $pwd == 'hw2016zyc')) {
                $_SESSION['login'] = true;
                $_SESSION['username'] = $usr;
                $_SESSION['rule'] = 'admin';
                $this -> redirect('Index/index');
            } else {
                $this -> error('username or password error!', 'login');
            }
        } else {
            //$this -> assign('token', $this -> _getToken());
            $this -> display();
        }
    }

    public function logout() {
        unset($_SESSION['login']);
        unset($_SESSION['username']);
        unset($_SESSION['uid']);
        $this -> redirect('Auth/login');
    }

    private function _getToken() {
        // for ($i = 0; $i < 31; $i++) {
        //     echo "'" . date('d', strtotime("+$i days", strtotime(date('Y-m-d')))) . "' => '" . md5(getRandomStr(20)) . "',<br/>";
        // }
        // exit();
        $token = array(
            '01' => 'd68e0101f188d21b8ba54fb659f9beac',
            '02' => '3794abe5b3ada897f2db56a017f31ec4',
            '03' => '89f4d140bfef04fda83bdf8097b0eaaa',
            '04' => 'e3db6297746a9b048fb4099e07500e41',
            '05' => '1d7073b7076075023dce090153ba51d8',
            '06' => 'b1d66f2f790d2fa92f3192a1916be100',
            '07' => '1aea18bcd446bd7755f3a2a809459ff6',
            '08' => '3a7b33e7c7e945b802b55c7331d7bf3d',
            '09' => '9b1881129a79b82366f5191abc05839e',
            '10' => 'd6f4ddf8e94ba01ae250cd0ad8d5fc89',
            '11' => '00be09918673e913775f53571e9769ba',
            '12' => 'dd4d5e18f580147674c5e6ede7309502',
            '13' => 'c7111080f676c711dd32df04607dc1c8',
            '14' => '0a9a887339fc00ed471607724cc89742',
            '15' => 'a41d0a27e5c10159647229feabe854c6',
            '16' => '11eaa1a1c7ba57a6598e580e32831ccf',
            '17' => '6f466df188e2352211c12693039e4115',
            '18' => 'd968e1cb3803aa7b42bfe41c89e1fd03',
            '19' => 'f611ea0c3e1ed4617d9e8e749a5abed4',
            '20' => 'bddf330502f7e49a7eff2dafafecafe5',
            '21' => 'c22ede1c55e06a30403a40619f457cb5',
            '22' => 'e9fa00f28c7534010fb24995fac8a7ab',
            '23' => '2ecc8e8d398dd2fb8307e6b39dbe902c',
            '24' => '891aa5aab723d324902d02f66d7edc88',
            '25' => 'ca1b3386fd2149409b131146adf9d60b',
            '26' => '0d692b2f3f0aaab345ac98475e434a51',
            '27' => '540e2138cbec76ca9cb497479908e360',
            '28' => 'de76fa9de79a40ea0f0b34acd6a8fe9a',
            '29' => 'f367754db25489ce29e6fb4884ad034e',
            '30' => 'f69d69ff562f8f7b6fe7968faac1f913',
            '31' => '62c84cce8e317d86e4931ec450906053',
        );
        $d = date('d', strtotime("+11 hours", time()));
        return $token[$d];
    }
}