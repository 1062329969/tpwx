<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 案例控制器
 */

namespace app\admin\controller;
use app\admin\model\CasesImgsModel;
use app\admin\model\CasesModel;
use app\admin\model\CasesRecordImgsModel;
use app\admin\model\CasesRecordModel;
use app\admin\model\DoctorModel;
use app\admin\model\GradeModel;
use app\admin\model\HospitalModel;
use app\admin\model\ProjectModel;
use think\Db;
use think\Session;


class Cases extends Admin
{
	protected $modelName='';
	protected $city=[
		'110000'=>'北京市',
		'120000'=>'天津市',
		'130000'=>'河北省',
		'140000'=>'山西省',
		'150000'=>'内蒙古',
		'210000'=>'辽宁省',
		'220000'=>'吉林省',
		'230000'=>'黑龙江省',
		'310000'=>'上海市',
		'320000'=>'江苏省',
		'330000'=>'浙江省',
		'340000'=>'安徽省',
		'350000'=>'福建省',
		'360000'=>'江西省',
		'370000'=>'山东省',
		'410000'=>'河南省',
		'420000'=>'湖北省',
		'430000'=>'湖南省',
		'440000'=>'广东省',
		'450000'=>'广西壮族',
		'460000'=>'海南省',
		'500000'=>'重庆',
		'510000'=>'四川省',
		'520000'=>'贵州省',
		'530000'=>'云南省',
		'540000'=>'西藏',
		'610000'=>'陕西省',
		'620000'=>'甘肃省',
		'630000'=>'青海省',
		'640000'=>'宁夏',
		'650000'=>'新疆',
		'710000'=>'台湾省',
		'810000'=>'香港',
		'820000'=>'澳门',
		'990000'=>'海外',
	];

