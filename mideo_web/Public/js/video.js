var allTopic = null;

function bindChangeVideoThumb(formBaseUrl) {
    $('.video-thumb').click(function(e) {
        var id = $(this).attr('data-id');
        var src = $(this).attr('src');
        if (id) {
            removeDialog();
            showChangeVideoThumbDialog(id, src, formBaseUrl);
        }
    });
}

function bindChangeVideoTopic(formBaseUrl) {
    $('.video-topic').click(function(e) {
        var id = $(this).attr('data-id');
        var tid = $(this).attr('data-tid');
        if (id) {
            removeDialog();
            if (!allTopic) {
                alert('正在获取话题数据，请稍后！');
            } else {
                showChangeVideoTopicDialog(id, tid, formBaseUrl);
            }
        }
    });
    getAllTopic(formBaseUrl);
}

function getAllTopic(formBaseUrl) {
    if (!allTopic) {
        $.getJSON(formBaseUrl + "/getAllTopic", function(data) {
            allTopic = data;
        });
    }
}

function removeDialog() {
    $('.dialog-mask').remove();
}

function showChangeVideoTopicDialog(id, tid, formBaseUrl) {
    var options = [];
    for (var i = 0; i < allTopic.length; i++) {
        if (allTopic[i].id == tid) {
            options.push('<option selected="" value="'+allTopic[i].id+'">【' + allTopic[i].id + '】 '+allTopic[i].title+'</option>');
        } else {
            options.push('<option value="'+allTopic[i].id+'">【' + allTopic[i].id + '】 '+allTopic[i].title+'</option>');
        }
    }
    var html = [
        '<div class="dialog-mask">',
        '   <div class="dialog">',
        '       <h1>修改视频专题(视频ID：'+id+')</h1>',
        '       <div style="margin:30px;">',
        '           <select id="dialog_tid_sel">',
        '               ' + options.join(''),
        '           </select>',
        '       </div>',
        '       <div><input id="dialog_tid_btn" type="button" class="btn btn-success" value="修改" /></div>',
        '   </div>',
        '</div>'
    ].join("");
    var dialgoNode = $('.dialog-mask');
    if (dialgoNode.length) {
        dialgoNode.show();
    } else {
        $('body').append($(html));
    }
    $('.dialog-mask .dialog').click(function(e) {
        e.stopPropagation();
    });
    $('#dialog_tid_btn').click(function(e) {
        var tid = $('#dialog_tid_sel').val();
        $.ajax({
            url: formBaseUrl + "/action_changeTopic/id/" + id + "/tid/" + tid
        }).done(function(ret) {
            if (ret.code == 200) {
                removeDialog();
                window.location.reload();
            } else {
                alert(ret.msg);
            }
        }).fail(function(msg) {
            alert('出错啦！');
        });
    });
    $('.dialog-mask').click(function(e) {
        $(this).hide();
    });
}

function showChangeVideoThumbDialog(id, src, formBaseUrl) {
    var html = [
        '<div class="dialog-mask">',
        '   <div class="dialog">',
        '       <h1>修改视频缩略图</h1>',
        '       <form action="'+formBaseUrl+'/action_changevideothumb" enctype="multipart/form-data" method="post">',
        '           <input class="dialog-video-id" type="hidden" name="id" value="'+id+'"/>',
        '           <img class="dialog-video-thumb" style="max-height:200px;max-width:200px;" src="' + src + '" alt="" /><br/>',
        '           <input type="file" name="thumb" required=""/><br/>',
        '           <input type="submit" class="btn btn-success" value="修改" />',
        '       </form>',
        '   </div>',
        '</div>'
    ].join("");
    var dialgoNode = $('.dialog-mask');
    if (dialgoNode.length) {
        $('.dialog-mask .dialog-video-thumb').attr('src', src);
        $('.dialog-mask .dialog-video-id').val(id);
        dialgoNode.show();
    } else {
        $('body').append($(html));
    }
    $('.dialog-mask .dialog').click(function(e) {
        e.stopPropagation();
    });
    $('.dialog-mask').click(function(e) {
        $(this).hide();
    });
}