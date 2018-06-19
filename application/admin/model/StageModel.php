<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * Time: 23:17
 */

namespace app\admin\model;


use think\Model;

class StageModel extends Model
{

    protected $pk = 's_id';
    protected $name='stage';
    protected $autoWriteTimestamp=true;


    public static function getAll()
    {
        return self::order('s_id asc')->select();
    }

}