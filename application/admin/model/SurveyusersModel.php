<?php

namespace app\admin\model;
use think\Db;
use think\Model;

class SurveyusersModel extends Model
{
    protected $name='surveyusers';
    protected $pk='s_id';
    protected $autoWriteTimestamp=true;

    //根据姓名 电话查询
    public static function getAll($str,$offsize=0,$page)
    {
        if(is_numeric($str)){
            return self:: where(['s_phone' => ['like','%'.$str.'%']]) ->field("s_id,s_name,s_phone,s_project,s_time,s_addtime,s_qid,s_feedback,s_receipt,status")->order('s_id asc')->limit($offsize,$page) -> select();
        }else{
            return self:: where(["s_name" => ['like','%'.$str.'%']]) ->field("s_id,s_name,s_phone,s_project,s_time,s_addtime,s_qid,s_feedback,s_receipt,status")->order('s_id asc')->limit($offsize,$page)-> select();
        }

    }


    public static function getS($id)
    {
        return self::where(['s_qid'=>$id,'status'=>1])->order('s_id asc')->paginate(2);
    }

    public static function getss($id,$name,$time_start,$time_over){
        $where = array();
        if(isset($name) && !empty($name)){
            if(is_numeric($name)){
                $where['s_phone'] = ['like','%'.$name.'%'];
            }else{
                $where['s_name'] = ['like','%'.$name.'%'];
            }
        }
        if(!empty($id)){
            $where['s_qid'] = $id;
        }
        if(!empty($time_start) && !empty($time_over)){
            $time_over = $time_over + 86400;
            $where['s_addtime'] = array('between',$time_start.','.$time_over);
        }
        return self::where($where)->order('s_id desc')->paginate(15,false,['query' => request()->param()]);
    }


    public static function getexport($id,$name,$time_start,$time_over){
        $where = array();
        if(isset($name) && !empty($name)){
            if(is_numeric($name)){
                $where['s_phone'] = ['like','%'.$name.'%'];
            }else{
                $where['s_name'] = ['like','%'.$name.'%'];
            }
        }
        if(!empty($id)){
            $where['s_qid'] = $id;
        }
        if(!empty($time_start) && !empty($time_over)){
            $time_over = $time_over + 86400;
            $where['s_addtime'] = array('between',$time_start.','.$time_over);
        }
        return Db::name('surveyusers')->where($where)->order('s_id desc')->field(['s_id','s_qid','s_name','s_phone','s_project','s_time','s_receipt','s_addtime','s_yard','s_feedback','s_province','s_city','s_county'])->select();
    }


}


