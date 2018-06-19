<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * 病历管理
 */
namespace app\admin\controller;

use ApiOauth\ApiOauth;
use app\admin\model\UserInfoModel;
use app\admin\model\VisitBackModel;
use app\admin\model\VisitModel;
use app\index\event\TemplateMessage;
use think\Loader;
use think\Db;

class Visit extends Admin
{
    protected $modelName='';

    public function _initialize()
    {
        parent::_initialize();
        $this->modelName=new VisitModel;
    }

    public function index()
    {
        $searchValue=trim(input('param.searchValue',""));
        $this->assign('searchValue',$searchValue);


        $content=$this->modelName->getAll(15,$searchValue);
        $this->assign('content',$content);
        return $this->fetch();
    }

    //显示疗程跟踪
    public function show()
    {
        $vid=input('param.vid');
        $content=VisitBackModel::getAll(15,$vid);
        $this->assign('content',$content);
        $this->assign('vid',$vid);
        return $this->fetch();
    }
    //修改疗程跟踪
    public function show_alter()
    {
        $request=request();
        if($request->method()=='POST')
        {
            $data=input('post.');
            $visit_back=new VisitBackModel();
            if($visit_back->allowField(true)->save($data,['id'=>$data['id']]))
            {

                $this->success('修改成功',url('show',['vid'=>$data['vid']]));
            }
            else
            {
                $this->error('修改失败',url('show',['vid'=>$data['vid']]));
            }
        }
        else
        {
            $id=input('param.id');
            $vid=input('param.vid');
            $content=VisitBackModel::find($id);
            $this->assign('content',$content);
            $this->assign('vid',$vid);
            return $this->fetch();
        }
    }

    //添加疗程跟踪
    public function add()
    {
        $request=request();
        if($request->method()=='POST')
        {
            $data=input('post.');
            $visit_back=new VisitBackModel();
            if($visit_back->allowField(true)->save($data))
            {
                $visit=VisitModel::find($data['vid']);
                $userInfo=UserInfoModel::where(['phonenum'=>$visit['tel']])->find();

                if(!empty($userInfo))
                {
                    //发送模板消息
                    $templateMessage=new TemplateMessage();
                    $sData["wecha_id"]=$userInfo['wecha_id'];
                    $sData["tempid"]='Ip0YeKLP_AV3DRRwwBXkQVDGs25aSB956Rg0BLitVTg';
                    $sData['first']='术后跟踪';
                    $sData['keyword1']=$visit['name'];
                    $sData['keyword2']=$visit['op_time'];
                    $sData['remark']=$data['title'];
                    $sData["href"]= config('domain').'/track_new/detail/id/'.$visit_back->id."html";
                    $sData["topcolor"]='';
                    $apiOauth=new ApiOauth();
                    $access_token=$apiOauth->update_authorizer_access_token(['appid'=>config('Appid'),'appsecret'=>config('Appsecret')]);
                    $templateMessage->sendTempMsg(3,$sData,$access_token);
                }


                $this->success('添加成功',url('show',['vid'=>$data['vid']]));
            }
            else
            {
                $this->error('添加失败',url('show',['vid'=>$data['vid']]));
            }
        }
        else
        {
            $vid=input('param.vid');
            //计算术后第几天
            $title="";
            $content=VisitModel::get($vid);
            if(!empty($content['op_time']))
            {
                $title="术后第".round((time()-strtotime($content['op_time'])) / 86400)."天注意事项";
            }

            $this->assign('title',$title);
            $this->assign('vid',$vid);
            return $this->fetch();
        }
    }

    //删除疗程跟踪
    public function show_del()
    {
        $id=input('param.id');
        $visit_back=new VisitBackModel();
        if($visit_back->save(['del'=>1],['id'=>$id]))
        {
            echo '{"code":"1"}';
        }
        else
        {
            echo '{"code":"0"}';
        }
    }


    public function alter()
    {

        $request=request();
        if($request->method()=='POST')
        {
            $data=input('post.');
            $sort=$data['sort'];
            foreach ($data['id'] as $key=>$value)
            {
                $updateData['sort']=$sort[$key];
                $this->modelName->allowField(true)->save($updateData,['id'=>$value]);
            }

            $this->success('修改成功',url('index'));

        }
        else
        {
            $id=input('param.id');
            $visit=$this->modelName->get($id);
            $this->assign('visit',$visit);
            //查找该用户的所有病例
            $content=$this->modelName->where(['tel'=>$visit['tel'],'del'=>0])->select();

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
                    if(!$v[0]){
                        continue;
                    }
//                    \PHPExcel_Shared_Date::ExcelToPHP($value);对数据返回负数
//                    var_dump($v[5]);die;
                    $data[$k]['code'] = $v[0];
                    $data[$k]['name'] = $v[1];
                    $data[$k]['sex'] = $v[2];
                    $data[$k]['jz_type'] = $v[3];
                    $data[$k]['hospital'] = $v[4];
                    $data[$k]['cz_time'] = $this->edittime($v[5]);
                    $data[$k]['op_time']=$this->edittime($v[6]);
                    $data[$k]['doctor']=$v[7];
                    $data[$k]['f_e']=$v[8];
                    $data[$k]['f_l']=$v[9];
                    $data[$k]['z_e']=$v[10];
                    $data[$k]['z_l']=$v[11];
                    $data[$k]['yhzk']=$v[12];
                    $data[$k]['yhq']=$v[13];
                    $data[$k]['zssfy']=$v[14];
                    $data[$k]['ztf']=$v[15];
                    $data[$k]['zzssf']=$v[16];
                    $data[$k]['tel']=$v[17];
                    $data[$k]['zdy1']=$v[18];
                    $data[$k]['zdy2']=$v[19];
                    $data[$k]['zdy3']=$v[20];
                    $data[$k]['zdy4']=$v[21];
                    $data[$k]['zdy5']=$v[22];
                    $data[$k]['zdy6']=$v[23];
                    $data[$k]['zdy7']=$v[24];
                    $data[$k]['create_time']=time();

                }
                //如果不是第二次导入标识为病历记录  type=1
                foreach($data as $key=>$value)
                {
                    if(Db::name('visit')->where(['tel'=>$value['tel']])->find())
                    {
                        $data[$key]['type']=1;
                        //Db::name('visit')->where(['tel'=>$value['tel']])->update(['type'=>1]);
                    }
                    if(Db::name('user_info')->where(['phonenum'=>$value['tel']])->find()){
                        Db::name('user_info')->where(['phonenum'=>$value['tel']])->update(['stage_type'=>$value['op_time']]);
                    }
                }

                Db::name('visit')->insertAll($data);

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

    public function edittime($a){
        if(preg_match('/^(\d{1,2}-){2}(\d{2}){1,2}$/',$a)){
            $b = explode('-',$a);
            $c = '20'.$b[2].'-'.$b[0].'-'.$b[1];
            return strtotime($c);
        }elseif(preg_match('/^\d{4}(-\d{1,2}){2}$/',$a)){
            return strtotime($a);
        }elseif(preg_match('/^\d{2}(-\d{1,2}){2}$/',$a)){
            return strtotime('20'.$a);
        }elseif(preg_match('/^\d{4}(\/\d{1,2}){2}$/',$a)){
            return strtotime($a);
        }elseif( stripos($a,'"年"') && stripos($a,'"月"') && stripos($a,'"日"') ){
            $a = str_replace('"年"',"-",$a);
            $a = str_replace('"月"',"-",$a);
            $a = str_replace('"日"',"",$a);
            return strtotime($a);
        }
    }
}