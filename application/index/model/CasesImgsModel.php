<?php

namespace app\index\model;


use think\Model;

class CasesImgsModel extends Model
{
	protected $name='cases_imgs';
	protected $pk='id';

	public static function getAll($cid)
	{
		return self::where(['cid'=>$cid])->order('id asc')->select();
	}

}