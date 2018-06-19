<?php

namespace app\index\model;


use think\Model;

class HospitalModel extends Model
{
	protected $name='Hospital';
	protected $pk='id';

	public static function getOne($id)
	{
		return self::where(['status'=>1,'del'=>0,'id'=>$id])->find();
	}

	public static function getAll()
	{
		return self::where(['status'=>1,'del'=>0])->select();
	}

	public static function getRidHos($rid)
	{
		return self::where(['status'=>1,'del'=>0,'rid'=>$rid])->find();
	}
	/*
	 * 下拉刷新
	 *$page   //一次查询的条数
	 *$offsize // 默认从0 开始查询
	 * */
	public static function getDropDown( $offsize = 0 , $page = 10){
	    return self::where(['status' => 1 , 'del' => 0]) -> limit( $offsize , $page ) -> select();
    }
    /*
	 * 下拉刷新
	 *$page   //一次查询的条数
	 *$offsize // 默认从0 开始查询
	 * */
    public static function getDropDown1( $offsize = 0 , $page = 10){
        //-> limit( $offsize , $page )
        return collection(self::where(['status' => 1 , 'del' => 0]) -> select()) -> toArray();
    }

    /*
     * 模糊查询检索医院
     *$str   // 模糊提交的字符串
     * return array(id)
     * */
    public static function getLikeSelect($str){
        if(!empty($str)) return collection(self:: where(["hosname" => ['like','%'.$str.'%']]) -> field("id") -> select()) -> toArray();
    }

    /*
     * 模糊查询检索医院
     *$str   // 模糊提交的字符串
     * return array
     * */
    public static function getHosSelect($str){
        if(!empty($str)) return collection(self:: where(["hosname" => ['like','%'.$str.'%'] , 'status' => 1 , 'del' => 0]) -> field("id,hosname,pic,address") -> select()) -> toArray();
    }



}