<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 搜索
 */

namespace app\index\controller;


use app\index\model\DoctorModel;
use app\index\model\VideoModel;
use app\index\model\AskImgsModel;
use app\index\model\AskModel;
use app\index\model\CasesModel;
use app\index\model\CasesRecordModel;
use app\index\model\CuringImgModel;
use app\index\model\CuringModel;
use app\index\model\ForumImgsModel;
use app\index\model\ForumModel;
use app\index\model\GoodsModel;
use app\index\model\NewsImgModel;
use app\index\model\NewsModel;
use app\index\model\ProjectModel;
use think\Db;

class Search extends Wap
{

	protected $searchType=[
		0=>['name'=>'植发日记'],
		1=>['name'=>'医生团队'],
		2=>['name'=>'发友说'],
		3=>['name'=>'新闻'],
		4=>['name'=>'养护'],
		5=>['name'=>'商品'],
		6=>['name'=>'视频'],
	];

	protected function _initialize()
	{
		//获得授权
		$this->getUserInfo();
		$this->assign('fansInfo',$this->fansInfo);

	}

	public function index()
	{
		$sType=input('param.sType',0);
		$this->assign('sType',$sType);
		//读取该用户的搜索记录
		$search_key=Db::name('search_key')->where(['uid'=>$this->fansInfo['id']])->order('id desc')->limit(0,10)->select();
		$this->assign('search_key',$search_key);
		//读取问答的分类
		$contType=ProjectModel::getAll();
		$this->assign('contType',$contType);

		return $this->fetch();
	}

	//清除搜索记录
	public function clear()
	{
		//读取该用户的搜索记录
		Db::name('search_key')->where(['uid'=>$this->fansInfo['id']])->delete();
	}

	public function search()
	{


		$seartext=urldecode(input('param.seartext'));
		$sType=input('param.sType',0);

		switch ($sType)
		{
			case 0:
				//读取案例内容
				$cases=CasesModel::search($seartext,0,10);

				foreach($cases as $key=>$v)
				{
					//读取该案例的最后一次回复情况
					$casesRecordModel=CasesRecordModel::getOne($v['id']);
					$cases[$key]['pic2']=$casesRecordModel['pic'];
				}

				$this->assign('cases',$cases);
				$this->assign('count',count($cases));

				break;
			case 1:
				$forum=DoctorModel::search($seartext,10);
//				echo "<pre>";
//				print_r($forum[0]['pic']);die;
//				foreach ($forum as $key=>$v)
//				{
//
//					$forum[$key]['imgs_nums']=ForumImgsModel::getCountImgs($v['id']);
//				}

				$this->assign('forum',$forum);
				$this->assign('count',count($forum));

				break;
			case 2:
				//读取问答
				$ask=AskModel::search($seartext);
				//读取问答下的图片
				foreach($ask as $key=>$v)
				{
					$ask[$key]['images']=AskImgsModel::getAll($v['id']);
				}
				$this->assign('ask',$ask);
				$this->assign('count',count($ask));
				break;

			case 3:
				$news=NewsModel::search($seartext,10);
				//判断是一图 三图 还是视频
				foreach ($news as $key=>$val)
				{
					$news[$key]['imgs_nums']=NewsImgModel::getCountImgs($val['id']);
				}

				$this->assign('news',$news);
				$this->assign('count',count($news));
				break;

			case 4:
				$news=CuringModel::search($seartext);

				//判断是一图 三图 还是视频
				foreach ($news as $key=>$val)
				{
					$news[$key]['imgs_nums']=CuringImgModel::getCountImgs($val['id']);
				}

				$this->assign('news',$news);
				$this->assign('count',count($news));
				break;

			case 5:

				$goods=GoodsModel::search($seartext,10);
				$this->assign('goods',$goods);
				$this->assign('count',count($goods));
				break;

			case 6:

				$video=VideoModel::search($seartext,10);
				$this->assign('video',$video);
				$this->assign('count',count($video));
				break;

		}
		//记录用户的搜索
		$search_key=Db::name('search_key')->where(['skey'=>$seartext,'uid'=>$this->fansInfo['id']])->find();
		if(empty($search_key))
		{
			Db::name('search_key')->insert(['sType'=>$sType,'skey'=>$seartext,'uid'=>$this->fansInfo['id'],'create_time'=>time()]);
		}


		$this->assign('seartext',input('param.seartext'));
		$this->assign('searchType',$this->searchType);
		$this->assign('sType',$sType);
		return $this->fetch();
	}

}