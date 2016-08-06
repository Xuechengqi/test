var country = "BR";

var baseURL = "http://www.mideo.cc/api.php";
var appTopicUrl = "mideo://topic";
var appVideoUrl = "mideo://video";
var appMainUrl = "mideo://main";

var videoPage = "play.php";
var downloadPage = "download.php";
var topicPage = "topic.php";
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
    getData();
});

function initEvent() {
    var tempCountry = getUrlParameter("country");
    if (tempCountry) {
        country = tempCountry;
    }
    $('.btn_down').click(function(e) {
        googlePlay_detail += "3DDiBuXiaZai%26utm_campaign%3DGuanWangXQ";
        ga("send", "event", "GuanWangXQ", "DiBu", "XiaZai");
        jump(googlePlay_detail, appTopicUrl, window.location.search);
    });

    $('.google').click(function(e) {
        googlePlay_detail += "3DGooglePlay%26utm_campaign%3DGuanWangXQ";
        ga("send", "event", "GuanWangXQ", "ShangBu", "GooglePlay");
        jump(googlePlay_detail, appTopicUrl, window.location.search);
    });
    $('.app-download').click(function(e) {
        ga("send", "event", "GuanWangXQ", "ShangBu","APK");
        var href = "../index/android_mideo.apk";
        window.location.href = href;
    });
    $(".fb").click(function(){
      ga("send", "event", "GuanWangXQ", "ShangBu","Facebook");
      window.location.href="https://www.facebook.com/mideoshow";
    });
    $(".tw").click(function(){
        ga("send", "event", "GuanWangXQ", "ShangBu", "Twitter");
        window.location.href="https://twitter.com/mideotv";
    })
}

function getData() {
    var hot_jp = baseURL + "/index/hot_jp/country/" + country;
    _ajax(hot_jp, fillvideothumbnails, "AppDownloadData");
}

function fillvideothumbnails(json) { //视频缩略(用于content_share,topic_share,app_download)
    var a_d_linum = json.data.length,
    json_data = json.data;
    for (i = 0; i < a_d_linum; i++) {
        var thumbnail = $('<li class="video_thumbnail"><div class="video_item"><a class="video_thumbnail_img"><img src="" alt="" /></a></div><div class="video_item_info"><a class="upload_head_thumbnail"><img src="./img/0.png" alt="" /></a><a class="upload_name_thumbnail"></a><span class="favo_number"><img src="./img/like.png" alt="" /><span></span></span></div></li>');
        var tb_container = $('.video_thumbnails');

        tb_container.append(thumbnail);
        var video_thumbnail = $('.video_thumbnail');
        if (json_data[i].avatar.length != 0) {
            video_thumbnail.eq(i).find('.upload_head_thumbnail img').attr('src', json_data[i].avatar);
        }
        video_thumbnail.eq(i).find('.video_thumbnail_img').attr('data-id', json_data[i].id);
        video_thumbnail.eq(i).find('.video_thumbnail_img img').attr('src', json_data[i].thumb);
        video_thumbnail.eq(i).find('.upload_name_thumbnail').html(json_data[i].username);
        video_thumbnail.eq(i).find('.favo_number span').html(json_data[i].likecount);

        video_thumbnail.eq(i).find('.video_thumbnail_img').click(function() {
            ga("send", "event", "GuanWangXQ", "XiaBu", "FengMian");
            var videoId = $(this).attr("data-id");
            if (videoId) {
                jump(videoPage, appVideoUrl, "?id=" + videoId + "&country=" + country);
            } else {
                jump(downloadPage, appMainUrl, window.location.search);
            }
        });
        video_thumbnail.eq(i).find('.upload_head_thumbnail').click(function() {
            jump(downloadPage, appMainUrl, window.location.search);
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
