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
        $where['res_addtime']=['gt',$timestamp0];
        $where['res_addtime']=['lt',$timestamp24];
        return self::where($where)->order('res_id desc')->select();
    }
}