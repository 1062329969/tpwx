<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 植发项目
 */

namespace app\index\controller;


use app\index\model\DoctorModel;
use app\index\model\ContAdModel;
use app\index\model\ProjectModel;
use app\index\model\ProjectCasesImgsModel;

class Project extends  Wap
{
	protected function _initialize()
	{
		//获得授权
		$this->getUserInfo();
		$this->assign('fansInfo',$this->fansInfo);
	}

	public function index()
	{
        $project=ProjectModel::getAll();
		$this->assign('project',$project);
		//读取广告图片
		$projectAd=ContAdModel::getAll(3);
		$this->assign('projectAd',$projectAd);

		return $this->fetch();
	}

	public function detail()
	{
		$id=input('param.id');
		if(empty($id) || !is_numeric($id))
		{
			$this->error('参数错误');
		}

		$project=ProjectModel::getOne($id);

		if(empty($project))
		{
			$this->error('参数错误');
		}

		$this->assign('project',$project);
		//获得该项目推荐的医生
		$doctor=DoctorModel::getIds($project['doc_ids']);
		$this->assign('doctor',$doctor);

		//读取该项目下的所有案例图片
		$projectCases=ProjectCasesImgsModel::getAll($id);

		$this->assign('projectCases',$projectCases);

		return $this->fetch();
	}

}