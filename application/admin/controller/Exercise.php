<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 商品订单管理控制器
 */
namespace app\admin\controller;

use ApiOauth\ApiOauth;
use app\admin\model\ExerciseModel;
use think\Session;
class Exercise extends Admin
{

    protected $ExerciseModel;
    protected $abc = array("A","B","C","D","E","F","G","H");

	public function _initialize()
	{
		parent::_initialize();
        $this->ExerciseModel = new ExerciseModel();
	}
	public function index()
	{
        $Exercise = $this->ExerciseModel->getall();
        $this->assign('exerciselist',$Exercise);
		return $this->fetch();
	}

    public function add()
    {
        $request=request();
        if($request->method()=='POST')
        {
            $data=input('post.');

            $dataval = array(
                'e_content'=>$data['e_content'],
                'e_sex'=>$data['e_sex'],
                'e_type'=>$data['e_type']
            );
            $datas = array();
            foreach($data['datas'] as $k => $v){
                $datas[$k]['score'] = $v['score'];
                $datas[$k]['answer'] = $v['answer'];
                $datas[$k]['code'] = $this->abc[$k];
                if(isset($v['imgarr'])){
                    foreach ($v['imgarr'] as $imgk=>$imgv){
                        $return = $this->saveBase64Images($imgv);
                        $datas[$k]['imgarr'][$imgk] = $return['url'];
                    }
                }
            }
            $dataval['e_answerscore'] = json_encode($datas);
            $dataval['e_addtime'] = time();
            $dataval['e_uid'] = Session::get('adminid');
//            $aw = array();
//            foreach($data['answer'] as $k=>$v){
//                $aw[] = array('answer'=>$v,'score'=>$data['score'][$k],'code'=>$this->abc[$k]);
//            }
            if($this->ExerciseModel->allowField(true)->save($dataval)){
                $this->success('添加成功！','index');
            }else{
                $this->error('添加失败！','index');
            }
        }
        else
        {
            return $this->fetch();
        }
    }

	public function alter(){
        $request=request();
        $id=input('param.e_id');
		if($request->method()=='POST')
        {
            $data=input('post.');
            $dataval = array(
                'e_content'=>$data['e_content'],
                'e_sex'=>$data['e_sex'],
                'e_type'=>$data['e_type']
            );
            $datas = array();
            foreach($data['datas'] as $k => $v){
                $datas[$k]['score'] = $v['score'];
                $datas[$k]['answer'] = $v['answer'];
                $datas[$k]['code'] = $this->abc[$k];
                if(isset($v['imgarr'])){
                    foreach ($v['imgarr'] as $imgk=>$imgv){
                        $return = $this->saveBase64Images($imgv);
                        $datas[$k]['imgarr'][$imgk] = $return['url'];
                    }
                }
            }
            $dataval['e_answerscore'] = json_encode($datas);
            $dataval['e_addtime'] = time();
            $dataval['e_uid'] = Session::get('adminid');
            if($this->ExerciseModel->allowField(true)->save($dataval ,['e_id'=>$id]))
            {
                $this->success('修改成功！','index');
            }
            else
            {
                echo $this->ExerciseModel->getLastSql();
                $this->error('修改失败！','index');
            }
        }
        else
        {
            $exercise= $this->ExerciseModel->find($id);
            $code = json_decode($exercise['e_answerscore'],true);
            $this->assign('exercise',$exercise);
            $this->assign('code',$code);
            $this->assign('id',$id);
            return $this->fetch();
        }
    }

    public function del(){
        $id=input('param.e_id');
        $val = $this->ExerciseModel->where(['e_id'=>$id])->delete();
        if($val){
            $this->success('删除成功！','index');
        }else{
            $this->success('删除失败！','index');
        }
    }

    public function check(){
        $id=input('post.id/a');
        foreach ($id as $k=>$v){
            $this->ExerciseModel->update(['e_id'=>$v,'e_state'=>1]);
        }
        $this->success('选中成功');
    }

    public function saveBase64Images($base64_image_content){

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

//                //压缩图片
//                $image = \think\Image::open('.'.$data['url']);
//                // 按照原图的比例生成一个最大为300*300的缩略图并保存为thumb.png
//                $image->thumb(300,300,\think\Image::THUMB_CENTER)->save('.'.$image_path.'m_'.$image_name);
//                $data['url']=$image_path.'m_'.$image_name;
            }else{
                $data['code']=1;
                $data['imgageName']='';
                $data['url']='';
                $data['msg']='图片保存失败！';
            }
        }else{
            $data['code']=1;
            $data['imgageName']='';
            $data['url']=$base64_image_content;
            $data['msg']='base64图片格式有误！';
        }
        return $data;
    }
}