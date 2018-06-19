<?php

namespace app\index\model;


use think\Model;
use think\Db;
class AnswerModel extends Model
{
	protected $name='ask_answer';
	protected $pk='id';
	protected $autoWriteTimestamp=true;

	public static function getAll($aid)
	{
		$where['status']=1;
		$where['del']=0;
		$where['aid']=$aid;

		return self::where($where)->select();
	}

	//关联用户表
	public function userInfo()
	{
		return $this->hasOne('UserInfoModel','id','uid')->field('id,portrait,wechaname,city');
	}





}