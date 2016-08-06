<?php
namespace Home\Model;
use Think\Model\ViewModel;
class CommentViewModel extends ViewModel {
   	public $viewFields = array(
   		'comment' => array('id', 'video_id', 'userid', 'username', 'content', 'createtime', 'status'),
    	'video'=>array('id' => 'videoid', 'cateid', 'description', 'thumb', 'filepath', 'duration', 'size', 'createtime' => 'videocreatetime', 'updatetime','commentcount', 'likecount', 'status' => 'videostatus', '_on' => 'comment.video_id=video.id'),
    	'cate'=>array('name'=>'catename', '_on'=>'video.cateid=cate.id')
   	);
}

?>