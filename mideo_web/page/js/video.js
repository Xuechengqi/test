var videoId = "";
var country = "BR";

var baseURL = "http://www.mideo.cc/api.php";
//var baseURL = "http://47.90.23.110/api.php";
var appTopicUrl = "mideo://topic";
var appVideoUrl = "mideo://video";
var appMainUrl = "mideo://main";


var videoPage = "play.php";
var topicPage = "topic.php";
var addPage = "../add.php";
var homePage = "../index.html";

var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? "" : sParameterName[1];
        }
    }
};

$(function() {
    initEvent();
    if (videoId) {
        getData();
    }
});

function initEvent() {
    videoId = getUrlParameter("id");
    if (videoId) {
        videoId = videoId;
    }
    var tempCountry = getUrlParameter("country");
    if (tempCountry) {
        country = tempCountry;
    }

    $('#ga_video').bind('play', function () {
        ga("send", "event", "ShiPinXQ", "BoFangQi", "BoFang");
        $('#ga_video').removeAttr('poster');
    });

    $('#ga_video').bind('pause', function () {
        ga("send", "event", "ShiPinXQ", "BoFangQi", "ZanTing");
    });

    /*$('header').click(function(e) {
        ga("send", "event", "GuanWang", "GooglePlay");
        var href = "https://play.google.com/store/apps/details?id=com.mideo&referrer=utm_source%3Dhtml5%26utm_medium%3Dclick%26utm_term%3Dbanner%26utm_content%3DDingBuBanner%26utm_campaign%3DShiPinXQ";
        window.location.href = href;
    });*/

    $('.header_left,.header_right,.btn_down').click(function(e) {
        var className = e.target.className;
        var googlePlay_detail = 'https://play.google.com/store/apps/details?id=com.mideo&referrer=utm_source%3Dhtml5%26utm_medium%3Dclick%26utm_term%3Dbanner%26utm_content%3D';
        if (className.indexOf("btn_down") !== -1) {
            if (className.indexOf("ga_bottom") !== -1) {
                /*ga("send", "event", "ShiPinXQ", "DiBu", "XiaZai");*/
                var fadeButton = className.getElementsByClassName('fade');
                if(fadeButton.indexOf("download_comments") !== -1){
                    ga("send", "event", "ShiPinXQ", "DiBu", "PingLunXZ");
                }else{
                    ga("send", "event", "ShiPinXQ", "DiBu", "ShiPinXZ");
                }
                googlePlay_detail += "DiBuXiaZai";
            } else {
                ga("send", "event", "ShiPinXQ", "BoFangQi", "JiaSu");
                googlePlay_detail += "JiaSu";
            }
        } else {
            ga("send", "event", "ShiPinXQ", "DingBu", "Banner");
            googlePlay_detail += "DingBuBanner";
        }
        googlePlay_detail += "%26utm_campaign%3DShiPinXQ";
        //jump("https://play.google.com/store/apps/details?id=com.mideo&referrer=utm_source%3Dvideoshare%26utm_medium%3Dbanner%26utm_campaign%3D%25E4%25BA%25A7%25E5%2593%2581", "market://details?id=com.mideo", "");
        jump(googlePlay_detail, appVideoUrl, window.location.search);
    });

    $('.video_switch').click(function() {
        $(this).addClass('switch_active');
        $('.topic_switch').removeClass('switch_active');
        $('.video_box').addClass('active');
        $('.topic_box').removeClass('active');
    });
    $('.topic_switch').click(function() {
        $(this).addClass('switch_active');
        $('.video_switch').removeClass('switch_active');
        $('.topic_box').addClass('active');
        $('.video_box').removeClass('active');
    });
    $('#title_comments').click(function(){
        //字体颜色变化
        $('#title_comments span').removeClass("changeColor");
        $('#title_recommend span').addClass("changeColor");
        //导航条交替显示
        $('#title_comments').siblings('.pro').removeClass("fade");
        $('#title_recommend').siblings('.pro').addClass("fade");
        //内容的交替显示
        $('.comments').removeClass("fade");
        $('.video_topic_box_frame').addClass("fade");
        //底部下载按钮的切换
        $('.download_comments').removeClass("fade");
        $('.download_videos').addClass("fade");
    });
    $('#title_recommend').click(function(){
        //字体颜色变化
        $('#title_recommend span').removeClass("changeColor");
        $('#title_comments span').addClass("changeColor");
        //导航条交替显示
        $('#title_recommend').siblings('.pro').removeClass("fade");
        $('#title_comments').siblings('.pro').addClass("fade");
        //内容的交替显示
        $('.video_topic_box_frame').removeClass("fade");
        $('.comments').addClass("fade");
        $('.download_videos').removeClass("fade");
        $('.download_comments').addClass("fade");
    });
    $('.tag').click(function(){
        ga("send", "event", "ShiPinXQ", "YongHu", "BiaoQian");
    });
}

