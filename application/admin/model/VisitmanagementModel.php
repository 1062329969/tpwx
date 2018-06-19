<?php


namespace app\admin\model;


use think\Model;
use think\Db;
class VisitmanagementModel extends Model
{
    protected $name='visitmanagement';
    protected $pk='vm_id';



    public static function getexport($name,$phone,$time_start,$time_over,$perform_start,$perform_over,$hospital,$project){
        $where = array();
        //输入姓名
        if(!empty($name) ){
            $where['vm_name'] = ['like','%'.$name.'%'];
        }
        //手机号码
        if(!empty($phone)){
            $where['vm_phone'] = ['like','%'.$phone.'%'];
        }
        //到诊时间
        if(!empty($time_start) && !empty($time_over)){
            $time_over = $time_over + 86400;
            $where['vm_visittime'] = array('between',$time_start.','.$time_over);
        }
        //手术日期
        if(!empty($perform_start) && !empty($perform_over)){
            $perform_over = $perform_over + 86400;
            $where['vm_operationtime'] = array('between',$perform_start.','.$perform_over);
        }
        return Db::name('visitmanagement')->where($where)->order('vm_id desc')->select();
    }


}
