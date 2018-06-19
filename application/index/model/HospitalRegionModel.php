<?php


namespace app\index\model;


use think\Model;

class HospitalRegionModel extends Model
{
	protected $name='hospital_region';
	protected $pk='id';

	public static function getAll()
	{
        return self::all();
	}

}