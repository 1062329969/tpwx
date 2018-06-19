<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 头发养护知识
 */
namespace app\index\controller;


use app\index\model\FotoplaceModel;
use app\index\model\HairCuringImgModel;
use app\index\model\HairCuringModel;
use app\index\model\CommentModel;
use think\Config;
use ApiOauth\ApiOauth;

class HairCuring extends Wap
{
	protected $timeStamp;
	protected $nonceStr;
	protected $signature;
	protected $shareTitle;
	protected $shareDesc;
	protected $shareLink;
	protected $shareImgUrl;

	protected function _initialize()
	{
		//获得授权
		$this->getUserInfo();
		$this->assign('fansInfo',$this->fansInfo);

		//微信jdk 验证
		$apiOauth=new ApiOauth();
		$ticket = $apiOauth->getJsApiTicket(Config::get('Appid'),Config::get('Appsecret'));
		$this->timeStamp = time();
		$this->nonceStr  = rand(100000,999999);
		$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		//获得jsdk签名
		$this->signature = $apiOauth->getSignature($this->nonceStr,$ticket,$this->timeStamp,$url);
		$this->assign('timeStamp',$this->timeStamp);
		$this->assign('nonceStr',$this->nonceStr);
		$this->assign('signature',$this->signature);
	}

	public function index()
	{
		$vid=0; input('param.vid',0);
		$news=HairCuringModel::getAll($vid);

		//判断是一图 三图 还是视频
		foreach ($news as $key=>$val)
		{
			$news[$key]['imgs_nums']=HairCuringImgModel::getCountImgs($val['id']);
		}

		$this->assign('news',$news);
		$this->assign('count',count($news));
		$this->assign('vid',$vid);
		//读取推荐的
		$newsTj=HairCuringModel::getTj();
		$this->assign('newsTj',$newsTj);

		return $this->fetch();
	}

	//下拉加载
	public function nextPage()
	{
		$vid=0;//input('param.vid',0);

		$offsize=input('param.offsize');

		$forum=HairCuringModel::getAll($vid,10,$offsize);

		if(empty($forum))
		{
			return 0;
		}

		foreach ($forum as $key=>$v)
		{
			$forum[$key]['imgs_nums']=HairCuringImgModel::getCountImgs($v['id']);
		}

		$returnStr="";
		foreach ($forum as $key=>$value)
		{

			$returnStr="<a href='".url('detail',array('id'=>$value['id']))."''>";

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
				foreach ($value['forumImgs'] as $k=>$v)
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

		$news=HairCuringModel::getOne($id);
		if(empty($news))
		{
			$this->error('参数错误');
		}
        //浏览次数加1
		HairCuringModel::where(['id'=>$id])->setInc('click');
		//记录足记
		$FotoplaceModel=new FotoplaceModel();
		$FotoplaceModel->save(['uid'=>$this->fansInfo['id'],'aid'=>$id,'type'=>2]);


		$this->assign('news',$news);
		//读取下的图片
		$images=HairCuringImgModel::getAll($id);
		$this->assign('images',$images);
		//获取评论内容
		$comment=CommentModel::getAll(2,$id);
		$this->assign('comment',$comment);
		$comment2=CommentModel::getAll2(2,$id);
		$this->assign('comment2',$comment2);
		//生成分享的内容
		$this->assign('aid',$id);
		$this->assign('shareType',2);
		$this->shareTitle=$news['title'];   // 分享标题
		$this->shareDesc='';    // 分享描述
		$this->shareLink=Config::get('domain').url('detail',array('id'=>$id));// 分享链接
		$this->shareImgUrl=Config::get('domain').$news['pic'];     //

		$this->assign('shareTitle',$this->shareTitle);
		$this->assign('shareDesc',$this->shareDesc);
		$this->assign('shareLink',$this->shareLink);
		$this->assign('shareImgUrl',$this->shareImgUrl);

		return $this->fetch();
	}

}