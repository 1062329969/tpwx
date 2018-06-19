<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 微信支付的回调
 */

namespace app\index\controller;
use app\admin\model\StageModel;
use app\admin\model\StagearticleModel;
use app\index\model\DetectionphotoModel;
use app\index\model\ExerciseModel;
use app\index\event\TemplateMessage;
use app\index\model\DetectionModel;
use app\index\model\HospitalModel;
use app\index\model\Readmodel;
use app\index\model\ReservationModel;
use app\index\model\StagearticlepraiseModel;
use app\index\model\StagecommentModel;
use app\index\model\StagepraiseModel;
use app\index\model\TipsModel;
use think\Db;
use ApiOauth\ApiOauth;
use think\Session;

class Track extends Wap
{
    protected $type = array('xie'=>1,'yang'=>2,'you'=>3,'cha'=>4,'tuo'=>5);
    protected function _initialize()
    {
        //获得授权
        $this->getUserInfo();
        $this->checkLogin();
        $this->assign('fansInfo',$this->fansInfo);
    }

    public function index(){
        $detectionModel = new DetectionModel();
        $detection = $detectionModel->getLastud($this->fansInfo['id']);
        if($detection){
            $sum = $detection['ud_'.$detection['ud_key']];
            $t_type = $this->type[$detection['ud_key']];
            $tipsmodel = new TipsModel();
            $tipsinfo = $tipsmodel->gettipsbykeyandsum($sum,$t_type);
        }else{
            $tipsinfo=NULL;
        }
        if(json_decode($detection['ud_fraction'],true)){
            $fenshu = array_values(json_decode($detection['ud_fraction'],true));
        }else{
            $fenshu = array();
        }


        foreach ($fenshu as $key => $value) {
            if($value!=0){
                $fenshu[$key] = $value+2;
            }else{
                $fenshu[$key] = $value+3;
            }
        }
        $this->assign('tipsinfo',$tipsinfo);
        $this->assign('fenshu',json_encode($fenshu));
        $stage_type = $this->fansInfo['stage_type'];
        $this->assign('stage_type',$stage_type);

        $info = Db::name('detectionphoto')->where(['dp_uid'=>$this->fansInfo['id']])->find();
        $this->assign('info',$info);

        return $this->fetch();
    }

    public function getdetectioninfo(){
        $detectionModel = new DetectionModel();
        $detection = $detectionModel->getLastud($this->fansInfo['id']);
        if($detection){
            $sum = $detection['ud_'.$detection['ud_key']];
            $t_type = $this->type[$detection['ud_key']];
            $tipsmodel = new TipsModel();
            $tipsinfo = $tipsmodel->gettipsbykeyandsum($sum,$t_type);
        }else{
            $tipsinfo=NULL;
        }
        if(json_decode($detection['ud_fraction'],true)){
            $fenshu = array_values(json_decode($detection['ud_fraction'],true));
        }else{
            $fenshu = array();
        }


        foreach ($fenshu as $key => $value) {
            if($value!=0){
                $fenshu[$key] = $value+2;
            }else{
                $fenshu[$key] = $value+3;
            }
        }
        return json(['tipsinfo'=>$tipsinfo,'fenshu'=>json_encode($fenshu)]);
    }

