<?php
/**
 * Created by aibx.net.
 * User: wafu7969
 * Date: 2017/7/12
 * Time: 10:05
 */

namespace app\admin\model;


use think\Model;

class HospitalRegionModel extends Model
{
	protected $name='hospital_region';
	protected $pk='id';
	protected $autoWriteTimestamp=true;


	public static function getAll()
	{
		return self::all();
	}

}