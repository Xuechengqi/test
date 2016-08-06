<?php
namespace Home\Model;
use Think\Model\ViewModel;
class VideoReportViewModel extends ViewModel {
   	public $viewFields = array(
    	'video'=>array('id','userid', 'cateid', 'topicid', 'country', 'description', 'thumb', 'filepath', 'duration', 'size', 'createtime', 'updatetime','commentcount', 'likecount', 'status', 'ishot'),
    	'cate'=>array('name'=>'catename', '_on'=>'video.cateid=cate.id'),
    	'report_video' => array('id'=> 'reportid', 'userid' => 'reportuserid', 'content' => 'reportcontent', 'createtime' => 'reporttime', '_on' => 'video.id=report_video.video_id')
   	);
}

?>