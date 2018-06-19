<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/30
 * Time: 15:31
 */

namespace app\admin\model;


use think\Model;

class StaffModel extends Model
{
    protected $name='staff';
    protected $pk='id';
    protected $autoWriteTimestamp=true;

    public static function getAll($page=15)
    {
        return self::order('id desc')->paginate($page);
    }

}