<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 邮费模板控制器
 */
namespace app\admin\controller;


use app\admin\model\MailAreaModel;
use app\admin\model\MailTempModel;
use think\Controller;

class MailTemp extends Admin
{
    protected $modelName='';
    protected $areaArr=array(
        "110000"=>"北京",
        "120000"=>"天津",
        "130000"=>"河北省",
        "140000"=>"山西省",
        "150000"=>"内蒙古自治区",
        "210000"=>"辽宁省",
        "220000"=>"吉林省",
        "230000"=>"黑龙江省",
        "310000"=>"上海",
        "320000"=>"江苏省",
        "330000"=>"浙江省",
        "340000"=>"安徽省",
        "350000"=>"福建省",
        "360000"=>"江西省",
        "370000"=>"山东省",
        "410000"=>"河南省",
        "420000"=>"湖北省",
        "430000"=>"湖南省",
        "440000"=>"广东省",
        "450000"=>"广西壮族自治区",
        "460000"=>"海南省",
        "500000"=>"重庆",
        "510000"=>"四川省",
        "520000"=>"贵州省",
        "530000"=>"云南省",
        "540000"=>"西藏自治区",
        "610000"=>"陕西省",
        "620000"=>"甘肃省",
        "630000"=>"青海省",
        "640000"=>"宁夏回族自治区",
        "650000"=>"新疆维吾尔自治区",
        "710000"=>"台湾省",
        "810000"=>"香港特别行政区",
        "820000"=>"澳门特别行政区",
        "990000"=>"海外"
    );

    public function _initialize()
    {
        parent::_initialize();
        $this->modelName=new MailTempModel;
    }

    public function index()
    {
        $content=MailTempModel::getAll();
        $this->assign('content',$content);

        return $this->fetch();
    }

    public function add()
    {
        $request=request();
        if($request->method()=='POST')
        {
            $data=input('post.');

            $result=$this->validate($data,'ValidateClass.CK22');
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

            $result=$this->validate($data,'ValidateClass.CK23');
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
            //读取该模板下的地区列表
            $mailArea=MailAreaModel::getAll($id);

            foreach($mailArea as $key=>$value)
            {

                unset($temArr);
                $area=explode(',',$value['area']);
                $temArr=array();
                foreach($area as $k=>$v)
                {
                    if(!empty($v))
                    {
                        $temArr[]=$this->areaArr[$v];
                    }
                }
                $mailArea[$key]['areaname']=implode(',',$temArr);
            }


            $this->assign('mailArea',$mailArea);
            $this->assign('id',$id);
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

    //添加地区
    public function add_area()
    {
        $id=input('param.id');

        $request=request();
        if($request->method()=='POST')
        {
            $data=input('post.');

            if(empty($data['area']))
            {
                $this->error('请选择地区');
            }

            $data['area']=implode(',',$data['area']);

            $mailAreaModel=new MailAreaModel();
            if($mailAreaModel->allowField(true)->save($data))
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

            //读取该模板下已用的地区列表
            $mailArea=MailAreaModel::getAll($id);
            $useArea=array();
            foreach($mailArea as $key=>$value)
            {
                $useArea=array_merge($useArea,explode(',',$value['area']));
            }

            $this->assign('id',$id);
            //读取可用的地区
            $areaArr=$this->areaArr;
            foreach($areaArr as $key=>$val)
            {
                 if(in_array($key,$useArea)) unset($areaArr[$key]);
            }

            $this->assign('areaArr',$areaArr);
            return $this->fetch();
        }

    }

    //删除地域
    public function del_area()
    {

        $id=input('param.id');
        $mailAreaModel=new MailAreaModel();
        if($mailAreaModel->save(['del'=>1],['id'=>$id]))
        {
            echo '{"code":"1"}';
        }
        else
        {
            echo '{"code":"0"}';
        }
    }

}