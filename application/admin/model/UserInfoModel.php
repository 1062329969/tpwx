<?php

namespace app\admin\model;


use think\Model;

class UserInfoModel extends Model
{
     protected $name='userInfo';
	 protected $pk='id';


	 //获得所有的会员
	 public static function getAll($page=15)
	 {
		 return self::order('id desc')->paginate($page);
	 }


    public static function getS($type,$page=15)
    {
        return self::where(['type'=>$type])->order('id desc')->paginate($page);
    }

	

}