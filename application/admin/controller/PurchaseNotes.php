<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 购买须知 第一版用 第二版没用
 */

namespace app\admin\controller;


use app\admin\model\PurchaseNotesModel;
use think\Controller;

class PurchaseNotes extends Admin
{
	public function index()
	{
		$purchaseNotes=PurchaseNotesModel::get(1);
		$this->assign('purchaseNotes',$purchaseNotes);
		return $this->fetch();
	}

	public function alter()
	{
		$data=input('post.');
		if(empty($data['title']))
		{
			$this->error('标题不能为空');
		}

		$data['update_time']=time();
		$purchaseNotes=new PurchaseNotesModel();
		$purchaseNotes->save($data,['id'=>1]);
		$this->success('修改成功');
	}

}