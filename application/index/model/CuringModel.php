<?php


namespace app\index\model;
use think\Model;

class CuringModel extends Model
{
    protected $name='curing';
    protected $pk='id';


    public static function getAll($tid=0,$vid=0,$page=10,$offsize=0)
    {

        $where['del']=0;
        $where['status']=1;
        $where['tid']=$tid;


        //图文
        if($vid==1)
        {
            $where['is_video']=0;
        }

     return self:: where($where)->order('sort desc,id desc')->limit($offsize,$page)->select();


    }
    //首页养护知识推荐
    public static function getAll_tow($tid=0,$vid=0,$page=10,$offsize=0)
    {
        $where['del']=0;
        $where['status']=1;

        if(!empty($tid))
        {
            $where['tid']=$tid;
        }

        //图文
        if($vid==1)
        {
            $where['is_video']=0;
        }
        //视频
        elseif($vid==2)
        {
            $where['is_video']=1;
        }

        return self::where($where)->order('sort desc,id desc')->limit('0,3')->select();

    }

	public static function search($search='',$page=10,$offsize=0)
	{
		$where['del']=0;
		$where['status']=1;
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

        return self::where($where)->order('sort desc,id desc')->limit(0,3)->select();
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
        return parent::hasMany('CuringImgModel', 'aid')->field('pic');
    }

}