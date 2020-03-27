<?php
namespace app\report\controller;

use app\index\controller\Auth as Auth;
use think\Db;

// 访问记录
class Visit extends Auth
{
    protected $DBConfig = "DBConfig_00"; // 数据仓库

    public function index()
    {
        if (request()->isGet()) {
            // 组织
            if (session('login_sale_team')) {
                $sql = "select id, name from tp5_organizational where id = '" . session('login_team') . "'";
                $teamList = Db::query($sql);
            } else {
                $sql = "select id, name from tp5_organizational where sign = 9 order by msort";
                $teamList = Db::query($sql);
            }
            $this->assign("teamList", $teamList);

            return $this->fetch('report@Visit/index');
        }

        if (request()->isPost()) {
            $postData = input('post.');

            if (!empty($postData)) {
                $where = "";

                $data = json_decode($postData['aoData'], true);
                $key = array("DateType", "dateMin", "dateMax", "Source", "Client",
                    "ItemName", "IP", "So", "ISP", 'TeamId',
                    "iDisplayLength", "iDisplayStart");
                $s = formatPostData($data, $key);

                if (array_check('dateMin', $s) && array_check('dateMax', $s)) {
                    $dateType = "date(from_unixTime(v.ctime, '%Y-%m-%d'))";

                    if ($s['dateMin'] != '' && $s['dateMax'] != '') {
                        $d1 = date('Y-m-d', strtotime($s['dateMin']));
                        $d2 = date('Y-m-d', strtotime($s['dateMax']));
                        if ($d1 < $d2) {
                            $where .= " and " . $dateType . " between '" . $d1 . "' and '" . $d2 . "'";
                        } else {
                            $where .= " and " . $dateType . " between '" . $d2 . "' and '" . $d1 . "'";
                        }
                    } else {
                        if ($s['dateMin'] != '') {
                            $d1 = date('Y-m-d', strtotime($s['dateMin']));
                            $where .= " and " . $dateType . " >= '" . $d1 . "'";
                        } elseif ($s['dateMax'] != '') {
                            $d2 = date('Y-m-d', strtotime($s['dateMax']));
                            $where .= " and " . $dateType . " <= '" . $d2 . "'";
                        }
                    }
                }

                if (array_check('Source', $s)) {
                    switch ($s['Source']) {
                        case '1': // 搜索
                            #$where .= " and v.searchEngines != ''";
                            $where .= " and v.keyword != ''";
                            break;
                        case '9': // 其它
                            #$where .= " and v.searchEngines = ''";
                            $where .= " and v.keyword = ''";
                            break;
                        default:
                            break;
                    }
                }

                if (array_check('Client', $s)) {
                    switch ($s['Client']) {
                        case '1': // 电脑设备
                            $where .= " and v.device = '电脑端'"; break;
                        case '2': // 移动设备
                            $where .= " and v.device <> '电脑端'"; break;
                        default: break;
                    }
                }

                if (array_check('ItemName', $s)) {
                    if ($s['ItemName'] != '') {
                        $where .= " and v.item like '%" . $s['ItemName'] . "%'";
                    }
                }

                if (array_check('IP', $s)) {
                    if ($s['IP'] != '') {
                        $delimiter = '|';
                        $str = str_replace(array(',', '，'), $delimiter, $s['IP']);
                        if (strpos($str, $delimiter)) {
                            $ips = "";
                            $arrIP = explode($delimiter, $str);
                            foreach ($arrIP as $ip) {
                                if ($ips != "") $ips .= ",";
                                $ips .= "'{$ip}'";
                            }
                            $where .= " and v.ip in ({$ips})";
                        } else {
                            $where .= " and v.ip like '" . $s['IP'] . "%'";
                        }
                    }
                }

                if (array_check('So', $s)) {
                    if ($s['So'] != '') {
                        $where .= " and v.keyword like '%" . $s['So'] . "%'";
                    }
                }

                if (array_check('ISP', $s)) {
                    if ($s['ISP'] != '') {
                        $where .= " and v.isp = '{$s['ISP']}'";
                    }
                }

                return json($this->getData($s, $where));
            }
        }
    }
    protected function getData($data, $where = "") {
        $aaData = array();
        $count = 0;
        $sql_field =
            "from_unixTime(v.ctime, '%Y-%m-%d %H:%i:%s') as cTime, v.id, " .
            "v.ip, v.province as sheng, v.city as shi, v.isp, v.item as ItemName, " .
            "v.searchEngines, v.keyword, v.device, v.brand, v.os as brand_os, v.url ";

        $sql =
            "select " . $sql_field .
            "  from z_visit v " .
            " where 1 = 1 " . $where .
            " order by v.ctime desc " .
            " limit " . $data['iDisplayStart'] . "," . $data['iDisplayLength'];
        $aList = Db::connect($this->DBConfig)->query($sql);
        if (!empty($aList)) {
            $aa = array();
            foreach ($aList as $aRow) {
                $webTemp = explode('.', explode('.com', $aRow['url'])[0]);
                $web = end($webTemp);

                $brand = "<a href='https://www.baidu.com/s?tn=news&wd=" . $aRow['brand'] . "' target='_blank'>" . "[{$aRow['device']}][{$aRow['brand']}]" . "</a>";
                $ItemName = "<a href='https://www.baidu.com/s?wd=" . $aRow['ItemName'] . "' target='_blank'>" . $aRow['ItemName'] . "</a>";

                if (strlen($aRow['sheng']) > 1) {
                    $IPInfo = "[" . $aRow['isp'] . "][" . $aRow['sheng'] . "-" . $aRow['shi'] . "][" . $aRow['ip'] . "]";
                } else {
                    $IPInfo = "<a href='https://www.baidu.com/s?wd=" . $aRow['ip'] . "' target='_blank'>" . $aRow['ip'] . "</a>";
                }

                $Note = "";
                if ($aRow['searchEngines'] != "") {
                    $Note .= "[引擎:" . $aRow['searchEngines'] . "]";
                }
                if ($aRow['keyword'] != "") {
                    $Note .= "[搜索词:" . $aRow['keyword'] . "]";
                }

                if ($Note == "") {
                    $Note = "直接访问网页";

                    $aa[0] = $aRow['id'];
                    $aa[1] = "<span class='c-red'>" . $brand . "</span>";
                    $aa[2] = "<span class='c-red'>" . $aRow['cTime'] . "</span>";
                    $aa[3] = "<span class='c-red'>" . $ItemName . "</span>";
                    $aa[4] = "<span class='c-red'>" . $IPInfo . "</span>";
                    $aa[5] = "<span class='c-red'>" . $Note . "</span>";
                } else {
                    $aa[0] = $aRow['id'];
                    $aa[1] = $brand;
                    $aa[2] = $aRow['cTime'];
                    $aa[3] = $ItemName;
                    $aa[4] = $IPInfo;
                    $aa[5] = $Note;
                }

                $aaData[] = $aa;
            }

            $sql = "select count(1) as c from z_visit v where 1 = 1 " . $where;
            $count = Db::connect($this->DBConfig)->query($sql)[0]['c'];
        }

        $output['aaData'] = $aaData;
        $output['iTotalDisplayRecords'] = $count;    //如果有全局搜索，搜索出来的个数
        $output['iTotalRecords'] = $count; //总共有几条数据
        return $output;
    }

