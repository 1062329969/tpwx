<?php

namespace app\admin\model;


use think\Db;
use think\Model;
use think\Log;
class QuestionnaireModel extends Model
{
	protected $name='questionnaire';
	protected $pk='q_id';

    public function add($datas,$topic){
        Db::startTrans();
        $val = Db::name('questionnaire')->insert($datas);
        if($val){
            $qt_qid = Db::getLastInsID();
            foreach ($topic as $k=>$v){
                $topic[$k]['qt_qid'] = $qt_qid;
            }
            foreach ($topic as $kt=>$vt){
                $qt_options = $vt['qt_options'];
                unset($vt['qt_options']);
                $res = Db::name('questionnairetopic')->insert($vt);
                if($res){
                    $resid = Db::getLastInsID();
                    foreach ($qt_options as $k=>$v){
                        $v['qtid'] = $resid;
                        $ress = Db::name('questionnaireoption')->insert($v);
                        if(!$ress){
                            Db::rollback();
                            return false;
                        }
                    }
                }else{
                    Db::rollback();
                    return false;
                }
            }
            Db::commit();
            return true;
        }else{
            Db::rollback();
            return false;
        }
    }

}