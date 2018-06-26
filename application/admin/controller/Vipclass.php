<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * 跟踪反馈
 */

namespace app\admin\controller;

use app\index\model\SmslogModel;
use app\index\event\Message;
use think\Db;
use think\Session;
class Vipclass extends Admin
{

    public function _initialize()
    {
//        parent::_initialize();
    }

    public function index(){
        $list = Db::name('vipclass')->select();
//        echo Db::getLastSql();die;
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function add(){
        $request=request();
        if($request->method()=='POST') {
            $vc_name = input('param.vc_name');
            $vc_int = input('param.vc_int');
            $val = Db::name('vipclass')->insert(['vc_name'=>$vc_name,'vc_int'=>$vc_int,'vc_addtime'=>time(),'vc_update'=>time()]);
            if($val){
                $this->success('添加成功');
            }else{
                $this->error('添加失败');
            }
        }else{
            return $this->fetch();
        }
    }

    public function del(){
        $id = input('param.id');
        if($id==1){
            $this->error('无法删除默认分组');
        }
        $vipclass = Db::name('vipclass')->find($id);
        $del = Db::name('vipclass')->where(['vc_id'=>$id])->delete();
        if($del){
            if($vipclass['vc_isdefault']==1){
                Db::name('vipclass')->where(['vc_id'=>1])->update(['vc_isdefault'=>1]);
            }
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    public function alter(){
        $request=request();
        if($request->method()=='POST') {
            $id = input('param.id');
            $vc_name = input('param.vc_name');
            $vc_int = input('param.vc_int');
            $val = Db::name('vipclass')->where(['vc_id'=>$id])->update(['vc_name'=>$vc_name,'vc_int'=>$vc_int]);
            if($val){
                $this->success('修改成功');
            }else{
                $this->error('修改失败');
            }
        }else{
            $id = input('param.id');
            $info = Db::name('vipclass')->find($id);
            $this->assign('info',$info);
            $this->assign('id',$id);
            return $this->fetch();
        }
    }

    public function setdefault(){
        $id = input('param.id');
        Db::startTrans();
        $res = Db::name('vipclass')->where('1=1')->update(['vc_isdefault'=>2]);
        if($res){
            $val = Db::name('vipclass')->where(['vc_id'=>$id])->update(['vc_isdefault'=>1]);
            if($val){
                Db::commit();
                $this->success('设置成功');
            }else{
                Db::rollback();
                $this->error('设置失败');
            }
        }else{
            Db::rollback();
            $this->error('设置失败');
        }
    }

    public function altersetting(){
        $request=request();
        if($request->method()=='POST') {
            $type = input('vcs_type');
            $val = Db::name('vipclasssetting')->where(['vcs_id'=>1])->update(['vcs_type'=>$type]);
            if($val){
                $this->success('设置成功');
            }else{
                $this->error('设置失败');
            }
        }else{
            $info = Db::name('vipclasssetting')->where(['vcs_id'=>1])->find();
            $this->assign('info',$info);
            return $this->fetch();
        }
    }
}