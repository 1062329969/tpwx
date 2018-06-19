<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * 植发视频
 */

namespace app\admin\controller;
use app\admin\model\VideoModel;
use app\admin\model\ProjectModel;

class Video extends Admin
{
	protected $modelName='';

	public function _initialize()
	{
		parent::_initialize();
		$this->modelName=new VideoModel;
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
            //var_dump($data);die;
			$result=$this->validate($data,'ValidateClass.CK7');
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




			//读取项目
			$project=ProjectModel::getAll();
			$this->assign('project',$project);
			return $this->fetch();
		}
	}

	public function alter()
	{

		$request=request();
		if($request->method()=='POST')
		{
			$data=input('post.');

			$result=$this->validate($data,'ValidateClass.CK7');
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
			//读取项目
			$project=ProjectModel::getAll();
			$this->assign('project',$project);

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