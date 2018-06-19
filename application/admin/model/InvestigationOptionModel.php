<?php


namespace app\admin\model;


use think\Model;

class InvestigationOptionModel extends Model
{
	protected $name='investigation_option';
	protected $pk='id';
	protected $autoWriteTimestamp=true;

	public static function getAll($page=15,$iid)
	{
		return self::where(['del'=>0,'iid'=>$iid])->order('sort desc,id desc')->paginate($page);
	}

	public static function getAll2($iid)
	{
		return self::where(['del'=>0,'iid'=>$iid])->order('sort desc,id desc')->select();
	}

	public function getTidAttr($value)
	{
		if($value==0)
		{
			return '单选';
		}
		else
		{
			return '多选';
		}
	}


}