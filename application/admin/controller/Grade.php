<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 脱发等级管理控制器
 */

namespace app\admin\controller;
use app\admin\model\GradeModel;


class Grade extends Admin
{
	protected $modelName='';

	public function _initialize()
	{
		parent::_initialize();
		$this->modelName=new GradeModel;
	}

	public function index()
	{
		$content=$this->modelName->getAll(15);
		$this->assign('content',$content);
		return $this->fetch();
	}

	public function add()
	{
		$request=request();
		if($request->method()=='POST')
		{
			$data=input('post.');

			if(!empty($data['doc_ids']))
            {
	            $data['doc_ids']=implode(',',$data['doc_ids']);
            }

			$result=$this->validate($data,'ValidateClass.CK36');
			if(true !== $result)
			{
				// 验证失败 输出错误信息
				$this->error($result);
			}

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


			$result=$this->validate($data,'ValidateClass.CK36');
			if(true !== $result)
			{
				// 验证失败 输出错误信息
				$this->error($result);
			}


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