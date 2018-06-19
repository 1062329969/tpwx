<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * 跟踪反馈
 */

namespace app\admin\controller;


use ApiOauth\ApiOauth;
use app\admin\model\HaircareModel;
use app\admin\model\StagearticleModel;
use app\admin\model\StageModel;
use app\admin\model\TrackImgsModel;
use app\admin\model\TrackModel;
use app\admin\model\VisitModel;
use app\index\event\TemplateMessage;
use app\index\model\UserInfoModel;
use think\Db;
use think\Session;
use app\admin\model\SurveyusersModel;
use think\Loader;

class Surveyusers extends Admin
{
    protected $searchType=[
        1=>'头发种植',
        2=>'顶部加密',
        3=>'美人尖种植',
        4=>'发际线调整',
        5=>'眉毛种植',
        6=>'胡须种植',
        7=>'鬓角种植',
        8=>'体毛种植',
        9=>'疤痕种植',
    ];

    protected $export_arr = ['K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS'];

    public function _initialize()
    {
//        parent::_initialize();
    }

    /**
     * 首页
     * @return mixed
     */
    public function index(){
        $id = input('param.q_id');//获取活动id
        //获取输入的姓名或电话与时间
        $name = input('param.name');
        $start =input('param.time_start');//开始时间
        $over = input('param.time_over');//结束时间
        $time_start = strtotime($start);
        $time_over = strtotime($over);
        if($time_over == strtotime(date('Y-m-d',time()))){
            $time_over = time();
        }

        //模糊查询 根据姓名，电话查询,时间查询
        $user = SurveyusersModel::getss($id,$name,$time_start,$time_over);
        foreach ($user as $k=>$v){
            $user[$k]['user'] = Db::name('user_info')->where(['phonenum'=>$v['s_phone']])->find();
            $user[$k]['activity'] = Db::name('questionnaire')->where(['q_id'=>$v['s_qid']])->find();
        }

        $this->assign('id',$id);
        $this->assign('user',$user);
        $this->assign('name',$name);
        $this->assign('start',$start);
        $this->assign('over',$over);
        $this->assign('searchType',$this->searchType);
        return $this->fetch();

    }

    public function del(){
        $id=input('param.id');
        $ids = explode(',',$id);
        Db::startTrans();
        foreach ($ids as $ki=>$vi){
            $si_info = Db::name('surveyusersinfo')->where(['si_sid'=>$vi])->field('si_info,si_qid')->find();
            $res = Db::name('surveyusers')->where(['s_id'=>$vi])->delete();
            if(!$res){
                Db::rollback();
                return ['code'=>6];
            }
            $info_res = Db::name('surveyusersinfo')->where(['si_sid'=>$vi])->delete();

            if(!$info_res){
                Db::rollback();
                return ['code'=>5];
            }
            $si_info_arr = json_decode($si_info['si_info'],true);
            foreach ($si_info_arr as $ka=>$va){
                $resoption = Db::name('questionnaireoption')->where(['qtid'=>$ka])->where('code','in',$va['code'])->setDec('sum');

                if(!$resoption){
                    Db::rollback();
                    return ['code'=>4];
                }

            }
            $questionres = Db::name('questionnaire')->where(['q_id'=>$si_info['si_qid']])->setDec('q_participate');
            if(!$questionres){
                Db::rollback();
                return ['code'=>3];
            }
        }
        Db::commit();
        return ['code'=>1];
    }

