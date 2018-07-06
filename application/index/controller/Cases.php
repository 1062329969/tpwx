<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 案例
 */

namespace app\index\controller;

use app\index\model\CasesModel;
use app\index\model\CasesRecordModel;
use app\index\model\CasesImgsModel;
use app\index\model\CasesRecordImgsModel;
use app\index\model\DoctorModel;
use app\index\model\HospitalModel;
use app\index\model\ProjectModel;
use app\index\model\CommentModel;
use app\index\model\ContAdModel;
use app\index\model\GradeModel;
use app\index\model\VideoModel;
use think\Db;

class Cases extends Wap
{

	protected function _initialize()
	{
		//获得授权
		$this->getUserInfo();
		$this->assign('fansInfo',$this->fansInfo);
	}

    //植发日记首页
    public function index(){

        //服务项目
        $pid=input('param.pid',0);
        $tid=input('param.tid',0);
        //地区
        $hid=input('param.hid',0);
        //读取院部
        $gid = input('param.gid',0);
        $hosname = Db::name('hospital')->where(['id'=>$gid])->find();
        $grade = HospitalModel::getAll();

        //读取项目
        $Hair_project = Db::name('project')->where(['id'=>$pid])->find();
        $project=ProjectModel::getAll();


        //读取案例内容
        $cases=CasesModel::getAll_tow($pid,$hid,$gid,$tid,10);
        foreach($cases as $key=>$v)
        {
            //读取该案例的最后一次回复情况
            $casesRecordModel=CasesRecordModel::getOne($v['id']);
            $cases[$key]['caser'] = $casesRecordModel;
            $cases[$key]['clicks'] = Db::name('cases_record')->where(['cid'=>$v['id']])->sum('click');
            $cases[$key]['clickss'] = Db::name('cases_record')->where(['cid'=>$v['id']])->sum('clicks');
            //用户是否点赞过
            $cases[$key]['res'] = Db::name('information')->where(['i_uid'=>$v['id'],'i_uids'=>$this->fansInfo['id'],'i_module'=>1,'i_type'=> 2])->find();

        }
        $this->assign('pid',$pid);
        $this->assign('tid',$tid);
        $this->assign('hid',$hid);
        $this->assign('hosname',$hosname);
        $this->assign('gid',$gid);
        $this->assign('project',$project);
        $this->assign('Hair_project',$Hair_project);
        $this->assign('grade',$grade);
        $this->assign('cases',$cases);
        $this->assign('count',count($cases));
        return $this->fetch();

    }

    //植发日记 详情页
    public function detail(){
    	$id=input('param.uid');
		$ttime=input('param.ttime',0);
		$this->assign('ttime',$ttime);
		$cases=CasesModel::getOne($id);
		$cases_hospital = Db::name('cases')
                ->alias('a')
                ->where(['a.id'=> $id])
                ->join('hospital b','a.hosname_id = b.id')
                ->find();

        $this->assign('cases_hospital',$cases_hospital);
//		echo CasesModel::getLastSql();die;
		//var_dump($cases);die;
		if(empty($cases))
		{
			$this->error('该案例不存在');
		}
		$this->assign('cases',$cases);
		//浏览数增1
		CasesModel::where('id',$id)->setInc('click',1);


        //读取术前的照片
		$casesImgs=CasesImgsModel::getAll($id);
		$this->assign('casesImgs',$casesImgs);
		//读取日记记录
		$casesRecord=CasesRecordModel::getAll($id,$ttime);
        foreach($casesRecord as $key=>$v)
		{
            //读取该记录的图片
			$casesRecordImgs=CasesRecordImgsModel::getAll($v['id']);
			$casesRecord[$key]['images']=$casesRecordImgs;
            $casesRecord[$key]['caser']= Db::name('information')->where(['i_ids'=>$v['id'],'i_uids'=>$this->fansInfo['id'],'i_module'=>1,'i_type'=> 2])->find();
		}
        $this->assign('cases_sum',count($casesRecord));
		$this->assign('casesRecord',$casesRecord);


        //同分院其他日记
//        $aboutCases = Db::name('cases')->where(['hosname_id'=>$cases['hosname_id'],'status'=>1,'del'=>0])->limit("0,3")->select();
        $aboutId =$cases['hosname_id'];
		$aboutCases=CasesModel::other($aboutId,$id,0,3);
//		echo CasesModel::getLastSql();die;
		foreach($aboutCases as $key=>$v)
		{
                //读取该案例的最后一次回复情况
                $casesRecordModel=CasesRecordModel::getOne($v['id']);
                $aboutCases[$key]['pic2']=$casesRecordModel['pic'];

		}
		$this->assign('aboutCases',$aboutCases);
		$this->assign('aboutId',$aboutId);




		//读取案例内容广告
		$cont_ad=ContAdModel::getOne(4);
		$this->assign('cont_ad',$cont_ad);

        //详情页医生
        $casess = CasesModel::find($id);
        $doctor_id = $casess['doctor'];
        $doctor = DoctorModel::find($doctor_id);
        //所属院部
        $hospital = Db::name('hospital')->where(['id'=>$casess['hosname_id']])->find();
        $this->assign('hospital',$hospital);
        $this->assign('doctor',$doctor);
	    return $this->fetch();
	}

