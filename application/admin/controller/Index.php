<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 后台登录控制器
 */

namespace app\admin\controller;
use app\admin\model\DepartmentModel;
use app\admin\model\LogModel;
use think\Controller;
use think\Db;
use think\Log;
use think\Session;

class Index extends Controller
{
    public function _initialize(){
        parent::_initialize();
        if(Session::get('adminid')){
            $this->redirect(url('main/index'));
        }
    }

    public function index()
    {
        return $this->fetch();
    }

    public function archives(){
        return $this->fetch('index/archives');
    }
    public function login()
    {
        $userName=input('post.userName');
        $passWord=input('post.passWord');

        if(empty($userName) || empty($passWord))
        {
            return $this->error('用户名或密码不能为空');
        }
        else
        {
            $passWord=md5($passWord);
	        $admin = Db::name('admin')->where(['username'=>$userName,'password'=>$passWord,'del'=>0])->find();

            if(empty($admin)){
                LogModel::savelog(1,1,input('param.'),'用户登录失败');
                return $this->error('用户名或密码不正确');
            }else{
                if($admin['status']==0){
                    LogModel::savelog(1,1,input('param.'),'该账号被停用，请联系管理员');
                    $this->error('该账号被停用，请联系管理员');
                }elseif($admin['status']==2){
                    LogModel::savelog(1,1,input('param.'),'该账号被删除，请联系管理员');
                    $this->error('该账号被删除，请联系管理员');
                }
                $config =  \think\Config::get('session');
                $config['lifetime'] = 0;
                session($config);
            	Session::set('adminid',$admin['id']);
                $role = Db::name('role')->field('mid')->find($admin['rid']);
                Session::set('mid',explode(',',$role['mid']));
                Session::set('username',$admin['username']);
//                $DepartmentModel = new DepartmentModel();
                empty($admin['did'])?$did=0:$did = $admin['did'];
                $dinfo = Db::name('department')->find($did);
                Session::set('d_name',$dinfo['d_name']);
	            //更新登录时间和ip
	            Db::name('admin')->where(['id'=>$admin['id']])->update(['login_time'=>time()]);
                LogModel::savelog(1,1,input('param.'),'用户登录成功');
                return $this->success('登陆成功,跳转到管理主页',url('Main/index'));
            }
            
        }
    }

    public function droplog(){
        Log::info('//////////////////////////////////////////////');
        $request=request();
        Log::info($request->ip());
        Log::info('//////////////////////////////////////////////');
    }
}
