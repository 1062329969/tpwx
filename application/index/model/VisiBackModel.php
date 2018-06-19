<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * Time: 23:59
 */

namespace app\index\model;


use think\Model;

class VisiBackModel extends Model
{
    protected $name="visit_back";
    protected $id='id';
    protected $autoWriteTimestamp=true;


    public static function getAll($vid)
    {
        return self::where(['del'=>0,'vid'=>$vid,'status'=>1])->order('id desc')->select();
    }

}