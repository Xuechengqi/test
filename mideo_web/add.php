<?php
    $userid = "";
    $username = "Mideo";
    if (isset($_GET['userid'])) {
        $userid = $_GET['userid'];
    }
    if (isset($_GET['username'])) {
        $username = $_GET['username'];
    }
    if (!is_numeric($userid)) {
        die('error');
    }
    $videos = array();
    if (!empty($userid)) {
        require_once('auto/auto_lib.php');
        require_once('Application/Lib/CryptDes.class.php');
        $des = new \Lib\CryptDes;
        $db = new mysql();
        $userName = $db -> findBySql("SELECT username FROM user WHERE id={$userid}");
        $username = $userName['username'];
        $videos = $db->findAllBySql("SELECT id,thumb,likecount,description from video where userid={$userid} AND status = 1 ORDER BY id desc LIMIT 0, 8");
        $followCount = $db -> findBySql("SELECT COUNT(0) count FROM `attention` a INNER JOIN `user` u ON u.id = a.touserid WHERE a.`fromuserid`={$userid} AND a.`relation`=1 AND a.`touserid` != {$userid} AND u.`status` = 1");
        if ($followCount) {
            $followCount = intval($followCount['count']);
        } else {
            $followCount = 0;
        }

        $fansCount = $db -> findBySql("SELECT COUNT(0) count FROM `attention` a INNER JOIN `user` u ON u.id = a.fromuserid WHERE a.`touserid`={$userid} AND a.`relation`=1 AND a.`fromuserid` != {$userid} AND u.`status` = 1");
        if ($fansCount) {
            $fansCount = intval($fansCount['count']);
        } else {
            $fansCount = 0;
        }
    }

    function getUploadResourceBaseUrl() {
        $baseUrl = $_SERVER["HTTP_HOST"];
        if (strpos($baseUrl, "mideo.cc") === FALSE) {
            return $baseUrl . "/uploads/";
        }
        return 'http://uploads.mideo.cc/';
        //return getBaseUrl() . '/uploads/';
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mideo:Grave o melhor momento</title>
    <link href="/favicon.ico" rel="icon" type="image/ico" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=0,minimal-ui">
    <meta name="full-screen" content="yes">
    <meta name="x5-fullscreen" content="true">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="browsermode" content="application">
    <meta name="x5-page-mode" content="app">
    <meta content="Mideo:Grave o melhor momento" property="og:title">
    <meta property="og:image" content="http://www.mideo.cc/index/img/x_5_3.png"/>
    <meta content="O aplicativo de Mideo é um aplicativo que ajudou-me conhecer melhor o nosso maravilhoso mundo e encontrei muitos novos amigos aqui,acredito que você também vai adorar este aplicativo." property="og:description">
    <link rel="stylesheet" href="index/css/add.css" />
    <script>
    window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)};ga.l=+new Date;
    ga('create', 'UA-77132610-2', 'auto');
    </script>
    <script async src='//www.google-analytics.com/analytics.js'></script>
