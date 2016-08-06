if ('ontouchstart' in window) {
	    var click = 'touchstart';
	} else {
	    var click = 'click';
	}
	$(function(){
		mediaquery();
	})
	$(window).resize(function(){
		mediaquery();
	})

    

function mediaquery(){
  var w_width=$(document).width();
  $('html').css('font-size',(w_width / 360 * 125).toFixed(5)+'%');
}
	$('div.burger').on(click, function () {
	    if (!$(this).hasClass('open')) {
	        $('.menu').show();
	        openMenu();
	    } else {
	        closeMenu();$('.menu').hide(200);
	    }
	});
	$('div.menu ul li').on(click, function (e) {
	    e.preventDefault();
	    closeMenu();
	    var _this=$(this),
	        lis=$('div.menu ul li'),
	        _index=lis.index(_this);
	        var items=$('div.list div.item');
	        items.eq(_index).addClass('active').siblings('.item').removeClass('active');
	        $('.menu').hide(200);
	});
	function openMenu() {
	    $('div.circle').addClass('expand');
	    $('div.burger').addClass('open');
	    $('div.x, div.y, div.z').addClass('collapse');
	    $('.menu li').addClass('animate');
	    setTimeout(function () {
	        $('div.y').hide();
	        $('div.x').addClass('rotate30');
	        $('div.z').addClass('rotate150');
	    }, 70);
	    setTimeout(function () {
	        $('div.x').addClass('rotate45');
	        $('div.z').addClass('rotate135');
	    }, 120);
	}
	function closeMenu() {
	    $('div.burger').removeClass('open');
	    $('div.x').removeClass('rotate45').addClass('rotate30');
	    $('div.z').removeClass('rotate135').addClass('rotate150');
	    $('div.circle').removeClass('expand');
	    $('.menu li').removeClass('animate');
	    setTimeout(function () {
	        $('div.x').removeClass('rotate30');
	        $('div.z').removeClass('rotate150');
	    }, 50);
	    setTimeout(function () {
	        $('div.y').show();
	        $('div.x, div.y, div.z').removeClass('collapse');
	    }, 70);
	}
	function orientaion(){
         if(navigator.userAgent.indexOf("iPad") > 0)//匹配ipad
    {
      if(window.orientation==90||window.orientation==-90)
         { 
          
         }
         if(window.orientation==180||window.orientation==0)
         {
        
         }
    }
    if(navigator.userAgent.match(/(iPhone|iPod|Android|ios)/i)){//匹配移动端(除ipad)
         if(navigator.userAgent.indexOf("UCBrowser") > 0){//移动下的uc    
          if(window.orientation==90||window.orientation==-90)
         { 
            
         }
         if(window.orientation==180||window.orientation==0)
         {
         
         }
         }
         else//移动下其他浏览器
         if(window.orientation==180||window.orientation==0)
         { 
         
         }
         if(window.orientation==90||window.orientation==-90)
         {
         
         }
    }
    //其他认作电脑
    else
	    if($('video.vvv').length==0)
	    { 
	      
	    }
	}