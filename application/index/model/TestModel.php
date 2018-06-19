<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/21
 * Time: 0:03
 */

namespace app\index\model;


use think\Model;

class TestModel extends Model
{
    protected $name='test';

    public static function insertTest($data)
    {
        return self::save(['str'=>$data]);
    }

}