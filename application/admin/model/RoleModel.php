<?php


namespace app\admin\model;


use think\Model;
use think\Session;

class RoleModel extends Model
{
	protected $name='role';
	protected $pk='id';
	protected $autoWriteTimestamp=true;

	public static function getAll(){
	    $adminid = Session::get('adminid');
        if($adminid==1){
            return self::where(['del'=>0])->order('id asc')->select();
        }else{
            return self::where(['del'=>0,'uid'=>$adminid])->order('id asc')->select();
        }
	}

}