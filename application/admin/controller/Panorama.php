<?php
/*
 * User: wafu7969
 * Date: 2017/7/25
 * Time: 13:42
 * 360全景 第一版的  第二版没有用
 */

namespace app\admin\controller;

use app\admin\model\PanoramaModel;


class Panorama extends Admin
{
	protected $modelName='';

	public function _initialize()
	{
		parent::_initialize();
		$this->modelName=new PanoramaModel();
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

			$result=$this->validate($data,'ValidateClass.CK18');
			if(true !== $result)
			{
				// 验证失败 输出错误信息
				$this->error($result);
			}

			//上传图片
			$pic=$this->upload($request,'pic');
			if(!empty($pic))
			{
				$data['pic']=$pic;
			}

			//上传图片
			$pic2=$this->upload($request,'pic2');
			if(!empty($pic2))
			{
				$data['pic2']=$pic2;
			}

			if($this->modelName->allowField(true)->save($data))
			{
				$this->success('添加成功',url('index'));
			}
			else
			{
				$this->error('添加失败');
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

			$result=$this->validate($data,'ValidateClass.CK18');
			if(true !== $result)
			{
				// 验证失败 输出错误信息
				$this->error($result);
			}

			//上传图片
			$pic=$this->upload($request,'pic');
			if(!empty($pic))
			{
				$data['pic']=$pic;
			}
			else
			{
				unset($data['pic']);
			}

			//上传图片
			$pic2=$this->upload($request,'pic2');
			if(!empty($pic2))
			{
				$data['pic2']=$pic2;
			}
			else
			{
				unset($data['pic2']);
			}

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