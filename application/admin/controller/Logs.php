<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * 跟踪反馈
 */

namespace app\admin\controller;


use app\admin\model\LogModel;
use think\Db;
use think\Log;
use think\Session;

class Logs extends Admin
{

    protected $modules = [
        1=>'登录',
        2=>'活动调查',
        3=>'诊断调查',
        4=>'头条'
    ];
    protected $LogModel;
    public function _initialize()
    {
//        parent::_initialize();
        $this->LogModel = new LogModel();
    }

    /**
     * 首页
     * @return mixed
     */
    public function index(){
        $data = input('param.');
        $where = array();
        if(isset($data['l_modules']) && !empty($data['l_modules'])){
            $where['l_modules'] = $data['l_modules'];
        }
        if(isset($data['l_operationtype']) && !empty($data['l_operationtype'])){
            $where['l_operationtype'] = $data['l_operationtype'];
        }
        $list = Db::name('log')->where($where)->paginate(15,false,['query' => request()->param()]);
        $this->assign('where',$where);
        $this->assign('modules',$this->modules);
        $this->assign('list',$list);
        return $this->fetch();
    }
}