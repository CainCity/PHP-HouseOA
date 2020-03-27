<?php
namespace app\common\validate;

use think\Validate;

class Followup extends Validate
{
    protected $rule = [
        'hid' => 'require',
        'fid' => 'require'
    ];
    
    protected $message = [
        'hid.require' => '跟进客户必须填写',
        'fid.require' => '跟进人必须填写'
    ];

    protected $scene = [
        'add' => ['hid'],
        'edit' => ['hid']
    ];
}