    //植发日记 术后记录
    public function record(){
        $id=input('param.id');//帖子id
        $this->assign('id',$id);

        $casesRecord=CasesRecordModel::getOneId($id);
        $cid=$casesRecord['cid'];//术前帖id
        $cases=CasesModel::getOne($cid);//术前帖
        $cases_hospital = Db::name('cases')
            ->alias('a')
            ->where(['a.id'=> $cid])
            ->join('hospital b','a.hosname_id = b.id')
            ->find();
        $this->assign('cases_hospital',$cases_hospital);
        $cases_doctor = $cases['doctor'];
        $doctor_name = DoctorModel::find($cases_doctor);
        $this->assign('doctor_name',$doctor_name);

        if(empty($cases))
        {
            $this->error('该案例不存在');
        }
        $this->assign('cases',$cases);

        //浏览数增1
        CasesRecordModel::where('id',$id)->setInc('click',1);
        //读取该记录的图片
        if(empty($casesRecord['vurl']))
        {
            $casesRecordImgs=CasesRecordImgsModel::getAll($id);
            $casesRecord['images']=$casesRecordImgs;
        }
        $this->assign('casesRecord',$casesRecord);

        //获取评论内容
        $comment3 = Db::name('comment')
            ->where(['type'=>6,'aid'=>$id,'del'=>0,'status'=>0,'plid'=>0])
            ->order('id desc')
            ->select();
        foreach($comment3 as $key=>$v) {
            //查看评论是否点赞
            $comment3[$key]['info'] = $praise_s = Db::name('information')->where(['i_ids' => $v['id'], 'i_uids' => $this->fansInfo['id'], 'i_module' => 1, 'i_type' => 2])->find();
         }
        $comment = Db::name('comment')->where(['type'=>6,'aid'=>$id,'del'=>0,'status'=>0,'plid'=>0])->find();
        $this->assign('comment',$comment);
        $comment2=CommentModel::getAll2(6,$id);
        $this->assign('comment2',$comment2);
        $this->assign('comment3',$comment3);
        $this->assign('count3',count($comment3));
        //读取案例内容广告
        $cont_ad=ContAdModel::getOne(4);
        $this->assign('cont_ad',$cont_ad);

        //详情页医生
        $ids = input('param.uid');
        $doctor = DoctorModel::find($ids);
        $this->assign('doctor',$doctor);

        //读取术前的照片
        $casesImgs=CasesImgsModel::getAll($id);
        $this->assign('casesImgs',$casesImgs);

        //收藏 - 点赞
        $sc = Db::name('collection')->where(['aid'=>$id,'uid'=>$this->fansInfo['id']])->find();
        $praise = Db::name('information')->where(['i_ids'=>$id,'i_uids'=>$this->fansInfo['id'],'i_module'=>1,'i_type'=>2])->find();
        $this->assign('sc',$sc);
        $this->assign('praise',$praise);

        //评论
        $alluid = Db::name('comment sc')->where(['aid'=>$id])->field('uid')->select();
        $newalluid = array_column($alluid,'uid');
        $alluser = Db::name('user_info')->where('id','in',$newalluid)->field('id,wechaname,portrait')->select();
        $newuser = array();
        foreach ($alluser as $uk => $uv) {
            $newuser[$uv['id']] = $uv;
        }
        $this->assign('alluser',$newuser);

        $count = Db::name('cases_record')->where(['cid'=>$cid,'status'=>1,'del'=>0])->select();
        $this->assign('count',count($count));

        return $this->fetch();
    }


    //测试发友说一级页面
    public function index_tow(){
        //服务项目
        $pid=input('param.pid',0);
        $this->assign('pid',$pid);
        //脱发等级
        $gid=input('param.gid',0);
        $this->assign('gid',$gid);
        //地区
        $hid=input('param.hid',0);
        $this->assign('hid',$hid);

        //读取项目
        $project=ProjectModel::getAll();
        $this->assign('project',$project);
        //读取脱发等级
        $grade=GradeModel::getAll();
        $this->assign('grade',$grade);

        //读取案例内容
        $cases=CasesModel::getAll($pid,$hid,$gid,10);

        foreach($cases as $key=>$v)
        {
            //读取该案例的最后一次回复情况
            $casesRecordModel=CasesRecordModel::getOne($v['id']);
            $cases[$key]['pic2']=$casesRecordModel['pic'];
        }
        $this->assign('cases',$cases);
        $this->assign('count',count($cases));
	    return $this->fetch();
    }

