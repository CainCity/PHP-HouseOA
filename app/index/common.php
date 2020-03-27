<?php
/**
 * Created by PhpStorm.
 * User: CainCity
 * Date: 2019-09-03
 * Time: 13:00
 */

use think\Db;

#region 邮件内容
/**
 * 邮件内容：推广小计
 * @param string $sDate 起始日期
 * @param string $eDate 截止日期
 * @param string $teamId 团队
 * @return string 邮件内容
 * @throws \think\Exception
 * @throws \think\exception\PDOException
 */
function extendSubtotal($sDate, $eDate, $teamId = '') {
    $where = '';

    switch ($teamId) {
        default:
            $where .= "";
    }

    $where .=
        "and v.keyword != '' " .
        "and from_UnixTime(v.ctime, '%Y%m%d') between date('{$sDate}') and date('{$eDate}')";

    $sql =
        "select device, item as itemName, keyword " .
        "  from ( " .
        "select distinct v.item, v.device, v.keyword, v.ip, v.searchEngines, " .
        "       from_unixTime(v.ctime, '%Y%m%d') as ctime, v.url " .
        "  from z_visit v " .
        " where 1 = 1 " . $where . ") t " .
        " order by convert(t.item using gbk), convert(t.keyword using gbk) ";
    $list = DB::connect("DBConfig_00")->query($sql);

    return fromExtendSubtotal($list, $sDate, $eDate, $teamId);
}

/**
 * 邮件内容：推广小计原始内容格式为邮件格式
 * @param array $list 邮件内容原始数据
 * @param string $sDate 起始日期
 * @param string $eDate 截止日期
 * @param string $teamId 团队
 * @return string 邮件内容
 */
