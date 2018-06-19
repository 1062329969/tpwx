<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * 跟踪反馈
 */

namespace app\admin\controller;


use ApiOauth\ApiOauth;
use app\admin\model\HospitalModel;
use app\admin\model\StagearticleModel;
use app\index\event\TemplateMessage;
use app\index\model\ReservationModel;
use app\index\model\UserInfoModel;
use think\Db;
use think\Session;


class Reservation extends Admin
{

    protected  $project=array("1"=>"头发种植", "2"=>"顶部加密", "3"=>"美人尖种植", "4"=>"发际线种植", "5"=>"眉毛种植","6"=>"胡须种植","7"=>"鬓角种植","8"=>"体毛种植","9"=>"疤痕种植");

    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $ReservationModel = new ReservationModel();
        $list = $ReservationModel->order('res_id desc')->select();
        $this->assign('list',$list);
        $hospital = Db::name('hospital')->field('id,hosname')->select();
        $newhospitallist = array_column($hospital,'hosname','id');
        $this->assign('hospitallist',$newhospitallist);
        $this->assign('project',$this->project);
        return $this->fetch();
    }


    public function alter()
    {
        $ReservationModel = new ReservationModel();
        $id=input('param.sid');
        $request=request();
        if($request->method()=='POST')
        {
            $data=input('post.');
            $dtime = $data['res_time'];
            $data['res_time'] = strtotime($data['res_time']);
            if($ReservationModel->allowField(true)->save($data,['res_id'=>$id])){

                $Reservation = $ReservationModel->find($id);
                $userinfo = UserInfoModel::find($Reservation['res_uid']);
                $dh = Db::name('hospital')->field('hosname')->find($data['res_hid']);
                //发送模板消息
                $templateMessage=new TemplateMessage();
                $sData["wecha_id"]=$userinfo['wecha_id'];
                $sData["tempid"]='p3Nii2VziAZ55gZ0CMyn9S2lxcLUDGXFzjb1_zjWerU';
                $sData['first']='您好，您已经预约成功';
                $sData['keyword1']=$data['res_name'];
                $sData['keyword2']=$data['res_tel'];
                $sData['keyword3']=$dtime;
                $sData['keyword4']=$dh['hosname'];
                $sData['remark']='请及时到达就诊';
                $sData["href"]= config('domain').'/myreservation/index.html';
                $sData["topcolor"]='';
                $apiOauth=new ApiOauth();
                $access_token=$apiOauth->update_authorizer_access_token(['appid'=>config('Appid'),'appsecret'=>config('Appsecret')]);
                $templateMessage->sendTempMsg(6,$sData,$access_token);

                $this->success('修改成功',url('index'));
            }else{
                $this->error('修改失败',url('index'));
            }
        }
        else
        {
            $hospital = Db::name('hospital')->field('id,hosname')->select();
            $this->assign('hlist',$hospital);
            $this->assign('project',$this->project);
            $content=$ReservationModel->find($id);
            $this->assign('content',$content);
            $this->assign('sid',$id);
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
            $data['a_img']=$this->upload($request,'a_img');
            $data['a_sid']=$id;
            $data['a_uid']=Session::get('adminid');
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

    public function articlealter(){
        $id=input('param.aid');
        $StagearticleModel = new StagearticleModel();
        $request=request();
        if($request->method()=='POST')
        {
            $data=input('param.');
            $data['a_img']=$this->upload($request,'a_img');
            if(!$data['a_img']){
                unset($data['a_img']);
            }
            $data['a_uid']=Session::get('adminid');
            if(!$data['a_video']){
                unset($data['a_video']);
            }
            $s_id = $StagearticleModel->field('a_sid')->find($id);
            if($StagearticleModel->allowField(true)->save($data,['a_id'=>$id])){
                $this->success('添加成功',url('article',['sid'=>$s_id['a_sid']]));
            }else{
                $this->error('添加失败',url('index',['sid'=>$s_id['a_sid']]));
            }
        }
        else
        {
            Session::set('stagevideo',time());
            $content = $StagearticleModel->find($id);
            $this->assign('aid',$id);
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
}