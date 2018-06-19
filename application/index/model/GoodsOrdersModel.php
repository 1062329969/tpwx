<?php
/**
 * Created by aibx.net.
 * User: wafu7969
 * Date: 2017/7/6
 * Time: 23:28
 */

namespace app\index\model;


use think\Model;

class GoodsOrdersModel extends Model
{
    protected $name='goods_orders';
    protected $pk='id';
    protected $autoWriteTimestamp=true;

    public function myOrder($uid,$tid=0)
    {
        $where['user_id']=$uid;

        //待付款
        if($tid==1)
        {
            $where['status']=0;
        }
        //       //待发货
        // if($tid==2)
        // {
        // 	$where['status']=1;
        // }
        //待收货
        if($tid==3 || $tid==2)
        {
            $where['status']=array('in',array(1,2));
        }
        //退换货
        if($tid==4)
        {
            $where['status']=4;
        }
        //已完成
        if($tid==5)
        {
            $where['status']=3;
        }
        $where['cancel']=0;
        return $this->where($where)->order('create_time desc')->limit(0,30)->select();
        // echo $this->getLastSql();die;
    }

    public static function myOrderCount($uid,$tid=0)
    {
        $where['user_id']=$uid;

        //待付款
        if($tid==1)
        {
            $where['status']=0;
        }
        // //待发货
        // if($tid==2)
        // {
        // 	$where['status']=1;
        // }
        //待收货
        if($tid==3 || $tid==2)
        {
            $where['status']=array('in',array(1,2));
        }
        //退换货
        if($tid==4)
        {
            $where['status']=4;
        }
        //已完成
        if($tid==5)
        {
            $where['status']=3;
        }
        $where['cancel']=0;

        return self::where($where)->count();
    }

    public function getOne($id)
    {
        return $this->where(['id'=>$id])->find();
    }

    //关联订单商品记录表
    public function orderGoods()
    {
        return $this->hasMany('GoodsOrdersGoodsModel','oid');
    }




}