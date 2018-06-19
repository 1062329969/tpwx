<?php


namespace app\admin\model;


use think\Model;

class InvestigationResultModel extends Model
{
	protected $name='investigation_result';
	protected $pk='id';
	protected $autoWriteTimestamp=false;

	public static function getAll($page=15,$iid)
	{
		return self::where(['del'=>0,'iid'=>$iid])->order('id desc')->paginate($page);
	}

}