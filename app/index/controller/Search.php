<?php
namespace app\index\controller;

// 查询弹出窗口功能入口
class Search extends Auth
{
    protected $msg = '您的版本尚未开通项目功能权限，请联系管理员！';

    public function _empty($name) {
        echo $this->msg;
    }

    // +----------------------------------------------------------------------
    // | 查询弹出窗体
    // +----------------------------------------------------------------------

    // 用户
    public function User() {
        if (method_exists('app\search\controller\User', 'index')) {
            $controller = controller('search/User', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    // 交易
    public function Transaction() {
        if (method_exists('app\search\controller\Transaction', 'index')) {
            $controller = controller('search/Transaction', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    // 会计科目
    public function AssetType() {
        if (method_exists('app\search\controller\AssetType', 'index')) {
            $controller = controller('search/AssetType', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    // 客户跟进
    public function Followup() {
        if (method_exists('app\search\controller\Followup', 'index')) {
            $controller = controller('search/Followup', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    // ICON
    public function Icon() {
        if (method_exists('app\search\controller\Icon', 'index')) {
            $controller = controller('search/Icon', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    // 项目
    public function Item() {
        if (method_exists('app\search\controller\Item', 'index')) {
            $controller = controller('search/Item', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    // 合作企业
    public function Company() {
        if (method_exists('app\search\controller\Company', 'index')) {
            $controller = controller('search/Company', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }
}