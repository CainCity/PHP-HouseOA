<?php
namespace app\index\controller;

// 业务功能入口
class Work extends Auth
{
    protected $msg = '您的版本尚未开通客户功能权限，请联系管理员！';

    public function _empty($name) {
        echo $this->msg;
    }

    // +----------------------------------------------------------------------
    // | 基础数据
    // +----------------------------------------------------------------------

    // region 外勤信息 OutWork()
    public function OutWork()
    {
        if (method_exists('app\work\controller\OutWork', 'index')) {
            $controller = controller('work/OutWork', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    public function OutWorkAdd()
    {
        if (method_exists('app\work\controller\OutWork', 'add')) {
            $controller = controller('work/OutWork', 'controller');
            return $controller->add();
        } else {
            echo $this->msg;
        }
    }

    public function OutWorkEdit()
    {
        if (method_exists('app\work\controller\OutWork', 'edit')) {
            $controller = controller('work/OutWork', 'controller');
            return $controller->edit();
        } else {
            echo $this->msg;
        }
    }


    public function OutWorkPictureAdd()
    {
        if (method_exists('app\work\controller\OutWork', 'picture_add')) {
            $controller = controller('work/OutWork', 'controller');
            return $controller->picture_add();
        } else {
            echo $this->msg;
        }
    }

    public function OutWorkPictureUpload()
    {
        if (method_exists('app\work\controller\OutWork', 'picture_upload')) {
            $controller = controller('work/OutWork', 'controller');
            return $controller->picture_upload();
        } else {
            echo $this->msg;
        }
    }

    public function OutWorkPictureDel()
    {
        if (method_exists('app\work\controller\OutWork', 'picture_del')) {
            $controller = controller('work/OutWork', 'controller');
            return $controller->picture_del();
        } else {
            echo $this->msg;
        }
    }

    public function OutWorkPictureRefresh()
    {
        if (method_exists('app\work\controller\OutWork', 'picture_refresh')) {
            $controller = controller('work/OutWork', 'controller');
            return $controller->picture_refresh();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 交易记录 Transactions()
    public function Transactions()
    {
        if (method_exists('app\work\controller\Transactions', 'index')) {
            $controller = controller('work/Transactions', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    public function TransactionsAdd()
    {
        if (method_exists('app\work\controller\Transactions', 'add')) {
            $controller = controller('work/Transactions', 'controller');
            return $controller->add();
        } else {
            echo $this->msg;
        }
    }

    public function TransactionsEdit()
    {
        if (method_exists('app\work\controller\Transactions', 'edit')) {
            $controller = controller('work/Transactions', 'controller');
            return $controller->edit();
        } else {
            echo $this->msg;
        }
    }


    public function TransactionsLine1()
    {
        if (method_exists('app\work\controller\Transactions', 'line1')) {
            $controller = controller('work/Transactions', 'controller');
            return $controller->line1();
        } else {
            echo $this->msg;
        }
    }

    public function TransactionsLine1Add()
    {
        if (method_exists('app\work\controller\Transactions', 'line1Add')) {
            $controller = controller('work/Transactions', 'controller');
            return $controller->line1Add();
        } else {
            echo $this->msg;
        }
    }

    public function TransactionsLine1Edit()
    {
        if (method_exists('app\work\controller\Transactions', 'line1Edit')) {
            $controller = controller('work/Transactions', 'controller');
            return $controller->line1Edit();
        } else {
            echo $this->msg;
        }
    }

    public function TransactionsLine1Del()
    {
        if (method_exists('app\work\controller\Transactions', 'line1Del')) {
            $controller = controller('work/Transactions', 'controller');
            return $controller->line1Del();
        } else {
            echo $this->msg;
        }
    }


    public function TransactionsLine2()
    {
        if (method_exists('app\work\controller\Transactions', 'line2')) {
            $controller = controller('work/Transactions', 'controller');
            return $controller->line2();
        } else {
            echo $this->msg;
        }
    }

    public function TransactionsLine2Add()
    {
        if (method_exists('app\work\controller\Transactions', 'line2Add')) {
            $controller = controller('work/Transactions', 'controller');
            return $controller->line2Add();
        } else {
            echo $this->msg;
        }
    }

    public function TransactionsLine2Edit()
    {
        if (method_exists('app\work\controller\Transactions', 'line2Edit')) {
            $controller = controller('work/Transactions', 'controller');
            return $controller->line2Edit();
        } else {
            echo $this->msg;
        }
    }

    public function TransactionsLine2Del()
    {
        if (method_exists('app\work\controller\Transactions', 'line2Del')) {
            $controller = controller('work/Transactions', 'controller');
            return $controller->line2Del();
        } else {
            echo $this->msg;
        }
    }


    public function TransactionsPictureAdd()
    {
        if (method_exists('app\work\controller\Transactions', 'picture_add')) {
            $controller = controller('work/Transactions', 'controller');
            return $controller->picture_add();
        } else {
            echo $this->msg;
        }
    }

    public function TransactionsPictureUpload()
    {
        if (method_exists('app\work\controller\Transactions', 'picture_upload')) {
            $controller = controller('work/Transactions', 'controller');
            return $controller->picture_upload();
        } else {
            echo $this->msg;
        }
    }

    public function TransactionsPictureDel()
    {
        if (method_exists('app\work\controller\Transactions', 'picture_del')) {
            $controller = controller('work/Transactions', 'controller');
            return $controller->picture_del();
        } else {
            echo $this->msg;
        }
    }

    public function TransactionsPictureRefresh()
    {
        if (method_exists('app\work\controller\Transactions', 'picture_refresh')) {
            $controller = controller('work/Transactions', 'controller');
            return $controller->picture_refresh();
        } else {
            echo $this->msg;
        }
    }
    // endregion

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