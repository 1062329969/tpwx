<?php

namespace app\index\model;
use think\Model;
use think\Db;

//自助答题
class AnswerModel_tow extends Model{
    protected $name='quiz';
    protected $pk='id';
    protected $autoWriteTimestamp=true;

    public static function getAll()
	{

		return  Db::table('quiz')->field('*')->order('id desc')->select();
	}


    public static function detail(){

        return Db::table('quiz')->order('id asc')->select();

    }
}

?>