<?php
namespace app\search\controller;

use app\finance\model\AssetType as AssetTypeModel;
use app\index\controller\Auth as Auth;

class AssetType extends Auth
{
    // region 查询弹出窗体：会计科目
    public function index()
    {
        if (request()->isGet()) {
            $data = input('get.');

            // 项目
            $model = new AssetTypeModel;
            $aList = $model->where("atype = {$data['type']} and Pid = '0'")->field("id, name")->order('mSort')->select();
            $this->assign("aList", $aList);

            return $this->fetch('search@AssetType/index');
        }

        if (request()->isPost()) {
            $postData = input('post.');
            $output = array();
            $returnData = array();
            $count = 0;

            if (!empty($postData)) {
                $data = json_decode($postData['aoData'], true);
                $key = array("sid", "iDisplayLength", "iDisplayStart");
                $s = formatPostData($data, $key);

                $where = "1 = 1 and status = 2 ";

                if (!empty($s['sid'])) {
                    $where .= " and pid = '{$s['sid']}'";
                }

                $model = new AssetTypeModel;
                $list = $model
                    ->field("id, name, description")
                    ->where($where)
                    ->order("mSort")->select();

                if (!empty($list)) {
                    $tempData = array();
                    foreach ($list as $aRow) {
                        $tempData[0] = $aRow['id'];
                        $tempData[1] = $aRow['name'];
                        $tempData[2] = $aRow['description'];
                        $returnData[] = $tempData;
                    }

                    $count = $model->where($where)->count();
                }
            }

            $output['aaData'] = $returnData;
            $output['iTotalDisplayRecords'] = $count;    //如果有全局搜索，搜索出来的个数
            $output['iTotalRecords'] = $count; //总共有几条数据

            return json($output);
        }
    }
    // endregion
}