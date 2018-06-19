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
use app\admin\model\RecommenddoctorModel;
use app\admin\model\UserInfoModel;
use think\Loader;
use think\Session;
class Recommenddoctor extends Admin
{

    protected $rdmodel;

	public function _initialize()
	{
		parent::_initialize();
        $this->rdmodel = new RecommenddoctorModel();
	}

    public function index()
    {
        $list = $this->rdmodel->where(['rd_state'=>1])->order('rd_id desc')->paginate(20);
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function add()
    {
        $request=request();
        if($request->method()=='POST'){
            $data=input('param.');
            if($data && !empty($data['rd_name'])){
                $va = $this->rdmodel->allowField(true)->save(
                    [
                        'rd_name'=>$data['rd_name'],
                        'rd_phone'=>$data['rd_phone'],
                        'rd_hospital'=>$data['rd_hospital'],
                        'rd_uids'=>Session::get('adminid'),
                        'rd_addtime'=>time(),'rd_state'=>1
                    ]
                );
                if($va){
                    $id = $this->rdmodel->getLastInsID();
                    $a = file_get_contents('http://qr.liantu.com/api.php?text=http://ihair.yongxianghui.net/member/reg/rd_id/'.$id.'.html');
                    $filename = time().'.png';
                    $path = ROOT_PATH . 'public' . DS . 'uploads/rdoctor/'.$filename;
                    file_put_contents($path,$a);
                    $this->rdmodel->where(['rd_id'=>$id])->update(['rd_img'=>'/uploads/rdoctor/'.$filename]);
                    $this->success('添加成功',url('index'));
                }else{
                    $this->error('添加失败',url('index'));
                }
            }else{
                $this->error('添加失败',url('index'));
            }
        }else{
            return $this->fetch();
        }
    }

    public function alter(){
        $request=request();
        $rd_id=intval(input('param.rd_id'));
        if($request->method()=='POST') {
            $data = input('param.');
            $va = $this->rdmodel->allowField(true)->where(['rd_id'=>$rd_id])->update(
                [
                    'rd_hospital' => $data['rd_hospital'],
                    'rd_phone'=>$data['rd_phone'],
                    'rd_name' => $data['rd_name']
                ]
            );
            if ($va) {
                $this->success('修改成功',url('index'));
            } else {
                $this->error('修改失败');
            }
        }else{
            $info = $this->rdmodel->find($rd_id);
            $this->assign('rd_id',$rd_id);
            $this->assign('info',$info);
            return $this->fetch();
        }
    }

    public function del(){
        $rd_id=intval(input('param.rd_id'));
        if($rd_id){
            $va = $this->rdmodel->where(['rd_id'=>$rd_id])->update(['rd_state'=>2]);
            if($va){
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }else{
            $this->error('数据错误');
        }
    }

    public function exportexcel(){
        $rd_id=intval(input('param.rd_id'));
        $dinfo = $this->rdmodel->find($rd_id);
        $user = new UserInfoModel();
        $data = input('param.');
        if(!isset($data['starttime'])){ $data['starttime']=''; }
        if(!isset($data['endtime'])){ $data['starttime']=time(); }
        if(!empty($data['starttime'])&&!empty($data['endtime'])){
            $this->assign('starttime',$data['starttime']);
            $this->assign('endtime',$data['endtime']);
            $list = $user->where(['fordoctor'=>$rd_id])->where('regtime','between',[strtotime($data['starttime']),strtotime($data['endtime'])])->order('id desc')->paginate(20);
        }else{
            $list = $user->where(['fordoctor'=>$rd_id])->order('id desc')->paginate(20);
        }
        $path = dirname(__FILE__); //找到当前脚本所在路径

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
            ->setCellValue("A1","ID")
            ->setCellValue("B1","微信名字")
            ->setCellValue("C1","手机号")
            ->setCellValue("D1","性别")
            ->setCellValue("E1","注册时间");//表格数据
        $ks = 2;
        foreach ($list as $k=>$v){
            $PHPSheet
                ->setCellValue("A".$ks,$v['id'])
                ->setCellValue("B".$ks,$v['wechaname'])
                ->setCellValue("C".$ks,$v['phonenum'])
                ->setCellValue("D".$ks,$v['sex'])
                ->setCellValue("E".$ks,date('Y-m-d H:i:s',$v['regtime']));
            $ks++;
        }
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");//创建生成的格式
        header('Content-Disposition: attachment;filename="'.$dinfo['rd_name'].'_'.date('YmdHis').'.xlsx"');//下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

    public function rlist(){
        $rd_id=intval(input('param.rd_id'));
        if(!$rd_id){
            $this->error('数据错误');
        }
        $user = new UserInfoModel();
        $dinfo = $this->rdmodel->find($rd_id);
        $data = input('param.');
        if(!isset($data['starttime'])){ $data['starttime']=''; }
        if(!isset($data['endtime'])){ $data['starttime']=time(); }

        if(!empty($data['starttime'])&&!empty($data['endtime'])){
            $this->assign('starttime',$data['starttime']);
            $this->assign('endtime',$data['endtime']);
            $list = $user->where(['fordoctor'=>$rd_id])->where('regtime','between',[strtotime($data['starttime']),strtotime($data['endtime'])])->order('id desc')->paginate(20);
        }else{
            $this->assign('starttime','');
            $this->assign('endtime','');
            $list = $user->where(['fordoctor'=>$rd_id])->order('id desc')->paginate(20);
        }
        $this->assign('dinfo',$dinfo);
        $this->assign('list',$list);
        $this->assign('rd_id',$rd_id);
        return $this->fetch();
    }

}