	public function _initialize()
	{
		parent::_initialize();
		$this->modelName=new CasesModel;

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
			$result=$this->validate($data,'ValidateClass.CK14');
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

			$cases=$this->modelName->allowField(true)->save($data);
//			echo $this->modelName->getLastSql();die;


			if($cases)
			{
				//保存多图
				if(count($imgs)>0)
				{
					foreach($imgs as $k=>$v)
					{
						$casesImgsModel=new CasesImgsModel;
						$casesImgsModel->save(['cid'=>$this->modelName->id,'pic'=>$v]);
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
			//读取项目
			$project=ProjectModel::getAll();
			$this->assign('project',$project);
            //读取医院
			$hospital=HospitalModel::getAll(100);
			$this->assign('hospital',$hospital);
			//读取脱发等级
			$grade=GradeModel::getAll(100);
            Session::set('stagevideo',time());
			//读取医生
            $doctor = Db::name('doctor')->where(['status'=>1,'del'=>0])->order('sort desc,id asc')->select();

			$this->assign('doctor',$doctor);
			$this->assign('grade',$grade);

			$this->assign('city',$this->city);

			return $this->fetch();
		}
	}

	public function alter()
	{
		$request=request();
		if($request->method()=='POST')
		{
            $data=input('post.');
            $result=$this->validate($data,'ValidateClass.CK15');
//            if(true !== $result)
//            {
//                // 验证失败 输出错误信息
//                $this->error($result);
//            }

            $pic=$this->upload($request,'pic');
            if(!empty($pic))
            {
                $data['pic']=$pic;
            }
            else
            {
                unset($data['pic']);
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
                    if($info)
                    {

                        $returnImg=str_replace('\\','/','/uploads/'.$info->getSaveName());
                        //生成缩略图
                        $returnImg=$this->thumbnail($file,$returnImg);

                        $imgs[]=$returnImg;
                    }
                    else
                    {
                        $this->error($file->getError());
                    }

                    $key=$key+1;
                }
            }



            if($this->modelName->allowField(true)->save($data,['id'=>$data['id']]))
			{
				//保存多图
				if(count($imgs)>0)
				{
					foreach($imgs as $k=>$v)
					{
						$casesImgsModel=new CasesImgsModel;
						$casesImgsModel->save(['cid'=>$this->modelName->id,'pic'=>$v]);
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
			$iid = input('post.');
			//读取项目
			$project=ProjectModel::getAll();
			$this->assign('project',$project);
			//读取该案例的所有图片
//			$casesImgs=CasesImgsModel::getAll($id);
            $casesImgs = Db::name('cases_imgs')->where(['cid'=>$id])->order('id asc')->select();

            $this->assign('casesImgs',$casesImgs);
			//读取医院
			$hospital_i=Db::table('yh_hospital')
                ->alias('a')
                ->join('yh_cases b','a.id =b.hosname_id')
                ->where(['b.id'=>$id])
                ->find();
			$this->assign('hospital_i',$hospital_i);

            //读取医生下拉列表
            $res =  Db::table('yh_cases')->alias('a')
                ->join('yh_doctor', 'a.doctor = yh_doctor.id')
                ->where(['a.id'=>$id])
                ->find();
            //读取视频
            $video = Db::table('yh_cases')->where(['uid'=>$id])->find();
            $this->assign('res',$res);

			$content=$this->modelName->get($id);

			$this->assign('content',$content);

            $this->assign('video',$video);

			
			$this->assign('city',$this->city);



            //读取医生
            $doctor = DoctorModel::getAll($id);

            //读取医生
            $hospital = HospitalModel::getAll($id);
            $this->assign('hospital',$hospital);
            $this->assign('doctor',$doctor);
			return $this->fetch();
		}

	}

	//查看日记记录
	public function record()
	{
		$cid=input('param.cid');
		$content=CasesRecordModel::getAll($cid);
		$this->assign('content',$content);
		$this->assign('cid',$cid);
		return $this->fetch();
	}

	//添加日记记录
	public function record_add()
	{
		$request=request();
		if($request->method()=='POST')
		{
			$data=input('post.');
			$data['create_time'] = strtotime($data['create_time']);//帖子发布时间
			$result=$this->validate($data,'ValidateClass.CK15');
			if(true !== $result)
			{
				// 验证失败 输出错误信息
				$this->error($result);
			}
            $pics=$this->upload($request,'video_pic');
			//多文件上传
			if(empty($data['default_img']))
			{
				$data['default_img']=0;
			}

            if(!empty($pics))
            {
                $data['video_pic']=$pics;
            }
            else
            {
                unset($data['video_pic']);
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
            $casesRecordModel=new CasesRecordModel;
			$casesRecordModel->allowField(true)->save($data);
            $cases = Db::name('cases')->where(['id'=>$casesRecordModel['cid']])->update(['update_time'=>time()]);

            $res = Db::name('cases')->where(['id'=>$casesRecordModel['cid']])->setInc('title_sum',1);
            if($cases)
			{
                //保存多图
                if(count($imgs)>0)
                {
                    foreach($imgs as $k=>$v)
                    {
                        $casesRecordImgsModel=new CasesRecordImgsModel;
                        $casesRecordImgsModel->save(['cid'=>$casesRecordModel->id,'pic'=>$v]);
                    }
                }
				$this->success('添加成功',url('record',['cid'=>$data['cid']]));
			}
			else
			{
				$this->error('添加失败',url('record',['cid'=>$data['cid']]));
			}
		}
		else
		{
            Session::set('stagevideo',time());

			$cid = input('param.cid');
			$this->assign('cid', $cid);
			return $this->fetch();
		}
	}

	//日记记录修改
	public  function record_alter()
	{
        $request=request();
        if($request->method()=='POST')
        {
            $data=input('post.');
            $data['create_time'] = strtotime($data['create_time']);//帖子发布时间
            $result=$this->validate($data,'ValidateClass.CK15');
            if(true !== $result)
            {
                // 验证失败 输出错误信息
                $this->error($result);
            }

            $pic=$this->upload($request,'pic');
            $pics=$this->upload($request,'video_pic');
            if(!empty($pic))
            {
                $data['pic']=$pic;
            }
            else
            {
                unset($data['pic']);
            }
            if(!empty($pics))
            {
                $data['video_pic']=$pics;
            }
            else
            {
                unset($data['video_pic']);
            }
            if(empty($data['vurl'])){
                unset($data['vurl']);
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
                    if($info)
                    {

                        $returnImg=str_replace('\\','/','/uploads/'.$info->getSaveName());
                        //生成缩略图
                        $returnImg=$this->thumbnail($file,$returnImg);

                        $imgs[]=$returnImg;
                    }
                    else
                    {
                        $this->error($file->getError());
                    }

                    $key=$key+1;
                }
            }

            $casesRecord=new CasesRecordModel;
			if($casesRecord->allowField(true)->save($data,['id'=>$data['id']]))
			{
                //保存多图
                if(count($imgs)>0)
                {
                    foreach($imgs as $k=>$v)
                    {
                        $casesRecordImgsModel=new CasesRecordImgsModel;
                        $casesRecordImgsModel->save(['cid'=>$casesRecord->id,'pic'=>$v]);
                    }
                }
				$this->success('修改成功');
			}
			else
			{
				$this->error('修改失败');
			}
		}
		else
		{
			$id=input('param.id');
            Session::set('stagevideo',time());
			//读取该案例记录的所有图片
//			$casesImgs=CasesRecordImgsModel::getAll($id);
            $casesImgs = Db::name('cases_record_imgs')->where(['cid'=>$id])->order('id asc')->select();
            $this->assign('casesImgs',$casesImgs);
			$content=CasesRecordModel::get($id);
			$this->assign('content',$content);
			return $this->fetch();
		}
	}


	//案例管理删除
    public function record_del(){
	    $id = input('post.id');
	    $res = Db::name('cases_record')->find($id);
        $info = Db::name('cases')->where(['id'=>$res['cid']])->setDec('title_sum',1);
        if($info){
            CasesRecordModel::where(['id'=>$id])->delete();
        }

        return ['code'=>1];

    }


	//删除日记记录图片
	public function record_delImg()
	{
		$imgurl=input('post.imgurl');
		$id=input('post.id');
		$result = @unlink ('.'.$imgurl);
		CasesRecordImgsModel::where(['id'=>$id,'pic'=>$imgurl])->delete();
		return ['code'=>1];

	}

	//删除日记图片
	public function delImg()
	{
		$imgurl=input('post.imgurl');
		$id=input('post.id');
		$result = @unlink ('.'.$imgurl);
		CasesImgsModel::where(['id'=>$id,'pic'=>$imgurl])->delete();
		return ['code'=>1];

	}

	public function del()
	{

		$id=input('param.id');
        CasesModel::where(['id'=>$id])->delete();
		return ['code'=>1];
	}


	//长传视频
    public function articlevideo(){
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

}