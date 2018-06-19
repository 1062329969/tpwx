<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * 跟踪反馈
 */

namespace app\admin\controller;

use app\admin\model\BlockwordsModel;
use app\admin\model\FeedbackModel;
use think\Db;
use think\Session;

class Feedback extends Admin
{

    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {

        $list = Db::name('feedback')->paginate(15);
        $typelist = Db::name('feedbacktype')->where(['ft_state'=>1])->select();
        $typeinfo = array_column($typelist,'ft_type','ft_id');
        $this->assign('list',$list);
        $this->assign('typelist',$typeinfo);
        return $this->fetch();
    }

    public function alter(){
        $vals=trim(input('get.vals'));
        $id=trim(input('get.id'));
        if($vals && $id){
            $FeedbackModel = new FeedbackModel();
            $va = $FeedbackModel->allowField(true)->save(['f_back'=>$vals,'f_backuid'=>Session::get('adminid'),'f_backtime'=>time()],['f_id'=>$id]);
            if($va){
                $this->success('success');
            }else{
                $this->error('error');
            }
        }else{
            $this->error('error');
        }
    }

}