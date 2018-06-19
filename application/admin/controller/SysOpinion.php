<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 系统反馈
 */
namespace app\admin\controller;
use think\Db;

class SysOpinion extends Admin
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
  {
      $sys_opinion=Db::name('sys_opinion')->field('a.id,a.text,a.lx,a.create_time,b.id as userid')->alias('a')->join('user_info b','a.wecha_id=b.wecha_id')->order('id desc')->paginate(15,true);
      $this->assign('sys_opinion',$sys_opinion);
      return $this->fetch();
  }

}