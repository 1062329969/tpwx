<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 满意度调查
 */

namespace app\index\controller;


use app\index\model\InvestigationModel;
use app\index\model\InvestigationOptionModel;
use app\index\model\InvestigationResultModel;
use think\Db;

class Investigation extends Wap
{
	public function index()
	{
        $investigation=InvestigationModel::getOne();
		$investigationOptionModel=InvestigationOptionModel::getAll($investigation['id']);

		$this->assign('investigation',$investigation);
		$this->assign('investigationOptionModel',$investigationOptionModel);
		return $this->fetch();
	}

	public function add()
	{
		$data=input('param.');

		$data2['result']=json_encode($data['season']);

		$pic=array();
		if(isset($data['images']))
		{

			foreach ($data['images'] as $k=>$v)
			{
				$return=$this->saveBase64Image($v);

				if($return['code']==0)
				{

					$pic[]=$return['url'];
				}

			}
			$data2['pic']=json_encode($pic);
		}
		$data2['create_time']=time();
		$data2['iid']=$data['iid'];
		$data2['xx']=$data['xx'];
		Db::name('investigation_result')->insert($data2);

		return ['code'=>1];
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