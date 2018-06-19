<?php

namespace app\index\model;


use think\Model;

class InvestigationOptionModel extends Model
{
	protected $name='investigation_option';
	protected $pk='id';
	protected $autoWriteTimestamp=true;

	public static function getAll($iid)
	{
		return self::where(['del'=>0,'iid'=>$iid,'status'=>1])->order('sort desc,id desc')->select();
	}


}