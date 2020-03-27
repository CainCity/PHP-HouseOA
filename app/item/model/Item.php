<?php
namespace app\item\model;

use think\Model;

class Item extends Model
{
    protected $auto = ['uid', 'utime'];
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

    protected function setUidAttr($value)
    {
        return $value == '' ? session('login_id') : $value;
    }

    protected function setUtimeAttr($value)
    {
        return $value == '' ? date('Y-m-d H:i:s', time()) : $value;
    }
}