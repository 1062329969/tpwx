<?php

namespace app\index\model;


use think\Model;

class CouponModel extends Model
{
    protected $name='coupon';
    protected $pk='id';

    public static function getAll()
    {
        $where['status']=1;
        $where['del']=0;
        $where['end_time']=['gt',time()];
        $where['start_time']=['lt',time()];
        return self::where($where)->order('sort desc,cprice desc')->select();
    }

    public static function getAllCoupon(){
        $where['del']=0;
        $where['end_time']=['gt',time()];
        $where['start_time']=['lt',time()];
        return self::where($where)->order('sort desc,cprice desc')->select();
    }

    public static function getOne($id)
    {
        $where['status']=1;
        $where['del']=0;
        $where['end_time']=['gt',time()];
        $where['id']=$id;
        return self::where($where)->order('sort desc,cprice desc')->find();
    }

    public static function getCount()
    {
        $where['status']=1;
        $where['del']=0;
        $where['end_time']=['gt',time()];
        $where['start_time']=['lt',time()];
        return self::where($where)->order('sort desc,cprice desc')->count();
    }

}