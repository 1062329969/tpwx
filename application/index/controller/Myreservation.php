<?php
/**
 * Created by yongxianghui.net.
 * User: liuyixin
 * Date: 2018/1/25
 * 药品券
 */
namespace app\index\controller;

use app\index\model\ShipaddressModel;
use app\index\model\SmslogModel;
use app\index\model\UserInfoModel;
use Phpqrcode\QRcode;
use think\Db;
use think\Session;
class Myreservation extends Wap
{
    protected  $project=array("1"=>"头发种植", "2"=>"顶部加密", "3"=>"美人尖种植", "4"=>"发际线种植", "5"=>"眉毛种植","6"=>"胡须种植","7"=>"鬓角种植","8"=>"体毛种植","9"=>"疤痕种植");
    protected function _initialize()
    {
        //获得授权
        $this->getUserInfo();
        $this->assign('fansInfo',$this->fansInfo);
    }

    public function index(){
        $type = input('param.type');
        $type = intval($type);
        if($type!=1&&$type!=2&&$type!=3){
            $type=1;
        }
        $where = ['res_uid'=>$this->fansInfo['id']];
        if($type==3){
            $where['res_state'] = 3;
            $list = Db::name('reservation')->where($where)->order('res_id desc')->select();
        }elseif($type==2){
            $list = Db::name('reservation')->where($where)->where('res_state!=3')->order('res_id desc')->select();
            // echo Db::getLastSql();die;
        }else{
            $list = Db::name('reservation')->where($where)->order('res_id desc')->select();
        }
        $hlist = Db::name('hospital')->select();
        $hlist = array_column($hlist,'hosname','id');
        $this->assign('list',$list);
        $this->assign('hlist',$hlist);
        $this->assign('type',$type);
        $this->assign('pro',$this->project);
        return $this->fetch();
    }

    public function del(){
        $id = input('param.id');
        $res_uid = $this->fansInfo['id'];
        $var = Db::name('reservation')->where(['res_id'=>$id,'res_uid'=>$res_uid])->update(['res_state'=>3]);
        if($var){
            $this->success('取消成功');
        }else{
            $this->error('取消失败');
        }
    }
}