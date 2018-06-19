<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 内部员工管理控制器
 */

namespace app\admin\controller;

use app\admin\model\StaffModel;
use think\Loader;
use think\Db;

class Staff extends Admin
{
    protected $modelName='';

    public function _initialize()
    {
        parent::_initialize();
        $this->modelName=new StaffModel;
    }

    public function index()
    {
        $content=$this->modelName->getAll(15);
        $this->assign('content',$content);
        return $this->fetch();
    }

    public function add()
    {
        $request=request();
        if($request->method()=='POST')
        {
            $data=input('post.');

            $result=$this->validate($data,'ValidateClass.CK24');
            if(true !== $result)
            {
                // 验证失败 输出错误信息
                $this->error($result);
            }

            if($this->modelName->allowField(true)->save($data))
            {
                $this->success('添加成功',url('index'));
            }
            else
            {
                $this->error('添加失败',url('index'));
            }
        }
        else
        {
            return $this->fetch();
        }
    }

    public function alter()
    {

        $request=request();
        if($request->method()=='POST')
        {
            $data=input('post.');

            $result=$this->validate($data,'ValidateClass.CK25');
            if(true !== $result)
            {
                // 验证失败 输出错误信息
                $this->error($result);
            }


            if($this->modelName->allowField(true)->save($data,['id'=>$data['id']]))
            {

                $this->success('修改成功',url('index'));
            }
            else
            {
                $this->error('修改失败',url('index'));
            }
        }
        else
        {
            $id=input('param.id');
            $content=$this->modelName->get($id);
            $this->assign('content',$content);
            return $this->fetch();
        }

    }


    public function del()
    {

        $id=input('param.id');
        if($this->modelName->save(['del'=>1],['id'=>$id]))
        {
            echo '{"code":"1"}';
        }
        else
        {
            echo '{"code":"0"}';
        }
    }

    //从excel表格导入
    public function insert()
    {

        $request=request();
        if($request->method()=='POST') {

            Loader::import('PHPExcel.PHPExcel');
            Loader::import('PHPExcel.PHPExcel.IOFactory.PHPExcel_IOFactory');
            Loader::import('PHPExcel.PHPExcel.Reader.Excel5');

            //获取表单上传文件
            $file = request()->file('excel');
            if($file==null){
                $this->error('请正确上传文件',url('visit/insert'));
            }
            $info = $file->validate(['ext' => 'xlsx'])->move(ROOT_PATH . 'public' . DS . 'uploads');
            if ($info)
            {
                //echo $info->getFilename();
                $exclePath = $info->getSaveName();  //获取文件名
                $file_name = ROOT_PATH . 'public' . DS . 'uploads' . DS . $exclePath;   //上传文件的地址
                $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
                $obj_PHPExcel = $objReader->load($file_name, $encode = 'utf-8');  //加载文件内容,编码utf-8
                $excel_array = $obj_PHPExcel->getsheet(0)->toArray();   //转换为数组格式
                array_shift($excel_array);  //删除第一个数组(标题);
                $data = [];
                foreach ($excel_array as $k => $v)
                {
	                $data[$k]['department'] = $v[0]?$v[0]:'';
	                $data[$k]['position'] = $v[1]?$v[1]:'';
                	$data[$k]['sname'] = $v[2]?$v[2]:'';
                    $data[$k]['stel'] = $v[3]?$v[3]:'';
	                $data[$k]['create_time']=time();
	                $data[$k]['update_time']=time();
                }

                Db::name('staff')->insertAll($data);
                $this->success('导入成功',url('index'));
            }
            else
            {
                $this->error($file->getError());
            }
        }
        else
        {
            return $this->fetch();
        }
    }

}