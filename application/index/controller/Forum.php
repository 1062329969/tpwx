<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 论坛
 */
namespace app\index\controller;
use ApiOauth\ApiOauth;
use app\admin\controller\ForumType;
use app\index\model\AskModel;
use app\index\model\CommentModel;
use app\index\model\ForumImgsModel;
use app\index\model\ForumModel;
use app\index\model\ForumOnetypeModel;
use app\index\model\ForumTypeModel;
use app\index\model\ContAdModel;
use app\index\model\FotoplaceModel;
use app\index\model\HairCuringModel;
use think\Config;
use think\session;
use think\Db;
use app\index\model\CasesModel;
use app\index\model\CasesRecordModel;
use app\index\model\ProjectModel;
use app\index\model\GradeModel;
use app\index\model\CasesRecordImgsModel;
use app\index\model\DoctorModel;
use app\index\model\CasesImgsModel;
use app\index\model\AskImgsModel;
class Forum extends Wap
{
    protected $timeStamp;
    protected $nonceStr;
    protected $signature;
    protected $shareTitle;
    protected $shareDesc;
    protected $shareLink;
    protected $shareImgUrl;

    protected function _initialize()
    {
        //获得授权
        $this->getUserInfo();
        $this->assign('fansInfo',$this->fansInfo);

        //微信jdk 验证
        $apiOauth=new ApiOauth();
        $ticket = $apiOauth->getJsApiTicket(Config::get('Appid'),Config::get('Appsecret'));
        $this->timeStamp = time();
        $this->nonceStr  = rand(100000,999999);
        $url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        //获得jsdk签名
        $this->signature = $apiOauth->getSignature($this->nonceStr,$ticket,$this->timeStamp,$url);
        $this->assign('timeStamp',$this->timeStamp);
        $this->assign('nonceStr',$this->nonceStr);
        $this->assign('signature',$this->signature);
    }


    public function cases_2_2_4(){

        return $this->fetch();
    }


    //发友说一级页面
    public function index(){
        $aid = input('param.aid',36);
        $this->assign('aid',$aid);
        //服务项目
        $pid=input('param.pid',0);
        $this->assign('pid',$pid);
        //脱发等级
        $gid=input('param.gid',0);
        $this->assign('gid',$gid);
        //地区
        $hid=input('param.hid',0);
        $this->assign('hid',$hid);

        $tid = input('param.tid',0);
        $this->assign('tid',$tid);
        $ppid=input('param.ppid',4);//默认是最新
        $this->assign('ppid',$ppid);

        //收藏
        $pppid = input('param.pppid',1);
        $this->assign('pppid',$pppid);

        $ppppid = AskModel::get_onon(0,8,0,$pppid);
        //发友说
        $id = input('param.id',0);
        $hair = AskModel::f_one($ppid,6,0);
        //大图
        $big_c = AskModel::f_ones($ppid,2,0);
        $position=input('param.position',20);
        $tjGoods=ContAdModel::getAll_tow($position);
        $this->assign('tjGoods',$tjGoods);

        //头条为你推荐四张图片
        $position_s = input('param.position_s',21);
        $recommend = ContAdModel::recommend($position_s);
        $this->assign('recommend',$recommend);

        //一级分类
        $one = Db::name('forum_onetype')->order('id asc')->select();
        $one_arr = array_column($one,'name','id');

        $this->assign('big_c',$big_c);
        $this->assign('one',$one);
        $this->assign('one_arr',$one_arr);
        $this->assign('id',$id);
        $this->assign('hair',$hair);
        $this->assign('count',count($hair));
        return $this->fetch();
    }







