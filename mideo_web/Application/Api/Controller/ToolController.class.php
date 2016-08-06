<?php
namespace Api\Controller;
use Think\Controller;

class ToolController extends SController {

    public function musics($country = "BR") {
        //checkToken();
        if (!checkCountry($country)) {
            $country = getDefaultCountry();
        }
        $listCache = S(array('prefix'=>'tool_music'));
        $cacheKey = 'tool_music_' . $country;
        $result = $listCache -> $cacheKey;
        if ($result == false) {
            $musicModel = M('music');
            $where = array("country" => $country);
            $result = $musicModel -> where($where) -> order('id desc') -> field('title,author,duration,url,bakurl,artworkurl') -> select();
            foreach ($result as &$value) {
                $value["bakurl"] = getUploadResourceBaseUrl().$value["bakurl"];
                $artworkurl=empty($value["artworkurl"])?"music/default.png":$value["artworkurl"];
                $value["artworkurl"] = getUploadResourceBaseUrl().$artworkurl;
            }
            $listCache -> $cacheKey = $result;
        }
        if ($result === false) {
            return show_output(400, "");
        } else {
            return show_output(200, "", $result);
        }
    }

    public function catemusics($country = "BR") {
        //checkToken();
        if (!checkCountry($country)) {
            $country = getDefaultCountry();
        }
        $listCache = S(array('prefix'=>'tool_catemusics'));
        $cacheKey = 'tool_catemusics_' . $country;
        $result = $listCache -> $cacheKey;
        if ($result == false) {
            $musicModel = M('music');
            $where = array("music.country" => $country);
            $result = $musicModel -> where($where) -> order('music.id desc') 
            -> field('title,cid,author,duration,url,bakurl,artworkurl,mcate.name as cname') 
            ->join('LEFT JOIN music_cate AS mcate ON mcate.id = music.cid')
            ->order("mcate.id desc")
            -> select();
            $res = array();
            foreach ($result as &$value) {
                $value["bakurl"] = getUploadResourceBaseUrl().$value["bakurl"];
                $artworkurl=empty($value["artworkurl"])?"music/default.png":$value["artworkurl"];
                $value["artworkurl"] = getUploadResourceBaseUrl().$artworkurl;

                $value['cname']=$value['cid']=="0"?"Outros":$value['cname'];
                
                if(isset($res[$value['cid']])){
                    $res[$value['cid']]['list'][]=$value;
                }else{
                    $res[$value['cid']]=array('cid'=>$value['cid'],'cname'=>$value['cname'],'list'=>array($value));
                }
            }
            $result = array_values($res);
            $listCache -> $cacheKey = $result;
        }
        if ($result === false) {
            return show_output(400, "");
        } else {
            return show_output(200, "", $result);
        }
    }

    public function stickers($country = "BR") {
        //checkToken();
        if (!checkCountry($country)) {
            $country = getDefaultCountry();
        }
        $listCache = S(array('prefix'=>'tool_sticker'));
        $cacheKey = 'tool_sticker_' . $country;
        $result = $listCache -> $cacheKey;
        if ($result == false) {
            $musicModel = M('sticker');
            $where = array("country" => $country);
            $result = $musicModel -> where($where) -> order('id desc') -> field('title,thumb,duration,url') -> select();
            $listCache -> $cacheKey = $result;
        }
        if ($result === false) {
            return show_output(400, "");
        } else {
            return show_output(200, "", buildStickerResourceUrl($result));
        }
    }

}
?>