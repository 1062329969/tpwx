<?php

namespace app\admin\model;


use think\Db;
use think\Model;

class ExerciseModel extends Model
{
	protected $name='exercise';
	protected $pk='e_id';

	public static function getall()
	{
        return Db::name('exercise')->order('e_id desc')->select();
	}

	public function getcheckall(){
        return Db::name('exercise')->where(['e_state'=>1])->order('e_id desc')->select();
    }
}