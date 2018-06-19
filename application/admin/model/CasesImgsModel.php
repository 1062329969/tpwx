<?php
/**
 * Created by aibx.net.
 * User: wafu7969
 * Date: 2017/7/19
 * Time: 12:20
 */

namespace app\admin\model;


use think\Model;

class CasesImgsModel extends Model
{
	protected $name='cases_imgs';
	protected $pk='id';
	protected $autoWriteTimestamp=true;

	public static function getAll($cid)
	{
		return self::where(['cid'=>$cid])->select();
	}

}