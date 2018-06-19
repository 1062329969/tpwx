<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * 跟踪反馈
 */

namespace app\admin\controller;


use app\index\model\TipsModel;
use app\admin\model\NewsModel;
use think\Db;
use think\Session;

class Tips extends Admin
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $TipsModel = new TipsModel();
        $list = $TipsModel->order(['t_id desc'])->paginate(15);
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function add()
    {
        $request=request();
        if($request->method()=='POST')
        {
            $TipsModel = new TipsModel();
            $data=input('post.');
            $datas['t_title'] = $data['t_title'];
            $datas['t_type'] = $data['t_type'];
            $datas['t_delay'] = $data['t_delay'];
            $datas['t_content'] = $data['t_content'];
            $datas['t_totalscore_s'] = $data['t_totalscore_s'];
            $datas['t_totalscore_e'] = $data['t_totalscore_e'];
            $datas['t_addtime'] = time();
            $datas['t_updatetime'] = time();
            $datas['t_uid'] = Session::get('adminid');
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
            return $this->fetch();
        }
    }

    public function alter()
    {
        $tid=input('param.tid');
        $TipsModel = new TipsModel();
        $request=request();
        if($request->method()=='POST')
        {
            $TipsModel = new TipsModel();
            $data=input('post.');
            $datas['t_title'] = $data['t_title'];
            $datas['t_content'] = $data['t_content'];
            $datas['t_type'] = $data['t_type'];
            $datas['t_delay'] = $data['t_delay'];
            $datas['t_totalscore_s'] = $data['t_totalscore_s'];
            $datas['t_totalscore_e'] = $data['t_totalscore_e'];
            $datas['t_updatetime'] = time();
            $datas['t_uid'] = Session::get('adminid');
            if($TipsModel->allowField(true)->save($datas,['t_id'=>$tid]))
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
            $indexnews = $TipsModel->find($tid);
            $this->assign('tid',$tid);
            $this->assign('indexnews',$indexnews);
            return $this->fetch();
        }
    }

    public function del(){
        $tid=input('param.tid');
        $TipsModel = new TipsModel();
        if($TipsModel->where('t_id',$tid)->delete()){
            $this->error('删除成功',url('index'));
        }else{
            $this->error('删除失败',url('index'));
        }
    }
}