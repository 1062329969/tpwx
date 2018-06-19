<?php

namespace app\admin\model;


use think\Model;

class ProjectModel extends Model
{
	protected $pk = 'id';
	protected $name='project';
	protected $autoWriteTimestamp=true;

	public static function getAll($page=15)
	{
		return self::where(['del'=>0])->order('sort desc,id asc')->paginate($page);
	}

	public function getDocIdsAttr($value)
	{
		if(!empty($value))
		{
			return explode(',',$value);
		}
		else
		{
			return array();
		}
	}

}