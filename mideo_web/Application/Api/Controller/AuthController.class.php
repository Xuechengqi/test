<?php
namespace Api\Controller;
use Think\Controller;

class AuthController extends Controller {

	public function gettoken($appid = '', $secret = '') {
		if ($appid == 'hwmideoand' && $secret == 'bf1ec5ae11f8fdf9a95b7') {
			$expire = 3600;
			$tokenCache = S(array('prefix'=>'token', 'expire' => $expire));
			$tokenCacheKey = 'token';
			$result = $tokenCache -> $tokenCacheKey;
			if ($result == false) {
				$token = getRandomStr(32);
				$result = array(
					"access_token" => $token,
					"expires_in" => $expire
				);
				$tokenCache -> $tokenCacheKey = $result;
			}
			return show_output(200, "", $result);
		} else {
			return show_output(101, "params error");
		}
	}

}
?>