    public function view()
    {
        if (request()->isGet()) {
            $data = input('get.');
            $sql_field =
                "from_unixTime(v.ctime, '%Y-%m-%d %H:%i:%s') as cTime, ".
                "v.id, v.IP, concat(v.province, '-', v.city) as area, v.ISP, ".
                "v.item as itemName, v.searchEngines, v.keyword, " .
                "o.thisUrl as URL, o.userAgent, v.device, v.brand, v.os as brand_os";

            $sql =
                "select " . $sql_field .
                "  from z_visit v " .
                "  left join z_visit_other o on v.id = o.id " .
                " where v.id = '" . $data['id'] . "'";
            $list = Db::connect($this->DBConfig)->query($sql)[0];
            $this->assign('list', $list);

            return $this->fetch('report@Visit/view');
        }
    }

    public function subtotal()
    {
        $data = input('get.');
        $message = "";

        if (!empty($data)) {
            $TeamId = "";
            $sDate = $data['dateMin'];
            $eDate = $data['dateMax'];
            if (array_check('TeamId', $data)) {
                $TeamId = $data['TeamId'];
            }

            if ($sDate > $eDate) { $temp = $sDate; $sDate = $eDate; $eDate = $temp; }

            $message .= extendSubtotal($sDate, $eDate, $TeamId);

            $message .= extendSubtotalNewCustomer($sDate, $eDate, $TeamId);
        }

        $message = str_replace(array("<strong>", "</strong>", "<br/>"), array("【", "】", "\n"), $message);

        $list['note'] = $message;
        $this->assign('list', $list);

        return $this->fetch('report@Visit/subtotal');
    }

