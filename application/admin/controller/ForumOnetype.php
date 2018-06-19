<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * 跟踪反馈
 */

namespace app\admin\controller;

use app\admin\model\FeedbacktypeModel;
use app\admin\model\ForumOnetypeModel;
use think\Db;
use think\Session;
class ForumOnetype extends Admin
{

    public function _initialize()
    {
//        parent::_initialize();
        $this->modelName=new ForumOnetypeModel;

    }

    public function index()
    {
        $mid=input('param.mid',1);
        $this->assign('mid',$mid);

        //查询板块
        $typelist = Db::name('forum_onetype')->select();

        //查询分类
        $h_class = Db::name('headlineclass')->where(['hc_pid'=>0])->order('hc_order desc')->select();
        foreach ($h_class as $k => $v){
            $h_class[$k]['child'] = Db::name('headlineclass')->where(['hc_pid'=>$v['hc_id']])->order('hc_order desc')->select();
        }
        $this->assign('h_class',$h_class);
        $this->assign('typelist',$typelist);
        return $this->fetch();
    }

    public function add(){
        $request=request();
        if($request->method()=='POST')
        {
            $data=input('post.');
            //多文件上传
            if(empty($data['default_img']))
            {
                $data['default_img']=0;
            }
            //上传图片
            $pic=$this->upload($request,'smallimg');
            $pics=$this->upload($request,'info_pic');
            $picss=$this->upload($request,'small_pic');
            if(!empty($pic))
            {
                $data['smallimg']=$pic;
            }
            else
            {
                unset($data['smallimg']);
            }
            if(!empty($pics))
            {
                $data['info_pic']=$pics;
            }
            else
            {
                unset($data['info_pic']);
            }
            if(!empty($picss))
            {
                $data['small_pic']=$picss;
            }
            else
            {
                unset($data['small_pic']);
            }
            $key=0;  //默认图片的位置
            $imgs=array();
            $files = request()->file('pic');
            if(count($files)>0)
            {
                foreach($files as $file)
                {

                    // 移动到框架应用根目录/public/uploads/ 目录下
                    $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                    if($info)
                    {

                        $returnImg=str_replace('\\','/','/uploads/'.$info->getSaveName());
                        //生成缩略图
//                        $returnImg=$this->thumbnail($file,$returnImg);

                        if($key==$data['default_img'])
                        {
                            $data['pic']=$returnImg;
                        }

                        $imgs[]=$returnImg;
                    }
                    else
                    {
                        $this->error($file->getError());
                    }

                    $key=$key+1;

                }
            }
            $ForumOnetype=new ForumOnetypeModel();


            if($this->modelName->allowField(true)->save($data))
            {

                $this->success('添加成功',url('index'));
            }
            else
            {
                $this->error('添加失败',url('index'));
            }
        }
        else
        {
            return $this->fetch();
        }
    }

    public function alter(){
        $request=request();
        if($request->method()=='POST')
        {
            $data=input('post.');


            //默认图片上传
            //上传缩略图图片
            $pic=$this->upload($request,'smallimg');
            $pics=$this->upload($request,'info_pic');
            $picss=$this->upload($request,'small_pic');
            if(!empty($pic))
            {
                $data['smallimg']=$pic;
            }
            else
            {
                unset($data['smallimg']);
            }
            if(!empty($pics))
            {
                $data['info_pic']=$pics;
            }
            else
            {
                unset($data['info_pic']);
            }

            if(!empty($picss))
            {
                $data['small_pic']=$picss;
            }
            else
            {
                unset($data['small_pic']);
            }


            $pic=$this->upload($request,'pic');
            if(!empty($pic))
            {
                $data['pic']=$pic;
            }
            else
            {
                unset($data['pic']);
            }

            //多文件上传
            $imgs=array();
            $imgs=$this->uploadMore($request,'pic2');

            if($this->modelName->allowField(true)->save($data,['id'=>$data['id']]))
            {

                $this->success('修改成功',url('index'));
            }
            else
            {
                $this->error('修改失败',url('index'));
            }
        }
        else
        {
            $id=input('param.id');
            $indexnews = Db::name('forum_onetype')->where(['id'=>$id])->find();
            $this->assign('id',$id);
            $this->assign('indexnews',$indexnews);
            return $this->fetch();
        }
    }

