<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 360全景 第一版的  第二版没有用
 */

namespace app\index\controller;


use app\index\model\PanoramaModel;

class Panorama extends Wap
{
	public function index()
	{

		$panorama=PanoramaModel::getAll();
		$this->assign('panorama',$panorama);

		return $this->fetch();
	}


	public function detail()
	{
        $id=input('param.id');
		$panorama=PanoramaModel::get($id);
		$this->assign('panorama',$panorama);

		return $this->fetch();
	}

}