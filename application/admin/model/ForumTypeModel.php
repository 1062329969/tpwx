<?php

namespace app\admin\model;
use think\Model;

class ForumTypeModel extends Model
{
    protected $pk = 'id';
    protected $name='forum_type';
    protected $autoWriteTimestamp=true;


    public static function getAll($page=15)
    {
        return self::where(['status'=>1,'del'=>0])->order('sort desc,id asc')->paginate($page);
    }

    //发布帖子的分类
    public static function getContType()
    {
        return self::where(['status'=>1,'del'=>0])->select();
    }

    protected function getStatusAttr($value)
    {
        if($value==0)
        {
            return '停用';
        }
        elseif($value==1)
        {
            return '正常';
        }
    }

}