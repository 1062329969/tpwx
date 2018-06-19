<?php
/**
 * User: wafu7969
 * Date: 2017/7/25
 * Time: 18:37
 */

namespace app\admin\model;


use think\Model;

class BespokeModel extends Model
{
	protected $name='bespoke';
	protected $pk='id';

	public static function getAll($page=15)
	{
		return self::where(['status'=>1,'del'=>0])->order('id desc')->paginate($page);
	}
}