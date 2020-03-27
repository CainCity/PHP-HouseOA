<?php
namespace app\customer\controller;

use app\customer\model\ChangeRule as ChangeRuleModel;
use app\index\controller\Auth as Auth;
use think\Db;

// 信息：变更规则
class ChangeRule extends Auth
{
    // region 主界面
    public function index()
    {
        if (request()->isGet()) {
            return $this->fetch('customer@ChangeRule/index');
        }

        if (request()->isPost()) {
            $postData = input('post.');
            $output = array();
            $returnData = array();
            $count = 0;

            if (!empty($postData)) {
                $data = json_decode($postData['aoData'], true);
                $key = array('aType', 'iDisplayLength', 'iDisplayStart');
                $s = formatPostData($data, $key);

                $where = "1 = 1";

                if (!empty($s['aType'])) {
                    $where .= " and cr.atype = {$s['aType']}";
                }

                /** 数据查询 */
                $sql =
                    "select cr.id, cr.atype, cr.islike, cr.description, " .
                    "       w1.name as oldTypeName, " .
                    "       w2.name as newTypeName, " .
                    "       w3.name as aTypeName " .
                    "  from tp5_change_rule cr " .
                    "  left join tp5_wordbook w1 on cr.oldid = w1.id " .
                    "  left join tp5_wordbook w2 on cr.newid = w2.id " .
                    "  left join tp5_wordbook w3 on cr.atype = w3.id " .
                    " where " . $where .
                    " order by cr.oldid, cr.newid " .
                    " limit " . $s['iDisplayStart'] . "," . $s['iDisplayLength'];
                $aList = Db::query($sql);
                if (!empty($aList)) {
                    $tempData = array();
                    foreach ($aList as $aRow) {
                        if ($aRow['islike'] == '2') {
                            $isLike = "是";
                        } else {
                            $isLike = "否";
                        }

                        if ($aRow['aTypeName'] == '') {
                            $aTypeName = "全部客户";
                        } else {
                            $aTypeName = $aRow['aTypeName'];
                        }

                        $TypeName = "【" . $aRow['oldTypeName'] . "】→【" . $aRow['newTypeName'] . "】";

                        $tempData[0] = $aRow['id'];
                        $tempData[1] = $aTypeName;
                        $tempData[2] = $isLike;
                        $tempData[3] = $TypeName;
                        $tempData[4] = $aRow['description'];

                        $returnData[] = $tempData;
                    }

                    /** 数据数量查询 */
                    $sql =
                        "select count(1) as c " .
                        "  from tp5_change_rule cr " .
                        "  left join tp5_wordbook w1 on cr.oldid = w1.id " .
                        "  left join tp5_wordbook w2 on cr.newid = w2.id " .
                        "  left join tp5_wordbook w3 on cr.atype = w3.id " .
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

    // region 新增
    public function add()
    {
        if (request()->isGet()) {
            $this->assign("aList", cacheData('wordbook', config('data.客户类型'))); // 类型

            $this->assign("bList", cacheData('wordbook', config('data.客户池'))); // 客户池

            return $this->fetch('customer@ChangeRule/add');
        }

        if (request()->isPost()) {
            $data = input('post.');
            if ($data) {
                $model = new ChangeRuleModel;
                $model['id'] = getID(); // 生成新ID

                // 输出结果
                if ($model->save($data)) {
                    return returnValue(2, "添加成功");
                } else {
                    return returnValue(1, $model->getError());
                }
            } else {
                return returnValue(1, "提交异常");
            }
            return $result;
        }
    }
    // endregion

    // region 编辑
    public function edit()
    {
        if (request()->isGet()) {
            $data = input('get.');

            // 主信息
            $model = new ChangeRuleModel;
            $list = $model->where('id', $data['id'])->find();
            $this->assign('list', $list);

            $this->assign("aList", cacheData('wordbook', config('data.客户类型'))); // 类型

            $this->assign("bList", cacheData('wordbook', config('data.客户池'))); // 客户池

            // 渲染界面
            return $this->fetch('customer@ChangeRule/edit');
        }

        if (request()->isPost()) {
            $data = input('post.');
            if ($data) {
                $model = new ChangeRuleModel;

                // 输出结果
                if ($model->save($data, ['id' => $data['id']])) {
                    return returnValue(2, "编辑成功");
                } else {
                    return returnValue(1, $model->getError());
                }
            } else {
                return returnValue(1, "提交异常");
            }

            return $result;
        }
    }
    // endregion
}