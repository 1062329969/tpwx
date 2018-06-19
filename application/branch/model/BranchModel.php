<?php


namespace app\branch\model;

use think\Model;

class BranchModel extends \think\Model
{
    protected $name='branch';
    protected $pk='id';

    public static function getOne($id)
    {
        return self::where(['id'=>$id])->find();
    }

}