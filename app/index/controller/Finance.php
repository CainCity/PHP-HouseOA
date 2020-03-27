<?php
namespace app\index\controller;

// 财务功能入口
class Finance extends Auth
{
    protected $msg = '您的版本尚未开通客户功能权限，请联系管理员！';

    public function _empty($name) {
        echo $this->msg;
    }

    // +----------------------------------------------------------------------
    // | 基础数据
    // +----------------------------------------------------------------------

    // region 信息：合作公司 Company()
    public function Company()
    {
        if (method_exists('app\finance\controller\Company', 'index')) {
            $controller = controller('finance/Company', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    public function CompanyAdd()
    {
        if (method_exists('app\finance\controller\Company', 'add')) {
            $controller = controller('finance/Company', 'controller');
            return $controller->add();
        } else {
            echo $this->msg;
        }
    }

    public function CompanyEdit()
    {
        if (method_exists('app\finance\controller\Company', 'edit')) {
            $controller = controller('finance/Company', 'controller');
            return $controller->edit();
        } else {
            echo $this->msg;
        }
    }

    public function CompanyChangeStatus()
    {
        if (method_exists('app\finance\controller\Company', 'changeStatus')) {
            $controller = controller('finance/Company', 'controller');
            return $controller->changeStatus();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 信息：资金账户 Account()
    public function Account()
    {
        if (method_exists('app\finance\controller\Account', 'index')) {
            $controller = controller('finance/Account', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    public function AccountAdd()
    {
        if (method_exists('app\finance\controller\Account', 'add')) {
            $controller = controller('finance/Account', 'controller');
            return $controller->add();
        } else {
            echo $this->msg;
        }
    }

    public function AccountEdit()
    {
        if (method_exists('app\finance\controller\Account', 'edit')) {
            $controller = controller('finance/Account', 'controller');
            return $controller->edit();
        } else {
            echo $this->msg;
        }
    }

    public function AccountChangeStatus()
    {
        if (method_exists('app\finance\controller\Account', 'changeStatus')) {
            $controller = controller('finance/Account', 'controller');
            return $controller->changeStatus();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 信息：财务科目 AssetType()
    public function AssetType()
    {
        if (method_exists('app\finance\controller\AssetType', 'index')) {
            $controller = controller('finance/AssetType', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    public function AssetTypeAdd()
    {
        if (method_exists('app\finance\controller\AssetType', 'add')) {
            $controller = controller('finance/AssetType', 'controller');
            return $controller->add();
        } else {
            echo $this->msg;
        }
    }

    public function AssetTypeEdit()
    {
        if (method_exists('app\finance\controller\AssetType', 'edit')) {
            $controller = controller('finance/AssetType', 'controller');
            return $controller->edit();
        } else {
            echo $this->msg;
        }
    }

    public function AssetTypeChangeStatus()
    {
        if (method_exists('app\finance\controller\AssetType', 'changeStatus')) {
            $controller = controller('finance/AssetType', 'controller');
            return $controller->changeStatus();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 信息：提成方案 CommissionRate()
    public function CommissionRate()
    {
        if (method_exists('app\finance\controller\CommissionRate', 'index')) {
            $controller = controller('finance/CommissionRate', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    public function CommissionRateAdd()
    {
        if (method_exists('app\finance\controller\CommissionRate', 'add')) {
            $controller = controller('finance/CommissionRate', 'controller');
            return $controller->add();
        } else {
            echo $this->msg;
        }
    }

    public function CommissionRateEdit()
    {
        if (method_exists('app\finance\controller\CommissionRate', 'edit')) {
            $controller = controller('finance/CommissionRate', 'controller');
            return $controller->edit();
        } else {
            echo $this->msg;
        }
    }

    public function CommissionRateChangeStatus()
    {
        if (method_exists('app\finance\controller\CommissionRate', 'changeStatus')) {
            $controller = controller('finance/CommissionRate', 'controller');
            return $controller->changeStatus();
        } else {
            echo $this->msg;
        }
    }


    public function CommissionRateLine()
    {
        if (method_exists('app\finance\controller\CommissionRate', 'line')) {
            $controller = controller('finance/CommissionRate', 'controller');
            return $controller->line();
        } else {
            echo $this->msg;
        }
    }

    public function CommissionRateLineAdd()
    {
        if (method_exists('app\finance\controller\CommissionRate', 'lineAdd')) {
            $controller = controller('finance/CommissionRate', 'controller');
            return $controller->lineAdd();
        } else {
            echo $this->msg;
        }
    }

    public function CommissionRateLineEdit()
    {
        if (method_exists('app\finance\controller\CommissionRate', 'lineEdit')) {
            $controller = controller('finance/CommissionRate', 'controller');
            return $controller->lineEdit();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 信息：任务目标 Target()
    public function Target()
    {
        if (method_exists('app\finance\controller\Target', 'index')) {
            $controller = controller('finance/Target', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    public function TargetAdd()
    {
        if (method_exists('app\finance\controller\Target', 'add')) {
            $controller = controller('finance/Target', 'controller');
            return $controller->add();
        } else {
            echo $this->msg;
        }
    }

    public function TargetEdit()
    {
        if (method_exists('app\finance\controller\Target', 'edit')) {
            $controller = controller('finance/Target', 'controller');
            return $controller->edit();
        } else {
            echo $this->msg;
        }
    }

    public function TargetChangeStatus()
    {
        if (method_exists('app\finance\controller\Target', 'changeStatus')) {
            $controller = controller('finance/Target', 'controller');
            return $controller->changeStatus();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 信息：收支记录 Finance()
    public function Finance()
    {
        if (method_exists('app\finance\controller\Finance', 'index')) {
            $controller = controller('finance/Finance', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    public function FinanceAdd()
    {
        if (method_exists('app\finance\controller\Finance', 'add')) {
            $controller = controller('finance/Finance', 'controller');
            return $controller->add();
        } else {
            echo $this->msg;
        }
    }

    public function FinanceEdit()
    {
        if (method_exists('app\finance\controller\Finance', 'edit')) {
            $controller = controller('finance/Finance', 'controller');
            return $controller->edit();
        } else {
            echo $this->msg;
        }
    }

    public function FinanceChangeStatus()
    {
        if (method_exists('app\finance\controller\Finance', 'changeStatus')) {
            $controller = controller('finance/Finance', 'controller');
            return $controller->changeStatus();
        } else {
            echo $this->msg;
        }
    }


    public function FinanceLine()
    {
        if (method_exists('app\finance\controller\Finance', 'line')) {
            $controller = controller('finance/Finance', 'controller');
            return $controller->line();
        } else {
            echo $this->msg;
        }
    }

    public function FinanceLineAdd()
    {
        if (method_exists('app\finance\controller\Finance', 'lineAdd')) {
            $controller = controller('finance/Finance', 'controller');
            return $controller->lineAdd();
        } else {
            echo $this->msg;
        }
    }

    public function FinanceLineEdit()
    {
        if (method_exists('app\finance\controller\Finance', 'lineEdit')) {
            $controller = controller('finance/Finance', 'controller');
            return $controller->lineEdit();
        } else {
            echo $this->msg;
        }
    }

    public function FinanceLineDel()
    {
        if (method_exists('app\finance\controller\Finance', 'lineDel')) {
            $controller = controller('finance/Finance', 'controller');
            return $controller->lineDel();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 信息：开票信息 Bill()
    public function Bill()
    {
        if (method_exists('app\finance\controller\Bill', 'index')) {
            $controller = controller('finance/Bill', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    public function BillAdd()
    {
        if (method_exists('app\finance\controller\Bill', 'add')) {
            $controller = controller('finance/Bill', 'controller');
            return $controller->add();
        } else {
            echo $this->msg;
        }
    }

    public function BillEdit()
    {
        if (method_exists('app\finance\controller\Bill', 'edit')) {
            $controller = controller('finance/Bill', 'controller');
            return $controller->edit();
        } else {
            echo $this->msg;
        }
    }

    public function BillChangeStatus()
    {
        if (method_exists('app\finance\controller\Bill', 'changeStatus')) {
            $controller = controller('finance/Bill', 'controller');
            return $controller->changeStatus();
        } else {
            echo $this->msg;
        }
    }


    public function BillLine()
    {
        if (method_exists('app\finance\controller\Bill', 'line')) {
            $controller = controller('finance/Bill', 'controller');
            return $controller->line();
        } else {
            echo $this->msg;
        }
    }

    public function BillLineAdd()
    {
        if (method_exists('app\finance\controller\Bill', 'lineAdd')) {
            $controller = controller('finance/Bill', 'controller');
            return $controller->lineAdd();
        } else {
            echo $this->msg;
        }
    }

    public function BillLineEdit()
    {
        if (method_exists('app\finance\controller\Bill', 'lineEdit')) {
            $controller = controller('finance/Bill', 'controller');
            return $controller->lineEdit();
        } else {
            echo $this->msg;
        }
    }


    public function BillPictureAdd()
    {
        if (method_exists('app\finance\controller\Bill', 'picture_add')) {
            $controller = controller('finance/Bill', 'controller');
            return $controller->picture_add();
        } else {
            echo $this->msg;
        }
    }

    public function BillPictureUpload()
    {
        if (method_exists('app\finance\controller\Bill', 'picture_upload')) {
            $controller = controller('finance/Bill', 'controller');
            return $controller->picture_upload();
        } else {
            echo $this->msg;
        }
    }

    public function BillPictureDel()
    {
        if (method_exists('app\finance\controller\Bill', 'picture_del')) {
            $controller = controller('finance/Bill', 'controller');
            return $controller->picture_del();
        } else {
            echo $this->msg;
        }
    }

    public function BillPictureRefresh()
    {
        if (method_exists('app\finance\controller\Bill', 'picture_refresh')) {
            $controller = controller('finance/Bill', 'controller');
            return $controller->picture_refresh();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // +----------------------------------------------------------------------
    // | 其它
    // +----------------------------------------------------------------------

    // 科目(查询方法)
    public function getAssetType()
    {
        if (method_exists('app\finance\controller\AssetType', 'getAssetType')) {
            $controller = controller('finance/AssetType', 'controller');
            return $controller->getAssetType();
        } else {
            echo $this->msg;
        }
    }
}