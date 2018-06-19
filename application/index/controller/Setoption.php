<?php
/**
 * Created by yongxianghui.net.
 * User: liuyixin
 * Date: 2018/1/25
 * 药品券
 */
namespace app\index\controller;

use app\index\model\ShipaddressModel;
use app\index\model\SmslogModel;
use app\index\model\UserInfoModel;
use Phpqrcode\QRcode;
use think\Db;
use think\Session;
use app\index\event\Message;
class Setoption extends Wap
{
    protected function _initialize()
    {
        //获得授权
        $this->getUserInfo();
        $this->assign('fansInfo',$this->fansInfo);
    }

    public function set()
    {
        $this->checkLogin();
        $uid = $this->fansInfo['id'];
        $userinfo = Db::name('user_info')->where(['id'=>$uid])->field('portrait,wechaname,sex,birthday,phonenum')->find();
        // var_dump($userinfo);die;
        $this->assign('userinfo',$userinfo);
        return $this->fetch();
    }

    public function name(){
        $this->checkLogin();
        $uid = $this->fansInfo['id'];
        $request=request();
        if($request->method()=='POST'){
            $userinfo = new UserInfoModel();
            $data=input('post.');
            $datas['wechaname'] = $data['wechaname'];
            if($userinfo->allowField(true)->save($datas,['id'=>$uid])){
                $this->success('修改成功',url('set'));
            }else{
                $this->error('修改失败',url('set'));
            }
        }else{
            $name = input('param.name');
            $this->assign('name',$name);
            return $this->fetch();
        }
    }

    public function telphone(){
        $this->checkLogin();
        $uid = $this->fansInfo['id'];
        $request=request();
        if($request->method()=='POST'){
            $data=input('post.');
            if(!$data['telphone'] || !$data['code']){
                $this->error('请填写手机号和验证码。');
            }
            $datas['sms_mobile'] = $data['telphone'];
            $datas['sms_type']=2;
            $datas['sms_wecha_id']=$this->fansInfo['wecha_id'];
            $smslog = new SmslogModel();
            $sms = $smslog->where($datas)->order('sms_id desc')->find();
            if(!$sms){
                return array('code'=>0,'msg'=>"请先点击发送验证码");
            }elseif($sms['sms_state']==1){
                return array('code'=>0,'msg'=>"验证码已经被使用");
            }elseif($sms['sms_addtime']+300<time()){
                return array('code'=>0,'msg'=>"验证码已经失效");
            }elseif($sms['sms_code']!=$data['code']){
                return array('code'=>0,'msg'=>"验证码错误");
            }else{
                $val = $smslog->where(['sms_wecha_id'=>$this->fansInfo['wecha_id'],'sms_code'=>$data['code']])->update(['sms_state'=>1]);
                Session::set('edittel',md5(md5($this->fansInfo['wecha_id'])));
                if($val){
                    $this->success('验证成功',url('editphone'));
                }else{
                    $this->error('验证失败');
                }
            }
        }else{
//            $telphone = input('param.phonenum');
            $this->assign('telphone',$this->fansInfo['phonenum']);
            return $this->fetch();
        }
    }

    public function editphone(){
        $request=request();
        if($request->method()=='POST'){
            $edittel = Session::get('edittel2');
            if($edittel != md5($this->fansInfo['wecha_id'])){
                $this->error('异常',url('set'));
            }else{
                $data=input('post.');
                $datas['sms_mobile'] = $data['telphone'];
                $datas['sms_type']=3;
                $datas['sms_wecha_id']=$this->fansInfo['wecha_id'];
                $smslog = new SmslogModel();
                $sms = $smslog->where($datas)->order('sms_id desc')->find();
                if(!$sms){
                    $this->error("请先点击发送验证码");
                }elseif($sms['sms_state']==1){
                    $this->error("验证码已经被使用");
                }elseif($sms['sms_addtime']+300<time()){
                    $this->error("验证码已经失效");
                }elseif($sms['sms_code']!=$data['code']){
                    $this->error("验证码错误");
                }else{
                    $val = $smslog->where(['sms_id'=>$sms['sms_id']])->update(['sms_state'=>1]);
                    $user = new UserInfoModel();
                    $user->allowField(true)->where(['id'=>$this->fansInfo['id']])->update(['phonenum'=>$data['telphone']]);
                    if($val){
                        $this->success("绑定成功",url('set'));
                    }else{
                        $this->error("绑定失败");
                    }
                }
            }
        }else{
            $edittel = Session::get('edittel');
            if($edittel != md5(md5($this->fansInfo['wecha_id']))){
                $this->error('异常','setoption/set');
            }else{
                Session::set('edittel2',md5($this->fansInfo['wecha_id']));
                return $this->fetch();
            }
        }
    }

