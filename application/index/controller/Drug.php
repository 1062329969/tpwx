<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 药品券
 */
namespace app\index\controller;


use app\index\model\DrugModel;
use ApiOauth\ApiOauth;
use app\index\event\Wxpay;
use app\index\model\DrugRegModel;
use think\Config;

class Drug extends Wap
{
	protected function _initialize()
	{
		//获得授权
		$this->getUserInfo();
		$this->assign('fansInfo',$this->fansInfo);
	}


	//药品券首页
	public function index()
	{


		$request=request();
		if($request->method()=='POST')
		{
			$data=input('post.');

			//读取充值的数据
			$recharge=DrugModel::getOne($data['id']);

			//生成微信支付的订单号

			$wxPay=new Wxpay();
			$order_id=$wxPay->orderId('xq');

			$rechargeReg=new DrugRegModel();
			$one=$rechargeReg->where(['rid'=>$recharge['id'],'uid'=>$this->fansInfo['id'],'status'=>0])->find();
			if(!empty($one))
			{
				//修改
				$rechargeReg->save(['order_id'=>$order_id,'rid'=>$recharge['id'],'uid'=>$this->fansInfo['id'],'amount'=>$recharge['saleprice'],'actual_amount'=>$recharge['cprice']],['id'=>$one['id']]);
			}
			else
			{
				//新增
				$rechargeReg->save(['order_id'=>$order_id,'rid'=>$recharge['id'],'uid'=>$this->fansInfo['id'],'amount'=>$recharge['saleprice'],'actual_amount'=>$recharge['cprice']]);
			}

			//生成微信支付的参数
			$body='购买药品券';
			$money=$recharge['saleprice'];
			$notify_url= Config::get('domain')."/index.php/notify/recharge.html";
			$return=$wxPay->pay(Config::get('Appid'),Config::get('apikey'),$this->fansInfo['wecha_id'],Config::get('mchid'),$body,$order_id,$money,$notify_url);
			if(!empty($return))
			{
				return array('code'=>1,'data'=>$return,'order_id'=>$order_id);
			}

		}
		else
		{
			//微信jdk 验证
			$apiOauth=new apiOauth();
			$ticket = $apiOauth->getJsApiTicket(Config::get('Appid'),Config::get('Appsecret'));
			$this->timeStamp = time();
			$this->nonceStr  = rand(100000,999999);
			$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			//获得jsdk签名
			$this->signature = $apiOauth->getSignature($this->nonceStr,$ticket,$this->timeStamp,$url);
			$this->assign('timeStamp',$this->timeStamp);
			$this->assign('nonceStr',$this->nonceStr);
			$this->assign('signature',$this->signature);

			//读取药品劵
			$drug=DrugModel::getAll();
			$this->assign('coupon',$drug);
			return $this->fetch();
		}
	}

}