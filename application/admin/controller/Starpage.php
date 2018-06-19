<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * 跟踪反馈
 */

namespace app\admin\controller;

use app\admin\model\StarpageimgModel;
use app\admin\model\StarpageModel;
use think\Db;
use think\Session;

class Starpage extends Admin
{

    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $starpagelist = Db::name('starpage')->where(['sp_state'=>1])->order('sp_id desc')->select();
        $this->assign('starpagelist',$starpagelist);
        return $this->fetch();
    }

    public function add(){
        $request=request();
        if($request->method()=='POST'){
            $StarpageModel = new StarpageModel();
            $data=input('post.');
            $datas['sp_img']=$this->upload($request,'sp_img');
            $datas['sp_name'] = $data['sp_name'];
            $datas['sp_content'] = $data['sp_content'];
            $datas['sp_state'] = $data['sp_state'];
            $datas['sp_uid'] = Session::get('adminid');
            if($StarpageModel->allowField(true)->save($datas)){
                $this->success('添加成功',url('index'));
            }else{
                $this->error('添加失败',url('index'));
            }
        }else{
            return $this->fetch();
        }
    }

    public function alter(){
        $sp_id=input('param.sp_id');
        $StarpageModel = new StarpageModel();
        $request=request();
        if($request->method()=='POST'){
            $StarpageModel = new StarpageModel();
            $data=input('post.');
            $url = $this->upload($request,'sp_img');
            if($url){
                $datas['sp_img'] = $url;
            }
            $datas['sp_name'] = $data['sp_name'];
            $datas['sp_content'] = $data['sp_content'];
            $datas['sp_isopen'] = $data['sp_isopen'];
            $datas['sp_uid'] = Session::get('adminid');
            if($StarpageModel->allowField(true)->save($datas,['sp_id'=>$sp_id])){
                $this->success('修改成功',url('index'));
            }else{
                $this->error('修改失败',url('index'));
            }
        }else{
            $starpage = $StarpageModel->find($sp_id);
            $this->assign('sp_id',$sp_id);
            $this->assign('starpage',$starpage);
            return $this->fetch();
        }
    }

    public function editopenstate(){
        $sp_id=input('param.sp_id');
        $sp_openstate=input('param.sp_openstate');
        $StarpageModel = new StarpageModel();
        if($StarpageModel->allowField(true)->save(['sp_openstate'=>$sp_openstate],['sp_id'=>$sp_id])){
            $this->success('修改成功',url('index'));
        }else{
            $this->error('修改失败',url('index'));
        }
    }

    public function del(){
        $sp_id=input('param.sp_id');
        if($sp_id){
            $StarpageModel = new StarpageModel();
            $va = $StarpageModel->where(['sp_id'=>$sp_id])->update(['sp_state'=>2]);
            if($va){
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }else{
            $this->error('删除失败');
        }
    }

    public function starpageimglist(){
        $sp_id=input('param.sp_id');
        $starpageimglist = Db::name('starpageimg')->where(['spi_spid'=>$sp_id])->order('spi_order desc')->select();
        $this->assign('starpageimglist',$starpageimglist);
        $this->assign('sp_id',$sp_id);
        return $this->fetch();
    }
    public function imgadd(){
        $sp_id=input('param.sp_id');
        $request=request();
        if($request->method()=='POST'){
            $StarpageimgModel = new StarpageimgModel();
            $data=input('post.');
            $datas['spi_pic']=$this->upload($request,'spi_img');
            $datas['spi_order'] = $data['spi_order'];
            $datas['spi_url'] = $data['spi_url'];
            $datas['spi_spid'] = $sp_id;
            $datas['spi_uid'] = Session::get('adminid');
            if($StarpageimgModel->allowField(true)->save($datas)){
                $this->success('添加成功',url('starpageimglist',['sp_id'=>$sp_id]));
            }else{
                $this->error('添加失败',url('starpageimglist',['sp_id'=>$sp_id]));
            }
        }else{
            $this->assign('sp_id',$sp_id);
            return $this->fetch();
        }
    }
    public function imgalter(){
        $spi_id=input('param.spi_id');
        $StarpageimgModel = new StarpageimgModel();
        $request=request();
        if($request->method()=='POST'){
            $data=input('post.');
            $url =$this->upload($request,'spi_pic');
            if($url){
                $datas['spi_pic'] = $url;
            }
            $sp_id = $data['sp_id'];
            $datas['spi_order'] = $data['spi_order'];
            $datas['spi_url'] = $data['spi_url'];
            $datas['spi_uid'] = Session::get('adminid');
            if($StarpageimgModel->allowField(true)->save($datas,['spi_id'=>$spi_id]))
            {
                $this->success('修改成功',url('starpageimglist',['sp_id'=>$sp_id]));
            }
            else
            {
                $this->error('修改失败',url('starpageimglist',['sp_id'=>$sp_id]));
            }
        }else{
            $starpageimg = $StarpageimgModel->find($spi_id);
            $this->assign('starpageimg',$starpageimg);
            $this->assign('spi_id',$spi_id);
            return $this->fetch();
        }
    }
    public function imgdel(){
        $sp_id=input('param.spi_id');
        $spi_spid=input('param.spi_spid');
        $StarpageimgModel = new StarpageimgModel();
        $val = $StarpageimgModel->where(['spi_id'=>$sp_id])->delete();
        if($val){
            $this->success('删除成功',url('starpageimglist',['sp_id'=>$spi_spid]));
        }else{
            $this->error('删除失败',url('starpageimglist',['sp_id'=>$spi_spid]));
        }
    }

    public function editorder(){
        $sp_id=input('param.spi_id');
        $orderval=input('param.orderval');
        $StarpageimgModel = new StarpageimgModel();
        $val = $StarpageimgModel->where(['spi_id'=>$sp_id])->update(['spi_order'=>$orderval]);
        if($val){
            $this->success('修改成功',url('starpageimglist'));
        }else{
            $this->error('修改删除',url('starpageimglist'));
        }
    }
}