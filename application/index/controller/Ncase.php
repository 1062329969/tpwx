<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 内部案例
 */
namespace app\index\controller;

use app\index\model\NcaseImgsModel;
use app\index\model\NcaseModel;
use app\index\model\ProjectModel;
use think\Db;


class Ncase extends Wap
{

	protected function _initialize()
	{
		//获得授权
		$this->getUserInfo();
		$this->assign('fansInfo',$this->fansInfo);

	}

	public function index()
	{
		$tid=input('param.tid',0);
		$news=NcaseModel::getAll($tid);

		//判断是一图 三图 还是视频
		foreach ($news as $key=>$val)
		{
			$news[$key]['imgs_nums']=NcaseImgsModel::getCountImgs($val['id']);
		}

		$this->assign('news',$news);
		$this->assign('count',count($news));
		//读取推荐的
		$newsTj=NcaseModel::getTj();
		$this->assign('newsTj',$newsTj);

		$contType=ProjectModel::getAll();
		$this->assign('contType',$contType);
		//获得选中的位置
		$postion=input('param.postion',0);
		$this->assign('postion',$postion);
		$this->assign('tid',$tid);

		return $this->fetch();
	}

	//下拉加载
	public function nextPage()
	{
		$tid=input('param.tid',0);
		$vid=input('param.vid',0);

		$offsize=input('param.offsize');

		$forum=NcaseModel::getAll($tid,10,$offsize);

		if(empty($forum))
		{
			return 0;
		}

		foreach ($forum as $key=>$v)
		{
			$forum[$key]['imgs_nums']=NcaseImgsModel::getCountImgs($v['id']);
		}

		$returnStr="";
		foreach ($forum as $key=>$value)
		{

			$returnStr=$returnStr."<a href='".url('detail',array('id'=>$value['id']))."''>";

			if($value['is_video']==1)
			{
				$returnStr=$returnStr.'<div class="tvideo">
			        <div class="title">'.$value['title'].'</div>
			        <div class="pics">
			            <img src="'.str_replace('m_','',$value['pic']).'">
			            <div class="spbf"></div>
			            <span class="pftime">'.$value['play_time'].'</span>
			        </div>
			        <div class="infos">
			            <span>'.$value['author'].'</span>
			            <span><img src="'.HTML_STATIC.'/images/plnum.png">'.$value['pinglun'].'</span>
			            <span>'.$value['create_time'].'</span>
			        </div>
			    </div>';
			}
			elseif($value['imgs_nums']==1)
			{
				$returnStr=$returnStr.'<div class="tpic1">
			        <div class="tp1left">
			            <div class="title">'.$value['foname'].'</div>
			            <div class="infos">
			                <span>'.$value['userInfo']['wechaname'].'</span>
			                <span><img src="'.HTML_STATIC.'/images/plnum.png">'.$value['comment'].'</span>
			                <span>'.$value['create_time'].'</span>
			            </div>
			        </div>
			        <div class="pics">
			            <img src="'.$value['pic'].'">
			        </div>
			    </div>';
			}
			elseif($value['imgs_nums']>=2)
			{
				$returnStr=$returnStr.'<div class="tpic3">
			        <div class="title">'.$value['foname'].'</div>
			        <div class="pics">';
				foreach ($value['newsImgs'] as $k=>$v)
				{
					$returnStr=$returnStr.'<div class="picll"><img src="'.$v['pic'].'"></div>';
				}

				$returnStr=$returnStr.'</div>';
				$returnStr=$returnStr.'<div class="infos">
			            <span>'.$value['userInfo']['wechaname'].'</span>
			            <span><img src="'.HTML_STATIC.'/images/plnum.png">'.$value['comment'].'</span>
			            <span>'.$value['create_time'].'</span>
			        </div>
			    </div>';
			}
			else
			{
				$returnStr=$returnStr.'<div class="tpic3">
			        <div class="title">'.$value['foname'].'</div>
			        <div class="infos">
			            <span>'.$value['userInfo']['wechaname'].'</span>
			            <span><img src="'.HTML_STATIC.'/images/plnum.png">'.$value['comment'].'</span>
			            <span>'.$value['create_time'].'</span>
			        </div>
			    </div>';
			}

			$returnStr=$returnStr.'</a>';


		}

		return $returnStr;
	}

	public function detail()
	{
		$id=input('param.id');
		if(empty($id) || !is_numeric($id))
		{
			$this->error('参数错误');
		}

		$news=NcaseModel::getOne($id);
		//浏览次数加1
		NcaseModel::where(['id'=>$id])->setInc('click');

		if(empty($news))
		{
			$this->error('参数错误');
		}
		//读取下的图片
		$images=NcaseImgsModel::getAll($id);


		if(Db::name('ncase_dianzan')->where(['aid'=>$id,'uid'=>$this->fansInfo['id']])->find())
		{
			$is_dz=1;
		}
		else
		{
			$is_dz=0;
		}

		$this->assign('is_dz',$is_dz);
		$this->assign('images',$images);
		$this->assign('news',$news);

		return $this->fetch();
	}

	public function dianzan()
	{
		$id=input('param.id');
		if(Db::name('ncase_dianzan')->where(['aid'=>$id,'uid'=>$this->fansInfo['id']])->find())
		{
			return ['code'=>0,'msg'=>'你已经点过赞了'];
		}

		Db::name('ncase_dianzan')->insert(['aid'=>$id,'uid'=>$this->fansInfo['id']]);
		NcaseModel::where(['id'=>$id])->setInc('dianzan');
		return ['code'=>1];
	}

}