<?php

namespace app\admin\model;


use think\Model;

class VideoModel extends Model
{
	protected $pk = 'id';
	protected $name='video';
	protected $autoWriteTimestamp=true;

	public static function getAll($page=15)
	{
		return self::where(['del'=>0,'status'=>1])->order('id desc')->paginate($page);
	}




}