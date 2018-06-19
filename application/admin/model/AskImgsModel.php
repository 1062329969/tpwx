<?php


namespace app\admin\model;


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

}