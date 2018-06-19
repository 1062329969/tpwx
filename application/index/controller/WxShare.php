<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 分享记录
 */

namespace app\index\controller;


use app\index\event\Integral;
use think\Db;

class WxShare extends Wap
{
	protected function _initialize()
	{
		//获得授权
		$this->getUserInfo();
		$this->assign('fansInfo',$this->fansInfo);
	}

	public function addShare()
	{
		$data=input('param.');
		//判断今日分享了吗
		$share_log=Db::name('share_log')->where(['uid'=>$this->fansInfo['id'],'share_time'=>strtotime(date('Y-m-d',time()))])->find();
		if(empty($share_log))
		{
			//记录分享
			$data['uid']=$this->fansInfo['id'];
			$data['share_time']=strtotime(date('Y-m-d',time()));
			Db::name('share_log')->insert($data);

			//记录分享积分
			$integral=new Integral();
			$integral->addition($this->fansInfo['id'],config('sharejf'),'分享');
		}
		return '';


	}

}