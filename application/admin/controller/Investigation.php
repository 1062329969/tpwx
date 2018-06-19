<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 问卷调查控制器
 */

namespace app\admin\controller;


use app\admin\model\InvestigationModel;
use app\admin\model\InvestigationOptionModel;
use app\admin\model\InvestigationResultModel;
use think\Db;

class Investigation extends Admin
{
	protected $modelName='';

	public function _initialize()
	{
		parent::_initialize();
		$this->modelName=new InvestigationModel;
	}

	public function index()
	{
		$content=$this->modelName->getAll(15);
		$this->assign('content',$content);
		return $this->fetch();
	}

	public function result2()
	{
		$iid=input('param.iid');
		$content=InvestigationResultModel::getAll(15,$iid);

		foreach($content as $key=>$v)
		{
			$re=json_decode($v['result']);
			//$investigation=InvestigationOptionModel::getAll2($iid);
			//$arr=array();
			//foreach($investigation as $k=>$value)
			//{
                 //$arr[$key]['title']=$value[$k]['title'];
				 //$arr[$key]['result']=$re[$key];
			//}
			$content[$key]['result']=$re;


		}
		$this->assign('content',$content);
		return $this->fetch();
	}

	public function add()
	{
		$request=request();
		if($request->method()=='POST')
		{
			$data=input('post.');

			if($this->modelName->allowField(true)->save($data))
			{

				$this->success('添加成功',url('index'));
			}
			else
			{
				$this->error('添加失败',url('index'));
			}
		}
		else
		{
			return $this->fetch();
		}
	}

	public function alter()
	{

		$request=request();
		if($request->method()=='POST')
		{
			$data=input('post.');

			if($this->modelName->allowField(true)->save($data,['id'=>$data['id']]))
			{

				$this->success('修改成功',url('index'));
			}
			else
			{
				$this->error('修改失败',url('index'));
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

	public function option()
	{
		$iid=input('param.id');
		$content=InvestigationOptionModel::getAll(15,$iid);
		$this->assign('content',$content);
		$this->assign('iid',$iid);
		return $this->fetch();
	}


	public function optionAdd()
	{

		$request=request();
		if($request->method()=='POST')
		{
			$data=input('post.');
			$data['content']=str_replace("，",",",$data['content']);
			$investigationOptionModel=new InvestigationOptionModel();
			if($investigationOptionModel->allowField(true)->save($data))
			{

				$this->success('添加成功',url('option',['id'=>$data['iid']]));
			}
			else
			{
				$this->error('添加失败',url('option',['id'=>$data['iid']]));
			}
		}
		else
		{
			$iid=input('param.iid');
			$this->assign('iid',$iid);
			return $this->fetch();
		}

	}

	public function optionAlter()
	{
		$request=request();
		if($request->method()=='POST')
		{
			$data=input('post.');
			$data['content']=str_replace("，",",",$data['content']);
			$investigationOptionModel=new InvestigationOptionModel();
			if($investigationOptionModel->allowField(true)->save($data,['id'=>$data['id']]))
			{

				$this->success('修改成功',url('option',['id'=>$data['iid']]));
			}
			else
			{
				$this->error('修改失败',url('option',['id'=>$data['iid']]));
			}
		}
		else
		{
			$id=input('param.id');
			$investigationOptionModel=new InvestigationOptionModel();
			$content=$investigationOptionModel->get($id);
			$this->assign('content',$content);
			return $this->fetch();
		}
	}

	public function optionDel()
	{
		$id=input('param.id');
		$investigationOptionModel=new InvestigationOptionModel();
		if($investigationOptionModel->save(['del'=>1],['id'=>$id]))
		{
			echo '{"code":"1"}';
		}
		else
		{
			echo '{"code":"0"}';
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