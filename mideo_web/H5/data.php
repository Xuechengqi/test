<?php
    header("Content-type: application/json;charset=utf-8");
    require_once('../auto/auto_lib.php');
    require_once('../Application/Lib/CryptDes.class.php');
    $page = 1;
    $pageSize = 10;
    if (isset($_GET['page'])) {
        $page = intval($_GET['page']);
    }
    $db = new mysql();
    $sql = "SELECT id,thumb,userid,likecount FROM `video` WHERE status=1 order by id desc limit " . ($page - 1) * $pageSize . "," . $pageSize;
    $rows = $db->findAllBySql($sql);
    echo json_encode($rows);
?>