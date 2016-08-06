<?php
namespace Home\Model;
use Think\Model\ViewModel;
class VideoViewModel extends ViewModel {
   	public $viewFields = array(
    	'video'=>array('id','userid', 'cateid', 'topicid', 'country', 'description', 'thumb', 'filepath', 'duration', 'size', 'createtime', 'updatetime','commentcount', 'likecount', 'playcount', 'status', 'ishot','showorder', 'orderfixed', 'istopic'),
    	//'cate'=>array('name'=>'catename', '_on'=>'video.cateid=cate.id'),
    	'user'=>array('username', '_on'=>'video.userid=user.id'),
   	);


   	public function getRecord($id,$type=1){
   		$recordModel = M('record_video');
		  $where['video_id'] 	= $id;
   		$where['type'] 		= $type;
        $ret = $recordModel ->field('count(*) as num')->where($where)->find();
        return $ret['num'];
   	}
}

?>