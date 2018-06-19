<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * 跟踪反馈
 */

namespace app\admin\controller;


use ApiOauth\ApiOauth;
use app\admin\model\LogModel;
use app\admin\model\QuestionnaireModel;
use think\Db;
use think\Log;
use think\Session;

class Questionnaire extends Admin
{

    protected $QuestionnaireModel;
    protected $abc = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q");
    public function _initialize()
    {
        parent::_initialize();
        $this->QuestionnaireModel = new QuestionnaireModel();
    }


    /**
     * 首页
     * @return mixed
     */
    public function index(){
        $data = input('param.');
        $where = array('q_state'=>['<>',4]);//4是已经删除的
        $strarr = array();
        if(isset($data['q_name']) && !empty($data['q_name'])){
            $where['q_name'] = ['like','%'.$data['q_name'].'%'];
            $strarr['q_name'] =$data['q_name'];
        }
        if(isset($data['q_state']) && !empty($data['q_state'])){
            $where['q_state'] = $data['q_state'];
            $strarr['q_state'] = $data['q_state'];
        }
        $list = Db::name('questionnaire')->where($where)->paginate(15,false,['query' => request()->param()]);
//        var_dump($list);
        $this->assign('where',$strarr);
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function add(){
        $datas = array();

        $request=request();
        if($request->method()=='POST') {
            $data = input('param.');
            $datas['q_name'] = $data['q_name'];
            $datas['q_gift'] = $this->saveBase64Images($data['q_gift']);
            $datas['q_rule'] = $data['q_rule'];
            $datas['q_prompt'] = $data['q_prompt'];
            $datas['q_state'] = intval($data['q_state']);
            $datas['q_start'] = strtotime($data['q_start']);
            $datas['q_end'] = strtotime($data['q_end']);
            $datas['q_suggest'] = intval($data['q_suggest']);
            $datas['q_addtime'] = time();
            $datas['q_uid'] = Session::get('adminid');

            $topic = $data['topic'];
            $topicarr = array();
            foreach ($topic as $k => $v) {
                $topicarr[$k]['qt_topic'] = $v['qt_topic'];
                $topicarr[$k]['qt_type'] = $v['qt_type'];
                $qt_options = array();
                foreach ($v['qt_options'] as $kt => $vt) {
                    $qt_options[$kt]['code'] = $this->abc[$kt];
                    $qt_options[$kt]['isopen'] = $vt['isopen'];
                    $qt_options[$kt]['content'] = $vt['options'];
                }
                $topicarr[$k]['qt_options'] = $qt_options;
                $topicarr[$k]['qt_order'] = $v['qt_order'];
                $topicarr[$k]['qt_addtime'] = time();
            }
            $res = $this->QuestionnaireModel->add($datas, $topicarr);
            if ($res) {
                LogModel::savelog(2,2,input('param.'),'添加项目成功');
                $this->success('添加成功', url('questionnaire/index'));
            } else {
                LogModel::savelog(2,2,input('param.'),'添加项目失败');
                $this->error('添加失败', url('questionnaire/index'));
            }
        }else{
            return $this->fetch();
        }
    }
    public function alter(){
        $q_id = input('param.q_id');
        $request=request();
        if($request->method()=='POST') {
            $data = input('param.');
            $datas['q_name'] = $data['q_name'];
            if(isset($data['q_gift']) && !empty($data['q_gift'])){
                $datas['q_gift'] = $this->saveBase64Images($data['q_gift']);
            }
            $datas['q_rule'] = $data['q_rule'];
            $datas['q_prompt'] = $data['q_prompt'];
            $datas['q_start'] = strtotime($data['q_start']);
            $datas['q_end'] = strtotime($data['q_end']);
            $datas['q_suggest'] = intval($data['q_suggest']);
            $res = $this->QuestionnaireModel->allowField(true)->save($datas,['q_id'=>$q_id]);
            if($res){
                LogModel::savelog(2,3,input('param.'),'修改项目成功');
                $this->success('修改成功', url('questionnaire/index'));
            }else{
                LogModel::savelog(2,3,input('param.'),'修改项目失败');
                $this->error('修改失败', url('questionnaire/index'));
            }
        }else{
            $info = Db::name('questionnaire')->find($q_id);
            $this->assign('info',$info);
            $this->assign('q_id',$q_id);
            return $this->fetch();
        }
    }

    public function stop(){
        $res = $this->_editstate();
        if($res){
            LogModel::savelog(2,3,input('param.'),'停用成功');
            $this->success('停用成功', url('questionnaire/index'));
        }else{
            LogModel::savelog(2,3,input('param.'),'停用失败');
            $this->error('停用失败', url('questionnaire/index'));
        }
    }
    public function start(){
        $res = $this->_editstate();
        if($res){
            LogModel::savelog(2,3,input('param.'),'启用成功');
            $this->success('启用成功', url('questionnaire/index'));
        }else{
            LogModel::savelog(2,3,input('param.'),'启用失败');
            $this->error('启用失败', url('questionnaire/index'));
        }
    }

    public function _editstate(){
        $q_id = input('param.q_id');
        $state = input('param.state');
        if(intval($q_id) && intval($state))
        $res = $this->QuestionnaireModel->allowField(true)->save(['q_state'=>$state],['q_id'=>$q_id]);
        return $res;
    }

    public function tmalter(){
        $q_id = input('param.q_id');
        $request=request();
        if($request->method()=='POST') {
            $data = input('param.');
            $qt['qt_order'] = $data['qt_order'];
            $qt['qt_topic'] = $data['qt_topic'];
            $qt['qt_type'] = $data['qt_type'];
            $qt['qt_updatetime'] = time();
            $qt_id = $data['qt_id'];
            Db::startTrans();
            $res = Db::name('questionnairetopic')->where(['qt_id'=>$qt_id])->update($qt);
            if(!$res){
                Db::rollback();
                Log::info('1111111111111111111111111111');
                LogModel::savelog(2,3,input('param.'),'修改题目失败');
                $this->error('修改失败', url('questionnaire/index'),1);
            }else{
                foreach ($data['topic'] as $k=>$v){
                    $option = array();
                    $option['code'] = $this->abc[$k];
                    $option['content'] = $v['options'];
                    $option['isopen'] = $v['isopen'];

                    if(isset($v['option_id']) && !empty($v['option_id'])){
                        $option['updatetime'] = time();
                        $ress = Db::name('questionnaireoption')->where(['qo_id'=>$v['option_id']])->update($option);
                    }else{
                        $option['qtid'] = $qt_id;
                        $ress = Db::name('questionnaireoption')->insert($option);
                    }
                    if(!$ress){
                        Db::rollback();
                        Log::info('222222222222222222222222');
                        LogModel::savelog(2,3,input('param.'),'修改题目失败');
                        $this->error('修改失败', url('questionnaire/index'),'2'.$k);
                    }
                }
                Db::commit();
                Log::info('3333333333333333333333333');
                LogModel::savelog(2,3,input('param.'),'修改题目成功');
                $this->success('修改成功', url('questionnaire/index'),3);
            }
        }else{
            $list = Db::name('questionnairetopic')->where(['qt_qid'=>$q_id])->select();
            foreach ($list as $k=>$v){
                $list[$k]['options'] = Db::name('questionnaireoption')->where(['qtid'=>$v['qt_id']])->select();
            }

            $this->assign('list',$list);
            $this->assign('q_id',$q_id);
            return $this->fetch();
        }
    }

    public function tmadd(){
        $q_id = input('param.q_id');
        $request=request();
        if($request->method()=='POST') {
            $data = input('param.');
            unset($data['q_id']);
            foreach ($data as $k=>$v){
                $topic = array();
                $temp = $v;
                $topic['qt_topic'] = $temp['qt_topic'];
                $topic['qt_type'] = $temp['qt_type'];
                $topic['qt_order'] = $temp['qt_order'];
                $topic['qt_qid'] = $q_id;
                $topic['qt_addtime'] = time();
                Db::startTrans();
                $res = Db::name('questionnairetopic')->insert($topic);
                if($res){
                    $resid = Db::getLastInsID();
                    foreach ($temp['qt_options'] as $ko=>$vo){
                        $options = array();
                        $options['qtid'] = $resid;
                        $options['code'] = $this->abc[$ko];
                        $options['isopen'] = $vo['isopen'];
                        $options['content'] = $vo['options'];
                        $ress = Db::name('questionnaireoption')->insert($options);
                        if(!$ress){
                            Db::rollback();
                            LogModel::savelog(2,3,input('param.'),'添加题目失败');
                            $this->error('添加失败');
                        }
                    }
                }else{
                    Db::rollback();
                    LogModel::savelog(2,3,input('param.'),'添加题目失败');
                    $this->error('添加失败');
                }
            }
            Db::commit();
            LogModel::savelog(2,3,input('param.'),'添加题目成功');
            $this->success('添加成功');
        }else{
            LogModel::savelog(2,3,input('param.'),'添加题目失败');
            $this->error('添加失败');
        }
    }
    public function del(){
        $q_id = input('param.q_id');
        $res = Db::name('questionnaire')->where(['q_id'=>$q_id])->update(['q_state'=>4]);
        if($res){
            LogModel::savelog(2,3,input('param.'),'删除活动成功');
            $this->success('删除成功');
        }else{
            LogModel::savelog(2,3,input('param.'),'删除活动失败');
            $this->error('删除失败');
        }
    }
    public function topicdel(){
        $qt_id = input('param.qt_id');
        $res = Db::name('questionnairetopic')->where(['qt_id'=>$qt_id])->delete();
        if($res){
            LogModel::savelog(2,4,input('param.'),'删除题目成功');
            $this->success('删除成功');
        }else{
            LogModel::savelog(2,4,input('param.'),'删除题目失败');
            $this->error('删除失败');
        }
    }

    public function optionsdel(){
        $dataid = input('param.dataid');
        $res = Db::name('questionnaireoption')->where(['qo_id'=>$dataid])->delete();
        if($res){
            LogModel::savelog(2,4,input('param.'),'删除选项成功');
            $this->success('删除成功');
        }else{
            LogModel::savelog(2,4,input('param.'),'删除选项失败');
            $this->error('删除失败');
        }
    }

    /**
     * 比例
     * @return mixed
     */
    public function proportion(){
        $q_id = input('param.q_id');//活动ID
        $info = Db::name('questionnaire')->field('q_name,q_participate')->find($q_id);//查询活动内容surveyusersinfo
        $pepolesum = $info['q_participate'];
        $list = Db::name('questionnairetopic')->where(['qt_qid'=>$q_id])->select();//查询活动题目
        foreach ($list as $k=>$v) {//循环题目
            $option = Db::name('questionnaireoption')->where(['qtid'=>$v['qt_id']])->select();
            foreach ($option as $ko=>$vo){
                if($pepolesum){
                    $option[$ko]['proportion'] = sprintf('%.4f',$vo['sum']/$pepolesum)*100;
                }else{
                    $option[$ko]['proportion'] = 0;
                }
            }
            $list[$k]['option'] = $option;
        }
        $this->assign('list',$list);
        $this->assign('info',$info);
        return $this->fetch();
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

}