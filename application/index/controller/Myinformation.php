<?php
/**
 * Created by yongxianghui.net.
 * User: liuyixin
 * Date: 2018/1/25
 * 药品券
 */
namespace app\index\controller;

use app\admin\model\AboutModel;
use app\index\model\DoctorModel;
use app\index\model\ShipaddressModel;
use app\index\model\SmslogModel;
use app\index\model\UserInfoModel;
use Phpqrcode\QRcode;
use think\Db;
use think\Session;
class Myinformation extends Wap
{
    protected function _initialize()
    {
        //获得授权
        $this->getUserInfo();
        $this->assign('fansInfo',$this->fansInfo);
    }

    public function index(){
        $list = Db::name('information')->where(['i_uid'=>$this->fansInfo['id'],'i_type'=>1])->order('i_id desc')->select();
        $this->getcount();
        Db::name('information')->where(['i_uid'=>$this->fansInfo['id'],'i_type'=>1])->update(['i_state'=>2]);
        //处理各种数据
        $list = $this->handle($list);
        $this->assign('list',$list);
        return $this->fetch();
    }

    function getcount(){
        $indexcount     = Db::name('information')->where(['i_uid'=>$this->fansInfo['id'],'i_type'=>1,'i_state'=>1])->count();
        $praisecount    = Db::name('information')->where(['i_uid'=>$this->fansInfo['id'],'i_type'=>2,'i_state'=>1])->count();
        $msgcount       = Db::name('information')->where(['i_uid'=>$this->fansInfo['id'],'i_type'=>3,'i_state'=>1])->count();
        $this->assign('indexcount',$indexcount);
        $this->assign('praisecount',$praisecount);
        $this->assign('msgcount',$msgcount);
        return true;
    }

    /**
     * @return View
     */
    public function handle($list)
    {
        foreach ($list as $k=>$v){
            switch ($v['i_module']){
                case 1:
                    $cases_r = Db::name('cases_record')->find($v['i_ids']);
                    $cases = Db::name('cases')->find($cases_r['cid']);
                    $hc = Db::name('comment')->where(['aid'=>$v['i_ids']])->find();
                    if(!$hc){
                        unset($list[$k]);continue;
                    }
                    $cases_r['pic2'] = $cases['pic'];
                    $list[$k]['info'] = $cases_r;
                    $list[$k]['hc'] = $hc;
                    break;
                case 2:
                    $haircarecomment = Db::name('haircare')->find($v['i_ids']);
                    $list[$k]['info'] = $haircarecomment;
                    break;
                case 5:

                    $hc = Db::name('comment')->where(['aid'=>$v['i_ids']])->find();
                    $forum = Db::name('ask')->find($hc['aid']);
                    if(!$hc){
                        unset($list[$k]);continue;
                    }
                    $list[$k]['info'] = $forum;
                    $list[$k]['hc'] = $hc;
                    break;
            }
        }
        return $list;
    }



//点赞
    public function handles($list)
    {
        foreach ($list as $k=>$v){
            switch ($v['i_module']){
                case 1:
                    $cases_rs = Db::name('comment')->where(['id'=>$v['i_ids']])->find();
                    $cases_r = Db::name('cases_record')->find($cases_rs['aid']);
                    $cases = Db::name('cases')->find($cases_r['cid']);
                    $hc = Db::name('comment')->where(['aid'=>$v['i_ids']])->find();
                    if(!$hc){
                        unset($list[$k]);continue;
                    }
                    $cases_r['pic2'] = $cases['pic'];
                    $list[$k]['info'] = $cases_r;
                    $list[$k]['hc'] = $hc;
                    break;
                case 2:
                    $haircarecomment = Db::name('haircare')->find($v['i_ids']);
                    $list[$k]['info'] = $haircarecomment;
                    break;
                case 5:

                    $hc = Db::name('comment')->where(['id'=>$v['i_ids']])->find();
                    $forum = Db::name('ask')->find($hc['aid']);

                    if(!$hc){
                        unset($list[$k]);continue;
                    }
                    $list[$k]['info'] = $forum;
                    $list[$k]['hc'] = $hc;
                    break;
            }
        }
        return $list;
    }

    public function praise(){
        $this->getcount();
        $list = Db::name('information')->where(['i_uid'=>$this->fansInfo['id'],'i_type'=>2])->order('i_id desc')->select();
        Db::name('information')->where(['i_uid'=>$this->fansInfo['id'],'i_type'=>2])->update(['i_state'=>2]);
        //处理各种数据
        $list = $this->handles($list);
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function msg(){
        $this->getcount();
        $list = Db::name('information')->where(['i_uid'=>$this->fansInfo['id'],'i_type'=>3])->order('i_id desc')->select();
        Db::name('information')->where(['i_uid'=>$this->fansInfo['id'],'i_type'=>3])->update(['i_state'=>2]);
        //处理各种数据
        $this->assign('list',$list);
        return $this->fetch();
    }
}