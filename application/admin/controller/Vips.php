<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * 跟踪反馈
 */

namespace app\admin\controller;

use app\index\model\SmslogModel;
use app\index\event\Message;
use think\Db;
use think\Session;
class Vips extends Admin
{

    public function _initialize()
    {
//        parent::_initialize();
    }

    private function getwhere($data){
        $where = ['state'=>1,'type'=>0];

        $arr = ['wechaname','truename','phonenum','id','wecha_id'];
        foreach ($arr as $k=>$v){
            if(isset($data[$v]) && !empty(trim($data[$v]))){
                if($v == 'wechaname'||$v == 'truename'){
                    $where[$v] = ['like','%'.$data[$v].'%'];
                }else{
                    $where[$v] = $data[$v];
                }
            }
        }

        if(!empty($data['regtime'])){
            $time = explode(' - ',$data['regtime']);
            $where['regtime'] = [
                ['>=',strtotime($time['0'])],
                ['<=',strtotime($time['1'])],
            ];
        }

        return $where;
    }

    public function index(){
        $data = input('param.');
        $this->assign('datas',$data);
        $where = $this->getwhere($data);
        $list = Db::name('user_info')->where($where)->order('id desc')->paginate(15,false,['query' => request()->param()]);

        //查询会员组
        $vipclass = Db::name('vipclass')->select();

        $this->assign('list',$list);
        $this->assign('viplass',$vipclass);
        return $this->fetch();
    }

