<?php


namespace app\index\model;


use think\Model;

class CollectionModel extends Model
{
	protected $name='collection';
	protected $pk='id';

	public static function getCount($uid)
	{
		return self::where(['uid'=>$uid])->count();
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
	//关联医生表
	public function doctor()
	{
		return $this->hasOne('DoctorModel','id','aid')->field('id,docname');
	}

	//关联案例
	public function cases()
	{
		return $this->hasOne('CasesModel','id','aid')->field('id,info');
	}

	//关联视频
	public function video()
	{
		return $this->hasOne('VideoModel','id','aid')->field('id,title');
	}

	//关联内部案例
	public function ncase()
	{
		return $this->hasOne('NcaseModel','id','aid')->field('id,title');
	}


}