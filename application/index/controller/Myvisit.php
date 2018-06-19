<?php
/**
 * Created by yongxianghui.net.
 * User: liuyixin
 * Date: 2018/1/25
 * 药品券
 */
namespace app\index\controller;

use app\admin\model\AboutModel;
use app\index\model\ShipaddressModel;
use app\index\model\SmslogModel;
use app\index\model\UserInfoModel;
use Phpqrcode\QRcode;
use think\Db;
use think\Session;
class Myvisit extends Wap
{
    protected function _initialize()
    {
        //获得授权
        $this->getUserInfo();
        $this->assign('fansInfo',$this->fansInfo);
    }

    public function index(){
        $list = Db::name('visit')->where(['tel'=>$this->fansInfo['phonenum']])->select();
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function info(){
        $id = input('get.id/d');
        $info = Db::name('visit')->where(['tel'=>$this->fansInfo['phonenum']])->find($id);
        if(!$info){
            $this->error('未查询到数据');
        }else{
            $this->assign('info',$info);
            return $this->fetch();
        }
    }

}