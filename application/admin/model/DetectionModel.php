<?php

namespace app\index\model;


use think\Db;
use think\Model;

class DetectionModel extends Model
{
	protected $name='detection';
	protected $pk='ud_id';

	public static function getLastud($uid='')
	{
        return Db::name('detection')->where(['ud_uid'=>$uid])->order('ud_id desc')->find();
	}
}