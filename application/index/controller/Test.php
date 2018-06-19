<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 测试用的 不用理会
 */

namespace app\index\controller;


use app\index\model\AskModel;
use think\Controller;

class Test extends Controller
{

	public function index()
	{
		//$ask=AskModel::getAll2();
		//echo json_encode($ask);
		$AskModel=new AskModel();
		$user = $AskModel->find(1);
		$user->appendRelationAttr('user_info', 'wechaname');
// 输出Profile关联模型的email属性
		echo $user->wechaname;
	}

}