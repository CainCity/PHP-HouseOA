<?php
namespace app\index\controller;

// 客户功能入口
class Customer extends Auth
{
    protected $msg = '您的版本尚未开通客户功能权限，请联系管理员！';

    public function _empty($name) {
        echo $this->msg;
    }

    // +----------------------------------------------------------------------
    // | 基础数据
    // +----------------------------------------------------------------------

    // region 客户信息 Customer()
    public function Customer()
    {
        if (method_exists('app\customer\controller\Customer', 'index')) {
            $controller = controller('customer/Customer', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    public function CustomerAdd()
    {
        if (method_exists('app\customer\controller\Customer', 'add')) {
            $controller = controller('customer/Customer', 'controller');
            return $controller->add();
        } else {
            echo $this->msg;
        }
    }

    public function CustomerEdit()
    {
        if (method_exists('app\customer\controller\Customer', 'edit')) {
            $controller = controller('customer/Customer', 'controller');
            return $controller->edit();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 报表：跟进记录 Followup()
    public function Followup()
    {
        if (method_exists('app\customer\controller\Followup', 'index')) {
            $controller = controller('customer/Followup', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 信息：变更规则 ChangeRule()
    public function ChangeRule()
    {
        if (method_exists('app\customer\controller\ChangeRule', 'index')) {
            $controller = controller('customer/ChangeRule', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    public function ChangeRuleAdd()
    {
        if (method_exists('app\customer\controller\ChangeRule', 'add')) {
            $controller = controller('customer/ChangeRule', 'controller');
            return $controller->add();
        } else {
            echo $this->msg;
        }
    }

    public function ChangeRuleEdit()
    {
        if (method_exists('app\customer\controller\ChangeRule', 'edit')) {
            $controller = controller('customer/ChangeRule', 'controller');
            return $controller->edit();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // +----------------------------------------------------------------------
    // | 其它
    // +----------------------------------------------------------------------

    // 电话号码
    public function getTel() {
        if (method_exists('app\customer\controller\Customer', 'getTel')) {
            $controller = controller('customer/Customer', 'controller');
            return $controller->getTel();
        } else {
            echo $this->msg;
        }
    }

    // 省级信息
    public function getProvince() {
        if (method_exists('app\base\controller\Province', 'getProvince')) {
            $controller = controller('base/Province', 'controller');
            return $controller->getProvince();
        } else {
            echo $this->msg;
        }
    }

    // 市级信息
    public function getCity() {
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
}