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
class Mypraise extends Wap
{
    protected function _initialize()
    {
        //获得授权
        $this->getUserInfo();
        $this->assign('fansInfo',$this->fansInfo);
    }

    public function index(){
        $list = Db::name('praise_reg')
            ->where('uid',$this->fansInfo['id'])
            ->where('type','in','6,5,1')
            ->order('id desc')
            ->select();
        $list = $this->handle($list);
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function handle($list)
    {
        foreach ($list as $k=>$v){
            switch ($v['type']){
                case 6:

                    $cases = Db::name('cases')->find($v['aid']);
                    $cases_r = Db::name('cases_record')->where(['cid'=>$v['aid']])->order('create_time desc')->find();

                    if (empty($cases_r)){

                        $list[$k]['info'] = $cases;

                    }else{

                        $cases_r['pic2'] = $cases['pic'];
                        $cases_r['uid'] = $cases['uid'];
                        $list[$k]['info'] = $cases_r;
                    }

                    break;
                case 1:
                    $arr = Db::name('curing')->find($v['aid']);
                    if(!$arr){
                        unset($list[$k]);break;
                    }
                    $list[$k]['info'] = $arr;
                    break;
                case 5:
                    $arr = Db::name('ask')->find($v['aid']);
                    if(!$arr){
                        unset($list[$k]);break;
                    }
                    $list[$k]['info'] = $arr;
                    break;
            }
        }
        return $list;
    }
}