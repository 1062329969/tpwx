<?php

namespace app\index\model;


use think\Model;

class ShipaddressModel extends Model
{
	protected $name='shipaddress';
	protected $pk='sd_id';

	public static function getAll()
	{
        $where['sd_state']=1;
        return self::where($where)->order('sd_default desc,sd_id desc')->select();
	}

}