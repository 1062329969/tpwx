<?php
/**
 * Created by aibx.net.
 * User: wafu7969
 * Date: 2017/6/25
 * Time: 11:59
 */

namespace app\admin\model;


use think\Model;

class GoodsOrdersModel extends Model
{
    protected $autoWriteTimestamp=true;
	protected $name='goods_orders';
	protected $pk='id';

	public static function getAll($page=15)
	{
        return self::order('id desc')->paginate($page);
	}

	public static function getBefor($limit)
	{
		return self::limit(0,$limit)->order('id desc')->select();
	}

	public function userInfo()
	{
		return $this->hasOne('UserInfoModel','id','user_id')->field('id,wechaname,portrait');
	}

	//返回有效的订单数量
	public static function ordersCount()
	{

		return self::where('status!=0')->count();

	}

	protected function getStatusAttr($value)
	{
		if($value==0)
		{
			return '未支付';
		}
		elseif($value==1)
		{
			return '已支付未发货';
		}
		elseif($value==2)
		{
			return '已发货';
		}
		elseif($value==3)
		{
			return '已完成';
		}
	}

	//今日收入
	public static function getToday($whereStr='')
	{
		$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
		$today=self::where('update_time>='.$beginToday.' and status!=0'.$whereStr)->sum('price');
		return $today;
	}

	//昨日收入
	public static function getYesterday($whereStr='')
	{
		$beginYesterday=mktime(0,0,0,date('m'),date('d')-1,date('Y'));
		$endYesterday=mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
		$Yesterday=self::where('update_time>='.$beginYesterday.' and update_time<'.$endYesterday.' and status!=0'.$whereStr)->sum('price');
		return $Yesterday;
	}
	//本月收入
	public static function getMonth($whereStr='')
	{

		$thisMonth=mktime(0,0,0,date('m'),1,date('Y'));
		$month=self::where('update_time>='.$thisMonth.' and status!=0'.$whereStr)->sum('price');
		return $month;
	}

	//总总收入
	public static function getTotal($whereStr='')
	{
		$total=self::where('status!=0'.$whereStr)->sum('price');
		return $total;
	}
}