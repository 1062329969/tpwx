<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 广告管理
 */

namespace app\admin\controller;
use app\admin\model\ContAdModel;
use think\Db;

class ContAd extends Admin
{   

	private $addPosition;   //广告的位置

	public function _initialize()
	{
		parent::_initialize();

		$this->addPosition[0]['id']=1;
		$this->addPosition[0]['title']='首页推荐商品图';
		$this->addPosition[0]['size']='640*392';

		$this->addPosition[4]['id']=5;
		$this->addPosition[4]['title']='首页文字广告';
		$this->addPosition[4]['size']='标题即广告内容';


		$this->addPosition[2]['id']=3;
		$this->addPosition[2]['title']='帖子内容底部广告';
		$this->addPosition[2]['size']='640*214';

		$this->addPosition[3]['id']=4;
		$this->addPosition[3]['title']='案例详细页文字广告';
		$this->addPosition[3]['size']='标题即广告内容';

		$this->addPosition[1]['id']=2;
		$this->addPosition[1]['title']='商城广告活动';
		$this->addPosition[1]['size']='750*336';

		//$this->addPosition[5]['id']=6;
		//$this->addPosition[5]['title']='商城广告活动2';
		//$this->addPosition[5]['size']='373*165';

		//$this->addPosition[6]['id']=7;
		//$this->addPosition[6]['title']='商城广告活动3';
		//$this->addPosition[6]['size']='373*165';

		$this->addPosition[13]['id']=14;
		$this->addPosition[13]['title']='商城首页头部广告';
		$this->addPosition[13]['size']='640*392';

		$this->addPosition[7]['id']=8;
		$this->addPosition[7]['title']='商城首页广告左';
		$this->addPosition[7]['size']='371*188';

		$this->addPosition[8]['id']=9;
		$this->addPosition[8]['title']='商城首页广告右';
		$this->addPosition[8]['size']='371*188';

		$this->addPosition[14]['id']=15;
		$this->addPosition[14]['title']='商城首页中间广告';
		$this->addPosition[14]['size']='640*250';

		$this->addPosition[9]['id']=10;
		$this->addPosition[9]['title']='我的中心文字广告';
		$this->addPosition[9]['size']='标题即广告内容';

		$this->addPosition[10]['id']=11;
		$this->addPosition[10]['title']='商城下底部广告';
		$this->addPosition[10]['size']='640*214';

		$this->addPosition[11]['id']=12;
		$this->addPosition[11]['title']='社区首页广告';
		$this->addPosition[11]['size']='640*392';

		$this->addPosition[12]['id']=13;
		$this->addPosition[12]['title']='养护首页广告';
		$this->addPosition[12]['size']='640*409';


        $this->addPosition[20]['id']=20;
        $this->addPosition[20]['title']='头条顶部广告';
        $this->addPosition[20]['size']='640*409';

        $this->addPosition[21]['id']=21;
        $this->addPosition[21]['title']='头条-为你推荐广告';
        $this->addPosition[21]['size']='头条-为你推荐广告';


	}

	public function index()
	{
	    $cont_ad=ContAdModel::getAll(15);
	    $this->assign('cont_ad',$cont_ad);
	    return $this->fetch();
	}

	public function add()
	{
	    $request=request();
		if($request->method()=='POST')
		{
		    $data=input('post.');
			$result=$this->validate($data,'ValidateClass.CK13');
			if(true !== $result)
			{
				// 验证失败 输出错误信息
				$this->error($result);
			}

			//上传图片
			$pic=$this->upload($request,'pic');
			if(!empty($pic))
			{
				$data['pic']=$pic;
			}

			$cont_ad=new ContAdModel();
	        if($cont_ad->allowField(true)->save($data))
	        {
	           $this->success('添加成功',url('index'));
	        }
	        else
	        {
	             $this->error('添加失败',url('index'));
	        }
		}
		else
		{

			$position=$this->addPosition;
		    $this->assign('position',$position);
			return $this->fetch();
		}
	}

	public function alter()
	{

	    $request=request();

		if($request->method()=='POST')
		{
		    $data=input('post.');

			$result=$this->validate($data,'ValidateClass.CK13');
			if(true !== $result)
			{
				// 验证失败 输出错误信息
				$this->error($result);
			}

			//上传图片
			$pic=$this->upload($request,'pic');
			if(!empty($pic))
			{
				$data['pic']=$pic;
			}
			else
			{
				unset($data['pic']);
			}

			$cont_ad=new ContAdModel();
			if($cont_ad->allowField(true)->save($data,['id'=>$data['id']]))
			{
				$this->success('修改成功',url('index'));
			}
			else
			{
				$this->error('修改失败',url('index'));
			}

		}
		else
		{
		    $id=input('param.id');
			$cont_ad=ContAdModel::get($id);
		    $this->assign('cont_ad',$cont_ad);

			$position=$this->addPosition;
			$this->assign('position',$position);
		    return $this->fetch();
		}

	}

	public function del()
	{

	     $id=input('param.id');
		 $cont_ad=Db::name('cont_ad')->where('id',$id)->update(array('del'=>1));
	     if($cont_ad)
		 {
		     echo '{"code":"1"}';
		 }
		 else
		 {
			 echo '{"code":"0"}';
		 }

	}

}