<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * Time: 23:59
 */

namespace app\admin\model;


use think\Model;

class VisiBackModel extends Model
{
    protected $name="visi_back";
    protected $id='id';
    protected $autoWriteTimestamp=true;


    public static function getAll($page=15)
    {
        return self::where(['del'=>0])->order('id desc')->paginate($page);
    }

}