<?php

namespace app\index\model;


use think\Model;

class ContTextModel extends Model
{
	protected $name='cont_text';
	protected $pk='id';

	public static function  getOne($tid)
	{
		return self::where(['del'=>0,'status'=>1,'tid'=>$tid])->order('sort desc,id desc')->find();
	}

	public static function getAll($tid)
	{
		return self::where(['del'=>0,'status'=>1,'tid'=>$tid])->order('sort desc,id desc')->select();
	}

	//关联分类表
	public function contType()
	{
		return $this->hasOne('ContTypeModel','id','tid')->field('typename');
	}
}