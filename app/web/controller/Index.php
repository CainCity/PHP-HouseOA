<?php
namespace app\web\controller;

use think\Db;
use app\index\controller\Auth as Auth;
use app\item\model\Item as ItemModel;
use app\base\model\Attachment as AttachmentModel;

class Index extends Auth
{
    public function index()
    {
        $this->createIndexToHtml();

        $this->createItemPageToHtml("apartment");
        $this->createItemPageToHtml("shops");
        $this->createItemPageToHtml("villas");

        $this->createContactUsToHtml();
        //$this->createTelToHtml();

        return '重新生成已完成';
    }

    /**
     * 创建静态页面：项目资料详细
     * @param string $id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function createItemInfoToHtml($id = '')
    {
        $where = '';
        if ($id != '') {
            $where .= " and i.id = '{$id}'";
        } else {
            $where .= " and i.url <> ''";
        }
        $sql =
            "select i.id, i.itemName, i.itemType as type, o.area, o.price, unix_timestamp(i.ctime) as num, " .
            "	    i.provinceId, i.cityId, i.districtId, i.address, " .
            "       o.type1, o.type2, o.type3, " .
            "       o.note1, o.note2, o.note3, o.note4, o.note5, o.note6 " .
            "  from tp5_item i " .
            "  left join tp5_item_other o on i.id = o.id " .
            " where 1 = 1 " . $where .
            " order by i.uTime desc ";
        $list = Db::query($sql);
        if (!empty($list)) {
            foreach ($list as $row) {
                // 基础信息
                if (true) {
                    $provinceList = cacheProvince($row['provinceId'], true); // 省
                    $cityList = cacheCity($row['provinceId'], true); // 市
                    $districtList = cacheDistrict($row['cityId'], true); // 区

                    $row['provinceName'] = $provinceList[$row['provinceId']];
                    $row['cityName'] = $cityList[$row['cityId']];
                    $row['districtName'] = $districtList[$row['districtId']];

                    $delimiter = "{|}";
                    if ($row['note1'] == "") {
                        $row['note1'] = array();
                    } else {
                        $str = str_replace(PHP_EOL, $delimiter, $row['note1']);
                        $row['note1'] = explode($delimiter, $str);
                    }

                    if ($row['note2'] == "") {
                        $row['note2'] = array();
                    } else {
                        $str = str_replace(PHP_EOL, $delimiter, $row['note2']);
                        $row['note2'] = explode($delimiter, $str);
                    }

                    if ($row['note3'] == "") {
                        $row['note3'] = array();
                    } else {
                        $str = str_replace(PHP_EOL, $delimiter, $row['note3']);
                        $row['note3'] = explode($delimiter, $str);
                    }

                    if ($row['note4'] == "") {
                        $row['note4'] = array();
                    } else {
                        $str = str_replace(PHP_EOL, $delimiter, $row['note4']);
                        $note4 = explode($delimiter, $str);
                        $row['note4'] = array();
                        foreach ($note4 as $r) {
                            $row['note4'][] = explode('：', $r);
                        }
                    }

                    if ($row['note5'] == "") {
                        $row['note5'] = array();
                    } else {
                        $str = str_replace(PHP_EOL, $delimiter, $row['note5']);
                        $row['note5'] = explode($delimiter, $str);
                    }

                    if ($row['note6'] == "") {
                        $row['note6'] = array();
                    } else {
                        $str = str_replace(PHP_EOL, $delimiter, $row['note6']);
                        $row['note6'] = explode($delimiter, $str);
                    }
                }
                // 小标签
                if (true) {
                    $typeList = cacheData('wordbook', config('data.项目性质'), true); // 项目性质
                    $typeList1 = cacheData('wordbook', config('data.项目类型'), true); // 项目类型
                    $typeList2 = cacheData('wordbook', config('data.销售情况'), true); // 销售情况
                    $typeList3 = cacheData('wordbook', config('data.装修标准'), true); // 装修标准

                    $row['typeName'] = formatNameById($row['type'], $typeList, 'array');
                    $row['typeName1'] = $row['type1'] == "" ? $row['typeName1'] = "" : $typeList1[$row['type1']];
                    $row['typeName2'] = $row['type2'] == "" ? $row['typeName2'] = "" : $typeList2[$row['type2']];
                    $row['typeName3'] = formatNameById($row['type3'], $typeList3, 'array');
                }
                // 相册
                if (true) {
                    $images0 = array();
                    $images1 = array();
                    $images2 = array();
                    $images3 = array();
                    $images4 = array();

                    $imageList = $this->getAttachmentList("item:", $row['id'], "jpg");

                    if (!empty($imageList)) {
                        foreach ($imageList as $r) {
                            switch ($r['type']) {
                                case "Item:0": $images0[] = array('url' => $r['url'], 'alt' => $r['alt']);
                                    break;
                                case 'Item:1': $images1[] = array('url' => $r['url'], 'alt' => $r['alt']); break;
                                case 'Item:2': $images2[] = array('url' => $r['url'], 'alt' => $r['alt']); break;
                                case 'Item:3': $images3[] = array('url' => $r['url'], 'alt' => $r['alt']); break;
                                case 'Item:4': $images4[] = array('url' => $r['url'], 'alt' => $r['alt']); break;
                            }
                        }
                    }

                    $row['images0'] = $images0;
                    $row['images1'] = $images1;
                    $row['images2'] = $images2;
                    $row['images3'] = $images3;
                    $row['images4'] = $images4;
                }
                $this->assign("data", $row);
                $html = $this->fetch('web@index/show');
                $this->saveFile('show', $row['num'], $html);

                // 数据更新：项目网页地址
                $ItemModel = new ItemModel;
                $ItemData['url'] = $row['num'] . '.html';
                $ItemModel->save($ItemData, ['id' => $row['id']]);
            }
        }
    }

    /**
     * 创建静态页面：项目资料列表
     * @param string $sign 类型
     */
    public function createItemPageToHtml($sign = "") {
        $limit = 16;

        $data = $this->getData($sign, $limit);

        $this->assign("type", $data['type']);
        $this->assign("t1", $data['t1']);
        $this->assign("t2", $data['t2']);

        if (!empty($data['data'])) {
            $list = $data['data'];

            $i = 0;
            $page = 0;
            $tempData = array();
            foreach ($list as $row) {
                $i++;

                $tempData[] = $row;

                if ($i % $limit == 0 || $i == count($list)) {
                    $page++;
                    $this->assign("data", $tempData);

                    $html = $this->fetch('web@index/list');
                    $this->saveFile($sign, $page, $html);

                    if ($page == 1) {
                        $this->saveFile($sign, "index", $html);
                    }

                    // 清空数组
                    array_splice($tempData, 0, count($tempData));
                }
            }

        } else {
            $page = 1;
            $this->assign("data", array());

            $html = $this->fetch('web@index/list');
            $this->saveFile($sign, $page, $html);

            if ($page == 1) {
                $this->saveFile($sign, "index", $html);
            }
        }
    }

