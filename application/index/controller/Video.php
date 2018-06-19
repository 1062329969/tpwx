<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 植发视频
 */

namespace app\index\controller;


use app\index\model\CommentModel;
use app\index\model\ProjectModel;
use app\index\model\VideoModel;

class Video extends Wap
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
		$video=VideoModel::getAll($tid);
		$this->assign('video',$video);
		$this->assign('count',count($video));
		$this->assign('tid',$tid);
        //读取分类
		$contType=ProjectModel::getAll();
		$this->assign('contType',$contType);
        //当前栏目位置
		$postion=input('param.postion',0);
		$this->assign('postion',$postion);
		return $this->fetch();
	}

	public function nextPage()
	{
		$offsize=input('param.offsize');
		$tid=input('param.tid');

		$forum=VideoModel::getAll($tid,10,$offsize);

		if(empty($forum))
		{
			return 0;
		}

		$returnStr="";
		foreach ($forum as $key=>$vo)
		{

			$returnStr=$returnStr.'<a href="'.url('detail',array('id'=>$vo['id'])).'">
			    <div class="vimg">
			        <img src="'.$vo['pic'].'">
			        <div class="vvititle">'.$vo['title'].'</div>
			        <div class="vvpfnums">'.$vo['click'].'次播放</div>
			        <div class="vvpfbut"><img src="{$Think.HTML_STATIC}/images/vvpf.png"></div>
			        <div class="vvpftime">'.$vo['play_time'].'</div>
			    </div>
			    <div class="vvinfos">
			        <div class="vfbu"><img src="'.$vo['userInfo']['portrait'].'">'.$vo['userInfo']['wechaname'].'</div>
			        <div class="vshare"><img src="{$Think.HTML_STATIC}/images/hxing.png">关注 <img src="{$Think.HTML_STATIC}/images/hl.png">'.$vo['comment'].'<img src="{$Think.HTML_STATIC}/images/hshare.png"></div>
			    </div>
			    </a>';
		}

		return $returnStr;
	}

	public function detail()
	{
		$id=input('param.id');
		$video=VideoModel::getOne($id);
		if(empty($video))
		{
			$this->error('参数错误');
		}
		$this->assign('video',$video);

		//浏览数增1
		VideoModel::where('id',$id)->setInc('click',1);
        //获取评论内容
        //$comment=CommentModel::getAll(1,$id);
		//$this->assign('comment',$comment);
		$this->assign('video',$video);

		//获得该栏目下的其他视频
		$aboutvideo=VideoModel::getAll($video['pid'],10);
		$this->assign('aboutvideo',$aboutvideo);

		//读取用户是否点赞过
		//$this->assign('praise',$this->getPraise($id,$this->fansInfo['id'],1));
		return $this->fetch();
	}


	public function comment()
	{
		$id=input('param.id');
		$video=VideoModel::getOne($id);
		if(empty($video))
		{
			$this->error('参数错误');
		}
		$this->assign('video',$video);

		//浏览数增1
		VideoModel::where('id',$id)->setInc('click',1);

		//获取评论内容
		$comment=CommentModel::getAll(7,$id);
		$this->assign('comment',$comment);
		$comment2=CommentModel::getAll2(7,$id);
		$this->assign('comment2',$comment2);

		//读取用户是否点赞过
		//$this->assign('praise',$this->getPraise($id,$this->fansInfo['id'],1));
		return $this->fetch();
	}

}