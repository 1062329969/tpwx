<?php

namespace app\admin\model;

use think\Model;
use think\Session;

class AskModel extends Model
{
	protected $pk = 'id';
	protected $name='ask';
	protected $autoWriteTimestamp=true;


	public static function getAll($data,$statuss,$page=15)
	{
        $where = ['del'=>0];

        if(Session::get('adminid') !=1){
            $where['admin_user'] = Session::get('adminid');
        }
	    if(!empty($data)){
            $where['fid'] = $data;
        }
        if(isset($statuss) && !empty($statuss)){
            $where['status'] = $statuss;
        }

        return self::where($where)->order('sort desc,create_time desc')->paginate($page,false,['query' => request()->param()]);
	}

}