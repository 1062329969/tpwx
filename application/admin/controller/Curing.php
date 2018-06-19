<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 养护管理控制器
 */

namespace app\admin\controller;

use app\admin\model\CuringModel;
use app\admin\model\CuringImgsModel;

class Curing extends Admin
{
    protected $modelName='';

    public function _initialize()
    {
        parent::_initialize();
        $this->modelName=new CuringModel;
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

            $result=$this->validate($data,'ValidateClass.CK3');
            if(true !== $result)
            {
                // 验证失败 输出错误信息
                $this->error($result);
            }

            //多文件上传
            if(empty($data['default_img']))
            {
                $data['default_img']=0;
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

            if($this->modelName->allowField(true)->save($data))
            {
                //保存多图
                if(count($imgs)>0)
                {
                    foreach($imgs as $k=>$v)
                    {
                        $CuringImgsModel=new CuringImgsModel;
                        $CuringImgsModel->save(['aid'=>$this->modelName->id,'pic'=>$v]);
                    }
                }

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

        $request=request();
        if($request->method()=='POST')
        {
            $data=input('post.');

            $result=$this->validate($data,'ValidateClass.CK3');
            if(true !== $result)
            {
                // 验证失败 输出错误信息
                $this->error($result);
            }

            //默认图片上传
            //上传缩略图图片
            $pic=$this->upload($request,'smallimg');
            if(!empty($pic))
            {
                $data['smallimg']=$pic;
            }
            else
            {
                unset($data['smallimg']);
            }


            //多文件上传
            $imgs=array();
            $imgs=$this->uploadMore($request,'pic2');


            if($this->modelName->allowField(true)->save($data,['id'=>$data['id']]))
            {
                //保存多图
                if(count($imgs)>0)
                {
                    foreach($imgs as $k=>$v)
                    {
                        $CuringImgsModel=new CuringImgsModel;
                        $CuringImgsModel->save(['aid'=>$this->modelName->id,'pic'=>$v]);
                    }
                }

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

            //读取该养护的所有图片
            $newsImgs=CuringImgsModel::getAll($id);
            $this->assign('newsImgs',$newsImgs);

            $content=$this->modelName->get($id);
//            var_dump($content);die;
            $this->assign('content',$content);
            return $this->fetch();
        }

    }


    //删除养护图片
    public function delImg()
    {
        $imgurl=input('post.imgurl');
        $id=input('post.id');
        $result = @unlink ('.'.$imgurl);
        CuringImgsModel::where(['id'=>$id,'pic'=>$imgurl])->delete();
        return ['code'=>1];


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



