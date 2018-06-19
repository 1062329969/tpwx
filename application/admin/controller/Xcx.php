<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * 小程序 这个是王总那边的不是一个合同
 */
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Session;
error_reporting(E_ERROR | E_WARNING | E_PARSE);
class Xcx extends Admin
{


	public $limitSize = 15;

	public function _initialize()
	{
		
	}

	public function index(){
		return 'index';
	}

	public function ad_cate(){
		$where = '1=1';

		$data = Db::name('xcx_ad_cate')->where($where)->order('cate_id desc')->paginate($this->limitSize);
		$this->assign('data',$data);
		return $this->fetch();
	}

	public function ad_cate_add($cate_id = null){

		$request=request();
		if($request->method()=='POST'){
			$data=input('post.');
			$cate_id = $data['cate_id'];
			$cate_name = $data['cate_name'];
			if($cate_id){
				Db::name('xcx_ad_cate')->where(['cate_id'=>$cate_id])->update(['cate_name'=>$cate_name]);
				$this->success('修改成功','ad_cate');
			} else {
				$cate_id = Db::name('xcx_ad_cate')->insert(['cate_name'=>$cate_name]);
				if($cate_id){
					$this->success('添加成功','ad_cate');
				}else{
					$this->error('添加失败');
				}
			}
		}
		    
		if($cate_id){
			$data = Db::name('xcx_ad_cate')->find($cate_id);
			$this->assign('data',$data);
		}
		
		return $this->fetch();
	}

	public function ad_cate_del(){
		if(request()->ispost()){
			$cate_id = input('cate_id');
			$del = Db::name('xcx_ad_cate')->where(['cate_id'=>$cate_id])->delete();
			if($del){
				$this->json(1 , '删除成功');
			}else{
				$this->json(1 , '删除失败');
			}
		}
	}

	public function ad_index(){
		$where = '1=1';
		$data = Db::name('xcx_ad')->field('Z.*,C.cate_name')->alias('Z')->join('xcx_ad_cate C','C.cate_id=Z.cate_id','LEFT')->where($where)->order('Z.id desc')->paginate($this->limitSize);
		$this->assign('data',$data);
		return $this->fetch();
	}

	public function ad_add($id = null){
		if(request()->ispost()){
			$post = input('post.');
			$id = $post['id'];
			unset($post['id']);
			unset($post['__token__']);
			if($id){
				Db::name('xcx_ad')->where(['id'=>$id])->update($post);
				$this->success('修改成功','ad_index');
			}else{
				$id = Db::name('xcx_ad')->insert($post);
				if($id){
					$this->success('添加成功','ad_index');
				}else{
					$this->error('添加失败');
				}
			}
		}

		if($id){
			$data = Db::name('xcx_ad')->find($id);
			$this->assign('data',$data);
		}
		$cate = Db::name('xcx_ad_cate')->order('cate_id desc')->select();
		$this->assign('cate',$cate);
		return $this->fetch();
	}

	public function ad_del(){
		if(request()->ispost()){
			$id = input('id');
			$del = Db::name('xcx_ad')->where(['id'=>$id])->delete();
			if($del){
				$this->json(1 , '删除成功');
			}else{
				$this->json(1 , '删除失败');
			}
		}
	}


	public function ad_sort_order(){
		if(request()->ispost()){
			$id = input('id');
			$sort_order = input('sort_order');
			Db::name('xcx_ad')->where(['id'=>$id])->update(['sort_order'=>$sort_order]);
		}
	}


	public function needs(){
		$where = '1=1';
		$data = Db::name('xcx_needs')->field('Z.*,U.nickName,U.avatarUrl')->alias('Z')->join('xcx_user U','U.user_id=Z.user_id','LEFT')->where($where)->order('Z.id desc')->paginate($this->limitSize);

		$res = $data->all();
		if($res){
			foreach($res as $k=>$v){
				$imgStr = ''; 
				if($v['imgs']){
					$imgs = explode(',',$v['imgs']);
					foreach($imgs as $src){
						$imgStr .= '<a data-lightbox="example-'.$k.'" href="'.$src.'"><img  src="'.$src.'" style="width:50px;height:50px;margin-right:5px;display:inline-block" /></a>';
					}
				}
				$res[$k]['needs'] = $res[$k]['needs'] ."<br />".$imgStr;
			}
		}
		$this->assign('data',$data);
		$this->assign('res',$res);
		return $this->fetch();
	}

