<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 管理组控制器
 */

namespace app\admin\controller;

use app\admin\model\RoleModel;
use think\Db;
use think\Session;

class Role extends Admin
{
    private $module_id = [
        1=>'首页',
        2=>'商城管理',
        3=>'项目&预约',
        4=>'营销中心',
        5=>'数据中心',
        6=>'帮助中心'
    ];

	public function _initialize()
	{
		parent::_initialize();
        $this->assign('module_id',$this->module_id);
	}

	public function index()
	{
		$role=RoleModel::getAll();
		$this->assign('role',$role);
		return $this->fetch();
	}

	public function add(){
		$request = request();
		if ($request->method() == 'POST'){
			$data=input('post.');
//            var_dump($data);die;
			$modular='';
			$roleStr='';
            if(isset($data['modular'])){
                foreach ($data['modular'] as $key=>$value){
                    $modular=$modular.$key.',';
                    if(isset($data["$key"])){
                        foreach ($data["$key"] as $k=>$v){
                            foreach ($v as $ke=>$va){
                                $roleStr=$roleStr.$k.'|'.$va.',';
                            }
                        }
                    }
                }
            }

            $role=new RoleModel;
			if($role->save(['rname'=>$data['rname'],'modular'=>$modular,'roles'=>$roleStr,'uid'=>Session::get('adminid'),'mid'=>$data['mid']])){
				$this->success('添加成功',url('index'));
			}else{
				$this->error('添加失败',url('index'));
			}
		}else{
            $roleinfo=RoleModel::get($this->getadmin()->rid);
            $roles = $roleinfo->roles;
            $rolesarr = explode(',', $roles);
            array_pop($rolesarr);
            $this->assign('rolesarr',$rolesarr);
            array_walk($rolesarr,function(&$v,$k){
                $v = substr($v,0,strrpos($v,'|'));
            });
            $newrolesarr = array_unique($rolesarr);
            $this->assign('newrolesarr',$newrolesarr);
            //读取所有的节点
			$where['display']=1;
			$where['status']=1;
			$where['level']=0;   //应用
			$order['sort']='asc';
            if(Session::get('adminid')==1){
                $navs=Db::name('node')->where($where)->order($order)->select();
            }else{
                $navs=Db::name('node')->where($where)->where('id','in', $roleinfo->modular)->order($order)->select();
            }
			$this->assign('navs',$navs);
			foreach($navs as $k=>$v){
				$pid[] = $v['id'];
			}
			$wheres['display']=1;
			$wheres['status']=1;
			$wheres['level']=2;   //模块
			$navx=Db::name('node')->where($wheres)->where('pid','in',$pid)->order($order)->select();
			$this->assign('navx',$navx);
			return $this->fetch();
		}
	}

	public function alter(){
		$request = request();
		if ($request->method() == 'POST'){
			$data=input('post.');
			$modular='';
			$roleStr='';
			if(isset($data['modular'])){
				foreach ($data['modular'] as $key=>$value){
					$modular=$modular.$key.',';
					if(isset($data["$key"])){
						foreach ($data["$key"] as $k=>$v){
							foreach ($v as $ke=>$va){
								$roleStr=$roleStr.$k.'|'.$va.',';
							}
						}
					}
				}
			}

			$role=new RoleModel;
//            var_dump($data['mid']);die;
			if($role->save(['rname'=>$data['rname'],'modular'=>$modular,'roles'=>$roleStr,'mid'=>$data['mid']],['id'=>$data['id']])){
				$this->success('修改成功',url('index'));
			}else{
				$this->error('修改失败',url('index'));
			}
		}else{
            $roleinfo=RoleModel::get($this->getadmin()->rid);
            $roles = $roleinfo->roles;
            $rolesarr = explode(',', $roles);
            array_pop($rolesarr);
            $this->assign('rolesarr',$rolesarr);
            array_walk($rolesarr,function(&$v,$k){
                $v = substr($v,0,strrpos($v,'|'));
            });
            $newrolesarr = array_unique($rolesarr);
            $this->assign('newrolesarr',$newrolesarr);
			//读取所有的节点
			$where['display']=1;
			$where['status']=1;
			$where['level']=0;   //应用
			$order['sort']='asc';
            if(Session::get('adminid')==1){
                $navs=Db::name('node')->where($where)->order($order)->select();
            }else{
                $navs=Db::name('node')->where($where)->where('id','in', $roleinfo->modular)->order($order)->select();
            }
			$this->assign('navs',$navs);
			foreach($navs as $k=>$v){
				$pid[] = $v['id'];
			}

			$wheres['display']=1;
			$wheres['status']=1;
			$wheres['level']=2;   //模块
			$navx=Db::name('node')->where($wheres)->where('pid','in',$pid)->order($order)->select();
			$this->assign('navx',$navx);

			//读取修改的角色
			$id=input('param.id');
			$content=RoleModel::get($id);
			$this->assign('content',$content);
			$this->assign('midarr',explode(',',$content['mid']));
			$this->assign('id',$id);
			return $this->fetch();
		}
	}

	public function del()
	{

		$id=input('param.id');
		$roleModel=new RoleModel;
		if($roleModel->save(['del'=>1],['id'=>$id]))
		{
			echo '{"code":"1"}';
		}
		else
		{
			echo '{"code":"0"}';
		}
	}

}