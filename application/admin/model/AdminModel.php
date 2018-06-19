<?php


namespace app\admin\model;


use think\Model;
use think\Session;
class AdminModel extends Model
{
	protected $name='admin';
	protected $pk='id';

	public static function getAll()
	{
        $adminid = Session::get('adminid');
        if($adminid==1) {
            return self::where('status !=2')->select();
        }else{
            return self::where('status !=2')->where(['uid'=>$adminid])->select();
        }
	}

	//管理角色表
	public function role()
	{
		return $this->hasOne('RoleModel','id','rid')->field('rname');
	}

}