<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 优惠券
 */
namespace app\index\controller;


use app\index\model\CouponLogModel;
use app\index\model\CouponModel;
use app\index\model\MsgModel;

class Coupon extends Wap
{
    protected function _initialize()
    {
        //获得授权
        $this->getUserInfo();
        $this->assign('fansInfo',$this->fansInfo);
    }

    //优惠券首页
    public function index()
    {
        //读取优惠券
        $coupon=CouponModel::getAllCoupon();
        //读取优惠券的领取使用情况
        foreach ($coupon as $key=>$value)
        {
            $couponLog=CouponLogModel::getOneCoupon($this->fansInfo['id'],$value['id']);
            if(empty($couponLog))
            {
                $coupon[$key]['state']=0;
            }else{
                $coupon[$key]['state']=$couponLog['status'];
            }
        }
        $this->assign('coupon',$coupon);

        return $this->fetch();
    }

    //领取优惠券
    public function getcoupon()
    {
        $id=input('post.id');
        $couponLog=new CouponLogModel();

        if($couponLog->save(['cid'=>$id,'uid'=>$this->fansInfo['id']]))
        {
            //发送站内消息
            MsgModel::insert(['uid'=>$this->fansInfo['id'],'type'=>1,'title'=>"领取优惠券通知",'msg'=>'成功领取一张优惠券，猛戳查看~~','url'=>url('coupon/index')]);
            return ['code'=>1];
        }
        else
        {
            return ['code'=>1,'msg'=>'领取失败'];
        }

    }

}