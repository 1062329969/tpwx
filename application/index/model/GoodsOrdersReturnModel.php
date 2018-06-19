<?php

namespace app\index\model;


use think\Db;
use think\Model;

class GoodsOrdersReturnModel extends Model
{
    protected $name='goods_orders_return';
    protected $pk='return_id';

    public function getall($uid=''){
        if($uid){
            return Db::name($this->name)->where(['return_uid'=>$uid])->order('return_id desc')->select();
        }else{
            return Db::name($this->name.' r')
                ->join('user_info u','r.return_uid = u.id')
                ->order('r.return_id desc')->paginate(10);
        }
    }
}