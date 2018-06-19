<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 免费检测控制器 第一版的 第二版没有用
 */

namespace app\index\controller;

use app\index\model\BespokeModel;

class Bespoke extends Wap
{
	public function index()
	{
		return $this->fetch();
	}
    //11111111122222
	public function add()
	{
		$request=request();
		if($request->method()=='POST')
		{

			$data = input('post.');

			$result=$this->validate($data,'ValidateClass.CK19');
			if(true !== $result)
			{
				// 验证失败 输出错误信息
				$this->error($result);
			}

			$bespoke = new BespokeModel;

			if ($bespoke->allowField(true)->save($data)) {
				return ['code' => 1];
			} else {
				return ['code' => 2, 'msg' => '提交失败'];
			}
		}
	}

}