<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 收藏
 */
namespace app\index\controller;


use app\admin\model\DoctorModel;
use app\index\model\CasesRecordModel;
use app\index\model\CollectionModel;
use app\admin\model\CuringModel;
use app\index\model\AskModel;
use app\index\model\CasesModel;
use app\index\model\ForumModel;
use app\index\model\HairCuringModel;
use app\index\model\NcaseModel;
use app\index\model\NewsModel;
use app\index\model\PraiseRegModel;
use app\index\model\VideoModel;
use app\index\model\InformationModel;

class Collection extends Wap
{
	protected function _initialize()
	{
		//获得授权
		$this->getUserInfo();
		$this->assign('fansInfo',$this->fansInfo);
	}

	//收藏或取消收藏
	public function index()
	{
		$data=input('param.');
		if($data['is']==0)
		{
			//记录收藏
			$CollectionModel=new CollectionModel;
			$CollectionModel->save(['aid'=>$data['aid'],'uid'=>$this->fansInfo['id'],'type'=>$data['type'],'create_time'=>time()]);


			if($data['type']==1)
			{
				$CuringModel=new CuringModel();
				$CuringModel->where(['id'=>$data['aid']])->setInc('shoucang',1);

			}
			elseif($data['type']==2)
			{
				$HairCuringModel=new HairCuringModel();
				$HairCuringModel->where(['id'=>$data['aid']])->setInc('shoucang',1);
			}
			elseif($data['type']==3)
			{

				$NewsModel=new NewsModel();
				$NewsModel->where(['id'=>$data['aid']])->setInc('shoucang',1);
			}
			elseif($data['type']==4)
			{

				$ForumModel=new ForumModel();
				$ForumModel->where(['id'=>$data['aid']])->setInc('shoucang',1);
			}
			elseif($data['type']==5)
			{

				$AskModel=new AskModel();
				$AskModel->where(['id'=>$data['aid']])->setInc('shoucang',1);
			}
			elseif($data['type']==6)
			{

				$DoctorModel=new DoctorModel();
				$DoctorModel->where(['id'=>$data['aid']])->setInc('follow',1);
			}
			elseif($data['type']==7)
			{

				$CasesRecordModel=new CasesRecordModel();
				$CasesRecordModel->where(['id'=>$data['aid']])->setInc('shoucang',1);

			}
			elseif($data['type']==8)
			{

				$VideoModel=new VideoModel();
				$VideoModel->where(['id'=>$data['aid']])->setInc('shoucang',1);
			}
			elseif($data['type']==9)
			{

				$NcaseModel=new NcaseModel();
				$NcaseModel->where(['id'=>$data['aid']])->setInc('shoucang',1);
			}

			return ['code'=>1];
		}
		else
		{
			//删除收藏
			$CollectionModel=new CollectionModel;
			$CollectionModel->where(['aid'=>$data['aid'],'uid'=>$this->fansInfo['id'],'type'=>$data['type']])->delete();


			if($data['type']==1)
			{
				$CuringModel=new CuringModel();
				$CuringModel->where(['id'=>$data['aid']])->setDec('shoucang',1);

			}
			elseif($data['type']==2)
			{
				$HairCuringModel=new HairCuringModel();
				$HairCuringModel->where(['id'=>$data['aid']])->setDec('shoucang',1);
			}
			elseif($data['type']==3)
			{

				$NewsModel=new NewsModel();
				$NewsModel->where(['id'=>$data['aid']])->setDec('shoucang',1);
			}
			elseif($data['type']==4)
			{

				$ForumModel=new ForumModel();
				$ForumModel->where(['id'=>$data['aid']])->setDec('shoucang',1);
			}
			elseif($data['type']==5)
			{

				$AskModel=new AskModel();
				$AskModel->where(['id'=>$data['aid']])->setDec('shoucang',1);
			}
			elseif($data['type']==6)
			{

				$DoctorModel=new DoctorModel();
				$DoctorModel->where(['id'=>$data['aid']])->setDec('shoucang',1);
			}
			elseif($data['type']==7)
			{

				$CasesRecordModel=new CasesRecordModel();
				$CasesRecordModel->where(['id'=>$data['aid']])->setDec('shoucang',1);
			}
			elseif($data['type']==8)
			{

				$VideoModel=new VideoModel();
				$VideoModel->where(['id'=>$data['aid']])->setDec('shoucang',1);
			}
			elseif($data['type']==9)
			{

				$NcaseModel=new NcaseModel();
				$NcaseModel->where(['id'=>$data['aid']])->setDec('shoucang',1);
			}

			return ['code'=>2];
		}
	}

	//判断文章是否被收藏
	public function getCollection($aid,$uid,$type)
	{
		$CollectionModel=CollectionModel::where(['aid'=>$aid,'uid'=>$uid,'type'=>$type])->find();
		if(empty($CollectionModel))
		{
			return ['code'=>1];
		}
		else
		{
			return ['code'=>2];
		}
	}

}