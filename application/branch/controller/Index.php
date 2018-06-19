<?php
namespace app\branch\controller;
use think\Controller;
use think\Db;
use think\Session;

class index extends Controller
{
    public function index()
    {
        return $this->fetch();

    }

    public function login()
    {
        $hosname=input('post.hosname');
        $passWord=input('post.passWord');

        if(empty($hosname) || empty($passWord))
        {
            return $this->error('用户名或密码不能为空');
        }
        else
        {
            $passWord=md5($passWord);
            $admin = Db::name('branch')->where(['name'=>$hosname,'password'=>$passWord])->find();

            if(empty($admin))
            {
                return $this->error('用户名或密码不正确');
            }
            else
            {
                if($admin['status']==0)
                {
                    $this->error('该账号被停用，请联系管理员');
                }

                Session::set('branchid',$admin['id']);
                Session::set('hosname',$admin['hosname']);
                Session::set('area',$admin['area']);
                //更新登录时间和ip
                Db::name('admin')->where(['id'=>$admin['id']])->update(['login_time'=>time()]);
                return $this->success('登陆成功,跳转到管理主页',url('Main/index'));
            }

        }
    }

}