<?php


namespace app\index\model;
use think\Model;

class HairCuringModel extends Model
{
    protected $name='hair_curing';
    protected $pk='id';


    public static function getAll($tid=0,$page=10,$offsize=0)
    {
        if(empty($tid))
        {
            return self::where(['del'=>0,'status'=>1])->order('sort desc,id desc')->limit($offsize,$page)->select();
        }
        elseif($tid==1)
        {
            return self::where(['is_video'=>0,'del'=>0,'status'=>1])->order('sort desc,id desc')->limit($offsize,$page)->select();
        }
        else
        {
            return self::where(['is_video'=>1,'del'=>0,'status'=>1])->order('sort desc,id desc')->limit($offsize,$page)->select();
        }

    }

    public static function getTj()
    {
        $where['del']=0;
        $where['status']=1;
        $where['tj']=1;

        return self::where($where)->order('sort desc,id desc')->limit(0,5)->select();
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
        return parent::hasMany('HairCuringImgModel', 'aid')->field('pic');
    }

}