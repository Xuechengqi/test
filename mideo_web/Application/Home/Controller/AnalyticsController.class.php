<?php
namespace Home\Controller;
class AnalyticsController extends SController {
    public function video($a_type = 'create'){
        $a_type = str_replace(" ", "", $a_type);
        $this -> assign('a_type', $a_type);
        $result = array();
        $title = "";
        switch ($a_type) {
            case 'create':
                $result = $this -> createAnalytics();
                $title = "video upload";
                break;
            case 'view':
                $result = $this -> viewAnalytics();
                $title = "video view";
                break;
            case 'play':
                $result = $this -> playAnalytics();
                $title = "video play";
                break;
            case 'like':
                $result = $this -> likeAnalytics();
                $title = "video like";
                break;
            case 'comment':
                $result = $this -> commentAnalytics();
                $title = "video comment";
                break;
            default:
                break;
        }
        $echartsData = $this -> _getEchartsData($result, $title);
        $this -> assign("echartsData", $echartsData);
        $this -> display('video');
    }

    private function createAnalytics() {
        $dateRange = getDateRangeFromForm();
        $where = array(
            'adate' => array (
                array("egt", $dateRange['begin']),
                array('elt', $dateRange['end'])
            ),
            "atype" => 1
        );
        $analyticsModel = M('analytics_video');
        $result = $analyticsModel -> where($where) -> select();
        return $result;
    }

    private function viewAnalytics() {
        $dateRange = getDateRangeFromForm();
        $where = array(
            'adate' => array (
                array("egt", $dateRange['begin']),
                array('elt', $dateRange['end'])
            ),
            "atype" => 2
        );
        $analyticsModel = M('analytics_video');
        $result = $analyticsModel -> where($where) -> select();
        return $result;
    }

    private function playAnalytics() {
        $dateRange = getDateRangeFromForm();
        $where = array(
            'adate' => array (
                array("egt", $dateRange['begin']),
                array('elt', $dateRange['end'])
            ),
            "atype" => 3
        );
        $analyticsModel = M('analytics_video');
        $result = $analyticsModel -> where($where) -> select();
        return $result;
    }

    private function likeAnalytics() {
        $dateRange = getDateRangeFromForm();
        $where = array(
            'adate' => array (
                array("egt", $dateRange['begin']),
                array('elt', $dateRange['end'])
            ),
            "atype" => 4
        );
        $analyticsModel = M('analytics_video');
        $result = $analyticsModel -> where($where) -> select();
        return $result;
    }

    private function commentAnalytics() {
        $dateRange = getDateRangeFromForm();
        $where = array(
            'adate' => array (
                array("egt", $dateRange['begin']),
                array('elt', $dateRange['end'])
            ),
            "atype" => 5
        );
        $analyticsModel = M('analytics_video');
        $result = $analyticsModel -> where($where) -> select();
        return $result;
    }

    private function _getEchartsData($result, $title) {
        $echartsResult = array(
            "title" => $title,
            "legend" => implode(",", getAllowedCountry())
        );
        $xAxis = array();
        $series = array();
        $allCountry = getAllowedCountry();
        for ($i = 0; $i < count($allCountry); $i++) {
            $country = $allCountry[$i];
            $series[$country] = array();
            for ($j = 0; $j < count($result); $j++) {
                if ($result[$j]['acountry'] === $country) {
                    $series[$country][] = intval($result[$j]['acount']);
                }
                if ($i == 0) {
                    if (!in_array($result[$j]['adate'], $xAxis)) {
                        $xAxis[] = $result[$j]['adate'];
                    }
                }
            }
            $series[$country] = implode(",", $series[$country]);
        }
        $echartsResult['series'] = $series;
        $echartsResult['xAxis'] = implode(",", $xAxis);
        return $echartsResult;
    }

    //$this -> updateAnalytics("video", "create");
    public function updateAnalytics($type = "", $subtype = "") {
        $result = -1;
        if (isLogin()) {
            switch ($type) {
                case 'video':
                    switch ($subtype) {
                        case 'create':
                            $result = $this -> _updateVideoCreateAnalytics();
                            break;
                        case 'view':
                            $result = $this -> _updateVideoViewAnalytics();
                            break;
                        case 'play':
                            $result = $this -> _updateVideoPlayAnalytics();
                            break;
                        default:
                            # code...
                            break;
                    }
                    break;
                default:
                    # code...
                    break;
            }
        }
        echo $result;
    }

    private function _getLastUpdateDate($type) {
        //last update date
        $analyticsModel = M('analytics_video');
        $analyticsLastDate = $analyticsModel -> where(array("atype" => analyticsVideoTypeHash()[$type])) -> max('adate');
        if ($analyticsLastDate === null) {
            return strtotime('2016-03-01');
        }
        return strtotime('+1 day', strtotime($analyticsLastDate));
    }

