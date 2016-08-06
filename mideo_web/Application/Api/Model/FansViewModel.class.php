<?php
namespace Api\Model;
use Think\Model\ViewModel;
class FansViewModel extends ViewModel {
    public $viewFields = array(
        'attention' => array('id', 'fromuserid', 'touserid', 'relation'),
        'user'=>array('id' => 'userid', 'username', 'avatar', 'status', '_on' => 'attention.fromuserid=user.id')
    );
}

?>