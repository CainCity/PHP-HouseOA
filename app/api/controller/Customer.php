<?php
namespace app\api\controller;

use app\item\model\item as itemModel;
use think\Controller;
use think\Db;

// 外来客户
class Customer extends Controller
{
    protected $DBConfig = "DBConfig_00"; // 数据仓库

    public function index()
    {
        return $this->add();
    }

    /**
     * 新增客户(客户报名)
     * @return false|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    protected function add()
    {
        if (request()->isPost()) {
            $postData = input('post.');

            // 提交内容是否为空
            if (empty($postData)) {
                return json_encode(returnValue(1, "异常请求"));
            }

            $data = json_decode($postData['aoData'], true);
            $arrKey = array('name', 'phone', 'address', 'note', 'thisweburl', 'fromweburl', 'useragent');
            $s = formatPostData($data, $arrKey);

            // 电话是否为空
            $phone = '';
            if ($phone == '') {
                $phone = $s['phone'];
            }
            if ($phone == '') {
                return json_encode(returnValue(1, "异常请求"));
            }

            // 项目是否为空
            $temp1 = explode('.html', $s['thisweburl'])[0];
            $temp2 = explode('/', $temp1);
            $code = end($temp2);
            $model = new ItemModel;
            $item = $model->field('id, itemName as name')->where("unix_timestamp(ctime) = '{$code}'")->find();
            if (empty($item)) {
                return json_encode(returnValue(1, "异常请求"));
            }

            $now = time();
            $IP = getIP();
            $ID = getID();

            // 校验是否重复报名
            if (true) {
                $crData['itemId'] = $item['id'];
                $crData['phone'] = $phone;
                $crData['ip'] = $IP;
                if ($this->checkRepeat($crData)) {
                    return json_encode(returnValue(2, "报名成功"));
                }
            }

            $extend = new \SmallTool\Device();
            $arrDevice = $extend->DeviceInformation($s['useragent']);
            $IPInfo = IPInformation($IP);

            $HData = array();
            $OData = array();

            // 主表信息
            if (true) {
                $HData['id'] = $ID; // 唯一码
                $HData['name'] = $s['name']; // 客户姓名
                $HData['phone'] = $s['phone']; // 客户电话
                $HData['address'] = $s['address']; // 地址
                $HData['note'] = $s['note']; // 备注
                $HData['ctime'] = $now; // 提交时间

                $HData['sign'] = ""; // 来源渠道标识
                $HData['itemid'] = $item['id']; // 项目ID
                $HData['itemname'] = $item['name']; // 项目
                $HData['device'] = $arrDevice['DEVICE']; // 设备
                $HData['brand'] = $arrDevice['BRAND']; // 设备品牌

                $HData['ip'] = $IP; // IP
                $HData['isp'] = $IPInfo['isp']; // IP归属(运营商)
                $HData['country'] = $IPInfo['country']; // IP归属(国)
                $HData['province'] = $IPInfo['region']; // IP归属(省)
                $HData['city'] = $IPInfo['city']; // IP归属(市)
                $HData['district'] = ''; // IP归属(区)
            }

            // 附表信息
            if (true) {
                $OData['id'] = $ID; // 唯一码
                $OData['thisweburl'] = $s['thisweburl']; // 当前网址
                $OData['fromweburl'] = $s['fromweburl']; // 上一级网址
                $OData['useragent'] = $s['useragent']; // 浏览器及设备信息
            }

            Db::startTrans();
            try {
                Db::connect($this->DBConfig)->table('x_customer')->insert($HData);
                Db::connect($this->DBConfig)->table('x_customer_other')->insert($OData);

                Db::commit();

                if (false) {
                    $IP = "";
                    if ($HData['isp'] != "") {
                        $IP .= "[{$HData['isp']}]";
                    }
                    if ($HData['province'] != "" || $HData['city'] != "") {
                        $IP .= "[{$HData['province']}-{$HData['city']}]";
                    }
                    if ($HData['ip'] != "") {
                        $IP .= "{$HData['ip']}";
                    }

                    $to_email = "bingchuan0203@163.com";
                    $title = "用户提交[{$HData['note']}]提醒";
                    $message = "";

                    $message .= "项目 :" . $HData['itemname'] . "<br>";
                    $message .= "姓名 :" . $HData['name'] . "<br>";
                    $message .= "电话 :" . $HData['phone'] . "<br>";
                    $message .= "备注 :" . $HData['note'] . "  " . $HData['address'] . "<br>";
                    $message .= "网址 :" . $OData['thisweburl'] . "<br>";
                    $message .= "提交 :" . $IP . "<br>";
                    $message .= "设备 :" . "[{$HData['device']}]{$HData['brand']}" . "<br>";

                    sendmail($title, $message, $to_email);
                }

                return json_encode(returnValue(2, $phone));
            } catch (\Exception $e) {
                Db::rollback();
            }
        }

        return json_encode(returnValue(1, "异常请求"));
    }

    /**
     * 效验用户重复报名
     * <p>同一天、同一个项目、同一个客户、同一个号码，只能报名3次</p>
     * @param $data <p>
     * itemId: 项目ID
     * phone: 电话
     * ip: IP地址
     * </p>
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    protected function checkRepeat($data)
    {
        $returnValue = false;

        if (!empty($data)) {
            $sql =
                "select count(1) as c " .
                "  from x_customer h " .
                " where h.itemId = '{$data['itemId']}' " .
                "   and h.phone = '{$data['phone']}' " .
                "   and h.ip = '{$data['ip']}' " .
                "   and date(from_unixTime(h.ctime)) = date(now()) ";
            $list = Db::connect($this->DBConfig)->query($sql);
            if (!empty($list)) { if ($list[0]['c'] >= 3) { $returnValue = true; } }
        }

        return $returnValue;
    }
}