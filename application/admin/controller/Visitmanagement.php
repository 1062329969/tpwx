<?php

namespace app\admin\controller;

use app\admin\model\VisitmanagementModel;
use think\Db;
use think\Session;
use app\admin\model\LogModel;
use think\Loader;

class Visitmanagement extends Admin
{
    protected $type = [
        1=>'头发种植',
        2=>'顶部加密',
        3=>'美人尖种植',
        4=>'发际线调整',
        5=>'眉毛种植',
        6=>'胡须种植',
        7=>'鬓角种植',
        8=>'体毛种植',
        9=>'疤痕种植'
    ];

    public function _initialize(){
//        parent::_initialize();
    }

    private function getwhere($data){
        $where = array();
        $arr = ['vm_name','vm_hospital','vm_phone','vm_project'];
        foreach ($arr as $k=>$v){
            if(isset($data[$v]) && !empty(trim($data[$v]))){
                if($v == 'vm_name'){
                    $where[$v] = ['like','%'.$data[$v].'%'];
                }else{
                    $where[$v] = $data[$v];
                }
            }
        }
        if(!empty($data['vm_visittimes']) && !empty($data['vm_visittimee'])){
            $where['vm_visittime'] = [
                ['>=',strtotime($data['vm_visittimes'])],
                ['<=',strtotime($data['vm_visittimee'])],
            ];
        }
        if(!empty($data['vm_operationtimes']) && !empty($data['vm_operationtimee'])){
            $where['vm_operationtime'] = [
                ['>=',strtotime($data['vm_operationtimes'])],
                ['<=',strtotime($data['vm_operationtimee'])],
            ];
        }
        return $where;
    }

    public function index(){
        $data = input('param.');
        $name = input('param.vm_name');//获取姓名
        $phone = input('param.vm_phone');//获取手机号
        $tstart = input('param.vm_visittimes');//开始到诊时间
        $tover = input('param.vm_visittimee');//结束到诊时间
        $pstart = input('param.vm_operationtimes');//开始手术时间
        $pover= input('param.vm_operationtimee');//结束手术时间
        $hospital = input('param.vm_hospital');//获取院部
        $project = input('param.vm_project');//获取项目

        $where = $this->getwhere($data);
        $list = Db::name('visitmanagement')->where($where)->order('vm_id desc')->paginate(15,false,['query' => request()->param()]);
        $this->assign('list',$list);
        $this->assign('ptype',$this->type);
        $hlist = Db::name('hospital')->where(['status'=>1,'del'=>0])->order('id desc')->select();
        $hlist = array_column($hlist,'hosname','id');
        $this->assign('hlist',$hlist);
        $this->assign('data',$data);


        $this->assign('name',$name);
        $this->assign('phone',$phone);
        $this->assign('tstart',$tstart);
        $this->assign('tover',$tover);
        $this->assign('pstart',$pstart);
        $this->assign('pover',$pover);
        $this->assign('hospital',$hospital);
        $this->assign('project',$project);

        return $this->fetch();
    }
    function del(){
        $data = input('param.');
        $vm_id = $data['vm_id'];
//        foreach ($vm_id as $k=>$v){
//            $vm_id[$k] = intval($v);
//        }

        if(intval($vm_id)){
            $res = Db::name('visitmanagement')->where('vm_id','in',$vm_id)->delete();
            if($res){
                LogModel::savelog(3,4,input('param.'),'删除诊断档案成功');
                $this->success('删除成功');
            }else{
                LogModel::savelog(3,4,input('param.'),'删除诊断档案失败');
                $this->error('删除失败');
            }
        }else{
            $this->error('数据错误');
        }
    }
    public function add(){
        $request=request();
        if($request->method()=='POST') {
            $data = input('param.');

//            $result = $this->validate($data,$this->_rule());
//            if(true !== $result){
//                $this->error('数据错误');
//            }
            $data['vm_visittime'] = strtotime($data['vm_visittime']);
            $data['vm_bir'] = strtotime($data['vm_bir']);
            $data['vm_operationtime'] = strtotime($data['vm_operationtime']);
            $data['vm_uid'] = Session::get('adminid');
            $data['vm_addtime'] = time();
            $res = Db::name('visitmanagement')->insert($data);
            if($res){
                LogModel::savelog(3,2,input('param.'),'修改诊断档案成功');
                $this->success('添加成功',url('index'));
            }else{
                LogModel::savelog(3,2,input('param.'),'修改诊断档案失败');
                $this->error('添加失败',url('index'));
            }
        }else{
            $hlist = Db::name('hospital')->where(['status'=>1,'del'=>0])->order('id desc')->select();
            $this->assign('hlist',$hlist);
            $this->assign('ptype',$this->type);
            return $this->fetch();
        }
    }

    private function _rule(){
        return [
            'vm_name'=>'require',
            'vm_bir'=>'require|date',
            'vm_sex'=>'require|number',
            'vm_phone'=>'require|number|length:11|/^1[3-8]{1}[0-9]{9}$/',
            'vm_visittime'=>'require|date',
            'vm_admissiondoctor'=>'require',
            'vm_project'=>'require|number',
            'vm_hospital'=>'require|number',
            'vm_diagnosisnote'=>'require',
            'vm_operationtime'=>'require|date',
            'vm_hairfollicle'=>'require|number',
            'vm_surgerydoctor'=>'require',
            'vm_money'=>'require',
            'vm_note'=>'require'
        ];
    }