    //测试发友说二季页面  复制的2018.2.1
    public function cases_2_2_2(){
    	$id=input('param.id',1);
        $casesRecord=CasesRecordModel::getOneId($id);
        $cid=$casesRecord['cid'];
        $cases=CasesModel::getOne($cid);
        if(empty($cases))
        {
            $this->error('该案例不存在');
        }
        $this->assign('cases',$cases);

        //浏览数增1
        CasesRecordModel::where('id',$id)->setInc('click',1);

        //读取该记录的图片
        if(empty($casesRecord['vurl']))
        {
            $casesRecordImgs=CasesRecordImgsModel::getAll($id);
            $casesRecord['images']=$casesRecordImgs;
        }

        $this->assign('casesRecord',$casesRecord);
        $this->assign('casesRecord',$casesRecord);

        //获取评论内容
        $comment=CommentModel::getAll(6,$id);
        $this->assign('comment',$comment);
        $comment2=CommentModel::getAll2(6,$id);
        $this->assign('comment2',$comment2);

        //读取案例内容广告
        $cont_ad=ContAdModel::getOne(4);
        $this->assign('cont_ad',$cont_ad);
        //详情页医生
        $ids = input('param.id',1);
        $doctor = DoctorModel::find($ids);
        //$doctor=$doctors['docname'];
        $this->assign('doctor',$doctor);

        //读取术前的照片
        $casesImgs=CasesImgsModel::getAll($id);
        $this->assign('casesImgs',$casesImgs);

        //获取评论内容
        $comment=CommentModel::getAll(6,$id);
        $this->assign('comment',$comment);
        $comment2=CommentModel::getAll2(6,$id);
        $this->assign('comment2',$comment2);

        //读取案例内容广告
        $cont_ad=ContAdModel::getOne(4);
        $this->assign('cont_ad',$cont_ad);

        //详情页医生
        $ids = input('param.id',1);
        $doctor = DoctorModel::find($ids);
        //$doctor=$doctors['docname'];
        $this->assign('doctor',$doctor);

        //读取术前的照片
        $casesImgs=CasesImgsModel::getAll($id);
        $this->assign('casesImgs',$casesImgs);
        return $this->fetch();
    }


    //测试发油友发帖页面  复制1.30
    public function ceses_2_2_3(){
        //服务项目
        $pid=input('param.pid',0);
        $this->assign('pid',$pid);
        //脱发等级
        $gid=input('param.gid',0);
        $this->assign('gid',$gid);
        //地区
        $hid=input('param.hid',0);
        $this->assign('hid',$hid);

        //读取项目
        $project=ProjectModel::getAll();
        $this->assign('project',$project);
        //读取脱发等级
        $grade=GradeModel::getAll();
        $this->assign('grade',$grade);

        //读取案例内容
        $cases=CasesModel::getAll($pid,$hid,$gid,10);
        //echo CasesModel::getLastSql();die;

        foreach($cases as $key=>$v)
        {
            //读取该案例的最后一次回复情况
            $casesRecordModel=CasesRecordModel::getOne($v['id']);
            $cases[$key]['pic2']=$casesRecordModel['pic'];
        }


        $this->assign('cases',$cases);
        $this->assign('count',count($cases));
	    return $this->fetch();
    }

    //测试发友说页面
    public function cases_2_2_4(){
	    return $this->fetch('cases_2_2_4');
    }

    //测试发友说 一页面
    public function cases_2_3_1(){
	    return $this->fetch();
    }

    //测试发友说 二页面
    public function cases_2_3_2_2(){
	    return $this->fetch();
    }

    //测试发友说 三页面
    public function cases_2_3_3(){
	    return $this->fetch();
    }



