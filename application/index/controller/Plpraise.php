<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 评论点赞
 */
namespace app\index\controller;
use app\index\event\Integral;
use app\index\model\AnswerModel;
use app\index\model\AskModel;
use app\index\model\CommentModel;
use app\index\model\InformationModel;
use app\index\model\PlpraiseModel;
use app\index\model\CommentpraiseModel;
use think\Db;

class Plpraise extends Wap
{
	protected function _initialize()
	{
		//获得授权
		$this->getUserInfo();
		$this->assign('fansInfo',$this->fansInfo);
	}

	//点赞或取消点赞
	public function index()
	{
		$data=input('param.');
		if(!isset($data['isxz']))
		{
			$data['isxz']=0;
		}

        $plpraiseModel = new InformationModel();
        $return =$plpraiseModel->where(['i_ids'=>$data['hc'],'i_type'=>2,'i_module'=>$data['type'],'i_uid'=>$data['lxuid'],'i_uids'=>$this->fansInfo['id']])->find();
		if(empty($return))
		{
			//记录点赞日志
            $plpraiseModel->save(['i_uid'=>$data['lxuid'],'i_type'=>2,'i_module'=>$data['type'],'i_addtime'=>time(),'i_ids'=>$data['hc'],'i_uids'=>$this->fansInfo['id']]);
			//点赞增加1 如何使5则是问答的回答点赞
			if($data['type']==5 && $data['isxz']==0)
			{
                $CommentModel=new CommentModel();
                $CommentModel->where(['id'=>$data['hc']])->setInc('praise',1);
                //存入评论点赞表中
                $comment_praise = new CommentpraiseModel();
                $comment_praise->allowField(true)->save(['uid'=>$this->fansInfo['id'],'cid'=>$data['hc'],'tid'=>$data['ppuid'],'type'=>$data['type']]);
            }else{
			    //植发日记评论点赞
                $CommentModel=new CommentModel();
                $CommentModel->where(['id'=>$data['hc']])->setInc('praise',1);
                //存入评论点赞表中
                $comment_praise = new CommentpraiseModel();
                $comment_praise->allowField(true)->save(['uid'=>$this->fansInfo['id'],'cid'=>$data['hc'],'tid'=>$data['ppuid'],'type'=>$data['type']]);
            }

			//记录点赞积分
			$integral=new Integral();
			$integral->addition($this->fansInfo['id'],config('hpdjf'),'点赞');
			return ['code'=>1];
		}
		else
		{
			//记录点赞日志
            $plpraiseModel->where(['i_uid'=>$data['lxuid'],'i_type'=>2,'i_module'=>$data['type'],'i_ids'=>$data['hc'],'i_uids'=>$this->fansInfo['id']])->delete();
			//点赞增加1
			if($data['type']==5 && $data['isxz']==0)
			{
//                $answerModel=new AskModel();
//                $answerModel->where(['id'=>$data['ppuid']])->setDec('praise',1);
                $CommentModel=new CommentModel();
                $CommentModel->where(['id'=>$data['hc']])->setDec('praise',1);
                //从评论点赞表中删除这条数据
                $comment_praise = new CommentpraiseModel();
                $comment_praise->where(['cid'=>$data['hc'],'tid'=>$data['ppuid'],'type'=>$data['type']])->delete();
			}else{
                $CommentModel=new CommentModel();
                $CommentModel->where(['id'=>$data['hc']])->setDec('praise',1);
                //从评论点赞表中删除这条数据
                $comment_praise = new CommentpraiseModel();
                $comment_praise->where(['cid'=>$data['hc'],'tid'=>$data['ppuid'],'type'=>$data['type']])->delete();
            }

			//记录点赞积分
			$integral=new Integral();
			$integral->reduce($this->fansInfo['id'],config('hpdjf'),'取消点赞');

			return ['code'=>2];
		}

	}

}