<?php

namespace app\admin\model;

use think\Model;

class ContAdModel extends Model
{
	protected $name='cont_ad';
	protected $pk='id';
	protected $autoWriteTimestamp=true;


	public static function getAll($page)
	{
		return self::where('del',0)->order('id desc')->paginate($page);

	}


}