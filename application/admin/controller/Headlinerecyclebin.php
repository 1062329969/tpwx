<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * 跟踪反馈
 */

namespace app\admin\controller;

use app\admin\model\AskModel;
use app\admin\model\LogModel;
use think\Db;
use think\Session;

class Headlinerecyclebin extends Admin
{
    protected $modelName='';
    public function _initialize(){
//        parent::_initialize();
        $this->modelName=new AskModel;

    }

    private function getwhere($datas){
        $where = ['specialclass'=>0,'del'=>1];
        //搜索内容关键词
        $arr = ['qtitle','question','author'];
        foreach ($arr as $k=>$v){
            if(isset($datas[$v]) && !empty(trim($datas[$v]))){
                if($v == 'qtitle'||$v == 'question' || $v =='author'){
                    $where[$v] = ['like','%'.$datas[$v].'%'];
                }else{
                    $where[$v] .= $datas[$v];
                }
            }
        }
        //创建时间
        if(isset($datas['time_starandend']) && !empty($datas['time_starandend'])){
            $timearr = explode(' - ',$datas['time_starandend']);
            $where['create_time'] = [
                ['>=',strtotime($timearr['0'])],
                ['<=',strtotime($timearr['0'])],
            ];
        }
        if(Session::get('adminid') !=1){
            $where['admin_user'] = Session::get('adminid');
        }
        //一级分类
        if(!empty($datas['class_first']) && !empty($datas['class_first'])){
            $where['class_first'] = $datas['class_first'];
        }
        //二级分类
        if(!empty($datas['class_second']) && !empty($datas['class_second'])){
            $where['class_second'] = $datas['class_second'];
        }
        //显示状态
        if(isset($datas['lstatus']) && !empty($datas['lstatus'])){
            $where['status'] = $datas['lstatus'];
        }
        //板块
        if(isset($datas['fid']) && !empty($datas['fid'])){
            $where['fid'] = $datas['fid'];
        }

        return $where;
    }
    public function index(){
        $datas = input('param.');
        $data = input('param.lname');
        $statuss = input('param.lstatus');

        //兼容搜索时 没有查询此字段或者导航进来没有查询
        $class_first = isset($datas['class_first'])?$datas['class_first']:'';
        $class_second = isset($datas['class_second'])?$datas['class_second']:'';
        $this->assign('class_first',$class_first);
        $this->assign('class_second',$class_second);
        $where = $this->getwhere($datas);
        //分类
        $category = Db::name('headlineclass')->select();
        $list = Db::name('forum_onetype')->order('id desc')->select();
        $list = array_column($list,'name','id');
        $content = Db::name('ask')->where($where)->order('id desc')->paginate(15,false,['query' => request()->param()]);
        //开始组装用户对应的部门和姓名
        $uid = array_unique(array_column($content->toArray()['data'], 'admin_user'));
        $departmentarr = Db::name('department')->field('d_id,d_name')->select();
        $newdepartmentarr = array_column($departmentarr,'d_name','d_id');
        $admin_user = Db::name('admin')->field('id,username,did')->where('id','in',$uid)->select();
        $newuserarr = [];
        array_walk($admin_user,function($v,$k,$newdepartmentarr) use(&$newuserarr){
            $newuserarr[$v['id']] = [
                    'name'=>isset($v['realnam'])?$v['realnam']:'',
                    'dp'=>isset($newdepartmentarr[$v['did']])?$newdepartmentarr[$v['did']]:''
                ];

        },$newdepartmentarr);

        if(isset($datas['fid'])){
            $this->assign('fid',$datas['fid']);
        }
        $this->assign('datas',$datas);
        $this->assign('category',$category);
        $this->assign('data',$data);
        $this->assign('admin_user',$newuserarr);
        $this->assign('statuss',$statuss);
        $this->assign('content',$content);
        $this->assign('list',$list);

        $class_arr = $this->getclasstree();
        $this->assign('class_arr',$class_arr);

        return $this->fetch();
    }



    public function alter()
    {
        $data = input('param.');
        $vm_id = $data['vm_id'];
        $data = ['del'=>0,'specialclass'=>1,'create_time'=>time(),'sort'=>0];
        if(is_array($vm_id)){
            $res = Db::name('ask')->where('id','in',$vm_id)->update($data);
        }elseif(is_null($vm_id)){
            $this->error('数据错误');
        }else{
            $res = Db::name('ask')->where(['id'=>intval($vm_id)])->update($data);
        }
        if($res){
            LogModel::savelog(4,3,input('param.'),'头条恢复到草稿箱成功');
            $this->success('恢复成功');
        }else{
            LogModel::savelog(4,3,input('param.'),'头条恢复到草稿箱失败');
            $this->error('恢复失败');
        }
    }

    public function del(){
        $data = input('param.');
        $vm_id = $data['vm_id'];
        if(is_array($vm_id)){
            $res = Db::name('ask')->where('id','in',$vm_id)->delete();
        }elseif(is_null($vm_id)){
            $this->error('数据错误');
        }else{
            $res = Db::name('ask')->where(['id'=>intval($vm_id)])->delete();
        }
        if($res){
            LogModel::savelog(4,4,input('param.'),'头条彻底删除删除成功');
            $this->success('删除成功');
        }else{
            LogModel::savelog(4,4,input('param.'),'头条彻底删除删除失败');
            $this->error('删除失败');
        }
    }

    public function getclasstree(){
        $h_class = Db::name('headlineclass')->where(['hc_pid'=>0])->select();
        $class_arr = [];
        foreach ($h_class as $k => $v){
            $child= Db::name('headlineclass')->where(['hc_pid'=>$v['hc_id']])->select();
            $childarr = [];
            foreach ($child as $kc=>$vc){
                $childarr[$vc['hc_id']] = $vc['hc_name'];
            }
            $class_arr[$v['hc_id']] = [
                'name'=>$v['hc_name'],
                'child'=>$childarr
            ];
        }
        return $class_arr;
    }
}