<?php
namespace Api\Model;
use Think\Model\ViewModel;
class VideoLikeViewModel extends ViewModel {
   	public $viewFields = array(
    	'vlike' => array('userid', 'videoid','relation', 'createtime' => 'liketime'),
    	'video'=>array('id', 'userid','cateid', 'country', 'description', 'thumb', 'filepath', 'duration', 'size', 'likecount', 'commentcount', 'createtime', 'updatetime', 'status', 'ishot', '_on' => 'vlike.videoid=video.id'),
    	'cate'=>array('name'=>'catename', '_on'=>'video.cateid=cate.id'),
    	'user'=>array('username', 'avatar', '_on'=>'video.userid=user.id')
   	);
}

?>