    public function getCode()
    {
        $request=request();
        if($request->method()=='POST')
        {
            $data = input('post.');
            $uid = $this->fansInfo['id'];
            $datas['sms_mobile'] = $data['phonenum'];
            $datas['sms_type']=2;
            $datas['sms_wecha_id']=$this->fansInfo['wecha_id'];
            $smslog = new SmslogModel();
            $sms = $smslog->where($datas)->order('sms_id desc')->find();
            if(time()-60<$sms['sms_addtime']){
                return array('code'=>0,'msg'=>"短信正在发送请稍后再试");
            }

            if(!UserInfoModel::where(['phonenum'=>$data['phonenum'],'id'=>$uid])->find())
            {
                return array('code'=>0,'msg'=>"该手机不是您的绑定手机");
            }
            //随机6位验证码
            $rand=rand(111111,999999);
            $code['phone']=$data['phonenum'];
            $codeCont=config('codeCont');
            $code['content']=str_replace('{code}',$rand,$codeCont);
            $message=new Message();
            @$return=$message->APIHttpRequestCURL($code,'get');
            if($return['status']=='0')
            {
                $arr = array(
                    'sms_code' =>$rand,
                    'sms_mobile' =>$data['phonenum'],
                    'sms_addtime' =>time(),
                    'sms_state' =>0,
                    'sms_wecha_id' =>$this->fansInfo['wecha_id'],
                    'sms_returnstatus' =>json_encode($return),
                    'sms_type' =>2,
                );
                $smslog = new SmslogModel();
                $smslog->allowField(true)->save($arr);
                return array('code'=>1);
            }
            else
            {
                return array('code'=>0,'msg'=>'短信接口错误！');
            }
        }
    }
    public function getCodeedit()
    {
        $request=request();
        if($request->method()=='POST')
        {
            $data = input('post.');
            $uid = $this->fansInfo['id'];
            $datas['sms_mobile'] = $data['phonenum'];
            $datas['sms_type']=2;
            $datas['sms_wecha_id']=$this->fansInfo['wecha_id'];
            $smslog = new SmslogModel();
            $sms = $smslog->where($datas)->order('sms_id desc')->find();
            if(time()-60<$sms['sms_addtime']){
                return array('code'=>0,'msg'=>"短信正在发送请稍后再试");
            }

            if(UserInfoModel::where(['phonenum'=>$data['phonenum'],'id'=>$uid])->find())
            {
                return array('code'=>0,'msg'=>"无法使用上一个手机号");
            }
            //随机6位验证码
            $rand=rand(111111,999999);
            $code['phone']=$data['phonenum'];
            $codeCont=config('codeCont');
            $code['content']=str_replace('{code}',$rand,$codeCont);
            $message=new Message();
            @$return=$message->APIHttpRequestCURL($code,'get');
            if($return['status']=='0')
            {
                $arr = array(
                    'sms_code' =>$rand,
                    'sms_mobile' =>$data['phonenum'],
                    'sms_addtime' =>time(),
                    'sms_state' =>0,
                    'sms_wecha_id' =>$this->fansInfo['wecha_id'],
                    'sms_returnstatus' =>json_encode($return),
                    'sms_type' =>3,
                );
                $smslog = new SmslogModel();
                $smslog->allowField(true)->save($arr);
                return array('code'=>1);
            }
            else
            {
                return array('code'=>0,'msg'=>'短信接口错误！');
            }
        }
    }

    public function editsex(){
        $sex = input('param.sex');
        if($sex==1){
            $sexs = '男';
        }else{
            $sexs = '女';
        }
        $user = new UserInfoModel();
        $val = $user->allowField(true)->where(['id'=>$this->fansInfo['id']])->update(['sex'=>$sexs]);
        if($val){
            $this->success('修改成功');
        }else{
            $this->error('修改失败');
        }
    }
    public function editbir(){
        $birthday = input('param.bir');
        $birthday = strtotime($birthday);
        $user = new UserInfoModel();
        $val = $user->allowField(true)->where(['id'=>$this->fansInfo['id']])->update(['birthday'=>$birthday]);
        if($val){
            $this->success('修改成功');
        }else{
            $this->error('修改失败');
        }
    }

