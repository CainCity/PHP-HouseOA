<?php
namespace app\base\model;

use think\Model;

class Organizational extends Model{
    protected $auto = ['uid', 'utime'];
    protected $insert = ['cid', 'status', 'ctime'];
    protected $update = [];

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

    protected function setUidAttr($value)
    {
        return $value == '' ? session('login_id') : $value;
    }

    protected function setUtimeAttr($value)
    {
        return $value == '' ? date('Y-m-d H:i:s', time()) : $value;
    }
}