    /**
     * 创建静态页面：首页
     */
    public function createIndexToHtml() {
        $data = array();
        $data[] = $this->getData("apartment");
        $data[] = $this->getData("shops");
        $data[] = $this->getData("villas");

        $this->assign('data', $data);
        $html = $this->fetch('web@index/index');
        $this->saveFile('', 'index', $html);
    }

    /**
     * 创建静态页面：联系我们
     */
    public function createContactUsToHtml() {
        $note = getConfigValue('company.profile');
        $noteList = explode('\r\n', $note);
        $this->assign('noteList', $noteList);

        $html = $this->fetch('web@index/page');
        $this->saveFile('about', 'index', $html);
    }

    /**
     * @param string $linkTable 关联表名
     * @param string $linkID 关联单据ID
     * @param string $suffix 附件后缀
     * @return false|\PDOStatement|string|\think\Collection 附件信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function getAttachmentList($linkTable, $linkID, $suffix)
    {
        $model = new AttachmentModel;
        $field = "linkTable as type, oldName as alt, path as url";
        $where = "linkTable like '{$linkTable}%' and linkID = '{$linkID}' and suffix = '{$suffix}'";
        $order = "linkTable, convert(oldName using gbk)";
        $list = $model->field($field)->where($where)->orderRaw($order)->select();

        return $list;
    }

    /**
     * 获取数据
     * @param string $sign
     * @param int $limit
     * @return array
     */
    protected function getData($sign = "", $limit = 4) {
        $returnValue = array();

        $t1 = "";
        $t2 = "";
        $type = 0;
        $where = "";
        $data = array();

        switch ($sign) {
            case 'apartment': // 全新楼盘
                $t1 = "全新楼盘";
                $t2 = "New Apartment";
                $type = 1;
                $where .=
                    " and locate('" . config('data.商铺') . "', o.type3) = 0" .
                    " and locate('" . config('data.别墅') . "', o.type3) = 0";
                break;
            case 'shops': // 商铺
                $t1 = "商铺";
                $t2 = "Shops";
                $type = 2;
                $where .= " and locate('" . config('data.商铺') . "', o.type3)";
                break;
            case 'villas': // 别墅
                $t1 = "别墅";
                $t2 = "Villas";
                $type = 3;
                $where .= " and locate('" . config('data.别墅') . "', o.type3)";
                break;
            default: // 其它
                $where .= " and 1 <> 1";
        }

        $sql =
            "select i.provinceId, i.cityId, i.districtId, i.itemName, o.price, o.area, v.path, unix_timestamp(i.ctime) as fileName " .
            "  from tp5_item i " .
            "  left join tp5_item_other o on i.id = o.id " .
            "  left join ( " .
            "select linkId as id, oldName, suffix, path " .
            "  from tp5_attachment " .
            " where linkTable = 'Item:0' and suffix = 'jpg' " .
            "  ) v on i.id = v.id " .
            " where v.id is not null " . $where .
            " order by i.uTime desc" .
            " limit " . $limit;

        $list = Db::query($sql);
        if (!empty($list)) {
            foreach ($list as $row) {
                $provinceList = cacheProvince($row['provinceId'], true);
                $cityList = cachecity($row['provinceId'], true);
                $districtList = cachedistrict($row['cityId'], true);

                $province = $provinceList[$row['provinceId']];
                $city = $cityList[$row['cityId']];
                $district = $districtList[$row['districtId']];

                if ($province == $city) {
                    $tempData['addr'] = "[{$city}-{$district}]";
                } else {
                    $tempData['addr'] = "[{$province}-{$city}-{$district}]";
                }

                $tempData['url'] = $row['fileName'];
                $tempData['item'] = $row['itemName'];
                $tempData['price'] = $row['price'];
                $tempData['area'] = $row['area'];
                $tempData['img'] = $row['path'];

                $data[] = $tempData;
            }

            $returnValue['data'] = $data;
        }

        $returnValue['t1'] = $t1;
        $returnValue['t2'] = $t2;
        $returnValue['type'] = $type;
        $returnValue['data'] = $data;

        return $returnValue;
    }

    /**
     * 保存文件
     * @param string $folderName 文件夹名
     * @param string $fileName 文件名
     * @param string $content 文件内容
     * @param string $suffix 文件后缀
     */
    protected function saveFile($folderName = "", $fileName = "", $content = "", $suffix = "")
    {
        if ($suffix == "") { $suffix = config('web.html_file_suffix'); }

        $filepath = config('web.html_path') . $folderName . '/';

        if (!file_exists($filepath)) { mkdir($filepath, 0777, true); }

        $filename = $filepath . $fileName . $suffix;

        file_put_contents($filename, $content);
    }
}