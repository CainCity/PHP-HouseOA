<?php
namespace app\index\controller;

use think\Controller;
use app\base\model\User as UserModel;

// 登录功能
class Login extends Controller
{
    // region 登录界面
    public function index()
    {
        return $this->fetch();
    }
    // endregion

    // region 登录方法
    public function login()
    {
        $loginData = input('post.');
        $result = $this->validate($loginData, 'User.login');
        if (true !== $result) {
            $this->error($result);
        } else {
            $user = new UserModel;

            $data['username'] = $loginData['loginuser'];
            $data['password'] = $loginData['loginpsd'];

            if ($user->login($data)) {
                addAction("登录", 'Web', getIP());
                $this->success('登录成功!', 'Index/index');
                exit;
            } else {
                $description = '【登录名：' . $data['username'] . '】【登录密码：' . $data['password'] . '】';
                addAction("登录失败", 'Web', $description);
                $this->error($user->getError());
            }
        }
    }
    // endregion

    // region 注销登陆方法
    public function logout()
    {
        addAction("注销", 'Web', getIP());

        if (session('?login_id')) {
            session(null, 'think');
            session_destroy();
        }

        $this->error('正在退出...', 'index');
    }
    // endregion
}