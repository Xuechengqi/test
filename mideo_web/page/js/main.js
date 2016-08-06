
var baseURL = "http://www.mideo.cc";

function mediaquery() {
    var $w_width = $(document).width();
    $('html').css('font-size', ($w_width / 360 * 125).toFixed(5) + '%');
}
function jump(httpurl, appurl, link) { //判断app或者浏览器链接打开
    if (navigator.userAgent.match(/(iPhone|iPod|iPad);?/i)) {
        var loadDateTime = new Date();
        window.setTimeout(function() {
            var timeOutDateTime = new Date();
            if (timeOutDateTime - loadDateTime < 5000) {
                window.location = httpurl + link;
            } else {
                // window.close();
            }
        },
        25);
        window.location = " apps custom url schemes ";
    } else if (navigator.userAgent.match(/android/i)) {
        var state = null;
        try {
            state = window.open(appurl + link, '_blank');
        } catch(e) {}
        if (state) {
            // window.close();
        } else {
            window.location = httpurl + link;
        }
    } else {
        window.location = httpurl + link;
    }
}

$(function() {
    init();
    mediaquery();
    selectajax();
    getparam(getURLref());
})
 $(window).resize(function() {
    mediaquery();
})

function init() {
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
}
function getURLref() {
    var url = window.location.search; //获取"?"后的字符串(带？)
    if (url.indexOf("?") != -1) { //如果存在
        var str = url.substr(1); //截取？后的字符串
        strs = str.split("&"); //用&区分
        var key = new Array(strs.length); //主键数
        var value = new Array(strs.length); //值数
        for (i = 0; i < strs.length; i++) {
            key[i] = strs[i].split("=")[0] //获取键的值
            value[i] = unescape(strs[i].split("=")[1]); //获取值得值
        }
        var datas = new Array(key, value);
        return datas;
    }
}
function _ajax(url, func) {
    $.ajax({
        type: "get",
        async: true,
        url: url,
        dataType: "jsonp",
        jsonp: "cb",
        //传递给请求处理程序或页面的，用以获得jsonp回调函数名的参数名(一般默认为:callback)
        // jsonpCallback:callback,//自定义的jsonp回调函数名称，默认为jQuery自动生成的随机函数名，也可以写"?"，jQuery会自动为你处理数据
        success: function(json) {
            func(json);
        },
        error: function() {
            console.log('fail!!!', url);
        }
    });
}
var a = getURLref();
var index_vd_thumb = getparam(a)[0],
//country
index_vd_cmt = getparam(a)[1],
//id
index_tpc = getparam(a)[2]; //topicid
function _cb(json) {
    console.log(json);
}
function getparam(a) {
    var vd_thumb = a[0].indexOf('country'),
    //hot videos
    vd_cmt = a[0].indexOf('id'),
    //
    tpc = a[0].indexOf('topicid');
    var theKeys = [vd_thumb, vd_cmt, tpc];
    return theKeys;
}
function selectajax() {
    if (window.location.href.indexOf('topic_share') != -1) {
        getTopicShareData()
    }
    if (window.location.href.indexOf('content_share') != -1) {
        getContentShareData()
    }
    if (window.location.href.indexOf('app_download') != -1) {
        getAppDownloadData()
    }
}

