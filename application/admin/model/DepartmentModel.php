<?php


namespace app\admin\model;


use think\Db;
use think\Model;
use think\Session;
class DepartmentModel extends Model
{
	protected $name='department';
	protected $pk='d_id';

    public static function getAll(){
        $adminid = Session::get('adminid');
        if($adminid==1) {
            $list = Db::name('department')->where(['d_state'=>1,'d_pid'=>0])->select();
        }else{
            $list = Db::name('department')->where(['d_state'=>1,'d_pid'=>0,'d_uid'=>$adminid])->select();
        }
        foreach ($list as $k => $v){
            $list[$k]['clist'] = Db::name('department')->where(['d_state'=>1,'d_pid'=>$v['d_id']])->select();
        }
        return $list;
    }

    public static function getTopAll($d_id=0){
        $list = Db::name('department')->where(['d_state'=>1,'d_pid'=>0])->where('d_id != '.$d_id)->select();
        return $list;
    }

    public static function getc($d_id){
        $list = Db::name('department')->where(['d_pid'=>$d_id])->select();
        return $list;
    }

}