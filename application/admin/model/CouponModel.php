<?php

namespace app\admin\model;


use think\Model;

class CouponModel extends Model
{
    protected $name='coupon';
    protected $autoWriteTimestamp='true';
    protected $pk='id';

    public static function getAll($page=15)
    {
        return self::where(['del'=>0])->order('id desc')->paginate($page);
    }

    //关联分类表
    public function goodType()
    {
        return $this->hasOne('GoodsTypeModel','id','gt_id')->field('typename');
    }

    //返回数量
    public static function contCount()
    {
        return self::where(['del'=>0])->count();
    }

    public function setEndTimeAttr($value)
    {
        return strtotime($value);
    }

    public function getEndTimeAttr($value)
    {
        return date('Y-m-d H:i:s',$value);
    }


	public function setStartTimeAttr($value)
	{
		return strtotime($value);
	}

	public function getStartTimeAttr($value)
	{
		return date('Y-m-d H:i:s',$value);
	}

}