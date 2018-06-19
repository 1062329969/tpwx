<?php


namespace app\admin\model;


use think\Model;

class NcaseImgsModel extends Model
{
	protected $name='ncase_imgs';
	protected $pk='id';
	protected $autoWriteTimestamp=true;

	public static function getAll($aid)
	{
		return self::where(['aid'=>$aid,'status'=>1,'del'=>0])->paginate(15);
	}

}