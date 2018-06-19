<?php


namespace app\branch\controller;
use app\branch\model\GoodsOrdersModel;
use app\branch\model\GoodsOrdersGoodsModel;
use think\Session;
use kdniao\Kdniao;


class Order extends Branch
{


    public function index()
    {

        $ordersModel=new GoodsOrdersModel();
        $ordersArr=$ordersModel->getAll(Session::get('area'));
        $this->assign('ordersArr',$ordersArr);
        return $this->fetch();
    }

    public function index2()
    {
	    $ordersModel=new GoodsOrdersModel();
	    $ordersArr=$ordersModel->getAll2(Session::get('branchid'));
	    $this->assign('ordersArr',$ordersArr);
    	return $this->fetch('index');
    }


    //物流信息查询
    public function express()
    {
        $oid=input('param.oid');
        $goodsOrders=GoodsOrdersModel::where(['order_id'=>$oid])->find();

        $kdniao=new Kdniao(config('EBusinessID'),config('AppKey'));
        $return=$kdniao->orderTracesSubByJson('text'.time(),'SF',$goodsOrders['express_code']);
        $info=json_decode($return);

        $reStr='快件揽收中...';
        if($info->Success==true)
        {
            if(empty($info->Traces))
            {
                return $reStr=$info->Reason;
            }
            $reStr=array_reverse(json_decode( json_encode($info->Traces),true));

        }
        else
        {
            $reStr='快件揽收中...';
        }

        $this->assign('reStr',$reStr);
        return $this->fetch();
    }


    public function alter()
    {
        $request=request();
        if($request->method()=='POST')
        {
            $data=input('post.');
            if(!empty($data['express_code']))
            {
                $data['status']=2;
	            $data['branch_id']=Session::get('branchid');
            }
            //else
            //{
            //    $data['status']=1;
            //}


            $goodsOrders=new GoodsOrdersModel();
            if($goodsOrders->allowField(true)->save($data,['id'=>$data['id']]))
            {
                $this->success('修改成功！','index');
            }
            else
            {
                $this->error('修改失败！');
            }
        }
        else
        {
            $id=input('param.id');
            $orders=GoodsOrdersModel::get($id);
            $this->assign('orders',$orders);

            //获得购买的商品及数量
            $goodsOrdersGoods=GoodsOrdersGoodsModel::where('oid',$id)->select();
            $this->assign('goodsOrdersGoods',$goodsOrdersGoods);

            return $this->fetch();
        }
    }

}