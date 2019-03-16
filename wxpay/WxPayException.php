<?php
/**
 * Created by PhpStorm.
 * User: lineage
 * Date: 2018-12-25
 * Time: 16:43
 */

namespace linepay\wxpay;

use think\Exception;

class WxPayException extends Exception {
    public function errorMessage()
    {
        return $this->getMessage();
    }
}