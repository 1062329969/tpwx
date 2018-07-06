<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 问答管理控制器
 */

namespace app\admin\controller;
use app\admin\model\AnswerImgsModel;
use app\admin\model\AnswerModel;
use app\admin\model\AskModel;
use app\admin\model\AskImgsModel;
use app\admin\model\UserInfoModel;
use app\admin\model\DoctorModel;
use app\admin\model\ForumOnetypeModel;
use app\admin\model\ProjectModel;
use think\Db;
use think\Session;
use app\admin\model\LogModel;
use app\admin\model\CommentModel;

class VideoCategory extends Admin

{

	protected $modelName='';
	public function _initialize()
	{
		parent::_initialize();
		//$this->modelName=new AskModel;
	}

    //首页
    public function index(){

       // dump(Db::name('virtualvipclass')->query('show create table yh_virtualvipclass'));exit;
        //查询分类
        $h_class = Db::name('video_category')->where(['v_pid'=>0])->order('v_order desc')->select();
        foreach ($h_class as $k => $v){
            $h_class[$k]['child'] = Db::name('video_category')->where(['v_pid'=>$v['v_id']])->order('v_order desc')->select();
        }
        $this->assign('h_class',$h_class);
        return $this->fetch();
    }

    //增加
    public function add(){
        $request=request();
        if($request->method()=='POST'){
            $data=input('post.');
           //dump($data);exit;
            $datas = [
                'v_name'=>$data['vc_name'],
                'v_pid'=>isset($data['vc_pid'])?$data['vc_pid']:0,
                'v_order'=>$data['vc_order']
            ];
            if(Db::name('video_category')->insert($datas)){
                $this->success('添加成功',url('index',['mid'=>2]));
            }else{
                $this->error('添加失败',url('index',['mid'=>2]));
            }
        }else{
            $pid = input('param.pid',0);
            $p_name = input('param.p_name','');
            if ($pid){
                $this->assign('pid',$pid);
                $this->assign('p_name',$p_name);
            }
            return $this->fetch();
        }
    }

    /**
     * 分类删除
     */
    public function del(){
        $id=input('param.id');
        $pid=input('param.pid');

        if($pid==0){
            $list = Db::name('video_category')->where(['v_pid'=>$id])->select();
            if($list){
                $this->error('请先删除该分类的子分类');
            }
        }else{
            $list = Db::name('video')->where(['class_second'=>$id])->select();
            if($list){
                $this->error('请先转移该分类的文章');
            }
        }
        $va = Db::name('video_category')->where(['v_id'=>$id])->delete();
        if($va){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    //提交问答
    public function alter(){
        $request=request();
        if($request->method()=='POST'){
            $data=input('post.');
            $id=input('param.id');
            $datas = [
                'v_name'=>$data['v_name'],
                'v_order'=>$data['v_order']
            ];
            if(Db::name('video_category')->where(['v_id'=>$id])->update($datas)){
                $this->success('添加成功',url('index',['mid'=>2]));
            }else{
                $this->error('添加失败',url('index',['mid'=>2]));
            }
        }else{
            $id = input('param.id',0);
            $this->assign('id',$id);
            $p_name = input('param.p_name','');
            if($p_name){
                $this->assign('p_name',$p_name);
            }
            $info = Db::name('video_category')->find($id);
            $this->assign('info',$info);
            return $this->fetch();
        }
    }

   



}