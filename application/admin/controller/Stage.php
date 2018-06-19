<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * 跟踪反馈
 */

namespace app\admin\controller;


use ApiOauth\ApiOauth;
use app\admin\model\StagearticleModel;
use app\admin\model\StageModel;
use app\admin\model\TrackImgsModel;
use app\admin\model\TrackModel;
use app\admin\model\VisitModel;
use app\index\event\TemplateMessage;
use app\index\model\UserInfoModel;
use think\Db;
use think\Session;

class Stage extends Admin
{
    protected $modelName='';

    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $StageModel = new StageModel();
        $list = $StageModel->getAll();
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function alter()
    {
        $id=input('param.sid');
        $StageModel = new StageModel();
        $request=request();
        if($request->method()=='POST')
        {
            $val =input('param.');
            $data['s_indextitle'] = $val['s_indextitle'];
            $data['s_img']=$this->upload($request,'s_img');
            if(!$data['s_img']){
                unset($data['s_img']);
            }
            if($StageModel->allowField(true)->save($data,['s_id'=>$id])){
                $this->success('修改成功',url('index'));
            }else{
                $this->error('修改失败',url('index'));
            }
        }
        else
        {
            $content=$StageModel->find($id);
            $this->assign('content',$content);
            $this->assign('s_id',$id);
            return $this->fetch();
        }

    }

    public function article(){
        $id=input('param.sid');
        $StagearticleModel = new StagearticleModel();
        $list = $StagearticleModel->getAll($id);
        $this->assign('list',$list);
        $this->assign('sid',$id);
        return $this->fetch();
    }

    public function articleadd(){
        $id=input('param.sid');
        $StagearticleModel = new StagearticleModel();
        $request=request();
        if($request->method()=='POST')
        {
            $data=input('param.');
            $a_label = $data['a_label'];
            unset($data['a_label']);
            $data['a_label'] = implode(',',$a_label);
            $data['a_img']=$this->upload($request,'a_img');
            $data['a_sid']=$id;
            $data['a_order']=intval($data['a_order']);
            $data['a_video_show']=intval($data['a_video_show']);
            $data['a_uid']=Session::get('adminid');
            $data['a_content'] = htmlspecialchars_decode($data['a_content']);
            if($StagearticleModel->allowField(true)->save($data)){
                $this->success('添加成功',url('article',['sid'=>$id]));
            }else{
                $this->error('添加失败',url('index',['sid'=>$id]));
            }
        }
        else
        {
            Session::set('stagevideo',time());
            $this->assign('sid',$id);
            return $this->fetch();
        }
    }

    public function articledel(){
        $id=input('param.aid');
        $StagearticleModel = new StagearticleModel();
        $val = $StagearticleModel->where(['a_id'=>$id])->delete();
        if($val){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    public function articlealter(){
        $id=input('param.aid');
        $StagearticleModel = new StagearticleModel();
        $request=request();
        if($request->method()=='POST')
        {
            $data=input('param.');
            $a_label = $data['a_label'];
            unset($data['a_label']);
            $data['a_label'] = implode(',',$a_label);
            $data['a_img']=$this->upload($request,'a_img');
            $data['a_order']=intval($data['a_order']);
            $data['a_video_show']=intval($data['a_video_show']);
            if(!$data['a_img']){
                unset($data['a_img']);
            }
            $data['a_uid']=Session::get('adminid');
            if(!$data['a_video']){
                unset($data['a_video']);
            }
            $s_id = $StagearticleModel->field('a_sid')->find($id);
            if($StagearticleModel->allowField(true)->save($data,['a_id'=>$id])){
                $this->success('修改成功',url('article',['sid'=>$s_id['a_sid']]));
            }else{
                $this->error('修改失败',url('article',['sid'=>$s_id['a_sid']]));
            }
        }
        else
        {
            Session::set('stagevideo',time());
            $content = $StagearticleModel->find($id);
            $this->assign('nid',$id);
            $this->assign('content',$content);
            return $this->fetch();
        }
    }

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

    public function articlecomment(){
        $aid = input('param.aid');
        $list = Db::name('stagecomment')->where(['c_tid'=>0,'c_aid'=>$aid])->paginate(15);
        $alluid = Db::name('stagecomment sc')->where(['c_tid'=>0,'c_aid'=>$aid])->field('c_uid')->select();
        $newalluid = array_column($alluid,'c_uid');
        $alluser = Db::name('user_info')->where('id','in',$newalluid)->field('id,wechaname,portrait')->select();
        $newuser = array();
        foreach ($alluser as $uk => $uv) {
            $newuser[$uv['id']] = $uv;
        }
        $this->assign('list',$list);
        $this->assign('newuser',$newuser);
        return $this->fetch();
    }

    public function articlecommentdel(){
        $cid = input('param.cid');
        if(Db::name('stagecomment')->where(['c_id'=>$cid])->delete()){
            Db::name('stagecomment')->where(['c_tid'=>$cid])->delete();
            return $this->success('删除成功');
        }else{
            return $this->success('删除成功');
        }
    }

    public function articlecommentchild(){
        $cid = input('param.cid');

        $alluid = Db::name('stagecomment sc')->where(['c_tid'=>$cid])->field('c_uid,c_cuid')->select();
        $newalluid  = array();
        foreach ($alluid as $v){
            $newalluid[] = $v['c_uid'];
            $newalluid[] = $v['c_cuid'];
        }
        $alluser = Db::name('user_info')->where('id','in',$newalluid)->field('id,wechaname,portrait')->select();
        $newuser = array();
        foreach ($alluser as $uk => $uv) {
            $newuser[$uv['id']] = $uv;
        }
        $this->assign('newuser',$newuser);

        $commontlist = Db::name('stagecomment')->where(['c_tid'=>$cid])->order('c_id asc')->select();
        $this->assign('commontlist',$commontlist);
        return $this->fetch();
    }
}