    public function uploadimg(){
        $imgs=array();
        $files = request()->file('pic2');
        $data=input('post.');
        if(count($files)>0){
            foreach($files as $file){
                // 移动到框架应用根目录/public/uploads/ 目录下
                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                if($info){
                    $returnImg=str_replace('\\','/','/uploads/'.$info->getSaveName());
                    $imgs[]=$returnImg;
                }else{
                    $this->error($file->getError());
                }
            }
        }elseif(isset($data['pic2'])&&!empty($data['pic2'])){
            $url = $this->saveBase64Images($data['pic2']);
            $imgs[] = $url['url'];
        }else{
            $this->error('提交失败');
        }
        $dp_upload_number = $this->_checknum();
        if($dp_upload_number>=3){
            $this->error('您的上传次数超出限制');
        }
        $have = $this->checkhaveimg();
        if($have){
            $this->error('您已做过检测，检测人员已回复您。');
        }
        $info = Db::name('detectionphoto')->where(['dp_uid'=>$this->fansInfo['id'],'dp_state'=>0])->find();
        if($info){
            $imgarr = json_decode($info['dp_img'],true);
            $ish = 1;
        }else{
            $ish = 0;
            $imgarr = array();
        }
        $imgarr = array_merge($imgarr,$imgs);
        $dp = new DetectionphotoModel();
        if($ish){
            $datas = array(
                'dp_img'=>json_encode($imgarr),
                'dp_addtime'=>time(),
                'dp_upload_number'=>$info['dp_upload_number']+1
            );
            if($dp->where(['dp_id'=>$info['dp_id']])->update($datas)){
                $this->success('提交成功');
            }else{
                $this->error('提交失败');
            }
        }else{
            $datas = array(
                'dp_img'=>json_encode($imgarr),
                'dp_addtime'=>time(),
                'dp_uid'=>$this->fansInfo['id'],
                'dp_state'=>0,
                'dp_upload_number'=>1
            );
            if($dp->allowField(true)->save($datas)){
                $this->success('提交成功');
            }else{
                $this->error('提交失败');
            }
        }

    }

    public function checknum(){
        $info = Db::name('detectionphoto')->where(['dp_uid'=>$this->fansInfo['id']])->field('dp_upload_number')->find();
        if(!$info){
            $info['dp_upload_number'] = 0;
        }
        if($info['dp_upload_number']>=3){
            $this->error('您的提交次数超出限制');
        }else{
            $this->success($info['dp_upload_number']);
        }
    }

    private function _checknum(){
        $info = Db::name('detectionphoto')->where(['dp_uid'=>$this->fansInfo['id']])->field('dp_upload_number')->find();
        if(!$info){
            $info['dp_upload_number'] = 0;
        }
        return $info['dp_upload_number'];
    }

    private function checkhaveimg(){
        $info = Db::name('detectionphoto')->where(['dp_uid'=>$this->fansInfo['id'],'dp_state'=>1])->find();
        if($info){
            return true;
        }else{
            return false;
        }
    }
    public function saveBase64Images($base64_image_content){

        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){

            //图片后缀
            $type = $result[2];

            //保存位置--图片名
            $image_name=date('His').str_pad(mt_rand(1, 99999999), 5, '0', STR_PAD_LEFT).".".$type;
            $image_url = '/uploads/'.date('Ymd').'/'.$image_name;
            if(!is_dir(dirname('.'.$image_url))){
                mkdir(dirname('.'.$image_url),0700,true);
            }
            //解码
            $decode=base64_decode(str_replace($result[1], '', $base64_image_content));
            if (file_put_contents('.'.$image_url, $decode)){
                $data['code']=0;
                $data['imageName']=$image_name;
                $data['url']=$image_url;
                $data['msg']='保存成功！';
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
    public function reservation()
    {
        $ReservationModel = new ReservationModel();
        $val = $ReservationModel->getusertoday($this->fansInfo['id']);
        if($val){
            $this->error('预约次数超出限制');
        }
        $request=request();
        if($request->method()=='POST')
        {

            $data=input('post.');
            $data['res_name'] = urldecode($data['res_name']);
            $data['res_content'] = urldecode($data['res_content']);
            $data['res_sex'] = intval($data['res_sex']);
//            $data['res_project'] = intval($data['res_project']);
            $data['res_hid'] = intval($data['res_hid']);
            $data['res_age'] = intval($data['res_age']);
            $data['res_addtime'] = time();
            $dtime = $data['res_time'];
            $data['res_time'] = strtotime($data['res_time']);
            if($data['res_time']<strtotime(date('Y-m-d',time()))){
                $this->error('只能预约当前之间之后');
            }elseif($data['res_time']-strtotime(date('Y-m-d',time()))>2592000){
                $this->error('最长预约时间为一个月');
            }
            $data['res_state'] = 0;
            $data['res_uid'] = $this->fansInfo['id'];
            $data['res_code'] = strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));

            if($ReservationModel->allowField(true)->save($data)){
                $openid = $this->fansInfo['wecha_id'];  //提交的用户
                $dh = Db::name('hospital')->field('hosname')->find($data['res_hid']);
                //发送模板消息
                $templateMessage=new TemplateMessage();
                $sData["wecha_id"]=$openid;
                $sData["tempid"]='p3Nii2VziAZ55gZ0CMyn9S2lxcLUDGXFzjb1_zjWerU';
                $sData['first']='您好，您已经预约成功';
                $sData['keyword1']=$data['res_name'];
                $sData['keyword2']=$data['res_tel'];
                $sData['keyword3']=$dtime;
                $sData['keyword4']=$dh['hosname'];
                $sData['remark']='请及时到达就诊';
                $sData["href"]= config('domain').url('myreservation/index');
                $sData["topcolor"]='';
                $apiOauth=new ApiOauth();
                $access_token=$apiOauth->update_authorizer_access_token(['appid'=>config('Appid'),'appsecret'=>config('Appsecret')]);
                $templateMessage->sendTempMsg(6,$sData,$access_token);
                $this->success('预约成功',url('myreservation/index',['type'=>2]));
            }else{
                echo $ReservationModel->getLastSql();
                $this->error('预约失败');
            }

        }else{
            $HospitalModel = new HospitalModel();
            $hlist = $HospitalModel->getall();
            $this->assign('hlist',$hlist);
            return $this->fetch();
        }

    }

