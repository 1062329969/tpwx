<?php

namespace app\index\model;

class ContTypeModel extends \think\Model
{
	protected $name='cont_type';
	protected $pk='id';

	public static function getAll()
	{
		return self::where(['status'=>1,'del'=>0])->order('sort desc,id asc')->select(); //->cache(true,7200)
	}

}