function changeEmoji(object){
    var descri = object;
    var emojis = descri.match(/(\[mideo\].*?\[\/mideo\])/g);
    if(emojis != null){
        for (var j = 0; j < emojis.length; j++) {
            var emojiStr = emojis[j];
            var emojiItem = emojiStr.replace('[mideo]', '');
            emojiItem = emojiItem.replace('[/mideo]', '');
            var emoji = new EmojiConvertor();
            if (emoji.emoticons_big_icon[emojiItem]) {
                img_name = emojiItem.replace('/','');
                descri = descri.replace(emojiStr, '<img src="/page/img/emoji/emoji_' + img_name + '.png" class="bigEmoji" style="width:2.4rem">');
            }else {
                var emojiCode = b64_to_utf8(emojiItem);
                emoji.img_sets.mideo.path += 'emoji_';
                var output = emoji.replace_unified(emojiCode);
                descri = descri.replace(emojiStr,output);
            }
        }
    }
    return descri;
}

function getData() {
    var get_jp = baseURL + "/video/get_jp/id/" + videoId + "/country/" + country;
    var getcomments_jp = baseURL + "/video/getcomments_jp/id/" + videoId + "/country/" + country;
    var hot_jp = baseURL + "/index/hot_jp/country/" + country;

    _ajax(get_jp, fillvideoinfo); //获取视频及相关信息
    _ajax(getcomments_jp, fillcomments); //获取评论
    _ajax(hot_jp, fillvideothumbnails); //获取热门视频
}


function fillvideoinfo(json) { //视频详情(用于content_share)
    var json_data = json.data;
    var video = $('.video video'),
    descriptionNode = $('.MainVideo_description'),
    upload_head = $('.upload_head'),
    upload_name = $('.upload_name'),
    upload_time = $('.upload_time'),
    favo0_number = $('.favo0_number'),
    talk_number = $('.talk_number'),
    share_number = $('.share_number'),
    btn_follow = $('.btn_follow'),
    favo = $('.favo'),
    talk = $('.talk'),
    share = $('.share');
    video.attr({
        poster: json_data.thumb,
        src: json_data.filepath
    });
    video.attr({
        poster: json_data.picture,
        src: json_data.video
    });

    if (json_data.description) {
        descriptionNode.html(changeEmoji(json_data.description));
    }

    if (json_data.avatar.length != 0) {
        upload_head.find("img").attr('src', json_data.avatar);
    }
    var cmt_tstring = new Date(json_data.createtime * 1000),
    cmt_month = cmt_tstring.getMonth() + 1,
    cmt_date = cmt_tstring.getDate();
    cmt_year = cmt_tstring.getFullYear();
    upload_name.html(json_data.username);
    upload_time.html(cmt_year + '-' + cmt_month + '-' + cmt_date);
    favo0_number.html(json_data.likecount);
    talk_number.html(json_data.commentcount);
    upload_head.click(function() {
        ga("send", "event", "ShiPinXQ", "YongHu", "TouXiang");
        var to = window.location.search + "&from=userE";
        if(json_data.userid){
            jump(addPage, appMainUrl, "?userid="+json_data.userid);
        }else{
            jump(homePage, appMainUrl, to);
        }
    });
    upload_name.click(function() {
        ga("send", "event", "ShiPinXQ", "YongHu", "MingZi");
        var to = window.location.search + "&from=userE";
        jump(homePage, appMainUrl, to);
    });
    btn_follow.click(function() {
        ga("send", "event", "ShiPinXQ", "YongHu", "GuanZhuButton");
        var to = window.location.search + "&from=userE";
        jump(homePage, appMainUrl, to);
    });
    favo.click(function() {
        ga("send", "event", "ShiPinXQ", "YongHu", "XiHuanButton");
        var to = window.location.search + "&from=heartE";
        jump(homePage, appVideoUrl, to);
    });
    talk.click(function() {
        ga("send", "event", "ShiPinXQ", "YongHu", "PingLunButton");
        var to = window.location.search + "&from=replyE";
        jump(homePage, appVideoUrl, to);
    });
    /*share.click(function() {
        ga("send", "event", "ShiPinXQ", "YongHu", "FenXiangButton");
        jump(homePage, appVideoUrl, window.location.search);
    });*/
}

