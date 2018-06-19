<?php


namespace app\index\model;


use think\Model;

class VideoModel extends Model
{
	protected $name='video';
	protected $pk='id';

	public static function getAll($tid,$page=10,$offsize=0)
	{
		$where['del']=0;
		$where['status']=1;
		if($tid!=0)
		{
			$where['pid']=$tid;
		}

		return self::where($where)->order('sort desc,id desc')->limit($offsize,$page)->select();

	}

    public static function get_one($id)
    {
        if($id!=0){
            $where['id']=$id;
        }
        return self::where($where)->select();

    }



    public static function search($search='',$page=10,$offsize=0)
	{
		$where['del']=0;
		$where['status']=1;
		if(!empty($search))
		{
			$where['title']=['like','%'.$search.'%'];
		}

		return self::where($where)->order('id desc')->limit($offsize,$page)->select();
	}

	public static function getOne($id)
	{
		return self::where(['del'=>0,'status'=>1,'id'=>$id])->find();
	}

	//关联用户表
	public function userInfo()
	{
		return $this->hasOne('UserInfoModel','id','uid')->field('id,portrait,wechaname,city');
	}

}