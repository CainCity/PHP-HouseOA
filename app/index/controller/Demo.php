<?php
namespace app\index\controller;

// 测试功能入口
class Demo extends Auth
{
    protected $msg = '您的版本尚未开通客户功能权限，请联系管理员！';

    public function _empty($name) {
        echo $this->msg;
    }

    // +----------------------------------------------------------------------
    // | 基础数据
    // +----------------------------------------------------------------------

    // Index()
    public function Index()
    {
        if (method_exists('app\demo\controller\Index', 'index')) {
            $controller = controller('demo/Index', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    // IndexAction()
    public function IndexAction()
    {
        if (method_exists('app\demo\controller\Index', 'action')) {
            $controller = controller('demo/Index', 'controller');
            return $controller->action();
        } else {
            echo $this->msg;
        }
    }

    // IndexExcel()
    public function IndexExcel()
    {
        if (method_exists('app\demo\controller\Index', 'excel')) {
            $controller = controller('demo/Index', 'controller');
            return $controller->excel();
        } else {
            echo $this->msg;
        }
    }

    // 批量新增客户 InsetCustomers()
    public function InsetCustomers()
    {
        if (method_exists('app\demo\controller\InsetCustomers', 'index')) {
            $controller = controller('demo/InsetCustomers', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    // +----------------------------------------------------------------------
    // | 其它
    // +----------------------------------------------------------------------

    // 市级信息
    public function getCity()
    {
        if (method_exists('app\base\controller\City', 'getCity')) {
            $controller = controller('base/City', 'controller');
            return $controller->getCity();
        } else {
            echo $this->msg;
        }
    }

    // 区级信息
    public function getDistrict() {
        if (method_exists('app\base\controller\District', 'getDistrict')) {
            $controller = controller('base/District', 'controller');
            return $controller->getDistrict();
        } else {
            echo $this->msg;
        }
    }

    // 用户信息
    public function getUser()
    {
        if (method_exists('app\base\controller\User', 'getUser')) {
            $controller = controller('base/User', 'controller');
            return $controller->getUser();
        } else {
            echo $this->msg;
        }
    }
}