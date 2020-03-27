<?php
namespace app\index\controller;

// 报表功能入口
class Report extends Auth
{
    protected $msg = '您的版本尚未开通报表功能权限，请联系管理员！';

    public function _empty($name) {
        echo $this->msg;
    }

    // +----------------------------------------------------------------------
    // | 首页
    // +----------------------------------------------------------------------

    // region 报表：连续未开单天数统计 notSaleDayStatistics()
    public function notSaleDayStatistics()
    {
        if (method_exists('app\report\controller\Welcome', 'notSaleDayStatistics')) {
            $controller = controller('report/Welcome', 'controller');
            return $controller->notSaleDayStatistics();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 报表：新增跟进统计 newFollowupStatistics()
    public function newFollowupStatistics()
    {
        if (method_exists('app\report\controller\Welcome', 'newFollowupStatistics')) {
            $controller = controller('report/Welcome', 'controller');
            return $controller->newFollowupStatistics();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 报表：新增客户统计 newCustomerStatistics()
    public function newCustomerStatistics()
    {
        if (method_exists('app\report\controller\Welcome', 'newCustomerStatistics')) {
            $controller = controller('report/Welcome', 'controller');
            return $controller->newCustomerStatistics();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 报表：本月新增客户项目统计 nMcis()
    public function nMcis()
    {
        if (method_exists('app\report\controller\Welcome', 'nMcis')) {
            $controller = controller('report/Welcome', 'controller');
            return $controller->nMcis();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // +----------------------------------------------------------------------
    // | 统计中心——财务统计
    // +----------------------------------------------------------------------

    // region 报表：财务结余 GeneralLedger()
    public function GeneralLedger()
    {
        if (method_exists('app\report\controller\GeneralLedger', 'index')) {
            $controller = controller('report/GeneralLedger', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 报表：结佣统计 Transaction()
    public function Transaction()
    {
        if (method_exists('app\report\controller\Transaction', 'index')) {
            $controller = controller('report/Transaction', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 报表：账套月报 AccountSet()
    public function AccountSet()
    {
        if (method_exists('app\report\controller\AccountSet', 'index')) {
            $controller = controller('report/AccountSet', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 报表：财务报表 AccountingSubjects()
    public function AccountingSubjects()
    {
        if (method_exists('app\report\controller\AccountingSubjects', 'index')) {
            $controller = controller('report/AccountingSubjects', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // +----------------------------------------------------------------------
    // | 统计中心——业绩统计
    // +----------------------------------------------------------------------

    # region 报表：月度业绩 MonthPerformance()
    public function MonthPerformance()
    {
        if (method_exists('app\report\controller\MonthPerformance', 'index')) {
            $controller = controller('report/MonthPerformance', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }
    # endregion

    # region 报表：员工提成参考 EmployeeCommission()
    public function EmployeeCommission()
    {
        if (method_exists('app\report\controller\EmployeeCommission', 'index')) {
            $controller = controller('report/EmployeeCommission', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }
    # endregion

    # region 报表：员工无业绩天数 NoPerformanceDay()
    public function NoPerformanceDay()
    {
        if (method_exists('app\report\controller\NoPerformanceDay', 'index')) {
            $controller = controller('report/NoPerformanceDay', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }
    # endregion

    // region 报表：员工收益 EmployeeProfit()
    public function EmployeeProfit()
    {
        if (method_exists('app\report\controller\EmployeeProfit', 'index')) {
            $controller = controller('report/EmployeeProfit', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // +----------------------------------------------------------------------
    // | 统计中心——业务统计
    // +----------------------------------------------------------------------

    // region 报表：未结佣金 OutstandingCommission()
    public function OutstandingCommission()
    {
        if (method_exists('app\report\controller\OutstandingCommission', 'index')) {
            $controller = controller('report/OutstandingCommission', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 报表：员工信息 User()
    public function User()
    {
        if (method_exists('app\report\controller\User', 'index')) {
            $controller = controller('report/User', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 报表：外勤统计 OutWork()
    public function OutWork()
    {
        if (method_exists('app\report\controller\OutWork', 'index')) {
            $controller = controller('report/OutWork', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // +----------------------------------------------------------------------
    // | 统计中心——综合统计
    // +----------------------------------------------------------------------

    # region 报表：流水账 Ledger()
    public function Ledger()
    {
        if (method_exists('app\report\controller\Ledger', 'index')) {
            $controller = controller('report/Ledger', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    public function LedgerList1()
    {
        if (method_exists('app\report\controller\Ledger', 'list1')) {
            $controller = controller('report/Ledger', 'controller');
            return $controller->list1();
        } else {
            echo $this->msg;
        }
    }

    public function LedgerList2()
    {
        if (method_exists('app\report\controller\Ledger', 'list2')) {
            $controller = controller('report/Ledger', 'controller');
            return $controller->list2();
        } else {
            echo $this->msg;
        }
    }

    public function LedgerList3()
    {
        if (method_exists('app\report\controller\Ledger', 'list3')) {
            $controller = controller('report/Ledger', 'controller');
            return $controller->list3();
        } else {
            echo $this->msg;
        }
    }
    # endregion

    // region 报表：综合统计 comprehensive()
    public function comprehensive()
    {
        if (method_exists('app\report\controller\Comprehensive', 'index')) {
            $controller = controller('report/Comprehensive', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    public function comprehensiveList()
    {
        if (method_exists('app\report\controller\Comprehensive', 'list1')) {
            $controller = controller('report/Comprehensive', 'controller');
            return $controller->list1();
        } else {
            echo $this->msg;
        }
    }

    public function comprehensiveListA()
    {
        if (method_exists('app\report\controller\Comprehensive', 'list2')) {
            $controller = controller('report/Comprehensive', 'controller');
            return $controller->list2();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 报表：员工价值统计 EmployeeValue()
    public function EmployeeValue()
    {
        if (method_exists('app\report\controller\EmployeeValue', 'index')) {
            $controller = controller('report/EmployeeValue', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    public function EmployeeValueList()
    {
        if (method_exists('app\report\controller\EmployeeValue', 'list1')) {
            $controller = controller('report/EmployeeValue', 'controller');
            return $controller->list1();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // +----------------------------------------------------------------------
    // | 统计中心——其它统计
    // +----------------------------------------------------------------------

    // region 报表：访问记录 Visit()
    public function Visit()
    {
        if (method_exists('app\report\controller\Visit', 'index')) {
            $controller = controller('report/Visit', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    public function VisitView()
    {
        if (method_exists('app\report\controller\Visit', 'view')) {
            $controller = controller('report/Visit', 'controller');
            return $controller->view();
        } else {
            echo $this->msg;
        }
    }

    public function VisitSubtotal()
    {
        if (method_exists('app\report\controller\Visit', 'subtotal')) {
            $controller = controller('report/Visit', 'controller');
            return $controller->subtotal();
        } else {
            echo $this->msg;
        }
    }

    public function VisitQueryItem()
    {
        if (method_exists('app\report\controller\Visit', 'queryItem')) {
            $controller = controller('report/Visit', 'controller');
            return $controller->queryItem();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 报表：外部客户 ExternalCustomer()
    public function ExternalCustomer()
    {
        if (method_exists('app\report\controller\ExternalCustomer', 'index')) {
            $controller = controller('report/ExternalCustomer', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    public function ExternalCustomerView()
    {
        if (method_exists('app\report\controller\ExternalCustomer', 'view')) {
            $controller = controller('report/ExternalCustomer', 'controller');
            return $controller->view();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 报表：操作日志 ActionLog()
    public function ActionLog()
    {
        if (method_exists('app\report\controller\ActionLog', 'index')) {
            $controller = controller('report/ActionLog', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // +----------------------------------------------------------------------
    // | 图表中心——同比
    // +----------------------------------------------------------------------

    // region 报表：业绩同比 cd()
    public function cd()
    {
        if (method_exists('app\report\controller\Chart', 'cd')) {
            $controller = controller('report/Chart', 'controller');
            return $controller->cd();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 报表：增客同比 ca()
    public function ca()
    {
        if (method_exists('app\report\controller\Chart', 'ca')) {
            $controller = controller('report/Chart', 'controller');
            return $controller->ca();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 报表：跟进同比 cg()
    public function cg()
    {
        if (method_exists('app\report\controller\Chart', 'cg')) {
            $controller = controller('report/Chart', 'controller');
            return $controller->cg();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 报表：推广费用 cj()
    public function cj()
    {
        if (method_exists('app\report\controller\Chart', 'cj')) {
            $controller = controller('report/Chart', 'controller');
            return $controller->cj();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // +----------------------------------------------------------------------
    // | 图表中心——环比
    // +----------------------------------------------------------------------

    // region 报表：来访情况 cb()
    public function cb()
    {
        if (method_exists('app\report\controller\Chart', 'cb')) {
            $controller = controller('report/Chart', 'controller');
            return $controller->cb();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 报表：跟进情况 ch()
    public function ch()
    {
        if (method_exists('app\report\controller\Chart', 'ch')) {
            $controller = controller('report/Chart', 'controller');
            return $controller->ch();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 报表：带看情况 ci()
    public function ci()
    {
        if (method_exists('app\report\controller\Chart', 'ci')) {
            $controller = controller('report/Chart', 'controller');
            return $controller->ci();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 报表：综合 cc()
    public function cc()
    {
        if (method_exists('app\report\controller\Chart', 'cc')) {
            $controller = controller('report/Chart', 'controller');
            return $controller->cc();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // +----------------------------------------------------------------------
    // | 图表中心——其它
    // +----------------------------------------------------------------------

    // region 报表：年度收支 ce()
    public function ce()
    {
        if (method_exists('app\report\controller\Chart', 'ce')) {
            $controller = controller('report/Chart', 'controller');
            return $controller->ce();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 报表：年度支出 cf()
    public function cf()
    {
        if (method_exists('app\report\controller\Chart', 'cf')) {
            $controller = controller('report/Chart', 'controller');
            return $controller->cf();
        } else {
            echo $this->msg;
        }
    }
    // endregion
}