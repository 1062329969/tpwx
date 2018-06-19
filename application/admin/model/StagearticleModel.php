<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * Time: 23:17
 */

namespace app\admin\model;


use think\Model;

class StagearticleModel extends Model
{

    protected $pk = 'a_id';
    protected $name='stagearticle';
    protected $autoWriteTimestamp=true;


    public static function getAll($id)
    {
        return self::where(['a_sid'=>$id])->order('a_order desc,a_id desc')->select();
    }

}