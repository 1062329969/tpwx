<?php

namespace app\index\model;


use think\Model;

class IntegralModel extends Model
{
	protected $name='integral_reg';
	protected $pk='id';
	protected $autoWriteTimestamp=true;

	public static function getUid($uid,$page=10,$offsize=0)
	{
         return self::where(['uid'=>$uid])->order('id desc')->limit($offsize,$page)->select();
	}

}