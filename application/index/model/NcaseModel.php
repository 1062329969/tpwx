<?php

namespace app\index\model;


use think\Model;

class NcaseModel extends Model
{
	protected $name='ncase';
	protected $pk='id';


	public static function getAll($tid=0,$page=10,$offsize=0)
	{
		$where['del']=0;
		$where['status']=1;
		if($tid!=0)
		{
			$where['tid']=$tid;

		}
		return self::where($where)->order('sort desc,id desc')->limit($offsize,$page)->select();
	}

	public static function search($search='',$page=10,$offsize=0)
	{
		$where['status']=1;
		$where['del']=0;
		if(!empty($search))
		{
			$where['title']=['like','%'.$search.'%'];
		}
		return self::where($where)->order('sort desc,id desc')->limit($offsize,$page)->select();
	}

	public static function getTj()
	{
		$where['del']=0;
		$where['status']=1;
		$where['tj']=1;

		return self::where($where)->order('sort desc,id desc')->limit(0,5)->select();
	}


	public function getCreateTimeAttr($value)
	{
		return date('Y-m-d',$value);
	}

	public function getKeywordAttr($value)
	{
		return explode(',',str_replace('，',',',$value));
	}

	public static function getOne($id)
	{
		return self::where(['del'=>0,'status'=>1,'id'=>$id])->find();
	}

	//关联新闻图片表 一对多
	public function newsImgs()
	{
		return parent::hasMany('NcaseImgsModel', 'aid')->field('pic');
	}

}