    /*
                    _ooOoo_
                   o8888888o
                   88" . "88
                   (| -_- |)
                   O\  =  /O
                ____/`---'\____
              .'  \\|     |//  `.
             /  \\|||  :  |||//  \
            /  _||||| -:- |||||-  \
            |   | \\\  -  /// |   |
            | \_|  ''\---/''  |   |
            \  .-\__  `-`  ___/-. /
          ___`. .'  /--.--\  `. . __
       ."" '<  `.___\_<|>_/___.'  >'"".
      | | :  `- \`.;`\ _ /`;.`/ - ` : | |
      \  \ `-.   \_ __\ /__ _/   .-` /  /
 ======`-.____`-.___\_____/___.-`____.-'======
                    `=---='
 ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
          佛祖保佑       永无BUG
 */


    public function  alter(){
        $id = input("param.vm_id");
        $request=request();
        if($request->method()=='POST') {
            $data = input('param.');
            $res = new VisitmanagementModel();
            $save = $res->allowField(true)->save($data,['vm_id'=>$id]);
            if (!$save) {
                $this->error('修改失败', url('index'));
            }else{
                $this->success('修改成功',url('index'));
            }
        }else{
            $list = Db::name('visitmanagement')->find($id);
            $hlist = Db::name('hospital')->where(['status'=>1,'del'=>0])->order('id desc')->select();
            $this->assign('list',$list);
            $this->assign('id',$id);
            $this->assign('hlist',$hlist);
            $this->assign('ptype',$this->type);
            return $this->fetch();
        }
    }


    //详情页面
    public function views(){
        $id = input("param.vm_id");
        $info = Db::name('visitmanagement')->find($id);
        //查询医院
        $hospital = Db::name('hospital')->field('hosname')->find($info['vm_hospital']);

        $this->assign('info',$info);
        $this->assign('hospital',$hospital);
        $this->assign('ptype',$this->type);
        return $this->fetch();

    }

    //就诊导出
    public function visit_export(){

        $data = input('param.');
        //根据传递过来的参数 查找要导出的用户
        $user = Db::name('visitmanagement')->where($this->getwhere($data))->order('vm_id desc')->select();
        if(empty($user)){
            $this->error('所属案例为空',url('index'));
        }
        $hospital = Db::name('hospital')->field('id,hosname')->where(['status'=>1,'del'=>0])->select();
        $hospital_arr = array_column($hospital, 'hosname', 'id');
//        echo "<pre>";
//
//        var_dump($user);die;
        Loader::import('PHPExcel.PHPExcel');//手动引入PHPExcel.php
        Loader::import('PHPExcel.PHPExcel.IOFactory.PHPExcel_IOFactory');//引入IOFactory.php 文件里面的PHPExcel_IOFactory这个类
        $PHPExcel = new \PHPExcel();//实例化
//        $PHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth('');
        $PHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth('25px');
        $PHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth('25px');
        $PHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth('25px');
        $PHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth('25px');
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("demo"); //给当前活动sheet设置名称
        $PHPSheet
            ->setCellValue("A1","患者姓名")
            ->setCellValue("B1","出生日期")
            ->setCellValue("C1","性别")
            ->setCellValue("D1","手机号")
            ->setCellValue("E1","到诊时间")//表格数据
            ->setCellValue("F1","接诊医生")//表格数据
            ->setCellValue("G1","项目类别")//表格数据
            ->setCellValue("H1","医院")//表格数据
            ->setCellValue("I1","诊断描述")//表格数据
            ->setCellValue("J1","手术时间")//表格数据
            ->setCellValue("K1","毛囊单位")//表格数据
            ->setCellValue("L1","手术医生")//表格数据
            ->setCellValue("M1","手术费用")//表格数据
            ->setCellValue("N1","备注");//表格数据
        $ks = 2;

        foreach ($user as $k=>$v){
            if($v['vm_sex'] ==1 ){
                $v['vm_sex'] = '男';
            }else{
                $v['vm_sex'] = '女';
            }
            $PHPSheet
                ->setCellValue("A".$ks,$v['vm_name'])
//                ->setCellValue("B".$ks,$this->searchType[$v['s_project']])
                ->setCellValue("B".$ks,date('Y-m-d',$v['vm_bir']))
                ->setCellValue("C".$ks,$v['vm_sex'])
                ->setCellValue("D".$ks,$v['vm_phone'])
                ->setCellValue("E".$ks,date('Y-m-d',$v['vm_visittime']))
                ->setCellValue("F".$ks,$v['vm_admissiondoctor'])
                ->setCellValue("G".$ks,$this->type[$v['vm_project']])
                ->setCellValue("H".$ks,$hospital_arr[$v['vm_hospital']])
                ->setCellValue("I".$ks,$v['vm_diagnosisnote'])
                ->setCellValue("J".$ks,date('Y-m-d',$v['vm_operationtime']))
                ->setCellValue("K".$ks,$v['vm_hairfollicle'])
                ->setCellValue("L".$ks,$v['vm_surgerydoctor'])
                ->setCellValue("M".$ks,$v['vm_money'])
                ->setCellValue("N".$ks,$v['vm_note']);
            $ks++;
        }
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");//创建生成的格式
        header('Content-Disposition: attachment;filename="'.date('Y-m-d H-i-s').'.xlsx"');//下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

}