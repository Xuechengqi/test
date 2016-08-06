<?php
    function buildResourceUrl($videos) {
        if (!isset($videos) || $videos == null || empty($videos)) {
            return $videos;
        }
        if (is_array($videos)) {
            for ($i = 0, $j = count($videos); $i < $j; $i++) {
                $videos[$i] = buildResourceUrlOne($videos[$i]);
            }
        } else {
            $videos = buildResourceUrlOne($videos);
        }
        return $videos;
    }

    function buildResourceUrlOne($video) {
        if (!isset($video) || $video == null || empty($video)) {
            return $video;
        }
        if (isset($video['thumb']) && !empty($video['thumb'])) {
            $video['thumb'] = getUploadResourceBaseUrl() . $video['thumb'];
        }
        if (isset($video['filepath']) && !empty($video['filepath'])) {
            $video['filepath'] = getUploadResourceBaseUrl() . $video['filepath'];
        }
        if (isset($video['avatar']) && !empty($video['avatar'])) {
            if(strtolower(substr($video['avatar'],0,4)) != 'http')
                $video['avatar'] = getAvatarResourceBaseUrl() . $video['avatar'];
            else
                $video['avatar'] = $video['avatar'];
        }
        return $video;
    }

    function buildTopicResourceUrl($topics) {
        if (!isset($topics) || $topics == null || empty($topics)) {
            return $topics;
        }
        for ($i=0; $i < count($topics); $i++) {
            if (isset($topics[$i]['picture']) && !empty($topics[$i]['picture'])) {
                $topics[$i]['picture'] = getUploadResourceBaseUrl() . $topics[$i]['picture'];
            }
            if (isset($topics[$i]['video']) && !empty($topics[$i]['video'])) {
                $topics[$i]['video'] = getUploadResourceBaseUrl() . $topics[$i]['video'];
            }
        }
        return $topics;
    }

    function buildSingleTopicResourceUrl($topic) {
        if (isset($topic['picture']) && !empty($topic['picture'])) {
            $topic['picture'] = getUploadResourceBaseUrl() . $topic['picture'];
        }
        if (isset($topic['video']) && !empty($topic['video'])) {
            $topic['video'] = getUploadResourceBaseUrl() . $topic['video'];
        }
        return $topic;
    }

    function buildStickerResourceUrl($stickers) {
        if (!isset($stickers) || $stickers == null || empty($stickers)) {
            return $stickers;
        }
        for ($i=0; $i < count($stickers); $i++) {
            if (isset($stickers[$i]['url']) && !empty($stickers[$i]['url'])) {
                $stickers[$i]['url'] = getStickerResourceBaseUrl() . $stickers[$i]['url'];
            }
            if (isset($stickers[$i]['thumb']) && !empty($stickers[$i]['thumb'])) {
                $stickers[$i]['thumb'] = getStickerResourceBaseUrl() . $stickers[$i]['thumb'];
            }
        }
        return $stickers;
    }

    function buildSplashResourceUrl($splash) {
        if (!isset($splash) || $splash == null || empty($splash)) {
            return $splash;
        }
        return getSplashResourceBaseUrl() . $splash;
    }

    function checkLogin(){
        //登录检查
        if (isset($_SESSION['login']) && $_SESSION['login'] == true && $_SESSION['rule'] == 'admin') {
            return true;
        }
        return false;
    }

    function checkToken() {
        if(checkLogin()){
            //后台登入后无需验证
            return true;
        }
        $token = '';
        if (isset($_GET['token'])) {
            $token = $_GET['token'];
        } else if (isset($_POST['token'])) {
            $token = $_POST['token'];
        }
        if($token==="ac9698f65294e3bd935e5d05d360e089"){
            //监控宝专用
            return ;
        }
        if (empty($token)) {
            return show_output(110, "token error");
        } else {
            $tokenCache = S(array('prefix'=>'token'));
            $cachedToken = $tokenCache -> token;
            if ($cachedToken == false) {
                return show_output(119, "token expried");
            } else if (strlen($cachedToken['access_token']) != 32 || $cachedToken['access_token'] !== $token){
                return show_output(110, "token error");
            }
        }
    }

    function _mergeVideoAttentions($result, $attentions) {
        for ($i=0; $i < count($result); $i++) {
            $result[$i]['attention'] = 0;
            for ($j=0; $j < count($attentions); $j++) {
                if (intval($result[$i]['userid']) == intval($attentions[$j]['touserid'])) {
                    $result[$i]['attention'] = 1;
                }
            }
        }
        return $result;
    }

    function _mergeVideoLikes($result, $likes) {
        for ($i=0; $i < count($result); $i++) {
            $result[$i]['like'] = 0;
            for ($j=0; $j < count($likes); $j++) {
                if (intval($result[$i]['id']) == intval($likes[$j]['videoid'])) {
                    $result[$i]['like'] = 1;
                    break;
                }
            }
            unset($result[$i]['id']);
        }
        return $result;
    }

    function _mergeVideoComments($result, $comments) {
        for ($i=0; $i < count($result); $i++) {
            $result[$i]['comments'] = array();
            for ($j=0; $j < count($comments); $j++) {
                if ($result[$i]['id'] == $comments[$j]['videoid']) {
                    unset($comments[$j]['videoid']);
                    $result[$i]['comments'][] = $comments[$j];
                }
            }
        }
        return $result;
    }

    function _mergeUserAttentions($result, $attentions) {
        for ($i=0; $i < count($result); $i++) {
            $result[$i]['attention'] = 0;
            for ($j=0; $j < count($attentions); $j++) {
                if (intval($result[$i]['id']) == intval($attentions[$j]['touserid'])) {
                    $result[$i]['attention'] = 1;
                }
            }
        }
        return $result;
    }

    function _mergeTopicUserAttentions($result, $attentions) {
        for ($i=0; $i < count($result); $i++) {
            $result[$i]['attention'] = 0;
            for ($j=0; $j < count($attentions); $j++) {
                if (intval($result[$i]['userid']) == intval($attentions[$j]['touserid'])) {
                    $result[$i]['attention'] = 1;
                }
            }
        }
        return $result;
    }
?>