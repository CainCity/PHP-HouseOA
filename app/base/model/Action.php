<?php
namespace app\base\model;

use think\Model;

class Action extends Model
{
    protected $auto = [];
    protected $insert = ['cid', 'ctime'];
    protected $update = [];

    protected function setCidAttr($value)
    {
        return $value == '' ? session('login_id') : $value;
    }

    protected function setCtimeAttr($value)
    {
        return $value == '' ? date('Y-m-d H:i:s', time()) : $value;
    }
}