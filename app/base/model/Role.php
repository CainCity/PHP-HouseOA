<?php
namespace app\base\model;

use think\Model;

class Role extends Model
{
    protected $auto = [];
    protected $insert = ['status', 'cid', 'ctime'];
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
}