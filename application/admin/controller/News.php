<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * 跟踪反馈
 */

namespace app\admin\controller;


use ApiOauth\ApiOauth;
use app\admin\model\IndexnewsModel;
use app\admin\model\NewsModel;
use app\admin\model\StagearticleModel;
use app\admin\model\StageModel;
use app\admin\model\TrackImgsModel;
use app\admin\model\TrackModel;
use app\admin\model\VisitModel;
use app\index\event\TemplateMessage;
use app\index\model\UserInfoModel;
use think\Db;
use think\Session;

class News extends Admin
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $NewsModel = new IndexnewsModel();
        $list = $NewsModel->order(['n_id desc'])->paginate(15);
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function add()
    {
        $request=request();

        if($request->method()=='POST')
        {
            $NewsModel = new IndexnewsModel();
            $data=input('post.');
            if(!trim($data['n_title']) || !$data['n_content']){
                $this->error('添加失败',url('index'));
            }
            $datas['n_title'] = $data['n_title'];
            $datas['n_content'] = $data['n_content'];
            $datas['n_uid'] = Session::get('adminid');
            $datas['n_addtime'] = time();
            $datas['n_updatetime'] = time();

            if($NewsModel->allowField(true)->save($datas))
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
            $stage=Db::name('index_tips')->select();
            $this->assign('stage',$stage);
            return $this->fetch();
        }
    }

    public function alter()
    {
        $nid=input('param.nid');
        $NewsModel = new IndexnewsModel();
        $request=request();
        if($request->method()=='POST')
        {
            $NewsModel = new IndexnewsModel();
            $data=input('post.');
            if(!trim($data['n_title']) || !$data['n_content']){
                $this->error('添加失败',url('index'));
            }
            $datas['n_title'] = $data['n_title'];
            $datas['n_content'] = $data['n_content'];
            $datas['n_uid'] = Session::get('adminid');
            $datas['n_addtime'] = time();
            $datas['n_updatetime'] = time();
            if($NewsModel->allowField(true)->save($datas,['n_id'=>$data['n_id']]))
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
            $id=input('param.nid');
            $indexnews = $NewsModel->find($id);
            $stage=Db::name('index_tips')->select();

            $this->assign('stage',$stage);
            $this->assign('nid',$nid);
            $this->assign('indexnews',$indexnews);
            return $this->fetch();
        }
    }

    public function del(){
        $nid=input('param.nid');
        $NewsModel = new IndexnewsModel();
        if($NewsModel->where('n_id',$nid)->delete()){
            $this->error('删除成功',url('index'));
        }else{
            $this->error('删除失败',url('index'));
        }
    }
}