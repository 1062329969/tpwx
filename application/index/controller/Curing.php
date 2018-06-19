<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 养护知识
 */
namespace app\index\controller;


use app\index\model\ContAdModel;
use app\index\model\CuringImgModel;
use app\index\model\CuringModel;
use app\index\model\CommentModel;
use app\index\model\FotoplaceModel;
use think\Config;
use ApiOauth\ApiOauth;
use app\index\model\CasesRecordModel;
use app\index\model\CasesModel;
use think\Db;

class Curing extends Wap
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


	//测试首页
    public function index(){
        $tid = input('param.tid',1);//默认显示养护内容
        $id=input('param.id');
        $res = CuringModel::getAll($tid);
        $this->assign('res',$res);
        $this->assign('tid',$tid);

        $vid=0;//input('param.vid',0);
        $news=CuringModel::getAll($tid,$vid);
        //判断是一图 三图 还是视频
        foreach ($news as $key=>$val)
        {
            $news[$key]['imgs_nums']=CuringImgModel::getCountImgs($val['id']);
        }

        $this->assign('vid',$vid);
        $this->assign('news',$news);
        $this->assign('count',count($news));
        $this->assign('tid',$tid);
        $this->assign('id',$id);
        //读取推荐的
        $newsTj=CuringModel::getTj();
        $this->assign('newsTj',$newsTj);
        //读取焦点图广告图片
        $cont_ad=ContAdModel::getAll(13);
        $this->assign('cont_ad',$cont_ad);
        return $this->fetch();
    }



    //测试详情页面
    public function detail(){
        $id=input('param.id');
        if(empty($id) || !is_numeric($id))
        {
            $this->error('参数错误');
        }
        //记录足记
        $FotoplaceModel=new FotoplaceModel();
        $FotoplaceModel->save(['uid'=>$this->fansInfo['id'],'aid'=>$id,'type'=>1]);
        $news=CuringModel::getOne($id);
        //浏览次数加1
        CuringModel::where(['id'=>$id])->setInc('click');

        if(empty($news))
        {
            $this->error('参数错误');
        }

        $this->assign('news',$news);
        //读取下的图片
        $images=CuringImgModel::getAll($id);
        $this->assign('images',$images);
        //获取评论内容
        $comment=CommentModel::getAll(1,$id);
        $this->assign('comment',$comment);
        $comment2=CommentModel::getAll2(1,$id);
        $this->assign('comment2',$comment2);
        //生成分享的内容
        $this->assign('aid',$id);
        $this->assign('shareType',1);
        $this->shareTitle=$news['title'];   // 分享标题
        $this->shareDesc='';    // 分享描述
        $this->shareLink=Config::get('domain').url('detail',array('id'=>$id));// 分享链接
        $this->shareImgUrl=Config::get('domain').$news['pic'];

        $ttime=input('param.ttime',0);
        $casesRecord=CasesRecordModel::getAll($id,$ttime);
        $this->assign('casesRecord',$casesRecord);
        $this->assign('ttime',$ttime);

        $this->assign('shareTitle',$this->shareTitle);
        $this->assign('shareDesc',$this->shareDesc);
        $this->assign('shareLink',$this->shareLink);
        $this->assign('shareImgUrl',$this->shareImgUrl);

	    return $this->fetch();
    }


	//养护知识首页
	public function index_one()
	{

		$tid=input('param.tid',0);
		$vid=0;//input('param.vid',0);
		$news=CuringModel::getAll($tid,$vid);

		//判断是一图 三图 还是视频
		foreach ($news as $key=>$val)
		{
			$news[$key]['imgs_nums']=CuringImgModel::getCountImgs($val['id']);
		}
		$this->assign('news',$news);
		$this->assign('count',count($news));
		$this->assign('tid',$tid);
		$this->assign('vid',$vid);
		//读取推荐的
		$newsTj=CuringModel::getTj();
		$this->assign('newsTj',$newsTj);
		//读取焦点图广告图片
		$cont_ad=ContAdModel::getAll(13);
		$this->assign('cont_ad',$cont_ad);

		return $this->fetch();
	}


	//下拉加载
	public function nextPage()
	{
		$tid=input('param.tid',0);
		$vid=0; //input('param.vid',0);

		$offsize=input('param.offsize');

		$forum=CuringModel::getAll($tid,$vid,10,$offsize);

		if(empty($forum))
		{
			return 0;
		}

		foreach ($forum as $key=>$v)
		{
			$forum[$key]['imgs_nums']=CuringImgModel::getCountImgs($v['id']);
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

	//养护知识详细页
	public function detail_tow()
	{
		$id=input('param.id');
		if(empty($id) || !is_numeric($id))
		{
			$this->error('参数错误');
		}
        //记录足记
		$FotoplaceModel=new FotoplaceModel();
		$FotoplaceModel->save(['uid'=>$this->fansInfo['id'],'aid'=>$id,'type'=>1]);


		$news=CuringModel::getOne($id);
        //浏览次数加1
		CuringModel::where(['id'=>$id])->setInc('click');

		if(empty($news))
		{
			$this->error('参数错误');
		}

		$this->assign('news',$news);
		//读取下的图片
		$images=CuringImgModel::getAll($id);
		$this->assign('images',$images);
		//获取评论内容
		$comment=CommentModel::getAll(1,$id);
		$this->assign('comment',$comment);
		$comment2=CommentModel::getAll2(1,$id);
		$this->assign('comment2',$comment2);
		//生成分享的内容
		$this->assign('aid',$id);
		$this->assign('shareType',1);
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