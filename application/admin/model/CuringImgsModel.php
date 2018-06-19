<?php


namespace app\admin\model;
use think\Model;

class CuringImgsModel extends Model
{
    protected $name='curing_imgs';
    protected $pk='';
    protected $autoWriteTimestamp=true;

    public static function getAll($aid)
    {
        return self::where(['aid'=>$aid])->select();
    }
}