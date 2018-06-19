<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 社区首页
 */
namespace app\index\controller;


use app\index\model\AskImgsModel;
use app\index\model\AskModel;
use app\index\model\CommentModel;
use app\index\model\ContAdModel;
use app\index\model\ProjectModel;
use think\Config;
use ApiOauth\ApiOauth;


class Community extends Wap
{

    protected function _initialize()
    {
        //获得授权
        $this->getUserInfo();
        $this->assign('fansInfo',$this->fansInfo);

    }

    //社区首页
    public function index()
    {
        $tid=input('param.tid',0);
        $type=input('param.type',0);
        //读取问答
        $ask=AskModel::getAll($tid);
        //读取问答下的图片
        foreach($ask as $key=>$v)
        {
            $ask[$key]['images']=AskImgsModel::getAll($v['id']);
            $ask[$key]['imgs_nums']=AskImgsModel::getCountImgs($v['id']);

        }

        $this->assign('ask',$ask);
        $this->assign('count',count($ask));
        //读取社区的首页广告
        $cont_ad=ContAdModel::getAll(12);
        $this->assign('cont_ad',$cont_ad);


        return $this->fetch();
    }

}