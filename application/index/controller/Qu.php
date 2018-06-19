<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 问答管理控制器
 */

namespace app\index\controller;

class Qu extends Wap
{
	protected function _initialize()
	{
	}

	//问答首页
	public function i(){
        $q = input('param.q');
        $this->redirect(url('questionnaire/index',['q_id'=>$q]));
	}

}