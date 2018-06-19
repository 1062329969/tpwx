<?php
//商品展示图

namespace app\index\model;


use think\Model;

class GoodsImgsModel extends Model
{
	protected $name='goods_imgs';
	protected $pk='id';
	protected $autoWriteTimestamp=true;

	public static function getAll($aid)
	{
		return self::where(['aid'=>$aid])->select();
	}

}