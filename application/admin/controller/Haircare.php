<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * 跟踪反馈
 */

namespace app\admin\controller;


use ApiOauth\ApiOauth;
use app\admin\model\HaircareModel;
use app\admin\model\StagearticleModel;
use app\admin\model\StageModel;
use app\admin\model\TrackImgsModel;
use app\admin\model\TrackModel;
use app\admin\model\VisitModel;
use app\index\event\TemplateMessage;
use app\index\model\UserInfoModel;
use think\Db;
use think\Session;

class Haircare extends Admin
{
    protected $modelName='';

    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $list = Db::name('haircare')->order('hc_id desc')->paginate();
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function add(){
        $request=request();
        if($request->method()=='POST'){
            $haircare = new HaircareModel();
            $data=input('param.');
            $data['hc_img']=$this->upload($request,'hc_img');
            $data['hc_uid']=Session::get('adminid');
            $contetn = strip_tags($data['hc_content']);
            $data['hc_sc'] = mb_strlen($contetn,'UTF-8');
            $data['hc_time'] = time();
            if($haircare->allowField(true)->save($data)){
                $this->success('添加成功',url('index'));
            }else{
                $this->error('添加失败',url('index'));
            }
        }else{
            return $this->fetch();
        }
    }

    public function del(){
        $id=input('param.aid');
        $haircare = new HaircareModel();
        $val = $haircare->where(['hc_id'=>$id])->delete();
        if($val){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    public function alter(){
        $id=input('param.aid');
        $haircare = new HaircareModel();
        $request=request();
        if($request->method()=='POST')
        {
            $data=input('param.');
            $contetn = strip_tags($data['hc_content']);
            $data['hc_sc'] = mb_strlen($contetn,'UTF-8');
            $data['hc_img']=$this->upload($request,'hc_img');
            if(!$data['hc_img']){
                unset($data['hc_img']);
            }
            $data['hc_uid']=Session::get('adminid');
            if($haircare->allowField(true)->save($data,['hc_id'=>$id])){
                $this->success('修改成功',url('index'));
            }else{
                $this->error('修改失败',url('index'));
            }
        }
        else
        {
            $content = $haircare->find($id);
            $this->assign('nid',$id);
            $this->assign('content',$content);
            return $this->fetch();
        }
    }

    public function articlecomment(){
        $aid = input('param.aid');
        $list = Db::name('haircarecomment')->where(['c_tid'=>0,'c_aid'=>$aid])->paginate(15);
        $alluid = Db::name('haircarecomment sc')->where(['c_tid'=>0,'c_aid'=>$aid])->field('c_uid')->select();
        $newalluid = array_column($alluid,'c_uid');
        $alluser = Db::name('user_info')->where('id','in',$newalluid)->field('id,wechaname,portrait')->select();
        $newuser = array();
        foreach ($alluser as $uk => $uv) {
            $newuser[$uv['id']] = $uv;
        }
        $this->assign('list',$list);
        $this->assign('newuser',$newuser);
        return $this->fetch();
    }

    public function articlecommentdel(){
        $cid = input('param.cid');
        if(Db::name('haircarecomment')->where(['c_id'=>$cid])->delete()){
            Db::name('haircarecomment')->where(['c_tid'=>$cid])->delete();
            return $this->success('删除成功');
        }else{
            return $this->success('删除成功');
        }
    }

    public function articlecommentchild(){
        $cid = input('param.cid');

        $alluid = Db::name('haircarecomment sc')->where(['c_tid'=>$cid])->field('c_uid,c_cuid')->select();
        $newalluid  = array();
        foreach ($alluid as $v){
            $newalluid[] = $v['c_uid'];
            $newalluid[] = $v['c_cuid'];
        }
        $alluser = Db::name('user_info')->where('id','in',$newalluid)->field('id,wechaname,portrait')->select();
        $newuser = array();
        foreach ($alluser as $uk => $uv) {
            $newuser[$uv['id']] = $uv;
        }
        $this->assign('newuser',$newuser);

        $commontlist = Db::name('haircarecomment')->where(['c_tid'=>$cid])->order('c_id asc')->select();
        $this->assign('commontlist',$commontlist);
        return $this->fetch();
    }
}