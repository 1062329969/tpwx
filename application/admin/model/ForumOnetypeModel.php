<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * Time: 23:17
 */

namespace app\admin\model;


use think\Model;

class ForumOnetypeModel extends Model
{

    protected $pk = 'id';
    protected $name='forum_onetype';

    //发布帖子的分类
    public static function getAll()
    {
         return self::order('id desc')->select();
    }

}