	public function needs_del(){
		if(request()->ispost()){
			$id = input('id');
			$del = Db::name('xcx_needs')->where(['id'=>$id])->delete();
			if($del){
				$this->json(1 , '删除成功');
			}else{
				$this->json(1 , '删除失败');
			}
		}
	}


	public function qa(){
		$where = '1=1';
		$data = Db::name('xcx_qa')->field('Z.*,U.nickName,U.avatarUrl')->alias('Z')->join('xcx_user U','U.user_id=Z.user_id','LEFT')->where($where)->order('Z.id desc')->paginate($this->limitSize);
		$res = $data->all();
		foreach($res as $k=>$v){
			$qaStr = '';
			$qa = unserialize($v['qa']);
			foreach($qa as $q => $a){
				$qaStr .= $q.'->'.$a.'<br />';
			}
			$res[$k]['qa']=$qa;
			$res[$k]['qaStr']=$qaStr;
		}

		$this->assign('data',$data);
		$this->assign('res',$res);
		return $this->fetch();
	}

	public function qa_del(){
		if(request()->ispost()){
			$id = input('id');
			$del = Db::name('xcx_qa')->where(['id'=>$id])->delete();
			if($del){
				$this->json(1 , '删除成功');
			}else{
				$this->json(1 , '删除失败');
			}
		}
	}

	public function msg(){
		$where = "Z.MsgType != 'event'";
		$uid = $_GET['uid'];
		if($uid) $where .= " and Z.openid='$uid'";
		$data = Db::name('xcx_msg')->field('Z.*,U.nickName,U.avatarUrl')->alias('Z')->join('xcx_user U','U.openid=Z.openid','LEFT')->where($where)->order('Z.id desc')->paginate($this->limitSize);
		$this->assign('data',$data);
		return $this->fetch();
	}


	public function msg_del(){
		if(request()->ispost()){
			$id = input('id');
			$del = Db::name('xcx_msg')->where(['id'=>$id])->delete();
			if($del){
				$this->json(1 , '删除成功');
			}else{
				$this->json(1 , '删除失败');
			}
		}
	}


	public function adUpload(){
		$baseData = $_POST['baseData'];
		if($baseData){
			if (preg_match('/(?<=\/)[^\/]+(?=\;)/',$baseData,$pregR)) $streamFileType ='.' .$pregR[0];  
		    $ext = array(".png", ".jpg", ".jpeg", ".gif");
		    if(!in_array($streamFileType,$ext)){
		        exit(json_encode(['error'=>1,'msg'=>'上传类型不允许']));
		    }

		    $dir_name =  '/uploads/'.date('Ymd').'/';
			$upload_dir = '.'.$dir_name;

			if( !is_dir($upload_dir) ){
				if(!mkdir($upload_dir , 0777 , true)){
					exit(json_encode(['error'=>1,'msg'=>'上传目录创建失败']));
				}
			}
			$file_name = date('YmdHis').rand(100000,999999).$streamFileType;
			$save_file = '.'.$dir_name . $file_name ; 
			//处理base64文本，用正则把第一个base64,之前的部分砍掉
    		preg_match('/(?<=base64,)[\S|\s]+/',$baseData,$streamForW);
    		if(file_put_contents($save_file,base64_decode($streamForW[0]))){
    			$rfile = $dir_name . $file_name;
				exit(json_encode(['error'=>0,'msg'=>'文件上传成功','img'=>$rfile]));
    		}else{
    			exit(json_encode(['error'=>1,'msg'=>'文件上传失败']));
    		}

		}else{
			exit(json_encode(['error'=>1,'msg'=>'没有需要上传的文件']));
		}
	}


	public function json($code , $msg , $res= []){
		exit(json_encode(['code'=>$code , 'msg'=>$msg , 'res'=>$res]));
	}
}