<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 内部案例
 */

namespace app\admin\controller;

use app\admin\model\NcaseModel;
use app\admin\model\ProjectModel;
use app\admin\model\NcaseImgsModel;

class Ncase extends Admin
{
	protected $modelName='';

	public function _initialize()
	{
		parent::_initialize();
		$this->modelName=new NcaseModel;
	}

	public function index()
	{
		$content=$this->modelName->getAll(15);
		$this->assign('content',$content);
		return $this->fetch();
	}

	public function uploadindex()
	{
		$aid=input('param.id');
		$this->assign('aid',$aid);
		$content=NcaseImgsModel::getAll($aid);
		$this->assign('content',$content);
		return $this->fetch();
	}

	public function uploadadd()
	{
		$request=request();
		if($request->method()=='POST')
		{
			$data=input('param.');

			//上传图片
			$pic=$this->upload($request,'pic');
			if(!empty($pic))
			{
				$data['pic']=$pic;
			}
			$ncaseImgsModel=new NcaseImgsModel();
			if($ncaseImgsModel->allowField(true)->save($data))
			{
				$this->success('添加成功',url('uploadindex',['id'=>$data['aid']]));
			}
			else
			{
				$this->error('添加失败',url('uploadindex',['id'=>$data['aid']]));
			}
		}
		else
		{
			$aid=input('param.aid');
			$this->assign('aid',$aid);

			return $this->fetch();
		}
	}



	public function add()
	{
		$request=request();
		if($request->method()=='POST')
		{
			$data=input('post.');

			$result=$this->validate($data,'ValidateClass.CK20');
			if(true !== $result)
			{
				// 验证失败 输出错误信息
				$this->error($result);
			}

			//多文件上传
			if(empty($data['default_img']))
			{
				$data['default_img']=0;
			}

			$key=0;  //默认图片的位置
			$imgs=array();
			$files = request()->file('pic');
			if(count($files)>0)
			{
				foreach($files as $file)
				{

					// 移动到框架应用根目录/public/uploads/ 目录下
					$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
					if($info)
					{

						$returnImg=str_replace('\\','/','/uploads/'.$info->getSaveName());
						//生成缩略图
						$returnImg=$this->thumbnail($file,$returnImg);

						if($key==$data['default_img'])
						{
							$data['pic']=$returnImg;
						}

						$imgs[]=$returnImg;
					}
					else
					{
						$this->error($file->getError());
					}

					$key=$key+1;

				}
			}

			if($this->modelName->allowField(true)->save($data))
			{
				//保存多图
				if(count($imgs)>0)
				{
					foreach($imgs as $k=>$v)
					{
						$newsImgsModel=new NcaseImgsModel;
						$newsImgsModel->save(['aid'=>$this->modelName->id,'pic'=>$v]);
					}
				}

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

	public function uploadalter()
	{

		$request=request();

		if($request->method()=='POST')
		{
			$data=input('param.');

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

			$ncaseImgsModel=new NcaseImgsModel();
			if($ncaseImgsModel->allowField(true)->save($data,['id'=>$data['id']]))
			{
				$this->success('修改成功',url('uploadindex',['id'=>$data['aid']]));
			}
			else
			{
				$this->error('修改失败',url('uploadindex',['id'=>$data['aid']]));
			}

		}
		else
		{
			$id=input('param.id');
			$aid=input('param.aid');
			$this->assign('aid',$aid);
			$ncaseImgsModel=new NcaseImgsModel();
			$content=$ncaseImgsModel->get($id);
			$this->assign('content',$content);
			return $this->fetch();
		}

	}

	public function alter()
	{

		$request=request();
		if($request->method()=='POST')
		{
			$data=input('post.');

			$result=$this->validate($data,'ValidateClass.CK21');
			if(true !== $result)
			{
				// 验证失败 输出错误信息
				$this->error($result);
			}

			//多文件上传
			$imgs=array();
			$imgs=$this->uploadMore($request,'pic2');

			if($this->modelName->allowField(true)->save($data,['id'=>$data['id']]))
			{
				//保存多图
				if(count($imgs)>0)
				{
					foreach($imgs as $k=>$v)
					{
						$newsImgsModel=new NcaseImgsModel;
						$newsImgsModel->save(['aid'=>$this->modelName->id,'pic'=>$v]);
					}
				}

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

			//读取该新闻的所有图片
			$newsImgs=NcaseImgsModel::getAll($id);
			$this->assign('newsImgs',$newsImgs);

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


	public function uploaddel()
	{

		$id=input('param.id');
		$ncaseImgsModel=new NcaseImgsModel();
		if($ncaseImgsModel->save(['del'=>1],['id'=>$id]))
		{
			echo '{"code":"1"}';
		}
		else
		{
			echo '{"code":"0"}';
		}
	}

}