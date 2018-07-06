<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 内容点赞
 */
namespace app\index\controller;

use app\admin\model\CuringModel;
use app\index\event\Integral;
use app\index\model\AskModel;
use app\index\model\CasesModel;
use app\index\model\CasesRecordModel;
use app\index\model\ForumModel;
use app\index\model\HairCuringModel;
use app\index\model\IndexnewsModel;
use app\index\model\NcaseModel;
use app\index\model\NewsModel;
use app\index\model\PraiseRegModel;
use app\index\model\Praise2RegModel;
use app\index\model\VideoModel;
use app\index\model\InformationModel;
use think\Db;

class Praise extends Wap
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
		if($data['is']==0)
		{
			if($data['type']==1)
			{
				$CuringModel=new CuringModel();
				$CuringModel->where(['id'=>$data['aid']])->setInc('dianzan',1);

            }
			elseif($data['type']==2)
			{
				$HairCuringModel=new HairCuringModel();
				$HairCuringModel->where(['id'=>$data['aid']])->setInc('dianzan',1);
			}
			elseif($data['type']==3)
			{
				$NewsModel=new NewsModel();
				if($data['is']==0){
                    $NewsModel->where(['id'=>$data['aid']])->setInc('dianzan',1);
                }else{
                    $NewsModel->where(['id'=>$data['aid']])->setDec('dianzan',1);
                }

			}
			elseif($data['type']==4)
			{
				$ForumModel=new ForumModel();
				$ForumModel->where(['id'=>$data['aid']])->setInc('praise',1);

                //发友说点赞
                $aid            = $data['aid']; //帖子id
                $forum_i_uid    = ForumModel::find($aid);
                $forum_uid      = $forum_i_uid['uid'];//发帖人id
                $forum_uids     = $this->fansInfo['id'];//操作人id
                $forum          = new InformationModel();
                $forum->allowField(true)->save(['i_type'=>2,'i_module'=>3,'i_uid'=>$aid,'i_ids'=>$forum_uid,'i_addtime'=>time(),'i_uids'=>$forum_uids]);

			}
			elseif($data['type']==5)
			{
			    //头条
				$AskModel=new AskModel();
				$AskModel->where(['id'=>$data['aid']])->setInc('praise',1);
                $ask  = new InformationModel();
                $ask->allowField(true)->save(['i_type'=>2,'i_module'=>5,'i_uid'=>$data['uid'],'i_ids'=>$data['aid'],'i_addtime'=>time(),'i_uids'=>$this->fansInfo['id']]);

            }
			elseif($data['type']==6)
			{
                //相关案例也需要加一
                $CasesModel=new CasesModel();
                $CasesModel->where(['id'=>$data['aid']])->setInc('praise',1);
				$CasesRecordModel=new CasesRecordModel();
				$CasesRecordModel->where(['id'=>$data['i_ids']])->setInc('praise',1);

                $casesRec=$CasesRecordModel->where(['id'=>$data['i_ids']])->find();

////

                //植发日记评论消息

                $res = new InformationModel();
                $res->allowField(true)->save(['i_type'=>2,'i_module'=>1,'i_uid'=>$data['aid'],'i_ids'=>$data['i_ids'],'i_addtime'=>time(),'i_uids'=>$this->fansInfo['id']]);
			}
			elseif($data['type']==7)
			{
				$VideoModel=new VideoModel();
				$VideoModel->where(['id'=>$data['aid']])->setInc('praise',1);
			}
			//内部案例点赞
			elseif($data['type']==8)
			{
				$NcaseModel=new NcaseModel();
				$NcaseModel->where(['id'=>$data['aid']])->setInc('dianzan',1);
			}

			if($data['type'] == 6){
                //记录点赞日志
                $PraiseRegModel=new PraiseRegModel;
                $PraiseRegModel->save(['aid'=>$data['i_ids'],'uid'=>$this->fansInfo['id'],'type'=>$data['type'],'create_time'=>time()]);
            }else{
                //记录点赞日志
                $PraiseRegModel=new PraiseRegModel;
                $PraiseRegModel->save(['aid'=>$data['aid'],'uid'=>$this->fansInfo['id'],'type'=>$data['type'],'create_time'=>time()]);
                //记录点赞积分
            }

			$integral=new Integral();
			$integral->addition($this->fansInfo['id'],config('hpdjf'),'点赞');
			return ['code'=>1];
		}
		else
		{

			if($data['type']==1)
			{
				$CuringModel=new CuringModel();
				$CuringModel->where(['id'=>$data['aid']])->setDec('dianzan',1);

			}
			elseif($data['type']==2)
			{
				$HairCuringModel=new HairCuringModel();
				$HairCuringModel->where(['id'=>$data['aid']])->setDec('dianzan',1);
			}
			elseif($data['type']==3)
			{
				$NewsModel=new NewsModel();
				$NewsModel->where(['id'=>$data['aid']])->setDec('dianzan',1);
			}
			elseif($data['type']==4)
			{
				$ForumModel=new ForumModel();
				$ForumModel->where(['id'=>$data['aid']])->setDec('praise',1);
			}
			elseif($data['type']==5)
			{

				$AskModel=new AskModel();
                $AskModel->where(['id'=>$data['aid']])->setDec('praise',1);
                $res = new InformationModel();
                $res->where(['i_ids'=>$data['aid'],'i_uids'=>$data['res'],'i_type'=>2,'i_module'=>5])->delete();

			}
			elseif($data['type']==6)
			{

                //相关案例也需要加一
                $CasesModel=new CasesModel();
                $CasesModel->where(['id'=>$data['aid']])->setDec('praise',1);
				$CasesRecordModel=new CasesRecordModel();
				$CasesRecordModel->where(['id'=>$data['i_ids']])->setDec('praise',1);
                $casesRec=$CasesRecordModel->where(['id'=>$data['i_ids']])->find();

                $res = new InformationModel();
                $res->where(['i_ids'=>$data['i_ids'],'i_uids'=>$this->fansInfo['id'],'i_type'=>2,'i_module'=>1])->delete();

			}
			elseif($data['type']==7)
			{
                $VideoModel=new VideoModel();
			    if($data['is']==1){
			        //用户的积分h
                    $VideoModel->where(['id'=>$data['aid']])->setDec('praise',1);
                }else{
			        //用户的积分加1
                    $VideoModel->where(['id'=>$data['aid']])->setInc('praise',1);
                }
			}
			//内部案例点赞
			elseif($data['type']==8)
			{
				$NcaseModel=new NcaseModel();
				$NcaseModel->where(['id'=>$data['aid']])->setDec('dianzan',1);
			}

			//删除点赞日志
			$PraiseRegModel=new PraiseRegModel;
			$PraiseRegModel->where(['aid'=>$data['aid'],'uid'=>$this->fansInfo['id'],'type'=>$data['type']])->delete();
			//记录点赞积分
			$integral=new Integral();
			$integral->reduce($this->fansInfo['id'],config('hpdjf'),'取消点赞');
			return ['code'=>2];
		}

	}


	//点鄙视
	public function index2()
	{
		$data=input('param.');

		if($data['is']==0)
		{

			if($data['type']==7)
			{
				$VideoModel=new VideoModel();
				$VideoModel->where(['id'=>$data['aid']])->setInc('praise2',1);
			}
			elseif($data['type']==9)
			{
				$NcaseModel=new NcaseModel();
				$NcaseModel->where(['id'=>$data['aid']])->setInc('praise2',1);
			}

			//记录日志
			$PraiseRegModel=new Praise2RegModel;
			$PraiseRegModel->save(['aid'=>$data['aid'],'uid'=>$this->fansInfo['id'],'type'=>$data['type'],'create_time'=>time()]);

			return ['code'=>1];
		}
		else
		{

			//删除点赞日志
			//$PraiseRegModel=new PraiseRegModel;
			//$PraiseRegModel->where(['aid'=>$data['aid'],'uid'=>$this->fansInfo['id'],'type'=>$data['type']])->delete();
			return ['code'=>2];
		}

	}

	//判断文章是否被点赞
	public function getPraise($aid,$uid,$type)
	{
		$PraiseRegModel=PraiseRegModel::where(['aid'=>$aid,'uid'=>$uid,'type'=>$type])->find();
		if(empty($PraiseRegModel))
		{
			return ['code'=>1];
		}
		else
		{
			return ['code'=>2];
		}
	}

	//判断文章是否被鄙视
	public function getPraise2($aid,$uid,$type)
	{
		$PraiseRegModel=Praise2RegModel::where(['aid'=>$aid,'uid'=>$uid,'type'=>$type])->find();
		if(empty($PraiseRegModel))
		{
			return ['code'=>1];
		}
		else
		{
			return ['code'=>2];
		}
	}

}