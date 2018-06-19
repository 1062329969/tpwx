<?php

namespace app\index\model;


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

    public function getcheckall($sex){
        return Db::name('exercise')->where(['e_state'=>1])->where('e_sex','in',[3,$sex])->order('e_id asc')->select();
    }
}