    /**
     * 分类删除
     */
    public function del(){
        $id=input('param.id');
        $va = Db::name('user_info')->where(['id'=>$id])->update(['state'=>2]);
        if($va){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    public function alter(){
        $request=request();
        if($request->method()=='POST'){
            $data = input('param.');

            $arr = ['wechaname','vipclass','truename','sex','emotion','signature','id_card','integral'];
            if(in_array($data['types'],$arr)){
                $vals = Db::name('user_info')->where(['id'=>$data['id']])->update([$data['types']=>$data[$data['types']]]);
            }elseif($data['types']=='phonenum'){
                $smslog = new SmslogModel();
                $mobile = $data['phonenum'];
                $smsinfo = $smslog->where(['sms_mobile'=>$mobile,'sms_type'=>7])->order('sms_id desc')->find();
                if($smsinfo['sms_state']!=0){
                    $this->error('验证码已使用过。');
                    $this->error('验证码已使用过。');
                    $this->error('验证码已使用过。');
                }elseif(($smsinfo['sms_addtime']+300)>time()){
                    $this->error('验证码过期，请重新发送。');
                }elseif($data['code']!=$smsinfo['sms_code']){
                    $this->error('验证码错误。');
                }else{
                    Session::delete('sms_time');
                    Session::delete('sms_code');
                    $vals = Db::name('user_info')->where(['id'=>$data['id']])->update(['phonenum'=>$data['phonenum']]);
                }
            }elseif($data['types']=='password'){
                $vals = Db::name('user_info')->where(['id'=>$data['id']])->update(['password'=>md5($data['password'])]);
            }elseif($data['types']=='portrait'){
                $url = $this->saveBase64Images($data['files']);
                $vals = Db::name('user_info')->where(['id'=>$data['id']])->update(['portrait'=>$url]);
            }elseif($data['types']=='birthday'){
                $vals = Db::name('user_info')->where(['id'=>$data['id']])->update(['birthday'=>strtotime($data['birthday'])]);
            }elseif($data['types']=='address'){
                $province = $data['province'];
                $citys = $data['citys'];
                $areas = $data['areas'];
                $vals = Db::name('user_info')->where(['id'=>$data['id']])->update(['address'=>$data['address'], 'province'=>$province, 'citys'=>$citys, 'areas'=>$areas]);
            }elseif($data['types']=='hobby'){
                $vals = Db::name('user_info')->where(['id'=>$data['id']])->update(['hobby'=>implode(',',$data['hobby'])]);
            }
            if($vals){
                $this->success('修改成功');
            }else{
                $this->error('修改失败');
            }
//            var_export($data);die;
        }else{
            $id = input('param.id',0);
            $this->assign('id',$id);
            $info = Db::name('user_info')->find($id);
            empty($info['vipclass'])?$vip=1:$vip=$info['vipclass'];
            $vip = Db::name('vipclass')->find($vip);
            $this->assign('info',$info);
            $this->assign('vip',$vip);
            return $this->fetch();
        }
    }

    public function checkvipclassset(){
        $vcs = Db::name('vipclasssetting')->find(1);
        if($vcs['vcs_type']!=1){
            $this->error('会员组变更规则设置为【不自动变更】时才能修改会员组');
        }else{
            $this->success('可以变更');
        }
    }

    public function editval(){
        $id=input('param.id');
        $type=input('param.type');
        if(in_array($type,['portrait','wechaname','truename','phonenum','id_card','sex','birthday','emotion','signature','hobby','id_card','integral'])){
            $val = Db::name('user_info')->field($type)->find($id);
        }elseif($type=='password'){
            $val = null;
        }elseif($type=='address'){
            $val = Db::name('user_info')->field('address,province,citys,areas')->find($id);
        }else{
            $info = Db::name('user_info')->field('vipclass')->find($id);
            empty($info['vipclass'])?$vip=1:$vip=$info['vipclass'];
            $vip = Db::name('vipclass')->find($vip);
            $val = '';
            $vipclass = Db::name('vipclass')->select();
            $this->assign('viplass',$vipclass);
            $this->assign('vip',$vip);
        }
        $this->assign('val',$val);
        $this->assign('id',$id);
        $this->assign('type',$type);
        return $this->fetch();
    }


    public function getclasstree(){
        $h_class = Db::name('virtualvipclass')->where(['vc_pid'=>0])->select();
        $class_arr = [];
        foreach ($h_class as $k => $v){
            $child= Db::name('virtualvipclass')->where(['vc_pid'=>$v['vc_id']])->select();
            $childarr = [];
            foreach ($child as $kc=>$vc){
                $childarr[$vc['vc_id']] = $vc['vc_name'];
            }
            $class_arr[$v['vc_id']] = [
                'name'=>$v['vc_name'],
                'child'=>$childarr
            ];
        }
        return $class_arr;
    }

    public function alterint(){
        $id = input('param.id',0);
        $list = Db::name('integral_reg')->where(['uid'=>$id])->order('id desc')->paginate(15);
        $admin_user = Db::name('admin')->field('id,realname')->where(['status'=>1,'del'=>0])->select();
        $admin_user = array_column($admin_user,'realname','id');
        $user = Db::name('user_info')->field('integral')->find($id);
        $this->assign('admin_user',$admin_user);
        $this->assign('user',$user);
        $this->assign('list',$list);
        $this->assign('id',$id);
        return $this->fetch();
    }

    public function doalterint(){

        $request=request();
        if($request->method()=='POST') {
            $data = input('param.');
            $id = $data['id'];
            $user = Db::name('user_info')->field('integral')->find($id);
            Db::startTrans();
            $integral = intval($data['integral']);
            if(!$integral){
                $this->success('修改成功');
            }else{
                $intarr = [
                    'uid'=>$id,
                    'original'=>$user['integral'],
                    'nums'=>abs($integral),
                    'update_time'=>time(),
                    'create_time'=>time(),
                    'douid'=>Session::get('adminid'),
                    'note'=>trim($data['note'])
                ];
                if($integral>0){
                    $intarr['type'] = 0;
                    Db::name('integral_reg')->insert($intarr);
                    if(Db::getLastInsID()){
                        $vals = Db::name('user_info')->where(['id'=>$id])->setInc('integral',abs($integral));
                        if($vals){
                            Db::commit();
                            $this->success('修改成功');
                        }else{
                            Db::rollback();
                            $this->error('修改失败');
                        }
                    }else{
                        Db::rollback();
                        $this->error('修改失败');
                    }
                }else{
                    $intarr['type'] = 1;
                    Db::name('integral_reg')->insert($intarr);
                    if(Db::getLastInsID()){
                        $vals = Db::name('user_info')->where(['id'=>$id])->setDec('integral',abs($integral));
                        if($vals){
                            Db::commit();
                            $this->success('修改成功');
                        }else{
                            Db::rollback();
                            $this->error('修改失败');
                        }
                    }else{
                        Db::rollback();
                        $this->error('修改失败');
                    }
                }
            }

        }else{
            $id = input('param.id',0);
            $user = Db::name('user_info')->field('integral,id,vipclass,wechaname,truename')->find($id);
            $vipclass = Db::name('vipclass')->select();
            $this->assign('viplass',$vipclass);
            $this->assign('user',$user);
            $this->assign('id',$id);
            return $this->fetch();
        }
    }

    public function getcode(){
        $data = input('post.');
        if(!trim($data['phonenum'])){
            $this->error('请正确填写手机号');
        }
        $val = $this->checksmstime($data['phonenum'],7);
        if(!$val){
            return array('code'=>0,'msg'=>'请等待时间60秒后重新发送！');
        }
        //随机6位验证码
        $rand=rand(111111,999999);
        Session::set('telCode',$rand);           //记录验证码
        Session::set('tel',$data['phonenum']);   //记录发送验证码的手机号码
        $code['phone']=$data['phonenum'];
        $codeCont='【雍享汇】您的验证码是：{code},5分钟内有效，请勿泄漏。如非本人操作，请忽略此信息。';
        $code['content']=str_replace('{code}',$rand,$codeCont);
        $message=new Message();
        @$return=$message->APIHttpRequestCURL($code,'get');
        if($return['status']==0)
        {
            $arr = array(
                'sms_code' =>$rand,
                'sms_mobile' =>$data['phonenum'],
                'sms_addtime' =>time(),
                'sms_state' =>0,
                'sms_returnstatus' =>json_encode($return),
                'sms_type' =>7,
            );
//            var_export($arr);
            $smslog = new SmslogModel();
            $smslog->allowField(true)->save($arr);
            Session::set('sms_code',$rand);
            return array('code'=>1,'msg'=>$rand);
        }
        else
        {
            return array('code'=>0,'msg'=>'短信接口错误！');
        }
    }

    public function saveBase64Images($base64_image_content){

        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
            //图片后缀
            $type = $result[2];
            //保存位置--图片名
            $image_name=date('His').str_pad(mt_rand(1, 99999999), 5, '0', STR_PAD_LEFT).".".$type;
            $image_url = '/uploads/'.date('Ymd').'/'.$image_name;
            $image_path = '/uploads/'.date('Ymd').'/';
            if(!is_dir(dirname('.'.$image_url))){
                mkdir(dirname('.'.$image_url),0700,true);
            }
            //解码
            $decode=base64_decode(str_replace($result[1], '', $base64_image_content));
            if (file_put_contents('.'.$image_url, $decode))
            {
                $data['url']=$image_url;
            }else{
                $data['url']='';
            }
        }else{
            $data['url']=$base64_image_content;
        }
        return $data['url'];
    }

    private function checksmstime($mobile,$type){
        $smslog = new SmslogModel();
        $smsinfo = $smslog->where(['sms_mobile'=>$mobile,'sms_type'=>$type,'sms_state'=>0])->order('sms_id desc')->find();
        if($smsinfo){
//            echo $smsinfo['sms_addtime']+60;
//            echo '--------';
//            echo time();
            if(($smsinfo['sms_addtime']+60)>time()){
                return false;
            }else{
                return true;
            }
        }else{
            return true;
        }
    }
}