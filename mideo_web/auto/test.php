<?php
    function run($hourMinuts) {
        if (($hourMinuts < 1114 && $hourMinuts >= 144)
            || ($hourMinuts < 1844 && $hourMinuts >= 1214)) {
            return 0;
        } else {
            if (($hourMinuts >= 2245 && $hourMinuts < 2400)
                || ($hourMinuts >= 0 && $hourMinuts < 45)) {
                return 2;
            }
            return 1;
        }
    }

    function mork($start) {
        //var_dump($start);
        //var_dump("------------------------------------------------------------");
        $h = 24;
        $m = 30;
        $begin = array();
        $count = 0;
        for ($i = 0; $i < $h; $i++) {
            for ($j = 0; $j < 2; $j++) {
                $curr = $start + $i * 100 + $j * $m;
                if (($curr % 100) > 60) {
                    $curr =$curr  - 60 + 100;
                }
                $curr = $curr % 2400;
                $begin[] = $curr;
                $isSkip = run($curr);
                if ($isSkip !== 0) {
                    //var_dump($curr . ' ' . $isSkip);
                } else {
                    //var_dump($curr . ' skip');
                }
                $count += $isSkip;
            }
        }
        //var_dump('-----------------------------------------------------------');
        var_dump($count);
    }

    //echo date('Y-m-d H:i:s');
    $hourMinuts = intval(date('Hi'));
    $hourMinuts = 0;
    for ($i = 0; $i < 60; $i++) {
        mork($i);
    }
    //mork($hourMinuts);
    //run($hourMinuts);


?>