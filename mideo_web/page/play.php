<?php
require_once('../auto/auto_lib.php');
require_once('../Application/Lib/CryptDes.class.php');
$des = new \Lib\CryptDes;
$db = new mysql();
//$videoid = '997e77a1436d5043';
$videoid = $_REQUEST['id'];
if(!$videoid){
    die('error');
}

$videoid = $des->decrypt($videoid);
if (!is_numeric($videoid)) {
    die('error');
}
$row = $db->findBySql("SELECT video.*,user.`username` FROM `video` JOIN `user` ON video.`userid` = user.`id` WHERE video.id={$videoid} ");
$tagRows = $db -> findAllBySql("SELECT topic.`id`, title FROM tag_relation JOIN topic ON topic.`id`=tag_relation.`tag_id` WHERE video_id={$videoid}");
$tagStr = "";
if ($tagRows != null && count($tagRows) > 0) {
    for ($i = 0; $i < count($tagRows); $i++) {
        $tagStr .= "#" . trim($tagRows[$i]["title"]) ." ";
    }
}
$country = strtolower($row['country']);

$des = "";
if($country == 'br') {
    $des = "Assista agora o vídeo compartilhado pelo usuário '{$row['username']}'";
} else if ($country == 'es') {
    $des = "Ver el vídeo compartida por el usuario ahora '{$row['username']}'";
} else {
    $des = "video is from {$row['username']}'s Mideo.";
}


$description = trim(removeEmoji($row['description']));
if (!empty($description)) {
    $description = "'" . $description . "' ";
}
$description .= "{$tagStr}{$des}";
$imgUrl = getHostInfo()."/uploads/".$row['thumb'];
$videoUrl = getHostInfo()."/uploads/".$row['filepath'];


$url =  getHostInfo().'/page/play.php?'.$_SERVER['QUERY_STRING'];

$db->close();
/**
 * 获取当前主机
 * @return string 主机字符串
 */
function getHostInfo(){
    if(!empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'],'off'))
        $http='https';
    else
        $http='http';
    if(isset($_SERVER['HTTP_HOST']))
        $hostInfo=$http.'://'.$_SERVER['HTTP_HOST'];
    else
    {
        $hostInfo=$http.'://'.$_SERVER['SERVER_NAME'];
        $port=isset($_SERVER['SERVER_PORT']) ? (int)$_SERVER['SERVER_PORT'] : 80;
        if($port!==80)
            $hostInfo.=':'.$port;
    }
    return $hostInfo;
}

function removeEmoji($str) {
    $pattern = '/(\[mideo\].*?\[\/mideo\])/i';
    $replacement = '';
    return preg_replace($pattern, $replacement, $str);
}

$title = "";
if($country == 'br') {
    $title = "Mideo:Grave o melhor momento";
} else if ($country == 'es') {
    $title = "Mideo:Registrar el mejor momento";
} else {
    $title = "Mideo:Record every moment";
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml"
                        xmlns:og="http://ogp.me/ns#"
                        xmlns:fb="https://www.facebook.com/2008/fbml">
<head>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=0,minimal-ui">
    <meta name="full-screen" content="yes">
    <meta name="x5-fullscreen" content="true">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="browsermode" content="application">
    <meta name="x5-page-mode" content="app">


    <meta charset="UTF-8">
    <title><?php echo $title; ?></title>
    <meta name="description" content="<?php echo $description; ?>">
    <meta name="keywords" content="<?php echo $title; ?>"/>

    <!-- 增加og相关标签属性，便于facebook抓取页面视频信息 start-->
    <meta content="video.other" property="og:type">

    <meta content="<?php echo $url; ?>" property="og:url">
    <meta content="<?php echo $title; ?>" property="og:title">
    <meta content="<?php echo $imgUrl; ?>" property="og:image">
    <meta content="<?php echo $description; ?>" property="og:description">
    <meta content="video/mp4" property="og:video:type">
    <meta content="<?php echo $videoUrl; ?>" property="og:video:url">
    <meta content="<?php echo $videoUrl; ?>" property="og:video:secure_url">
    <meta content="480" property="og:video:width">
    <meta content="480" property="og:video:height">
    <!-- 增加og相关标签属性，便于facebook抓取页面视频信息 end-->
    <meta property="fb:app_id" content="1669264926663691"/>
    <link rel="stylesheet" href="./css/main.css" />
    <script>
    window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)};ga.l=+new Date;
    ga('create', 'UA-77132610-2', 'auto');
    </script>
    <script async src='//www.google-analytics.com/analytics.js'></script>
</head>
<body>

<header>
    <div class="header_left">
        <div class="h-logo"></div>
        <p class="h-title">
        <?php if($country== 'br'): ?>
            Grave o melhor momento
        <?php elseif ($country == 'es'): ?>
            Registrar el mejor momento
        <?php else : ?>
            Record every moment
        <?php endif; ?>
        </p>
        <div class="h-go">
          <?php if ($country == 'br') : ?>
            Baixe agora
          <?php elseif ($country == 'es'): ?>
            descargar ahora
          <?php else : ?>
            Download Now
          <?php endif; ?>
        </div>
    </div>
