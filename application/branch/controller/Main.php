<?php
namespace app\branch\controller;
use app\branch\model\GoodsOrdersModel;
use app\branch\model\OrdersModel;
use think\Db;
use think\Session;


class Main extends Branch
{

	//判断session是否已经过期 过期自动登录
	protected function _initialize()
	{
		$this->checkAdminLogin();
	}

	public function index()
    {

        return $this->fetch();
    }

    public function mainIndex()
    {

        $GoodsOrdersModel=new GoodsOrdersModel(Session::get('area'));

		$orderCount=$GoodsOrdersModel->ordersCount();
		$this->assign('orderCount',$orderCount);

		$today=$GoodsOrdersModel->getToday();
		$Yesterday=$GoodsOrdersModel->getYesterday();
		$month=$GoodsOrdersModel->getMonth();
		$total=$GoodsOrdersModel->getTotal();

		$this->assign('today', $today);
		$this->assign('Yesterday', $Yesterday);
		$this->assign('month', $month);
		$this->assign('total', $total);

		//查询最新的订单
		$ordersArr=$GoodsOrdersModel->getAll(Session::get('area'),5);
        $this->assign('orders',$ordersArr);
	    return $this->fetch();
    }
    
    //修改密码
    public function alterps()
    {
		parent::alterps();
    }

	//退出
	public function out()
	{
		parent::out();
	}


}
