<?php


namespace app\index\model;

use think\Db;
use think\Model;

class InformationModel extends Model
{
    protected $name = 'information';
    protected $pk = 'i_id';

    public static function getAll($i_id,$offsize,$page)
    {
        if (!empty($i_id)) {
            $where['i_uid'] = $i_id;
        }

        return self::where($where)->order('i_uid desc')->limit($offsize, $page)->select();

    }
}