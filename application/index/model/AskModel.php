<?php

namespace app\index\model;


use think\Model;

class AskModel extends Model
{
	protected $name='ask';
	protected $pk='id';
	protected $autoWriteTimestamp=true;

	//protected static function init()
	//{
		//AskModel::event('after_write', function ($user) {
        //   TestModel::insert(['str'=>$user->getLastSql()]);
		//});

	//}


	public static function getAll($tid=0,$page=10,$offsize=0)
	{
        $where['del']=0;
		$where['status']=1;
        if($tid!=0)
        {
	        $where['pid']=$tid;
        }
		return self::where($where)->order('sort desc,id desc')->limit($offsize,$page)->select();
	}


    //
    public static function get_onon($tid=0,$page=15,$offsize=0,$pppid=0)
    {
        $arr = array('del' => 0, 'status' => 1);
        $orders = '';
        if ($pppid == 1) {
            $orders .= 'click desc';
        } elseif ($pppid == 2) {
            $orders .= ' ,comment desc';
        } elseif ($pppid == 3) {
            $orders .= ' ,create_time desc';

            if ($tid == 0) {
                return self::where($arr)->order($orders)->limit($offsize, $page)->select();
            } else {
                $arr['tid'] = $tid;
                return self::where($arr)->order($orders)->limit($offsize, $page)->select();
            }
        }
    }


    public static function getAll_one($tid=0,$page=10,$offsize=0,$ppid=0)
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


    //首页下拉加载
    public static function f_one($ppid,$page=6,$offsize=0)
    {
        $where['status']=1;
        $where['del']=0;
        $where['fid']=['neq',0];
        $where['bs'] = 1;
        $orders = "";

        if($ppid == 4){
            //推荐
            $where['recommend'] = 0;
            $orders = 'sort desc,recommend desc';
        }elseif ($ppid ==3){
            //热榜
            $orders = 'sort desc,click desc';
        }elseif ($ppid == 2){
            //精选
            $orders = 'sort desc,selecteds desc';
        }elseif($ppid == 1){
            //最新
            $orders = 'sort desc,create_time desc';
        }
        return self::where($where)->order($orders)->limit($offsize,$page)->select();
    }


    //大图
    public static function f_ones($ppid,$page=6,$offsize=0)

    {
        $where['status']=1;
        $where['del']=0;
        $where['fid']=['neq',0];
        $where['bs'] = 2;
        $orders = "";

        if($ppid == 4){
            //推荐
            $where['recommend'] = 0;
            $orders = 'sort desc,recommend desc';
        }elseif ($ppid ==3){
            //热榜
            $orders = 'sort desc,click desc';
        }elseif ($ppid == 2){
            //精选
            $orders = 'sort desc,selecteds desc';
        }elseif($ppid == 1){
            //最新
            $orders = 'sort desc,create_time';
        }
        return self::where($where)->order($orders)->limit($offsize,$page)->select();
    }

    //专题页 下拉加载
    public static function f_tow($tid,$ppid,$page=6,$offsize=0)
    {
        $where['fid'] = $tid;
        $where['status']=1;
        $where['del']=0;
        $where['bs'] = 1;
        $orders = "";
        if($ppid == 4){
            //推荐
            $where['recommend'] = 0;
            $orders = 'sort desc,recommend desc';
        }elseif ($ppid ==3){
            //热榜
            $orders = 'sort desc,click desc';
        }elseif ($ppid == 2){
            //精选
            $orders = 'sort desc,selecteds desc';
        }elseif($ppid == 1){
            //最新
            $orders = 'sort desc,create_time';
        }
        return self::where($where)->order($orders)->limit($offsize,$page)->select();
    }


	//首页发友说推荐
    public static function getAll_tow($tid=0,$page=10,$offsize=0)
    {
        $where['del']=0;
        $where['status']=1;
        $where['bs'] = 1;
        if($tid!=0)
        {
            $where['pid']=$tid;
        }
        return self::where($where)->order('sort desc,create_time desc')->limit('0,5')->select();
    }

    //首页发友说推荐
    public static function f_tows($tid,$ppid,$page=6,$offsize=0)
    {
        $where['fid'] = $tid;
        $where['status']=1;
        $where['bs'] = 2;
        $where['del']=0;
        $orders = "";
        if($ppid == 4){
            //推荐
            $where['recommend'] = 0;
            $orders = 'sort desc,recommend desc';
        }elseif ($ppid ==3){
            //热榜
            $orders = 'sort desc,click desc';
        }elseif ($ppid == 2){
            //精选
            $orders = 'sort desc,selecteds desc';
        }elseif($ppid == 1){
            //最新
            $orders = 'sort desc,create_time';
        }
        return self::where($where)->order($orders)->limit($offsize,$page)->select();
    }

	public static function search($search='',$page=10,$offsize=0)
	{
		$where['status']=1;
		$where['del']=0;
		if(!empty($search))
		{
			$where['qtitle']=['like','%'.$search.'%'];
		}
		return self::where($where)->order('sort desc,id desc')->limit($offsize,$page)->select();
	}


	public static function getOne($id)
	{
		return self::where(['del'=>0,'id'=>$id])->find();
	}


    public static function getOne_0ne($aid,$ppid,$page=5,$offsize=0)
    {
        $where = array('fid'=>$aid);
        $where['status']=1;
        $where['del']=0;
        $where['bs'] = 1;
        $orders = "";

        if($ppid == 4){
            //推荐
            $where['recommend'] = 0;
            $orders = 'sort desc,recommend desc';
        }elseif ($ppid ==3){
            //热榜
            $orders = 'sort desc,click desc';
        }elseif ($ppid == 2){
            //精选
            $orders = 'sort desc,selecteds desc';
        }elseif($ppid == 1){
            //最新
            $orders = 'sort desc,create_time';
        }
        return self::where($where)->order($orders)->limit($offsize,$page)->select();
    }

    public static function getOne_0nes($aid,$ppid,$page=5,$offsize=0)
    {
        $where = array('fid'=>$aid);
        $where['status']=1;
        $where['del']=0;
        $where['bs'] = 2;
        $orders = "";

        if($ppid == 4){
            //推荐
            $where['recommend'] = 0;
            $orders = 'sort desc,recommend desc';
        }elseif ($ppid ==3){
            //热榜
            $orders = 'sort desc,click desc';
        }elseif ($ppid == 2){
            //精选
            $orders = 'sort desc,selecteds desc';
        }elseif($ppid == 1){
            //最新
            $orders = 'sort desc,create_time';
        }
        return self::where($where)->order($orders)->limit($offsize,$page)->select();
    }



	public function getCreateTimeAttr($value)
	{
		return date('Y-m-d',$value);
	}

	//获得下一个问答id
	public static function getNext($id)
	{
		return self::where(['del'=>0,'id'=>array('lt',$id)])->order('id desc')->find();
	}

	public static function getAll2()
	{
		$where['del']=0;
		$where['status']=1;

		return self::with('userInfo')->where($where)->order('sort desc,id desc')->select();
	}

	//关联用户表
	public function userInfo()
	{
		return $this->hasOne('UserInfoModel','id','uid')->field('id,portrait,wechaname,city');
	}

	//关联医生表
	public function doctor()
	{
		return $this->hasOne('DoctorModel','id','did')->field('id,pic,docname,positional_titles,position');
	}

}