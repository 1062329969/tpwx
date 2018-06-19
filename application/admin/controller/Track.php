<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * 跟踪反馈
 */

namespace app\admin\controller;


use ApiOauth\ApiOauth;
use app\admin\model\TrackImgsModel;
use app\admin\model\TrackModel;
use app\admin\model\VisitModel;
use app\index\event\TemplateMessage;
use app\index\model\UserInfoModel;

class Track extends Admin
{
    protected $modelName='';

    public function _initialize()
    {
        parent::_initialize();
        $this->modelName=new TrackModel();
    }

    public function index()
    {
        $content=$this->modelName->getAll(15);
        $this->assign('content',$content);
        return $this->fetch();
    }


    public function alter()
    {

        $request=request();
        if($request->method()=='POST')
        {
            $data=input('post.');



            if($this->modelName->allowField(true)->save($data,['id'=>$data['id']]))
            {
                if(!empty($data['doc_back']))
                {
                    $track=$this->modelName->find($data['id']);
                    $visit=VisitModel::find($track['visit_id']);
                    $userInfo=UserInfoModel::where(['phonenum'=>$visit['tel']])->find();
                    //发送模板消息
                    $templateMessage=new TemplateMessage();
                    $sData["wecha_id"]=$userInfo['wecha_id'];
                    $sData["tempid"]='fOZxmPcyhm7oUBqoXL26Y5_eNoT-MZRbGHcfS6eZGWw';
                    $sData['first']='我们对您提出的反馈进行了回复。';
                    $sData['keyword1']=$data['doc_back'];
                    $sData['keyword2']=date('Y-m-d H:i:s',time());
                    $sData['remark']='感谢您对提出的反馈。';
                    $sData["href"]= config('domain').'/my/track.html';
                    $sData["topcolor"]='';
                    $apiOauth=new ApiOauth();
                    $access_token=$apiOauth->update_authorizer_access_token(['appid'=>config('Appid'),'appsecret'=>config('Appsecret')]);
                    $templateMessage->sendTempMsg(5,$sData,$access_token);
                }

                $this->success('修改成功',url('index'));
            }
            else
            {
                $this->error('修改失败',url('index'));
            }
        }
        else
        {
            $id=input('param.id');
            $content=$this->modelName->find($id);
            $this->assign('content',$content);
            //读取用户反馈的图片
            $newsImgs=TrackImgsModel::where(['aid'=>$id])->select();
            $this->assign('newsImgs',$newsImgs);
            return $this->fetch();
        }

    }

}