function fromExtendSubtotal($list, $sDate, $eDate, $teamId = '') {
    $sHtml = '';

    if (!empty($list)) {
        $team = "";
        switch ($teamId) {
            case config('data.A组'):
                $team = "A组";
                break;
            case config('data.B组'):
                $team = "B组";
                break;
        }

        $msg_body = "";
        $msg_temp = "";

        $total_number = 0;
        $total_www_number = 0;
        $total_wap_number = 0;

        $item_www_number = 0;
        $item_wap_number = 0;
        $keyword_number = 0;

        for ($i = 0; $i < count($list); $i++) {
            $total_number++;
            if ($list[$i]['device'] != '移动端') { $total_www_number++; } else { $total_wap_number++; }

            // ---------------- 单个项目的客户端计数 ----------------
            if ($list[$i]['device'] != '移动端') { $item_www_number++; } else { $item_wap_number++; }

            // ---------------- 单个搜索词计数 ----------------
            $keyword_number++;

            if (count($list) > 1) {
                if ($i != count($list) - 1) {
                    if ($list[$i]['keyword'] != $list[$i + 1]['keyword'] || $list[$i]['itemName'] != $list[$i + 1]['itemName']) {
                        $msg_temp .= "  \"" . $list[$i]['keyword'] . "\"搜索" . $keyword_number . "次" . "<br/>";
                        $keyword_number = 0;
                    }

                    if ($list[$i]['itemName'] != $list[$i + 1]['itemName']) {
                        $item_total_number = $item_wap_number + $item_www_number;
                        $msg_body .= "<strong>" . $list[$i]['itemName'] . "</strong>";
                        if ($item_wap_number != 0) {$msg_body .= "移动端" . $item_wap_number . "次，";}
                        if ($item_www_number != 0) {$msg_body .= "电脑端" . $item_www_number . "次，";}
                        if ($item_total_number != 0) {$msg_body .= "共计" . $item_total_number . "次。搜索词：" . "<br/>";}
                        if ($msg_temp != "") {$msg_body .= $msg_temp . "<br/>";}

                        $msg_temp = "";
                        $item_www_number = 0;
                        $item_wap_number = 0;
                    }
                } else {
                    $msg_temp .= "  \"" . $list[$i]['keyword'] . "\"搜索" . $keyword_number . "次" . "<br/>";
                    $keyword_number = 0;

                    $item_total_number = $item_wap_number + $item_www_number;
                    $msg_body .= "<strong>" . $list[$i]['itemName'] . "</strong>";
                    if ($item_wap_number != 0) {$msg_body .= "移动端" . $item_wap_number . "次，";}
                    if ($item_www_number != 0) {$msg_body .= "电脑端" . $item_www_number . "次，";}
                    if ($item_total_number != 0) {$msg_body .= "共计" . $item_total_number . "次。搜索词：" . "<br/>";}
                    if ($msg_temp != "") {$msg_body .= $msg_temp . "<br/>";}
                }
            } else {
                $msg_temp .= "  \"" . $list[$i]['keyword'] . "\"搜索" . $keyword_number . "次" . "<br/>";
                $keyword_number = 0;

                $item_total_number = $item_wap_number + $item_www_number;
                $msg_body .= "<strong>" . $list[$i]['itemName'] . "</strong>";
                if ($item_wap_number != 0) {$msg_body .= "移动端" . $item_wap_number . "次，";}
                if ($item_www_number != 0) {$msg_body .= "电脑端" . $item_www_number . "次，";}
                if ($item_total_number != 0) {$msg_body .= "共计" . $item_total_number . "次。搜索词：" . "<br/>";}
                if ($msg_temp != "") {$msg_body .= $msg_temp . "<br/>";}
            }
        }

        if ($total_number != 0) {

            if ($sDate == $eDate) {
                $sHtml .= "<strong>" . $sDate . "</strong>";
            } else {
                $tempA = explode("-", $sDate);
                $tempB = explode("-", $eDate);

                if ($tempA[0] == $tempB[0] && $tempA[1] == $tempB[1]) {
                    $sHtml .= "<strong>" . $sDate . "~" . $tempB[2] . "</strong>";
                } else if($tempA[0] == $tempB[0]) {
                    $sHtml .= "<strong>" . $sDate . "~" . $tempB[1] . "-" . $tempB[2] . "</strong>";
                } else {
                    $sHtml .= "<strong>" . $sDate . "~" . $eDate . "</strong>";
                }
            }

            if ($team != "") { $sHtml .= "{$team}"; }
            $sHtml .= ":展现统计";
            $sHtml = str_replace(array("-"), "/", $sHtml);

            if ($total_wap_number != 0) { $sHtml .= ",移动端" . $total_wap_number . "次"; }
            if ($total_www_number != 0) { $sHtml .= ",电脑端" . $total_www_number . "次"; }
            if ($total_wap_number != 0 && $total_www_number != 0) {
                $sHtml .= ",共计" . $total_number . "次";
            }
            $sHtml .= "<br/>" . "<br/>" . "<br/>";

            $sHtml .= $msg_body;
        }
    }

    return $sHtml;
}

/**
 * 邮件内容：增客小计
 * @param string $sDate 起始日期
 * @param string $eDate 截止日期
 * @param string $teamId 团队
 * @return string 邮件内容
 */