</header>
<div class="video" style="margin-top: 3rem;">
    <video id="ga_video" poster="" src="" controls="controls"></video>
</div>
<div class="download">
    <a class="btn_down ga_middle">
    <?php if($country == 'br'): ?>
        Vídeos carregam melhor no app, baixe agora
    <?php elseif ($country == 'es'): ?>
        Videos llevan mejor en la aplicación,descarga ahora
    <?php else: ?>
        Video play Faster Download <span>Mideo</span> now
    <?php endif; ?>
    </a>
</div>

<div class="video_info">
    <div class="upload">
        <div class="upload_info">
            <a class="upload_head"><img src="./img/0.png" alt="" /></a>
            <div class="upload_name_time_frame">
                <div class="upload_name_time">
                    <p class="upload_name"></p>
                    <span class="upload_time"></span>
                </div>
            </div>
        </div>

        <div class="btn_follow_frame">
            <div class="btn_follow">
                <a>
                <!-- <?php if($country == 'br'): ?>
                    Seguir
                <?php elseif ($country == 'es'): ?>
                    seguir
                <?php else: ?>
                    follow
                <?php endif; ?> -->
                </a>
            </div>
        </div>

    </div>

    <div class="video_text">
        <div class="MainVideo_description">
            
        </div>
        <div class="video_tags">
            <?php for($i = 0; $i < count($tagRows); $i ++){ ?>
                <div class="tag"><a href="topic.php?topicid=<?php echo $tagRows[$i]["id"]; ?>"><?php echo "#" . trim($tagRows[$i]["title"]);?></a></div>
            <?php } ?>
        </div>
    </div>

    <div class="favo_talk_share">
        <div class="favo_box">
            <a class="favo"></a>
            <span class="favo0_number"></span>
        </div>
         <div class="talk_box">
            <a class="talk"></a>
            <span class="talk_number"></span>
        </div>
    </div>
</div>

<div class="content_tab">
    
    <div class="tab_header">
        <div class="tab_nav">
            <div class="item">
                <div class="tab_title" id="title_comments">
                    <?php if($country == 'br'): ?>
                        <span class="changeColor">Comente</span>
                    <?php elseif ($country == 'es'): ?>
                        <span class="changeColor">crítica</span>
                    <?php else: ?>
                        <span class="changeColor">Comments</span>
                    <?php endif; ?>
                </div>
                <div class="pro fade"></div>
            </div><div class="item">
                <div class="tab_title" id="title_recommend">
                    <?php if($country == 'br'): ?>
                        <span>Recomende</span>
                    <?php elseif ($country == 'es'): ?>
                        <span>recomendar</span>
                    <?php else: ?>
                        <span>Recommend</span>
                    <?php endif; ?>
                </div>
                <div class="pro"></div>
            </div>
        </div>
    </div>

    <div class="comments fade">
        <div class="comment_date">
            <span></span>
        </div>

        <div class="comments_more">
            <!-- <a>
                    <?php if($country == 'br'): ?>
                        mais
                    <?php elseif ($country == 'es'): ?>
                        más
                    <?php else: ?>
                        more
                    <?php endif; ?>
                    </a> -->
        </div>
    </div>

    <div class="video_topic_box_frame">
        <div class="video_box active">
            <ul class="video_thumbnails">
            </ul>
        </div>
    </div>
</div>

<div class="download download_bottom">
    <a class="btn_down ga_bottom">
        <span class="tab_download download_comments">
        <?php if($country == 'br'): ?>
            Veja mais comentários, baixe o <span style="color:#ffff02">Mideo</span> agora
        <?php elseif ($country == 'es'): ?>
            Ver más comentarios , descarga <span style="color:#ffff02">Mideo</span> ahora
        <?php else: ?>
            check more comments Download <span style="color:#ffff02">Mideo</span> now
        <?php endif; ?>
        </span>
        <span class="tab_download download_videos fade">
        <?php if($country == 'br'): ?>
            Veja os últimos vídeos, baixe o <span style="color:#ffff02">Mideo</span> agora
        <?php elseif ($country == 'es'): ?>
            Ver más vídeos , descarga ahora <span style="color:#ffff02">Mideo</span>
        <?php else: ?>
            check more videos Download <span style="color:#ffff02">Mideo</span> now
        <?php endif; ?>
        </span>
    </a>
