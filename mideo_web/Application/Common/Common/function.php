<?php
    /**
     * 网站基础url（移除脚本路径）
     * @param  boolean $absolute [description]
     * @return [type]            [description]
     */
    function getBaseUrl($absolute=true){
        $baseUrl=rtrim(dirname(getScriptUrl()),'\\/');
        return $absolute ? getHostInfo() . $baseUrl : $baseUrl;
    }
    /**
     * 获取当前主机
     * @return string 主机字符串
     */
    function getHostInfo(){
        if(!empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'],'off'))
            $http='https';
        else
            $http='http';
        if(isset($_SERVER['HTTP_HOST']))
            $hostInfo=$http.'://'.$_SERVER['HTTP_HOST'];
        else
        {
            $hostInfo=$http.'://'.$_SERVER['SERVER_NAME'];
            $port=isset($_SERVER['SERVER_PORT']) ? (int)$_SERVER['SERVER_PORT'] : 80;
            if($port!==80)
                $hostInfo.=':'.$port;
        }
        return $hostInfo;
    }
    /**
     * 当前脚本url
     * @return [type] [description]
     */
    function getScriptUrl(){
        $scriptName=basename($_SERVER['SCRIPT_FILENAME']);
        if(basename($_SERVER['SCRIPT_NAME'])===$scriptName)
            $scriptUrl=$_SERVER['SCRIPT_NAME'];
        elseif(basename($_SERVER['PHP_SELF'])===$scriptName)
            $scriptUrl=$_SERVER['PHP_SELF'];
        elseif(isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME'])===$scriptName)
            $scriptUrl=$_SERVER['ORIG_SCRIPT_NAME'];
        elseif(($pos=strpos($_SERVER['PHP_SELF'],'/'.$scriptName))!==false)
            $scriptUrl=substr($_SERVER['SCRIPT_NAME'],0,$pos).'/'.$scriptName;
        elseif(isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'],$_SERVER['DOCUMENT_ROOT'])===0)
            $scriptUrl=str_replace('\\','/',str_replace($_SERVER['DOCUMENT_ROOT'],'',$_SERVER['SCRIPT_FILENAME']));
        else
            die("ERROR");
        return $scriptUrl;
    }

    function getBaseUrl_old() {
        return "http://mideo.local.com";
        //return "http://mideo.thehotapps.com";
    }

    function getApiBaseUrl() {
        return getBaseUrl() . '/api.php';
    }

    function getUploadResourceBaseUrl() {
        $baseUrl = getBaseUrl();
        if (strpos($baseUrl, "mideo.cc") === FALSE) {
            return $baseUrl . "/uploads/";
        }
        return 'http://uploads.mideo.cc/';
        //return getBaseUrl() . '/uploads/';
    }

    function getStickerResourceBaseUrl() {
        return getUploadResourceBaseUrl() . 'sticker/';
    }

    function getSplashResourceBaseUrl() {
        return getUploadResourceBaseUrl() . 'splash/';
    }

    function getAvatarResourceBaseUrl() {
        return getUploadResourceBaseUrl() . 'avatar/';
    }

    function getDefaultUserPhoto() {
        return getAvatarResourceBaseUrl() . 'default.png';
    }

    function getRandomStr($length = 32) {
        $token = "";
        $codeAlphabet = "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        for($i=0;$i<$length;$i++){
            $token .= $codeAlphabet[crypto_rand_secure(0,strlen($codeAlphabet))];
        }
        return $token;
    }

    function crypto_rand_secure($min, $max) {
        $range = $max - $min;
        if ($range < 0) return $min; // not so random...
        $log = log($range, 2);
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
    }

    function formatBytes($size) {
        $units = array(' KB', ' MB', ' GB', ' TB');
        for ($i = 0; $size >= 1024 && $i < 4; $i++) {
            $size /= 1024;
        }
        return round($size, 2).$units[$i];
    }

    function cutStr($str = '', $len = 0) {
        if (empty($str)) {
            return $str;
        }
        if ($len == 0) {
            return $str;
        }
        return mb_substr($str, 0, $len, "utf-8");
    }

    //unicode码转utf8
    function unicode2utf8($str){
        if(!$str)
            return $str;
        $decode = json_decode($str);
        if($decode)
            return $decode;
        $str = '["' . $str . '"]';
        $decode = json_decode($str);
        if(count($decode) == 1){
            return $decode[0];
        }
        return $str;
    }
    /**
     * utf-8 转unicode
     *
     * @param string $name
     * @return string
     */
    function utf82unicode($name){
        $name = iconv('UTF-8', 'UCS-2', $name);
        $len  = strlen($name);
        $str  = '';
        for ($i = 0; $i < $len - 1; $i = $i + 2){
            $c  = $name[$i];
            $c2 = $name[$i + 1];
            if (ord($c) > 0){   //两个字节的文字
                $str .= '\u'.base_convert(ord($c), 10, 16).str_pad(base_convert(ord($c2), 10, 16), 2, 0, STR_PAD_LEFT);
                //$str .= base_convert(ord($c), 10, 16).str_pad(base_convert(ord($c2), 10, 16), 2, 0, STR_PAD_LEFT);
            } else {
                $str .= '\u'.str_pad(base_convert(ord($c2), 10, 16), 4, 0, STR_PAD_LEFT);
                //$str .= str_pad(base_convert(ord($c2), 10, 16), 4, 0, STR_PAD_LEFT);
            }
        }
        $str = strtoupper($str);//转换为大写
        return $str;
    }

    /**
     * utf-8 转unicode
     *
     * @param string $name
     * @return string
     */
    function utf8_unicode($name){
        $name = iconv('UTF-8', 'UCS-2', $name);
        $len  = strlen($name);
        $str  = '';
        for ($i = 0; $i < $len - 1; $i = $i + 2){
            $c  = $name[$i];
            $c2 = $name[$i + 1];
            if (ord($c) > 0){   //两个字节的文字
                $str .= '\u'.base_convert(ord($c), 10, 16).str_pad(base_convert(ord($c2), 10, 16), 2, 0, STR_PAD_LEFT);
                //$str .= base_convert(ord($c), 10, 16).str_pad(base_convert(ord($c2), 10, 16), 2, 0, STR_PAD_LEFT);
            } else {
                $str .= '\u'.str_pad(base_convert(ord($c2), 10, 16), 4, 0, STR_PAD_LEFT);
                //$str .= str_pad(base_convert(ord($c2), 10, 16), 4, 0, STR_PAD_LEFT);
            }
        }
        $str = strtoupper($str);//转换为大写
        return $str;
    }

    /**
     * unicode 转 utf-8
     *
     * @param string $name
     * @return string
     */
    function unicode_decode($name)
    {
        $name = strtolower($name);
        // 转换编码，将Unicode编码转换成可以浏览的utf-8编码
        $pattern = '/([\w]+)|(\\\u([\w]{4}))/i';
        preg_match_all($pattern, $name, $matches);
        if (!empty($matches))
        {
            $name = '';
            for ($j = 0; $j < count($matches[0]); $j++)
            {
                $str = $matches[0][$j];
                if (strpos($str, '\\u') === 0)
                {
                    $code = base_convert(substr($str, 2, 2), 16, 10);
                    $code2 = base_convert(substr($str, 4), 16, 10);
                    $c = chr($code).chr($code2);
                    $c = iconv('UCS-2', 'UTF-8', $c);
                    $name .= $c;
                }
                else
                {
                    $name .= $str;
                }
            }
        }
        return $name;
    }





    function gen_uuid() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,

            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

    function getCurrentPageURL() {
        $pageURL = 'http';
        if (isset($_SERVER['HTTPS']) && $_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    function getCurrentPagePostParams() {
        $result = "";
        foreach ($_POST as $param_name => $param_val) {
            $result .= "       $param_name:$param_val\n";
        }
        return $result;
    }

    /*
    *按综合方式输出数据
    **/
    function show_output($code, $message = '', $data = array(), $type = '', $cb = '') {
        if ((!isset($type) || empty($type)) && isset($_GET['format'])) {
            $type = $_GET['format'];
        }
        if ($code !== 200) {
            \Think\Log::record("code:" . $code . "\nmessage:" . $message . "\nparams:\n" . getCurrentPagePostParams() );
        }
        if ($type == "xml") {
            xml_output($code, $message, $data);
        } else if ($type === "jsonp") {
            jsonp_output($code, $message, $data, $cb);
        } else {
            json_output($code, $message, $data);
        }
    }

    function jsonp_output($code, $message = "", $data = array(), $cb) {
        if (!is_numeric($code)) {
            $code = 101;
            $message = '状态码有误';
        }
        $response = array(
            "code" => $code,
            "message" => $message,
            "data" => $data
        );
        header('Content-type: application/json');
        echo $cb . '(' . json_encode($response) . ');';
        exit();
    }

    /*
    *按JSON方式输出数据
    **/
    function json_output($code, $message = '', $data = array()) {
        if (!is_numeric($code)) {
            $code = 101;
            $message = '状态码有误';
        }
        $response = array(
            "code" => $code,
            "message" => $message,
            "data" => $data
        );
        header('Content-type: application/json');
        echo json_encode($response);
        exit();
    }

    /*
    *按XML方式输出数据
    **/
    function xml_output($code, $message = '', $data = array()) {
        if (!is_numeric($code)) {
            $code = 101;
            $message = '状态码有误';
        }
        $response = array(
            "code" => $code,
            "message" => $message,
            "data" => $data
        );
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>';
        $xml .= '<root>';
        $xml .= array2xml($response);
        $xml .= '</root>';
        header('Content-type: text/xml');
        echo $xml;
        exit();
    }

    function array2xml($arrData) {
        $xml = "";
        $attr = "";
        foreach ($arrData as $key => $value) {
            if (is_numeric($key)) {
                $attr = " id=\"{$key}\"";
                $key = "item";
            }

            $xml .= "<{$key}{$attr}>";
            if (is_array($value)) {
                $xml .= array2xml($value);
            } else {
                $xml .= $value;
            }
            $xml .= "</{$key}>";
        }
        return $xml;
    }

    function getCacheDir() {
        return dirname(__FILE__) . '/cache/';
    }

    /*
    *生成缓存
    */
    function cacheData($key, $value = '') {
        $filename = getCacheDir() . $key.'.txt';
        if (is_null($value)) {
            return unlink($filename);
        }
        if (!empty($value)) {
            $dir = dirname($filename);
            if (!is_dir($dir)) {
                mkdir($dir, 0777);
            }
            return file_put_contents($filename, json_encode($value));
        }

        if (!is_file($filename)) {
            return FALSE;
        } else {
            return json_decode(file_get_contents($filename), true);
        }
    }

    function getRecordViewType($num) {
        $allType = recordVideoTypeHash();
        foreach ($allType as $key => $value) {
            if ($value === $num) {
                return $key;
            }
        }
        return "unknown";
    }

    function recordVideoTypeHash() {
        return array(
            "view" => 1,
            "play" => 2
        );
    }

    function getMessageEventType($type) {
        $allType = getAllMessageEventType();
        foreach ($allType as $key => $value) {
            if ($key == $type) {
                return $value;
            }
        }
        return $allType['unknown'];
    }

    function getMessageEventName($num) {
        $allType = getAllMessageEventType();
        foreach ($allType as $key => $value) {
            if ($value == $num) {
                return $key;
            }
        }
        return 'unknown';
    }

    function getAllMessageEventType() {
        return array(
            "system" => 1,
            "like" => 2,
            "attention" => 3,
            "comment" => 4,
            "at" => 5,
            "unknown" => 6
        );
    }

    function getDefaultCountry() {
        return "BR";
    }

    function checkCountry(&$country) {
        $country = strtoupper($country);
        return in_array($country, getAllowedCountry());
    }

    function getAllowedCountry() {
        $allCountry = getAllCountry();
        $allowedCountry = array();
        foreach ($allCountry as $key => $value) {
            if ($key !== 'AA') {
                $allowedCountry[] = $key;
            }
        }
        return $allowedCountry;
    }

    function getCountryName($n) {
        return getAllCountry()[$n];
    }

    function getAllCountry() {
        return array(
            "AA" => "ALL",
            "BR" => "Brazil",
            "US" => "United States of America",
            "GB" => "United Kiongdom",
            "KR" => "Korea",
            "ES" => "Spain",
            "PT" => "Portugal",
            "CN" => "China",
            "IN" => "India"
        );
    }

/*
    function getAllCountry() {
        return array(
            "AA" => "ALL",
            "AD" => "Andorra",
            "AE" => "Arab Emirates",
            "AF" => "Afghanistan",
            "AG" => "Antigua and Barbuda",
            "AI" => "Anguilla",
            "AL" => "Albania",
            "AM" => "Armenia",
            "AO" => "Angola",
            "AR" => "Argentina",
            "AT" => "Austria",
            "AU" => "Australia",
            "AZ" => "Azerbaijan",
            "BB" => "Barbados",
            "BD" => "Bangladesh",
            "BE" => "Belgium",
            "BF" => "Burkina-faso",
            "BG" => "Bulgaria",
            "BH" => "Bahrain",
            "BI" => "Burundi",
            "BJ" => "Benin",
            "BL" => "Palestine",
            "BM" => "Bermuda Is.",
            "BN" => "Brunei",
            "BO" => "Bolivia",
            "BR" => "Brazil",
            "BS" => "Bahamas",
            "BW" => "Botswana",
            "BY" => "Belarus",
            "BZ" => "Belize",
            "CA" => "Canada",
            "CF" => "Central African Republic",
            "CG" => "Congo",
            "CH" => "Switzerland",
            "CK" => "Is.",
            "CL" => "Chile",
            "CM" => "Cameroon",
            "CN" => "China",
            "CO" => "Colombia",
            "CR" => "Costa Rica",
            "CS" => "Czech",
            "CU" => "Cuba",
            "CY" => "Cyprus",
            "CZ" => "Czech Republic",
            "DE" => "Germany",
            "DJ" => "Djibouti",
            "DK" => "Denmark",
            "DO" => "Dominica Rep.",
            "DZ" => "Algeria",
            "EC" => "Ecuador",
            "EE" => "Estonia",
            "EG" => "Egypt",
            "ES" => "Spain",
            "ET" => "Ethiopia",
            "FI" => "Finland",
            "FJ" => "Fiji",
            "FR" => "France",
            "GA" => "Gabon",
            "GB" => "United Kiongdom",
            "GD" => "Grenada",
            "GE" => "Georgia",
            "GF" => "French Guiana",
            "GH" => "Ghana",
            "GI" => "Gibraltar",
            "GM" => "Gambia",
            "GN" => "Guinea",
            "GR" => "Greece",
            "GT" => "Guatemala",
            "GU" => "Guam",
            "GY" => "Guyana",
            "HK" => "Hongkong",
            "HN" => "Honduras",
            "HT" => "Haiti",
            "HU" => "Hungary",
            "ID" => "Indonesia",
            "IE" => "Ireland",
            "IL" => "Israel",
            "IN" => "India",
            "IQ" => "Iraq",
            "IR" => "Iran",
            "IS" => "Iceland",
            "IT" => "Italy",
            "JM" => "Jamaica",
            "JO" => "Jordan",
            "JP" => "Japan",
            "KE" => "Kenya",
            "KG" => "Kyrgyzstan",
            "KH" => "Kampuchea (Cambodia )",
            "KP" => "North Korea",
            "KR" => "Korea",
            "KT" => "Republic of Ivory Coast",
            "KW" => "Kuwait",
            "KZ" => "Kazakstan",
            "LA" => "Laos",
            "LB" => "Lebanon",
            "LC" => "St.Lucia",
            "LI" => "Liechtenstein",
            "LK" => "Lanka",
            "LR" => "Liberia",
            "LS" => "Lesotho",
            "LT" => "Lithuania",
            "LU" => "Luxembourg",
            "LV" => "Latvia",
            "LY" => "Libya",
            "MA" => "Morocco",
            "MC" => "Monaco",
            "MD" => "Republic of",
            "MG" => "Madagascar",
            "ML" => "Mali",
            "MM" => "Burma",
            "MN" => "Mongolia",
            "MO" => "Macao",
            "MS" => "Montserrat Is",
            "MT" => "Malta",
            "MU" => "Mauritius",
            "MV" => "Maldives",
            "MW" => "Malawi",
            "MX" => "Mexico",
            "MY" => "Malaysia",
            "MZ" => "Mozambique",
            "NA" => "Namibia",
            "NE" => "Niger",
            "NG" => "Nigeria",
            "NI" => "Nicaragua",
            "NL" => "Netherlands",
            "NO" => "Norway",
            "NP" => "Nepal",
            "NR" => "Nauru",
            "NZ" => "New Zealand",
            "OM" => "Oman",
            "PA" => "Panama",
            "PE" => "Peru",
            "PF" => "French Polynesia",
            "PG" => "Papua New Cuinea",
            "PH" => "Philippines",
            "PK" => "Pakistan",
            "PL" => "Poland",
            "PR" => "Rico",
            "PT" => "Portugal",
            "PY" => "Paraguay",
            "QA" => "Qatar",
            "RO" => "Romania",
            "RU" => "Russia",
            "SA" => "Saudi Arabia",
            "SB" => "Solomon Is",
            "SC" => "Seychelles",
            "SD" => "Sudan",
            "SE" => "Sweden",
            "SG" => "Singapore",
            "SI" => "Slovenia",
            "SK" => "Slovakia",
            "SL" => "Leone",
            "SM" => "Marino",
            "SN" => "Senegal",
            "SO" => "Somali",
            "SR" => "Suriname",
            "ST" => "Tome and Principe",
            "SV" => "Salvador",
            "SY" => "Syria",
            "SZ" => "Swaziland",
            "TD" => "Chad",
            "TG" => "Togo",
            "TH" => "Thailand",
            "TJ" => "Tajikstan",
            "TM" => "Turkmenistan",
            "TN" => "Tunisia",
            "TO" => "Tonga",
            "TR" => "Turkey",
            "TT" => "Trinidad and Tobago",
            "TW" => "Taiwan",
            "TZ" => "Tanzania",
            "UA" => "Ukraine",
            "UG" => "Uganda",
            "US" => "United States of America",
            "UY" => "Uruguay",
            "UZ" => "Uzbekistan",
            "VC" => "Saint Vincent",
            "VE" => "Venezuela",
            "VN" => "Vietnam",
            "YE" => "Yemen",
            "YU" => "Yugoslavia",
            "ZA" => "South Africa",
            "ZM" => "Zambia",
            "ZR" => "Zaire",
            "ZW" => "Zimbabwe"
        );
    }
    */

?>