    public function detection(){
        $ExerciseModel = new ExerciseModel();
        $detectionModel = new DetectionModel();
        $byuid = $detectionModel->getLastud($this->fansInfo['id']);
        // if($byuid){
        //     $this->error('您无需重复测试');
        // }
        $request=request();
        if($request->method()=='POST'){
            $data=input('post.');
            $newdata['ud_uid'] = $this->fansInfo['id'];
            $newdata['ud_time'] = time();
            $newdata['ud_exercise'] = json_encode($data['ud_exercise']);
            $newdata['ud_fraction'] = json_encode($data['ud_fraction']);
            $newdata['ud_code'] = json_encode($data['ud_code']);
            $newdata['ud_xie'] = $data['ud_fraction']['xie'];
            $newdata['ud_yang'] = $data['ud_fraction']['yang'];
            $newdata['ud_you'] = $data['ud_fraction']['you'];
            $newdata['ud_cha'] = $data['ud_fraction']['cha'];
            $newdata['ud_tuo'] = $data['ud_fraction']['tuo'];

            $key = $this->gettopkey($data['ud_fraction']);
            $newdata['ud_key'] = $key;

            if($detectionModel->save($newdata)){
                $this->success('提交成功');
            }else{
                $this->error('提交失败');
            }
        }else{
            if($this->fansInfo['sex']=='男'){
                $sex = 1;
            }elseif($this->fansInfo['sex']=='女'){
                $sex = 2;
            }else{
                $this->error('请先设置性别',url('setoption/set'));
            }
            $Exercise = $ExerciseModel->getcheckall($sex);
            // var_dump($Exercise);die;
            $this->assign('exerciselist',$Exercise);
            return $this->fetch();
        }
    }

    private function gettopkey($ud_fraction){
        arsort($ud_fraction);
        $temp = array();
        $va = -1;
        foreach($ud_fraction as $k=>$v){
            if($va==-1){
                $temp[$k] = $v;
                $va = $v;
            }else{
                if($va==$v){
                    $temp[$k] = $v;
                }else{
                    break;
                }
            }
        }

        $key = '';
        foreach (array('tuo', 'cha', 'yang', 'xie', 'you' ) as $kt=>$vt){
            if(isset($temp[$vt])){
                $key = $vt;
                break;
            }
        }
        return $key;
    }