    private function _getUpdateDayRange($lastUpdateTime) {
        $currentDay = strtotime(date('Y-m-d'));
        $result = array();
        $daySeconds = 60 * 60 * 24;
        for ($i = $lastUpdateTime; $i <= $currentDay + $daySeconds; $i += $daySeconds) {
            $result[] = $i;
            //echo date('Y-m-d', $i) . '<br/>';
        }
        return $result;
    }

    private function _updateVideoCreateAnalytics() {
        $lastUpdateTime = $this -> _getLastUpdateDate("create");
        if ($lastUpdateTime === date('Y-m-d')) {
            return 1;
        }
        $dateRange = $this -> _getUpdateDayRange($lastUpdateTime);
        $minDate = $lastUpdateTime;
        $maxDate = $dateRange[count($dateRange) - 1];
        $Model = new \Think\Model();
        $createVideoSql = "SELECT date(FROM_UNIXTIME(A.createtime)) as day, A.country, count(A.id) as count FROM `video` A where A.createtime < $maxDate and A.createtime >= $minDate group by A.country, date(FROM_UNIXTIME(createtime))";
        try {
            $createResult = $Model->query($createVideoSql);
            $analyticsResults = array();
            for ($i = 0; $i < count($dateRange); $i++) {
                $day = date('Y-m-d', $dateRange[$i]);
                $allCountry = getAllowedCountry();
                for ($m = 0; $m < count($allCountry); $m++) {
                    $country = $allCountry[$m];
                    $analyticsItem = array(
                        "atype" => analyticsVideoTypeHash()["create"],
                        "acountry" => $country,
                        "adate" => $day,
                        "addtime" => time()
                    );
                    $isDayHasValue = false;
                    for ($j = 0; $j < count($createResult); $j++) {
                        if ($createResult[$j]['country'] === $country &&
                            $createResult[$j]['day'] === $day) {
                            $analyticsItem['acount'] = intval($createResult[$j]['count']);
                            $isDayHasValue = true;
                            break;
                        }
                    }
                    if (!$isDayHasValue) {
                        $analyticsItem['acount'] = 0;
                    }
                    $analyticsResults[] = $analyticsItem;
                }
            }
            $analyticsModel = M('analytics_video');
            $result = $analyticsModel -> addAll($analyticsResults);
            if ($result === false) {
                return -3;
            } else {
                return 1;
            }
        } catch(\Exception $e) {
            return -2;
            //todo
        }
    }

    private function _updateVideoViewPlayAnalytics($type) {
        $lastUpdateTime = $this -> _getLastUpdateDate($type);
        if ($lastUpdateTime === date('Y-m-d')) {
            return 1;
        }
        $dateRange = $this -> _getUpdateDayRange($lastUpdateTime);
        $minDate = $lastUpdateTime;
        $maxDate = $dateRange[count($dateRange) - 1];
        $Model = new \Think\Model();
        $createVideoSql = "SELECT date(FROM_UNIXTIME(A.createtime)) as day, A.country, count(A.id) as count FROM `record_video` A where A.createtime < $maxDate and A.createtime >= $minDate group by A.country, date(FROM_UNIXTIME(createtime))";
        try {
            $createResult = $Model->query($createVideoSql);
            $analyticsResults = array();
            for ($i = 0; $i < count($dateRange); $i++) {
                $day = date('Y-m-d', $dateRange[$i]);
                $allCountry = getAllowedCountry();
                for ($m = 0; $m < count($allCountry); $m++) {
                    $country = $allCountry[$m];
                    $analyticsItem = array(
                        "atype" => analyticsVideoTypeHash()[$type],
                        "acountry" => $country,
                        "adate" => $day,
                        "addtime" => time()
                    );
                    $isDayHasValue = false;
                    for ($j = 0; $j < count($createResult); $j++) {
                        if ($createResult[$j]['country'] === $country &&
                            $createResult[$j]['day'] === $day) {
                            $analyticsItem['acount'] = intval($createResult[$j]['count']);
                            $isDayHasValue = true;
                            break;
                        }
                    }
                    if (!$isDayHasValue) {
                        $analyticsItem['acount'] = 0;
                    }
                    $analyticsResults[] = $analyticsItem;
                }
            }
            $analyticsModel = M('analytics_video');
            $result = $analyticsModel -> addAll($analyticsResults);
            if ($result === false) {
                return -3;
            } else {
                return 1;
            }
        } catch(\Exception $e) {
            return -2;
            //todo
        }
    }

    private function _updateVideoViewAnalytics() {
        return $this -> _updateVideoViewPlayAnalytics("view");
    }

    private function _updateVideoPlayAnalytics() {
        return $this -> _updateVideoViewPlayAnalytics("play");
    }


    /**
     * 用户统计
     * @param  string $a_type [description]
     * @return [type]         [description]
     */
    public function user($a_type = 'create'){
        S('abc',null);
        var_dump(S('abc'));
        $this -> display('user');
    }
}