<?php

namespace app\admin\model;
use think\Model;

class GoodsTypeModel extends Model
{
	protected $pk = 'id';
	protected $name='goods_type';
	protected $autoWriteTimestamp=true;

	public static function getAll($page=15)
	{
		$goodtype = self::where(['status'=>1,'del'=>0,'pid'=>0])->order('sort desc,id asc')->paginate($page);
        foreach ($goodtype as $k=>$v){
            $goodtype[$k]['child'] = self::where(['status'=>1,'del'=>0,'pid'=>$v['id']])->order('sort desc,id asc')->select();;
        }
        return $goodtype;
	}

	public static function getTop(){
        return self::where(['status'=>1,'del'=>0,'pid'=>0])->select();
    }

	public static function getContType()
	{
		return self::where(['status'=>1,'del'=>0,'url'=>''])->select();
	}

	protected function getStatusAttr($value)
	{
		if($value==0)
		{
			return '停用';
		}
		elseif($value==1)
		{
			return '正常';
		}
	}

}