    public function del(){
        $id=input('param.id');
        if(!empty($id)){
            $Feedbacktype = new ForumOnetypeModel();
            $va = $Feedbacktype->where(['id'=>$id])->delete();
            if($va){
                $this->success('success');
            }else{
                $this->error('error');
            }
        }else{
            $this->error('error');
        }
    }

    /**
     * 分类删除
     */
    public function delclass(){
        $id=input('param.id');
        $pid=input('param.pid');

        if($pid==0){
            $list = Db::name('headlineclass')->where(['hc_pid'=>$id])->select();
            if($list){
                $this->error('请先删除该分类的子分类');
            }
        }else{
            $list = Db::name('ask')->where(['class_second'=>$id])->select();
            if($list){
                $this->error('请先转移该分类的文章');
            }
        }
        $va = Db::name('headlineclass')->where(['hc_id'=>$id])->delete();
        if($va){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    /**
     * 转移文章查询
     */
    public function shiftarticle(){
        $id = input('param.id');
        $name = input('param.name');
        $pname = input('param.pname');
        $title = input('param.title');
        $wheres = ['class_second'=>$id];
        if(trim($title)){
            $wheres['qtitle'] = ['like','%'.$title.'%'];
        }
        $list = Db::name('ask')->where($wheres)->paginate(4,false,['query' => request()->param()]);
        $class_arr = $this->getclasstree();
        $this->assign('list',$list);
        $this->assign('name',$name);
        $this->assign('pname',$pname);
        $this->assign('title',$title);
        $this->assign('id',$id);
        $this->assign('class_arr',$class_arr);
        return $this->fetch();
    }

    public function doshiftarticle(){
        $class_first    = input('param.class_first');
        $class_second   = input('param.class_second');
        $id             = input('post.id/a');
        $res = Db::name('ask')->where('id','in',$id)->update([
            'class_first'=>$class_first,
            'class_second'=>$class_second,
        ]);
        if($res){
            $this->success('转移成功');
        }else{
            $this->error('转移失败');
        }
    }

    public function transferplate(){
        $id = input('param.id');
        $name = input('param.name');
        $title = input('param.title');
        $wheres = ['fid'=>$id];
        if(trim($title)){
            $wheres['qtitle'] = ['like','%'.$title.'%'];
        }
        $list = Db::name('ask')->where($wheres)->paginate(4,false,['query' => request()->param()]);
        $this->assign('list',$list);
        $this->assign('name',$name);
        $this->assign('title',$title);
        $this->assign('id',$id);
        $arr = Db::name('forum_onetype')->field('id,name')->select();
        $typearr = array_column($arr,'name','id');
        $this->assign('typearr',$typearr);
        return $this->fetch();
    }

    public function dotransferplate(){
        $fid    = input('param.fid');
        $id             = input('post.id/a');
        $res = Db::name('ask')->where('id','in',$id)->update([
            'fid'=>$fid,
        ]);
        if($res){
            $this->success('转移成功');
        }else{
            $this->error('转移失败');
        }
    }

    public function classadd(){
        $request=request();
        if($request->method()=='POST'){
            $data=input('post.');
            $datas = [
                'hc_name'=>$data['hc_name'],
                'hc_articlesum'=>0,
                'hc_pid'=>isset($data['hc_pid'])?$data['hc_pid']:0,
                'hc_order'=>$data['hc_order']
            ];
            if(Db::name('headlineclass')->insert($datas)){
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

    public function classalter(){
        $request=request();
        if($request->method()=='POST'){
            $data=input('post.');
            $id=input('param.id');
            $datas = [
                'hc_name'=>$data['hc_name'],
                'hc_order'=>$data['hc_order']
            ];
            if(Db::name('headlineclass')->where(['hc_id'=>$id])->update($datas)){
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
            $info = Db::name('headlineclass')->find($id);
            $this->assign('info',$info);
            return $this->fetch();
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