<?php
namespace app\index\controller;

// 基础功能入口
class Base extends Auth
{
    protected $msg = '您的版本尚未开通基础功能权限，请联系管理员！';

    public function _empty($name) {
        echo $this->msg;
    }

    // +----------------------------------------------------------------------
    // | 基础数据
    // +----------------------------------------------------------------------

    // region 地域：省级信息 Province()
    public function Province()
    {
        if (method_exists('app\base\controller\Province', 'index')) {
            $controller = controller('base/Province', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    public function ProvinceAdd()
    {
        if (method_exists('app\base\controller\Province', 'add')) {
            $controller = controller('base/Province', 'controller');
            return $controller->add();
        } else {
            echo $this->msg;
        }
    }

    public function ProvinceEdit()
    {
        if (method_exists('app\base\controller\Province', 'edit')) {
            $controller = controller('base/Province', 'controller');
            return $controller->edit();
        } else {
            echo $this->msg;
        }
    }

    public function ProvinceChangeStatus()
    {
        if (method_exists('app\base\controller\Province', 'changeStatus')) {
            $controller = controller('base/Province', 'controller');
            return $controller->changeStatus();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 地域：市级信息 City()
    public function City()
    {
        if (method_exists('app\base\controller\City', 'index')) {
            $controller = controller('base/City', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    public function CityAdd()
    {
        if (method_exists('app\base\controller\City', 'add')) {
            $controller = controller('base/City', 'controller');
            return $controller->add();
        } else {
            echo $this->msg;
        }
    }

    public function CityEdit()
    {
        if (method_exists('app\base\controller\City', 'edit')) {
            $controller = controller('base/City', 'controller');
            return $controller->edit();
        } else {
            echo $this->msg;
        }
    }

    public function CityChangeStatus()
    {
        if (method_exists('app\base\controller\City', 'changeStatus')) {
            $controller = controller('base/City', 'controller');
            return $controller->changeStatus();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 地域：区级信息 District()
    public function District()
    {
        if (method_exists('app\base\controller\District', 'index')) {
            $controller = controller('base/District', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    public function DistrictAdd()
    {
        if (method_exists('app\base\controller\District', 'add')) {
            $controller = controller('base/District', 'controller');
            return $controller->add();
        } else {
            echo $this->msg;
        }
    }

    public function DistrictEdit()
    {
        if (method_exists('app\base\controller\District', 'edit')) {
            $controller = controller('base/District', 'controller');
            return $controller->edit();
        } else {
            echo $this->msg;
        }
    }

    public function DistrictChangeStatus()
    {
        if (method_exists('app\base\controller\District', 'changeStatus')) {
            $controller = controller('base/District', 'controller');
            return $controller->changeStatus();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 数据：数据字典 Wordbook()
    public function Wordbook()
    {
        if (method_exists('app\base\controller\Wordbook', 'index')) {
            $controller = controller('base/Wordbook', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    public function WordbookAdd()
    {
        if (method_exists('app\base\controller\Wordbook', 'add')) {
            $controller = controller('base/Wordbook', 'controller');
            return $controller->add();
        } else {
            echo $this->msg;
        }
    }

    public function WordbookEdit()
    {
        if (method_exists('app\base\controller\Wordbook', 'edit')) {
            $controller = controller('base/Wordbook', 'controller');
            return $controller->edit();
        } else {
            echo $this->msg;
        }
    }

    public function WordbookChangeStatus()
    {
        if (method_exists('app\base\controller\Wordbook', 'changeStatus')) {
            $controller = controller('base/Wordbook', 'controller');
            return $controller->changeStatus();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 数据：ICON图标 Icon()
    public function Icon()
    {
        if (method_exists('app\base\controller\Icon', 'index')) {
            $controller = controller('base/Icon', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // +----------------------------------------------------------------------
    // | 基础数据——权限
    // +----------------------------------------------------------------------

    // region 数据：菜单信息 Menu()
    public function Menu()
    {
        if (method_exists('app\base\controller\Menu', 'index')) {
            $controller = controller('base/Menu', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    public function MenuAdd()
    {
        if (method_exists('app\base\controller\Menu', 'add')) {
            $controller = controller('base/Menu', 'controller');
            return $controller->add();
        } else {
            echo $this->msg;
        }
    }

    public function MenuEdit()
    {
        if (method_exists('app\base\controller\Menu', 'edit')) {
            $controller = controller('base/Menu', 'controller');
            return $controller->edit();
        } else {
            echo $this->msg;
        }
    }

    public function MenuChangeStatus()
    {
        if (method_exists('app\base\controller\Menu', 'changeStatus')) {
            $controller = controller('base/Menu', 'controller');
            return $controller->changeStatus();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 数据：特殊权限 Power()
    public function Power()
    {
        if (method_exists('app\base\controller\Power', 'index')) {
            $controller = controller('base/Power', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    public function PowerAdd()
    {
        if (method_exists('app\base\controller\Power', 'add')) {
            $controller = controller('base/Power', 'controller');
            return $controller->add();
        } else {
            echo $this->msg;
        }
    }

    public function PowerEdit()
    {
        if (method_exists('app\base\controller\Power', 'edit')) {
            $controller = controller('base/Power', 'controller');
            return $controller->edit();
        } else {
            echo $this->msg;
        }
    }

    public function PowerChangeStatus()
    {
        if (method_exists('app\base\controller\Power', 'changeStatus')) {
            $controller = controller('base/Power', 'controller');
            return $controller->changeStatus();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 数据：组织机构 Organizational()
    public function Organizational()
    {
        if (method_exists('app\base\controller\Organizational', 'index')) {
            $controller = controller('base/Organizational', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    public function OrganizationalAdd()
    {
        if (method_exists('app\base\controller\Organizational', 'add')) {
            $controller = controller('base/Organizational', 'controller');
            return $controller->add();
        } else {
            echo $this->msg;
        }
    }

    public function OrganizationalEdit()
    {
        if (method_exists('app\base\controller\Organizational', 'edit')) {
            $controller = controller('base/Organizational', 'controller');
            return $controller->edit();
        } else {
            echo $this->msg;
        }
    }

    public function OrganizationalChangeStatus()
    {
        if (method_exists('app\base\controller\Organizational', 'changeStatus')) {
            $controller = controller('base/Organizational', 'controller');
            return $controller->changeStatus();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 数据：角色 Role()
    public function Role()
    {
        if (method_exists('app\base\controller\Role', 'index')) {
            $controller = controller('base/Role', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    public function RoleAdd()
    {
        if (method_exists('app\base\controller\Role', 'add')) {
            $controller = controller('base/Role', 'controller');
            return $controller->add();
        } else {
            echo $this->msg;
        }
    }

    public function RoleEdit()
    {
        if (method_exists('app\base\controller\Role', 'edit')) {
            $controller = controller('base/Role', 'controller');
            return $controller->edit();
        } else {
            echo $this->msg;
        }
    }

    public function RoleChangeStatus()
    {
        if (method_exists('app\base\controller\Role', 'changeStatus')) {
            $controller = controller('base/Role', 'controller');
            return $controller->changeStatus();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 数据：用户 User()
    public function User()
    {
        if (method_exists('app\base\controller\User', 'index')) {
            $controller = controller('base/User', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    public function UserAdd()
    {
        if (method_exists('app\base\controller\User', 'add')) {
            $controller = controller('base/User', 'controller');
            return $controller->add();
        } else {
            echo $this->msg;
        }
    }

    public function UserEdit()
    {
        if (method_exists('app\base\controller\User', 'edit')) {
            $controller = controller('base/User', 'controller');
            return $controller->edit();
        } else {
            echo $this->msg;
        }
    }

    public function UserSee()
    {
        if (method_exists('app\base\controller\User', 'see')) {
            $controller = controller('base/User', 'controller');
            return $controller->see();
        } else {
            echo $this->msg;
        }
    }

    public function UserChangePassword()
    {
        if (method_exists('app\base\controller\User', 'changePassword')) {
            $controller = controller('base/User', 'controller');
            return $controller->changePassword();
        } else {
            echo $this->msg;
        }
    }
    // endregion

    // region 数据：参数配置 Config()
    public function Config()
    {
        if (method_exists('app\base\controller\Config', 'index')) {
            $controller = controller('base/Config', 'controller');
            return $controller->index();
        } else {
            echo $this->msg;
        }
    }

    public function ConfigEdit()
    {
        if (method_exists('app\base\controller\Config', 'edit')) {
            $controller = controller('base/Config', 'controller');
            return $controller->edit();
        } else {
            echo $this->msg;
        }
    }


    // endregion

    // +----------------------------------------------------------------------
    // | 基础数据——其它
    // +----------------------------------------------------------------------

    public function getCity()
    {
        if (method_exists('app\base\controller\City', 'getCity')) {
            $controller = controller('base/City', 'controller');
            return $controller->getCity();
        } else {
            echo $this->msg;
        }
    }

    public function getMenuTwo()
    {
        if (method_exists('app\base\controller\Menu', 'getMenuTwo')) {
            $controller = controller('base/Menu', 'controller');
            return $controller->getMenuTwo();
        } else {
            echo $this->msg;
        }
    }

    public function sendmailText()
    {
        if (method_exists('app\base\controller\Config', 'sendmailText')) {
            $controller = controller('base/Config', 'controller');
            return $controller->sendmailText();
        } else {
            echo $this->msg;
        }
    }
}