<?php

namespace app\admin\model;


use think\Model;
use think\Request;

class CommentModel extends Model
{
    protected $name='comment';
	protected $pk='id';
	protected $autoWriteTimestamp=true;

	public static function getAll($page=15)
	{

		return self::where('del',0)->order('id desc')->paginate($page);

	}



	//关联用户信息
	public function userInfo()
	{
		return $this->hasOne('UserInfoModel','id','uid')->field('id,wechaname,portrait');
	}

}