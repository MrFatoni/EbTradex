<?php
/**
 * Created by PhpStorm.
 * User: zahid
 * Date: 2018-08-05
 * Time: 5:32 PM
 */

namespace App\Services\Core;


class CommonService
{
    public function customEncode(){
        $code = $this->_code();
    }

    public function customDecode(){
        $code = $this->_code(true);
    }

    private function _code($decode=false){
        return ['o','r','s','t','u','v','w','x','y','z'];
    }
}