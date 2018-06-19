<?php


namespace app\index\model;

use think\Db;
use think\Model;

class CasesModel extends Model
{
    protected $name='cases';
    protected $pk='id';

    public static function getAll($pid=0,$hid=0,$gid=0,$page=15,$offsize=0)
    {

        $where['status']=1;
        $where['del']=0;
        if(!empty($pid))
        {
            $where['pid']=$pid;
        }

        if(!empty($hid))
        {
            $where['hid']=$hid;
        }

        if(!empty($gid))
        {
            $where['grade_id']=$gid;
        }

        return self::where($where)->order('update_time desc')->limit($offsize,$page)->select();

    }


    public static function getAll_tow($pid=0,$hid=0,$gid=0,$tid,$page=15,$offsize=0)
    {

        $where['status']=1;
        $where['del']=0;
        if(!empty($pid))
        {
            $where['pid']=$pid;
        }

        if(!empty($hid))
        {
            $where['hid']=$hid;
        }

        if(!empty($gid))
        {
            $where['hosname_id']=$gid;
        }
        $order='';
        if($tid==1 || $tid==3){
            $order="update_time desc,";
        }elseif($tid==2){
            $order.="click desc,";
        }
        $order.='update_time desc';

        return self::where($where)->order($order)->limit($offsize,$page)->select();
//        echo self::getLastSql();

    }




    //首页日记推荐 两条
    public static function getAll_one($pid=0,$hid=0,$gid=0,$page=15,$offsize=0)
    {

        $where['status']=1;
        $where['del']=0;
        if(!empty($pid))
        {
            $where['pid']=$pid;
        }

        if(!empty($hid))
        {
            $where['hid']=$hid;
        }

        if(!empty($gid))
        {
            $where['id']=$gid;
        }

        return self::where($where)->order('click desc')->limit("0,3")->select();

    }

    /*
     * 医院详情页推荐日记 两条
     * $hid 、分院id
     * return   array(0)
     * */
    public static function getHosCases($hid){
        if (!empty($hid)) return self::where(['status' => 1 , 'del' => 0 ,'hosname_id' => $hid])->order('data_time desc')->limit(2)->select();
    }

    public static function search($search='',$page=15,$offsize=0)
    {

        $where['status']=1;
        $where['del']=0;
        if(!empty($search))
        {
            $where['info']=['like','%'.$search.'%'];
        }


        return self::where($where)->order('sort desc')->limit($offsize,$page)->select();

    }

    public static function getOne($id)
    {
        return self::where(['status'=>1,'del'=>0,'id'=>$id])->find();
    }

    //关联用户表
    public function userInfo()
    {
        return $this->hasOne('UserInfoModel','id','uid')->field('id,portrait,wechaname,city');
    }

    //关联项目表
    public function project()
    {
        return $this->hasOne('ProjectModel','id','pid')->field('proname');
    }

    //关联医院表
    public function hospital()
    {
        return $this->hasOne('HospitalModel','id','hid')->field('hosname');
    }


}