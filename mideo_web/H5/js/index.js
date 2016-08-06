$(function(){
    //选择的nav
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
            if(itemIndex == '0'){
                _ajax();
            }
        }
    });
});

function _ajax(url, func){
	$.ajax({
		type : 'GET',
		async : true;
		url : url,
		dataType : 'json',
		success : function(json){
			func(json);
		},
		error : function(){
			console.log('fail!', url);
		}
	});
}

//获取数据，先分析页面要得到哪些信息：话题&视频
function getData(){
	var 
}

function fillvideothumbnails(json){//视频缩略
	var data_num = json.data.length,
	    json_data = json.data;
	    for(var i = 0, i < data_num; i ++){
	    	var thumbnail = $('<li class = "video_thumbnail"><div class="video_item"><a class="video_thumbnail_img"><img src="" alt="" /></a></div><div class="video_item_info"><a class="upload_head_thumbnail"><img src="./img/0.png" alt="" /></a><a class="upload_name_thumbnail"></a><span class="favo_number"><img src="./img/like.png" alt="" /><span></span></span></div></li>');
	    	var tb_container = $('.video_thumbnails');
	    	tb_container.append(thumbnail);
	    	var video_thumbnail = $('.video_thumbnail');
	    	if(json_data[i].avatar.length != 0){
                video_thumbnail.eq(i).find('.upload_head_thumbnail img').attr('src', json_data[i].avatar);
            }
            video_thumbnail.eq(i).find('.video_thumbnail_img').attr('data-id', json_data[i].id);
            video_thumbnail.eq(i).find('.video_thumbnail_img img').attr('src', json_data[i].thumb);
            video_thumbnail.eq(i).find('.upload_name_thumbnail').html(json_data[i].username);
            video_thumbnail.eq(i).find('.favo_number span').html(json_data[i].likecount);

            //改
	    }
}