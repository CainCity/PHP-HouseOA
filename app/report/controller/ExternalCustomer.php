<?php
namespace app\report\controller;

use app\index\controller\Auth as Auth;
use think\Db;

// 报表：外部客户
class ExternalCustomer extends Auth
{
    protected $DBConfig = "DBConfig_00"; // 数据仓库

    public function index()
    {
        if (request()->isGet()) {
            return $this->fetch('report@ExternalCustomer/index');
        }

        if (request()->isPost()) {
            $postData = input('post.');
            $output = array();
            if (!empty($postData)) {
                $data = json_decode($postData['aoData'], true);
                $key = array("dbSign", "dateType", "dateMin", "dateMax", "Tel", "Item", "iDisplayLength", "iDisplayStart");
                $s = formatPostData($data, $key);

                $returnData = array();
                $count = 0;
                $where = "";
                // $where = " and v.name not like '%测%' and v.name not like '%ce%'";

                // 查询条件：日期类型
                $DateType = "date(from_unixTime(ctime))";

                // 查询条件：日期范围
                if (!empty($s['dateMin']) && !empty($s['dateMax'])) {
                    $d1 = date('Y-m-d', strtotime($s['dateMin']));
                    $d2 = date('Y-m-d', strtotime($s['dateMax']));
                    if ($d1 < $d2) {
                        $where .= " and {$DateType} between '{$d1}' and '{$d2}'";
                    } else {
                        $where .= " and {$DateType} between '{$d2}' and '{$d1}'";
                    }
                } else {
                    if (!empty($s['dateMin'])) {
                        $d1 = date('Y-m-d', strtotime($s['dateMin']));
                        $where .= " and {$DateType} >= '{$d1}'";
                    } elseif (!empty($s['dateMax'])) {
                        $d2 = date('Y-m-d', strtotime($s['dateMax']));
                        $where .= " and {$DateType} <= '{$d2}'";
                    }
                }

                // 查询条件：电话
                if (!empty($s['Tel'])) {
                    $where .= " and phone like '%{$s['Tel']}%'";
                }

                // 查询条件：项目
                if (!empty($s['Item'])) {
                    $where .= " and itemName like '%{$s['Item']}%'";
                }

                $sql =
                    "select id, sign, from_unixTime(ctime) as ctime, itemName as item, name, phone, address, note, " .
                    "       ip, isp, province, city " .
                    "  from x_customer " .
                    " where 1 = 1 {$where} " .
                    " order by ctime desc " .
                    " limit {$s['iDisplayStart']}, {$s['iDisplayLength']}";

                $aList = Db::connect($this->DBConfig)->query($sql);
                if (!empty($aList)) {
                    $tempData = array();
                    foreach ($aList as $aRow) {
                        $item = "";
                        if (!empty($aRow['item'])) {
                            $item = "<a href='https://www.baidu.com/s?wd={$aRow['item']}' target='_blank'>{$aRow['item']}</a>";
                        }

                        $phone = "";
                        if (!empty($aRow['phone'])) {
                            $phone = "<a href='https://www.baidu.com/s?wd={$aRow['phone']}' target='_blank'>{$aRow['phone']}</a>";
                        }

                        $note = "";
                        if (!empty($aRow['note'])) {
                            $note .= $aRow['note'] . " ";
                        }
                        if (!empty($aRow['address'])) {
                            $note .= $aRow['address'] . " ";
                        }

                        $IP =
                            "[{$aRow['isp']}]" .
                            "[{$aRow['province']}.{$aRow['city']}]" .
                            "[{$aRow['ip']}]";

                        $tempData[0] = $aRow['id'];
                        $tempData[1] = $aRow['ctime'];
                        $tempData[2] = $item;
                        $tempData[3] = $aRow['name'];
                        $tempData[4] = $phone;
                        $tempData[5] = $note;
                        $tempData[6] = $IP;


                        $returnData[] = $tempData;
                    }

                    $sql = "select count(1) as c from x_customer where 1 = 1 {$where} ";
                    $aList = Db::connect($this->DBConfig)->query($sql);
                    $count = !empty($aList) ? $aList[0]['c'] : 0;
                }

                $output['aaData'] = $returnData;
                $output['iTotalDisplayRecords'] = $count;    //如果有全局搜索，搜索出来的个数
                $output['iTotalRecords'] = $count; //总共有几条数据
            }

            return json($output);
        }
    }

    public function view()
    {
        // Get
        if (request()->isGet()) {
            $data = input('get.');

            $sql =
                "select c.id, device, brand, " .
                "       itemName, name, concat(note, address) as note, " .
                "       concat(left(phone, 3), '****', right(phone, 4)) as phone, " .
                "       concat('[', isp, '][', province, '-', city, '][', ip, ']') as ip, " .
                "       from_unixTime(ctime) as cTime, " .
                "       thisWebUrl, fromWebUrl, userAgent " .
                "  from x_customer c " .
                "  left join x_customer_other o on c.id = o.id " .
                " where c.id = '{$data['id']}' ";
            $list = Db::connect($this->DBConfig)->query($sql)[0];
            $this->assign('list', $list);

            return $this->fetch('report@ExternalCustomer/view');
        }
    }
}