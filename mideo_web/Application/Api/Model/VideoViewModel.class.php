<?php
namespace Api\Model;
use Think\Model\ViewModel;
class VideoViewModel extends ViewModel {
   	public $viewFields = array(
    	'video'=>array('id','userid', 'cateid', 'topicid', 'country', 'description', 'thumb', 'filepath', 'duration', 'size', 'likecount', 'commentcount', 'createtime', 'updatetime', 'status', 'ishot','showorder', 'orderfixed', 'istopic'),
    	//'cate'=>array('name'=>'catename', '_on'=>'video.cateid=cate.id'),
    	'user'=>array('username', 'avatar', '_on'=>'video.userid=user.id'),
   	);
}

?>