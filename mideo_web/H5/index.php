<?php
    require_once('../auto/auto_lib.php');
    require_once('../Application/Lib/CryptDes.class.php');
    $db = new mysql();
    $sql = "SELECT id,thumb,userid,likecount FROM `video` WHERE status=1 order by id desc limit 0, 10";
    $videos = $db->findAllBySql($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>mideo</title>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=0,minimal-ui">
    <meta name="full-screen" content="yes">
    <meta name="x5-fullscreen" content="true">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="browsermode" content="application">
    <meta name="x5-page-mode" content="app">
    <link rel="stylesheet" href="./css/index.css">
</head>
<body>
    <div id="outer">
        <div class="top_mideo">
            <div class="mideo_logo"><div class="logo"></div><span>MIDEO</span></div>
            <div class="icon">
                <a href=""><div class="like"></div></a>
                <a href=""><div class="add_friends"></div></a>
            </div>
        </div>
        <div class="header">
            <div class="top_nav">
                <div class="item">
                    <div class="nav_title" id="title_hot"><span>Hot</span></div>
                    <div class="pro wrap_0 fade"></div>
                </div><div class="item"><!--这里必须不能有空格或回车，否则两个div之间会有空格；还有一种解决方法就是在父标签上设置font-size:0，不过这样父标签的高度就要根据子元素的大小来确定了-->
                    <div class="nav_title" id="title_friends"><span>Friends</span></div>
                    <div class="pro wrap_1"></div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="list fade list_0">
                <ul class="video_thumbnails">
                    
                </ul>
            </div>
            <div class="list list_1"></div>
        </div>

        <div class="footer">
            <div class="container">
                <a href=""><div class="icon_home"></div></a>
                <a href=""><div class="icon_find"></div></a>
                <a href=""><div class="icon_camera"></div></a>
                <a href=""><div class="icon_news"></div></a>
                <a href=""><div class="icon_center"></div></a>
            </div>
        </div>
    </div>
    <script src="./js/jquery-2.2.2.min.js"></script>
    <!--<script src="./js/slidepaging.js"></script>-->
    <!--<script src="./js/index.js"></script>-->
    <script>
        $(function(){
            /*//选择的nav
            var itemIndex = 0;
            //判断数据是否加载完
            var item1LoadEnd = false;
            var item2LoadEnd = false;
            //选择mideo还是friend
            $('.top_nav .item').on('click',function(){
                var $this = $(this);
                itemIndex = $this.index();
                $this.addClass('cur').siblings('.item').removeClass('cur');
                $('.list').eq(itemIndex).show().siblings('.list').hide();

                //如果选择热门
                if(itemIndex == '0'){
                    //如果数据没有加载完
                    if(!item1LoadEnd){
                        //解锁
                        slidepaging.unlock();
                        slidepaging.noData(false);
                    }else{
                        //锁定
                        slidepaging.lock('down');
                        slidepaging.noData();
                    }
                }else if(itemIndex == '1'){
                    if(!item2LoadEnd){
                        slidepaging.unlock();
                        slidepaging.noData(false);
                    }else{
                        slidepaging.lock('down');
                        slidepaging.noData();
                    }
                }
                slidepaging.resetload();
            });

            var counter = 0;
            var num = 10;//每页展示10条
            var pageStart = 0, pageEnd = 0;

            var dropload = $('.content').slidepaging({
                scrollArea : window,
                loadDownFn : function(para){
                    //加载热门的数据
                    _ajax();
                }
            });*/

            //滚动header到顶部之后固定
            /*var elem = $('.header');
            var startPos = $(elem).offset().top;
            $.event.add(window, "scroll", function(){
                var p = $(window).scrollTop();
                $(elem).css("position", ((p) > startPos) ? "fixed" : "static");
                $(elem).css("top", ((p) > startPos) ? "0px" : "");
            });*/
            //获取header距离顶部的距离
            var headerH = $(".header").offset().top;
            $(window).scroll(function(){
                //获取滚动条的滑动距离
                var scrollH = $(this).scrollTop();
                if(scrollH >= headerH){
                    $(".header").css({
                        "position" : "fixed", 
                        "top" : 0,
                        "z-index": 1
                    });
                    $(".content").css({
                        "padding-top" : "48px"
                    });
                }else if(scrollH < headerH){
                    $(".header").css({"position" : "static"});
                    $(".content").css({
                        "padding-top" : 0
                    });
                }
            });

            $('#title_hot').click(function(){
                //字体颜色变化
                $('#title_hot span').removeClass("changeColor");
                $('#title_friends span').addClass("changeColor");
                //导航条交替显示
                $('#title_hot').siblings('.pro').removeClass("fade");
                $('#title_friends').siblings('.pro').addClass("fade");
                //内容的交替显示
                $('.content .list_0').removeClass("fade");
                $('.content .list_1').addClass("fade");
            });
            $('#title_friends').click(function(){
                $('#title_friends span').removeClass("changeColor");
                $('#title_hot span').addClass("changeColor");
                $('#title_friends').siblings('.pro').removeClass("fade");
                $('#title_hot').siblings('.pro').addClass("fade");
                $('.content .list_1').removeClass("fade");
                $('.content .list_0').addClass("fade");
            });
        });
    </script>
</body>
</html>