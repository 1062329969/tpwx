<?php


namespace app\branch\model;


use think\Model;

class GoodsModel extends Model
{
    protected $name='goods';
	protected $pk='id';
	protected $autoWriteTimestamp=true;

	public static function getAll($page=15)
	{
		return self::where('del',0)->order('id desc')->paginate($page);
	}

	public function getDocIdsAttr($value)
	{
		if(!empty($value))
		{
			return explode(',',$value);
		}
		else
		{
			return array();
		}
	}
	
	//获得订单中购买的商品信息
	public static function getGoods($ids)
	{
		return self::where(['id'=>['in',$ids]])->select();
	}
}