function fillcomments(json) { //视频评论(用于content_share)
    var cmt_num = json.data.length,
    json_data = json.data;
    if (cmt_num > 0) {
        var cmt_tstring = new Date(json_data[0].createtime * 1000),
        cmt_hour = cmt_tstring.getHours(),
        cmt_min = cmt_tstring.getMinutes(),
        cmt_month = cmt_tstring.getMonth() + 1,
        cmt_date = cmt_tstring.getDate(),
        $cmter = $('.commenter_head');
        $('.comment_date span').html(cmt_month + ' / ' + cmt_date);
        var $comment_box = $('.comment_box');
        for (i = 0; i < cmt_num; i++) {
            var cmt = $('<div class="comment_box"><div class="commenter_info"><a class="commenter_head"><img class=" commenter_' + i +'" src="./img/0.png" alt="" /></a><span class="comment_time"></span></div><div class="comment_content"><p class="commenter_name"></p><div class="comment_context"><div class="comment_frame_tri"></div><p class="comment"></p></div></div></div>');
            var $cmt_more = $('.comments_more');
            cmt_tstring = new Date(json_data[i].createtime * 1000);
            cmt_hour = cmt_tstring.getHours(),
            cmt_min = cmt_tstring.getMinutes();
            $('.comments_more').before(cmt);
            if (json_data[i].avatar.length > 0) {
                $('.comment_box').eq(i).find('.commenter_head img').attr('src', json_data[i].avatar);
            }
            $('.comment_box').eq(i).find('.comment_time').html(cmt_hour + ":" + cmt_min);
            $('.comment_box').eq(i).find('.commenter_name').html(json_data[i].username);
            var content = json_data[i].content;
            //Que isso vamos mata de vergunha nos munher manda bonito ai.[mideo]8J+YmA==[/mideo]
            //content = content.replace(/(\[mideo\].*?\[\/mideo\])/g,"");
            var emojis = content.match(/(\[mideo\].*?\[\/mideo\])/g);
            if(emojis != null){
                for (var j = 0; j < emojis.length; j++) {
                    var emojiStr = emojis[j];
                    var emojiItem = emojiStr.replace('[mideo]', '');
                    emojiItem = emojiItem.replace('[/mideo]', '');
                    var emoji = new EmojiConvertor();
                    if (emoji.emoticons_big_icon[emojiItem]) {
                        img_name = emojiItem.replace('/','');
                        content = content.replace(emojiStr, '<img src="/page/img/emoji/emoji_' + img_name + '.png" class="bigEmoji" style="width:2.4rem">');
                    }else {
                        var emojiCode = b64_to_utf8(emojiItem);
                        emoji.img_sets.mideo.path += 'emoji_';
                        var output = emoji.replace_unified(emojiCode);
                        content = content.replace(emojiStr,output);
                    }
                }
            }
            if (!content.replace(/ /g, "")) {
                cmt.hide();
                continue;
            }
            $('.comment_box').eq(i).find('.comment').html(content);
            $('.comment').click(function(){
                ga("send", "event", "ShiPinXQ", "PingLun", "NeiRong");
                var to = window.location.search + "&from=commentE";
                jump(homePage,appMainUrl,to);
            });
            $('.commenter_head').click(function(e) {
                ga("send", "event", "ShiPinXQ", "PingLun", "TouXiang");
                var to = window.location.search + "&from=userE";
                var className = e.target.className;
                var j = 0;
                while(className.indexOf("commenter_"+j) === -1){
                    j ++;
                }
                if(className.indexOf("commenter_"+j) !== -1){
                    if(json_data[j].userid){
                        jump(addPage, appMainUrl, "?userid="+json_data[j].userid);
                    }else{
                        jump(homePage, appMainUrl, to);
                    }
                }
            });
        }
        /*$('.comments_more').click(function() {
            ga("send", "event", "ShiPinXQ", "PingLun", "GengDuoButton");
            jump(homePage, appMainUrl, window.location.search);
        })*/
    }

    else {
        $('.comments').hide();
    }
}

