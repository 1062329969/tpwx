<?php
/**
 * Created by yongxianghui.net.
 * User: liuyixin
 * Date: 2018/1/25
 * 药品券
 */
namespace app\index\controller;

use app\admin\model\FeedbackModel;
use app\index\model\UserInfoModel;
use Phpqrcode\QRcode;
use think\Db;
use think\Session;
class Myfeedback extends Wap
{
    protected function _initialize()
    {
        //获得授权
        $this->getUserInfo();
        $this->assign('fansInfo',$this->fansInfo);
    }

    public function index(){
        $type = Db::name('feedbacktype')->where(['ft_state'=>1])->select();
        $this->assign('typelist',$type);
        return $this->fetch();
    }

    public function add(){
        $request=request();
        if($request->method()=='POST'){
            $data=input('post.');
            if($data['f_type'] && $data['f_content']){
                $img = array();
                if(isset($data['imgarr']))
                {
                    foreach ($data['imgarr'] as $k=>$v)
                    {
                        $return=$this->saveBase64Image($v);
                        $img[] = $return['url'];
                    }
                }
                $data['f_pic'] = json_encode($img);
                $feedbackModel = new FeedbackModel();
                $data['f_addtime'] = time();
                $data['f_uid'] = $this->fansInfo['id'];
                if($feedbackModel->allowField(true)->save($data)){
                    $this->success('添加成功',url('backlist'));
                }else{
                    $this->error('添加失败',url('index'));
                }
            }else{
                $this->error('请填写完整数据',url('index'));
            }
        }else{
            $this->error('请填写完整数据',url('index'));
        }
    }

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

    public function backlist(){
        $type = Db::name('feedbacktype')->where(['ft_state'=>1])->select();
        $typeinfo = array_column($type,'ft_type','ft_id');
        $list = Db::name('feedback')->where(['f_uid'=>$this->fansInfo['id']])->order('f_id desc')->select();
        $this->assign('typelist',$typeinfo);
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function setpic(){
        $data=input('post.');
        //多文件上传
        if(empty($data['default_img'])){
            $data['default_img']=0;
        }
        $key=0;  //默认图片的位置
        $imgs=array();
        $files = request()->file('pic');
        if(count($files)>0){
            foreach($files as $file){
                // 移动到框架应用根目录/public/uploads/ 目录下
                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                if($info){
                    $returnImg=str_replace('\\','/','/uploads/'.$info->getSaveName());
                    //生成缩略图
                    $returnImg=$this->thumbnail($file,$returnImg);
                    if($key==$data['default_img']){
                        $data['pic']=$returnImg;
                    }
                    $imgs[]=$returnImg;
                }else{
                    $this->error($file->getError());
                }
                $key=$key+1;
            }
        }
    }
}