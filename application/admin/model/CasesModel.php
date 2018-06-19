<?php

namespace app\admin\model;


use think\Model;

class CasesModel extends Model
{
	protected $pk = 'id';
	protected $name='cases';
	protected $autoWriteTimestamp=true;


	public static function getAll($page=15)
	{
		return self::where(['del'=>0])->order('sort desc,create_time desc')->paginate($page);
	}

    public static function getAll_one($id)
    {
        return self::where(['id'=>$id])->find();
    }




	//关联用户表
	public function userInfo()
	{
		return $this->hasOne('UserInfoModel','id','uid')->field('id,portrait,wechaname,city');
	}

	//管理项目表
	public function project()
	{
		return $this->hasOne('ProjectModel','id','pid')->field('proname');
	}

}