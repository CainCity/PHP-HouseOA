<?php
namespace app\base\controller;

use app\index\controller\Auth as Auth;
use think\Db;

// 参数配置
class Config extends Auth
{
    public function index()
    {
        if (request()->isGet()) {
            // 企业信息设置
            $key = ['name', 'abbreviation', 'address', 'profile', 'contacts', 'number', 'qq', 'wechat', 'email', '400'];
            $this->assign('companyData', $this->setValue('company', $key));

            // 系统设置
            $key = ['name', 'abbreviation', 'icp', 'team', 'email', 'web_name'];
            $this->assign('systemData', $this->setValue('system', $key));

            // 站点设置
            $key = ['website', 'api', 'root_path', 'public_path', 'html_path', 'html_style_path', 'html_image_path', 'html_file_suffix'];
            $this->assign('webData', $this->setValue('web', $key));

            // 邮件服务设置
            $key = ['server', 'port', 'user', 'password', 'test'];
            $this->assign('emailData', $this->setValue('email', $key));

            return $this->fetch('base@Config/index');
        }
    }

    private function setValue($fileName, $key)
    {
        $returnValue = array();

        if (is_array($key)) {
            foreach ($key as $k) {
                $returnValue["{$k}"] =  getConfigValue("{$fileName}.{$k}");
            }
        } elseif (is_string($key)) {
            $returnValue["{$key}"] = getConfigValue("{$fileName}.{$key}");
        }

        return $returnValue;
    }

    public function edit()
    {
        if (request()->isPost()) {
            $data = input('post.');
            if (!empty($data)) {
                $fileName = '';
                $tempData = array();
                foreach ($data as $key => $value) {
                    if (empty($fildName)) {
                        $fileName = explode('_', $key)[0];
                    }
                    $k = substr($key, strpos($key, '_') + 1);
                    $tempData["{$k}"] = setConfigValue($value);
                }

                replaceConfig($tempData, $fileName);

                return returnValue(2, "添加成功");
            } else {
                return returnValue(1, "提交异常");
            }
        }
    }

    public function sendmailText()
    {
        $email = config('email.test');
        if (!empty($email)) {
            $title = '测试邮件';
            $message = '<strong>这是一封测试邮件</strong><br><br>十、九、八、七、六、五、四、三、二、一、零';
            if (sendmail($title, $message, $email)) {
                return returnValue(2, '发送成功');
            }
        }

        return returnValue(1, '发送失败');
    }
}