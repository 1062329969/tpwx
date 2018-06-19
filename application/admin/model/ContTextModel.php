<?php

namespace app\admin\model;
use think\Model;

class ContTextModel extends Model
{
	protected $pk = 'id';
	protected $name='cont_text';
	protected $autoWriteTimestamp=true;


	public static function getAll($page=15)
	{
		return self::where(['del'=>0])->order('id desc')->paginate($page);
	}

	//关联分类表
	public function contType()
	{
		return $this->hasOne('ContTypeModel','id','tid')->field('typename');
	}

	//返回文章数量
	public static function contCount()
	{
		return self::where(['del'=>0])->count();
	}

}