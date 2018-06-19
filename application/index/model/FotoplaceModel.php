<?php

namespace app\index\model;


use think\Model;

class FotoplaceModel extends Model
{
	protected $name='fotoplace';
	protected $pk='id';
	protected $autoWriteTimestamp=true;

	public static function getCount($uid)
	{
		$count=self::where(['uid'=>$uid])->count();
		if($count<20)
		{
            return $count;
		}
		else
		{
            return 20;
		}
	}

	public static function getAll($uid)
	{
		return self::where(['uid'=>$uid])->order('create_time desc')->limit(0,20)->select();
	}

	//关联养护
	public function curing()
	{
		return $this->hasOne('CuringModel','id','aid')->field('id,title');
	}
	//关联头发养护知识
	public function hairCuring()
	{
		return $this->hasOne('HairCuringModel','id','aid')->field('id,title');
	}
	//关联新闻
	public function news()
	{
		return $this->hasOne('NewsModel','id','aid')->field('id,title');
	}
	//关联帖子
	public function forum()
	{
		return $this->hasOne('ForumModel','id','aid')->field('id,foname');
	}
	//关联问答
	public function ask()
	{
		return $this->hasOne('AskModel','id','aid')->field('id,qtitle');
	}

	//关联商品
	public function goods()
	{
		return $this->hasOne('GoodsModel','id','aid')->field('id,gdname');
	}

}