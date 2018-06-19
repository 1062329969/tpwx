<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 微信支付的回调
 */

namespace app\index\controller;
use app\admin\model\HaircareModel;
use app\index\model\HaircarecollectionModel;
use app\index\model\HaircarecommentModel;
use app\index\model\HaircarecommentparaiseModel;
use app\index\model\HaircarepraiseModel;
use app\index\model\InformationModel;
use think\Db;
use think\Session;

class Haircare extends Wap
{
    protected function _initialize()
    {
        //获得授权
        $this->getUserInfo();
        $this->checkLogin();
        $this->assign('fansInfo',$this->fansInfo);
    }

    public function article(){
        $from=input('param.from');
        if($from=='index'){
            Session::set('from','index');
        }elseif($from=='bottom'){
            Session::set('from','bottom');
        }
        $hc_type=input('param.hc_type',1);
        $list = Db::name('haircare')->where(['hc_type'=>$hc_type])->order('hc_id desc')->paginate(4);
        // echo Db::getLastSql();die;
        $this->assign('hc_type',$hc_type);
        $this->assign('list',$list);
        return $this->fetch();
    }
    public function articlelist(){
        $hc_type=input('param.hc_type',1);
        $list = Db::name('haircare')->where(['hc_type'=>$hc_type])->order('hc_id desc')->paginate(4);
        return json($list);
    }

    public function commontlist(){
        $aid=input('param.aid',1);
        $commontlist = Db::name('stagecomment sc')
            ->where(['c_aid'=>$aid,'c_tid'=>0])
            ->join('stagepraise sp','sc.c_id=sp.p_cid','LEFT')
            ->order('c_id asc')
            ->paginate(1)->toArray();
        // var_dump($commontlist);die;
        foreach ($commontlist['data'] as $key => $value) {
            $commontlist['data'][$key]['child'] = Db::name('stagecomment')->where(['c_aid'=>$aid,'c_tid'=>$value['c_id']])->order('c_id asc')->select();
            // echo Db::getLastSql();die;
        }
        // var_dump($commontlist);die;
        return json($commontlist);
    }

    public function articleinfo(){
        $aid=input('param.aid',1);
        if($aid){
            //对两表添加浏览数
            HaircareModel::where(['hc_id'=>$aid])->setInc('hc_browse');
            $Stagearticle = HaircareModel::find($aid);
            $praise = HaircarepraiseModel::where(['p_said'=>$aid,'p_uid'=>$this->fansInfo['id']])->find();
            $sc = HaircarecollectionModel::where(['c_said'=>$aid,'c_uid'=>$this->fansInfo['id']])->find();
            $this->assign('stagearticle',$Stagearticle);
            $this->assign('praise',$praise);
            $this->assign('sc',$sc);

            $alluid = Db::name('haircarecomment sc')->where(['c_aid'=>$aid])->field('c_uid')->select();
            $newalluid = array_column($alluid,'c_uid');
            $alluser = Db::name('user_info')->where('id','in',$newalluid)->field('id,wechaname,portrait')->select();
            $newuser = array();
            foreach ($alluser as $uk => $uv) {
                $newuser[$uv['id']] = $uv;
            }


            $commontlist = Db::name('haircarecomment sc')
                ->where(['c_aid'=>$aid,'c_tid'=>0])
//                ->join('haircarecommentparaise sp','sc.c_id=sp.p_cid','LEFT')
                ->order('c_id asc')
                ->select();
            foreach ($commontlist as $key => $value) {
                $praise = Db::name('haircarecommentparaise')->where(['p_cid'=>$value['c_id']])->select();
                $commontlist[$key]['praise'] = array_column($praise,'p_uid');
                $commontlist[$key]['child'] = Db::name('haircarecomment')->where(['c_aid'=>$aid,'c_tid'=>$value['c_id']])->order('c_id asc')->select();
            }

            // echo Db::getLastSql();die;
            // var_dump($commontlist);die;
            $this->assign('alluser',$newuser);
            $this->assign('commontlist',$commontlist);
            return $this->fetch();
        }else{
            $this->error('您不是术后用户');
        }
    }