    public function editheadimg(){
        $request=request();
        if($request->method()=='POST')
        {
            $data = input('post.');
            if(!$data['images']){
                $this->error('修改失败');
            }
            $return=$this->saveBase64Image($data['images']);
            if($return['code']==0)
            {
                $datas['portrait']=$return['url'];
                $userinfo = new UserInfoModel();
                if($userinfo->where(['id'=>$this->fansInfo['id']])->update($datas)){
                    $this->success('修改成功',url('set'));
                }else{
                    $this->error('修改失败',url('set'));
                }
            }
        }
    }

    /**
     * 保存64位编码图片
     */

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

    public function editpwd(){
        $this->checkLogin();
        $uid = $this->fansInfo['id'];
        $userinfo = Db::name('user_info')->where(['id'=>$uid])->field('portrait,wechaname,sex,birthday,phonenum')->find();
        $this->assign('userinfo',$userinfo);
    }

    //获得验证码
    public function geteditpwdCode()
    {
        $request=request();
        if($request->method()=='POST')
        {
            $data = input('post.');
            $result = $this->validate($data, 'ValidateClass.CK26');
            if (true !== $result)
            {
                return array('code'=>0,'msg'=>"$result");
            }
            $val = $this->checksmstime($data['phonenum2'],1);
            if(!$val){
                return array('code'=>0,'msg'=>'请等待时间60秒后重新发送！');
            }
            //随机6位验证码
            $rand=rand(111111,999999);
            Session::set('telCode',$rand);           //记录验证码
            Session::set('tel',$data['phonenum2']);   //记录发送验证码的手机号码
            $code['phone']=$data['phonenum2'];
            $codeCont=config('codeCont');
            $code['content']=str_replace('{code}',$rand,$codeCont);
            $message=new Message();
            @$return=$message->APIHttpRequestCURL($code,'get');
            // var_dump($return);die;
            if($return['status']==0)
            {
                $arr = array(
                    'sms_code' =>$rand,
                    'sms_mobile' =>$data['phonenum2'],
                    'sms_addtime' =>time(),
                    'sms_state' =>0,
                    'sms_wecha_id' =>$this->fansInfo['wecha_id'],
                    'sms_returnstatus' =>json_encode($return),
                    'sms_type' =>6,
                );
                $smslog = new SmslogModel();
                $smslog->allowField(true)->save($arr);
                return array('code'=>1);
            }
            else
            {
                return array('code'=>0,'msg'=>'短信接口错误！');
            }
        }
    }

    public function doeditpwd(){
        $data = input('post.');
        if(!$data['code'] || !$data['pwd'] || !$data['confirmpwd']){
            $this->error('数据错误');
        }
        if($data['pwd']!=$data['confirmpwd']){
            $this->error('密码与确认密码不一致');
        }
        $phonenum = Db::name('user_info')->where(['wecha_id'=>$this->fansInfo['wecha_id']])->field('phonenum')->find();
        $datas['sms_mobile'] = $phonenum['phonenum'];
        $datas['sms_type']=6;
        $datas['sms_wecha_id']=$this->fansInfo['wecha_id'];
        $smslog = new SmslogModel();
        $sms = $smslog->where($datas)->order('sms_id desc')->find();
        if(!$sms){
            $this->error('请先点击发送验证码');
        }elseif($sms['sms_code']!=$data['code']) {
            $this->error('验证码错误');
        }elseif($sms['sms_state']==1){
            $this->error('验证码已经被使用');
        }elseif($sms['sms_addtime']+300<time()){
            $this->error('验证码已经失效');
        }else{
            $user = new UserInfoModel();
            $val = $user->allowField(true)->where(['id'=>$this->fansInfo['id']])->update(['password'=> md5($data['pwd'])]);
            if($val){
                $this->success('修改成功',url('setoption/set'));
            }else{
                $this->error('修改失败',url('setoption/set'));
            }
        }
    }
}