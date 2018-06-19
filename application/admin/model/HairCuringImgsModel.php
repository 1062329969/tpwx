<?php


namespace app\admin\model;
use think\Model;

class HairCuringImgsModel extends Model
{
    protected $name='hair_curing_imgs';
    protected $pk='';
    protected $autoWriteTimestamp=true;

    public static function getAll($aid)
    {
        return self::where(['aid'=>$aid])->select();
    }
}