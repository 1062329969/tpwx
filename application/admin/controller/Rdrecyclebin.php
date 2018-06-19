<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 商品订单管理控制器
 */
namespace app\admin\controller;

use app\admin\model\RecommenddoctorModel;
class Rdrecyclebin extends Admin
{

    public function _initialize()
    {
        parent::_initialize();
        $this->rdmodel = new RecommenddoctorModel();
    }

    public function index()
    {
        $list = $this->rdmodel->where(['rd_state'=>2])->order('rd_id desc')->paginate(20);
        $this->assign('list',$list);
        return $this->fetch();
    }
    public function alter(){
        $rd_id=intval(input('param.rd_id'));
        $RecommenddoctorModel = new RecommenddoctorModel();
        $val = $RecommenddoctorModel->where(['rd_id'=>$rd_id])->update(['rd_state' => 1]);
        if($val){
            $this->success('还原成功');
        }else{
            $this->error('还原成功');
        }
    }

}