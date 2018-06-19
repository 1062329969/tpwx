<?php

namespace app\index\model;


use think\Db;
use think\Model;

class DepartmenthospitalModel extends Model
{
    protected $name='departmenthospital';
    protected $pk='hd_id';

    public function getall(){
        return Db::name('departmenthospital')->where(['hd_state'=>1])->select();
    }
}