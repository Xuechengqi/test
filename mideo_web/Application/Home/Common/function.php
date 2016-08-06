<?php
    function isLogin() {
        $ret = false;
        if (isset($_SESSION['login']) && $_SESSION['login'] == true && $_SESSION['rule'] == 'admin') {
            $ret = true;
        }
        return $ret;
    }


    function getAdminUserName() {
        return $_SESSION['username'];
    }

    function getToken() {
        $tokenCache = S(array('prefix'=>'token'));
        $token = $tokenCache -> token;
        if ($token == false) {
            return '';
        } else {
            return $token['access_token'];
        }
    }

    function analyticsVideoTypeHash() {
        return array(
            "create" => 1,
            "view" => 2,
            "play" => 3,
            "like" => 4,
            "comment" => 5
        );
    }

    function startsWith($haystack, $needle) {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }

    function endsWith($haystack, $needle) {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
    }

    function getDateRangeFromForm() {
        $today = strtotime(date('Y-m-d'));
        $ret = array (
            'begin' => date('Y-m-d', strtotime('-30 day', $today)),
            'end' => date('Y-m-d', strtotime('-1 day', $today)),
        );
        if (isset($_GET['dateRange'])) {
            $dateRange = $_GET['dateRange'];
            if ($dateRange == 'today') {
                $ret['begin'] = date('Y-m-d');
                $ret['end'] = date('Y-m-d');
            } else if ($dateRange == 'last_7_days') {
                $ret['begin'] = date('Y-m-d', strtotime('-7 day', $today));
                $ret['end'] = date('Y-m-d', strtotime('-1 day', $today));
            } else if ($dateRange == 'last_30_days') {

            } else if ($dateRange == 'this_month') {
                $ret['begin'] = date('Y-m-01');
                $ret['end'] = date('Y-m-d');
            } else if ($dateRange == 'last_month') {
                $ret['begin'] = date('Y-m-d', strtotime('-1 month', strtotime(date('Y-m-01'))));
                $ret['end'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-01'))));
            } else if ($dateRange == 'custom') {
                if (isset($_GET['timebegin']) && isset($_GET['timeend'])) {
                    $ret['begin'] = $_GET['timebegin'];
                    $ret['end'] = $_GET['timeend'];
                }
            }
        }
        return $ret;
    }

    function getAvatarUrl($uri){
        if(empty($uri)){
            return getAvatarResourceBaseUrl()."default.png";
        }elseif (strtolower(substr($uri,0,4)) == 'http') {
            return $uri;
        }else{
            return getAvatarResourceBaseUrl().$uri;
        }
    }
?>