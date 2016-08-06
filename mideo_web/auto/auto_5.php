<?php
//header("Content-type: text/html; charset=utf-8");
//定时任务，每5分钟执行一次
if(isset($_GET['key'])&&$_GET['key']=="c15f59b4733f3ea8a5b54bef19a8d4c5"){
	set_time_limit(100);
	require_once('auto_lib.php');
	$auto = new mgo();
	//自动添加通用评论1个,300秒内视频数据
	$ids = $auto->auto_commom(1,300);
	if(!empty($ids)){
	    echo date("Y-m-d H:i")."-auto_comment_5 videoid = (".implode(',', $ids).") ok\r\n";
	}
	//自动添加1个赞,300秒内视频数据
	$ids = $auto->auto_add_like(2,300);
	if(!empty($ids)){
	    echo date("Y-m-d H:i")."-auto_like_5 videoid = (".implode(',', $ids).") ok\r\n";
	}
	echo "test is ok";
}
