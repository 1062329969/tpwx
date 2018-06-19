<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 专家
 */

namespace app\index\controller;


use app\index\model\ContAdModel;
use app\index\model\DoctorFollowModel;
use app\index\model\DoctorModel;
use app\index\model\HospitalModel;
use app\index\model\HospitalRegionModel;
use app\index\model\ProjectModel;
use think\Db;

class Doctor extends Wap
{

    protected function _initialize()
    {
        //获得授权
        $this->getUserInfo();
        $this->assign('fansInfo',$this->fansInfo);
    }

    //曹姐姐医生团队 doctor 首页
    public function doctor_2_2_3(){
        $pid=input('param.pid',0);
        $this->assign('pid',$pid);
        $hid=input('param.hid',0);
        $this->assign('hid',$hid);

        //读取项目
        $project=ProjectModel::getAll();

        $this->assign('project',$project);

        //读取医院
        $hospital=HospitalModel::getAll();

        $this->assign('hospital',$hospital);


        $doctors=DoctorModel::getDocs($pid,$hid,10);
        $this->assign('doctors',$doctors);
        $this->assign('count',count($doctors));
        return $this->fetch();
    }


    //曹姐姐医生团队 doctor 详情页面
    public function detail(){

        $id=input('param.id');
        if(empty($id) || !is_numeric($id))
        {
            $this->error('参数错误');
        }

        $doctor=DoctorModel::getOne($id);
        $hospital = Db::name('hospital')->find($doctor['hid']);

        if(empty($doctor))
        {
            $this->error('参数错误');
        }

        $this->assign('doctor',$doctor);
        $this->assign('hospital',$hospital);
        //判断该用户是否关注了该医生
        $doctorFollow=new DoctorFollowModel;
        $isFollow=$doctorFollow->where(['docid'=>$doctor['id'],'uid'=>$this->fansInfo['id']])->find();
        if(!empty($isFollow))
        {
            $this->assign('isFollow',1);
        }
        else
        {
            $this->assign('isFollow',0);
        }
        $this->assign('doctor',$doctor);
        return $this->fetch();
    }


    //曹姐姐医生团队 doctor 三页面



    //专家首页
    public function index()
    {
        $pid=input('param.pid',0);
        $this->assign('pid',$pid);
        $hid=input('param.hid',0);
        $this->assign('hid',$hid);

        //读取项目
        $project=ProjectModel::getAll();

        $this->assign('project',$project);

        //读取医院
        $hospital=HospitalModel::getAll();

        $this->assign('hospital',$hospital);

        $doctors=DoctorModel::getDocs($pid,$hid);
        $this->assign('doctors',$doctors);
        $this->assign('count',count($doctors));
        return $this->fetch();
    }

    //上拉加载
    public function nextPage()
    {

        $offsize = input('param.offsize', 0);
        $pid = input('param.pid', 0);
        $hid = input('param.hid', 0);
        //读取案例内容
//        $doctor = DoctorModel::getDocs($pid, $hid,10,$offsize);
        $doctor =  Db::name('hospital')
            ->alias('a')
            ->join(' doctor b','a.id = b.hid')
            ->limit($offsize,10)
            ->select();
        if (empty($doctor)) return 0;

        $reStr = '';
        foreach ($doctor as $key => $vo) {
            $reStr= '<a href="'.url('detail',['id'=>$vo['id']]).'">
                <div class="doclist">
                    <div class="docimname">
                        <p class="cjtx mui-pull-left"><img src="'.$vo['pic'].'" /></p>
                        <p class="name"><span>'.$vo['docname'].'</span></p>
                        
                        
                        <ul class="cjtitle">
                            <li>'.$vo['position'].'</li>
                            <li>'.$vo['positional_titles'].'</li>
                        </ul>
                        <div class="cjhos">
                            <img src="'.HTML_STATIC.'/images/19.png"/>
                            <span>就职院部:</span>
                            <span>'.$vo['hosname'].'</span>
                        </div>
                    </div>
                    <img class="cjsj" src="'.HTML_STATIC.'/images/20.png"/>
                </div>
            </a>';
        }

        return $reStr;
    }

    //专家详细页
    public function detail_1()
    {
        $id=input('param.id');
        if(empty($id) || !is_numeric($id))
        {
            $this->error('参数错误');
        }

        $doctor=DoctorModel::getOne($id);

        if(empty($doctor))
        {
            $this->error('参数错误');
        }

        $this->assign('doctor',$doctor);
        //判断该用户是否关注了该医生
        $doctorFollow=new DoctorFollowModel;
        $isFollow=$doctorFollow->where(['docid'=>$doctor['id'],'uid'=>$this->fansInfo['id']])->find();
        if(!empty($isFollow))
        {
            $this->assign('isFollow',1);
        }
        else
        {
            $this->assign('isFollow',0);
        }
        $this->assign('doctor',$doctor);
        return $this->fetch();
    }

    //用户关注、取消关注医生
    public function follow()
    {
        $request=request();
        if($request->method()=='POST')
        {
            $is=input('param.is');
            $docid=input('param.docid');
            $uid=$this->fansInfo['id'];
            if($is==0)
            {

                $doctorFollow=new DoctorFollowModel;
                if($doctorFollow->save(['docid'=>$docid,'uid'=>$uid,'create_time'=>time()]))
                {
                    return ['code'=>1];
                }
            }
            else
            {
                $doctorFollow=new DoctorFollowModel;
                if($doctorFollow->where(['docid'=>$docid,'uid'=>$uid])->delete())
                {
                    return ['code'=>2];
                }
            }

        }
    }

}