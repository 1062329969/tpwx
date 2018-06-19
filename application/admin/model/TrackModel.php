<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * Time: 23:17
 */

namespace app\admin\model;


use think\Model;

class TrackModel extends Model
{

    protected $pk = 'id';
    protected $name='track';
    protected $autoWriteTimestamp=true;


    public static function getAll($page=15)
    {
        return self::order('id desc')->paginate($page);
    }

}