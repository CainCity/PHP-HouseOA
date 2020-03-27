<?php
namespace app\index\controller;

use think\Controller;

// 系统任务入口
class SysAction extends Controller
{
    protected $msg = '您的版本尚未开通项目功能权限，请联系管理员！';

    public function _empty($name) {
        echo $this->msg;
    }

    // +----------------------------------------------------------------------
    // | 基础数据
    // +----------------------------------------------------------------------

    // 系统任务 Index()
    public function Index()
    {
        if (method_exists('app\sysAction\controller\Index', 'index')) {
            $controller = controller('sysAction/Index', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }
}