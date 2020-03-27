<?php
namespace app\customer\model;

use app\customer\model\Customer as CustomerModel;
use think\Model;

class Customer extends Model
{
    protected $auto = [];
    protected $insert = ['code'];
    protected $update = [];

    protected function setCodeAttr($value)
    {
        if ($value == '') {
            $value = createCode(new CustomerModel, "C", "ymd", 6);
        }

        return $value;
    }
}