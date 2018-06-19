<?php


namespace app\index\event;

use app\index\model\IntegralModel;
use app\index\model\UserInfoModel;

class Integral
{

	//增加积分
	/**
	 * @param $uid    指定的会员id
	 * @param int $addNums  增加多少积分
	 * @param string $event 什么业务行为导致的增加
	 */
	public function addition($uid,$integralNums=0,$event='')
	{


		$UserInfoModel=new UserInfoModel();
		$userInfo=$UserInfoModel->get($uid);

		if(empty($userInfo))
        {
        	return false;
        }

		$updateNums=$userInfo['integral']+$integralNums;

		$UserInfoModel->save(['integral'=>$updateNums],['id'=>$uid]);

        //记录积分的增减的记录
		$integral=new IntegralModel();
		$integral->save(['uid'=>$uid,'type'=>0,'event'=>$event,'nums'=>$integralNums,'original'=>$userInfo['integral']]);

		return $updateNums;

	}

	public function reduce($uid,$integralNums=0,$event='')
	{


		$UserInfoModel=new UserInfoModel;
		$userInfo=$UserInfoModel->get($uid);

		if(empty($userInfo))
		{
			return false;
		}

		$updateNums=$userInfo['integral']-$integralNums;

		if($UserInfoModel->save(['integral'=>$updateNums],['id'=>$uid]))
		{
			//记录积分的增减的记录
			$integral=new IntegralModel();
			$integral->save(['uid'=>$uid,'type'=>1,'event'=>$event,'nums'=>$integralNums,'original'=>$userInfo['integral']]);
			return true;
		}
		else
		{
			return false;
		}

	}

}