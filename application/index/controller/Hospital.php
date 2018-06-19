<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 医院
 */
namespace app\index\controller;

use app\index\model\DoctorModel;
use app\index\model\HospitalModel;
use app\index\model\HospitalRegionModel;

class Hospital extends Wap
{
	protected function _initialize()
	{
		//获得授权
		$this->getUserInfo();
		$this->assign('fansInfo',$this->fansInfo);
	}

	public function index()
	{
        $id=input('param.id',1);

		//读取地区
        $hospitalRegion=HospitalRegionModel::getAll();
        //读取当前医院
        $hospital=HospitalModel::getRidHos($id);

		//读取医院的经纬度
        $coordinate=explode(',',$hospital['coordinate']);
		if(count($coordinate)>1)
		{
			$abc[]=$coordinate[1];
			$abc[]=$coordinate[0];
			$hospital['coordinate']=implode(',',$abc);
		}

		//读取该医院的专家
		$doctor=DoctorModel::getHosDoc($id);
		$this->assign('doctor',$doctor);
		$this->assign('hospitalRegion',$hospitalRegion);
		$this->assign('hospital',$hospital);
		$this->assign('id',$id);
		return $this->fetch();
	}

}