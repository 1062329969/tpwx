<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * Time: 23:17
 */

namespace app\admin\model;


use think\Db;
use think\Model;
use think\Session;

class LogModel extends Model
{

    protected $pk = 'l_id';
    protected $name='log';

    /**
     * @param $l_modules 模块
     * @param $l_operationtype 操作类型 1查询2新增3修改4删除
     * @param $l_param 传值
     * @param $l_content 内容
     * @return int|string
     */
    public static function savelog($l_modules,$l_operationtype,$l_param,$l_content)
    {
        $logarr = [
            'l_modules'=>$l_modules,
            'l_operationtype'=>$l_operationtype,
            'l_param'=>passport_encrypt(json_encode($l_param,JSON_UNESCAPED_UNICODE),'yxh_log'),
            'l_time'=>time(),
            'l_content'=>passport_encrypt($l_content,'yxh_log'),
            'l_uid'=>Session::get('adminid'),
        ];
        return Db::name('log')->insert($logarr);
    }
}