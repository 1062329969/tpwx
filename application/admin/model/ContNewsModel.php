<?php

namespace app\admin\model;
use think\Model;

class ContNewsModel extends Model
{
	protected $pk = 'id';
	protected $name='cont_news';
	protected $autoWriteTimestamp=true;


	public static function getAll($page=15)
	{
		return self::where(['del'=>0])->order('id desc')->paginate($page);
	}

}