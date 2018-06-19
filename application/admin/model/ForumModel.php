<?php
/**
 * Created by aibx.net.
 * User: wafu7969
 * Date: 2017/7/12
 * Time: 15:34
 */

namespace app\admin\model;


use think\Model;

class ForumModel extends Model
{
	protected $pk = 'id';
	protected $name='forum';
	protected $autoWriteTimestamp=true;


	public static function getAll($page=15)
	{
		return self::where(['del'=>0])->order('id desc')->paginate($page);
	}


    public static function getone($id,$page=15)
    {
        return self::where(['id'=>$id])->order('id desc')->paginate($page);
    }

}