function extendSubtotalNewCustomer($sDate, $eDate, $teamId = '') {
    $message = "";
    $where = "";

    switch ($teamId) {
        case config('data.A组'):
            $where .= " and c.orgId = '" . config('data.A组') . "'";
            break;
        case config('data.B组'):
            $where .= " and c.orgId = '" . config('data.B组') . "'";
            break;
        default:
            $where .= "";
    }

    $where .=
        "and ifNull(w.temp1, 1) = 1 " .
        "and date(c.ctime) between date('" . $sDate . "') and date('" . $eDate . "') ";

    $sql =
        "select ifNull(o.name, '') as teamName, i.itemName, " .
        "       sum(if(c.aType = '" . config('data.中介') . "', 0, 1)) as num1, " .
        "       sum(if(c.aType = '" . config('data.中介') . "', 1, 0)) as num2 " .
        "  from tp5_customer c " .
        "  left join tp5_organizational o on c.orgId = o.id " .
        "  left join tp5_item i on c.itemId = i.id " .
        "  left join tp5_wordbook w on c.source = w.id " .
        " where 1 = 1 " . $where .
        " group by c.orgId, c.itemId " .
        " order by convert(o.name using gbk), convert(i.itemName using gbk) ";

    $dList = Db::query($sql);
    if (!empty($dList)) {
        $message .= "<br/>";
        $arrOrg = array();
        foreach ($dList as $row) { $arrOrg[] = $row['teamName']; }
        $arrOrg = array_unique($arrOrg);

        foreach ($arrOrg as $org) {
            $tempMessage = "";
            $total_num1 = 0;
            $total_num2 = 0;
            $arrItem = array();

            foreach ($dList as $row) {
                if ($org == $row['teamName']) {
                    $total_num1 += $row['num1'];
                    $total_num2 += $row['num2'];

                    $arrItem[] = $row['itemName'];

                    $tempMessage .= "<strong>" . ($row['itemName'] == "" ? "未知项目" : $row['itemName']) . "</strong>";

                    if ($row['num1'] != 0) {
                        $tempMessage .= " 客户" . $row['num1'] . "位";
                    }

                    if ($row['num2'] != 0) {
                        $tempMessage .= " 中介" . $row['num2'] . "位";
                    }

                    if ($row['num1'] != 0 && $row['num2'] != 0) {
                        $tempMessage .= " 总计" . ($row['num1'] + $row['num2']) . "位";
                    }

                    $tempMessage .= "<br/>";
                }
            }
            $total_num = $total_num1 + $total_num2;

            $title = "";
            if ($sDate == $eDate) {
                $title .= "<strong>" . $sDate . "</strong>";
            } else {
                $tempA = explode("-", $sDate);
                $tempB = explode("-", $eDate);

                if ($tempA[0] == $tempB[0] && $tempA[1] == $tempB[1]) {
                    $title .= "<strong>" . $sDate . "~" . $tempB[2] . "</strong>";
                } else if($tempA[0] == $tempB[0]) {
                    $title .= "<strong>" . $sDate . "~" . $tempB[1] . "-" . $tempB[2] . "</strong>";
                } else {
                    $title .= "<strong>" . $sDate . "~" . $eDate . "</strong>";
                }
            }
            $title .= $org . ":新增客户";
            $title = str_replace(array("-"), "/", $title);


            $message .= $title . "<br/>";

            // 如果推广项目数量大于1，则统计项目、客户总计；反之则不需要统计。
            $itemNumber = count(array_unique($arrItem));
            if ($itemNumber > 1) {
                $tempTotalInfo = "项目" . count(array_unique($arrItem)) . "个 ";
                if ($total_num1 > 0) {
                    $tempTotalInfo .= "客户" . $total_num1 . "位 ";
                }
                if ($total_num2 > 0) {
                    $tempTotalInfo .= "中介" . $total_num2 . "位 ";
                }
                if ($total_num1 > 0 && $total_num2 > 0) {
                    $tempTotalInfo .= "总计" . $total_num . "位 ";
                }
                $message .= "---- 总计 ----" . "<br/>";
                $message .= $tempTotalInfo . "<br/>" . "<br/>";
            }

            $message .= "---- 项目 ----" . "<br/>";
            $message .= $tempMessage;
            $message .= "<br/>" . "<br/>";
        }
    }

    return $message;
}
// endregion

// region 缓存：城市
/**
 * 获取城市
 * @return array
 */
function cacheCityList()
{
    $tag = 'city';
    $key = $tag . ':all';

    $cache = cache($key);
    if (empty($cache)) {
        $sql =
            "select a.id, concat(p.name, '·', cs.name, '·', a.name) as name " .
            "  from tp5_district a " .
            "  left join tp5_City cs on a.pid = cs.id " .
            "  left join tp5_province p on cs.pid = p.id " .
            " where 1 = 1 ";
        $data = Db::query($sql);
        \think\Cache::tag($tag)->set($key, dataToCache($data));

        $returnData = cache($key);
    } else {
        $returnData = $cache;
    }

    return $returnData;
}
// endregion
