<?php



namespace app\index\model;

class ForumTypeModel extends \think\Model
{
    protected $name='forum_type';
    protected $pk='id';

    public static function getAll()
    {
        return self::where(['status'=>1,'del'=>0])->order('sort desc,id asc')->select(); //->cache(true,7200)
    }

    public static function getAll_one()
    {
        return self::where(['status'=>1,'del'=>0])->order('sort desc,id asc')->limit('0,7')->select(); //->cache(true,7200)
    }


    //关注
    public static function get_onon($tid=0,$page=15,$offsize=0,$pppid=0){
        $arr = array('del'=>0,'status'=>1);
        $orders = 'id desc';
        if($pppid ==1){
            $arr=array('del'=>0,'status'=>1, 'follow'=>1);
            $orders.= ' ,follow desc';
        }elseif($pppid==2){
            $arr=array('del'=>0,'status'=>1);
            $orders.= ' ,create_time desc'; //comment
        }elseif ($pppid ==3){
            $arr=array('del'=>0,'status'=>1);
            $orders.=' ,id desc'; //praise
        }elseif ($pppid ==4){
            $arr=array('del'=>0,'status'=>1);
            $orders.=" ,id desc"; //shoucang
        }
        if($tid==0)
        {
            return self::where($arr)->order($orders)->limit($offsize,$page)->select();
//            echo ForumTypeModel::getLastSql();die;
        }
        else
        {
            $arr['tid'] =$tid;
            return self::where($arr)->order($orders)->limit($offsize,$page)->select();
        }
    }


    //小组
    public static function get_one($tid=0,$page=15,$offsize=0,$pppid=0){
        $arr = array('del'=>0,'status'=>1);
        $orders = 'id desc';
        $arr=array('o_id'=>$pppid);
        if($tid==0)
        {
            return self::where($arr)->order($orders)->limit($offsize,$page)->select();
//
        }
        else
        {
            $arr['tid'] =$tid;
            return self::where($arr)->order($orders)->limit($offsize,$page)->select();
        }
    }





	public static function getOne($id)
	{
		return self::where(['status'=>1,'del'=>0,'id'=>$id])->find();
	}


    public function userInfo()
    {
        return $this->hasOne('UserInfoModel','id','uid')->field('id,portrait,wechaname,city');
    }

}