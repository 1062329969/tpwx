<?php

namespace app\admin\model;


use think\Model;

class DoctorModel extends Model
{
	protected $pk = 'id';
	protected $name='doctor';
	protected $autoWriteTimestamp=true;



	public static function getAll($page=15)
	{
		return self::where(['del'=>0])->order('sort desc,id asc')->paginate($page);
	}


    //关联医院表
	public function hospital()
	{
		return $this->hasOne('HospitalModel','id','hid')->field('hosname');
	}

}