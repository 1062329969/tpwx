<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * 跟踪反馈
 */

namespace app\admin\controller;

use app\admin\model\FeedbacktypeModel;
use app\admin\model\ForumOnetypeModel;
use think\Db;
use think\Session;
class Virtualvipclass extends Admin
{

    public function _initialize()
    {
//        parent::_initialize();
    }

    public function index(){
        //查询分类
        $h_class = Db::name('virtualvipclass')->where(['vc_pid'=>0])->order('vc_order desc')->select();
        foreach ($h_class as $k => $v){
            $h_class[$k]['child'] = Db::name('virtualvipclass')->where(['vc_pid'=>$v['vc_id']])->order('vc_order desc')->select();
        }
        $this->assign('h_class',$h_class);
        return $this->fetch();
    }

    /**
     * 分类删除
     */
    public function del(){
        $id=input('param.id');
        $pid=input('param.pid');

        if($pid==0){
            $list = Db::name('virtualvipclass')->where(['vc_pid'=>$id])->select();
            if($list){
                $this->error('请先删除该分类的子分类');
            }
        }else{
            $list = Db::name('ask')->where(['class_second'=>$id])->select();
            if($list){
                $this->error('请先转移该分类的文章');
            }
        }
        $va = Db::name('virtualvipclass')->where(['vc_id'=>$id])->delete();
        if($va){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    public function add(){
        $request=request();
        if($request->method()=='POST'){
            $data=input('post.');
            $datas = [
                'vc_name'=>$data['vc_name'],
                'vc_pid'=>isset($data['vc_pid'])?$data['vc_pid']:0,
                'vc_order'=>$data['vc_order']
            ];
            if(Db::name('virtualvipclass')->insert($datas)){
                $this->success('添加成功',url('index',['mid'=>2]));
            }else{
                $this->error('添加失败',url('index',['mid'=>2]));
            }
        }else{
            $pid = input('param.pid',0);
            $p_name = input('param.p_name','');
            if ($pid){
                $this->assign('pid',$pid);
                $this->assign('p_name',$p_name);
            }
            return $this->fetch();
        }
    }

    public function alter(){
        $request=request();
        if($request->method()=='POST'){
            $data=input('post.');
            $id=input('param.id');
            $datas = [
                'vc_name'=>$data['vc_name'],
                'vc_order'=>$data['vc_order']
            ];
            if(Db::name('virtualvipclass')->where(['vc_id'=>$id])->update($datas)){
                $this->success('添加成功',url('index',['mid'=>2]));
            }else{
                $this->error('添加失败',url('index',['mid'=>2]));
            }
        }else{
            $id = input('param.id',0);
            $this->assign('id',$id);
            $p_name = input('param.p_name','');
            if($p_name){
                $this->assign('p_name',$p_name);
            }
            $info = Db::name('virtualvipclass')->find($id);
            $this->assign('info',$info);
            return $this->fetch();
        }
    }

    public function getclasstree(){
        $h_class = Db::name('virtualvipclass')->where(['vc_pid'=>0])->select();
        $class_arr = [];
        foreach ($h_class as $k => $v){
            $child= Db::name('virtualvipclass')->where(['vc_pid'=>$v['vc_id']])->select();
            $childarr = [];
            foreach ($child as $kc=>$vc){
                $childarr[$vc['vc_id']] = $vc['vc_name'];
            }
            $class_arr[$v['vc_id']] = [
                'name'=>$v['vc_name'],
                'child'=>$childarr
            ];
        }
        return $class_arr;
    }

}