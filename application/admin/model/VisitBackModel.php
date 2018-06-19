<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * Time: 23:59
 */

namespace app\admin\model;


use think\Model;

class VisitBackModel extends Model
{
    protected $name="visit_back";
    protected $id='id';
    protected $autoWriteTimestamp=true;


    public static function getAll($page=15,$vid=0)
    {
        $where['del']=0;
        if($vid!=0)
        {
            $where['vid']=$vid;
        }
        return self::where($where)->order('id desc')->paginate($page);
    }

}