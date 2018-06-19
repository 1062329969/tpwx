<?php

namespace app\index\model;
use think\Model;

class ContAdModel extends Model
{
	protected $name='cont_ad';
	protected $pk='id';
	protected $autoWriteTimestamp=true;


	public static function getAll($position)
	{
		return self::where(['del'=>0,'status'=>1,'position'=>$position])->order('sort desc,id desc')->select();
	}
	//首页商品推荐
    public static function getAll_tow($position)
    {
        return self::where(['del'=>0,'status'=>1,'position'=>$position])->order('update_time desc')-> limit('0,3')->select();
    }


    //头条首页为你推荐
    public static function recommend($position)
    {
        return self::where(['del'=>0,'status'=>1,'position'=>$position])->order('sort desc,update_time desc')-> limit('0,4')->select();
    }

    //首页商品下放推荐
    public static function getAll_one($res)
    {
        return self::where(['del'=>0,'status'=>1,'position'=>$res])->order('update_time desc')-> limit('0,3')->select();
    }

	public static function getOne($position)
	{
		return self::where(['del'=>0,'status'=>1,'position'=>$position])->order('id desc')->find();
	}


}