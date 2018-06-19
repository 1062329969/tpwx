<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 注册协议
 */

namespace app\admin\controller;


use app\admin\model\ProtocolModel;

class Protocol extends Admin
{
    public function _initialize()
    {
        parent::_initialize();
        $this->modelName=new ProtocolModel();
    }

    public function index()
    {

        $request=request();
        if($request->method()=='POST')
        {
            $data=input('param.');
            $this->modelName->allowField(true)->where('id',1)->update($data);

            $this->success('添加成功',url('index'));
        }
        else
        {
            $content=$this->modelName->find(1);
            $this->assign('content',$content);
            return $this->fetch();
        }

    }

}