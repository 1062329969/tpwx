<?php

namespace app\index\model;


use think\Model;

class GoodsModel extends Model
{
    protected $name='goods';
    protected $pk='id';

    //读取首页推荐商品
    public static function getTj()
    {
        return self::where(['del'=>0,'status'=>1,'tj'=>1])->order('id desc')->limit('0,4')->select();
    }

    //读取首页中间推荐商品
    public static function getsTj()
    {
        return self::where(['del'=>0,'status'=>1,'stj'=>1])->order('id desc')->find();
    }

    //读取详细页推荐商品 当前分类下的推荐商品
    public static function getZq($tid)
    {
        return self::where(['del'=>0,'status'=>1,'tid'=>$tid,'tj'=>1])->order('id desc')->select();
    }

    public static function getJd()
    {
        return self::where(['del'=>0,'status'=>1,'jd'=>1])->order('id desc')->select();
    }


    public static function search($search='',$page=10,$offsize=0)
    {
        $where['del']=0;
        $where['status']=1;
        if(!empty($search))
        {
            $where['gdname']=['like','%'.$search.'%'];
        }

        return self::where($where)->order('id desc')->limit($offsize,$page)->select();
    }

    //读取某个分类下的所有商品
    public static function getTypeGoods($tid,$page=15,$jg_order=0,$xl_order=0,$pj_order=0,$zh_order=0)
    {
        $order='id desc';
        if($jg_order==1)
        {
            $order='price desc';
        }
        elseif($jg_order==2)
        {
            $order='price asc';
        }

        if($xl_order==1)
        {
            $order='xl desc';
        }
        elseif($xl_order==2)
        {
            $order='xl asc';
        }


        if($pj_order==1)
        {
            $order='pj desc';
        }
        elseif($pj_order==2)
        {
            $order='pj asc';
        }

        if($zh_order==1)
        {
            $order='id desc';
        }
        elseif($zh_order==2)
        {
            $order='id asc';
        }
        $where = array('del'=>0,'status'=>1)+$tid;
        return self::where($where)->order($order)->paginate($page);
    }

    public function getDocIdsAttr($value)
    {
        if(!empty($value))
        {
            return explode(',',$value);
        }
        else
        {
            return array();
        }
    }


    public static function getBuyGoods($ids)
    {

        return self::where(['status'=>1,'del'=>0,'id'=>array('in',$ids)])->select();

    }

    public static function getOne($id)
    {
        return self::where(['status'=>1,'del'=>0])->find($id);
    }

}