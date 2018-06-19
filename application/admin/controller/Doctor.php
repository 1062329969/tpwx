<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 专家管理控制器
 */
namespace app\admin\controller;
use app\admin\model\DoctorModel;
use app\admin\model\HospitalModel;
use think\Db;
class Doctor extends Admin
{
    protected $modelName='';

    public function _initialize()
    {
        parent::_initialize();
        $this->modelName=new DoctorModel;
    }

    public function index()
    {
        $content=$this->modelName->getAll(15);
        $this->assign('content',$content);
        return $this->fetch();
    }

    public function add()
    {
        $request=request();
        if($request->method()=='POST')
        {
            $data=input('post.');

            $result=$this->validate($data,'ValidateClass.CK8');
            if(true !== $result)
            {
                // 验证失败 输出错误信息
                $this->error($result);
            }
            $val = Db::name('doctor')->where(['docname'=>$data['docname']])->find();
            if($val){
                $this->error('姓名重复',url('index'));
            }
            //上传图片
            $pic=$this->upload($request,'pic');
            if(!empty($pic))
            {
                $data['pic']=$pic;
            }else{
                $this->error('请上传头像',url('index'));
            }

            //上传图片////各地分院显示
            $yardPic=$this->upload($request,'yard_pic');
            if(!empty($yardPic))
            {
                $data['yard_pic']=$yardPic;
            }else{
                $this->error('请上传头像',url('index'));
            }

            //医生是否隐藏
            if(!empty($data['show'])) {
                $data['status'] = $data['show'];
            }
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
            //读取所有医院
            $hospital=HospitalModel::getAll(50);
            $this->assign('hospital',$hospital);
            return $this->fetch();
        }
    }

    public function alter()
    {

        $request=request();
        if($request->method()=='POST')
        {
            $data=input('post.');

            $result=$this->validate($data,'ValidateClass.CK8');
            if(true !== $result)
            {
                // 验证失败 输出错误信息
                $this->error($result);
            }
            $val = Db::name('doctor')
                ->where(['docname'=>$data['docname']])
                ->where('id','<>',$data['id'])
                ->find();
            if($val){
                $this->error('姓名重复',url('index'));
            }
            //上传图片
            $pic=$this->upload($request,'pic');
            if(!empty($pic))
            {
                $data['pic']=$pic;
            }
            else
            {
                unset($data['pic']);
            }

            //上传图片////各地分院显示
            $yardPic=$this->upload($request,'yard_pic');
            if(!empty($yardPic))
            {
                $data['yard_pic']=$yardPic;
            }


            //医生是否隐藏
            if(!empty($data['show'])) {
                $data['status'] = $data['show'];
            }

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

            //读取所有医院
            $hospital=HospitalModel::getAll(50);
            $this->assign('hospital',$hospital);

            $content=$this->modelName->get($id);
            $this->assign('content',$content);
            return $this->fetch();
        }

    }


    public function del()
    {

        $id=input('param.id');
        DoctorModel::where(['id'=>$id])->delete();
        return ['code'=>1];
    }

}