<?php

namespace app\admin\model;


use think\Db;
use think\Model;

class IndextipsModel extends Model
{

    protected $name='index_tips';
    protected $pk='id';

    public static function getAll()
    {
        return Db::name('index_tips')->select();
    }

    public static function paeges($page=15)
    {
        return self::where(['del'=>0])->order('i_id desc')->paginate($page);
    }

}
