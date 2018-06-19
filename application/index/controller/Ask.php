<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 问答管理控制器
 */

namespace app\index\controller;

use app\index\model\AnswerImgsModel;
use app\index\model\AnswerModel;
use app\index\model\AskImgsModel;
use app\index\model\AskModel;
use app\index\model\CommentModel;
use app\index\model\ContAdModel;
use app\index\model\FotoplaceModel;
use app\index\model\ProjectModel;
use think\Config;
use ApiOauth\ApiOauth;
use think\Session;

class Ask extends Wap
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

	//问答首页
	public function index()
	{
		$tid=input('param.tid',0);
		$type=input('param.type',0);
		//读取问答
		$ask=AskModel::getAll($tid);
		//获取点赞
        $asky = AskModel::getAll('praise');
        if($asky < 0){
            $this->error('请勿重复点赞');
        }
		//读取问答下的图片
		foreach($ask as $key=>$v)
		{
			$ask[$key]['images']=AskImgsModel::getAll($v['id']);
			$ask[$key]['imgs_nums']=AskImgsModel::getCountImgs($v['id']);
		}
		$this->assign('ask',$ask);
		$this->assign('count',count($ask));
		$this->assign('asky',$asky);
		//读取问答的分类
		$contType=ProjectModel::getAll();
		$this->assign('contType',$contType);
		$this->assign('tid',$tid);
		//获得选中的位置
		$postion=input('param.postion',0);
		$this->assign('postion',$postion);
		$this->assign('type',$type);
		return $this->fetch();
	}

	//下拉加载
	public function nextPage()
	{

		$offsize=input('param.offsize');
		$tid=input('param.tid');

		$forum=AskModel::getAll($tid,10,$offsize);

		if(empty($forum))
		{
			return 0;
		}

		foreach ($forum as $key=>$v)
		{
			$forum[$key]['images']=AskImgsModel::getAll($v['id']);
		}

		$returnStr="";
		foreach ($forum as $key=>$value)
		{

			$returnStr=$returnStr."<a href='".url('detail',array('id'=>$value['id']))."''>";

			$returnStr=$returnStr.'<div class="askmain">
			      <div class="atxd">
			            <div class="tx"><img src="'.get_wxheadimg($value['userInfo']['portrait'],132).'" /> '.$value['userInfo']['wechaname'].'</div>
			            <div class="dz"><img src="'.HTML_STATIC.'/images/adz.png">'.$value['praise'].'</div>
			      </div>
			      <div class="asktitle"><span>问</span>'.$value['qtitle'].'</div>
			      <div class="askans">'.$value['question'].'...<span>展开</span></div>
			      <div class="askimgs">';

			         foreach ($value['images'] as $k=>$v)
			         {
				         $returnStr=$returnStr.'<img src="'.$v['pic2'].'">';

			         }

			      $returnStr=$returnStr.'</div>
			      <div class="zdask">查看3个折叠回答 ></div>
			</div>';

			$returnStr=$returnStr.'</a>';


		}

		return $returnStr;
	}

    //问答详细页
	public function detail()
	{
        $id=input('param.id');
		if(empty($id) || !is_numeric($id))
		{
			$this->error('参数错误');
		}

		$ask=AskModel::getOne($id);

		if(empty($ask))
		{
			$this->error('参数错误');
		}

		//浏览数增1
		AskModel::where('id',$id)->setInc('click',1);
		//记录足记
		$FotoplaceModel=new FotoplaceModel();
		$FotoplaceModel->save(['uid'=>$this->fansInfo['id'],'aid'=>$id,'type'=>5]);

		$this->assign('ask',$ask);
		//读取问答下的图片
		$images=AskImgsModel::getAll($id);
		$this->assign('images',$images);
		//读取问答的评论
		//$comment=CommentModel::getAll(5,$id);
		//$this->assign('comment',$comment);
		//$comment2=CommentModel::getAll2(5,$id);
		//$this->assign('comment2',$comment2);
		//获得下一个问答的id
		$nextId=AskModel::getNext($id);
		$this->assign('nextId',$nextId['id']);
        //读取详细页的广告
		$askAd=ContAdModel::getAll(11);
		$this->assign('askAd',$askAd);
        //读取问答的回答内容
		$answer=AnswerModel::getAll($id);
		foreach ($answer as $key=>$v)
		{
			$answer[$key]['images']=AnswerImgsModel::getAll($v['id']);
		}
		$this->assign('answer',$answer);
		$this->assign('answerCount',count($answer));

		//生成分享的内容
		$this->assign('aid',$id);
		$this->assign('shareType',5);
		$this->shareTitle=$ask['qtitle'];   // 分享标题
		$this->shareDesc='';    // 分享描述
		$this->shareLink=Config::get('domain').url('detail',array('id'=>$id));// 分享链接
		$this->shareImgUrl='';     //

		$this->assign('shareTitle',$this->shareTitle);
		$this->assign('shareDesc',$this->shareDesc);
		$this->assign('shareLink',$this->shareLink);
		$this->assign('shareImgUrl',$this->shareImgUrl);
		return $this->fetch();
	}

	//提交问答
	public function add()
	{
		$request=request();
		if($request->method()=='POST')
		{
            $data=input('post.');
			$ask=new AskModel();

			$data['uid']=$this->fansInfo['id'];
			$data['question']=$data['text'];
			$data['pid']=$data['tid'];

			if($ask->allowField(true)->save($data))
			{
				if(isset($data['images']))
				{

					foreach ($data['images'] as $k=>$v)
					{
						$return=$this->saveBase64Image($v);

						if($return['code']==0)
						{
							$AskImgsModel=new AskImgsModel();
							$AskImgsModel->save(['aid'=>$ask->id,'pic'=>$return['url']]);
						}

					}
				}
				return ['code'=>1,'id'=>$ask->id];

			}

		}
		else
		{
			$this->checkLogin();
			//读取项目分类
            $contType=ProjectModel::getAll();
			$this->assign('contType',$contType);
			return $this->fetch();
		}
	}


	//提交回答
	public function answer()
	{
		$request=request();
		$aid=input('param.aid');
		if($request->method()=='POST')
		{
			$data=input('post.');
			$answer=new AnswerModel();
			if($answer->allowField(true)->save(['uid'=>$this->fansInfo['id'],'content'=>$data['text'],'aid'=>$aid]))
			{
				if(isset($data['images']))
				{

					foreach ($data['images'] as $k=>$v)
					{
						$return=$this->saveBase64Image($v);

						if($return['code']==0)
						{
							$AnswerImgsModel=new AnswerImgsModel();
							$AnswerImgsModel->save(['aid'=>$answer->id,'pic'=>$return['url']]);
						}

					}
				}
				return ['code'=>1,'id'=>$aid];
			}
		}
		else
		{
			$this->checkLogin();
			$ask=AskModel::getOne($aid);
			//读取问答下的图片
			$images=AskImgsModel::getAll($aid);
			$this->assign('images',$images);
			$this->assign('ask',$ask);
			$this->assign('aid',$aid);
			//读取项目分类
			return $this->fetch();
		}
	}


	/**
	 * 保存64位编码图片
	 */

	public function saveBase64Image($base64_image_content){

		if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result))
		{

			//图片后缀
			$type = $result[2];

			//保存位置--图片名
			$image_name=date('His').str_pad(mt_rand(1, 99999999), 5, '0', STR_PAD_LEFT).".".$type;
			$image_url = '/uploads/'.date('Ymd').'/'.$image_name;
			$image_path = '/uploads/'.date('Ymd').'/';
			if(!is_dir(dirname('.'.$image_url)))
			{
				mkdir(dirname('.'.$image_url),0700,true);
			}

			//解码
			$decode=base64_decode(str_replace($result[1], '', $base64_image_content));
			if (file_put_contents('.'.$image_url, $decode))
			{
				$data['code']=0;
				$data['imageName']=$image_name;
				$data['url']=$image_url;
				$data['msg']='保存成功！';

				//压缩图片
				$image = \think\Image::open('.'.$data['url']);
                // 按照原图的比例生成一个最大为300*300的缩略图并保存为thumb.png
				$image->thumb(300,300,\think\Image::THUMB_CENTER)->save('.'.$image_path.'m_'.$image_name);
				$data['url']=$image_path.'m_'.$image_name;
			}else{
				$data['code']=1;
				$data['imgageName']='';
				$data['url']='';
				$data['msg']='图片保存失败！';
			}
		}else{
			$data['code']=1;
			$data['imgageName']='';
			$data['url']='';
			$data['msg']='base64图片格式有误！';


		}
		return $data;


	}

}