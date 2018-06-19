<?php


namespace app\admin\model;


use think\Model;

class CasesRecordImgsModel extends Model
{
	protected $name='cases_record_imgs';
	protected $pk='id';
	protected $autoWriteTimestamp=true;

	public static function getAll($cid)
	{
		return self::where(['cid'=>$cid])->select();
	}

}