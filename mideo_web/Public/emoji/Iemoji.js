/**
 * @author ivy <guoxuivy@gmail.com>
 * @copyright Copyright &copy; 2013-2017 Ivy Software LLC
 * @license https://github.com/guoxuivy/ivy/
 * @package framework
 * @link https://github.com/guoxuivy/ivy 
 * @since 1.0 
 *
 *对话框插件 Iemoji 基于jquery 封装
 *
 *
 *
 *
 *
 需要样式
.Iemoji{ overflow:hidden; position:fixed; left:50%; top:200px;  display:none; z-index:999;}
.Iemoji_title{ height:30px; background:#07aaff; padding-left:20px; line-height:30px;font-family:"微软雅黑"; font-size:14px; color:#ffffff;}
.Iemoji_title span{ float:right; display:inline; margin-right:5px; height:30px; width:30px; text-align:center; cursor:pointer;}
.Iemoji_body{  overflow:hidden;font-family:"微软雅黑"; font-size:12px; color:#666666; background:#FFF; border-left:1px solid #dddddd; border-right:1px solid #dddddd; border-bottom:1px solid #dddddd;}
.Iemoji_content{padding:20px 30px 5px 5px;}
.Iemoji_active{ height:60px; background:#fafafa; width:100%;border-top:1px solid #dddddd; text-align:right;}
.Iemoji_active a{ display:inline-block; height:28px; width:80px; border:1px solid #cccccc; border-radius:5px; margin:0 5px;font-family:"微软雅黑"; font-size:12px; color:#666666; text-align:center; line-height:28px; margin-top:16px;}


使用示例
var d = Iemoji({
        el:$("#em"),
        input:$("#text")
    });


 */



(function($,win,dom,undef){

	var __EMOJI__ = "http://47.90.23.110/Public/emoji";

	var template = [
        '<table class="emoji_table">',
        '   <tr>',
        '       <td><img class="emj" data-code="\uD83D\uDE04" src="'+__EMOJI__+'/img/emoji_1f600.png"></td>',
        '       <td><img class="emj" data-code="\ue40f" src="'+__EMOJI__+'/img/emoji_1f602.png"></td>',
        '       <td><img class="emj" data-code="0x1f604" src="'+__EMOJI__+'/img/emoji_1f604.png"></td>',
        '       <td><img class="emj" data-code="0x1f609" src="'+__EMOJI__+'/img/emoji_1f609.png"></td>',

        '       <td><img class="emj" data-code="0x1f60a" src="'+__EMOJI__+'/img/emoji_1f60a.png"></td>',
        '       <td><img class="emj" data-code="0x1f60c" src="'+__EMOJI__+'/img/emoji_1f60c.png"></td>',
        '       <td><img class="emj" data-code="0x1f60d" src="'+__EMOJI__+'/img/emoji_1f60d.png"></td>',
        '       <td><img class="emj" data-code="0x1f618" src="'+__EMOJI__+'/img/emoji_1f618.png"></td>',

        '       <td><img class="emj" data-code="0x1f61c" src="'+__EMOJI__+'/img/emoji_1f61c.png"></td>',
        '       <td><img class="emj" data-code="0x1f60e" src="'+__EMOJI__+'/img/emoji_1f60e.png"></td>',
        '       <td><img class="emj" data-code="0x1f60f" src="'+__EMOJI__+'/img/emoji_1f60f.png"></td>',
        '       <td><img class="emj" data-code="0x1f612" src="'+__EMOJI__+'/img/emoji_1f612.png"></td>',

        '       <td><img class="emj" data-code="0x1f633" src="'+__EMOJI__+'/img/emoji_1f633.png"></td>',
        '       <td><img class="emj" data-code="0x1f621" src="'+__EMOJI__+'/img/emoji_1f621.png"></td>',
        '       <td><img class="emj" data-code="0x1f614" src="'+__EMOJI__+'/img/emoji_1f614.png"></td>',
        '       <td><img class="emj" data-code="0x1f623" src="'+__EMOJI__+'/img/emoji_1f623.png"></td>',
        '       <td><img class="emj" data-code="0x1f624" src="'+__EMOJI__+'/img/emoji_1f624.png"></td>',
        '   </tr>',
        '</table>'
    ].join("");


	var Iemoji=function(settings){
		this.settings=$.extend({},Iemoji.defaults,settings);
		this._self=$(template);
		//执行
		this.run=function(){
			this.bind();
			this.show();
			//渲染后内容初始化
			//this.init();
			return this;
		}
	};

	/**
	 * 弹出框 默认配置 可扩展
	 * @type {Object}
	 */
	Iemoji.defaults={
		el:null,
		input:null,
		cancel:true,
		init:function(){},
		close:function(){}
	};

	Iemoji.prototype={
		show:function(){
			var obj=this;
			var _self=this._self;
			this.settings.el.html(_self);
		},

		//对话框本身事件绑定
		bind:function(){
			var obj=this;
			var _self=this._self;
			var textarea=this.settings.input;

			//点击事件
			_self.find('img').click(function(){
				var code = $(this).attr('data-code');
				var e = $(this).prop("outerHTML");
				textarea.focus();
				obj._insertimg(e)
			});
		
		},

		_insertimg:function(str){
			var textarea=this.settings.input;
		    var selection= window.getSelection ? window.getSelection() : document.selection;
		    var range= selection.createRange ? selection.createRange() : selection.getRangeAt(0);
		    if (!window.getSelection){
		        textarea.focus();
		        var selection= window.getSelection ? window.getSelection() : document.selection;
		        var range= selection.createRange ? selection.createRange() : selection.getRangeAt(0);
		        range.pasteHTML(str);
		        range.collapse(false);
		        range.select();
		    }
		    else{
		        textarea.focus();
		        range.collapse(false);
		        var hasR = range.createContextualFragment(str);
		        var hasR_lastChild = hasR.lastChild;
		        while (hasR_lastChild && hasR_lastChild.nodeName.toLowerCase() == "br" && hasR_lastChild.previousSibling && hasR_lastChild.previousSibling.nodeName.toLowerCase() == "br") {
		            var e = hasR_lastChild;
		            hasR_lastChild = hasR_lastChild.previousSibling;
		            hasR.removeChild(e)
		        }
		        range.insertNode(hasR);
		        if (hasR_lastChild) {
		            range.setEndAfter(hasR_lastChild);
		            range.setStartAfter(hasR_lastChild)
		        }
		        selection.removeAllRanges();
		        selection.addRange(range)
		    }
		    textarea.focus();
		},

		content:function(){
			var textarea=this.settings.input;
			return textarea.html();
		},

		
	};



	win['Iemoji']=function(settings){
		var emoji=new Iemoji(settings);
		return emoji.run();
	};


})(jQuery,window,document);