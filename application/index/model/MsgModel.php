<?php

namespace app\index\model;
use think\Model;

class MsgModel extends Model
{
    protected $name='msg';
    protected $pk='id';
    protected $autoWriteTimestamp=true;

    public static function getAll($uid,$type=0)
    {
        //账号信息$type==1
        //服药提醒$type==2
        //优惠推荐$type==3
        //订单信息$type==4
        //医师问答$type==5
        //我的社区$type==6
        return self::where("(uid=$uid or is_global=1) and status=1 and type=$type")->order('id desc')->select();

    }

    //未读消息数量
    public static function getNoRead($uid,$type=0)
    {
        if($type!=0)
        {
            $typeStr="and type=$type";
        }
        else
        {
            $typeStr="";
        }
        //
        // or
        //is_global=1
        return self::where("( uid=$uid ) and status=1 ".$typeStr."  and is_read=0" )->count();
    }

}