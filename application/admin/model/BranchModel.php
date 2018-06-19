<?php

namespace app\admin\model;
use think\Model;

class BranchModel extends Model
{
    protected $pk = 'id';
    protected $name='branch';
    protected $autoWriteTimestamp=true;


    public static function getAll($page=15)
    {
        return self::where(['del'=>0])->order('id desc')->paginate($page);
    }

}