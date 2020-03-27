<?php
namespace app\base\model;

use think\Model;
use app\base\model\Power as PowerModel;

class Power extends Model
{
    protected $auto = [];
    protected $insert = ['code', 'status', 'cid', 'ctime'];
    protected $update = [];

    protected function setCodeAttr($value)
    {
        if ($value == '') {
            $value = createCode(new PowerModel, "T", "ym", 4);
        }

        return $value;
    }

    protected function setStatusAttr($value)
    {
        return $value == '' ? 2 : $value;
    }

    protected function setCidAttr($value)
    {
        return $value == '' ? session('login_id') : $value;
    }

    protected function setCtimeAttr($value)
    {
        return $value == '' ? date('Y-m-d H:i:s', time()) : $value;
    }
}