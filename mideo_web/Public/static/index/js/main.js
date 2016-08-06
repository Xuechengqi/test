var v_width=1920,
  v_height=1080,
  v_ratio=v_width/v_height;
var w_width=$(window).width(),
  w_height=$(window).height(),
  w_ratio=w_width/w_height; 
              
 $(function(){
    putImgOrVideo();
    hengshuping();
 })

 $(window).resize(function(){
    putImgOrVideo();
    hengshuping();
 })
function putImgOrVideo(){
    w_width=$(window).width(),
  w_height=$(window).height(),
  w_ratio=w_width/w_height;
   var wrap_h=$('.wrap').height();
  $('.wrap').css('paddingTop',(w_height-wrap_h)/2 + 'px'); 
    if(navigator.userAgent.indexOf("iPad") > 0)//匹配ipad
    {
      if(window.orientation==90||window.orientation==-90)
         { 
           $('.video_bg').css('background','url("./video/party.jpg")');
           $('.video_bg').css('backgroundSize','cover'); 
         }
         if(window.orientation==180||window.orientation==0)
         {
        
          $('.video_bg').css('background','url("./video/Cheer-Up.jpg")');
          $('.video_bg').css('backgroundSize','cover');
         }
    }
    if(navigator.userAgent.match(/(iPhone|iPod|Android|ios)/i)){//匹配移动端(除ipad)
         if(navigator.userAgent.indexOf("UCBrowser") > 0){    
          if(window.orientation==90||window.orientation==-90)
         { 
        
           $('.video_bg').css('background','url("./video/party.jpg")');
           $('.video_bg').css('backgroundSize','cover'); 
         }
         if(window.orientation==180||window.orientation==0)
         {
           
          $('.video_bg').css('background','url("./video/Cheer-Up.jpg")');
          $('.video_bg').css('backgroundSize','cover');
         }
         }
         else//其他视作pc端
         if(window.orientation==180||window.orientation==0)
         { 
           $('.video_bg').css('background','url("./video/party.jpg")');
           $('.video_bg').css('backgroundSize','cover'); 
         }
         if(window.orientation==90||window.orientation==-90)
         {
          $('.video_bg').css('background','url("./video/Cheer-Up.jpg")');
          $('.video_bg').css('backgroundSize','cover');
         }
    }
    //其他认作电脑
    else
    if($('video.vvv').length==0)
    { 
       var vd=$('<video poster="./video/Cheer-Up.jpg" width="100%" class="vvv" preload="auto" FullScreen="FullScreen" autoplay="autoplay" muted="muted" loop="loop" src="./video/Cheer-Up.mp4" ></video>');
       $('.video_bg').append(vd);
       $('.video_bg').css('background','none');
    }

}
function hengshuping(){
   w_width=$(window).width(),
  w_height=$(window).height(),
  w_ratio=w_width/w_height;
  if(w_ratio>v_ratio){
                       $('video.vvv').css({width:'100%',height:'auto'});
                       console.log('截取水平中间')
                }
                else{
                       $('video.vvv').css({height:'100%',width:'auto'});
                       console.log('截取上下中间')
                }   
}
window.addEventListener("onorientationchange" in window ? "orientationchange" : "resize", hengshuping, false); 
//移动端的浏览器一般都支持window.orientation这个参数，通过这个参数可以判断出手机是处在横屏还是竖屏状态。