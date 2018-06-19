<?php
/**
 * Created by Xmh.
 * User: wang fuqiang
 * Date: 2017/10/17
 * Time: 17:04
 */

namespace app\branch\controller;
use think\Controller;
use think\Session;

class Branch extends Controller
{


    //判断session是否已经过期 过期自动登录
    protected function _initialize()
    {
        $this->checkAdminLogin();
    }


    //判断session是否已经过期 过期自动登录
    protected function checkAdminLogin()
    {
        $adminid=Session::get('branchid');
        $username=Session::get('hosname');
        if(empty($adminid) || empty($username))
        {
            $this->redirect('/branch.php');
        }

    }


    //修改密码
    public function alterps()
    {

        $request=request();
        if($request->method()=='POST')
        {
            $data=input('post.');
            if(empty($data['oldps']) || empty($data['newps']) || empty($data['newps2']))
            {
                $this->error('新旧密码都不能为空！');
            }
            if($data['newps']!=$data['newps2'])
            {
                $this->error('新密码和确认密码不一致！');
            }

            $admin=Db::name('branch')->where('id',Session::get('branchid'))->find();
            if($admin['password']!=md5($data['oldps']))
            {
                $this->error('旧密码不正确！');
            }
            else
            {
                Db::name('branch')->where('id',Session::get('branchid'))->update(array('password'=>md5($data['newps'])));
                $this->success('密码修改成功！',url('out'));
            }
        }


    }

    //退出
    protected function out()
    {
        if(BIND_MODULE=='admin')
        {
            Session::delete('branchid');
            Session::delete('hosname');

        }

        $this->redirect('/'.BIND_MODULE.'.php');

    }



}