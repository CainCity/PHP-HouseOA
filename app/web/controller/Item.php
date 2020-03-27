<?php
namespace app\web\controller;

use app\item\model\Item as ItemModel;
use app\item\model\ItemOther as ItemOtherModel;
use app\web\controller\Index as IndexController;
use app\index\controller\Auth as Auth;
use think\Db;

class Item extends Auth
{
    // region 主界面
    public function Index()
    {
        if (request()->isGet()) {
            $this->assign("aList", cacheData('wordbook', config('data.项目性质')));
            return $this->fetch('web@item/index');
        }

        if (request()->isPost()) {
            $postData = input('post.');
            $output = array();
            $returnData = array();
            $count = 0;

            if (!empty($postData)) {
                $data = json_decode($postData['aoData'], true);
                $key = array('status', 'itemType', 'sName', 'ProvinceName', 'CityName', 'DistrictName', 'iDisplayLength', 'iDisplayStart');
                $s = formatPostData($data, $key);

                $where = "1 = 1";

                if (!empty($s['status'])) {
                    if ($s['status'] == '2') {
                        $where .= " and i.status = 2";
                    } else {
                        $where .= " and i.status != 2";
                    }
                }

                if (!empty($s['itemType'])) {
                    $where .= " and locate('{$s['itemType']}', o.type3)";
                }

                if (!empty($s['sName'])) {
                    $itemName = str_replace(array("·", " "), "", $s['sName']);
                    $where .= " and replace(i.itemName,'·','') like '%{$itemName}%'";
                }

                if (!empty($s['ProvinceName'])) {
                    $where .= " and p.name like '%{$s['ProvinceName']}%'";
                }

                if (!empty($s['CityName'])) {
                    $where .= " and c.name like '%{$s['CityName']}%'";
                }

                if (!empty($s['DistrictName'])) {
                    $where .= " and d.name like '%{$s['DistrictName']}%'";
                }

                $sql =
                    "select i.id, i.itemName, i.status, i.description, i.itemType, " .
                    "       concat(p.name, '·', c.name, '·', d.name, '·', i.address) as address " .
                    "  from tp5_item i " .
                    "  left join tp5_item_other o on i.id = o.id " .
                    "  left join tp5_province p on i.provinceId = p.id " .
                    "  left join tp5_city c on i.cityId = c.id " .
                    "  left join tp5_district d on i.districtId = d.id " .
                    " where " . $where .
                    " order by CONVERT(p.name USING GBK), CONVERT(c.name USING GBK), CONVERT(d.name USING GBK), CONVERT(i.itemName USING GBK) " .
                    " limit " . $s['iDisplayStart'] . "," . $s['iDisplayLength'];

                $aList = Db::query($sql);
                if (!empty($aList)) {
                    $tempData = array();
                    $itemType = cacheData('wordbook', config('data.项目性质'), true);
                    foreach ($aList as $aRow) {
                        $item = "【" . $itemType[$aRow['itemType']] . "】 " . $aRow['itemName'];
                        $tempData[0] = $aRow['id'];
                        $tempData[1] = $item;
                        $tempData[2] = $aRow['address'];
                        $tempData[3] = $aRow['description'];
                        $returnData[] = $tempData;
                    }

                    /** 数据数量查询 */
                    $sql =
                        "select count(1) as c " .
                        "  from tp5_item i " .
                        "  left join tp5_item_other o on i.id = o.id " .
                        "  left join tp5_province p on i.provinceId = p.id " .
                        "  left join tp5_city c on i.cityId = c.id " .
                        "  left join tp5_district d on i.districtId = d.id " .
                        " where " . $where;
                    $aList = Db::query($sql);
                    $count = !empty($aList) ? $aList[0]['c'] : 0;
                }
            }

            $output['aaData'] = $returnData;
            $output['iTotalDisplayRecords'] = $count;    //如果有全局搜索，搜索出来的个数
            $output['iTotalRecords'] = $count; //总共有几条数据

            return json($output);
        }
    }
    // endregion