	//案例首页
//	public function index()
//	{
//		//服务项目
//		$pid=input('param.pid',0);
//		$this->assign('pid',$pid);
//
//		//全国院部
//		$gid=input('param.gid',0);
//		$this->assign('gid',$gid);
//
//		//地区
//		$hid=input('param.hid',0);
//
//
//		$this->assign('hid',$hid);
//
//		//读取项目
//		$project=ProjectModel::getAll();
//		$this->assign('project',$project);
//		//读取院部
//		$grade=GradeModel::getAll();
//		$this->assign('grade',$grade);
//
//		//读取案例内容
//		$cases=CasesModel::getAll($pid,$hid,$gid,10);
//		//echo CasesModel::getLastSql();die;
//
//		foreach($cases as $key=>$v)
//		{
//			//读取该案例的最后一次回复情况
//			$casesRecordModel=CasesRecordModel::getOne($v['id']);
//			$cases[$key]['pic2']=$casesRecordModel['pic'];
//		}
//
//
//		$this->assign('cases',$cases);
//		$this->assign('count',count($cases));
//		return $this->fetch();
//	}


	//上拉加载
	public function nextPage()
	{
		$offsize=input('param.offsize',0);
		$pid=input('param.pid',0);
		$hid=input('param.hid',0);
        $gid=input('param.gid',0);
        $tid=input('param.tid',0);

		//读取案例内容
		$cases=CasesModel::getAll($pid,$hid,$gid,10,$offsize);
//        $cases=CasesModel::getAll_tow($pid,$hid,$gid,$tid,10,$offsize);

        if(empty($cases)) return 0;

		$reStr='';
		foreach($cases as $key=>$v)
		{

			//读取该案例的最后一次回复情况
			$casesRecordModel=CasesRecordModel::getOne($v['id']);
            $cases[$key]['caser'] = $casesRecordModel;
            $in = $casesRecordModel['id'];
            $cases[$key]['caser'] = $casesRecordModel;
            $cases[$key]['clicks'] = Db::name('cases_record')->where(['cid'=>$v['id']])->sum('click');
            $cases[$key]['clickss'] = Db::name('cases_record')->where(['cid'=>$v['id']])->sum('clicks');
            $praise = Db::name('information')->where(['i_ids'=>$in,'i_uids'=>$this->fansInfo['id'],'i_module'=>1])->find();
            $pic2=$casesRecordModel['pic'];


			//用户是否点赞过
			if($v['id'] && $this->fansInfo['id']==$praise['i_uids']){
			    $name = 66;
			    $hc = 1;
			}else{
                $name = 65;
                $hc = 0;
            }
            $user = $this->fansInfo['id'];
            $str = mb_substr(strip_tags($v['caser']['info']),0,47);
			$uid = $v['caser']['id'];

            if($v['caser']['vurl']){
                $reStr=$reStr.'<a href="' . url('detail', array('uid' => $v['id'])) . '">
                               <div class="csuser abtx" style="padding: 1rem">
						                  <img src="' .get_wxheadimg($v['userInfo']['portrait'],132).'">
						                  <p>'.$v['userInfo']['wechaname'].'</p>
						                  <p class="ddtime">'.$v['data_time'].'</p>
						        </div>
                                <div class="csmian2" style="padding:0 1rem">
                                    <div class="csmian1" style="border:0px;padding:0 ">
                                     	<div class="csmian" >
						                    <div class="csmian" style="border:0px;">
						                        <div class="csinfo">
			                                     	<div class="m" style="width:100%;">
				                                        <video controls preload="auto" width="100%" poster="'.$v['caser']['video_pic'].'" webkit-playsinline="true" x-webkit-airplay="true" playsinline="true"
			                                                      x5-playsinline="true" style="object-fit: fill;">
				                                             <source src="'.$v['caser']['vurl'].'" type="video/mp4"/>
				                                        </video>
			                                        </div>
	                                        	</div>
					                     	</div>
				                		</div>
                                     </div>
                                         <p style="padding:1.5rem 0 0 ">'.$str.'...</p>
                                          <div class="tags"style="padding:1rem 0"><img src="'.HTML_STATIC.'/images/a5.png">'.$v['project']['proname'].'</div>
                                          
                                          <div class="plinfos" >
	                                          	<div class="lll">
	                                          		<img src="'.HTML_STATIC.'/images/6.png">'.$v['clicks'].'
	                                          	</div>
	                                          <a href="' . url('record', array('id' =>$v['caser']['id'])) . '#b">
	                                          	<div class="pl"><img src="'.HTML_STATIC.'/images/7.png">'.$v['caser']['comment'].'</div>
	                                          </a>
	                                          <div class="cdz"  name="dianzan" uid="'.$uid.'" res="'.$user.'"  dis="0" datas="'.$hc.'" data-idss="'.$v['id'].'">
	                                          	<img src="'.HTML_STATIC.'/images/'.$name.'.png">
	                                          		<span>'.$v['caser']['praise'].'</span>
	                                          </div>
                                          </div>
                                	</div>
                                </a>'
                                .'<div class="bar"></div>';
            }else{
                $reStr=$reStr.'<a href="'.url('detail',array('uid'=>$v['id'])).'">
                             <div class="csmian2" >
						     <div class="csmian1" style="border:0px;">
						           <div class="csuser abtx">
						                  <img src="'.get_wxheadimg($v['userInfo']['portrait'],132).'">
						                  <p>'.$v['userInfo']['wechaname'].'</p>
						                  <p class="ddtime">'.$v['data_time'].'
						            </div>
						            <div class="aliimg">
						                  <div class="albf"><img src="'.$v['pic'].'"></div>
						                  <div class="alaf"><img src="'.$pic2.'"></div>
						            </div>
						            <p>'.$str.'...</p>
						            <div class="tags"><img src="'.HTML_STATIC.'/images/a5.png">'.$v['project']['proname'].'</div>
						            
						            <div class="plinfos"><div class="lll"><img src="'.HTML_STATIC.'/images/6.png">'.$v['clicks'].'</div>
						            <a href="' . url('record', array('id' => $v['caser']['id'])) . '#b"><div class="pl"><img src="'.HTML_STATIC.'/images/7.png">'.$v['caser']['comment'].'</div></a>
						            <div class="cdz" name="dianzan" uid="'.$uid.'" res="'.$user.'"  dis="0" datas="'.$hc.'" data-idss="'.$v['id'].'" ><img src="'.HTML_STATIC.'/images/'.$name.'.png"><span>'.$v['caser']['praise'].'</span></div></div>
						     </div>
						     </div>
                    <div class="bar"></div>
                    </a>';
            }
            $reStr=$reStr.'</a>';

		}

		return $reStr;
	}