function getTopicShareData() { //话题页
    var gettopic_jp_topic = baseURL + "/api.php/topic/gettopic_jp/" + getURLref()[0][index_tpc] + "/" + getURLref()[1][index_tpc],
    getusers_jp = baseURL + "/api.php/topic/getusers_jp/" + getURLref()[0][index_tpc] + "/" + getURLref()[1][index_tpc],
    gethotvideos_jp = baseURL + "/api.php/topic/gethotvideos_jp/" + getURLref()[0][index_tpc] + "/" + getURLref()[1][index_tpc],
    gettopic_jp_thumb = baseURL + "/api.php/topic/gettopics_jp/" + getURLref()[0][index_vd_thumb] + "/" + getURLref()[1][index_vd_thumb];

    _ajax(gettopic_jp_topic, filltopicbanner, "TopicShareData1"); //获取topic_banner
    _ajax(getusers_jp, filljoinhead, "TopicShareData2"); //获取参与人数
    _ajax(gethotvideos_jp, fillvideothumbnails, "TopicShareData3"); //获取热门视频
    _ajax(gettopic_jp_thumb, filltopicitem, "TopicShareData4"); //获取话题栏目信息
};
function getContentShareData() { //视频页
    var get_jp = baseURL + "/api.php/video/get_jp/" + getURLref()[0][index_vd_cmt] + "/" + getURLref()[1][index_vd_cmt],
    getcomments_jp_cmt = baseURL + "/api.php/video/getcomments_jp/" + getURLref()[0][index_vd_cmt] + "/" + getURLref()[1][index_vd_cmt],
    hot_jp = baseURL + "/api.php/index/hot_jp/" + getURLref()[0][index_vd_thumb] + "/" + getURLref()[1][index_vd_thumb];
    _ajax(get_jp, fillvideoinfo, "ContentShareData1"); //获取视频及相关信息
    _ajax(getcomments_jp_cmt, fillcomments, "ContentShareData2"); //获取评论
    _ajax(hot_jp, fillvideothumbnails, "ContentShareData3"); //获取热门视频
};
function getAppDownloadData() { //app下载页
    var hot_jp = baseURL + "/api.php/index/hot_jp/" + getURLref()[0][index_vd_thumb] + "/" + getURLref()[1][index_vd_thumb];
    _ajax(hot_jp, fillvideothumbnails, "AppDownloadData");

};

