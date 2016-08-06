<?php
//header("Content-type: text/html; charset=utf-8");
//定时任务，每30分钟执行一次
if(isset($_GET['key'])&&$_GET['key']=="c15f59b4733f3ea8a5b54bef19a8d4c5"){

    set_time_limit(100);
    require_once('auto_lib.php');
    $auto = new mgo();

    //自动添加10个赞,1700秒内视频数据
    $ids = $auto->auto_add_like(30,1700,false);
    if(!empty($ids)){
        echo date("Y-m-d H:i")."-auto_like_30 videoid = (".implode(',', $ids).") ok\r\n";
    }


    //审核视频
    $hourMinuts = intval(date('Hi'));
    if (($hourMinuts < 1114 && $hourMinuts >= 144)
        || ($hourMinuts < 1844 && $hourMinuts >= 1214)) {
        exit("skip");
    } else {
        //自动审核一个视频
        $ids = $auto->auto_check_one();
        if(!empty($ids)){
            echo date("Y-m-d H:i")."-auto_check_30 videoid = (".implode(',', $ids).") ok\r\n";
        }
        //如果在这个时间段，再审核一个
        if (($hourMinuts >= 2245 && $hourMinuts < 2400)
            || ($hourMinuts >= 0 && $hourMinuts < 45)) {
            $ids = $auto->auto_check_one();
            if(!empty($ids)){
                echo date("Y-m-d H:i")."-auto_check_30 videoid = (".implode(',', $ids).") ok\r\n";
            }
        }
    }

    echo "ok";
}
