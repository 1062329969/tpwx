<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 商品订单管理控制器
 */
namespace app\admin\controller;

use ApiOauth\ApiOauth;
use app\admin\model\ExerciseModel;
use app\index\model\DetectionphotoModel;
use app\index\model\InformationModel;
use think\Db;
use think\Session;
class Detectionphoto extends Admin
{

    protected $DetectionphotoModel;

	public function _initialize()
	{
		parent::_initialize();
        $this->DetectionphotoModel = new DetectionphotoModel();
	}

	public function index()
	{

        $Detection =
            Db::name('detectionphoto dp')
                ->join('user_info ui','ui.id=dp.dp_uid')
                ->field('dp_id,dp_addtime,dp_state,wechaname')
                ->order('dp.dp_addtime desc')
                ->paginate();
//        print_r($Detection);
        $this->assign('detectionlist',$Detection);
		return $this->fetch();
	}

    public function dpinfo(){
        $id=input('param.dp_id');
        $info = Db::name('detectionphoto dp')
            ->join('user_info ui','ui.id=dp.dp_uid')
            ->field('dp.*,wechaname')
            ->find($id);
        $this->assign('info',$info);
        return $this->fetch();
    }

    public function alter(){
        $id=input('param.dp_id');
        $dp_rcontent=input('param.dp_rcontent');
        $data = array(
            'dp_ruid'=>Session::get('adminid'),
            'dp_rcontent'=>$dp_rcontent,
            'dp_rtime'=>time(),
            'dp_state'=>1
        );
        if($this->DetectionphotoModel->where(['dp_id'=>$id])->update($data)){
            $dinfo = $this->DetectionphotoModel->find($id);
            $information = new InformationModel();
            $information->save(
                [
                    'i_uid'=>$dinfo['dp_uid'],
                    'i_type'=>3,
                    'i_title'=>'拍照检测批复',
                    'i_note'=>$dp_rcontent,
                    'i_state'=>1,
                    'i_addtime'=>time()
                ]
            );
            $this->success('批复成功');
        }else{
            $this->error('批复失败');
        }
    }
}