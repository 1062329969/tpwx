<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * 跟踪反馈
 */

namespace app\admin\controller;

use app\admin\model\FeedbacktypeModel;
use think\Db;
use think\Session;

class Feedbacktype extends Admin
{

    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $typelist = Db::name('feedbacktype')->where(['ft_state'=>1])->order('ft_id desc')->select();
        $this->assign('typelist',$typelist);
        return $this->fetch();
    }

    public function add(){
        $vals=trim(input('get.vals'));
        if($vals){
            $Feedbacktype = new FeedbacktypeModel();
            $va = $Feedbacktype->allowField(true)->save(['ft_type'=>$vals,'ft_state'=>1]);
            if($va){
                $this->success('success');
            }else{
                $this->error('error');
            }
        }else{
            $this->error('error');
        }
    }

    public function alter(){
        $vals=trim(input('get.vals'));
        $fid=intval(input('get.fid'));
        if($fid && $vals){
            $Feedbacktype = new FeedbacktypeModel();
            $va = $Feedbacktype->allowField(true)->save(['ft_type'=>$vals],['ft_id'=>$fid]);
            if($va){
                $this->success('success');
            }else{
                $this->error('error');
            }
        }else{
            $this->error('error');
        }
    }

    public function del(){
        $fid=intval(input('get.fid'));
        if($fid){
            $tcount  = Db::name('feedback')->where(['f_type'=>$fid])->count();
            if($tcount){
                $this->error('此类型下有'.$tcount.'条反馈，无法删除');
            }
            $Feedbacktype = new FeedbacktypeModel();
            $va = $Feedbacktype->where(['ft_id'=>$fid])->update(['ft_state'=>2]);
            if($va){
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }else{
            $this->error('删除失败');
        }
    }
}