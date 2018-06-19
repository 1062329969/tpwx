<?php

namespace app\admin\model;


use think\Model;

class MailTempModel extends Model
{
    protected $name='mail_temp';
    protected $pk='id';
    protected $autoWriteTimestamp=true;

    public static function getAll($page=15)
    {
        return self::where(['del'=>0])->order('id desc')->paginate($page);
    }

}