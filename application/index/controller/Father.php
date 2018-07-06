<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 微信支付的回调
 */

namespace app\index\controller;

use ApiOauth\ApiOauth;
use think\Config;
use think\Db;
class Father extends Wap{
    protected function _initialize(){
        //获得授权
//        $this->getUserInfo();
//        $this->checkLogin();
//        $this->assign('fansInfo',$this->fansInfo);
    }

    public function index(){
        // echo config('app.api');die;
        $apiOauth=new apiOauth();
        $ticket = $apiOauth->getJsApiTicket('wxa7704589f3ea416d','5aa272ed957a388543431724afcd178a');
        $this->timeStamp = time();
        $this->nonceStr  = rand(100000,999999);
        $url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

        //获得jsdk签名
        $this->signature = $apiOauth->getSignature($this->nonceStr,$ticket,$this->timeStamp,$url);
        $this->assign('timeStamp',$this->timeStamp);
        $this->assign('nonceStr',$this->nonceStr);
        $this->assign('signature',$this->signature);
        return $this->fetch();
    }

    /**
     * @return View
     */
    public function getresult()
    {
        $a = input('param.attr/a');
        array_shift($a);
        /*$a=array("A","A","A","B","C");
        $a=array("B","B","C","B","C");
        $a=array("C","A","B","A","C");
        $a=array("A","A","C","B","C");
        $a=array("A","A","B","B","C");
        $a=array("A","B","C","B","C");*/
        $res = array_count_values($a);
        $a_3 = [
            'A'=>'时尚达人',
            'B'=>'典雅绅士',
            'C'=>'亲民随性派'
        ];
        $i = [3,4,5];

        if($v = array_intersect($i,$res)){
            $v = array_values($v);
            $info = array_search($v[0],$res);
            $val = $a_3[$info];
        }elseif($res['A']==2){
            $val = $a_3['A'];
        }elseif($res['A']==1){
            $val = $a_3['B'];
        }else{
            return $this->error('数据错误');
        }

        Db::name('activityshare')->where('as_id', 1)->setInc('as_sum');

        return $this->success($val);
    }

    public function recordshare(){
        $go = input('param.go');
        $code = input('param.code');
        if($go==1){
            if($code==1){
                $res = Db::name('activityshare')->where('as_id', 1)->setInc('as_wxfriend_s');
            }else{
                $res = Db::name('activityshare')->where('as_id', 1)->setInc('as_wxfriend_e');
            }
        }else{
            if($code==1){
                $res = Db::name('activityshare')->where('as_id', 1)->setInc('as_pyq_s');
            }else{
                $res = Db::name('activityshare')->where('as_id', 1)->setInc('as_pyq_e');
            }
        }
    }
}
?>