<?php
namespace app\api\controller;

use app\item\model\item as itemModel;
use think\Controller;
use think\Db;

// 访问日志
class Visit extends Controller
{
    protected $DBConfig = "DBConfig_00"; // 数据仓库

    public function index()
    {
        return $this->add();
    }

    /**
     * 新增访问记录
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    protected function add(){
        if (request()->isPost()) {
            $postData = input('post.');
            if (!empty($postData)) {
                if (!array_check('aoData', $postData)) {
                    return returnValue('1', 'HD00001:异常请求');
                }

                $data = json_decode($postData['aoData'], true);
                $arrKey = array('thisweburl', 'fromweburl', 'useragent');
                $s = formatPostData($data, $arrKey);

                // 当前页地址
                $thisUrl = $_SERVER['HTTP_REFERER'];
                // 来源页地址
                $fromUrl = $s['fromweburl'];
                // 客户端信息
                $userAgent = $_SERVER['HTTP_USER_AGENT'];

                // 项目是否为空
                $temp1 = explode('.html', $thisUrl)[0];
                $temp2 = explode('/', $temp1);
                $code = end($temp2);
                $model = new ItemModel;
                $item = $model->field('id, itemName as name')->where("unix_timestamp(ctime) = '{$code}'")->find();
                if (empty($item)) {
                    return json_encode(returnValue(1, "HD00002:异常请求"));
                }

                $now = time();
                $ID = getID();

                // 解析信息：关键字
                $extend = new \SmallTool\SearchKeyword();
                $arrKeyword = $extend->KeywordInformation($fromUrl);
                // 解析信息：客户端
                $extend = new \SmallTool\Device();
                $arrDevice = $extend->DeviceInformation($userAgent);
                // 解析信息：IP
                $IP = getIP();
                $IPInfo = IPInformation($IP);

                $HData = array();
                $OData = array();

                // 主表信息
                if (true) {
                    $HData['id'] = $ID; // 唯一码
                    $HData['ctime'] = $now; // 提交时间

                    $HData['item'] = $item['name']; // 项目
                    $HData['device'] = $arrDevice['DEVICE']; // 设备
                    $HData['brand'] = $arrDevice['BRAND']; // 设备品牌
                    $tempH['os'] = $arrDevice['BRAND_OS']; // 设备系统

                    $HData['ip'] = $IP; // IP
                    $HData['isp'] = $IPInfo['isp']; // IP归属(运营商)
                    $HData['country'] = $IPInfo['country']; // IP归属(国)
                    $HData['province'] = $IPInfo['region']; // IP归属(省)
                    $HData['city'] = $IPInfo['city']; // IP归属(市)
                    $HData['district'] = ''; // IP归属(区)

                    $HData['url'] = explode('?', $thisUrl)[0]; // 访问网址
                    $HData['searchengines'] = $arrKeyword['SEARCH_ENGINES']; // 搜索引擎
                    $HData['keyword'] = $arrKeyword['KEYWORD']; // 搜索关键词
                }

                // 附表信息
                if (true) {
                    $OData['id'] = $ID; // 唯一码
                    $OData['thisurl'] = $thisUrl; // 当前网址
                    $OData['fromurl'] = $fromUrl; // 上一级网址
                    $OData['useragent'] = $userAgent; // 浏览器及设备信息
                }

                Db::startTrans();
                try {
                    Db::connect($this->DBConfig)->table('z_visit')->insert($HData); // 批量添加
                    Db::connect($this->DBConfig)->table('z_visit_other')->insert($OData); // 批量添加
                    Db::commit();

                    return returnValue('2', '成功');
                } catch (\Exception $e) {
                    Db::rollback();
                    return returnValue('1', '失败');
                }
            }
        }

        return returnValue('1', 'HD99999:异常请求');
    }
}