<?php


namespace app\index\model;


use think\Model;

class AskImgsModel extends Model
{
	protected $name='ask_imgs';
	protected $pk='id';
	protected $autoWriteTimestamp=true;

	public static function getAll($aid)
	{
		return self::where(['aid'=>$aid])->select();
	}

	public static function getCountImgs($aid)
	{
		return self::where(array('aid'=>$aid))->count();
	}

}