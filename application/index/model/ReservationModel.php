<?php

namespace app\index\model;


use think\Db;
use think\Model;

class ReservationModel extends Model
{
    protected $name='reservation';
    protected $pk='res_id';

    public function getusertoday($uid)
    {
        $dateStr = date('Y-m-d', time());
        $timestamp0 = strtotime($dateStr);
        $timestamp24 = strtotime($dateStr) + 86400;
        $where['res_addtime']=['gt'=>$timestamp0,'lt'=>$timestamp24];
        $val = self::where('res_addtime>'.$timestamp0.' and res_addtime<'.$timestamp24)->where(['res_uid'=>$uid])->order('res_id desc')->find();
        if($val['res_state']==3 || !$val){
            return false;
        }else{
            return true;
        }
    }
}