<?php
namespace app\base\model;

use think\Model;

class User extends Model
{
    //登陆
    public function login($data)
    {
        // 从模型中获取用户名和密码
        $username = $data['username'];
        $password = md5($data['password']);

        // 先查询这个用户名是否存在
        $where = "(usercode = '{$username}' or username = '{$username}') and status = 2";
        $user = $this->where($where)->find();
        if (!empty($user)) {
            if ($user['password'] == $password) {
                if ($user['status'] == 2) {
                    //保存到session
                    session('login_id', $user['id'], 'think');
                    session('login_code', $user['usercode'], 'think');
                    session('login_name', $user['username'], 'think');
                    session('login_nickname', $user['nickname'], 'think');
                    session('login_password', $user['password'], 'think');
                    session('login_team', $user['teamid'], 'think');
                    session('login_sale_team', existSaleTeam(session('login_team')), 'think');
                    session('login_customer_org', getUserOrg(session('login_id')), 'think');

                    return TRUE;
                } else {
                    $this->error = '当前用户已被禁用！';
                    return FALSE;
                }
            } else {
                $this->error = '密码不正确！';
                return FALSE;
            }
        } else {
            $this->error = '登录名不存在！';
            return FALSE;
        }
    }

    protected $auto = [];
    protected $insert = ['status', 'password', 'cid', 'ctime'];
    protected $update = [];

    protected function setStatusAttr($value)
    {
        return $value == '' ? 2 : $value;
    }

    protected function setPasswordAttr($value)
    {
        return $value == '' ? md5('123Qwe') : $value;
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