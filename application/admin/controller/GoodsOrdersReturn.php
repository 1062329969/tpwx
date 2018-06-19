<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 商品订单管理控制器
 */
namespace app\admin\controller;

use app\index\model\GoodsOrdersReturnModel;
use app\index\model\InformationModel;
use think\Db;
use think\Session;
class GoodsOrdersReturn extends Admin
{

    protected $GoodsOrdersReturnModel;

	public function _initialize()
	{
		parent::_initialize();
        $this->GoodsOrdersReturnModel = new GoodsOrdersReturnModel();
	}
	public function index()
	{
        $goodsOrders = $this->GoodsOrdersReturnModel->getall();
        $this->assign('goodsOrders',$goodsOrders);
		return $this->fetch();
	}

	public function alter(){
        $request=request();
		if($request->method()=='POST')
        {
            $data=input('post.');
            $data['return_auditcontent'] = urldecode($data['return_auditcontent']);
            $data['return_audituid'] = Session::get('adminid');
            $data['return_audittime'] = time();
            if($this->GoodsOrdersReturnModel->allowField(true)->save($data,['id'=>$data['return_id']]))
            {
                $returninfo = Db::name('goods_orders_return')->find($data['return_id']);
                $info['i_uid']=$returninfo['return_uid'];
                $info['i_type']=3;
                $info['i_module']=4;
                $info['i_title']='售后通知';
                $info['i_note']=$data['return_auditcontent'];
                $info['i_state']=2;
                $info['i_addtime']=time();
                $info['i_ids']=$data['return_id'];
                $info['i_uids']=0;//系统
                $res = new InformationModel();
                $res->allowField(true)->save($info);
                $this->success('审批成功！','index');
            }
            else
            {
                $this->error('审批失败！','index');
            }
        }
        else
        {
            $id=input('param.returnid');
            $orders= $this->GoodsOrdersReturnModel->find($id);
            $this->assign('orders',$orders);
            return $this->fetch();
        }
    }

}