<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 问答管理控制器
 */

namespace app\admin\controller;
use app\admin\model\AnswerImgsModel;
use app\admin\model\AnswerModel;
use app\admin\model\AskModel;
use app\admin\model\AskImgsModel;
use app\admin\model\UserInfoModel;
use app\admin\model\DoctorModel;
use app\admin\model\ForumOnetypeModel;
use app\admin\model\ProjectModel;
use app\admin\model\VideoCommentModel;
use think\Db;
use think\Session;
use app\admin\model\LogModel;
use app\admin\model\CommentModel;

class Videocomment extends Admin

{

    protected $modelName='';
    public function _initialize()
    {
        parent::_initialize();
        $this->modelName=new AskModel;
    }


    //列表
    private function getwhere($datas){
        $where = ['specialclass'=>0,'del'=>0];

        //搜索内容关键词
        $arr = ['qtitle','question',];
        foreach ($arr as $k=>$v){
            if(isset($datas[$v]) && !empty(trim($datas[$v]))){
                if($v == 'qtitle'||$v == 'question'){
                    $where[$v] = ['like','%'.$datas[$v].'%'];
                }else{
                    $where[$v] .= $datas[$v];
                }
            }
        }


        if(!empty($datas['author'])&& isset($datas['author'])){
            $res = Db::name('admin')->where(['realname'=>['like','%'.$datas['author'].'%']])->find();
            $where['admin_user'] = $res['id'];
        }
        //创建时间
        if(!empty($datas['start_time']) && isset($datas['over_time'])){

            $datas['over_time'] = strtotime($datas['over_time']) + 86400;
            $datas['start_time'] = strtotime($datas['start_time']);
            $where['create_time'] = array('between',$datas['start_time'].','.$datas['over_time']);

        }

        if(Session::get('adminid') !=1){
            $where['admin_user'] = Session::get('adminid');
        }
        //一级分类
        if(!empty($datas['class_first']) && isset($datas['class_first'])){
            $where['class_first'] = $datas['class_first'];
        }
        //二级分类
        if(!empty($datas['class_second']) && isset($datas['class_second'])){
            $where['class_second'] = $datas['class_second'];
        }
        //显示状态
        if(isset($datas['lstatus'])&& !empty($datas['lstatus'])){
            if($datas['lstatus'] == 2){
                $where['status'] = 0;
            }else{
                $where['status'] = $datas['lstatus'];

            }
        }

        //板块
        if(isset($datas['fids']) && !empty($datas['fids'])){
            $where['fid'] = $datas['fids'];
        }
        //板块
        if(isset($datas['fid']) && !empty($datas['fid'])){
            $where['fid'] = $datas['fid'];
        }

        return $where;
    }



    //草稿箱
    private function getwheres($data_s){
        $where = ['specialclass'=>1,'del'=>0];

        //搜索内容关键词
        $arr = ['qtitle','question','author'];
        foreach ($arr as $k=>$v){
            if(isset($data_s[$v]) && !empty(trim($data_s[$v]))){
                if($v == 'qtitle'||$v == 'question' || $v =='author'){
                    $where[$v] = ['like','%'.$data_s[$v].'%'];
                }else{
                    $where[$v] .= $data_s[$v];
                }
            }
        }
        //创建时间
        if(!empty($data_s['start_time']) && isset($data_s['over_time'])){

            $data_s['over_time'] = strtotime($data_s['over_time']) + 86400;
            $data_s['start_time'] = strtotime($data_s['start_time']);
            $where['create_time'] = array('between',$data_s['start_time'].','.$data_s['over_time']);

        }

        if(Session::get('adminid') !=1){
            $where['admin_user'] = Session::get('adminid');
        }
        //一级分类
        if(!empty($data_s['class_first']) && isset($data_s['class_first'])){
            $where['class_first'] = $data_s['class_first'];
        }
        //二级分类
        if(!empty($data_s['class_second']) && isset($data_s['class_second'])){
            $where['class_second'] = $data_s['class_second'];
        }
        //显示状态
        if(isset($data_s['lstatus'])&& !empty($data_s['lstatus'])){
            if($data_s['lstatus'] == 2){
                $where['status'] = 0;
            }else{
                $where['status'] = $data_s['lstatus'];

            }
        }

        //板块
        if(isset($data_s['fids']) && !empty($data_s['fids'])){
            $where['fid'] = $data_s['fids'];
        }
        //板块
        if(isset($data_s['fid']) && !empty($data_s['fid'])){
            $where['fid'] = $data_s['fid'];
        }

        return $where;
    }


