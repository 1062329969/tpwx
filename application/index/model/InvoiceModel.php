<?php

namespace app\index\model;


use think\Db;
use think\Model;

class InvoiceModel extends Model
{
	protected $name='invoice';
	protected $pk='invoice_id';

	public static function getAll($uid='')
	{
	    if($uid){
            return Db::name('invoice')->where(['invoice_uid'=>$uid])->order('invoice_id desc')->select();
        }else{
            return Db::name('invoice')->order('sd_default desc,sd_id desc')->select();
        }
	}
}