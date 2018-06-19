<?php


namespace app\admin\model;


use think\Model;

class ProjectCasesImgsModel extends Model
{
	protected $name='project_cases_imgs';
	protected $pk='id';
	protected $autoWriteTimestamp=true;

	public static function getAll($pid)
	{
		return self::where(['pid'=>$pid])->select();
	}

}