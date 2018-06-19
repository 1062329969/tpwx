<?php
/**
 * Created by aibx.net.
 * User: wafu7969
 * Date: 2017/7/19
 * Time: 18:25
 */

namespace app\index\model;


use think\Model;

class CasesRecordModel extends Model
{
	protected $name='cases_record';
	protected $pk='id';

	public static function getOne($cid)
	{
		return self::where(['status'=>1,'del'=>0,'cid'=>$cid])->order('sort desc,create_time desc')->find();
	}

	public static function getOneId($id)
	{
		return self::where(['status'=>1,'del'=>0,'id'=>$id])->order('id desc')->find();
	}

	public static function getAll($cid,$order=0)
	{
		if($order==0)
		{
			return self::where(['status'=>1,'del'=>0,'cid'=>$cid])->order('sort desc,create_time desc')->select();
		}
		else
		{
			return self::where(['status'=>1,'del'=>0,'cid'=>$cid])->order('sort asc,create_time asc')->select();
		}

	}

}