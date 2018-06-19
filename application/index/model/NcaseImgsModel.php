<?php


namespace app\index\model;


use think\Model;

class NcaseImgsModel extends Model
{
	protected $name='ncase_imgs';
	protected $pk='id';

	public static function getCountImgs($aid)
	{
		return self::where(array('aid'=>$aid))->count();
	}

	public static function getAll($aid)
	{
		return self::where(['aid'=>$aid,'del'=>0,'status'=>1])->order('sort desc,id desc')->select();
	}
}