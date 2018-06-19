<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 管理员管理
 */

namespace app\admin\controller;


use app\admin\model\AdminModel;
use app\admin\model\DepartmentModel;
use app\admin\model\RoleModel;
use think\Session;

class Manage extends Admin
{

	public function _initialize()
	{
		parent::_initialize();
	}

	public function index()
	{
        $admin=AdminModel::getAll();
        $Department = new DepartmentModel();
        foreach ($admin as $k=>$v){
            $deinfo = $Department->find($v['did']);
            $admin[$k]['dname'] = $deinfo['d_name'];
        }
		$this->assign('admin',$admin);
		return $this->fetch();
	}

	public function add(){
		$request = request();
		if ($request->method() == 'POST')
		{
			$data=input('post.');
			if(empty($data['username']) || empty($data['password']) || empty($data['password2']))
			{
                $this->error('用户名和密码必须填写');
			}

			$AdminModel=AdminModel::where(['username'=>$data['username']])->find();
			if(!empty($AdminModel)){
				$this->error('该登录名已经存在');
			}

			if(empty($data['rid'])){
				$this->error('请选择用户角色');
			}

            if($data['password']!=$data['password2']){
                $this->error('两次密码输入不一致');
            }

            if(empty($data['did'])){
                $this->error('请选择部门');
            }
            if(empty($data['realname'])){
                $this->error('请填写真实姓名');
            }

			$adminModel=new AdminModel;
			if($adminModel->save(
			    [
			        'rid'=>$data['rid'],
                    'username'=>$data['username'],
                    'password'=>md5($data['password']),
                    'status'=>$data['status'],
                    'did'=>$data['did'],
                    'realname'=>$data['realname'],
                    'uid'=>Session::get('adminid')
                ]
            )){
				$this->success('添加成功',url('index'));
			}
			else
			{
				$this->error('添加失败',url('index'));
			}
		}
		else
		{
			//读取角色
			$role=RoleModel::getAll();
			$this->assign('role',$role);
            $Department = new DepartmentModel();
            $list = $Department->getAll();
            $this->assign('list',$list);
            return $this->fetch();
		}
	}

	public function alter()
	{
		$request = request();
		if ($request->method() == 'POST')
		{
			$data=input('post.');
			if(!empty($data['password']))
			{
				if(!empty($data['password2']) && $data['password']!=$data['password2'])
				{
					$this->error('两次密码输入不一致');
				}

			}

			if(empty($data['rid']))
			{
				$this->error('请选择用户角色');
			}

			$adminModel=new AdminModel;
			if($adminModel->save(
			    [
			        'rid'=>$data['rid'],
                    'password'=>md5($data['password']),
                    'status'=>$data['status'],
                    'did'=>$data['did'],
                    'realname'=>$data['realname']
                ],[
                    'id'=>$data['id'
                    ]
            ])){
				$this->success('修改成功',url('index'));
			}
			else
			{
				$this->error('修改失败',url('index'));
			}
		}
		else
		{
			//读取角色
			$role=RoleModel::getAll();
			$this->assign('role',$role);

			$id=input('param.id');
			$content=AdminModel::get($id);
			$this->assign('content',$content);
            $Department = new DepartmentModel();
            $list = $Department->getAll();
            $this->assign('list',$list);
			return $this->fetch();
		}
	}

    public function del(){

        $id=input('param.id');
        $adminModel=new AdminModel;
        if($adminModel->save(['status'=>2],['id'=>$id]))
        {
            echo '{"code":"1"}';
        }
        else
        {
            echo '{"code":"0"}';
        }
    }
    public function stop(){

        $id=input('param.id');
        $adminModel=new AdminModel;
        if($adminModel->save(['status'=>0],['id'=>$id]))
        {
            echo '{"code":"1"}';
        }
        else
        {
            echo '{"code":"0"}';
        }
    }
    public function start(){

        $id=input('param.id');
        $adminModel=new AdminModel;
        if($adminModel->save(['status'=>1],['id'=>$id]))
        {
            echo '{"code":"1"}';
        }
        else
        {
            echo '{"code":"0"}';
        }
    }

}