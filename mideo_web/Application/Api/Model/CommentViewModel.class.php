<?php
namespace Api\Model;
use Think\Model\ViewModel;
class CommentViewModel extends ViewModel {
    public $viewFields = array(
        'comment' => array('id', 'video_id' , 'userid', 'content', 'createtime', 'status'),
        'user'=>array('username', 'avatar', '_on'=>'user.id=comment.userid'),
    );
}

?>