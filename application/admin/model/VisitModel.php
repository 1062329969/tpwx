<?php


namespace app\admin\model;


use think\Model;

class VisitModel extends Model
{
	protected $name='visit';
	protected $pk='id';
	protected $autoWriteTimestamp=true;

	public static function getAll($page=15,$searchValue="")
	{
		$where['del']=0;
		$where['type']=0;

		if(!empty($searchValue))
		{
            $where['code|name|tel']=['like','%'.$searchValue.'%'];
		}

		return self::where($where)->order('sort desc,id desc')->paginate($page);
	}

}