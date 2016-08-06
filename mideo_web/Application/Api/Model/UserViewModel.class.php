<?php
namespace Api\Model;
use Think\Model\ViewModel;
class UserViewModel extends ViewModel {
   	public $viewFields = array(
    	'user'=>array('id', 'username', 'avatar', 'motto', 'status', 'addtime', 'lastlogintime'),
    	'attention' => array('COUNT(attention.id)' => 'attentioncount', '_on' => 'user.id=attention.fromuserid')
   	);
}

?>