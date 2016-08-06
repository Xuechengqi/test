<?php
namespace Api\Model;
use Think\Model\ViewModel;
class FollowsViewModel extends ViewModel {
    public $viewFields = array(
        'attention' => array('id', 'fromuserid', 'touserid', 'relation'),
        'user'=>array('id' => 'userid', 'username', 'avatar', 'status', '_on' => 'attention.touserid=user.id')
    );
}

?>