<?php


namespace app\index\model;


use think\Model;

class ForumImgsModel extends Model
{
	protected $name='forum_imgs';
	protected $pk='id';
	protected $autoWriteTimestamp=true;

	public static function getAll($fid)
	{
		return self::where(['fid'=>$fid])->select();
	}

	public static function getCountImgs($fid)
	{
		return self::where(array('fid'=>$fid))->count();
	}

}