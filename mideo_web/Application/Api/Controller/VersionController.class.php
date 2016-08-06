<?php
namespace Api\Controller;
use Think\Controller;

class VersionController extends Controller {

	public function init() {
		$params = array();
		//$params['app_id'] = isset($_POST['app_id']) ? $_POST['app_id'] : '';
		//$params['version_code'] = isset($_POST['version_code']) ? $_POST['version_code'] : '';
		$params['app_id'] = isset($_GET['app_id']) ? $_GET['app_id'] : '';
		$params['version_code'] = isset($_GET['version_code']) ? $_GET['version_code'] : '';
		if (!is_numeric($params['app_id'])) {
			return show_output(101, "app_id param error");
		}
		if (!is_numeric($params['version_code'])) {
			return show_output(101, "version_code param error");
		}

		$appInfo = $this -> _getApp($params['app_id']);
		if ($appInfo === false) {
			return show_output(404, "app not exist");
		} else {
			$versionUpgradeInfo = $this -> _getVersionUpgrade($params['app_id']);
			if ($versionUpgradeInfo !== false) {
				if (intval($versionUpgradeInfo['versioncode']) > intval($params['version_code'])) {
					$versionUpgradeInfo['is_upload'] = $versionUpgradeInfo['type'];
				}  else {
					$versionUpgradeInfo['is_upload'] = 0;
				}
				return show_output(200, "", $versionUpgradeInfo);
			} else {
				return show_output(404, "verison not exist", $versionUpgradeInfo);
			}
		}

	}

	private function _getApp($id) {
		$appModel = M('app');
		$where = array('id' => $id, 'status' => 1);
		$result = $appModel -> where($where) -> find();
		return $result;
	}

	private function _getVersionUpgrade($appId) {
		$appModel = M('version_upgrade');
		$where = array('appid' => $appId);
		$result = $appModel -> where($where) -> find();
		return $result;
	}
}
?>