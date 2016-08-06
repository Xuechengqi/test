<?php
namespace Api\Controller;

class AnalyticsController extends SController {
    //action: view, play
    //label: video
    //value: 1
    public function send($action = "", $videoid = "", $country = "") {
        //checkToken();
        if ($action === "view" || $action === "play") {

            $videoid = $this->decode_vid($videoid);
            if (!checkCountry($country)) {
                $country = getDefaultCountry();
            }
            $model = M('record_video');
            $params = array(
                "type" => recordVideoTypeHash()[$action],
                "video_id" => $videoid,
                "country" => $country,
                "createtime" => time()
            );
            $result = $model -> add($params);
            if ($action == "play") {
                $videoModel = M('video');
                $videoInfo = $videoModel -> where(array("id" => $videoid)) -> find();
                if ($videoInfo != false) {
                    $videoInfo['playcount'] = intval($videoInfo['playcount']) + 1;
                    $videoRet = $videoModel -> save($videoInfo);
                }
            }
            if ($result === false) {
                return show_output(300, "");
            } else {
                return show_output(200, "");
            }
        } else {
            return show_output(101, "action");
        }
    }
}

?>