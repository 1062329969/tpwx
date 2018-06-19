<?php
/**
 * User: wafu7969
 * Date: 2017/7/25
 * Time: 16:29
 */

namespace app\index\model;


use think\Model;

class PanoramaModel extends Model
{
	protected $name='panorama';
	protected $pk='id';

	public static function getAll()
	{
		return self::where(['status'=>1,'del'=>0])->order('sort desc,id desc')->select();
	}

}