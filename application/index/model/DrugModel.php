<?php

namespace app\index\model;

use think\Model;

class DrugModel extends Model
{
	protected $name='drug';
	protected $pk='id';

	public static function getAll()
	{
		$where['status']=1;
		$where['del']=0;
		return self::where($where)->order('sort desc,cprice desc')->select();
	}

	public static function getOne($id)
	{
		$where['status']=1;
		$where['del']=0;
		$where['id']=$id;
		return self::where($where)->order('sort desc,cprice desc')->find();
	}

}