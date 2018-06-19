<?php
/*
 * User: wafu7969
 * Date: 2017/7/25
 * Time: 13:42
 * 项目管理
 */

namespace app\admin\controller;
use app\admin\model\DoctorModel;
use app\admin\model\ProjectModel;
use app\admin\model\ProjectCasesImgsModel;

class Project extends Admin
{
	protected $modelName='';

	public function _initialize()
	{
		parent::_initialize();
		$this->modelName=new ProjectModel;
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

			$result=$this->validate($data,'ValidateClass.CK9');
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

			//多文件上传
			if(empty($data['default_img']))
			{
				$data['default_img']=0;
			}

			$imgs=array();
			$files = request()->file('pic3');
			if(count($files)>0)
			{
				foreach($files as $file)
				{

					// 移动到框架应用根目录/public/uploads/ 目录下
					$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
					if($info)
					{
						$returnImg=str_replace('\\','/','/uploads/'.$info->getSaveName());
						$imgs[]=$returnImg;
					}
					else
					{
						$this->error($file->getError());
					}
				}
			}

			if($this->modelName->allowField(true)->save($data))
			{
				//保存多图
				if(count($imgs)>0)
				{
					foreach($imgs as $k=>$v)
					{
						$projectCasesImgsModel=new ProjectCasesImgsModel;
						$projectCasesImgsModel->save(['pid'=>$this->modelName->id,'pic'=>$v]);
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
			//读取全部的医生
			$doctor=DoctorModel::getAll(100);
			$this->assign('doctor',$doctor);
			return $this->fetch();
		}
	}

	public function alter()
	{

		$request=request();
		if($request->method()=='POST')
		{
			$data=input('post.');

			if(!empty($data['doc_ids']))
			{
				$data['doc_ids']=implode(',',$data['doc_ids']);
			}
			else
			{
				$data['doc_ids']=0;
			}

			$result=$this->validate($data,'ValidateClass.CK9');
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

			//多文件上传
			$imgs=array();
			$imgs=$this->uploadMore($request,'pic3',0);

			if($this->modelName->allowField(true)->save($data,['id'=>$data['id']]))
			{
				//保存多图
				if(count($imgs)>0)
				{
					foreach($imgs as $k=>$v)
					{
						$projectCasesImgsModel=new ProjectCasesImgsModel;
						$projectCasesImgsModel->save(['pid'=>$this->modelName->id,'pic'=>$v]);
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
			//读取全部的医生
			$doctor=DoctorModel::getAll(100);
			$this->assign('doctor',$doctor);

			$content=$this->modelName->get($id);
			$this->assign('content',$content);

			//读取该问答的所有图片
			$proCasesImgs=ProjectCasesImgsModel::getAll($id);
			$this->assign('proCasesImgs',$proCasesImgs);

			return $this->fetch();
		}

	}

	//删除项目案例图片
	public function delImg()
	{
		$imgurl=input('post.imgurl');
		$id=input('post.id');
		$pid=input('post.pid');
		$result = @unlink ('.'.$imgurl);
		ProjectCasesImgsModel::where(['id'=>$id,'pid'=>$pid,'pic'=>$imgurl])->delete();
		return ['code'=>1];


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