<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 商品管理控制器
 */
namespace app\admin\controller;


use app\admin\model\GoodsImgsModel;
use app\admin\model\GoodsModel;
use app\admin\model\GoodsTypeModel;
use app\admin\model\DoctorModel;
use app\admin\model\MailTempModel;
use think\Db;

class Goods extends Admin
{


	public function _initialize()
	{
		parent::_initialize();
	}


	public function index()
    {
    	$goods=GoodsModel::getAll();
	    $this->assign('goods',$goods);
    	return $this->fetch();
    }

    public function add()
    {
	    $request=request();
	    if($request->method()=='POST')
	    {
            $data=input('post.');
		    $result =$this->validate($data,'ValidateClass.CK12');
		    if(true !== $result)
		    {
			    // 验证失败 输出错误信息
			    $this->error($result);
		    }

		    if(!empty($data['doc_ids']))
		    {
			    $data['doc_ids']=implode(',',$data['doc_ids']);
		    }

		    //上传图片
		    $data['pic']=$this->upload($request,'pic');
			//上传图片
			$data['pic3']=$this->upload($request,'pic3');
		    //$data['pic2']=$this->upload($request,'pic2');



            //获得规格和规格价格
		    $guige=[];
		    foreach ($data['gdgg'] as $key=>$value)
		    {
			    $value=trim($value);
		    	if(!empty($value))
			    {
				    $guige[$key]['gg']=$value;
				    if(empty($data['gdjg'][$key]) || $data['gdjg'][$key]<0)
				    {
					    $this->error('请输入正确的规格价格！');
				    }
				    else
				    {
					    $guige[$key]['ggprice']=$data['gdjg'][$key];
				    }
			    }

		    }
		    if(!empty($guige))
		    {
			    $data['gg']=json_encode($guige);
		    }
		    else
		    {
			    $data['gg']='';
		    }


			if(empty($data['default_img']))
			{
				$data['default_img']=0;
			}

			$key=0;  //默认图片的位置
			$imgs=array();
			$files = request()->file('pic2');
			if(count($files)>0)
			{
				foreach($files as $file)
				{

					// 移动到框架应用根目录/public/uploads/ 目录下
					$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
//                    var_dump($info->getSaveName());die;
					if($info)
					{

						$returnImg=str_replace('\\','/','/uploads/'.$info->getSaveName());
//						//生成缩略图
//						$returnImg=$this->thumbnail($file,$returnImg);
//
//						if($key==$data['default_img'])
//						{
//							$data['pic2']=$returnImg;
//						}

						$imgs[]=$returnImg;
					}
					else
					{
						$this->error($file->getError());
					}

					$key=$key+1;

				}
			}


		    $goods=new GoodsModel();
		    if($goods->allowField(true)->save($data))
		    {

				//保存多图
				if(count($imgs)>0)
				{
					foreach($imgs as $k=>$v)
					{
						$imgsModel=new GoodsImgsModel();
						$imgsModel->save(['aid'=>$goods->id,'pic'=>$v]);
					}
				}

				$this->success('添加成功',url('index'));
		    }
		    else
		    {
			    $this->error('添加失败',url('index'));
			}
	    }
	    else
	    {
		    //读取商品分类
            $goodsType=GoodsTypeModel::getAll();
		    $this->assign('goodsType',$goodsType);
		    //读取邮费模板
		    $mailTemp=MailTempModel::getAll();
		    $this->assign('mailTemp',$mailTemp);
	    	return $this->fetch();
	    }
    }

    public function alter()
    {
	    $request=request();
	    if($request->method()=='POST')
	    {
		    $data=input('post.');
		    if(empty($data['tj']))
		    {
			    $data['tj']=0;
		    }

			if(empty($data['stj']))
			{
				$data['stj']=0;
			}

		    if(empty($data['jd']))
		    {
			    $data['jd']=0;
		    }

		    if(empty($data['is_ypq']))
		    {
			    $data['is_ypq']=0;
		    }

		    if(empty($data['is_yhq']))
		    {
			    $data['is_yhq']=0;
		    }

		    if(!empty($data['doc_ids']))
		    {
			    $data['doc_ids']=implode(',',$data['doc_ids']);
		    }
		    else
		    {
			    $data['doc_ids']=0;
		    }

		    //$result =$this->validate($data,'ValidateClass.CK12');
		    //if(true !== $result)
		    //{
			    // 验证失败 输出错误信息
			    //$this->error($result);
		    //}

		    //上传图片

		    $data['pic']=$this->upload($request,'pic');
			
			$data['pic3']=$this->upload($request,'pic3');

		    //$data['pic2']=$this->upload($request,'pic2');

		    if(empty($data['pic']))
		    {
			    unset($data['pic']);
		    }
			if(empty($data['pic3']))
		    {
			    unset($data['pic3']);
		    }


		    //获得规格和规格价格
		    $guige=[];
		    foreach ($data['gdgg'] as $key=>$value)
		    {
			    $value=trim($value);
			    if(!empty($value))
			    {
				    $guige[$key]['gg']=$value;
				    if(empty($data['gdjg'][$key]) || $data['gdjg'][$key]<0)
				    {
					    $this->error('请输入正确的规格价格！');
				    }
				    else
				    {
					    $guige[$key]['ggprice']=$data['gdjg'][$key];
				    }
			    }

		    }
		    if(!empty($guige))
		    {
			    $data['gg']=json_encode($guige);
		    }
		    else
		    {
			    $data['gg']='';
		    }

			//多图上传
			$imgs=array();
			$imgs=$this->uploadMore($request,'pic2');

		    $goods=new GoodsModel();
		    if($goods->allowField(true)->save($data,['id'=>$data['id']]))
		    {
				//保存多图
				if(count($imgs)>0)
				{
					foreach($imgs as $k=>$v)
					{
						$imgsModel=new GoodsImgsModel();
						$imgsModel->save(['aid'=>$goods->id,'pic'=>$v]);
					}
				}

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
		    //读取商品分类
		    $goodsType=GoodsTypeModel::getAll();
		    $this->assign('goodsType',$goodsType);
		    $goods=GoodsModel::get($id);
		    $this->assign('goods',$goods);
		    //读取商品的规格
		    if(empty($goods['gg']))
		    {
			    $this->assign('gg','');
		    }
		    else
		    {
			    $this->assign('gg',json_decode($goods['gg'],true));
		    }

			//读取该商品的所有展示图
			$goodsImgs=GoodsImgsModel::getAll($id);
			$this->assign('goodsImgs',$goodsImgs);

            //读取邮费模板
		    $mailTemp=MailTempModel::getAll();
		    $this->assign('mailTemp',$mailTemp);
	    	return $this->fetch();
	    }
    }


	//删除商品展示图片
	public function delImg()
	{
		$imgurl=input('post.imgurl');
		$id=input('post.id');
		$result = @unlink ('.'.$imgurl);
		GoodsImgsModel::where(['id'=>$id,'pic'=>$imgurl])->delete();
		return ['code'=>1];

	}

	public function del()
	{
		$id=input('param.id');

		$goods=Db::name('goods')->where('id',$id)->update(array('del'=>1));
		if($goods)
		{
			return array('code'=>'1');
		}
		else
		{
			return array('code'=>'0');
		}
	}
}