    /**
     * 用户调查信息
     * @return mixed
     */
    public function surveyinfo(){
        $id = input('param.id');
        //根据id查找用户参与的活动
        $quest = Db::name('surveyusers')->find($id);
//       echo "<pre>";
//       var_dump($quest);die;
        //获取活动
        $questionnaire = Db::name('questionnaire')->find($quest['s_qid']);

        //获取题目
        $topic = Db::name('questionnairetopic')->where(['qt_qid'=>$quest['s_qid']])->select();

        foreach ($topic as $key=>$value){
            //获取选项
            $topic[$key]['option'] =Db::name('questionnaireoption')->where(['qtid'=>$value['qt_id']])->select();
//            $topic[$key]['answer'] = Db::name('surveyusersinfo')->where(['si_qid'=>$value['qt_qid']])->find();

        }
        $answer = Db::name('surveyusersinfo')->where(['si_sid'=>$quest['s_id']])->find();
        $res = json_decode($answer['si_info'],true);
//        $option = Db::name('questionnaireoption')->where(['qtid'=>$quest['s_id']])->find();
//        $res = json_decode($option['si_info'],true);
        $this->assign('res',$res);
        $this->assign('quest',$quest);
        $this->assign('questionnaire',$questionnaire);
        $this->assign('topic',$topic);
        $this->assign('answer',$answer);
        $this->assign('searchType',$this->searchType);
        return $this->fetch();
    }
    /**
     * 导出
     * @return mixed
     */
    public function export(){
        $id = input('param.q_id');//活动idv
        $name =urldecode( input('param.name'));//获取用户输入的是姓名还是电话
        $start =input('param.time_start');//开始时间
        $over = input('param.time_over');//结束时间
        $time_start = strtotime($start);
        $time_over = strtotime($over);
        //根据传递过来的参数 查找要导出的用户
        $user = SurveyusersModel::getexport($id,$name,$time_start,$time_over);
        foreach ($user as $k=>$v){
            $names = Db::name('user_info')->field('wechaname')->where(['phonenum'=>$v['s_phone']])->find();
            $user[$k]['answer'] = Db::name('surveyusersinfo')->where(['si_sid'=>$v['s_id']])->find();
            $user[$k]['wechaname']= $names['wechaname'];
        }
        //查询活动名称
            $questionnaire = Db::name('questionnaire')->field('q_name')->find($id);

        //查询医院
        $hospital = Db::name('hospital')->field('id,hosname')->where(['status'=>1,'del'=>0])->select();
        $hospital_arr = array_column($hospital, 'hosname', 'id');
        $headarr = [
            "A1"=>"序号",
            "B1"=>"姓名",
            "C1"=>"手机",
            "D1"=>"手术院部",
            "E1"=>"活动名称",
            "F1"=>"手术时间",
            "G1"=>"项目",
            "H1"=>"提交时间",
            "I1"=>"反馈信息",
            "J1"=>"收货地址",
        ];
        $topicarr = Db::name('questionnairetopic')->where(['qt_qid'=>$id])->select();

        $userletter = $export_arr  = $this->export_arr;
//        foreach ($topicarr as $kt=>$kv){
//            $headarr[$export_arr[0].'1'] = '题目';array_shift($export_arr);
//            $headarr[$export_arr[0].'1'] = '答案';array_shift($export_arr);
//            $option_isopen = Db::name('questionnaireoption')->where(['qtid'=>$kv['qt_id'],'isopen'=>1])->find();
//            $option = Db::name('questionnaireoption')->where(['qtid'=>$kv['qt_id']])->select();
//            $topicarr[$k]['option'] = $option;
//            if($option_isopen){
//                $headarr[$export_arr[0].'1'] = '原因';array_shift($export_arr);
//            }
//        }//确认表头信息结束

        $info = array();
        foreach ($user as $ku=>$vu) {
            $info[$ku]['A'] = $ku+1;
            $info[$ku]['B'] = $vu['s_name'];
            $info[$ku]['C'] = $vu['s_phone'];
            $info[$ku]['D'] = $hospital_arr[$vu['s_yard']];
            $info[$ku]['E'] = $questionnaire['q_name'];
            $info[$ku]['F'] = date('Y-m-d',$vu['s_time']);
            $info[$ku]['G'] = $this->searchType[$vu['s_project']];
            $info[$ku]['H'] = date('Y-m-d',$vu['s_addtime']);
            $info[$ku]['I'] = $vu['s_feedback'];
            $info[$ku]['J'] =$vu['s_province'].=$vu['s_city'].=$vu['s_county'].=$vu['s_receipt'];
            $answer = json_decode($vu['answer']['si_info'],true);
            foreach ($topicarr as $kt=>$kv){
                if($ku==0){
                    $headarr[$export_arr[0].'1'] = '题目';array_shift($export_arr);//确认表头
                    $headarr[$export_arr[0].'1'] = '答案';array_shift($export_arr);//确认表头
                }

                $info[$ku][$userletter[0]] = $kv['qt_topic'];array_shift($userletter);//添加问题内容
                $option = Db::name('questionnaireoption')->where(['qtid'=>$kv['qt_id']])->where('code','in',$answer[$kv['qt_id']]['code'])->select();
                $excel_answer_arr = array();
                foreach ($option as $ko=>$vo){
                    $excel_answer_arr[] = $vo['code'].'、'.$vo['content'];
                }
                $excel_answer = implode(',',$excel_answer_arr);
                $info[$ku][$userletter[0]] = $excel_answer;array_shift($userletter);

                $option_isopen = Db::name('questionnaireoption')->where(['qtid'=>$kv['qt_id'],'isopen'=>1])->find();

                if($option_isopen){
                    $info[$ku][$userletter[0]] = $answer[$kv['qt_id']]['feedback'];array_shift($userletter);
                    if($ku==0) {
                        $headarr[$export_arr[0] . '1'] = '原因';array_shift($export_arr);
                    }
                }
            }
            $userletter = $this->export_arr;
        }
        Loader::import('PHPExcel.PHPExcel');//手动引入PHPExcel.php
        Loader::import('PHPExcel.PHPExcel.IOFactory.PHPExcel_IOFactory');//引入IOFactory.php 文件里面的PHPExcel_IOFactory这个类
        $PHPExcel = new \PHPExcel();//实例化
//        $PHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth('');
        $PHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth('20px');
        $PHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth('20px');
        $PHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth('20px');
        $PHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth('25px');
        $PHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth('30px');
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("Export"); //给当前活动sheet设置名称

        foreach ($headarr as $kh=>$vh){
            $PHPSheet->setCellValue($kh,$vh);
        }
        $ks = 2;
        foreach ($info as $ki=>$vi){
            foreach ($vi as $kvi=>$vvi){
                $PHPSheet->setCellValue($kvi.$ks,$vvi);
            }
            $ks++;
        }
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");//创建生成的格式
        header('Content-Disposition: attachment;filename="'.date('Y-m-d H-i-s').'.xlsx"');//下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }


    //用户管理导出 用户导出
    public function user_export(){

        $id = input('param.id');//活动id
        $name =urldecode( input('param.name'));//获取用户输入的是姓名还是电话
        $start =input('param.time_start');//开始时间
        $over = input('param.time_over');//结束时间
        $time_start = strtotime($start);
        $time_over = strtotime($over);

        //根据传递过来的参数 查找要导出的用户
        $user = SurveyusersModel::getexport($id,$name,$time_start,$time_over);
        foreach ($user as $k=>$v){
            //参与活动
            $activity = Db::name('questionnaire')->field('q_name')->where(['q_id'=>$v['s_qid']])->find();
            $user[$k]['activity'] = $activity['q_name'];

        }

        Loader::import('PHPExcel.PHPExcel');//手动引入PHPExcel.php
        Loader::import('PHPExcel.PHPExcel.IOFactory.PHPExcel_IOFactory');//引入IOFactory.php 文件里面的PHPExcel_IOFactory这个类
        $PHPExcel = new \PHPExcel();//实例化
//        $PHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth('');
        $PHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth('25px');
        $PHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth('25px');
        $PHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth('25px');
        $PHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth('25px');
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("demo"); //给当前活动sheet设置名称
        $PHPSheet
            ->setCellValue("A1","参与活动")
            ->setCellValue("B1","项目名称")
            ->setCellValue("C1","手术时间")
            ->setCellValue("D1","姓名")
            ->setCellValue("E1","手机")//表格数据
            ->setCellValue("F1","收货地址")//表格数据
            ->setCellValue("G1","提交时间");//表格数据
        $ks = 2;
        foreach ($user as $k=>$v){
            $PHPSheet
                ->setCellValue("A".$ks,$v['activity'])
                ->setCellValue("B".$ks,$this->searchType[$v['s_project']])
                ->setCellValue("C".$ks,date('Y-m-d',$v['s_time']))
                ->setCellValue("D".$ks,$v['s_name'])
                ->setCellValue("E".$ks,$v['s_phone'])
                ->setCellValue("F".$ks,$v['s_province'].' '.$v['s_city'].' '.$v['s_county'].' '.$v['s_receipt'])
                ->setCellValue("G".$ks,date('Y-m-d H:i:s',$v['s_addtime']));
            $ks++;
        }
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");//创建生成的格式
        header('Content-Disposition: attachment;filename="'.date('Y-m-d H-i-s').'.xlsx"');//下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }



    public function detail(){
        $id = input('param.q_id');//获取活动id
        //获取输入的姓名或电话与时间
        $name = input('param.name');
        $start =input('param.time_start');//开始时间

        $over = input('param.time_over');//结束时间
        $time_start = strtotime($start);
        $time_over = strtotime($over);
        $where = array();
        if(isset($name) && !empty($name)){
            if(is_numeric($name)){
                $where['s_phone'] = ['like','%'.$name.'%'];
            }else{
                $where['s_name'] = ['like','%'.$name.'%'];
            }
        }
        if(!empty($id)){
            $where['s_qid'] = $id;
        }

        if(!empty($time_start) && !empty($time_over)){
            $time_over = $time_over+86400;
            $where['s_addtime'] = array('between',$time_start.','.$time_over);
        }

        //模糊查询 根据姓名，电话查询,时间查询
        $user = Db::name('surveyusers')
            ->alias('a')
            ->join('questionnaire b','a.s_qid = b.q_id')
            ->join('hospital c','a.s_yard = c.id')
            ->where($where)->order('s_id desc')->paginate(15,false,['query' => request()->param()]);
//        echo Db::getLastSql();die;
//        foreach ($user as $k=>$v){
//            $user[$k]['user'] = Db::name('user_info')->where(['phonenum'=>$v['s_phone']])->find();
//        }

        $questionnaire = Db::name('questionnaire')->find($id);
        $this->assign('id',$id);
        $this->assign('questionnaire',$questionnaire);
        $this->assign('user',$user);
        $this->assign('name',$name);
        $this->assign('start',$start);
        $this->assign('over',$over);
        $this->assign('searchType',$this->searchType);
        return $this->fetch();

    }




}