<?php
namespace app\index\controller;

use app\base\model\User as UserModel;
use think\Controller;

// 后台通用验证
class Auth extends Controller
{
    protected $msg = '您的版本尚未开通客户功能权限，请联系管理员！';

    protected function _empty($name) {
        echo $this->msg;
    }

    protected function _initialize()
    {
        //session不存在时，不允许直接访问
        if (!session('?login_id')) {
            $this->error('还没有登录，正在跳转到登录页', 'login/index');
        }

        $this->auth_check();
    }

    protected function auth_check()
    {
        //密码校验
        if (config('extra.auth_password_check')) {
            $this->auth_password_check();
        }

        //过期时间校验
        if (config('extra.auth_expired_check')) {
            $this->auth_expired_check();
        }
    }

    /**
     * [auth_password_check 动态密码校验]
     * 应用场景：修改密码后，使其它地方登录的账号进行操作时使账号失效
     */
    protected function auth_password_check()
    {
        $user = new UserModel;
        $where_query = array(
            'id' => session('login_id'),
        );
        $user = $user->where($where_query)->find();
        if ($user) {
            if ($user['password'] != session('login_password')) {
                //注销当前账号
                //session(null, 'think');

                //$this->error('登录失效:用户密码已更改', 'login/index');
            }

            if ($user['status'] != 2) {
                //注销当前账号
                session(null, 'think');

                $this->error('登录失效:用户已被禁用', 'login/index');
            };
        } else {
            //注销当前账号
            session(null, 'think');

            $this->error('登录失效:非法登录', 'login/index');
        }
    }

    /**
     * [auth_expired_check 登录时间校验]
     * 应用场景：主要是在他人电脑上登陆后，忘了登出
     */
    protected function auth_expired_check()
    {
        $user = new UserModel;
        $where_query = array(
            'username' => session('admin_username'),
            'password' => session('admin_password'),
            'status' => 1
        );
        $user = $user->where($where_query)->find();
        if ((time() > strtotime($user->expire_time))) { //登录超时
            //注销当前账号
            session(null, 'think');

            $this->error('账号已过期', 'login/index');
        }
    }
}