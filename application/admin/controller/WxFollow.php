<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * 关注回复内容
 */

namespace app\admin\controller;
use think\Db;

class WxFollow extends Admin
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $wx_follow=Db::name('wx_follow')->where('id',1)->find();
        $this->assign('wx_follow',$wx_follow);
        return $this->fetch();
    }

    public function alter()
    {
        $data=input('post.');
        $data['create_time']=time();
        Db::name('wx_follow')->update($data);
        $this->success('更新成功！');
    }
}