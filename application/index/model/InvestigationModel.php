<?php

namespace app\index\model;


use think\Model;

class InvestigationModel extends Model
{

	protected $name='investigation';
	protected $pk='id';
	protected $autoWriteTimestamp=true;

	public static function getOne()
	{
		return self::where(['del'=>0,'status'=>1])->order('sort desc,id desc')->find();
	}

}