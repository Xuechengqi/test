<?php
/**
 * @author ivy <guoxuivy@gmail.com>
 * @copyright Copyright &copy; 2013-2017 Ivy Software LLC
 * @license https://github.com/guoxuivy/ivy/
 * @package framework
 * @link https://github.com/guoxuivy/ivy * @since 1.0
 */
namespace Lang;

class Lang {

    public $data=array();
    //当前语言
    public $country=null;

    public function __construct() {
        $this->setCountry('US');
    }

    public function setCountry($lang="BR") {
        if(!isset($this->data[$lang]) ){
            $file = dirname(__FILE__)."/".$lang.".php";
            if(file_exists($file)){
                $this->data[$lang] = require_once($file);
            }
        }
        $this->country=$lang;
    }


    public function get($key,$country="BR") {
        $this->setCountry($country);
        $res = $this->data[$country][$key];
        if(!$res)
            $res = $this->data['US'][$key];
        return $res;
    }

}