    public function index()
    {
        $id = input('param.id');
        $params = input('param.');
        $comment = Db::name('video_comment')->alias('v')->join('user_info u', 'v.uid=u.id')->field('v.*,u.id as uid,u.wechaname,u.portrait')->order('v.id asc')->where(['v.vid' => $id,'del'=>0])->select();

       //获取评论表

        $comment=Db::name('video_comment')->alias('v')->join("user_info u",'v.uid=u.id')->field('v.*,u.portrait,u.wechaname')->where(['vid'=>$id,'plid'=>0])->select();
        //$comment = Db::name('video_comment')->alias('v')->join('user_info u', 'v.uid=u.id')->where(['v.vid' => $id,'del'=>0])->select();
     // dump($comment);exit;
        $article = Db::name('demand_video')->where(['m_id' => $id])->find();


        ///dump($article);exit;
//        $comment_name_lists = Db::name('user_info')->select();
//        foreach ($comment as $k => $v) {
//            if ($v['plid'] == 0) {
//                $sum = array();
//                $sum [$k] = $v;
//                $sum [$k]['son'] = getSubs($comment, $v['id'], $level = 1);
//                $comment_lists[] = $sum[$k];
//            }
//        }
//        $comment_lists=!empty($comment_lists)?$comment_lists:[];
//        foreach ($comment_name_lists as $key => $value) {
//            $comment_names[$value['id']] = $value['wechaname'];
//        }
//        $comment_names=!empty($comment_names)?$comment_names:null;
        //传递对话数
//        $this->assign('dialogue_count', count($comment) - count($comment_lists));
        //传递评论数
//        $this->assign('comment_count', count($comment_lists));
        //传递评论数据
       // $this->assign('comment', $comment_lists);
        $this->assign('content', $comment);
//dump();exit;
        //传递文章数据
        $this->assign('article', $article);
        //传递所有评论关联的wechaname
        //$this->assign('comment_names', $comment_names);
        return $this->fetch();
    }

//	public function indexdemo(){
//        $datas = input('param.');
//        $data = input('param.lname');
//        $statuss = input('param.lstatus');
//        $where = $this->getwhere($datas);
//
//        //用户组
//        $admin_user = Db::name('admin')->field('id,username')->where(['status'=>1,'del'=>0])->select();
//        $admin_user = array_column($admin_user,'username','id');
//        //所属部门
//        $department = Db::name('department')->field('d_id,d_name')->where(['d_state'=>1])->select();
//        $department = array_column($department,'d_name','d_id');
//
//        //分类
//        $category = Db::name('headlineclass')->select();
//
//        //板块
//        $list = Db::name('forum_onetype')->order('id desc')->select();
//        $list = array_column($list,'name','id');
//        $content = Db::name('ask')->where($where)->order('sort desc,create_time desc')->paginate(15,false,['query' => request()->param()]);
//        if(!empty($datas['fid'])){
//            $this->assign('fid',$datas['fid']);
//        }
//        $this->assign('department',$department);
//        $this->assign('datas',$datas);
//        $this->assign('category',$category);
//        $this->assign('data',$data);
//        $this->assign('admin_user',$admin_user);
//        $this->assign('statuss',$statuss);
//        $this->assign('content',$content);
//        $this->assign('list',$list);
//        return $this->fetch();
//    }

