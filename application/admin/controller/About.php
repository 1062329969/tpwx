<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * 跟踪反馈
 */

namespace app\admin\controller;

use app\admin\model\AboutModel;
use app\admin\model\BlockwordsModel;
use think\Db;
use think\Session;

class About extends Admin
{

    public function _initialize()
    {
        parent::_initialize();
    }

    public function alter()
    {
        $About = new AboutModel();
        $list = $About->order('a_order asc,a_id desc')->paginate(15);
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function add(){
        $request=request();
        if($request->method()=='POST')
        {
            $About = new AboutModel();
            $data=input('post.');
            $data['a_addtime'] = time();
            $data['a_uid'] = Session::get('adminid');
            if($About->allowField(true)->save($data)){
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

    public function index(){
        $aid = 3;
//        $aid = input('param.aid');
        $About = new AboutModel();
        $request=request();
        if($request->method()=='POST')
        {
            $data=input('post.');
            $aid = $data['aid'];
            unset($data['aid']);
            if($About->allowField(true)->save($data,['a_id'=>$aid])){
                $this->success('修改成功',url('index'));
            }
            else
            {
                $this->error('修改失败',url('index'));
            }
        }
        else
        {
            $info = $About->find($aid);
            $this->assign('content',$info);
            return $this->fetch();
        }
    }

    public function del(){
        $aid = input('param.aid');
        if($aid){
            $StageModel = new AboutModel();
            $va = $StageModel->where(['a_id'=>$aid])->delete();
            if($va){
                $this->success('删除成功');
            }else{
                $this->error('error');
            }
        }else{
            $this->error('error');
        }
    }
}