<?php
namespace app\base\model;

use think\Model;

class City extends Model
{
    protected $auto = [];
    protected $insert = ['status'];
    protected $update = [];

    protected function setStatusAttr($value)
    {
        return $value == '' ? 2 : $value;
    }
}