    //修改排序
    public function addsort(){
        $data = input('param.sort/a');
        foreach ($data as $k=>$v){
            $list = Db::name('ask')->where(['id'=>$v['pid']])->update(['sort'=>$v['aid'],'update_time'=>time()]);
        }
//        $list = Db::name('ask')->where(['id'=>$data['id']])->update(['sort'=>$data['sort']]);
        if($list){
            return ['code'=>1];
        }else{
            return ['code'=>2];
        }
    }

    //修改状态
    public function editstatus(){
        $id = input('param.id');
        $title = Db::name('ask')->find($id);
        if($title['status'] == 0){
            LogModel::savelog(4,4,input('param.'),'头条修改排序保存成功');
            $user = Db::name('ask')->where(['id'=>$id])->update(['status'=>1]);
        }else{
            LogModel::savelog(4,4,input('param.'),'头条修改排序保存失败');
            $user = Db::name('ask')->where(['id'=>$id])->update(['status'=>0]);
        }
        if($user){
            return ['code'=>1];
        }else{
            return ['code'=>2];
        }
    }

    //用户案例  头条 选择用户
    public function selectuid()
    {
        $data = input('param.');
        $where = array();
        if(isset($data['wechaname'])&& !empty($data['wechaname'])){
            $where['wechaname'] = ['like','%'.$data['wechaname'].'%'];

        }
//        $user_info=UserInfoModel::getAll();
        $user_info = Db::name('userInfo')->where($where)->order('id desc')->paginate(6,false,['query' => request()->param()]);
        $this->assign('user_info',$user_info);
        if(isset($data['wechaname'])){
            $this->assign('wechaname',$data['wechaname']);
        }
        return $this->fetch();
    }


//    //查看评论
//    public function usercomment(){
//	    $id = input('param.id');
//        //查看评论
//        $comment = Db::name('comment')->where(['uid'=>$id])->find();
//
//        $this->assign('comment',$comment);
//        return $this->fetch();
//    }
//查看评论
    public function usercomment()
    {
        $id = input('param.id');
        $comment = Db::name('comment')->alias('c')->join('user_info u', 'c.uid=u.id')->field('c.*,u.id as uid,u.wechaname,u.portrait')->order('create_time asc')->where(['c.aid' => $id,'del'=>0])->select();

        $article = Db::name('ask')->where(['id' => $id])->find();
        $comment_name_lists = Db::name('user_info')->select();
        foreach ($comment as $k => $v) {
            if ($v['plid'] == 0) {
                $sum = array();
                $sum [$k] = $v;
                $sum [$k]['son'] = getSubs($comment, $v['id'], $level = 1);
                $comment_lists[] = $sum[$k];
            }
        }
        $comment_lists=!empty($comment_lists)?$comment_lists:[];
        foreach ($comment_name_lists as $key => $value) {
            $comment_names[$value['id']] = $value['wechaname'];
        }
        $comment_names=!empty($comment_names)?$comment_names:null;
        //传递对话数
        $this->assign('dialogue_count', count($comment) - count($comment_lists));
        //传递评论数
        $this->assign('comment_count', count($comment_lists));
        //传递评论数据
        $this->assign('comment', $comment_lists);
        //传递文章数据
        $this->assign('article', $article);
        //传递所有评论关联的wechaname
        $this->assign('comment_names', $comment_names);
        return $this->fetch();
    }

    public function add()
    {
        $request = request();
        if ($request->method() == 'POST') {
           // dump(input('param.'));exit;
            $Comment = new VideoCommentModel();
            $data = input('post.');
            $uid = input('post.uid') ? input('post.uid') :input('post.pri_uid');
            $comment_id = input('post.comment_id')?input('post.comment_id'):0;
            $comment_uid = input('post.comment_uid');
            $text = input('post.com_content');
            $aid = input('post.article_id');
            $data['uid'] = $uid;
            $data['type'] = 5;
            $data['reuid'] = $comment_uid;
            $data['vid'] = $aid;
            $data['text'] = $text;
            $data['plid'] = $comment_id;
            $data['create_time'] = time();
            if ($Comment->allowField(true)->save($data)) {
                $this->success('添加成功', url('index'));
            } else {
                $this->error('添加失败', url('index'));
            }
        } else {
            return $this->fetch();
        }

    }

