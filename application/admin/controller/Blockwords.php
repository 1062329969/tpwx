<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * 跟踪反馈
 */

namespace app\admin\controller;

use app\admin\model\BlockwordsModel;
use think\Db;
use think\Session;

class Blockwords extends Admin
{

    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $StageModel = new BlockwordsModel();
        $list = $StageModel->order('b_id desc')->paginate(15);
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function add(){
        $vals=trim(input('get.vals'));
        if($vals){
            $StageModel = new BlockwordsModel();
            $va = $StageModel->allowField(true)->save(['b_content'=>$vals,'b_uid'=>Session::get('adminid'),'b_addtime'=>time(),'b_updatetime'=>time()]);
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
        $bid=intval(input('get.bid'));
        if($bid && $vals){
            $StageModel = new BlockwordsModel();
            $va = $StageModel->allowField(true)->save(['b_content'=>$vals,'b_updatetime'=>time()],['b_id'=>$bid]);
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
        $bid=intval(input('get.bid'));
        if($bid){
            $StageModel = new BlockwordsModel();
            $va = $StageModel->where(['b_id'=>$bid])->delete();
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