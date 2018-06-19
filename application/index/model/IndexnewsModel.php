<?php

namespace app\index\model;


use think\Model;

class IndexnewsModel extends Model
{
    protected $name = 'indexnews';
    protected $pk = 'id';


    public static function getAll($n_type,$page=10,$offsize=0)
    {
        return self::where(['n_type'=>$n_type])->order('n_id desc')->limit($offsize,$page)->select();
    }

    //术前
    public static function getAll_one($n_type,$offsize=10,$page=0)
    {
        return self::where(['n_type'=>$n_type])->order('n_addtime desc')->limit($offsize,$page)->select();
    }

    //关联用户表
    public function userInfo()
    {
        return $this->hasOne('UserInfoModel','id','n_id')->field('id,portrait,wechaname,city');
    }
}
