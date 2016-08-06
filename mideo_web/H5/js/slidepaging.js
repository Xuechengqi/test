

(function($){
	var win = window;
	var doc = document;
	var $win = $(win);
	var $doc = $(doc);
	//为jquery对象添加属性
	$.fn.slidepaging = function(options){
		return new Myslidepaging(this, options);
	};

	var Myslidepaging = function(element, options){
		this.$element = element;
		//上方是否插入DOM
		this.upInsertDom = false;
		//loading状态
		this.isloading = false;
		//是否锁定
		this.isLockUp = false;
		this.isLockDown = false;
		//是否有数据
		this.hasData = true;
		this._scrollTop = 0;
		this._threshold = 0;
		this.init(options);
	};

	//对象的初始化
	Myslidepaging.prototype.init = function(options){
		this.opts = $.extend(true, {}, {
			//滑动区域
			scrollArea : this.$element,
			//上方的DOM
			domUp : {
				domClass : 'dropload-up',
				domRefresh : '<div class="dropload-refresh">↓下拉刷新</div>',
				domUpdate : '<div class="dropload-update">↑释放更新</div>',
				domLoad : '<div class="dropload-load"><span class="loading"></span>加载中...</div>'
			},
			//下面的DOM
			domDown : {
				domClass : 'dropload-down',
				domRefresh : '<div class="dropload-refresh">↑上拉加载更多</div>',
				domLoad : '<div><span class="loading"></span>加载中...</div>',
				domNoData : '<div class="dropload-noData">暂无数据</div>'
			},
			//自动加载
			autoLoad : true,
			//拉动距离
			distance : 50,
			//提前加载距离
			threshold : '',
			//上下方function
			loadUpFn : '',
			loadDownFn : ''
		}, options);

		//如果加载分页，事先在下方插入DOM
		if(this.opts.loadDownFn != ''){
			this.$element.append('<div class="' + this.opts.domDown.domClass + '">' + this.opts.domDown.domRefresh + '</div>');
			this.$domDown = $('.' + this.opts.domDown.domClass);
		}
		//计算提前加载距离
		if(!(!this.$domDown && this.opts.threshold === '')){
			this._threshold = Math.floor(this.$domDown.height() * 1/3);
		}else{
			this._threshold = this.opts.threshold;
		}

		//判断滚动区域
		if(this.opts.scrollArea == win){
			this.$scrollArea = $win;
			//获取文档高度
			this._scrollDocHeight = $doc.height();
			//获取win显示区高度——改
			this._scrollHeight = doc.documentElement.clientHeight;
		}else{
			this.$scrollArea = this.opts.scrollArea;
			this._scrollDocHeight = this.$element[0].scrollHeight;
			this._scrollHeight = this.$element.height();
		}
		//自动加载
		fnAutoLoad(this);

		//窗口调整
		$win.on('resize', function(){
			if(this.opts.scrollArea == win){
				//重新获取win显示区高度
				this._scrollHeight = win.innerHeight;
			}else{
				this._scrollHeight = this.$element.height();
			}
		});

		//绑定触摸
		this.$element.on('touchstart', function(e){
			if(!this.isloading){
				fnTouches(e);
				fnTouchstart(e, this);
			}
		});
		this.$element.on('touchmove', function(e){
			if(!this.isloading){
				fnTouches(e);
				fnTouchmove(e, this);
			}
		});
		this.$element.on('touchend', function(){
			if(!this.isloading){
				fnTouchend(this);
			}
		});

		//分页加载
		this.$scrollArea.on('scroll', function(){
			this._scrollTop = this.$scrollArea.scrollTop();
			//滚动页面触发加载数据
			if(this.opts.loadDownFn != '' && !this.isloading && !this.isLockDown && (this._scrollDocHeight - this._threshold) <= (this._scrollHeight + this._scrollTop)){
				loadDown(this);
			}
		});
	}

	//如果文档高度不大于窗口高度，数据较少，自动加载后面的数据
	function fnAutoLoad(object){
		if(object.opts.autoLoad){
			if((object._scrollDocHeight - object._threshold) <= object._scrollHeight){
				loadDown(object);
			}
		}
	}

	//加载分页
	function loadDown(object){
		object.direction = 'up';
		object.$domDown.html(object.opts.domDown.domLoad);
		object.isloading = true;
		object.opts.loadDownFn(object);
	}

	//touches
	function fnTouches(e){
		if(!e.touches){
			e.touches = e.originalEvent.touches;
		}
	}

	//touchstart
	function fnTouchstart(e, object){
		object._startY = e.touches[0].pageY;
		//记录触摸时的scrolltop值
		object.touchScrollTop = object.$scrollArea.scrollTop();
	}

	//touchmove
	function fnTouchmove(e, object){
		object._curY = e.touches[0].pageY;
		object._moveY = object._curY - object._startY;

		if(object._moveY > 0){
			object.direction = 'down';
		}else if(object._moveY < 0){
			object.direction = 'up';
		}

		var _absMoveY = Math.abs(object._moveY);

		//更新
		if(object.opts.loadUpFn != '' && object.touchScrollTop <= 0 && this.direction == 'down' && !object.isLockUp){
			e.preventDefault();

			object.$domUp = $('.' + object.opts.domUp.domClass);
			//如果加载区没有DOM
			if(!object.upInsertDom){
				object.$element.prepend('<div class="' + object.opts.domUp.domClass + '"></div>');
				object.upInsertDom = true;
			}

			fnTransition(object.$domUp, 0);

			//下拉
			if(_absMoveY <= object.opts.distance){
				object._offsetY = _absMoveY;
				object.$domUp.html(object.opts.domUp.domRefresh);
			}else if(_absMoveY > object.opts.distance && _absMoveY <= object.opts.distance * 2){//指定距离 < 下拉距离 < 指定距离*2
				object._offsetY = object.opts.distance + (_absMoveY - object.opts.distance) * 0.5;
				object.$domUp.html(object.opts.domUp.domUpdate);
			}else{//下拉距离 > 指定距离*2
				object._offsetY = object.opts.distance + object.opts.distance * 0.5 + (_absMoveY - object.opts.distance * 2) * 0.2;
			}
			object.$domUp.css({'height' : object._offsetY});
		}
	}

	//css过渡
	function fnTransition(dom, num){
		dom.css({
			'-webkit-transition' : 'all ' + num + 'ms',
			'transition' : 'all ' + num + 'ms'
		});
	}

	//touchend
	function fnTouchend(object){
		var _absMoveY = Math.abs(object._moveY);
		if(object.opts.loadUpFn != '' && object.touchScrollTop <= 0 && object.direction == 'down' && !object.isLockUp){
			fnTransition(object.$domUp, 300);

			if(_absMoveY > object.opts.distance){
				object.$domUp.css({'height' : object.$domUp.children().height()});
				object.$domUp.html(object.opts.domUp.domLoad);
				object.isloading = true;
				object.opts.loadUpFn(object);
			}else{
				object.$domUp.css({'height' : '0'}).on('webkitTransitionEnd mozTransitionEnd transitionend', function(){
					object.upInsertDom = false;
					$(this).remove();
				});
			}
			object._moveY = 0;
		}
	}

	Myslidepaging.prototype.noData = function(flag){
		if(flag === undefined || flag == true){
			this.hasData = false;
		}else if(flag == false){
			this.hasData = true;
		}
	};
	//重置
	Myslidepaging.prototype.resetload = function(){
		if(this.direction == 'down' && this.upInsertDom){
			this.$domUp.css({'height' : '0'}).on('webkitTransitionEnd mozTransitionEnd transitionend', function(){
				this.isloading = false;
				this.upInsertDom = false;
				$(this).remove();
				fnRecoverDocHeight(this);
			});
		}else if(object.direction == 'up'){
			this.isloading = false;
			if(this.hasData){
				//加载区修改样式
				this.$domDown.html(this.opts.domDown.domRefresh);
				fnRecoverDocHeight(this);
				fnAutoLoad(this);
			}else{
				this.$domDown.html(this.opts.domDown.domNoData);
			}
		}
	};

	//重新获取文档高度
	function fnRecoverDocHeight(object){
		if(object.opts.scrollArea == win){
			object._scrollDocHeight = $doc.height();
		}else{
			object._scrollDocHeight = object.$element[0].scrollHeight;
		}
	}

	Myslidepaging.prototype.unlock = function(){
		this.isLockUp = false;
		this.isLockDown = false;
		//改
	}

	Myslidepaging.prototype.lock = function(direction){
		if(direction === undefined){
			if(this.direction == 'up'){//如果操作方向向上
				this.isLockDown = true;
			}else if(this.direction == 'down'){//如果操作方向向下
				this.isLockUp = true;
			}else{
				this.isLockUp = true;
				this.isLockDown = true;
			}
		}//未完，改？
	}
})(window.JQuery);