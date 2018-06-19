<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 评论
 */

namespace app\index\controller;


use app\admin\controller\ForumType;
use app\index\event\Integral;
use app\index\model\AskModel;
use app\index\model\CasesModel;
use app\index\model\CasesRecordModel;
use app\index\model\CommentModel;
use app\index\model\CuringModel;
use app\index\model\ForumModel;
use app\index\model\HairCuringModel;
use app\index\model\NewsModel;
use app\index\model\VideoModel;
use app\index\model\InformationModel;

class Comment extends Wap
{


    protected function _initialize()
    {
        //获得授权
        $this->getUserInfo();
        $this->assign('fansInfo',$this->fansInfo);



    }

    //添加评论
    public function addComment()
    {
        //检查是否登录
        $this->checkLoginAjax();

        $data=input('post.');
        $data['text'] = filterEmoji($data['text']);
        $result=$this->validate($data,'ValidateClass.CK16');
        if(true !== $result)
        {
            // 验证失败 输出错误信息
            $this->error($result);
        }


        if($data['type']==1)
        {

            $CuringModel=new CuringModel();
            $CuringModel->where(['id'=>$data['aid']])->setInc('pinglun',1);
//            //养护健康评论
//            $curing_ids = $data['aid'];//帖子的id
//            $curing_uids = $this->fansInfo['id'];//操作人ID
//            $curing_text = $data['text'];//评轮
//            $curing_save = new InformationModel();
//            $curing_save->allowField(true)->save(['i_type'=>1,'i_module'=>2,'i_ids'=>$curing_ids,'i_addtime'=>time(),'i_note'=>$curing_text,'i_uids'=>$curing_uids]);


        }
        elseif($data['type']==2)
        {
            $HairCuringModel=new HairCuringModel();
            $HairCuringModel->where(['id'=>$data['aid']])->setInc('pinglun',1);
        }
        elseif($data['type']==3)
        {

            $NewsModel=new NewsModel();
            $NewsModel->where(['id'=>$data['aid']])->setInc('pinglun',1);
        }
        elseif($data['type']==4)
        {

            $ForumModel=new ForumModel();
            $ForumModel->where(['id'=>$data['aid']])->setInc('comment',1);
//			$aid            = $data['aid']; //帖子id
//			$forum_i_uid    = ForumModel::find($aid);
//			$forum_uid      = $forum_i_uid['uid'];//发帖人id
//            $forum_uids     = $this->fansInfo['id'];//操作人id
//            $forum_text     = $data['text'];
//            $forum = new InformationModel();
//            $forum->allowField(true)->save(['i_type'=>1,'i_module'=>3,'i_uid'=>$aid,'i_ids'=>$forum_uid,'i_addtime'=>time(),'i_note'=>$forum_text,'i_uids'=>$forum_uids]);

        }
        elseif($data['type']==5)
        {
            $AskModel=new AskModel();
            $AskModel->where(['id'=>$data['aid']])->setInc('comment',1);
        }
        elseif($data['type']==6)
        {
            $CasesRecordModel=new CasesRecordModel();
            $CasesRecordModel->where(['id'=>$data['aid']])->setInc('comment',1);
            $casesRec=$CasesRecordModel->where(['id'=>$data['aid']])->find();
            //相关案例也需要加一
            $CasesModel=new CasesModel();
            $CasesModel->where(['id'=>$casesRec['cid']])->setInc('comment',1);

            $cases = new InformationModel();
            $cases->allowField(true)->save(['i_ids'=>$data['aid'],'i_note'=>$data['text'],'i_uid'=>$data['tid'],'i_type'=>1,'i_module'=>1,'i_addtime'=>time(),'i_uids'=>$this->fansInfo['id']]);

//


        }
        elseif($data['type']==7)
        {
            $VideoModel=new VideoModel();
            $VideoModel->where(['id'=>$data['aid']])->setInc('comment',1);
        }



        $data['uid']=$this->fansInfo['id'];
        $comment=new CommentModel();
        if($comment->allowField(true)->save($data))
        {
            //记录评论积分
            $integral=new Integral();
            $integral->addition($this->fansInfo['id'],config('hpdjf'),'评论');
            return ['code'=>1,'wechaname'=>$this->fansInfo['wechaname'],'portrait'=>$this->fansInfo['portrait'],'time'=>date('Y-m-d H:i:s',time())];
        }
        else
        {
            return ['code'=>0,'msg'=>'提交评论失败！'];
        }
    }

    //评论加载更多
    public function nextpagecomment()
    {
        $type=input('post.type');//6
        $offsize=input('post.offsize',0);//10
        $aid=input('post.aid');//134
        $comment=CommentModel::getAll($type,$aid,10,$offsize);
        $comment2=CommentModel::getAll2($type,$aid);

        $returnStr="";
        foreach ($comment as $key=>$vo)
        {

            $returnStr=$returnStr.'<div class="tx">
	            <div class="tximg"><img src="'.get_wxheadimg($vo['userInfo']['portrait'],132).'">'.$vo['userInfo']['wechaname'].'</div>
	            <div class="txdz" data-id="'.$vo['id'].'" id="txdz'.$vo['id'].'"><img src="'.HTML_STATIC.'/images/pldz.png"><span>'.$vo['praise'].'</span></div>
	        </div>
	        <p>'.$vo['text'].'</p>
	        <div class="plinfo"><span class="backText" data-plid="'.$vo['id'].'" data-uid="'.$vo['userInfo']['id'].'" data-align="'.$vo['userInfo']['wechaname'].'">回复</span>'.$vo['create_time'].'</div>';

            foreach ($comment2 as $key2=>$vo2)
            {
                if($vo2['plid']==$vo['id'])
                {
                    $returnStr=$returnStr.'<div class="reCont"><p><font>'.$vo2['userInfo']['wechaname'].':</font> '.$vo2['text'].' <span>'.$vo2['create_time'].'</span></p></div>';
                }
            }

            $returnStr=$returnStr.'<div class="fonline"></div>';

        }


        return $returnStr;
    }

