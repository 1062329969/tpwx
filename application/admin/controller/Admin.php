<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 后台基类继承顶级基类 所有的后台控制器继承该类
 */
namespace app\admin\controller;
use app\admin\model\AdminModel;
use app\admin\model\RoleModel;
use think\Session;

class Admin extends GlobalCheck
{
    //判断session是否已经过期 过期自动登录
	protected function _initialize()
	{
		$this->checkAdminLogin();
		//判断该用户是否有权限
		//读取控制器名称
		$controller=request()->controller();
		//读取方法名称
		$action=request()->action();
		//读取管理员权限
		$AdminModel=AdminModel::get(Session::get('adminid'));
		$RoleModel=RoleModel::get($AdminModel['rid']);
//        var_dump($RoleModel);die;
		$RoleModel['roles']=str_replace('_','',$RoleModel['roles']);
		$action=str_replace('_','',$action);

		if(!stristr($RoleModel['roles'],$controller.'|'.$action))
		{
			$this->error('对不起，您没有操作权限！');
		}
	}

	protected function getadmin($adminid=NULL){
	    if($adminid){
            $AdminModel=AdminModel::get($adminid);
        }else{
            $AdminModel=AdminModel::get(Session::get('adminid'));
        }
        return $AdminModel;
    }

	//图片上传方法
	protected function upload($request,$name)
	{

		$file = request()->file($name);

		if($file)
		{

			$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
			//->validate(['size'=>config('picSize'),'ext'=>config('picType')])
			if($info)
			{
				// echo str_replace('\\','/','/uploads/'.$info->getSaveName());
				return str_replace('\\','/','/uploads/'.$info->getSaveName());
			}
			else
			{
				// 上传失败获取错误信息
				$this->error($file->getError());
			}
		}
		else
		{
			return '';
		}
	}

	//多文件上传
	protected function uploadMore($request,$name,$isThumb=1)
	{

		$imgs=array();
		$files = request()->file($name);

		if(count($files)>0)
		{
		
			foreach($files as $file)
			{
				// 移动到框架应用根目录/public/uploads/ 目录下
				$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
				if($info)
				{
					$returnImg=str_replace('\\','/','/uploads/'.$info->getSaveName());
					//$returnImg='.'.$returnImg;
//					if($isThumb==1)
//					{
//						//生成缩略图
//						$returnImg=$this->thumbnail($file,$returnImg);
//					}
					$imgs[]=$returnImg;


				}
				else
				{
					$this->error($file->getError());
				}

			}
		}
		return $imgs;
	}

	//上传时生成缩略图
	protected function thumbnail($file,$filePath,$width=300,$height=300)
	{
        $pic=explode('/',$filePath);
		$picName=end($pic);
		$savePath=str_replace($picName,'m_'.$picName,$filePath);
		$image = \think\Image::open('.'.$filePath);
		$image->thumb($width,$height,\think\Image::THUMB_CENTER)->save('.'.$savePath);
		return $savePath;

		//压缩图片
		//$image = \think\Image::open('.'.$data['url']);
		// 按照原图的比例生成一个最大为300*300的缩略图并保存为thumb.png
		//$image->thumb(300,300,\think\Image::THUMB_CENTER)->save('.'.$image_path.'m_'.$image_name);

	}
}
