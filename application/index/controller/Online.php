<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 在线客服 没用
 */

namespace app\index\controller;


use think\Controller;

class Online extends Controller
{
	public function index()
	{
		return $this->fetch();
	}

}