    //回复评论
    public function recComment()
    {
        //检查是否登录
        $this->checkLoginAjax();

        $data=input('post.');
        $result=$this->validate($data,'ValidateClass.CK16');
        if(true !== $result)
        {
            // 验证失败 输出错误信息
            $this->error($result);
        }

        if($data['type']==4){
            //发友说评论
            $forum_uid      = $data['reuid'];//被通知的id
            $forum_i_ids    = $data['aid'];//帖子的id
            $forum_text     = $data['text'];//评论内容
            $forum_i_uids   = $this->fansInfo['id'];
            $res = new InformationModel();
            $res->allowField(true)->save(['i_type'=>1,'i_module'=>3,'i_ids'=>$forum_i_ids,'i_addtime'=>time(),'i_note'=>$forum_text,'i_uid'=>$forum_uid,'i_uids'=>$forum_i_uids]);

        }elseif($data['type']==6){
            //植发日记
            if($data['uuid'] == $this->fansInfo['id']){
                $this->error('不能回复自己的评论');
            }

            $res = new InformationModel();
            $res->allowField(true)->save(['i_type'=>1,'i_module'=>1,'i_ids'=>$data['aid'],'i_addtime'=>time(),'i_note'=>$data['text'],'i_uid'=>$data['uuid'],'i_uids'=>$this->fansInfo['id']]);

        }elseif ($data['type']==1){
            //养护健康日记
            $curing_uid      = $data['reuid'];//被通知的id
            $curing_i_ids    = $data['aid'];//帖子的id
            $curing_text     = $data['text'];//评论内容
            $curing_i_uids   = $this->fansInfo['id'];
            $res = new InformationModel();
            $res->allowField(true)->save(['i_type'=>1,'i_module'=>2,'i_ids'=>$curing_i_ids,'i_addtime'=>time(),'i_note'=>$curing_text,'i_uid'=>$curing_uid,'i_uids'=>$curing_i_uids]);

        }elseif ($data['type']==5){
            //记录头条 回复评论
            if($data['reuid'] == $this->fansInfo['id']){
                $this->error('不能回复自己的评论');
            }
            $ask = new InformationModel();
            $ask->allowField(true)->save(['i_uid'=>$data['reuid'],'i_ids'=>$data['aid'],'i_note'=>$data['text'],'i_type'=>1,'i_module'=>$data['type'],'i_addtime'=>time(),'i_uids'=>$this->fansInfo['id']]);
        }



        $data['uid']=$this->fansInfo['id'];
        $comment=new CommentModel();
        if($comment->allowField(true)->save($data))
        {
            //记录评论积分
            $integral=new Integral();
            $integral->addition($this->fansInfo['id'],config('hpdjf'),'回复评论');
            return ['code'=>1,'wechaname'=>$this->fansInfo['wechaname'],'portrait'=>$this->fansInfo['portrait'],'time'=>date('Y-m-d H:i:s',time())];
        }
        else
        {
            return ['code'=>0,'msg'=>'提交回复评论失败！'];
        }
    }



    //头条回复评论
    public function articlecomment(){
        $data=input('post.');
        $datas['uid'] = $this->fansInfo['id'];
        //不能回复自己的评论
        if($data['cuid'] == $this->fansInfo['id']){
            return ['code'=>1,'msg'=>'不能回复自己的评论！'];
        }

        //表中评论数要加一
        $ask = new AskModel();
        $ask->where(['id'=>$data['aid']])->setInc('comment',1);

        $forum = new CommentModel();
        $forum->allowField(true)->save(['uid'=>$datas['uid'],'aid'=>$data['aid'],'text'=>$data['commnet_text'],'plid'=>$data['tid'],'reuid'=>$data['cuid'],'type'=>5]);
        //记录到消息表
        $info = new InformationModel();
        $info->allowField(true)->save(['i_note'=>$data['commnet_text'],'i_uid'=>$data['cuid'],'i_type'=>1,'i_module'=>5,'i_ids'=>$data['aid'],'i_uids'=>$this->fansInfo['id'],'i_addtime'=>time()]);

    }

    //植发日记回复评论
    public function casescomment(){
        $data = input("post.");
        $datas['uid']=$this->fansInfo['id'];
        //不能回复自己的评论
        if($data['cuid'] == $this->fansInfo['id']){
            return ['code'=>1,'msg'=>'不能回复自己的评论！'];
        }
        $cases = new CommentModel();
        $cases->allowField(true)->save(['uid'=>$datas['uid'],'aid'=>$data['aid'],'text'=>$data['commnet_text'],'plid'=>$data['tid'],'reuid'=>$data['cuid'],'type'=>6]);
        //Record表中评论数要加一
        $casesRecord = new CasesRecordModel();
        $casesRecord->where(['id'=>$data['aid']])->setInc('comment',1);
        $casesRe=$casesRecord->where(['id'=>$data['aid']])->find();

        //cases表中评论数量也要加一
        $cases_s = new CasesModel();
        $cases_s->where(['id'=>$casesRe['cid']])->setInc('comment',1);
        //记录到消息表
        $info = new InformationModel();
        $info->allowField(true)->save(['i_note'=>$data['commnet_text'],'i_uid'=>$data['cuid'],'i_type'=>1,'i_module'=>1,'i_ids'=>$data['aid'],'i_uids'=>$this->fansInfo['id'],'i_addtime'=>time()]);

    }

}