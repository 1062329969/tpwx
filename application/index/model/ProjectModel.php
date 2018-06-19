<?php


namespace app\index\model;


use think\Model;

class ProjectModel extends Model
{
	protected $name='project';
	protected $pk='id';

	public static function getAll()
	{
		return self::where(['del'=>0,'status'=>1])->order('sort desc,id asc')->select();
	}

	public static function getOne($id)
	{
		return self::where(['del'=>0,'status'=>1,'id'=>$id])->find();
	}

}