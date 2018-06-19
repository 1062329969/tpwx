<?php


namespace app\index\model;


use think\Model;

class EvaluateModel extends Model
{
	protected $name='goods_evaluate';
	protected $pk='id';

	public static function getAll($goods_id,$page)
	{
		$where['status']=1;
		$where['del']=0;
		$where['goods_id']=$goods_id;
        $pages = $page*10;
		return self::where($where)->order('id desc')->limit($pages,10)->select();
	}

	//获得评价总数
	public static function getCount($goods_id)
	{
		$where['status']=1;
		$where['del']=0;
		$where['goods_id']=$goods_id;

		return self::where($where)->count();
	}



	//差评的数量
	public static function getCpCount($goods_id)
	{
		$where['status']=1;
		$where['del']=0;
		$where['goods_id']=$goods_id;
		$where['xx']=['in','1'];
		return self::where($where)->count();
	}

	//中评的数量
	public static function getZpCount($goods_id)
	{
		$where['status']=1;
		$where['del']=0;
		$where['goods_id']=$goods_id;
		$where['xx']=['in','2,3,4'];
		return self::where($where)->count();
	}

	//好评的数量
	public static function getHpCount($goods_id)
	{
		$where['status']=1;
		$where['del']=0;
		$where['goods_id']=$goods_id;
		$where['xx']=['in','5'];
		return self::where($where)->count();
	}


	//关联用户表
	public function userInfo()
	{
		return $this->hasOne('UserInfoModel','id','uid')->field('id,portrait,wechaname,city');
	}

}