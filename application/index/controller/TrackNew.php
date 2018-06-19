<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 电子病历
 */

namespace app\index\controller;

use app\index\model\ContAdModel;
use app\index\model\VisiBackModel;
use think\Db;

class TrackNew extends Wap
{
    protected function _initialize()
    {
        $this->checkLogin();
        //获得授权
        $this->getUserInfo();
        $this->assign('fansInfo',$this->fansInfo);
    }

    public function index()
    {
        //读取焦点图广告图片
        $cont_ad=ContAdModel::getAll(1);
        $this->assign('cont_ad',$cont_ad);
        //读取首页文字广告图片
        $cont_ad2=ContAdModel::getAll(5);
        $this->assign('cont_ad2',$cont_ad2);
        //读取注意事项
        //先读取所有的电子病历id
        $visit=Db::name('visit')->field('id')->where(['tel'=>$this->fansInfo['phonenum']])->select();
        foreach($visit as $k=>$v)
        {
            $inids[]=$v['id'];
        }
        //读取这些电子病历的注意事项
        if(!empty($inids))
        {
            $visitBack=Db::name('visit_back')->where(['vid'=>['in',$inids],'del'=>0,'status'=>1])->order('id asc')->select();
        }
        else
        {
            $visitBack="";
        }


        $this->assign('visitBack',$visitBack);
        return $this->fetch();
    }


    //跟踪反馈 详情
    public function detail()
    {

        $id=input('param.id');
        $news=VisiBackModel::find($id);
        //获得该病历
        $visit=Db::name('visit')->where(['id'=>$news['vid']])->find();
        $this->assign('visit',$visit);
        $this->assign('news',$news);
        return $this->fetch();
    }

}