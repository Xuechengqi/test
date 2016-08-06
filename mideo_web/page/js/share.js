
$(function(){
    $('.facebook-share').click(function(){
        var href = window.location.href;
        if (href.indexOf("local.mideo.cc") !== -1) {
            href = href.replace("local.mideo.cc", "www.mideo.cc");
        }
        window.open("https://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(href), "share", "'width=600,height=400'");
    });
    $('.whatsapp-share').click(function(){
        var href = window.location.href;
        if (href.indexOf("local.mideo.cc") !== -1) {
            href = href.replace("local.mideo.cc", "www.mideo.cc");
        }
        jumpwhatsapp("https://www.whatsapp.com/","whatsapp://send?text=",href);
    });
    $('.messager-share').click(function(){
        var href = window.location.href;
        if (href.indexOf("local.mideo.cc") !== -1) {
            href = href.replace("local.mideo.cc", "www.mideo.cc");
        }
        window.open("https://www.facebook.com/dialog/send?app_id=1669264926663691&link=" + encodeURIComponent(href) + "&redirect_uri=" + encodeURIComponent(href), "send", "'width=600,height=400'");
    });
    $('.twitter-share').click(function(){
        var href = window.location.href;
        if (href.indexOf("local.mideo.cc") !== -1) {
            href = href.replace("local.mideo.cc", "www.mideo.cc");
        }
        window.open("https://twitter.com/intent/tweet?url=" + encodeURIComponent(href),"" , "'width=600,height=400'");
    });
    $('.googlePlus-share').click(function(){
        var href = window.location.href;
        if (href.indexOf("local.mideo.cc") !== -1) {
            href = href.replace("local.mideo.cc", "www.mideo.cc");
        }
        window.open("https://plus.google.com/share?url=" + encodeURIComponent(href), "menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=400");
    });
});

function jumpwhatsapp(httpurl, appurl, link){
    //判断app或者浏览器链接打开

        // Deep link to your app goes here
        document.getElementById("l").src = appurl + link;

        setTimeout(function() {
            // Link to the App Store should go here -- only fires if deep link fails
            window.open(httpurl,'',"'width=600,height=400'");
        }, 500);
}