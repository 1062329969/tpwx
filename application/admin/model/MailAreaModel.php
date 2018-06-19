<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/30
 * Time: 11:38
 */

namespace app\admin\model;


use think\Model;

class MailAreaModel extends Model
{
    protected $name='mail_area';
    protected $pk='id';
    protected $autoWriteTimestamp=true;

    public static function getAll($mid)
    {
        return self::where(['mid'=>$mid,'del'=>0])->select();
    }

}