    public function stage(){
        $from=input('param.from');
        if($from=='index'){
            Session::set('from','index');
        }elseif($from=='bottom'){
            Session::set('from','bottom');
        }
        $stage_type = $this->fansInfo['stage_type'];
        $this->assign('stage_type',$stage_type);
        if($stage_type){
            $time = time()-$stage_type;
            if($time<(86400*7)){
                $period = 1;
            }elseif($time>(86400*7) && $time<(86400*120)){
                $period = 2;
            }else{
                $period = 3;
            }
        }else{
            $period = 0;
        }
        $stage1 = StageModel::find(1);
        $stage1_count = Db::name('stagearticle')->where(['a_sid'=>1])->count();
        $stage2 = StageModel::find(2);
        $stage2_count = Db::name('stagearticle')->where(['a_sid'=>2])->count();
        $stage3 = StageModel::find(3);
        $stage3_count = Db::name('stagearticle')->where(['a_sid'=>3])->count();
        $this->assign('stage1',$stage1);
        $this->assign('stage1_count',$stage1_count);
        $this->assign('stage2',$stage2);
        $this->assign('stage2_count',$stage2_count);
        $this->assign('stage3',$stage3);
        $this->assign('stage3_count',$stage3_count);
        $this->assign('period',$period);


        return $this->fetch();

    }

    public function stagearticle(){
        $sid=input('param.sid',1);
        $a_type=input('param.a_type',3);
        $stage = StageModel::find($sid);
        $stage_type = $this->fansInfo['stage_type'];
        $this->assign('stage_type',$stage_type);
        if($stage_type){
            $time = time()-$stage_type;
            if(($time<(86400*7) && $sid>1) || ($time>(86400*7) && $time<(86400*120) && $sid>2)){
                $this->error('您暂时无法查看该阶段');
            }

            $list = Db::name('stagearticle sa')
                // ->join('stagearticlepraise sap','sa.a_id=sap.p_said','LEFT')
                ->where(['sa.a_sid'=>$sid,'sa.a_type'=>$a_type,'a_video_show'=>1])
                ->order('sa.a_order desc')
                ->select();
            foreach ($list as $key => $value) {
                $plist = Db::name('stagearticlepraise')->where('p_said',$value['a_id'])->field('p_uid')->select();
                // echo Db::getLastSql();
                // var_dump();die;
                $list[$key]['p_uidarr'] = array_column($plist,'p_uid');
            }
            //记录消息
            $res = Db::name('read')->where(['uid'=>$this->fansInfo['id'],'sid'=>$sid])->find();
            if(empty($res)){
                $read = new Readmodel();
                $read ->save(['uid'=>$this->fansInfo['id'],'sid'=>$sid,'create_time'=>time()]);
            }

            $this->assign('sid',$sid);
            $this->assign('stage',$stage);

            $this->assign('a_type',$a_type);
            $this->assign('list',$list);
            return $this->fetch();
        }else{
            $this->error('您不是术后用户');
        }
    }

    public function commontlist(){
        $aid=input('param.aid',1);
        $commontlist = Db::name('stagecomment sc')
            ->where(['c_aid'=>$aid,'c_tid'=>0])
            ->join('stagepraise sp','sc.c_id=sp.p_cid','LEFT')
            ->order('c_id asc')
            ->paginate(1)->toArray();
        // var_dump($commontlist);die;
        foreach ($commontlist['data'] as $key => $value) {
            $commontlist['data'][$key]['child'] = Db::name('stagecomment')->where(['c_aid'=>$aid,'c_tid'=>$value['c_id']])->order('c_id asc')->select();
            // echo Db::getLastSql();die;
        }
        // var_dump($commontlist);die;
        return json($commontlist);
    }

