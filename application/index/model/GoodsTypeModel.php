<?php


namespace app\index\model;


use think\Model;

class GoodsTypeModel extends Model
{
	protected $name='goods_type';
	protected $pk='id';

	public static function getAll()
	{
		return self::where(['del'=>0,'status'=>1,'pid'=>0])->select();
	}

}