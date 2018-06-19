<?php

namespace app\index\model;
use think\Model;

class HairCuringImgModel extends Model
{
    protected $name='hair_curing_imgs';
    protected $pk='id';

    public static function getCountImgs($aid)
    {
        return self::where(array('aid'=>$aid))->count();
    }

    public static function getAll($aid)
    {
        return self::where(['aid'=>$aid])->select();
    }

}