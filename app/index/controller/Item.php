<?php
namespace app\index\controller;

// 项目功能入口
class Item extends Auth
{
    protected $msg = '您的版本尚未开通项目功能权限，请联系管理员！';

    public function _empty($name) {
        echo $this->msg;
    }

    // +----------------------------------------------------------------------
    // | 基础数据
    // +----------------------------------------------------------------------

    // region 项目信息 Item()
    public function Item()
    {
        if (method_exists('app\item\controller\Item', 'index')) {
            $controller = controller('item/Item', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    public function ItemAdd()
    {
        if (method_exists('app\item\controller\Item', 'add')) {
            $controller = controller('item/Item', 'controller');
            return $controller->add();
        } else {
            echo $this->msg;
        }
    }

    public function ItemEdit()
    {
        if (method_exists('app\item\controller\Item', 'edit')) {
            $controller = controller('item/Item', 'controller');
            return $controller->edit();
        } else {
            echo $this->msg;
        }
    }

    public function ItemChangeStatus()
    {
        if (method_exists('app\item\controller\Item', 'changeStatus')) {
            $controller = controller('item/Item', 'controller');
            return $controller->changeStatus();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 详细信息 ItemDetailed()
    public function ItemDetailed() {
        if (method_exists('app\web\controller\Item', 'index')) {
            $controller = controller('web/Item', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    public function ItemDetailedEdit() {
        if (method_exists('app\web\controller\Item', 'edit')) {
            $controller = controller('web/Item', 'controller');
            return $controller->edit();
        } else {
            echo $this->msg;
        }
    }

    public function ItemDetailedImageAdd() {
        if (method_exists('app\web\controller\Item', 'image_add')) {
            $controller = controller('web/Item', 'controller');
            return $controller->image_add();
        } else {
            echo $this->msg;
        }
    }

    public function ItemDetailedImageRef() {
        if (method_exists('app\web\controller\Item', 'image_ref')) {
            $controller = controller('web/Item', 'controller');
            return $controller->image_ref();
        } else {
            echo $this->msg;
        }
    }

    public function ItemDetailedImageDel() {
        if (method_exists('app\web\controller\Item', 'image_del')) {
            $controller = controller('web/Item', 'controller');
            return $controller->image_del();
        } else {
            echo $this->msg;
        }
    }

    public function ItemDetailedRefStaticHtml() {
        if (method_exists('app\web\controller\Item', 'refStaticHtml')) {
            $controller = controller('web/Item', 'controller');
            return $controller->refStaticHtml();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    public function Index() {
        if (method_exists('app\web\controller\Index', 'index')) {
            $controller = controller('web/Index', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    // +----------------------------------------------------------------------
    // | 其它
    // +----------------------------------------------------------------------

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