<?php
/**
 * Created by aibx.net.
 * User: wafu7969
 * Date: 2017/7/12
 * Time: 9:31
 */

namespace app\admin\model;


use think\Model;

class HospitalModel extends Model
{
	protected $name='hospital';
	protected $pk='id';
	protected $autoWriteTimestamp=true;

	//获得所有的医院
	public static function getAll($page=15)
	{
		return self::where(['status'=>1,'del'=>0])->order('id desc')->paginate($page);
	}

	//关联地区分类
	public function region()
	{
		return $this->hasOne('HospitalRegionModel','id','rid')->field('region');
	}

}