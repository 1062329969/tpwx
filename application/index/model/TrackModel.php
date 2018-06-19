<?php

namespace app\index\model;


use think\Model;

class TrackModel extends Model
{
	protected $name='track';
	protected $pk='id';

	public static function getAll($visit_id)
	{
		return self::where(['visit_id'=>$visit_id])->order('id desc')->select();
	}

}