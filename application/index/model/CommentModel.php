<?php


namespace app\index\model;


use think\Model;

class CommentModel extends Model
{
	protected $name='comment';
	protected $id='id';
	protected $autoWriteTimestamp=true;

	//获得某文章的评论
	public static function getAll($type,$aid,$page=20,$offsize=0)
	{
         return self::where(['type'=>$type,'aid'=>$aid,'del'=>0,'status'=>0,'plid'=>0])->limit($offsize,$page)->order('id desc')->select();

	}
	//获得某文章的回复评论
	public static function getAll2($type,$aid)
	{
            return self::where(['type'=>$type,'aid'=>$aid,'del'=>0,'status'=>0,'plid'=>['gt',0]])->select();
	}

	protected function getCreateTimeAttr($value)
	{
		return date('Y-m-d H:i:s',$value);
	}

	//关联用户表
	public function userInfo()
	{
		return $this->hasOne('UserInfoModel','id','uid')->field('id,portrait,wechaname');
	}


    //获得某用户的所有评论
	public static function getUid($uid)
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

}