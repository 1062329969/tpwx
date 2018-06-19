<?php
/**
 * Created by Xmh.
 * User: wang fuqiang
 * Date: 2017/10/18
 * Time: 11:46
 */

namespace app\branch\model;


use think\Model;

class GoodsOrdersModel extends Model
{

    protected $autoWriteTimestamp=true;
    protected $name='goods_orders';
    protected $pk='id';

    //未发货
    public function getAll($area,$page=15)
    {
    	$areaArr=explode(',',$area);
        //合成like语句
        $like=array();
        foreach($areaArr as $key=>$value)
        {
            $like[]="address like '%".$value."%'";
        }

        //$where['address']=['like',$like,'OR'];
        //$where['status']=1;
        return self::where('status=1 and ('.implode(' or ',$like).')')->order('id desc')->paginate($page);


    }

	//已经发货
	public function getAll2($branch_id,$page=15)
	{

		$where['branch_id']=$branch_id;
		$where['status']=2;

		return self::where($where)->order('id desc')->paginate($page);
	}




    //关联用户表
    public function userInfo()
    {
        return $this->hasOne('UserInfoModel','id','user_id')->field('id,wechaname,portrait');
    }

    //返回有效的订单数量
    public function ordersCount()
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
    }

    //今日收入
    public function getToday($whereStr='')
    {
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $today=self::where('update_time>='.$beginToday.' and status!=0'.$whereStr)->sum('price');
        return $today;
    }

    //昨日收入
    public function getYesterday($whereStr='')
    {
        $beginYesterday=mktime(0,0,0,date('m'),date('d')-1,date('Y'));
        $endYesterday=mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
        $Yesterday=self::where('update_time>='.$beginYesterday.' and update_time<'.$endYesterday.' and status!=0'.$whereStr)->sum('price');
        return $Yesterday;
    }
    //本月收入
    public function getMonth($whereStr='')
    {

        $thisMonth=mktime(0,0,0,date('m'),1,date('Y'));
        $month=self::where('update_time>='.$thisMonth.' and status!=0'.$whereStr)->sum('price');
        return $month;
    }

    //总总收入
    public function getTotal($whereStr='')
    {
        $total=self::where('status!=0'.$whereStr)->sum('price');
        return $total;
    }

}