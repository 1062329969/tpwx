<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 每日签到
 */

namespace app\index\controller;
use ApiOauth\ApiOauth;
use app\index\event\Integral;
use Calendar\Calendar;
use think\Config;
use think\Db;

class Sign extends Wap
{

	protected $timeStamp;
	protected $nonceStr;
	protected $signature;
	protected $shareTitle;
	protected $shareDesc;
	protected $shareLink;
	protected $shareImgUrl;

	protected function _initialize()
	{
		//获得授权
		$this->getUserInfo();
		$this->assign('fansInfo',$this->fansInfo);

		//微信jdk 验证
		$apiOauth=new ApiOauth();
		$ticket = $apiOauth->getJsApiTicket(Config::get('Appid'),Config::get('Appsecret'));
		$this->timeStamp = time();
		$this->nonceStr  = rand(100000,999999);
		$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		//获得jsdk签名
		$this->signature = $apiOauth->getSignature($this->nonceStr,$ticket,$this->timeStamp,$url);
		$this->assign('timeStamp',$this->timeStamp);
		$this->assign('nonceStr',$this->nonceStr);
		$this->assign('signature',$this->signature);
	}

	public function index()
    {

	    //今日是否签到
    	$return=Db::name('sign_log')->where(['uid'=>$this->fansInfo['id'],'sign_time'=>strtotime(date('Y-m-d',time()))])->find();
        if(empty($return))
        {
	        //计算今日积分
            //获得最后的签到记录
	        $lastSign=Db::name('sign_log')->where(['uid'=>$this->fansInfo['id']])->order('id desc')->find();


	        //为空说明是第一次签到
	        $next_time=strtotime(date("Y-m-d",strtotime("+1 day")));  //连续签到的下次时间
	        if(empty($lastSign))
	        {
		        //今日签到的积分
		        $todaySign=config('signjf');
	        	Db::name('sign_log')->insert(['uid'=>$this->fansInfo['id'],'sign_time'=>strtotime(date('Y-m-d',time())),'next_time'=>$next_time,'sign_num'=>1,'today_sign'=>$todaySign]);
	        }

	        else
	        {
		        //连续签到
	        	if($lastSign['next_time']==strtotime(date('Y-m-d',time())))
		        {
			        //获得连续签到规则
		        	$conSignjf=explode(',',str_replace('，',',',config('conSignjf')));

			        foreach($conSignjf as $key=>$value)
			        {
				        $signArr=explode('|',$value);
				        if($lastSign['sign_num']==$signArr[0])
				        {
				        	//今日签到的积分
					        $todaySign=$signArr[1]+config('signjf');
					        break;
				        }
				        else
				        {
					        $todaySign=config('signjf');
				        }
			        }

		        	Db::name('sign_log')->insert(['uid'=>$this->fansInfo['id'],'sign_time'=>strtotime(date('Y-m-d',time())),'next_time'=>$next_time,'sign_num'=>$lastSign['sign_num']+1,'today_sign'=>$todaySign]);
		        }
		        //没有连续 重新开始签到
		        else
		        {
			        //今日签到的积分
			        $todaySign=config('signjf');
		        	Db::name('sign_log')->insert(['uid'=>$this->fansInfo['id'],'sign_time'=>strtotime(date('Y-m-d',time())),'next_time'=>$next_time,'sign_num'=>1,'today_sign'=>$todaySign]);
		        }
	        }


	        $this->assign('sign',1);
	        //记录签到积分
	        $integral=new Integral();
	        $integral->addition($this->fansInfo['id'],$todaySign,'签到');
        }
        else
        {
	        $this->assign('sign',0);

        }

	    //获得最后的签到记录
	    $lastSign=Db::name('sign_log')->where(['uid'=>$this->fansInfo['id']])->order('id desc')->find();
        //今天签到的积分数
	    $this->assign('todaySign',$lastSign['today_sign']);
	    //连续签到天数
	    $this->assign('signNum',$lastSign['sign_num']);
	    //签到的总数
	    $signCount=Db::name('sign_log')->where(['uid'=>$this->fansInfo['id']])->count();
	    $this->assign('signCount',$signCount);

    	$date=input('param.date',0);
	    if(empty($date))
	    {
		    $year = date('Y');
		    $month = date('n');
		    $up=date('Y-m',strtotime('-1 month'));
		    $next=date('Y-m',strtotime('+1 month'));
		    $current=date('Y年m月',time());

	    }
	    else
	    {
		    $year = date('Y',strtotime($date));
		    $month = date('m',strtotime($date));
		    $current=date('Y年m月',strtotime($date));
		    $up=date('Y-m',strtotime(date('Y',strtotime($date)).'-'.(date('m',strtotime($date))-1)));
		    $next=$this->getNextMonth($date);


	    }

    	$util = new Calendar();

        $calendar = $util->threshold($year, $month);//获取各个边界值
        $caculate = $util->caculate($calendar);//计算日历的天数与样式
        $draws = $util->draw($caculate);//画表格，设置table中的tr与td

        $this->assign('year',$year);
        $this->assign('month',$month);
        $this->assign('draws',$draws);
	    $this->assign('up',$up);
	    $this->assign('next',$next);
	    $this->assign('current',$current);

	    //生成分享的内容
	    $this->assign('aid',0);
	    $this->assign('shareType',6);
	    $this->shareTitle='签到赢积分';   // 分享标题
	    $this->shareDesc='';    // 分享描述
	    $this->shareLink=Config::get('domain').url('sign/index');// 分享链接
	    $this->shareImgUrl=Config::get('domain').'/static/index/images/jftop.png';     //

	    $this->assign('shareTitle',$this->shareTitle);
	    $this->assign('shareDesc',$this->shareDesc);
	    $this->assign('shareLink',$this->shareLink);
	    $this->assign('shareImgUrl',$this->shareImgUrl);


        return $this->fetch();
    }

	public function getNextMonth($date)
	{
		$timestamp=strtotime($date);
		$arr=getdate($timestamp);
		if($arr['mon'] == 12)
		{
			$year=$arr['year'] +1;
			$month=$arr['mon'] -11;
			$firstday=$year.'-0'.$month;

		}else{
			$firstday=date('Y-m',strtotime(date('Y',$timestamp).'-'.(date('m',$timestamp)+1).'-01'));

		}
		return $firstday;
	}

}