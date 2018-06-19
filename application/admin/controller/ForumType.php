<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 圈子分类控制器
 */

namespace app\admin\controller;

use app\index\model\ForumOnetypeModel;
use think\Controller;
use app\admin\model\ForumTypeModel;
use think\Db;

class ForumType extends Admin
{

    protected $modelName='';

    public function _initialize()
    {
        parent::_initialize();
        $this->modelName=new ForumTypeModel;
    }

    public function index()
    {

        $cont_type=$this->modelName->getAll(15);
        $this->assign('cont_type',$cont_type);
        return $this->fetch();
    }

    public function add()
    {
        $one = Db::name('forum_onetype')->select();
        $this->assign('one',$one);
        $request=request();
        if($request->method()=='POST')
        {

            $data=input('post.');
//            var_dump($data);die;
            $result=$this->validate($data,'ValidateClass.CK1');
            if(true !== $result)
            {
                // 验证失败 输出错误信息
                $this->error($result);
            }

            //上传图片
            $pic=$this->upload($request,'pic');
            if(!empty($pic))
            {
                $data['pic']=$pic;
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
            return $this->fetch();
        }
    }

    public function alter()
    {
        $one = Db::name('forum_onetype')->select();
        $this->assign('one',$one);
        $request=request();

        if($request->method()=='POST')
        {
            $data=input('post.');
//            var_dump($data);die;
            $result=$this->validate($data,'ValidateClass.CK2');
            if(true !== $result)
            {
                // 验证失败 输出错误信息
                $this->error($result);
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
            $cont_type=$this->modelName->get($id);
            $this->assign('cont_type',$cont_type);
            return $this->fetch();
        }

    }


    public function del()
    {

        $id=input('param.id');
        if($this->modelName->save(['del'=>1],['id'=>$id]))
        {
            echo '{"code":"1"}';
        }
        else
        {
            echo '{"code":"0"}';
        }
    }


}