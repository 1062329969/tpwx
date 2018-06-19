<?php

namespace app\index\model;


use think\Model;

class ForumModel extends Model
{
	protected $name='forum';
	protected $pk='id';
	protected $autoWriteTimestamp=true;

	public static function getAll($tid=0,$page=5,$offsize=0,$ppid=0)
	{
	    $arr = array('del'=>0,'status'=>1);
        $orders = "";
        if($ppid ==1){
            $orders= 'click desc';
        }elseif ($ppid == 2){
            $orders= 'create_time desc';
        }elseif ($ppid == 3){
            $orders= 'update_time desc';
        }

		if($tid==0)
		{
			return self::where($arr)->order($orders)->limit($offsize,$page)->select();
		}
		else
		{
            $arr['tid'] =$tid;
            return self::where($arr)->order($orders)->limit($offsize,$page)->select();
		}

	}

	//论坛
    public static function casesi($id_id){
	    return self::where(['tid'=>$id_id])->select();
    }


    //关注
	public static function get_onon($tid=0,$page=15,$offsize=0,$pppid=0){
        $arr = array('del'=>0,'status'=>1);
        $orders = 'id desc';
        if($pppid ==1){
            $orders.= ' ,follow desc';
        }elseif($pppid==2){
            $orders.= ' ,comment desc';
        }elseif ($pppid ==3){
            $orders.=' ,praise desc';
        }elseif ($pppid ==4){
            $orders.=" ,shoucang desc";
        }
        if($tid==0)
        {
            return self::where($arr)->order($orders)->limit($offsize,$page)->select();
        }
        else
        {
            $arr['tid'] =$tid;
            return self::where($arr)->order($orders)->limit($offsize,$page)->select();
        }
    }


	//导航
    public static function get_Navigation($tid=0,$page=15,$offsize=0)
    {
        if($tid==0)
        {
            return self::where(['del'=>0,'status'=>1])->order('comment descc')->limit($offsize,$page)->select();
        }
        else
        {
            return self::where(['del'=>0,'status'=>1,'tid'=>$tid])->order('comment desc')->limit($offsize,$page)->select();
        }

    }

    public static function getAl_one($tid=0,$page=15,$offsize=0)
    {
        if($tid==0)
        {
            return self::where(['del'=>0,'status'=>1])->order('create_time desc')->limit('0,5')->select();
        }
        else
        {
            return self::where(['del'=>0,'status'=>1,'tid'=>$tid])->order('create_time desc')->limit('0,5')->select();
        }

    }

    public static function getAll_one($tid=0,$page=15,$offsize=0)
    {
        if($tid==0)
        {
            return self::where(['del'=>0,'status'=>1])->order('sort desc,id desc')->limit($offsize,$page)->select();
        }
    }

	public static function search($search='',$page=15,$offsize=0)
	{
		$where['status']=1;
		$where['del']=0;
		if(!empty($search))
		{
			$where['foname']=['like','%'.$search.'%'];
		}
		return self::where($where)->order('sort desc,id desc')->limit($offsize,$page)->select();


	}

	//返回推荐的帖子
	public static function getTj($tid=0)
	{
		if($tid==0)
		{
			return self::where(['del'=>0,'status'=>1,'tj'=>1])->order('sort desc,id desc')->limit(0,5)->select();
		}
		else
		{
			return self::where(['del'=>0,'status'=>1,'tj'=>1,'tid'=>$tid])->order('sort desc,id desc')->limit(0,5)->select();
		}

	}

	public static function getOne($ids)
	{
		return self::where(['del'=>0,'status'=>1,'id'=>$ids])->find();
	}

	public function getCreateTimeAttr($value)
	{
		return date('Y-m-d',$value);
	}

	//返回帖子栏目下的文章数
	public static function getCount($tid)
	{
		return self::where(['del'=>0,'status'=>1,'tid'=>$tid])->count();
	}

	public function getKeywordAttr($value)
	{
		return explode(',',str_replace('，',',',$value));
	}

    public function userInfo()
    {
        return $this->hasOne('UserInfoModel','id','uid')->field('id,portrait,wechaname,city');
    }



    //关联用户表
    public function forum_one()
    {
        return $this->hasOne('ForumTypeModel','id','tid')->field('typename');
    }


	//关联新闻图片表 一对多
	public function forumImgs()
	{
		return parent::hasMany('ForumImgsModel', 'fid')->field('pic');
	}



}