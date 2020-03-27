<?php
namespace app\customer\model;

use app\customer\model\Followup as FollowupModel;
use think\Model;

class Followup extends Model
{
    protected $auto = ['uid', 'utime'];
    protected $insert = ['code', 'is_update', 'cid', 'ctime'];
    protected $update = [];

    protected function setCodeAttr($value)
    {
        if ($value == '') {
            $value = createCode(new FollowupModel, "CF", "ymd", 6);
        }

        return $value;
    }

    protected function setIs_UpdateAttr($value)
    {
        return $value == '' ? 1 : $value;
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