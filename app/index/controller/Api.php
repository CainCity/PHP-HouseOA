<?php
namespace app\index\controller;

use think\Controller;

// 接口入口
class Api extends Controller
{
    protected $msg = '您的版本尚未开通客户功能权限，请联系管理员！';

    public function _empty($name)
    {
        echo $this->msg;
    }

    // region 提交客户 Customer()
    public function Customer()
    {
        if (method_exists('app\api\controller\Customer', 'index')) {
            $controller = controller('api/Customer', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    // region 访问记录 Visit()
    public function Visit()
    {
        if (method_exists('app\api\controller\Visit', 'index')) {
            $controller = controller('api/Visit', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

}