    public function queryItem()
    {
        if (request()->isGet()) {
            return $this->fetch('report@Visit/queryItem');
        }

        if (request()->isPost()) {
            $postData = input('post.');
            if (!empty($postData)) {
                $data = json_decode($postData['aoData'], true);
                $key = array('dateMin', 'dateMax', 'info');
                $s = formatPostData($data, $key);

                $where = " and keyword <> '' ";

                $dateType = 'date(from_unixTime(ctime))';
                if ($s['dateMin'] != '' && $s['dateMax'] != '') {
                    $d1 = date('Y-m-d', strtotime($s['dateMin']));
                    $d2 = date('Y-m-d', strtotime($s['dateMax']));
                    if ($d1 < $d2) {
                        $where .= " and " . $dateType . " between '" . $d1 . "' and '" . $d2 . "'";
                    } else {
                        $where .= " and " . $dateType . " between '" . $d2 . "' and '" . $d1 . "'";
                    }
                } else {
                    if ($s['dateMin'] != '') {
                        $d1 = date('Y-m-d', strtotime($s['dateMin']));
                        $where .= " and " . $dateType . " >= '" . $d1 . "'";
                    } elseif ($s['dateMax'] != '') {
                        $d2 = date('Y-m-d', strtotime($s['dateMax']));
                        $where .= " and " . $dateType . " <= '" . $d2 . "'";
                    }
                }

                $arrCustomer = array();
                $arrItem = array();
                $arrWeb = array();
                $arrNewInfo = array();
                $returnData = "";

                $IPs = "";
                $s['info'] = trim(str_replace(array("\t"), " ", $s['info'])); // 去空格
                $arrInfo = explode("\n", $s['info']); // 转数组
                if (!empty($arrInfo)) {
                    $arrInfo = array_unique($arrInfo); // 去重复
                    foreach ($arrInfo as $row) {
                        $row = trim($row); // 去空格
                        if ($row == "") continue;

                        $arrTemp = explode(" ", $row);
                        $num = count($arrTemp);

                        switch ($num) {
                            case 2:
                                $temp['phone'] = $arrTemp[0];
                                $temp['ip'] = $arrTemp[1];
                                $arrCustomer[] = $temp;

                                if ($IPs != "") {
                                    $IPs .= ",";
                                }
                                $IPs .= "'{$temp['ip']}'";
                                break;
                            case 4:
                                $temp['phone'] = $arrTemp[0] . $arrTemp[1] . $arrTemp[2];
                                $temp['ip'] = $arrTemp[3];
                                $arrCustomer[] = $temp;

                                if ($IPs != "") {
                                    $IPs .= ",";
                                }
                                $IPs .= "'{$temp['ip']}'";
                                break;
                        }
                    }
                }

                if ($IPs != "") {
                    $where .= " and ip in ({$IPs}) ";
                    $sql =
                        "select distinct substring_index(substring_index(url, '.', 2), '.', -1) as web, ip, item " .
                        "  from z_visit " .
                        " where 1 = 1 " . $where .
                        " order by ctime desc ";
                    $vData = Db::connect($this->DBConfig)->query($sql);
                    if (!empty($vData)) {
                        foreach ($vData as $row) {
                            $arrItem[] = $row;
                            $arrWeb[] = $row['web'];
                        }
                    }
                }

                if (!empty($arrWeb)) {
                    $arrWeb = array_unique($arrWeb);

                    foreach ($arrCustomer as $customer) {
                        $tempBool = false;
                        foreach ($arrWeb as $web) {
                            foreach ($arrItem as $item) {
                                if ($item['web'] == $web && $item['ip'] == $customer['ip']) {
                                    $temp['web'] = $web;
                                    $temp['phone'] = $customer['phone'];
                                    $temp['item'] = $item['item'];
                                    $temp['ip'] = $customer['ip'];
                                    $arrNewInfo[] = $temp;
                                    $tempBool = true;
                                    break;
                                }
                            }
                        }
                        if (!$tempBool) {
                            $temp['web'] = '';
                            $temp['phone'] = $customer['phone'];
                            $temp['item'] = '';
                            $temp['ip'] = $customer['ip'];
                            $arrNewInfo[] = $temp;
                        }
                    }

                    //根据字段web对数组$arrNewInfo进行降序排列
                    $webs = array_column($arrNewInfo, 'web');
                    array_multisort($webs, SORT_DESC, $arrNewInfo);
                }

                if (!empty($arrNewInfo)) {
                    $web = "CainCity";
                    foreach ($arrNewInfo as $row) {
                        if ($web != $row['web']) {
                            if ($web != "CainCity") { $returnData .= "\n\n"; }
                            $web = $row['web'];
                            $returnData .= "{$web}:\n";
                        }

                        if ($row['item'] != "") {
                            $returnData .= "{$row['phone']} {$row['item']}\n";
                        } else {
                            $returnData .= "{$row['phone']} {$row['ip']}\n";
                        }
                    }
                }

                return returnValue(2, $returnData);
            }
            return returnValue(1, '异常请求');
        }
    }
}