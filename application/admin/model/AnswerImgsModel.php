<?php

namespace app\admin\model;


use think\Model;

class AnswerImgsModel extends Model
{
	protected $name='ask_answer_imgs';
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