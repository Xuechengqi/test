<?php
namespace Api\Model;
use Think\Model;
class VlikeModel extends Model {
	//点赞触发数 ，大于此值则加入到hot中
	const Z_NUM = 0;

	/**
	 * 重写扩展add方法
	 * @param string  $data    [description]
	 * @param array   $options [description]
	 * @param boolean $replace [description]
	 */
	public function add($data='',$options=array(),$replace=false) {
		$result = parent::add($data,$options,$replace);
		if($result!==false){
			$this->_after_add($result);
		}
		return $result;
	}

	public function _after_add($id){
		$self = $this->find($id);
		M("video")->where('id='.$self['videoid'])->setInc('likecount'); // 用户likecount 1
		//$video = M("video")->find($self['videoid']);
		//M("video")->where('id='.$video['id'])->save(array('likecount'=>$video['likecount']+1));
		
		// if($video['ishot']==0 && $video['likecount'] > self::Z_NUM){
		// 	$data['ishot'] = '1';
		// 	$maxOrderWhere = array(
		// 		'ishot' => 1,
		// 		'status' => 1,
		// 		'orderfixed' => 0
		// 	);
		// 	$maxOrder = M("video") -> where($maxOrderWhere) -> max('showorder');
		// 	$data['showorder'] = $maxOrder + 1;
		// 	M("video")->where('id='.$video['id'])->save($data); // 根据条件更新记录
		// }
	}
}

?>