<?php


namespace app\admin\model;


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

}