<?php

namespace app\index\model;


use think\Model;

class CasesRecordImgsModel extends Model
{
	protected $name='cases_record_imgs';
	protected $pk='id';

	public static function getAll($id)
	{
		return self::where(['cid'=>$id])->order('id asc')->select();
	}

}