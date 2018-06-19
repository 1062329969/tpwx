<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 养护知识
 */
namespace app\index\controller;

use think\Db;

class Indexnews extends Wap
{

	protected function _initialize()
	{
		//获得授权
		$this->getUserInfo();
		$this->assign('fansInfo',$this->fansInfo);
	}

	//测试首页
    public function index(){
        $id=input('param.id');
        $info = Db::name('indexnews')->find($id);
        if(!$info){
            $this->error('未找到文章');
        }else{
            $this->assign('news',$info);
            return $this->fetch();
        }
    }
}