    //搜索评论
    public function serch()
    {
        $datas = input('param.');
        $where = $this->get_page_where($datas);

        $content = Db::name('comment')->alias('c')->join('user_info u', 'u.id=c.uid')->field('c.*,u.wechaname')->where($where)->order('id desc,create_time desc')->paginate(15, false, ['query' => request()->param()]);

        $this->assign('datas', $datas);

        $this->assign('content', $content);

        return $this->fetch();
    }

    private function get_page_where($datas)
    {
        $where = ['del' => 0];
        //搜索内容关键词
        foreach ($datas as $k => $v) {
            if (isset($datas['com_tent']) && !empty(trim($datas['com_tent']))) {
                $where['text'] = ['like', '%' . $datas['com_tent'] . '%'];
            } elseif (isset($datas['nickname']) && !empty(trim($datas['nickname']))) {
                $where['u.wechaname'] = ['like', '%' . $datas['nickname'] . '%'];
            } elseif (isset($datas['memberid']) && !empty(trim($datas['memberid']))) {
                $where['uid'] = $datas['memberid'];
            }
        }
        //创建时间
        if (!empty($datas['start_time']) && isset($datas['end_time'])) {
            $where['create_time'] = [
                ['>=', strtotime($datas['start_time'])],
                ['<=', strtotime($datas['end_time'])],
            ];
        }
        return $where;
    }



    //一分钟保存一次
    public function titlesave(){
        $data = input('param.');
        //如果填写了标题，板块，文章一级分类，二级分类。则保存，否则不进项任何操作
        if(!empty($data['qtitle']) && !empty($data['fid'])&& !empty($data['class_first']) && !empty($data['class_second'])){
            $data['admin_user'] = Session::get('adminid');
            //同时也保存到草稿箱
            $data['specialclass']=1;
            $res = $this->modelName->allowField(true)->save($data);
            $id = $this->modelName->getLastInsID($res);
            if ($res){
                LogModel::savelog(4,4,input('param.'),'头条自动保存成功');
                $this->success('添加成功',null,$id);
            }else{
                LogModel::savelog(4,4,input('param.'),'头条自动保存失败');
                $this->error('添加失败',null);
            }
        }
    }

    //修改方法
    public function titleedit(){
        $data = input('param.');
        $id = $data['sids'];
        unset($data['sids']);
        $data['specialclass'] = 1;
        $title = $this->modelName->allowField(true)->save($data,['id'=>$id]);

        if ($title){
            LogModel::savelog(4,4,input('param.'),'头条自动修改保存成功');
            $this->success('添加成功',null,$id);
        }else{
            LogModel::savelog(4,4,input('param.'),'头条自动修改保存失败');
            $this->error('添加失败',null);
        }

    }


    //新增文章测试
    public function addarticle(){
        $request=request();
        if($request->method()=='POST')
        {
            $data=input('post.');
            //多文件上传
            if(empty($data['default_img']))
            {
                $data['default_img']=0;
            }
            //判断用户是否选择分类
            if(empty($data['fid'])){
                $this->error('请选择分类',url('index'));
            }

            $key=0;  //默认图片的位置
            $imgs=array();
            $files = request()->file('pic_s');
            if(count($files)>0)
            {

                foreach($files as $file)
                {
                    // 移动到框架应用根目录/public/uploads/ 目录下
                    $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                    if($info)
                    {

                        $returnImg=str_replace('\\','/','/uploads/'.$info->getSaveName());
                        //生成缩略图
                        if($key==$data['default_img'])
                        {
                            $data['pic_s']=$returnImg;
                        }

                        $imgs[]=$returnImg;
                    }
                    else
                    {
                        $this->error($file->getError());
                    }
                    $key=$key+1;
                }
            }


            if($this->modelName->allowField(true)->save($data))
            {
                //保存多图
                if(count($imgs)>0)
                {
                    foreach($imgs as $k=>$v)
                    {

                        $askImgsModel=new AskImgsModel;
                        $askImgsModel->save(['aid'=>$this->modelName->id,'pic'=>$v]);
                    }
                }

                $this->success('添加成功',url('index'));
            }
            else
            {
                $this->error('添加失败',url('index'));

            }
        }
        else
        {
            //读取项目
            $project=ProjectModel::getAll();
            $forum = ForumOnetypeModel::getAll();
            $userss = Session::get('adminid');//读取管理员id
            $this->assign('userss',$userss);
            $this->assign('project',$project);
            $this->assign('forum',$forum);
            return $this->fetch();
        }
    }

