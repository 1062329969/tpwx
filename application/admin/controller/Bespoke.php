<?php
/**
 * User: wafu7969
 * Date: 2017/7/25
 * Time: 18:36
 * 第一版的免费检测控制器 第二版中没有用
 */

namespace app\admin\controller;


use app\admin\model\BespokeModel;

class Bespoke extends Admin
{
	protected $modelName='';

	public function _initialize()
	{
		parent::_initialize();
		$this->modelName=new BespokeModel;
	}

	public function index()
	{
		$content=$this->modelName->getAll(15);
		$this->assign('content',$content);
		return $this->fetch();
	}

	public function alter()
	{

		$request=request();
		if($request->method()=='POST')
		{
			$data=input('post.');

			if($this->modelName->allowField(true)->save($data,['id'=>$data['id']]))
			{
				$this->success('修改成功');
			}
			else
			{
				$this->error('修改失败');
			}
		}
		else
		{
			$id=input('param.id');
			$content=$this->modelName->get($id);
			$this->assign('content',$content);
			return $this->fetch();
		}

	}


	public function del()
	{

		$id=input('param.id');
		if($this->modelName->save(['del'=>1],['id'=>$id]))
		{
			echo '{"code":"1"}';
		}
		else
		{
			echo '{"code":"0"}';
		}
	}

}