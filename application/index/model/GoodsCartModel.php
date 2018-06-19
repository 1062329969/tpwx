<?php
/**
 * Created by aibx.net.
 * User: wafu7969
 * Date: 2017/7/6
 * Time: 14:15
 */

namespace app\index\model;


use think\Model;

class GoodsCartModel extends Model
{
	protected $name='goods_cart';
	protected $pk='id';
	protected $autoWriteTimestamp=true;

    public function getOne($uid,$goods_id,$gg)
    {
    	return $this->where(['status'=>0,'uid'=>$uid,'goods_id'=>$goods_id,'gg'=>$gg])->find();
    }

	public static function getAll($uid)
	{
		return self::where(['status'=>0,'uid'=>$uid])->select();
	}

	public static function getIds($id)
	{
		return self::where(['id'=>['in',$id]])->select();
	}

	public static function getId($id)
	{
		return self::where(['id'=>$id])->find();
	}

	//获得购物车的商品数量
	public static function getCount($uid)
	{
		return self::where(['status'=>0,'uid'=>$uid])->count();
	}
    public static function getcartCount($uid){
        return self::where(['status'=>0,'uid'=>$uid])->sum('nums');
    }
	//关联商品表
	public function goods()
	{
		return $this->hasOne('GoodsModel','id','goods_id')->field('id,gdname,pic,price,number,del,status');
	}


}