function fillvideothumbnails(json) { //视频缩略(用于content_share,topic_share,app_download)
    var a_d_linum = json.data.length,
    json_data = json.data;
    for (i = 0; i < a_d_linum; i++) {
        var thumbnail = $('<li class="video_thumbnail"><div class="video_item"><a class="video_thumbnail_img"><img src="" alt="" /></a></div><div class="video_item_info"><a class="upload_head_thumbnail"><img src="./img/0.png" alt="" /></a><a class="upload_name_thumbnail"></a><span class="favo_number"><img src="./img/like.png" alt="" /><span></span></span></div><div class="video_description"></div></li>');
        var tb_container = $('.video_thumbnails');

        tb_container.append(thumbnail);
        var video_thumbnail = $('.video_thumbnail');
        if (json_data[i].avatar.length != 0) {
            video_thumbnail.eq(i).find('.upload_head_thumbnail img').attr({
                'src': json_data[i].avatar,
                'data-userid': json_data[i].userid
            });
        }
        video_thumbnail.eq(i).find('.video_thumbnail_img').attr('data-id', json_data[i].id);
        video_thumbnail.eq(i).find('.video_thumbnail_img img').attr('src', json_data[i].thumb);
        video_thumbnail.eq(i).find('.upload_name_thumbnail').html(json_data[i].username);
        video_thumbnail.eq(i).find('.favo_number span').html(json_data[i].likecount);

        var descri = json_data[i].description;
        var emojis = descri.match(/(\[mideo\].*?\[\/mideo\])/g);
        if(emojis != null){
            for (var j = 0; j < emojis.length; j++) {
                var emojiStr = emojis[j];
                var emojiItem = emojiStr.replace('[mideo]', '');
                emojiItem = emojiItem.replace('[/mideo]', '');
                var emoji = new EmojiConvertor();
                if (emoji.emoticons_big_icon[emojiItem]) {
                    img_name = emojiItem.replace('/','');
                    descri = descri.replace(emojiStr, '<img src="/page/img/emoji/emoji_' + img_name + '.png" class="bigEmoji" style="width:2.4rem">');
                }else {
                    var emojiCode = b64_to_utf8(emojiItem);
                    emoji.img_sets.mideo.path += 'emoji_';
                    var output = emoji.replace_unified(emojiCode);
                    descri = descri.replace(emojiStr,output);
                }
            }
        }
        video_thumbnail.eq(i).find('.video_description').html(descri);

        video_thumbnail.eq(i).find('.video_thumbnail_img').click(function() {
            ga("send", "event", "ShiPinXQ", "ShiPinLieBiao", "FengMian");
            var videoId = $(this).attr("data-id");
            if (videoId) {
                jump(videoPage, appVideoUrl, "?id=" + videoId + "&country=" + country);
            } else {
                jump(homePage, appMainUrl, window.location.search);
            }
        });
        video_thumbnail.eq(i).find('.upload_head_thumbnail').click(function(e) {
            ga("send", "event", "ShiPinXQ", "ShiPinLieBiao", "TouXiang");
            var to = window.location.search + "&from=userE";
            var userid = $(e.target).attr('data-userid');
            if (userid) {
                jump(addPage, appMainUrl, "?userid="+userid);
            } else {
                jump(homePage, appMainUrl, to);
            }
        });
    }
}

function _ajax(url, func) {
    $.ajax({
        type: "get",
        async: true,
        url: url,
        dataType: "jsonp",
        jsonp: "cb",
        success: function(json) {
            func(json);
        },
        error: function() {
            console.log('fail!!!', url);
        }
    });
}

function jump(httpurl, appurl, link) { //判断app或者浏览器链接打开

        // Deep link to your app goes here
        document.getElementById("l").src = appurl + link;

        setTimeout(function() {
            // Link to the App Store should go here -- only fires if deep link fails
            window.location = httpurl + link;
        }, 500);
}
