<?php

namespace app\admin\model;


use think\Model;

class InvestigationModel extends Model
{

	protected $name='investigation';
	protected $pk='id';
	protected $autoWriteTimestamp=true;

	public static function getAll($page=15)
	{
		return self::where(['del'=>0])->order('sort desc,id desc')->paginate($page);
	}

}