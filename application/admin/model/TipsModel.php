<?php

namespace app\index\model;


use think\Db;
use think\Model;

class TipsModel extends Model
{
	protected $name='tips';
	protected $pk='t_id';

	public static function gettipsbyid($tid='')
	{
        return Db::name('tips')->find($tid);
	}
}