</div>
<div class="share-dock">
    <a role="buttom" tabindex="1" title="Facebook" class="share-btn facebook-share"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 32 32" title="Facebook" alt="Facebook"><g><path d="M22 5.16c-.406-.054-1.806-.16-3.43-.16-3.4 0-5.733 1.825-5.733 5.17v2.882H9v3.913h3.837V27h4.604V16.965h3.823l.587-3.913h-4.41v-2.5c0-1.123.347-1.903 2.198-1.903H22V5.16z" fill-rule="evenodd"></path></g></svg></a><a role="buttom" tabindex="1" title="Whatsapp" class="share-btn whatsapp-share"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 32 32" title="WhatsApp" alt="WhatsApp"><g><path d="M19.11 17.205c-.372 0-1.088 1.39-1.518 1.39a.63.63 0 0 1-.315-.1c-.802-.402-1.504-.817-2.163-1.447-.545-.516-1.146-1.29-1.46-1.963a.426.426 0 0 1-.073-.215c0-.33.99-.945.99-1.49 0-.143-.73-2.09-.832-2.335-.143-.372-.214-.487-.6-.487-.187 0-.36-.043-.53-.043-.302 0-.53.115-.746.315-.688.645-1.032 1.318-1.06 2.264v.114c-.015.99.472 1.977 1.017 2.78 1.23 1.82 2.506 3.41 4.554 4.34.616.287 2.035.888 2.722.888.817 0 2.15-.515 2.478-1.318.13-.33.244-.73.244-1.088 0-.058 0-.144-.03-.215-.1-.172-2.434-1.39-2.678-1.39zm-2.908 7.593c-1.747 0-3.48-.53-4.942-1.49L7.793 24.41l1.132-3.337a8.955 8.955 0 0 1-1.72-5.272c0-4.955 4.04-8.995 8.997-8.995S25.2 10.845 25.2 15.8c0 4.958-4.04 8.998-8.998 8.998zm0-19.798c-5.96 0-10.8 4.842-10.8 10.8 0 1.964.53 3.898 1.546 5.574L5 27.176l5.974-1.92a10.807 10.807 0 0 0 16.03-9.455c0-5.958-4.842-10.8-10.802-10.8z" fill-rule="evenodd"></path></g></svg></a><a role="buttom" tabindex="1" title="Messager" class="share-btn messager-share"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 32 32" title="Facebook Messenger" alt="Facebook Messenger"><g><path d="M16 6C9.925 6 5 10.56 5 16.185c0 3.205 1.6 6.065 4.1 7.932V28l3.745-2.056c1 .277 2.058.426 3.155.426 6.075 0 11-4.56 11-10.185C27 10.56 22.075 6 16 6zm1.093 13.716l-2.8-2.988-5.467 2.988 6.013-6.383 2.868 2.988 5.398-2.987-6.013 6.383z" fill-rule="evenodd"></path></g></svg></a><a role="buttom" tabindex="1" title="Twitter" class="share-btn twitter-share"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 32 32" title="Twitter" alt="Twitter"><g><path d="M27.996 10.116c-.81.36-1.68.602-2.592.71a4.526 4.526 0 0 0 1.984-2.496 9.037 9.037 0 0 1-2.866 1.095 4.513 4.513 0 0 0-7.69 4.116 12.81 12.81 0 0 1-9.3-4.715 4.49 4.49 0 0 0-.612 2.27 4.51 4.51 0 0 0 2.008 3.755 4.495 4.495 0 0 1-2.044-.564v.057a4.515 4.515 0 0 0 3.62 4.425 4.52 4.52 0 0 1-2.04.077 4.517 4.517 0 0 0 4.217 3.134 9.055 9.055 0 0 1-5.604 1.93A9.18 9.18 0 0 1 6 23.85a12.773 12.773 0 0 0 6.918 2.027c8.3 0 12.84-6.876 12.84-12.84 0-.195-.005-.39-.014-.583a9.172 9.172 0 0 0 2.252-2.336" fill-rule="evenodd"></path></g></svg></a><a role="buttom" tabindex="1" title="Google+" class="share-btn googlePlus-share"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 32 32" title="Google+" alt="Google+"><g><path d="M12 15v2.4h3.97c-.16 1.03-1.2 3.02-3.97 3.02-2.39 0-4.34-1.98-4.34-4.42s1.95-4.42 4.34-4.42c1.36 0 2.27.58 2.79 1.08l1.9-1.83C15.47 9.69 13.89 9 12 9c-3.87 0-7 3.13-7 7s3.13 7 7 7c4.04 0 6.72-2.84 6.72-6.84 0-.46-.05-.81-.11-1.16H12zm15 0h-2v-2h-2v2h-2v2h2v2h2v-2h2v-2z" fill-rule="evenodd"></path></g></svg></a>
</div>

<script src="./js/jquery-2.2.2.min.js"></script>
<script src="./js/decode.js"></script>
<script src="./js/emoji.js"></script>
<script src="./js/video.js"></script>
<script src="./js/share.js"></script>
<!-- Go to www.addthis.com/dashboard to customize your tools 
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-5795ba0e26eaa44c"></script>-->
<iframe id="l" width="1" height="1" style="visibility:hidden"></iframe>
</body>
</html>
