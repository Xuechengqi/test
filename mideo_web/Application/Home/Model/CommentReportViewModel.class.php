<?php
namespace Home\Model;
use Think\Model\ViewModel;
class CommentReportViewModel extends ViewModel {
   	public $viewFields = array(
   		'report_comment' => array('id', 'commentid', 'userid', 'content', 'createtime'),
   		'comment' => array('video_id', 'userid' => 'commentuserid', 'username' => 'commentusername', 'content' => 'commentcontent', 'createtime' => 'commenttime', 'status', '_on' => 'comment.id=report_comment.commentid'),
    	'video'=>array('id'=> 'videoid', 'status' => 'videostatus', '_on' => 'comment.video_id=video.id'),
    	'user' => array('username', '_on' => 'report_comment.userid=user.id')
   	);
}

?>