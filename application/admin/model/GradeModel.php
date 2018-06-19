<?php

namespace app\admin\model;


use think\Model;

class GradeModel extends Model
{
	protected $pk = 'id';
	protected $name='grade';
	protected $autoWriteTimestamp=true;

	public static function getAll($page=15)
	{
		return self::where(['del'=>0])->order('sort desc,id asc')->paginate($page);
	}

}