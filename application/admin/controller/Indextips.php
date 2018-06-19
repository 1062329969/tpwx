<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 后台登录控制器
 */

namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Session;
use app\admin\model\IndextipsModel;

class Indextips extends Admin
{
    protected $modelName='';

    public function _initialize()
    {
        parent::_initialize();
    }


    public function index()
    {
        $content=IndextipsModel::paeges(15);
        $this->assign('content',$content);
        return $this->fetch();
    }

    public function add()
    {
        $request=request();
        if($request->method()=='POST')
        {
            $data=input('post.');
            $TipsModel = new IndextipsModel();

            $datas['i_title'] = $data['i_title'];
            $datas['i_types'] = $data['i_type_s'];
            $datas['i_type'] = $data['i_type'];
            $datas['i_addtime'] = time();
            $datas['i_updatetime'] = time();

            if($TipsModel->allowField(true)->save($datas))
            {
                $this->success('添加成功',url('index'));
            }
            else
            {
                $this->error('添加失败',url('index'));
            }
        }
        else
        {
            $forum = Db::name('stage')->select();
            foreach($forum as $k=>$v){
                foreach($v as $key=>$val){
                    $arr[$key][]=$val;
                }
            }
            $stage_day = Db::name('stage_day')->select();

            $this->assign('stage_day',$stage_day);
            $this->assign('arr',$arr);
            $this->assign('forum',$forum);
            return $this->fetch();
        }
    }


    public function stage_day(){
        $data = input('post.');
        if($data['option'] == 1){
            return ['code'=>1,'msg'=>'ok'];
        }elseif ($data['option'] == 2){
            return ['code'=>2,'msg'=>'ok'];
        }elseif ($data['option'] == 3){
            return ['code'=>3,'msg'=>'ok'];
        }
    }

    public function alter()
    {
        $id=input('param.id');
        $TipsModel = new IndextipsModel();
        $request=request();
        if($request->method()=='POST')
        {
            $data=input('post.');
            $datas['i_title']        = $data['i_title'];
            $datas['i_type']         = $data['i_type'];
            $datas['i_types']       = $data['i_type_s'];
            $datas['i_updatetime']   = time();
            if( $TipsModel->allowField(true)->save($datas,['i_id'=>$data['i_id']]))
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
            $forum = Db::name('stage')->select();
            $arr = Db::name('stage_day')->select();
            $indexnews = Db::name('index_tips')->where(['i_id'=>$id])->find();
            $stage_day = Db::name('stage_day')->where(['x_type'=>$indexnews['i_type']])->select();
            $this->assign('forum',$forum);
            $this->assign('arr',$arr);
            $this->assign('stage_day',$stage_day);
            $this->assign('id',$id);
            $this->assign('indexnews',$indexnews);


            return $this->fetch();
        }
    }

    public function del(){
        $id=input('param.id');
        if(!empty($id)){
            $Indextips = new IndextipsModel();
            if($Indextips->where('i_id',$id)->delete()){
                $this->error('删除成功',url('index'));
            }else{
                $this->error('删除失败',url('index'));
            }
        }

    }
}
