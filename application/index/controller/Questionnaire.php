<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 微信支付的回调
 */

namespace app\index\controller;

use app\index\model\HospitalModel;
use think\Log;
use think\Db;

class Questionnaire extends Wap
{
    protected function _initialize()
    {
        //获得授权
//        $this->getUserInfo();
//        $this->checkLogin();
//        $this->assign('fansInfo',$this->fansInfo);
    }

    public function index(){
        $q_id = input('param.q_id');
        // $q_id = passport_decrypt($q_id,'yongxianghuidiaocha');
        // $q_id = 12;
        $questionnaireinfo = Db::name('questionnaire')->find($q_id);
        $topic  = Db::name('questionnairetopic')->where(['qt_qid'=>$q_id])->order('qt_order asc')->select();
        foreach ($topic as $k=>$v){
            $option = Db::name('questionnaireoption')->where(['qtid'=>$v['qt_id']])->select();
            $topic[$k]['option'] = $option;
        }

        $this->assign('q_id',$q_id);
        $this->assign('questionnaireinfo',$questionnaireinfo);
        $this->assign('topic',$topic);

        if($questionnaireinfo['q_state']==3|| time()>$questionnaireinfo['q_end']){
            $state = 3;
        }
        if($questionnaireinfo['q_state']==2){
            $state = 2;
        }elseif ($questionnaireinfo['q_state']==1){
            $state = 1;
        }
        $hospital = HospitalModel::getAll();

        $this->assign('state',$state);
        $this->assign('hospital',$hospital);
        $pe = Db::name('questionnaire')->where(['q_id'=>$q_id])->setInc('q_pageviews');
        return $this->fetch();
    }

    public function add(){
        $q_id = input('param.s_qid');
        $request=request();
        if($request->method()=='POST') {
            $data = input('param.');
            $info = Db::name('surveyusers')->where(['s_phone'=>$data['s_phone']])->find();
            if($info){
                $this->error('您已参与测试，无法重复参与');
            }
            $surveyusers = array(
                's_name'=>$data['s_name'],
                's_phone'=>$data['s_phone'],
                's_project'=>$data['s_project'],
                's_time'=>strtotime($data['s_time']),
                's_province'=>isset($data['s_province'])?$data['s_province']:'',
                's_city'=>isset($data['s_city'])?$data['s_city']:'',
                's_county'=>isset($data['s_county'])?$data['s_county']:'',
                's_receipt'=>$data['s_receipt'],
                's_feedback'=>$data['s_feedback'],
                's_yard'=>$data['s_yard'],
                's_addtime'=>time(),
                's_qid'=>$q_id,
            );
            Db::startTrans();
            $res = Db::name('surveyusers')->insert($surveyusers);
            $si_sid = Db::getLastInsID();
            if(!$res){
                Log::info('++++++++++++++++++++++++++++++++++++++++');
                Db::rollback();
                $this->error('提交失败');
            }else{
                $pe = Db::name('questionnaire')->where(['q_id'=>$q_id])->setInc('q_participate');
                if(!$pe){
                    Db::rollback();
                    Log::info('/////////////////////////////////////////');
                    Log::info(Db::getLastSql());
                    $this->error('提交失败');
                }
                $info = array();
                $info['si_info'] = json_encode($data['info']);
                $info['si_addtime'] = time();
                $info['si_qid'] = $q_id;
                $info['si_sid'] =$si_sid;
                $val = Db::name('surveyusersinfo')->insert($info);
                if(!$val){
                    Db::rollback();
                    $this->error('提交失败');
                }else{
                    foreach($data['info'] as $k=>$v){
                        foreach ($v['code'] as $kc=>$kv){
                            $option = Db::name('questionnaireoption')->where(['code'=>$kv,'qtid'=>$k])->setInc('sum');
                            if(!$option){
                                Log::info('ooooooooooooooooooooooooo');
                                Db::rollback();
                                $this->error('提交失败');
                            }
                        }
                    }
                    DB::commit();
                    Log::info('sssssssssssssss');
                    $this->success('提交成功');
                }
            }
        }else{
            $this->error('非法操作');
        }
    }

    public function demo(){
        $info = Db::name('surveyusersinfo')->find();
        var_dump(json_decode($info,true));
    }

    public function checkphone(){
        $phone = input('param.phone');
        $result = $this->validate(['phone' => $phone],['phone'=> 'require|length:11|number']);
        if(true !== $result){
            $this->error('手机号格式错误');
        }
        $info = Db::name('surveyusers')->where(['s_phone'=>$phone])->find();
        if($info){
            $this->error('您已参与测试，无法重复参与');
        }else{
            $this->success('可以使用');
        }
    }
}
?>