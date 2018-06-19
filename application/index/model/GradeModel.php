<?php

namespace app\index\model;


use think\Model;

class GradeModel extends Model
{
	protected $pk = 'id';
	protected $name='grade';
	protected $autoWriteTimestamp=true;

	public static function getAll()
	{
		return self::where(['del'=>0])->order('sort desc,id asc')->select();
	}

}