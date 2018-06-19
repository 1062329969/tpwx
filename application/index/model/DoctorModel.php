<?php


namespace app\index\model;


use think\Model;

class DoctorModel extends Model
{
	protected $name='doctor';
	protected $pk='id';


	public static function getDocs($pid,$hid,$page=10,$offsize=0)
	{
		$where['status']=1;
		$where['del']=0;
		if(!empty($pid))
		{
			//读取项目id对应的项目名称
			$project=ProjectModel::getOne($pid);
			$where['good_at']=array("like","%".$project['proname']."%");
		}

		if(!empty($hid))
		{
			$where['hid']=$hid;
		}

		return self::where($where)->order('sort desc')->limit($offsize,$page)->select();
	}

	public static function getHosDoc($hid)
	{
		return self::where(['hid'=>$hid,'del'=>0,'status'=>1])->select();
	}

	//按id查找医生
	public static function getIds($ids)
	{
		return self::where(['del'=>0,'status'=>1,'id'=>['in',$ids]])->select();
	}

	public static function getOne($id)
	{
		return self::where(['del'=>0,'status'=>1,'id'=>$id])->find();
	}

	//关联医院表
	public function hospital()
	{
		return $this->hasOne('HospitalModel','id','hid')->field('id,hosname');
	}


    public static function search($search='',$page=15,$offsize=0)
    {

        $where['status']=1;
        $where['del']=0;
        if(!empty($search))
        {
            $where['docname']=['like','%'.$search.'%'];
        }


        return self::where($where)->order('sort desc')->limit($offsize,$page)->select();

    }

}