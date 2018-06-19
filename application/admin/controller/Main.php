<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 后台主页
 */

namespace app\admin\controller;
use app\admin\model\AdminModel;
use app\admin\model\ContCommentModel;
use app\admin\model\ContTextModel;
use app\admin\model\GoodsOrdersModel;
use app\admin\model\OrdersModel;
use app\admin\model\RoleModel;
use think\Db;
use think\Session;

class Main extends Admin
{
    private $module_id = [
        1=>'首页',
        2=>'商城管理',
        3=>'项目&预约',
        4=>'营销中心',
        5=>'数据中心',
        6=>'帮助中心'
    ];

	//判断session是否已经过期 过期自动登录
	protected function _initialize()
	{
		$this->checkAdminLogin();
		// parent::_initialize();
	}

	public function index()
    {
        $mid = input('param.mid',1);
        $s_mid = Session::get('mid');
        if(!$s_mid){
            parent::out();
        }
        if(!in_array($mid,$s_mid)){
            $this->error('对不起，您没有操作权限！');
        }
        //初始化右侧菜单
        $where['display']=1;
        $where['status']=1;
        $where['level']=0;   //应用
        $order['sort']='asc';
        $navs=Db::name('node')->where(['module_id'=>$mid])->where($where)->order($order)->select();
        $this->assign('navs',$navs);
        $pid = [];
        foreach($navs as $k=>$v){
            $pid[] = $v['id'];
        }
       

        $wheres['display']=1;
        $wheres['status']=1;
//        $wheres['level']=2;   //模块
        $navx=Db::name('node')->where($wheres)->where('pid','in',$pid)->order($order)->select();
        $this->assign('navx',$navx);

	    //读取管理员权限
	    $AdminModel=AdminModel::get(Session::get('adminid'));
	    $RoleModel=RoleModel::get($AdminModel['rid']);

	    $RoleModel['roles']=str_replace('_','',$RoleModel['roles']);

	    $this->assign('modular',$RoleModel['modular']);
	    $this->assign('roles',$RoleModel['roles']);

        $this->assign('mid',$mid);
        $this->assign('module_id',$this->module_id);

        return $this->fetch();
    }

    public function mainIndex()
    {

        $orderCount=GoodsOrdersModel::ordersCount();
	    $this->assign('orderCount',$orderCount);

        $contCount=ContTextModel::contCount();
	    $this->assign('contCount',$contCount);

	    $today=GoodsOrdersModel::getToday();
	    $Yesterday=GoodsOrdersModel::getYesterday();
	    $month=GoodsOrdersModel::getMonth();
	    $total=GoodsOrdersModel::getTotal();

	    $this->assign('today', $today);
	    $this->assign('Yesterday', $Yesterday);
	    $this->assign('month', $month);
	    $this->assign('total', $total);

	    //最新订单记录前5条
	    $orders=GoodsOrdersModel::getBefor(5);
	    $this->assign('orders', $orders);
	    return $this->fetch();

    }
    
    //修改密码
    public function alterps()
    {
	    $this->table='admin';
	    $this->uid=Session::get('adminid');
    	parent::alterps();
	    return $this->fetch();
    }

	//退出
	public function out()
	{
		parent::out();
	}


}
