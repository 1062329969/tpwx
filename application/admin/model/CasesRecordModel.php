<?php


namespace app\admin\model;


use think\Model;

class CasesRecordModel extends Model
{
	protected $name='cases_record';
	protected $pk='id';
	protected $autoWriteTimestamp=true;

	public static function getAll($cid,$page=15)
	{
		return self::where(['cid'=>$cid,'status'=>1,'del'=>0])->order('sort desc, create_time desc')->paginate($page);
	}

}