    public function stagearticleinfo(){
        $aid=input('param.aid',1);
        $sid=input('param.sid',1);
        $stage = StageModel::find($sid);
        $this->assign('stage',$stage);
        if($aid){
            //对两表添加浏览数
            StageModel::where(['s_id'=>$sid])->setInc('s_browse');
            StagearticleModel::where(['a_id'=>$aid])->setInc('a_browse');
            $Stagearticle = StagearticleModel::find($aid);
            $praise = StagearticlepraiseModel::where(['p_said'=>$aid,'p_uid'=>$this->fansInfo['id']])->find();
            $this->assign('stagearticle',$Stagearticle);
            $this->assign('praise',$praise);

            $alluid = Db::name('stagecomment sc')->where(['c_aid'=>$aid])->field('c_uid')->select();
            $newalluid = array_column($alluid,'c_uid');
            $alluser = Db::name('user_info')->where('id','in',$newalluid)->field('id,wechaname,portrait')->select();
            $newuser = array();
            foreach ($alluser as $uk => $uv) {
                $newuser[$uv['id']] = $uv;
            }

            $commontlist = Db::name('stagecomment sc')
                ->where(['c_aid'=>$aid,'c_tid'=>0])
                // ->join('stagepraise sp','sc.c_id=sp.p_cid','LEFT')
                ->order('c_id asc')
                ->select();
            //     echo Db::getLastSql();die;

            foreach ($commontlist as $key => $value) {
                $praise = Db::name('stagepraise')->where(['p_cid'=>$value['c_id']])->select();
                $commontlist[$key]['praise'] = array_column($praise,'p_uid');
                $commontlist[$key]['child'] = Db::name('stagecomment')->where(['c_aid'=>$aid,'c_tid'=>$value['c_id']])->order('c_id asc')->select();
            }

            // var_dump($commontlist);die;
            $this->assign('alluser',$newuser);
            $this->assign('sid',$sid);
            $this->assign('commontlist',$commontlist);
            return $this->fetch();
        }else{
            $this->error('您不是术后用户');
        }
    }

    public function stagearticlepraise(){
        $StagearticlepraiseModel = new StagearticlepraiseModel();
        $StagearticleModel = new StagearticleModel();
        if(input('param.data')==0){
            $data['p_said'] = $aid = input('param.aid',1);
            $data['p_uid'] = $this->fansInfo['id'];
            $data['p_time'] = time();

            if($StagearticlepraiseModel->allowField(true)->save($data)){
                $StagearticleModel->where(['a_id'=>$aid])->setInc('a_collection');
                $this->success('成功');
            }else{
                $this->error('失败');
            }
        }else{
            $data['p_said'] = $aid = input('param.aid',1);
            $data['p_uid'] = $this->fansInfo['id'];
            if($StagearticlepraiseModel->where($data)->delete()){
                $StagearticleModel->where(['a_id'=>$aid])->setDec('a_collection');
                $this->success('成功');
            }else{
                $this->error('失败');
            }
        }
    }

    public function stagearticlecomment(){
        $data=input('post.');
        $datas['c_uid'] = $this->fansInfo['id'];
        $datas['c_content'] = $this->filter($data['commnet_text']);
        $datas['c_oldcontent'] = $data['commnet_text'];
        $datas['c_aid'] = $data['aid'];
        $datas['c_pid'] = $data['pid'];
        $datas['c_tid'] = $data['tid'];
        $datas['c_cuid'] = $data['cuid'];
        if($data['cuid'] == $this->fansInfo['id']){
            $this->error('不能回复自己的评论');
        }
        $datas['c_addtime'] = time();
        $datas['c_praise'] = 0;
        $StagecommentModel = new StagecommentModel();
        if($StagecommentModel->allowField(true)->save($datas)){
            if($data['tid']==0){
                Db::name('stagearticle')->where(['a_id'=>$data['aid']])->setInc('a_reply');
            }
            $this->success('成功');
        }else{
            $this->error('失败');
        }
    }

    public function stagearticlecommentpraise(){
        $StagepraiseModel = new StagepraiseModel();
        $StagecommentModel = new StagecommentModel();
        $data=input('get.');
        if($data['data']==0){
            $datas['p_uid'] = $this->fansInfo['id'];
            $datas['p_said'] = $data['p_said'];
            $datas['p_cid'] = $data['p_cid'];
            $datas['p_time'] = time();

            if($StagepraiseModel->allowField(true)->save($datas)){
                $StagecommentModel->where(['c_id'=>$data['p_cid']])->setInc('c_praise');
                $this->success('成功');
            }else{
                $this->error('失败');
            }
        }else{
            $datas['p_cid'] = $data['p_cid'];
            $datas['p_uid'] = $this->fansInfo['id'];
            if($StagepraiseModel->where($datas)->delete()){
                $StagecommentModel->where(['c_id'=>$data['p_cid']])->setDec('c_praise');
                $this->success('成功');
            }else{
                $this->error('失败');
            }
        }
    }

}
?>