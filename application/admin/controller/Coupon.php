<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 优惠券管理
 */

namespace app\admin\controller;


use app\admin\model\CouponModel;
use app\admin\model\GoodsTypeModel;
use app\admin\model\MsgModel;

class Coupon extends Admin
{
    protected $modelName='';

    public function _initialize()
    {
        parent::_initialize();
        $this->modelName=new CouponModel();
    }

    public function index()
    {
        $content=$this->modelName->getAll(15);
        $this->assign('content',$content);
        return $this->fetch();
    }

    public function add()
    {
        $request=request();
        if($request->method()=='POST')
        {
            $data=input('post.');

            $result=$this->validate($data,'ValidateClass.CK32');
            if(true !== $result)
            {
                // 验证失败 输出错误信息
                $this->error($result);
            }

            if($this->modelName->allowField(true)->save($data))
            {

                //发送站内消息
                //MsgModel::insert(['is_global'=>1,'type'=>3,'title'=>"有新的优惠券注意查收",'msg'=>'有新的优惠券，猛戳查看~~','url'=>'/coupon/index.html']);
                //return ['code'=>1];

                $this->success('添加成功',url('index'));
            }
            else
            {
                $this->error('添加失败',url('index'));
            }
        }
        else
        {
            //读取分类 其中不包含跳转链接的分类
            $contType=GoodsTypeModel::getContType();
            $this->assign('contType',$contType);
            return $this->fetch();
        }
    }

    public function alter()
    {

        $request=request();
        if($request->method()=='POST')
        {
            $data=input('post.');

            $result=$this->validate($data,'ValidateClass.CK32');
            if(true !== $result)
            {
                // 验证失败 输出错误信息
                $this->error($result);
            }


            if($this->modelName->allowField(true)->save($data,['id'=>$data['id']]))
            {


                $this->success('修改成功',url('index'));
            }
            else
            {
                $this->error('修改失败',url('index'));
            }
        }
        else
        {
            $id=input('param.id');
            //读取分类 其中不包含跳转链接的分类
            $contType=GoodsTypeModel::getContType();
            $this->assign('contType',$contType);

            $content=$this->modelName->get($id);
            $this->assign('content',$content);
            return $this->fetch();
        }

    }


    public function del()
    {

        $id=input('param.id');
        if($this->modelName->save(['del'=>1],['id'=>$id]))
        {
            echo '{"code":"1"}';
        }
        else
        {
            echo '{"code":"0"}';
        }
    }

}