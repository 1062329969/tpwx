<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 *后台的顶级基类
 */

namespace app\admin\controller;

use think\Controller;
use think\Session;
use think\Db;

class GlobalCheck extends Controller
{

	protected $table='';
	protected $uid='';

	//判断session是否已经过期 过期自动登录
	protected function checkAdminLogin()
	{
		$adminid=Session::get('adminid');
		$username=Session::get('username');
		// var_dump($adminid);
		// var_dump($username);
		if(empty($adminid) || empty($username))
		{
			$this->redirect('/admin.php');
		}

	}

	//返回上一级链接的 session
	protected function getBackUrl()
	{
		//获得当前的url地址 用于返回
		$tbackUrl=get_url();
		Session::set('tbackUrl',$tbackUrl);
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

			$admin=Db::name('admin')->where('id',Session::get('adminid'))->find();
			if($admin['password']!=md5($data['oldps']))
			{
				$this->error('旧密码不正确！');
			}
			else
			{
				Db::name('admin')->where('id',Session::get('adminid'))->update(array('password'=>md5($data['newps'])));
				$this->success('密码修改成功！',url('out'));
			}
		}
	}

	//退出
	protected function out()
	{
		if(BIND_MODULE=='admin')
		{
			Session::delete('adminid');
			Session::delete('username');

		}

		$this->redirect('/'.BIND_MODULE.'.php');

	}

}
