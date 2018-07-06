<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 问答管理控制器
 */

namespace app\admin\controller;
use think\Db;
use think\Session;
use app\admin\model\DemandvideoModel;

class Livebroadcast extends Admin

{

	protected $modelName='';
	public function _initialize()
	{
		parent::_initialize();
		//$this->modelName=new AskModel;
	}

    //首页
    public function index()
    {
        return $this->fetch();
    }

    //增加
    public function add()
    {
        $request=request();
        if($request->method()=='POST') {
            $data = input('param.');
            $info =new DemandvideoModel();
            if($info->allowField(true)->save($data)){
                $this->success('添加成功',url('index'));
            }else{
                $this->error('添加失败',url('add'));
            }
        }else{
            Session::set('stagevideo',time());
            //获取分类
            $class_arr = $this->getclasstree();
            $this->assign('class_arr',$class_arr);
            return $this->fetch();

        }
    }

    //删除
    public function del()
    {
        return $this->fetch();
    }

    //修改
    public function alter()
    {
        $id = input('param.id',31);//获取传递过来的帖子id
        $request = request();
        if($request->method()=='POST'){
            $data = input('param.');
            if(empty($data['m_video_url'])){
                unset($data['m_video_url']);
            }
            $liveTitle = new DemandvideoModel();
            if($liveTitle->allowField(true)->save($data,['m_id'=>$id])){
                $this->success('修改成功',url('index'));
            }else{
                $this->error('修改失败',url('alter'));
            }
        }else{

            $live = Db::name('demand_video')->find($id);
            Session::set('stagevideo',time());
            //获取分类
            $class_arr = $this->getclasstree();
            $this->assign('live',$live);
            $this->assign('class_arr',$class_arr);
            return $this->fetch();
        }

    }


    public function getclasstree(){
        $h_class = Db::name('video_category')->where(['v_pid'=>0])->select();
        $class_arr = [];
        foreach ($h_class as $k => $v){
            $child= Db::name('video_category')->where(['v_pid'=>$v['v_id']])->select();
            $childarr = [];
            foreach ($child as $kc=>$vc){
                $childarr[$vc['v_id']] = $vc['v_name'];
            }
            $class_arr[$v['v_id']] = [
                'name'=>$v['v_name'],
                'child'=>$childarr
            ];
        }

        return $class_arr;
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