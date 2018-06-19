<?php


namespace app\branch\model;


use think\Model;

class GoodsOrdersGoodsModel extends Model
{
	protected $name='goods_orders_goods';
	protected $pk='id';
	protected $autoWriteTimestamp=true;

	//关联商品表
	public function goods()
	{
		return $this->hasOne('GoodsModel','id','gid');
	}
}