    public function getclasstree(){
        $h_class = Db::name('headlineclass')->where(['hc_pid'=>0])->select();
        $class_arr = [];
        foreach ($h_class as $k => $v){
            $child= Db::name('headlineclass')->where(['hc_pid'=>$v['hc_id']])->select();
            $childarr = [];
            foreach ($child as $kc=>$vc){
                $childarr[$vc['hc_id']] = $vc['hc_name'];
            }
            $class_arr[$v['hc_id']] = [
                'name'=>$v['hc_name'],
                'child'=>$childarr
            ];
        }

        return $class_arr;
    }

    public function alter()
    {
        //获取草稿箱的状态
        $specialclass = input('param.specialclass');
        $request=request();
        if($request->method()=='POST')
        {
            $data=input('post.');
            //判断是否存入草稿箱
            if(!empty($specialclass)){
                $data['specialclass'] = $specialclass;
            }else{
                $data['specialclass'] = 0;
            }

            //上传图片
            $pic=$this->upload($request,'pic_s');
            $home_pic_s=$this->upload($request,'home_pic_s');
            if(!empty($pic))
            {
                $data['pic_s']=$pic;
            }
            else
            {

                unset($data['pic_s']);
            }

            if(!empty($home_pic_s))
            {
                $data['home_pic_s']=$home_pic_s;
            }
            else
            {
                unset($data['home_pic_s']);
            }

            $data['id'] = intval($data['id']);

            //多文件上传
            $imgs=array();
            $imgs=$this->uploadMore($request,'pic_s');
            if($this->modelName->allowField(true)->save($data,['id'=>$data['id']]))
            {
                //保存多图
                if(count($imgs)>0)
                {

                    foreach($imgs as $k=>$v)
                    {
                        $askImgsModel=new AskImgsModel;
                        $askImgsModel->save(['aid'=>$this->modelName->id,'pic'=>$v]);
                    }
                }
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
            $uid =Session::get('adminid');
            $class_arr = $this->getclasstree();
            if($uid == 1){
                $content = Db::name('ask')->where(['id'=>$id])->find();
            }else{
                $content = Db::name('ask')->where(['id'=>$id,'admin_user'=>$uid])->find();
            }

            if($uid != $content['admin_user'] && $uid!=1){
                $this->error('操作失败！',url('index'));
            }
//			$content=$this->modelName->get($id);
            $this->assign('content',$content);

            //读取该问答的所有图片
            $askImgs=AskModel::find($id);
            $this->assign('askImgs',$askImgs);


            //读取项目
            $project=ProjectModel::getAll();
            //文章板块
            $forum = ForumOnetypeModel::getAll();
            //文章分类
            $category = Db::name('headlineclass')->select();

            $this->assign('id',$id);
            $this->assign('class_arr',$class_arr);
            $this->assign('category',$category);
            $this->assign('forum',$forum);
            $this->assign('project',$project);
            return $this->fetch();
        }

    }




    //草稿箱
    public function drafts(){
        $data_s = input('param.');
        $data = input('param.lname');

        $fids = input('param.fids');
        $statuss = input('param.lstatus');
        $where = $this->getwheres($data_s);

        //用户组
        $admin_user = Db::name('admin')->field('id,username')->where(['status'=>1,'del'=>0])->select();
        $admin_user = array_column($admin_user,'username','id');
        //所属部门
        $department = Db::name('department')->field('d_id,d_name')->where(['d_state'=>1])->select();
        $department = array_column($department,'d_name','d_id');

        //分类
        $category = Db::name('headlineclass')->select();

        //板块
        $list = Db::name('forum_onetype')->order('id desc')->select();
        $list = array_column($list,'name','id');
        $content = Db::name('ask')->where($where)->order('sort desc,create_time desc')->paginate(15,false,['query' => request()->param()]);
        if(isset($data_s['fid'])){
            $this->assign('fid',$data_s['fid']);
        }
        $this->assign('fids',$fids);
        $this->assign('department',$department);
        $this->assign('datas',$data_s);
        $this->assign('category',$category);
        $this->assign('data',$data);
        $this->assign('admin_user',$admin_user);
        $this->assign('statuss',$statuss);
        $this->assign('content',$content);
        $this->assign('list',$list);
        return $this->fetch();
    }



    //删除问答图片
    public function delImg()
    {

        $imgurl=input('post.imgurl');
        $id=input('post.id');
        $result = @unlink ('.'.$imgurl);
        AskImgsModel::where(['id'=>$id,'pic'=>$imgurl])->delete();
        return ['code'=>1];

    }

    public function del()
    {
        $data=input('param.');
        $id = $data['id'];
//        foreach ($id as $k=>$v){
//            $id[$k] = intval($v);
//        }

        $uid = Session::get('adminid');
        $res = new AskModel();
        if($uid ==1){
            $res = Db::name('ask')->where('id','in',$id)->update(['del'=>1]);
        }else{
            $res = Db::name('ask')->where('id','in',$id)->where(['admin_user'=>$uid])->update(['del'=>1]);
        }
        if($res)
        {
            LogModel::savelog(4,4,input('param.'),'头条删除成功');
            $this->success('头条删除成功');
        }
        else
        {
            LogModel::savelog(4,4,input('param.'),'头条删除失败');
            $this->error('头条删除失败');
        }
    }

    //回答内容
    public function answer()
    {
        $id=input('param.id');
        $this->assign('aid',$id);
        $content=AnswerModel::getAll($id);
        $this->assign('content',$content);
        return $this->fetch();
    }


    public function answerAlter()
    {
        $request=request();
        if($request->method()=='POST')
        {

            $data=input('post.');
            //上传图片
            $pic=$this->upload($request,'pic');
            if(!empty($pic))
            {
                $data['pic']=$pic;
            }
            else
            {
                unset($data['pic']);
            }

            //多文件上传
            $imgs=array();
            $imgs=$this->uploadMore($request,'pic2');
            $answer=new AnswerModel();
            if($answer->allowField(true)->save($data,['id'=>$data['id']]))
            {

                //保存多图
                if(count($imgs)>0)
                {

                    foreach($imgs as $k=>$v)
                    {
                        $askImgsModel=new AnswerImgsModel;
                        $askImgsModel->save(['aid'=>$answer->id,'pic'=>$v]);
                    }
                }
                $this->assign('aid',$answer->id);
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
            $this->assign('id',$id);
            $aid=input('param.aid');
            $this->assign('aid',$aid);
            $answer=new AnswerModel();
            $content=$answer->get($id);
            $this->assign('content',$content);

            //读取该问答的所有图片
            $askImgs=AnswerImgsModel::getAll($id);
            $this->assign('askImgs',$askImgs);

            return $this->fetch();
        }

    }


    //删除回答图片
    public function answerDelImg()
    {

        $imgurl=input('post.imgurl');
        $id=input('post.id');
        $result = @unlink ('.'.$imgurl);
        AnswerImgsModel::where(['id'=>$id,'pic'=>$imgurl])->delete();
        return ['code'=>1];
    }


    public function answerDel()
    {

        $id=input('param.id');
        $answer=new AnswerModel();
        if($answer->save(['del'=>1],['id'=>$id]))
        {
            echo '{"code":"1"}';
        }
        else
        {
            echo '{"code":"0"}';
        }
    }

}