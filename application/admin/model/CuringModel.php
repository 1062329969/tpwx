<?php


namespace app\admin\model;
use think\Model;


class CuringModel extends Model
{
    protected $name='curing';
    protected $pk='id';

    protected $autoWriteTimestamp=true;


    public static function getAll($page=15)
    {
        return self::where(['del'=>0])->order('id desc')->paginate($page);
    }

    public static function getAll_tow($page=15)
    {
        return self::where(['del'=>0])->order('id desc')->paginate($page);
    }


}