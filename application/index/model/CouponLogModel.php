<?php

namespace app\index\model;


use think\Model;

class CouponLogModel extends Model
{
    protected $name='coupon_log';
    protected $pk='id';
    protected $autoWriteTimestamp=true;

    public static function getOne($uid,$cid)
    {
        return self::where(['uid'=>$uid,'cid'=>$cid,'status'=>1])->find();
    }

    public static function getOneCoupon($uid,$cid)
    {
        return self::where(['uid'=>$uid,'cid'=>$cid])->find();
    }

    //获得某用户的优惠券
    public static function getUser($uid)
    {
        return self::where(['uid'=>$uid,'status'=>1])->select();
    }


}