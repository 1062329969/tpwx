<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 商品订单管理控制器
 */
namespace app\admin\controller;

use ApiOauth\ApiOauth;
use app\admin\model\GoodsOrdersGoodsModel;
use app\admin\model\GoodsOrdersModel;
use app\admin\model\UserInfoModel;
use app\index\event\TemplateMessage;
use kdniao\Kdniao;

class GoodsOrders extends Admin
{

	public function _initialize()
	{
		parent::_initialize();
	}
	public function index()
	{
		$today=GoodsOrdersModel::getToday();
		$Yesterday=GoodsOrdersModel::getYesterday();
		$month=GoodsOrdersModel::getMonth();
		$total=GoodsOrdersModel::getTotal();

		$this->assign('today', $today);
		$this->assign('Yesterday', $Yesterday);
		$this->assign('month', $month);
		$this->assign('total', $total);

		$goodsOrders=GoodsOrdersModel::getAll();

		$this->assign('goodsOrders',$goodsOrders);
		return $this->fetch();
	}

	//物流信息查询
	public function express()
	{
		$oid=input('param.oid');
		$goodsOrders=GoodsOrdersModel::where(['id'=>$oid])->find();

		$kdniao=new Kdniao(config('EBusinessID'),config('AppKey'));
		
		$return=$kdniao->orderTracesSubByJson('text'.time(),$goodsOrders['shipper_code'],$goodsOrders['express_code']);
		$info=json_decode($return);

		$reStr='快件揽收中...';
		if($info->Success==true)
		{
			if(empty($info->Traces))
			{
				return $reStr=$info->Reason;
			}
			$reStr=array_reverse(json_decode( json_encode($info->Traces),true));

		}
		else
		{
			$reStr='快件揽收中...';
		}



		$this->assign('reStr',$reStr);

		return $this->fetch();
	}


	public function alter()
	{
		$request=request();
		if($request->method()=='POST')
		{
			$data=input('post.');

			$goodsOrders=new GoodsOrdersModel();

			if(!empty($data['express_code']))
			{
				$data['status']=2;
				$orders=$goodsOrders->where(['id'=>$data['id']])->find();
				$userInfo=UserInfoModel::where(['id'=>$orders['user_id']])->find();
				$kdname='';
				if($data['shipper_code']=='SF') $kdname='顺丰快递';
				if($data['shipper_code']=='STO') $kdname='申通快递';
				if($data['shipper_code']=='YTO') $kdname='圆通速递';
				if($data['shipper_code']=='YD') $kdname='韵达快递';
				if($data['shipper_code']=='ZJS') $kdname='宅急送';
				if($data['shipper_code']=='YZPY') $kdname='邮政平邮/小包';
				//发送模板消息
				$templateMessage=new TemplateMessage();
				$sData["wecha_id"]=$userInfo['wecha_id'];
				$sData["tempid"]='NfpcYLnb6Q8LMqDv_HQy2TJdN7RnbapzlLEMuGanFjE';
				$sData['first']='您好，您购买的商品已发货';
				$sData['keyword1']=$orders['order_id'];
				$sData['keyword2']=$kdname;
				$sData['keyword3']=$data['express_code'];
				$sData['keyword4']=date('Y-m-d H:i:s',time());
				$sData['remark']='可点击查看订单详情及物流信息，感谢您的惠顾！';
				$sData["href"]= config('domain').'/my/mygoods.html';
				$sData["topcolor"]='';
				$apiOauth=new ApiOauth();
				$access_token=$apiOauth->update_authorizer_access_token(['appid'=>config('Appid'),'appsecret'=>config('Appsecret')]);
				$templateMessage->sendTempMsg(4,$sData,$access_token);
			}
			else
			{
				$data['status']=1;
			}



			if($goodsOrders->allowField(true)->save($data,['id'=>$data['id']]))
			{
				$this->success('修改成功！','index');
			}
			else
			{
				$this->error('修改失败！','index');
			}
		}
		else
		{
			$id=input('param.id');
			$orders=GoodsOrdersModel::get($id);
			$this->assign('orders',$orders);

			//获得购买的商品及数量
			$goodsOrdersGoods=GoodsOrdersGoodsModel::where('oid',$id)->select();
			$this->assign('goodsOrdersGoods',$goodsOrdersGoods);

			return $this->fetch();
		}
	}

}