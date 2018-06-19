<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * 跟踪反馈
 */

namespace app\admin\controller;

use app\admin\model\BlockwordsModel;
use app\admin\model\DepartmentModel;
use think\Db;
use think\Session;

class Department extends Admin
{

    protected $Department;
    public function _initialize()
    {
        parent::_initialize();
        $this->Department = new DepartmentModel();
    }

    public function index()
    {
        $list = $this->Department->getAll();
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function add(){
        $request = request();
        if ($request->method() == 'POST'){
            $data=input('post.');
            $new['d_name'] = $data['d_name'];
            $info = $this->Department->where('d_name',$new['d_name'])->find();
            if($info){
                $this->success('部门名称不能重复',url('index'));
            }
            $new['d_state'] = 1;
            $new['d_addtime'] = time();
            isset($data['d_pid'])&&intval($data['d_pid'])>0?$new['d_pid']=$data['d_pid']:$new['d_pid']=0;
            $new['d_uid'] = Session::get('adminid');
            if($this->Department->save($new)){
                $this->success('添加成功',url('index'));
            }else{
                $this->error('添加失败',url('index'));
            }
        }else{
            $list = $this->Department->getTopAll();
            $this->assign('list',$list);
            return $this->fetch();
        }
    }

    public function alter(){
        $d_id=input('param.d_id');
        $this->assign('d_id',$d_id);
        $request = request();
        if ($request->method() == 'POST'){
            $data=input('post.');
            $info = $this->Department->where('d_name',$data['d_name'])->find();
            if($info){
                $this->success('部门名称不能重复',url('index'));
            }
            if($this->Department->allowField(true)->save($data,['d_id'=>$d_id])){
                $this->success('修改成功',url('index'));
            }else{
                $this->error('修改失败',url('index'));
            }
        }else{
            $info = $this->Department->find($d_id);
            $this->assign('info',$info);
            $list = $this->Department->getTopAll($d_id);
            $this->assign('list',$list);
            return $this->fetch();
        }
    }

    public function del(){
        $d_id=intval(input('get.d_id'));
        if($d_id){
            $info = $this->Department->getc($d_id);
            if($info){
                $this->error('需要删除子部门');
            }else{
                $userlist = Db::name('admin')->where(['did'=>$d_id,'status'=>1])->select();
                if($userlist){
                    $this->error('需要删除或转移子部门员工');
                }else{
                    $this->Department->allowField(true)->save(['d_state'=>2],['d_id'=>$d_id]);
                    $this->success('success');
                }
            }
        }else{
            $this->error('error');
        }
    }
}