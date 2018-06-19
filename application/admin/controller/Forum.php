<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 植发论坛管理控制器
 */
namespace app\admin\controller;
use app\admin\model\ForumModel;
use app\admin\model\ForumOnetypeModel;
use app\admin\model\ForumTypeModel;
use app\admin\model\ForumImgsModel;
use think\Db;
use think\Session;

class Forum extends Admin
{
	protected $modelName='';

	public function _initialize()
	{
		parent::_initialize();
		$this->modelName=new ForumModel;
	}

	public function index()
	{
		$content=$this->modelName->getAll(15);
		$this->assign('content',$content);
		return $this->fetch();
	}

	public function add()
	{
		$request=request();
		if($request->method()=='POST')
		{
			$data=input('post.');
			$result=$this->validate($data,'ValidateClass.CK11');
			if(true !== $result)
			{
				// 验证失败 输出错误信息
				$this->error($result);
			}

			//多文件上传

			if(empty($data['default_img']))
			{
				$data['default_img']=0;
			}

			$key=0;  //默认图片的位置
			$imgs=array();
			$files = request()->file('pic');
			if(count($files)>0)
			{
				foreach($files as $file)
				{
					// 移动到框架应用根目录/public/uploads/ 目录下
					$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
					if($info)
					{
						$returnImg=str_replace('\\','/','/uploads/'.$info->getSaveName());
						//生成缩略图
						$returnImg=$this->thumbnail($file,$returnImg);
						if($key==$data['default_img'])
						{
							$data['pic']=$returnImg;
						}

						$imgs[]=$returnImg;
					}
					else
					{
						$this->error($file->getError());
					}

					$key=$key+1;

				}
			}

			if($this->modelName->allowField(true)->save($data))
			{
				//保存多图
				if(count($imgs)>0)
				{
					foreach($imgs as $k=>$v)
					{
						$forumImgsModel=new ForumImgsModel;
						$forumImgsModel->save(['fid'=>$this->modelName->id,'pic'=>$v]);
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
			//读取帖子分类
            $contType = ForumOnetypeModel::getAll();
			$this->assign('contType',$contType);
			return $this->fetch();
		}
	}

    public function forumvideo(){
        ini_set("display_errors", "On");

        error_reporting(E_ALL | E_STRICT);

        $stagevideo = Session::get('stagevideo');
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit; // finish preflight CORS requests here
        }
        if ( !empty($_REQUEST[ 'debug' ]) ) {
            $random = rand(0, intval($_REQUEST[ 'debug' ]) );
            if ( $random === 0 ) {
                header("HTTP/1.0 500 Internal Server Error");
                exit;
            }
        }
        @set_time_limit(5 * 60);
        $targetDir = ROOT_PATH . 'public' . DS . 'uploads/upload_tmp';
        $uploadDir = ROOT_PATH . 'public' . DS . 'uploads/upload';
        $cleanupTargetDir = true; // Remove old files
        $maxFileAge = 5 * 3600; // Temp file age in seconds
        // Create target dir
        if (!file_exists($targetDir)) {
            @mkdir($targetDir);
        }
        // Create target dir
        if (!file_exists($uploadDir)) {
            @mkdir($uploadDir);
        }
        // Get a file name
        if (isset($_REQUEST["name"])) {
            $fileName = $_REQUEST["name"];
        } elseif (!empty($_FILES)) {
            $fileName = $_FILES["file"]["name"];
        } else {
            $fileName = uniqid("file_");
        }
        $pic = $fileName;
        $pics = explode('.' , $pic);
        $num = count($pics);
        $filePath = $targetDir . DIRECTORY_SEPARATOR . $stagevideo.'.'.$pics[$num-1];
        $uploadPath = $uploadDir . DIRECTORY_SEPARATOR . $stagevideo.'.'.$pics[$num-1];
        // Chunking might be enabled
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 1;
        // Remove old temp files
        if ($cleanupTargetDir) {
            if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
            }
            while (($file = readdir($dir)) !== false) {
                $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
                // If temp file is current file proceed to the next
                if ($tmpfilePath == "{$filePath}_{$chunk}.part" || $tmpfilePath == "{$filePath}_{$chunk}.parttmp") {
                    continue;
                }
                // Remove temp file if it is older than the max age and is not the current file
                if (preg_match('/\.(part|parttmp)$/', $file) && (@filemtime($tmpfilePath) < time() - $maxFileAge)) {
                    @unlink($tmpfilePath);
                }
            }
            closedir($dir);
        }
        // Open temp file
        if (!$out = @fopen("{$filePath}_{$chunk}.parttmp", "wb")) {
            die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
        }
        if (!empty($_FILES)) {
            if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
            }
            // Read binary input stream and append it to temp file
            if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
            }
        } else {
            if (!$in = @fopen("php://input", "rb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
            }
        }
        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }
        @fclose($out);
        @fclose($in);
        rename("{$filePath}_{$chunk}.parttmp", "{$filePath}_{$chunk}.part");
        $index = 0;
        $done = true;
        for( $index = 0; $index < $chunks; $index++ ) {
            if ( !file_exists("{$filePath}_{$index}.part") ) {
                $done = false;
                break;
            }
        }
        if ( $done ) {
            if (!$out = @fopen($uploadPath, "wb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
            }
            if ( flock($out, LOCK_EX) ) {
                for( $index = 0; $index < $chunks; $index++ ) {
                    if (!$in = @fopen("{$filePath}_{$index}.part", "rb")) {
                        break;
                    }
                    while ($buff = fread($in, 4096)) {
                        fwrite($out, $buff);
                    }
                    @fclose($in);
                    @unlink("{$filePath}_{$index}.part");
                }
                flock($out, LOCK_UN);
            }
            @fclose($out);
        }
        die( '/uploads/upload/' . $stagevideo.'.'.$pics[$num-1]);
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


			//多文件上传
			$imgs=array();

			if(empty($pic))
			{
				$imgs=$this->uploadMore($request,'pic2');
			}
			if($this->modelName->allowField(true)->save($data,['id'=>$data['id']]))
			{
				//保存多图
				if(count($imgs)>0)
				{
					foreach($imgs as $k=>$v)
					{
						$forumImgsModel=new ForumImgsModel;
						$forumImgsModel->save(['fid'=>$this->modelName->id,'pic'=>$v]);
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
			//读取分类 其中不包含跳转链接的分类
            $contType = ForumOnetypeModel::getAll();
            $forum = Db::name('forum')->where('id',$id)->find();

            $this->assign('forum',$forum);
			$this->assign('contType',$contType);

			$content=$this->modelName->get($id);
			$this->assign('content',$content);



			//读取该问答的所有图片
			$forumImgs=ForumImgsModel::getAll($id);
			$this->assign('forumImgs',$forumImgs);

			return $this->fetch();
		}

	}

    //删除论坛图片
	public function delImg()
	{
		$imgurl=input('post.imgurl');
		$id=input('post.id');
		$result = @unlink ('.'.$imgurl);
		ForumImgsModel::where(['id'=>$id,'pic'=>$imgurl])->delete();
		return ['code'=>1];

	}

	public function del()
	{

		$id=input('param.id');
		$res = new ForumModel();
		$res = $this->modelName->save(['del'=>1],['id'=>$id]);

		if($this->modelName->save(['del'=>1],['id'=>$id]))
		{
			echo '{"code":"1"}';
		}
		else
		{
			echo '{"code":"0"}';
		}
	}

}