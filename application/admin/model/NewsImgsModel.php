<?php


namespace app\admin\model;


use think\Model;

class NewsImgsModel extends Model
{
	protected $name='news_imgs';
	protected $pk='id';
	protected $autoWriteTimestamp=true;

	public static function getAll($aid)
	{
		return self::where(['aid'=>$aid])->select();
	}

}