<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 问答管理控制器
 */

namespace app\index\controller;

use app\index\model\AnswerImgsModel;
use app\index\model\AnswerModel;
use app\index\model\AskImgsModel;
use app\index\model\AskModel;
use app\index\model\CommentModel;
use app\index\model\ContAdModel;
use app\index\model\FotoplaceModel;
use app\index\model\ProjectModel;
use think\Config;
use ApiOauth\ApiOauth;
use think\Session;

class Livebroadcast extends Wap
{
    protected $timeStamp;
    protected $nonceStr;
    protected $signature;
    protected $shareTitle;
    protected $shareDesc;
    protected $shareLink;
    protected $shareImgUrl;

    protected function _initialize()
    {
        //获得授权
        $this->getUserInfo();
        $this->assign('fansInfo', $this->fansInfo);

        //微信jdk 验证
        $apiOauth = new ApiOauth();
        $ticket = $apiOauth->getJsApiTicket(Config::get('Appid'), Config::get('Appsecret'));
        $this->timeStamp = time();
        $this->nonceStr = rand(100000, 999999);
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        //获得jsdk签名
        $this->signature = $apiOauth->getSignature($this->nonceStr, $ticket, $this->timeStamp, $url);
        $this->assign('timeStamp', $this->timeStamp);
        $this->assign('nonceStr', $this->nonceStr);
        $this->assign('signature', $this->signature);
    }

    //首页
    public function index()
    {
        return $this->fetch();
    }

    //增加
    public function add()
    {
        return $this->fetch();
    }

    //删除
    public function del()
    {
        return $this->fetch();
    }

    //提交问答
    public function alter()
    {
        return $this->fetch();
    }

}