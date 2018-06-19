<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 前台的基类
 */
namespace app\index\controller;
use app\index\model\CollectionModel;
use app\index\model\MsgModel;
use app\index\model\PraiseRegModel;
use think\Controller;
use ApiOauth\ApiOauth;
use think\Db;
use think\Request;
use think\Session;

class Wap extends Controller
{
    public $fansInfo;
	//网页授权函数

	public function getUserInfo()
	{
		$request=request();
		$controller=$request->instance()->controller();
		$this->assign('controller',$controller);
		//Session::clear();
		if(!Session::has('openid'))
		{
			$info['appid']=config('Appid');
			$info['appsecret']=config('Appsecret');
			
			$ApiOauth=new ApiOauth();
			$ApiOauthArr=$ApiOauth->webOauth($info,'snsapi_userinfo');
			//获取用户信息
			$getUserInfo=$ApiOauth->get_fans_info($ApiOauthArr['access_token'],$ApiOauthArr['openid']);
			$user_info=Db::name('user_info')->where('wecha_id',$getUserInfo['openid'])->find();
			if(empty($user_info))
			{
				$data['wechaname']=filterEmoji($getUserInfo['nickname']);
				$data['wecha_id']=$getUserInfo['openid'];
				if($getUserInfo['sex']==1)
				{
					$data['sex']='男';
				}
				elseif($getUserInfo['sex']==2)
				{
					$data['sex']='女';
				}
				else
				{
					$data['sex']='未知';
				}
				//$data['sex']=$getUserInfo['sex']?'男':'女';
				$data['portrait']=$getUserInfo['headimgurl'];
				$data['city']=$getUserInfo['province'].$getUserInfo['city'];
				$data['regtime']=time();
				Db::name('user_info')->insert($data);
			}
//			else
//			{
//				$data['wechaname']=filterEmoji($getUserInfo['nickname']);
//				if($getUserInfo['sex']==1)
//				{
//					$data['sex']='男';
//				}
//				elseif($getUserInfo['sex']==2)
//				{
//					$data['sex']='女';
//				}
//				else
//				{
//					$data['sex']='未知';
//				}
//
//				$data['portrait']=$getUserInfo['headimgurl'];
//				$data['city']=$getUserInfo['province'].$getUserInfo['city'];
//				Db::name('user_info')->where('wecha_id',$getUserInfo['openid'])->update($data);
//			}
			
			Session::set('openid',$getUserInfo['openid']);
		}


		//获得用户的信息
		$user_info=Db::name('user_info')->where('wecha_id',Session::get('openid'))->find();
		$this->fansInfo['id']=$user_info['id'];
		$this->fansInfo['wechaname']=$user_info['wechaname'];
		$this->fansInfo['wecha_id']=$user_info['wecha_id'];
		$this->fansInfo['portrait']=$portrait=$user_info['portrait'];
		$portrait = substr($portrait,0,strlen($portrait)-1);
		
		$this->fansInfo['portrait64']=$portrait.'64';
		$this->fansInfo['portrait96']=$portrait.'96';
		$this->fansInfo['portrait132']=$portrait.'132';

		$this->fansInfo['sex']=$user_info['sex'];
		$this->fansInfo['integral']=$user_info['integral'];
		$this->fansInfo['truename']=$user_info['truename'];
		$this->fansInfo['city']=$user_info['city'];
		$this->fansInfo['phonenum']=$user_info['phonenum'];

		$this->fansInfo['birthday']=$user_info['birthday'];
        $this->fansInfo['company']=$user_info['company'];
        $this->fansInfo['position']=$user_info['position'];

		$this->fansInfo['id_card']=$user_info['id_card'];
		$this->fansInfo['area']=$user_info['area'];
		$this->fansInfo['area2']=$user_info['area2'];
		$this->fansInfo['address']=$user_info['address'];
		$this->fansInfo['tel2']=$user_info['tel2'];
		$this->fansInfo['email']=$user_info['email'];
		$this->fansInfo['stage_type']=$user_info['stage_type'];
		$this->fansInfo['balance']=$user_info['balance'];
		//获得用户的未读消息数
		$this->fansInfo['msgNums']=MsgModel::getNoRead($user_info['id']);
		//返回的url地址
		if(empty($_SERVER["HTTP_REFERER"]))
		{
			$this->fansInfo['backUrl']='#';
		}
		else
		{
			$this->fansInfo['backUrl']=$_SERVER["HTTP_REFERER"];
		}


	}

	public function checkLogin()
	{
		$request = Request::instance();
		$backGoUrl=config('domain').$request -> url();
		Session::set('backGoUrl',$backGoUrl);
		if(!Session::has('loginId'))
		{
			$this->redirect(url('member/alllogin'));
		}
	}

	public function checkLogin2()
	{
		$backGoUrl=$_SERVER["HTTP_REFERER"];
		Session::set('backGoUrl',$backGoUrl);
		if(!Session::has('loginId'))
		{
			$this->redirect(url('member/login'));
		}
	}

	public function checkLoginAjax()
	{

		$backGoUrl=$_SERVER["HTTP_REFERER"];
		Session::set('backGoUrl',$backGoUrl);
		if(!Session::has('loginId'))
		{
			return exit(json_encode(['code'=>'-1']));
		}

	}

	public function filter($content){
        $blockwords = Db::name('blockwords')->select();
        $badword = array_column($blockwords, 'b_content');
        $badword1 = array_combine($badword,array_fill(0,count($badword),'**'));
        if(!trim($content)){
            return false;
        }else{
            return strtr($content, $badword1);
        }
    }

}