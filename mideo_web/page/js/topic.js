var topicId = 0;
var topicTitle = "";
var topicContent = "";
var topicImageUrl = "";
var country = "BR";

var baseURL = "http://www.mideo.cc/api.php/topic/";
//var baseURL = "http://47.89.45.68/api.php/topic/";
var appTopicUrl = "mideo://topic";
var appVideoUrl = "mideo://video";
var appMainUrl = "mideo://main";

var videoPage = "play.php";
var topicPage = "topic.php";
var addPage = "../add.php";
var homePage = "../index.html"

var googlePlay_detail = "https://play.google.com/store/apps/details?id=com.mideo&referrer=utm_source%3Dhtml5%26utm_medium%3Dclick%26utm_term%3Dbanner%26utm_content%";

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
    if (topicId > 0) {
        getData();
    }
});

function initEvent() {
    topicId = getUrlParameter("topicid");
    if (topicId) {
        topicId = parseInt(topicId, 10);
    }
    var tempCountry = getUrlParameter("country");
    if (tempCountry) {
        country = tempCountry;
    }
    $('.header_left').click(function(e) {
        googlePlay_detail += "3DDingBuBanner%26utm_campaign%3DHuaTiXQ";
        ga("send", "event", "HuaTiXQ", "DingBu", "Banner");
        jump(googlePlay_detail, appTopicUrl, window.location.search);
    });
    $('.topic_banner').click(function(e) {
        ga("send", "evnet", "HuaTiXQ", "DingBu", "FengMian");
        jump(homePage, appTopicUrl, window.location.search);
    });
    $('.btn_down').click(function(e) {
        googlePlay_detail += "3DDiBuXiaZai%26utm_campaign%3DHuaTiXQ";
        ga("send", "event", "HuaTiXQ", "DiBu", "XiaZai");
        jump(googlePlay_detail, appTopicUrl, window.location.search);
    });

    $('.video_switch').click(function() {
        ga("send", "event", "HuaTiXQ", "NeiRong", "ZuiRe");
        $(this).addClass('switch_active');
        $('.topic_switch').removeClass('switch_active');
        $('.video_box').addClass('active');
        $('.topic_box').removeClass('active');
        //底部下载按钮的切换
        $('.download_videos').removeClass("fade");
        $('.download_topics').addClass("fade");
    });
    $('.topic_switch').click(function() {
        ga("send", "event", "HuaTiXQ", "NeiRong", "HuaTi");
        $(this).addClass('switch_active');
        $('.video_switch').removeClass('switch_active');
        $('.topic_box').addClass('active');
        $('.video_box').removeClass('active');
        //底部下载按钮的切换
        $('.download_topics').removeClass("fade");
        $('.download_videos').addClass("fade");
    });
}

function buildTopicParams() {
    return "?topic_id=" + topicId +
           "&topic_title=" + encodeURIComponent(topicTitle) +
           "&topic_content" + encodeURIComponent(topicContent) +
           "&topic_image_url=" + encodeURIComponent(topicImageUrl);
}

function getData() {
    var gettopic_jp = baseURL + "gettopic_jp/topicid/" + topicId + "/country=" + country,
    getusers_jp = baseURL + "getusers_jp/topicid/" + topicId + "/country=" + country,
    gethotvideos_jp = baseURL + "gethotvideos_jp/topicid/" + topicId + "/country=" + country,
    gettopics_jp = baseURL + "gettopics_jp/country/" + country;

    _ajax(gettopic_jp, filltopicbanner); //获取topic_banner
    _ajax(getusers_jp, filljoinhead); //获取参与人数
    _ajax(gethotvideos_jp, fillvideothumbnails); //获取热门视频
    _ajax(gettopics_jp, filltopicitem); //获取话题栏目信息
}

function filltopicbanner(json) { //话题banner(用于topic_share)
    var a_d_linum = json.data.length,
    json_data = json.data;
    $('.topic_banner a img').attr('src', json_data.picture);
    $('.topic_banner a p').html(json_data.title);
    topicTitle = json_data.title;
    topicContent = json_data.content;
    topicImageUrl = json_data.picture;
}
function filljoinhead(json) { //参与头像(用于topic_share)
    var a_d_linum = json.data.users.length,
    json_data = json.data;
    if (json_data.users.length > 0) {
        for (i = 0; i < a_d_linum; i++) {
            var jh = $('<li class="join_head"><a><img src="./img/0.png" alt="" /></a></li>');
            var jhb = $('.join_head_box ul');
            var $join_head = $('.join_head a'),
            $join_number = $('.join_number_value');
            jhb.append(jh);
            if (json.data.users[i].avatar.length != 0) {
                $('.join_head a img').eq(i).attr('src', json.data.users[i].avatar)
            }
        }

        $('.join_number_val').html(a_d_linum = json.data.users.length);
        $('.join_box').click(function(){
            ga("send", "event", "HuaTiXQ", "CanYuRen", "TouXiang");
            var to = window.location.search + "&from=participantE";
            jump(homePage, appMainUrl, to);
        });
        /*$('.join_head a').click(function() {
            ga("send", "event", "HuaTiXQ", "CanYuRen", "TouXiang");
            jump(homePage, appMainUrl, window.location.search);
        });*/
        /*$('.join_number_value').click(function() {
            jump(homePage, appMainUrl, window.location.search);
        })*/
    } else {
        $('.join_number_val').html(0);
    }
}
function filltopicitem(json) { //hot topics栏(用于topic_share)
    var a_d_linum = json.data.length,
    json_data = json.data;
    var $_topic_item = $('.topic_item');
    for (i = 0; i < a_d_linum; i++) {
        var ti = $('<li class="topic_item"><a><img src="" alt="" /><p class="topic_item_title"></p></a></li>');
        var tb = $('.topic_box ul');
        tb.append(ti);
        $('.topic_item').eq(i).attr("data-id", json_data[i].id);
        $('.topic_item').eq(i).find('img').attr('src', json_data[i].picture);
        $('.topic_item').eq(i).find('p.topic_item_title').html(json_data[i].title);
        $('.topic_item').eq(i).click(function() {
            ga("send", "event", "HuaTiXQ", "NeiRong", "HuaTi", "FengMian");
            var topicId = $(this).attr("data-id");
            if (topicId) {
                jump(topicPage + "?", appTopicUrl + buildTopicParams(), "topicid=" + topicId + "&country=" + country);
            } else {
                jump(homePage, appMainUrl, window.location.search);
            }
        })
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
            ga("send", "event", "HuaTiXQ", "NeiRong", "ShiPin", "FengMian");
            var videoId = $(this).attr("data-id");
            if (videoId) {
                jump(videoPage, appVideoUrl, "?id=" + videoId + "&country=" + country);
            } else {
                jump(homePage, appMainUrl, window.location.search);
            }
        })
        video_thumbnail.eq(i).find('.upload_head_thumbnail').click(function(e) {
            var to = window.location.search + "&from=userE";
            var userid = $(e.target).attr('data-userid');
            if (userid) {
                jump(addPage, appMainUrl, "?userid="+userid);
            } else {
                jump(homePage, appMainUrl, to);
            }
        })
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