    //发友说 二页面
    public function bbs(){
        $id_id = input('param.id');

        $res = Db::name('forum_type')
            ->alias('a')
            ->join('forum b','a.id=b.tid')
            ->where(['a.id'=>$id_id])
            ->select();
        $this->assign('res',$res);
        //服务项目
        $pid=input('param.pid',0);
        $this->assign('pid',$pid);
        //脱发等级
        $gid=input('param.gid',0);
        $this->assign('gid',$gid);
        //地区
        $hid=input('param.hid',0);
        $this->assign('hid',$hid);

        $ppid=input('param.ppid',0);
        $this->assign('ppid',$ppid);

        //发友说
        $id = input('param.id',0);
        $hair = ForumModel::getAll(0,8,0,$ppid);
        $arr = $hair[0];

        $this->assign('arr',$arr);
        $this->assign('hair',$hair);
        $this->assign('id',$id);
        $this->assign('count',count($hair));
        return $this->fetch();
    }

    //发友说 发帖页面
    public function posts(){

        $res = $this->fansInfo['id'];
        $data = input('post.');
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

    //发帖页面
    public function postss(){
        $data = input('post.');
        $res =new ForumModel;
        $forum = $res->allowField(true)->save($data);
        if($forum){
            return true;
        }
        return false;
    }

    //选择模板
    public function bbss(){
        $bbss = ForumTypeModel::getAll();
        $this->assign('bbss',$bbss);
        return $this->fetch();
    }

    //专题页面
    public function homePage(){
        $aid =input('param.aid');
        //专题
        $ppid = input('param.ppid',2);//默认显示热点
//        $forum = ForumOnetypeModel::find($aid);
        $forum = Db::name('forum_onetype')->where(['id'=>$aid])->find();
        $hair=AskModel::getOne_0ne($aid,$ppid,6,0);

        //大图
        $big_c = AskModel::getOne_0nes($aid,$ppid,2,0);


        $one = Db::name('forum_onetype')->order('id asc')->select();
        $one_arr = array_column($one,'name','id');

        $this->assign('one_arr',$one_arr);
        $this->assign('aid',$aid);
        $this->assign('ppid',$ppid);
        $this->assign('hair',$hair);
        $this->assign('big_c',$big_c);
        $this->assign('forum',$forum);
        $this->assign('count',count($forum));
        return $this->fetch();
    }


    //专题页面下拉加载
    public function homepagenextpage(){
        $ppid = input('param.pid');
        $tid=input('param.tid');
        $offsize=input('param.offsize',0);

        $big_offsize = ($offsize / 3);
//        $forum= AskModel::f_tow($tid,$ppid,6,$offsize);
        $hair = AskModel::f_tow($tid,$ppid,6,$offsize);

        //大图
        $big_c = AskModel::f_tows($tid,$ppid,2,$big_offsize);




        if(empty($hair) && empty($big_c))
        {
            return 0;
        }
        //一级分类
        $one = Db::name('forum_onetype')->order('id asc')->select();
        $one_arr = array_column($one,'name','id');

       /* $returnStr="";
        foreach ($forum as $key=>$value)
        {
            $returnStr .= "<a href='".url('detail',array('uid'=>$value['id']))."''>";


            if($value['pic_s']==1)
            {
                $returnStr=$returnStr.'<div class="tpic1">
			        <div class="tp1left">
			            <div class="title">'.$value['qtitle'].'</div>
			            <div class="infos">
			                <span>'.$value['userInfo']['wechaname'].'</span>
			                <span><img src="'.HTML_STATIC.'/images/plnum.png">'.$value['comment'].'</span>
			                <span>'.$value['create_time'].'</span>
			            </div>
			        </div>
			        <div class="pics">
			            <img src="'.$value['pic_s'].'">
			        </div>
			    </div>';
            }
            else
            {

                $returnStr=$returnStr.'<div class="csmian2" style="width: 100%; background: white;">
                <div class="csmian1">
                    <div class="csmian3" style="border:0px;">
                        <div class="csuser abtx">
                            <p class="dytitle">'.$value['qtitle'].'</p>
                            <div class="plinfos">
                                <div class="o">
                                   <div class="clearfix cj-name"><img class="mui-pull-left" src="'.HTML_STATIC.'/images/31.jpg"/><span class="mui-pull-left">阳光的暖冬</span></div>
                                </div>
                                 <div class="lll">'.$value['click'].'阅读</div>
                             </div>

                            </div>
                            <div class="dyimg">
                            <img src="'.$value['pic_s'].'">
                        </div>
                        </div>


                        <p style="clear: both;"></p>
                    </div>
                </div>
            </div>'
                    .'<div class="bar"></div>';

            }

            $returnStr=$returnStr.'</a>';

        }
        print_r($returnStr);die;*/
        $this->assign('big_c',$big_c);
        $this->assign('hair',$hair);
        return $this->fetch();

    }


    //发友说详情页面
    public function detail(){

        //发友说
        $id = input('param.uid');
        $this->assign('id',$id);
        $list = Db::name('ask')
            ->alias('a')
            ->join('forum_onetype b','a.fid = b.id')
            ->where(['a.id'=>$id])
            ->find();
        $this->assign('list',$list);
        //获取内容总数
        $res = strip_tags($list['question']);
        $quantity = mb_strlen($res,'utf-8');
        $this->assign('quantity',$quantity);

        //浏览数增1
        AskModel::where('id',$id)->setInc('click',1);

        $this->assign('id',$id);

        //获取评论内容
        $res = AskModel::find($id);
        $ress = $res['id'];
        //收藏
        $sc = Db::name('collection')->where(['aid'=>$id,'uid'=>$this->fansInfo['id']])->find();
        //点赞
        $praise = Db::name('information')->where(['i_ids'=>$id,'i_uids'=>$this->fansInfo['id'],'i_module'=>5,'i_type'=>2])->find();
        $this->assign('sc',$sc);
        $this->assign('praise',$praise);

        $this->assign('res',$res);
        $this->assign('ress',$ress);
        $comment = Db::name('comment')
            ->where(['aid'=>$id,'plid'=>0,'type'=>5])
//                ->join('haircarecommentparaise sp','sc.c_id=sp.p_cid','LEFT')
            ->order('id desc')
            ->select();
        foreach($comment as $key=>$v)
        {
            $comment[$key]['child'] = Db::name('comment')->where(['uid'=>$id,'plid'=>$v['plid']])->order('id asc')->select();
            //查看评论是否点赞
            $comment[$key]['info']=$praise_s = Db::name('information')->where(['i_ids'=>$v['id'],'i_uids'=>$this->fansInfo['id'],'i_module'=>5,'i_type'=>2])->find();
            $comment[$key]['child']= Db::name('comment')->where(['aid'=>$id,'plid'=>$v['id']])->order('aid asc')->select();
//            echo Db::getLastSql();die;

        }
        $this->assign('comment',$comment);
        $comment3=CommentModel::getAll(5,$ress);
        $this->assign('comment3',count($comment3));
        $comment2=CommentModel::getAll2(5,$ress);
        $this->assign('comment2',$comment2);



        //读取案例内容广告
        $cont_ad=ContAdModel::getOne(4);
        $this->assign('cont_ad',$cont_ad);

        //详情页医生
        $ids = input('param.id');
        $doctor = DoctorModel::find($ids);
        //$doctor=$doctors['docname'];
        $this->assign('doctor',$doctor);

        //读取术前的照片
        $casesImgs=CasesImgsModel::getAll($id);
        $this->assign('casesImgs',$casesImgs);
        //读取案例内容广告
        $cont_ad=ContAdModel::getOne(4);
        $this->assign('cont_ad',$cont_ad);

        //详情页医生
        $ids = input('param.id');
        $doctor = DoctorModel::find($ids);
        //$doctor=$doctors['docname'];
        $this->assign('doctor',$doctor);


        //评论
        $alluid = Db::name('comment sc')->where(['aid'=>$id])->field('uid')->select();
        $newalluid = array_column($alluid,'uid');
        $alluser = Db::name('user_info')->where('id','in',$newalluid)->field('id,wechaname,portrait')->select();
        $newuser = array();
        foreach ($alluser as $uk => $uv) {
            $newuser[$uv['id']] = $uv;
        }

        $this->assign('alluser',$newuser);



        //读取术前的照片
        $casesImgs=CasesImgsModel::getAll($id);
        $this->assign('casesImgs',$casesImgs);
        return $this->fetch();



    }


    //导航栏目
    public function  Navigation(){
        $id = input('param.id');
        $res = ForumModel::get_Navigation();

    }

    //圈子首页读取全部分类
    public function homr2018_2_2_2(){
        $tid=input('param.tid');
        $forum=ForumModel::getAll($tid);
        //var_dump($forum);die;
        $this->assign('forum',$forum);
//        $this->assign('forum',$forumType);
        $this->assign('count',count($forum));
        return $this->fetch('cases_2_2_1');
    }

    //圈子首页2
    public function index2()
    {

        $tid=input('param.tid');
        $postion=input('param.postion',0);

        $forum=ForumModel::getAll($tid);
        foreach ($forum as $key=>$v)
        {

            $forum[$key]['imgs_nums']=ForumImgsModel::getCountImgs($v['id']);
        }

        $this->assign('forum',$forum);
        $this->assign('count',count($forum));

        //读取帖子的首页推荐帖子 焦点图
        $forumTj=ForumModel::getTj($tid);
        $this->assign('forumTj',$forumTj);
        //读取圈子全部分类
        $forumType=ForumTypeModel::getAll();
        foreach ($forumType as $key=>$v)
        {
            if($v['id']==$tid)
            {
                $postion=$key;
            }
        }
        $this->assign('forumType',$forumType);
        //读取圈子的当前分类
        $fType=ForumTypeModel::getOne($tid);
        $this->assign('fType',$fType);
        $this->assign('tid',$tid);
        $this->assign('postion',$postion);
        return $this->fetch();
    }

    //下拉加载
    public function index2NextPage()
    {
        $ppid = input('param.tid',3);
        $offsize=input('param.offsize',0);
        $big_offsize = ($offsize / 3);

        //大图
//        $big_c = AskModel::f_ones($ppid,2,$offsize);
//
//
//        $forum= AskModel::f_one($ppid,6,$offsize);

        $hair = AskModel::f_one($ppid,6,$offsize);
        //大图
        $big_c = AskModel::f_ones($ppid,2,$big_offsize);

        if(empty($hair) && empty($big_c))
        {
            return 0;
        }


        //一级分类
        $one = Db::name('forum_onetype')->order('id asc')->select();
        $one_arr = array_column($one,'name','id');
        $returnStr="";

        /*foreach ($forum as $key=>$value)
        {
            $returnStr.="<a href='".url('detail',array('uid'=>$value['id']))."''>";
                $returnStr=$returnStr.'<div class="csmian2" style="width: 100%; background: white;">
                <div class="csmian1">
                    <div class="csmian3" style="border:0px;">
                        <div class="csuser abtx">
                            <p class="dytitle">'.$value['qtitle'].'</p>
                            <div class="plinfos">
                                <div class="o">
                                   <div class="clearfix cj-name"><img class="mui-pull-left" src="'.get_wxheadimg($value['userInfo']['portrait'],132).'"/><span class="mui-pull-left">'.$value['userInfo']['wechaname'].'</span></div>
                                </div>
                                <div class="lll">'.comment_sum($value['click']).'阅读</div>
                            </div>
                        </div>
                        <div class="dyimg">
                            <img src="'.$value['pic_s'].'">
                        </div>
                        <p style="clear: both;"></p>
                    </div>
                </div>
            </div>'
                .'<div class="bar"></div>';


            $returnStr=$returnStr.'</a>';

        }

        return $returnStr;*/
        $this->assign('big_c',$big_c);
        $this->assign('hair',$hair);
        return $this->fetch();
    }

    //圈子详细页
    public function detail_tow()
    {
        $id=input('param.id');
        if(empty($id) || !is_numeric($id))
        {
            $this->error('参数错误');
        }

        $forum=ForumModel::getOne($id);

        if(empty($forum))
        {
            $this->error('参数错误');
        }
        //浏览数增1
        ForumModel::where('id',$id)->setInc('click',1);
        //记录足记
        $FotoplaceModel=new FotoplaceModel();
        $FotoplaceModel->save(['uid'=>$this->fansInfo['id'],'aid'=>$id,'type'=>4]);


        $this->assign('forum',$forum);
        $images=ForumImgsModel::getAll($id);
        $this->assign('images',$images);
        //读取帖子的分类
        $forumType=ForumTypeModel::getOne($forum['tid']);
        $arcNums=ForumModel::getCount($forumType['id']);
        $forumType['arcNums']=$arcNums;
        $this->assign('forumType',$forumType);
        //读取帖子内容广告
        $cont_ad=ContAdModel::getOne(3);
        $this->assign('cont_ad',$cont_ad);

        //获取评论内容
        $comment=CommentModel::getAll(4,$id);
        $this->assign('comment',$comment);
        $comment2=CommentModel::getAll2(4,$id);
        $this->assign('comment2',$comment2);

        //生成分享的内容
        $this->assign('aid',$id);
        $this->assign('shareType',4);
        $this->shareTitle=$forum['foname'];   // 分享标题
        $this->shareDesc='';    // 分享描述
        $this->shareLink=Config::get('domain').url('detail',array('id'=>$id));// 分享链接
        $this->shareImgUrl=Config::get('domain').$forum['pic'];     //

        $this->assign('shareTitle',$this->shareTitle);
        $this->assign('shareDesc',$this->shareDesc);
        $this->assign('shareLink',$this->shareLink);
        $this->assign('shareImgUrl',$this->shareImgUrl);


        return $this->fetch();
    }


    //提交帖子
    public function add()
    {
        $request=request();
        if($request->method()=='POST')
        {
            $data=input('post.');
            $forum=new ForumModel();
            if($forum->allowField(true)->save(['tid'=>$data['tid'],'uid'=>$this->fansInfo['id'],'text'=>$data['text'],'foname'=>$data['foname']]))
            {
                if(isset($data['images']))
                {
                    foreach ($data['images'] as $k=>$v)
                    {
                        $return=$this->saveBase64Image($v);
                        if($return['code']==0)
                        {
                            $ForumImgsModel=new ForumImgsModel();
                            $ForumImgsModel->save(['fid'=>$forum->id,'pic'=>$return['url']]);
                        }
                    }
                }
                return ['code'=>1,'id'=>$forum->id];
            }
        }
        else
        {
            $this->checkLogin();

            $tid=input('param.tid');
            $this->assign('tid',$tid);
            return $this->fetch();
        }
    }


    /**
     * 保存64位编码图片
     */

    public function saveBase64Image($base64_image_content){

        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result))
        {

            //图片后缀
            $type = $result[2];

            //保存位置--图片名
            $image_name=date('His').str_pad(mt_rand(1, 99999999), 5, '0', STR_PAD_LEFT).".".$type;
            $image_url = '/uploads/'.date('Ymd').'/'.$image_name;
            $image_path = '/uploads/'.date('Ymd').'/';
            if(!is_dir(dirname('.'.$image_url)))
            {
                mkdir(dirname('.'.$image_url),0700,true);
            }

            //解码
            $decode=base64_decode(str_replace($result[1], '', $base64_image_content));
            if (file_put_contents('.'.$image_url, $decode))
            {
                $data['code']=0;
                $data['imageName']=$image_name;
                $data['url']=$image_url;
                $data['msg']='保存成功！';

                //压缩图片
                $image = \think\Image::open('.'.$data['url']);
                // 按照原图的比例生成一个最大为300*300的缩略图并保存为thumb.png
                $image->thumb(300,300,\think\Image::THUMB_CENTER)->save('.'.$image_path.'m_'.$image_name);
                $data['url']=$image_path.'m_'.$image_name;
            }else{
                $data['code']=1;
                $data['imgageName']='';
                $data['url']='';
                $data['msg']='图片保存失败！';
            }
        }else{
            $data['code']=1;
            $data['imgageName']='';
            $data['url']='';
            $data['msg']='base64图片格式有误！';


        }
        return $data;


    }



}