<?php

namespace app\index\controller;

use app\index\model\AnswerModel;
use app\index\model\AnswerModel_tow;

class Answer extends Wap
{
    protected function _initialize()
    {
        //获得授权
        $this->getUserInfo();
        $this->assign('fansInfo', $this->fansInfo);
    }
    public function index(){
       // $res=input('param.id',0);
        $res=AnswerModel_tow::getAll();
        //echo AnswerModel::getLastSql();die;
        $answer = explode('###', $res[0]['answer']);
        $arr = array(
             'question' => $res[0]['id'] . ',' . $res[0]['question'],//题目
              'answer'=>$answer,
         );
        $json = json_encode($arr);
        //var_dump($json);die;
        $this->assign('json',$json);
        return $this->fetch();
    }


    public function detail(){
        $data = $_REQUEST['an'];

        $answers = explode('|',$data);
        $an_len = count($answers)-1; //题目数

        $query = AnswerModel_tow::detail();

        $i = 0;
        $score = 0; //初始得分
        $q_right = 0; //答对的题数
        if($answers[$i]==$row['correct']){
            $arr['res'][] = 1;
            $q_right += 1;
        }else{
            $arr['res'][] = 0;
        }
        $i++;
        $arr['score'] = round(($q_right/$an_len)*100); //总得分
        echo json_encode($arr);
    }
}



?>