	//测试案例详细页
	public function detail_tow()
	{
		$id=input('param.id');
		//var_dump($id);die;
		$ttime=input('param.ttime',0);
		$video=input('param.video',0);
		$this->assign('ttime',$ttime);
		$this->assign('video',$video);

		$cases=CasesModel::getOne($id);
		if(empty($cases))
		{
			$this->error('该案例不存在');
		}
		$this->assign('cases',$cases);
		//浏览数增1
		CasesModel::where('id',$id)->setInc('click',1);

        //读取术前的照片
		$casesImgs=CasesImgsModel::getAll($id);
		$this->assign('casesImgs',$casesImgs);
		//读取日记记录
		$casesRecord=CasesRecordModel::getAll($id,$ttime);

		foreach($casesRecord as $key=>$v)
		{
			//读取该记录的图片
			$casesRecordImgs=CasesRecordImgsModel::getAll($v['id']);
			$casesRecord[$key]['images']=$casesRecordImgs;
		}
		$this->assign('casesRecord',$casesRecord);

		//读取相关案例
		$aboutCases=CasesModel::getAll($cases['pid'],0,5);

		foreach($aboutCases as $key=>$v)
		{
			//读取该案例的最后一次回复情况
			$casesRecordModel=CasesRecordModel::getOne($v['id']);
			$aboutCases[$key]['pic2']=$casesRecordModel['pic'];
		}

		$this->assign('aboutCases',$aboutCases);

        //详情页医生
        $ids = input('param.id');
        $doctors = DoctorModel::getIds($ids);

        $this->assign('doctors',$doctors);


		//读取案例内容广告
		$cont_ad=ContAdModel::getOne(4);
		$this->assign('cont_ad',$cont_ad);

		//读取用户是否点赞过
//		$this->assign('praise',$this->getPraise($id,$this->fansInfo['id'],3));
		return $this->fetch();
	}


	//测试术后记录
	public function record_tow()
	{

		$id=input('param.id');
		//var_dump($id);die;
		$casesRecord=CasesRecordModel::getOneId($id);
		$cid=$casesRecord['cid'];
		$cases=CasesModel::getOne($cid);
		if(empty($cases))
		{
			$this->error('该案例不存在');
		}
		$this->assign('cases',$cases);

		//浏览数增1
		CasesRecordModel::where('id',$id)->setInc('click',1);

		//读取该记录的图片
		if(empty($casesRecord['vurl']))
		{
			$casesRecordImgs=CasesRecordImgsModel::getAll($id);
			$casesRecord['images']=$casesRecordImgs;
		}

		$this->assign('casesRecord',$casesRecord);

		//获取评论内容
		$comment=CommentModel::getAll(6,$id);
		$this->assign('comment',$comment);
		$comment2=CommentModel::getAll2(6,$id);
		$this->assign('comment2',$comment2);

		//读取案例内容广告
		$cont_ad=ContAdModel::getOne(4);
		$this->assign('cont_ad',$cont_ad);

		return $this->fetch();
	}



}