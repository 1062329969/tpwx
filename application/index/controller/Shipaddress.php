<?php
/**
 * Created by yongxianghui.net.
 * User: liuyixin
 * Date: 2018/1/25
 * 药品券
 */
namespace app\index\controller;

use app\index\model\ShipaddressModel;
use Phpqrcode\QRcode;
use think\Db;
use think\Session;
class Shipaddress extends Wap
{
    protected $ShipaddressModel;
    protected function _initialize()
    {
        //获得授权
        $this->getUserInfo();
        $this->assign('fansInfo',$this->fansInfo);
        $this->ShipaddressModel = new ShipaddressModel();
    }

    /**
     * @author Liuyixin
     * @return 该用户所有的收货地址
     */
    public function index()
    {
        $go = input('param.go');
        $this->checkLogin();
        $uid = $this->fansInfo['id'];
        $list = $this->ShipaddressModel->where(['sd_uid'=>$uid])->select();
        $this->assign('list',$list);
        $this->assign('go',$go);
        return $this->fetch();
    }

    public function add(){
        $this->checkLogin();
        $request=request();
        if($request->method()=='POST'){
            $data=input('post.');
            $data['sd_uid']=$this->fansInfo['id'];
            $data['sd_addtime']=time();
            $data['sd_updatetime']=time();
            if(isset($data['sd_default']) && intval($data['sd_default'])){
                $this->ShipaddressModel->update(['sd_default'=>0],['sd_uid'=>$this->fansInfo['id']]);
            }
            if($this->ShipaddressModel->allowField(true)->save($data)){
                if($data['go']){
                    $this->redirect(url('goods/cart'));
                }else{
                    $this->success('添加成功',url('index'));
                }
            }else{
                if($data['go']){
                    $this->error('添加失败',url('index',['go'=>$data['go']]));
                }else{
                    $this->error('添加失败',url('index'));
                }

            }
        }else{
            $this->checkLogin();
            return $this->fetch();
        }
    }

    public function alter()
    {
        $this->checkLogin();
        $uid = $this->fansInfo['id'];
        $request=request();
        if($request->method()=='POST'){
            $data=input('post.');
            $data['sd_uid']=$this->fansInfo['id'];
            $data['sd_updatetime']=time();
            if(isset($data['sd_default']) && intval($data['sd_default'])){
                $this->ShipaddressModel->update(['sd_default'=>0],['sd_uid'=>$this->fansInfo['id']]);
            }else{
                $data['sd_default']=0;
            }
            if($this->ShipaddressModel->allowField(true)->save($data,['sd_id'=>$data['sd_id']])){
                $this->success('修改成功',url('index'));
            }else{
                $this->error('修改失败',url('index'));
            }
        }else{
            $id=input('param.sd_id');
            $detail = $this->ShipaddressModel->where('sd_uid',$uid)->find($id);
            if(!$detail){
                $this->error('收货地址不存在！');
            }else{
                $this->assign('detail',$detail);
            }
            return $this->fetch();
        }
    }

    public function setdefault(){
        //检查是否登录
        $this->checkLogin();
        $uid = $this->fansInfo['id'];
        $id=input('param.sd_id');
        $val = $this->ShipaddressModel->where('sd_uid',$uid)->find($id);
        if($val){
            if($val['sd_default']){
                if($this->ShipaddressModel->where(['sd_id'=>$id])->update(['sd_default'=>0])){
                    return ['code'=>1,'msg'=>'修改成功'];
                }else{
                    return ['code'=>0,'msg'=>'修改失败'];
                }
            }else{
                $this->ShipaddressModel->where(['sd_uid'=>$uid])->update(['sd_default'=>0]);
                if($this->ShipaddressModel->where(['sd_id'=>$id])->update(['sd_default'=>1])){
                    return ['code'=>1,'msg'=>'修改成功'];
                }else{
                    return ['code'=>0,'msg'=>'修改失败'];
                }
            }
        }else{
            return ['code'=>0,'msg'=>'收货地址不存在'];
        }
    }

    public function del(){
        $uid = $this->fansInfo['id'];
        $id=input('param.sd_id');
        if($this->ShipaddressModel->where('sd_uid',$uid)->find($id)){
            if($this->ShipaddressModel->where(['sd_id'=>$id])->delete()){
                return ['code'=>1];
            }else{
                return ['code'=>0,'msg'=>'删除失败'];
            }
        }else{
            return ['code'=>0,'msg'=>'收货地址不存在'];
        }
    }

}