    // region 编辑
    public function edit()
    {
        if (request()->isGet()) {
            $data = input('get.');
            $sql =
                "select i.*, unix_timestamp(i.ctime) as num, " .
                "       l1.area, l1.price, " .
                "       l1.type1, l1.type2, l1.type3, l1.type4, " .
                "       l1.note1, l1.note2, l1.note3, l1.note4, l1.note5, l1.note6 " .
                "  from tp5_item i " .
                "  left join tp5_item_other l1 on i.id = l1.id " .
                " where i.id = '" . $data['id'] . "'";
            $list = Db::query($sql)[0];

            $filepath = config('web.html_path') . 'show/' . $list['num'] . '.html';

            if(!file_exists($filepath)){
                $list['html'] = "";
            } else {
                $list['html'] = $filepath;
            }

            $this->assign('list', $list);

            // 省
            $this->assign("provinceList", cacheProvince());
            // 市
            $this->assign("cityList", cacheCity($list['provinceid']));
            // 区
            $this->assign("districtList", cacheDistrict($list['cityid']));

            // 项目类型
            $this->assign("type1List", cacheData('wordbook', config('data.项目性质')));
            // 销售情况
            $this->assign("type2List", cacheData('wordbook', config('data.装修标准')));
            // 项目性质
            $this->assign("type3List", cacheData('wordbook', config('data.项目类型')));
            // 装修标准
            $this->assign("type4List", cacheData('wordbook', config('data.销售情况')));


            // 相册：banner
            $this->assign("imageList0", getAttachmentList("item:0", $data['id'], "jpg", 'HTML'));
            // 相册：效果相册
            $this->assign("imageList1", getAttachmentList("item:1", $data['id'], "jpg", 'HTML'));
            // 相册：外景相册
            $this->assign("imageList2", getAttachmentList("item:2", $data['id'], "jpg", 'HTML'));
            // 相册：户型相册
            $this->assign("imageList3", getAttachmentList("item:3", $data['id'], "jpg", 'HTML'));
            // 相册：样板相册
            $this->assign("imageList4", getAttachmentList("item:4", $data['id'], "jpg", 'HTML'));

            return $this->fetch('web@item/edit');
        }

        if (request()->isPost()) {
            $postData = input('post.');
            if (!empty($postData)) {
                $data = json_decode($postData['aoData'], true);

                if (true) {
                    $itemModel = new ItemModel;
                    $key = array('id', 'status', 'itemtype', 'itemname',
                        'provinceid', 'cityid', 'districtid', 'address', 'itemtype',
                        'developer', 'description'
                    );
                    $headData = formatPostData($data, $key);
                    if ($this->checkItem($headData, true)) {
                        return returnValue(1, "项目[" . $data['itemname'] . "]已存在");
                    }
                    $itemModel->save($headData, ['id' => $headData['id']]);
                }

                if (true) {
                    $otherModel = new ItemOtherModel;
                    $key = array('id', 'price', 'area',
                        'type1', 'type2', 'type3', 'type4',
                        'note1', 'note2', 'note3', 'note4', 'note5', 'note6'
                    );
                    $otherData = formatPostData($data, $key);
                    if ($otherModel->where('id', $otherData['id'])->find()) {
                        $otherModel->where('id', $otherData['id'])->update($otherData);
                    } else {
                        $otherModel->insert($otherData);
                    }
                }

                return returnValue(2, "编辑成功");
            } else {
                return returnValue(1, "提交异常");
            }
        }
    }
    // endregion


    // region 相册：刷新 image_ref()
    public function image_ref()
    {
        $data = input('post.');

        if (!empty($data)) {
            if (array_check(array('id', 'sign'), $data)) {
                $tableName = "Item:";
                switch ($data['sign']) {
                    case 0: $tableName .= "0"; break;
                    case 1: $tableName .= "1"; break;
                    case 2: $tableName .= "2"; break;
                    case 3: $tableName .= "3"; break;
                    case 4: $tableName .= "4"; break;
                }

                return getAttachmentList($tableName, $data['id'], "jpg", 'HTML');
            }
        }

        return '数据异常';
    }
    // endregion

    // region 相册：新增 image_add()
    public function image_add()
    {
        if (request()->isGet()) {
            $data = input('get.');

            if (!empty($data)) {
                $list = array();
                $list['id'] = $data['id'];
                $list['sign'] = $data['sign'];
                $this->assign("list", $list);
            }

            return $this->fetch('web@item/image_add');
        }

        if (request()->isPost()) {
            $data = input('post.');

            if (!empty($data)) {
                if (array_check(array('lineId', 'sign'), $data)) {
                    $tableName = "Item:";
                    switch ($data['sign']) {
                        case 0: $tableName .= "0"; break;
                        case 1: $tableName .= "1"; break;
                        case 2: $tableName .= "2"; break;
                        case 3: $tableName .= "3"; break;
                        case 4: $tableName .= "4"; break;
                    }

                    $isSuccess = attachmentUpload($data['lineId'], $tableName, 'HTML');

                    if ($isSuccess) {
                        return '上传成功';
                    } else {
                        return '上传失败';
                    }
                }
            }

            return '数据异常';
        }
    }
    // endregion

    // region 相册：删除 image_del()
    public function image_del()
    {
        $data = input('post.');

        if (!empty($data)) {

            if (array_check(array('id', 'sign'), $data)) {
                $tableName = "Item:";
                switch ($data['sign']) {
                    case 0: $tableName .= "0"; break;
                    case 1: $tableName .= "1"; break;
                    case 2: $tableName .= "2"; break;
                    case 3: $tableName .= "3"; break;
                    case 4: $tableName .= "4"; break;
                }

                $isSuccess = attachmentDelete($data['id'], $tableName, 'HTML');

                if ($isSuccess) {
                    return '删除成功';
                } else {
                    return '删除失败';
                }
            }
        }

        return '数据异常';
    }
    // endregion


    // region 静态页面：跟新
    public function refStaticHtml()
    {
        if (request()->isPost()) {
            $data = input('post.');

            if (array_check('id', $data)) {
                $c = new IndexController;
                $c->createItemInfoToHtml($data['id']);

                return returnValue(2, '生成成功');
            }
        }
        return returnValue(1, "提交异常");
    }
    // endregion

    // region 校验:项目是否存在
    public function checkItem($data, $sign)
    {
        $model = new ItemModel;
        $where = "itemname = '{$data['itemname']}' AND districtid = '{$data['districtid']}' AND itemtype = '{$data['itemtype']}'";

        if ($sign) {
            $where .= " and id <> '" . $data['id'] . "'";
        }

        $count = $model->where($where)->count();

        if ($count != 0) {
            return true;
        }
        return false;
    }
    // endregion
}