    public function articlepraise(){
        $HaircarepraiseModel = new HaircarepraiseModel();
        $HaircareModel = new HaircareModel();
        if(input('param.data')==0){
            $data['p_said'] = $aid = input('param.aid',1);
            $data['p_uid'] = $this->fansInfo['id'];
            $data['p_time'] = time();

            if($HaircarepraiseModel->allowField(true)->save($data)){
                $HaircareModel->where(['hc_id'=>$aid])->setInc('hc_collection');
                $this->success('成功');
            }else{
                $this->error('失败');
            }
        }else{
            $data['p_said'] = $aid = input('param.aid',1);
            $data['p_uid'] = $this->fansInfo['id'];
            if($HaircarepraiseModel->where($data)->delete()){
                $HaircareModel->where(['hc_id'=>$aid])->setDec('hc_collection');
                $this->success('成功');
            }else{
                $this->error('失败');
            }
        }
    }

    public function articlecollection(){
        $HaircarecollectionModel = new HaircarecollectionModel();
        $HaircareModel = new HaircareModel();
        if(input('param.data')==0){
            $data['c_said'] = $aid = input('param.aid',1);
            $data['c_uid'] = $this->fansInfo['id'];
            $data['c_time'] = time();

            if($HaircarecollectionModel->allowField(true)->save($data)){
                $HaircareModel->where(['hc_id'=>$aid])->setInc('hc_sc');
                $this->success('成功');
            }else{
                $this->error('失败');
            }
        }else{
            $data['c_said'] = $aid = input('param.aid',1);
            $data['c_uid'] = $this->fansInfo['id'];
            if($HaircarecollectionModel->where($data)->delete()){
                $HaircareModel->where(['hc_id'=>$aid])->setDec('hc_sc');
                $this->success('成功');
            }else{
                $this->error('失败');
            }
        }
    }

    public function articlecomment(){
        $data=input('post.');
        $datas['c_uid'] = $this->fansInfo['id'];
        $datas['c_content'] = $this->filter($data['commnet_text']);
        $datas['c_oldcontent'] = $data['commnet_text'];
        $datas['c_aid'] = $data['aid'];
        $datas['c_pid'] = $data['pid'];
        $datas['c_tid'] = $data['tid'];
        $datas['c_cuid'] = $data['cuid'];
        if($data['cuid'] == $this->fansInfo['id']){
            $this->error('不能回复自己的评论');
        }
        $datas['c_addtime'] = time();
        $datas['c_praise'] = 0;
        $HaircarecommentModel = new HaircarecommentModel();
        if($HaircarecommentModel->allowField(true)->save($datas)){
            if($data['tid']==0){
                Db::name('haircare')->where(['hc_id'=>$data['aid']])->setInc('hc_reply');

            }
            //存入表中
            $info = new InformationModel();
            $info->allowField(true)->save(['i_type'=>1,'i_module'=>2,'i_uid'=>$data['cuid'],'i_note'=>$data['commnet_text'],'i_ids'=>$data['aid'],'i_addtime'=>time(),'i_uids'=>$this->fansInfo['id']]);


            $this->success('成功');
        }else{
            $this->error('失败');
        }



    }

    public function articlecommentpraise(){
        $HaircarepraiseModel = new HaircarecommentparaiseModel();
        $HaircarecommentModel = new HaircarecommentModel();
        $data=input('get.');
        if($data['data']==0){
            $datas['p_uid'] = $this->fansInfo['id'];
            $datas['p_said'] = $data['p_said'];
            $datas['p_cid'] = $data['p_cid'];
            $datas['p_time'] = time();

            if($HaircarepraiseModel->allowField(true)->save($datas)){
                $HaircarecommentModel->where(['c_id'=>$data['p_cid']])->setInc('c_praise');
                $this->success('成功');
            }else{
                $this->error('失败');
            }
        }else{
            $datas['p_cid'] = $data['p_cid'];
            $datas['p_uid'] = $this->fansInfo['id'];
            if($HaircarepraiseModel->where($datas)->delete()){
                $HaircarecommentModel->where(['c_id'=>$data['p_cid']])->setDec('c_praise');
                $this->success('成功');
            }else{
                $this->error('失败');
            }
        }
    }
}
?>