</head>
<body>
    <!-- <div class="video_bg">
        <div class="mask"></div>
    </div> -->
    <div class="video_bg">
        <div class="wrap">
            <div class="app_lang">
                <div><span class="icon"></span><span class="show_lang">Português</span></div>
                <div class="clang" data-lang='br'>Português</div>
                <div class="clang" data-lang='en'>English</div>
                <div class="clang" data-lang='es'>Español</div>
            </div>

            <div class="app_logo"></div>
            <div class="context">
                <p class=""><?php echo $username; ?></p>
                <p class="lang_0">Baixe agora o Mideo para se divertir comigo.</p>
            </div>
            <div class="user_follow">
                <div class="focus"><span class="lang_2">Seguir</span>：<?php echo $followCount; ?></div>
                <div class="fans"><span class="lang_3">seguindo</span>：<?php echo $fansCount; ?></div>
            </div>
            <a id="market" href="javascript:void(0);" class="logo">
                <span class="icon1"></span>
                <div class="right">
                    <div class="up lang_1">DISPONíVEL NO</div>
                    <div class="down" >Google Play</div>
                </div>
            </a>
            <ul>
                <li class="c1"><a class="fb" href="###"></a></li>
                <li class="c2"><a class="tw" href="###"></a></li>
                <li class="c3"><a class="ins" href="###"></a></li>
            </ul>
        </div>
    </div>
    <div class="video_box_frame">
        <div class="video_box active">
            <ul class="video_thumbnails">
                <?php for ($i = 0; $i < count($videos); $i++) : ?>
                    <li class="video_thumbnail">
                        <div class="video_item">
                            <a class="video_thumbnail_img" data-id="<?php echo $des -> encrypt($videos[$i]['id']); ?>">
                                <img src="<?php echo getUploadResourceBaseUrl().$videos[$i]['thumb']; ?>" alt="" />
                            </a>
                        </div>
                        <div class="video_item_info">
                            <a class="upload_head_thumbnail">
                                <img />
                            </a>
                            <a class="upload_name_thumbnail"><?php echo $username; ?></a>
                            <span class="favo_number">
                                <img src="page/img/like.png" alt="" />
                                <span><?php echo $videos[$i]['likecount']; ?></span>
                            </span>
                        </div>
                        <div class="video_description">
                            <?php echo $videos[$i]['description']; ?>
                        </div>
                    </li>
                <?php endfor; ?>
            </ul>
        </div>
    </div>
    <div class="download download_bottom">
        <a class="btn_down ga_bottom">
            <span class="download_lang">Veja os últimos vídeos, baixe o <span style='color:#ffff02'>Mideo</span> agora</span>
        </a>
    </div>
    <iframe id="l" width="1" height="1" style="visibility:hidden"></iframe>
    <script src="index/js/jquery-2.2.2.min.js"></script>
    <script src="page/js/decode.js"></script>
    <script src="page/js/emoji.js"></script>
    <script src="index/js/add.js"></script>
    <?php if (!empty($userid) && is_numeric($userid)) : ?>
    <script type="text/javascript">
        function avatar(data) {
            if (data && data.code == 200) {
                var avatar = data.data.avatar;
                $('.app_logo').css("backgroundImage","url("+avatar+")");
                $('.upload_head_thumbnail img').attr('src',avatar);
            }
        }
    </script>
    <script type="text/javascript" src="http://www.mideo.cc/api.php/user/getavatar_jp/id/<?php echo $userid; ?>/cb/avatar"></script>
    <?php endif; ?>
    <script type="text/javascript">

        var videoId = "";
        var country = "BR";

        var baseURL = "http://www.mideo.cc/api.php";
        var appVideoUrl = "mideo://video";
        var appMainUrl = "mideo://main";

        var videoPage = "./page/play.php";
        var homePage = "index.html";

    function getCookie(c_name){
        if (document.cookie.length>0){
            c_start=document.cookie.indexOf(c_name + "=")
            if (c_start!=-1){
                c_start=c_start + c_name.length+1
                c_end=document.cookie.indexOf(";",c_start)
                if (c_end==-1) c_end=document.cookie.length
                return unescape(document.cookie.substring(c_start,c_end))
            }
        }
        return ""
    }

    function setCookie(c_name,value,expiredays){
        var exdate=new Date()
        exdate.setDate(exdate.getDate()+expiredays)
        document.cookie=c_name+ "=" +escape(value)+
        ((expiredays==null) ? "" : ";expires="+exdate.toGMTString())
    }

    function getLang(){
        var lang='br';
        var co_lang = getCookie('lang');
        if(co_lang != "")
            lang = co_lang;
        return lang;
    }

    function changeLang(lang){
        var data={
            'en':[
                'Welcome to make fun with me and download Mideo APP now!',
                'Get it on',
                'follow',
                'following'
            ],
            'br':[
                'Baixe agora o Mideo para se divertir comigo.',
                'DISPONíVEL NO',
                'Seguir',
                'seguindo'
            ],
            'es': [
                'Descargar el Mideo para divertirse conmigo.',
                'conseguirlo en',
                'Amigos',
                'seguidos'
            ]};
        var v = data[lang];
        for (var i = 0; i < v.length; i++) {
            $('.lang_'+i).html(v[i])
        }
        setCookie('lang',lang,10)

        var dom = $('.clang[data-lang="'+lang+'"]');
        $('.show_lang').html(dom.html());
        var download_lang = $('.download_lang');
        if (lang == "br") {
            document.title = "Mideo:Grave o melhor momento";
            download_lang.html("Veja os últimos vídeos, baixe o <span style='color:#ffff02'>Mideo</span> agora");
        } else if (lang == 'es') {
            document.title = "Mideo:Registrar el mejor momento";
            download_lang.html("Ver más vídeos , descarga ahora <span style='color:#ffff02'>Mideo</span>");
        } else {
            document.title = "Mideo:Record every moment";
            download_lang.html("Check more videos Download <span style='color:#ffff02'>Mideo</span> now");
        }
    }


    $(function(){
        $("#market").click(function(e) {
            ga("send", "event", "YaoQing", "NeiRong", "GooglePlay");
            var href = "https://play.google.com/store/apps/details?id=com.mideo";
            var userid = "<?php echo $userid; ?>";
            var marketHref = "market://details?id=com.mideo";
            var mideoHref = "mideo://main?userid=";
            if (userid) {
                href += "&referrer=" + userid;
                marketHref += "&referrer=" + userid;
                mideoHref += userid;
            }
            document.getElementById("l").src = mideoHref;
            setTimeout(function() {
                jump(href, marketHref, "");
            }, 500);
        });

        $(".c1").click(function(){
            ga("send", "event", "YaoQing", "DiBu", "Facebook");
            window.location.href="https://www.facebook.com/mideoshow";
        });

        $(".c2").click(function(){
            ga("send", "event", "YaoQing", "DiBu", "Twitter");
            window.location.href="https://twitter.com/mideotv";
        });
        $(".c3").click(function(){
            ga("send", "event", "YaoQing", "DiBu", "Instagram");
            window.location.href="https://www.instagram.com/mideo.tv/";
        });

        if(getLang()!='br'){
            changeLang(getLang())
        }

        $('.app_lang').click(function(){
            $('.clang').show();
        });

        $('.clang').click(function(event){
            $('.clang').hide();
            var c_lang=$(this).attr('data-lang');
            if(c_lang != getLang()){
                ga("send", "event", "YaoQing", "DingBu", "YuYan");
                changeLang(c_lang)
            }
            event.stopPropagation();
        });
        var video_thumbnail = $('.video_thumbnail');
        for(var i = 0; i < video_thumbnail.length; i ++){
            video_thumbnail.eq(i).find('.video_thumbnail_img').click(function() {
                ga("send", "event", "YaoQing", "DiBu", "ShiPin");
                var videoId = $(this).attr("data-id");
                if (videoId) {
                    jump(videoPage, appVideoUrl, "?id=" + videoId + "&country=" + country);
                } else {
                    jump(homePage, appMainUrl, window.location.search);
                }
            });
        }

        $('.btn_down').click(function(){
            ga("send", "event", "YaoQing", "DiBu", "XiaZai")
            /*window.location.href="https://play.google.com/store/apps/details?id=com.mideo&referrer=utm_source%3Dh5%26utm_medium%3DButton%26utm_term%3DDiBu%26utm_content%3DYaoQing";*/
            var href = "https://play.google.com/store/apps/details?id=com.mideo";
            var userid = "<?php echo $userid; ?>";
            var marketHref = "market://details?id=com.mideo";
            var mideoHref = "mideo://main?userid=";
            if (userid) {
                href += "&referrer=" + userid;
                marketHref += "&referrer=" + userid;
                mideoHref += userid;
            }
            document.getElementById("l").src = mideoHref;
            setTimeout(function() {
                jump(href, marketHref, "");
            }, 500);
        });
    });
    function jump(httpurl, appurl, link) { //判断app或者浏览器链接打开

        // Deep link to your app goes here
        document.getElementById("l").src = appurl + link;

        setTimeout(function() {
            // Link to the App Store should go here -- only fires if deep link fails
            window.location = httpurl + link;
        }, 500);
    }
</script>
</body>
</html>