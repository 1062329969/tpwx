<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 微信支付的回调
 */

namespace app\index\controller;
use app\index\event\Integral;
use app\index\event\TemplateMessage;
use app\index\model\GoodsModel;
use app\index\model\GoodsOrdersGoodsModel;
use app\index\model\IntegralModel;
use app\index\model\UserInfoModel;
use think\Controller;
use think\Db;
use think\Config;
use ApiOauth\ApiOauth;
use think\Log;

class Notify extends Controller
{

	//商品支付的回调函数
	public function goods()
	{

		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		$postObj = simplexml_load_string($postStr,'SimpleXMLElement',LIBXML_NOCDATA);

		$order_id = $postObj->out_trade_no."";   //商户系统的订单号，与请求一致。
		$transaction_id = $postObj->transaction_id."";  //微信支付订单号
		//$sign = $postObj->sign;  //签名
		$openid = $postObj->openid."";  //提交的用户
		$total_fee = $postObj->total_fee."";  //微信返回的订单金额


		$goods_orders=Db::name('goods_orders')->where(['order_id'=>$order_id,'status'=>0])->find();
		if(!empty($goods_orders))
		{
			if(Db::name('goods_orders')->where('id',$goods_orders['id'])->update(array('status'=>1,'transaction_id'=>$transaction_id,'total_fee'=>$total_fee,'update_time'=>time())))
			{
			    //扣除优惠券
                Db::name('coupon_log')->where('id',$goods_orders['coupon_id'])->update(array('status'=>2));
				//如果是积分抵现的减对应的积分
				if($goods_orders['integral_money']>0)
				{
					//计算减少的积分数
					$integralNums=floor($goods_orders['integral_money']*config('getIntegral'));   //啥去取整
					$event = new Integral();
					if($integralNums>0)
					{
						$event->reduce($goods_orders['user_id'],$integralNums,'购买抵现');
					}

				}

				//查找该订单的商品
				$GoodsOrdersGoods=GoodsOrdersGoodsModel::field('gid,nums')->where(['oid'=>$goods_orders['id']])->select();
				$goodsId=array();
				$gtitle=array();
				foreach ($GoodsOrdersGoods as $key=>$v)
				{
					$goods=GoodsModel::getOne($v['gid']);
					$goodsId[$key]['id']=$goods['id'];
					$goodsId[$key]['nums']=$v['nums'];
					$gtitle[]=$goods['gdname'];
				}

				//减库存
				//$goodsId=explode(',',$goods_orders['goods_ids']);
				foreach($goodsId as $key=>$v)
				{
					//减库存
					GoodsModel::where(['id'=>$v['id']])->setDec('number',$v['nums']);
					//增加销量
					GoodsModel::where(['id'=>$v['id']])->setInc('xl',$v['nums']);
				}

				//发送模板消息
				$templateMessage=new TemplateMessage();
				$sData["wecha_id"]=$openid;
				$sData["tempid"]='LR4Z3ZReURpm3e2vEFS8wJKkhLlvIfH0ZFJuY-D_ytA';
				$sData['first']='购买成功';
				$sData['keyword1']=implode(',',$gtitle);
				$sData['keyword2']=date('Y-m-d',time());
				$sData['remark']='非常感谢您的购买，欢迎下次惠顾。';
				$sData["href"]= config('domain').url('my/mygoods');
				$sData["topcolor"]='';
				$apiOauth=new ApiOauth();
				$access_token=$apiOauth->update_authorizer_access_token(['appid'=>config('Appid'),'appsecret'=>config('Appsecret')]);
				$templateMessage->sendTempMsg(2,$sData,$access_token);
			}


		}


		echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
	}


	//充值的回调
	public function recharge()
	{
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		$postObj = simplexml_load_string($postStr,'SimpleXMLElement',LIBXML_NOCDATA);

		$order_id = $postObj->out_trade_no."";   //商户系统的订单号，与请求一致。
		$transaction_id = $postObj->transaction_id."";  //微信支付订单号
		//$openid = $postObj->openid."";  //提交的用户
		$total_fee = $postObj->total_fee."";  //微信返回的订单金额

		$recharge_reg=Db::name('drug_reg')->where(['order_id'=>$order_id,'status'=>0])->find();
		if(!empty($recharge_reg))
		{
			Db::name('drug_reg')->where('id',$recharge_reg['id'])->update(array('status'=>1,'transaction_id'=>$transaction_id,'total_fee'=>$total_fee,'update_time'=>time()));
			$userInfoModel=new UserInfoModel;
			$userInfoModel->where('id',$recharge_reg['uid'])->setInc('balance',$recharge_reg['actual_amount']);
		}

		echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';



	}

}
?>