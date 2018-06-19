<?php
/**
 * Created by aibx.net.
 * User: wafu7969
 * Date: 2017/7/6
 * Time: 23:40
 */

namespace app\index\model;

use think\Db;
use think\Model;

class GoodsOrdersGoodsModel extends Model
{
    protected $name='goods_orders_goods';
    protected $pk='id';
    protected $autoWriteTimestamp=true;

    //关联商品表
    public function goods()
    {
        return $this->hasOne('GoodsModel','id','gid');
    }

    public function getgogbyid($id){
        return Db::name('goods_orders_goods gog')
            ->field('gog.*,g.pic,g.gdname')
            ->where(['gog.id'=>$id])
            ->join('goods g','gog.gid = g.id')
            ->find();
    }
}