<?php
namespace app\common\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        'loginuser' => 'require',
        'loginpsd' => 'require|alphaDash|length:6,16',
        
        'status' => 'require|number',
        'usercode' => 'require|max:25',
        'username' => 'require|max:25',
        'password' => 'require|alphaDash|length:6,16',
    ];
    
    protected $message = [
        'loginuser.require' => '用户名必须填写',
        
        'loginpsd.require' => '密码必须填写',
        'loginpsd.alphaDash' => '密码只能使用字母、数字、下划线_、破折号-',
        'loginpsd.length' => '密码长度最低不能少于6位，且最大不能超过16位',
        
        'status.require' => '状态必须选择',
        'status.number' => '状态必须为数字',
        
        'usercode.require' => '编号必须选择',
        'usercode.max' => '编号最多不能超过25个字',
        
        'username.require' => '姓名必须填写',
        'username.max' => '姓名最多不能超过25个字',
        
        'password.require' => '密码必须填写',
        'password.alphaDash' => '密码只能使用字母、数字、下划线_、破折号-',
        'password.length' => '密码长度最低不能少于6位，且最大不能超过16位',
    ];
    
    protected $scene = [
        'add' => ['status', 'usercode', 'username', 'password'],
        'edit' => ['status', 'usercode', 'username'],
        'login' => ['loginuser', 'loginpsd'],
        'changepsw' => ['password'],
    ];
}