//填充页面信息
function fillvideothumbnails(json) { //视频缩略(用于content_share,topic_share,app_download)
    var a_d_linum = json.data.length,
    json_data = json.data;
    for (i = 0; i < a_d_linum; i++) {
        var thumbnail = $('<li class="video_thumbnail"><div class="video_item"><a href="" class="video_thumbnail_img"><img src="" alt="" /></a></div><div class="video_item_info"><a href="" class="upload_head_thumbnail"><img src="./img/0.png" alt="" /></a><a href="" class="upload_name_thumbnail"></a><span class="favo_number"><img src="./img/like.png" alt="" /><span></span></span><p class="video_description"></p></div></li>');
        var tb_container = $('.video_thumbnails');

        tb_container.append(thumbnail);
        var video_thumbnail = $('.video_thumbnail');
        if (json_data[i].avatar.length != 0) {
            video_thumbnail.eq(i).find('.upload_head_thumbnail img').attr('src', json_data[i].avatar);
        }
        video_thumbnail.eq(i).find('.video_thumbnail_img img').attr('src', json_data[i].thumb);
        video_thumbnail.eq(i).find('.upload_name_thumbnail').html(json_data[i].username);
        video_thumbnail.eq(i).find('.favo_number span').html(json_data[i].likecount);
        video_thumbnail.eq(i).find('.video_description').html(json_data[i].description);

        video_thumbnail.eq(i).find('.video_thumbnail_img').click(function() {
            // jump(json_data[i].videosrc)//填充链接参数（还没有参数!) 视频详情
        })
        video_thumbnail.eq(i).find('.upload_head_thumbnail').click(function() {
            // jump(json_data[i].uploader)//填充链接参数（还没有参数!) 上传者主页
        })
    }
}
function fillvideoinfo(json) { //视频详情(用于content_share)
    var json_data = json.data;
    var video = $('.video video'),
    upload_head = $('upload_head img'),
    upload_name = $('.upload_name'),
    upload_time = $('.upload_time'),
    upload_content = $('.upload_content'),
    favo0_number = $('.favo0_number'),
    talk_number = $('.talk_number'),
    share_number = $('.share_number'),
    btn_follow = $('.btn_follow'),
    upload_more = $('.upload_more'),
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

    if (json_data.avatar.length != 0) {
        upload_head.attr('src', json_data.avatar);
    }
    var cmt_tstring = new Date(json_data.createtime * 1000),
    cmt_month = cmt_tstring.getMonth() + 1,
    cmt_date = cmt_tstring.getDate();
    cmt_year = cmt_tstring.getFullYear();
    upload_name.html(json_data.username);
    upload_time.html(cmt_year + '-' + cmt_month + '-' + cmt_date);
    upload_content.html(json_data.description);
    favo0_number.html(json_data.likecount);
    talk_number.html(json_data.commentcount);
    upload_head.click(function() {
        // jump(json_data.uploader)//上传者主页
    })
    btn_follow.click(function() {
        // jump(json_data.follow)//关注上传者
    })
    upload_more.click(function() {
        // jump(json_data.uploader_more)//视频详情更多
    })
    favo.click(function() {
        // jump(json_data.favo)//收藏
    })
    talk.click(function() {
        // jump(json_data.comment)//评论
    })
    share.click(function() {
        // jump(json_data.share)//分享
    })
}
function fillcomments(json) { //视频评论(用于content_share)
    console.log(json);
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
            var cmt = $('<div class="comment_box"><div class="commenter_info"><a href="" class="commenter_head"><img src="./img/0.png" alt="" /></a><span class="comment_time"></span></div><div class="comment_content"><p class="commenter_name"></p><div class="comment_context"><div class="comment_frame_tri"></div><p class="comment"></p></div></div></div>');
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
            $('.comment_box').eq(i).find('.comment').html(json_data[i].content);
            $('.commenter_head').click(function() {
                // jump(json_data[i].commenter_info)//评论者主页
            })
        }
        $('.comments_more').click(function() {
            // jump(json_data[0].comment_more)//更多评论
        })
    }

    else {
        $('.comments').hide();
    }
}
function filltopicbanner(json) { //话题banner(用于topic_share)
    var a_d_linum = json.data.length,
    json_data = json.data;
    $('.topic_banner a img').attr('src', json_data.picture);
    $('.topic_banner a p').html(json_data.title);
}
function filljoinhead(json) { //参与头像(用于topic_share)
    var a_d_linum = json.data.users.length,
    json_data = json.data;
    if (json_data.users.length > 0) {
        for (i = 0; i < a_d_linum; i++) {
            var jh = $('<li class="join_head"><a href=""><img src="./img/0.png" alt="" /></a></li>');
            var jhb = $('.join_head_box ul');
            var $join_head = $('.join_head a'),
            $join_number = $('.join_number_value');
            jhb.append(jh);
            if (json.data.users[i].avatar.length != 0) {
                $('.join_head a img').attr('src', json.data.users[i].avatar)
            }
            $('.join_number_val').html(a_d_linum = json.data.users.length);
            $('.join_head a').click(function() {
                // jump(json_data[i].joiner_info)//参与者主页
            })
        }
        $('.join_number_value').click(function() {
            // jump(json_data[0].joiner_all)//参与所有人数信息
        })
    } else {
        $('.join_number_val').html(0);
    }
}
function filltopicitem(json) { //hot topics栏(用于topic_share)
    var a_d_linum = json.data.length,
    json_data = json.data;
    var $_topic_item = $('.topic_item');
    for (i = 0; i < a_d_linum; i++) {
        var ti = $('<li class="topic_item"><a href=""><img src="" alt="" /><p class="topic_item_title"></p></a></li>');
        var tb = $('.topic_box ul');
        tb.append(ti);
        $('.topic_item').eq(i).find('img').attr('src', json_data[i].picture);
        $('.topic_item').eq(i).find('p.topic_item_title').html(json_data[i].title);
        $('.topic_item').eq(i).click(function() {
            // jump(json_data[i].topic)//话题主页
        })
    }
}

// function TopicShareData1(){
// }
// function TopicShareData2(){
// }
// function TopicShareData3(){
// }
// function TopicShareData4(){
// }
// function ContentShareData1(){
// }
// function ContentShareData2(){
// }
// function ContentShareData3(){
// }
// function AppDownloadData(){
// }
