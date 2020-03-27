<?php
namespace app\base\model;

use think\Model;

class Icon extends Model
{
    protected $auto = [];
    protected $insert = [];
    protected $update = [];

    protected function setCidAttr($value)
    {
        return $value == '' ? session('login_id') : $value;
    }

    protected function setCtimeAttr($value)
    {
        return $value == '' ? date('Y-m-d H:i:s', time()) : $value;
    }

    protected function setUidAttr($value)
    {
        return $value == '' ? session('login_id') : $value;
    }

    protected function setUtimeAttr($value)
    {
        return $value == '' ? date('Y-m-d H:i:s', time()) : $value;
    }
}