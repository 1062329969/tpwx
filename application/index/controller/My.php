<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 我的
 */
namespace app\index\controller;

use app\index\model\ContAdModel;
use app\index\model\GoodsOrdersGoodsModel;
use app\index\model\GoodsOrdersModel;
use app\index\model\GoodsOrdersReturnModel;
use app\index\model\MsgModel;
use app\index\model\TrackImgsModel;
use app\index\model\TrackModel;
use app\index\model\UserInfoModel;
use app\index\model\CollectionModel;
use app\index\model\CommentModel;
use app\index\model\CouponModel;
use app\index\model\FotoplaceModel;
use app\index\model\IntegralModel;
use app\index\model\VisiBackModel;
use Phpqrcode\QRcode;
use think\Db;
use think\Session;

class My extends Wap
{
    protected function _initialize()
    {
        $this->checkLogin();

        //获得授权
        $this->getUserInfo();
        $this->assign('fansInfo',$this->fansInfo);
    }

    public function index()
    {
        //获取优惠券的数目
        $couponNum=CouponModel::getCount();
        $this->assign('couponNum',$couponNum);
        //获得收藏记录的数目
        $collectionNum=CollectionModel::getCount($this->fansInfo['id']);
        $this->assign('collectionNum',$collectionNum);
        //获得足记记录的数目
        $fotoplaceNum=FotoplaceModel::getCount($this->fansInfo['id']);
        $this->assign('fotoplaceNum',$fotoplaceNum);
        //判断是否是内部员工
        $staff=Db::name('staff')->where(['stel'=>['like','%'.$this->fansInfo['phonenum'].'%']])->find();
        if(!empty($staff))
        {
            $isStaff=1;
        }
        else
        {
            $isStaff=0;
        }
        $this->assign('isStaff',$isStaff);
        //获取广告
        //读取首页文字广告图片
        $cont_ad=ContAdModel::getAll(10);
        $this->assign('cont_ad',$cont_ad);
        //读取病历表的id
        $visit=Db::name('visit')->where(['tel'=>$this->fansInfo['phonenum']])->find();
        $this->assign('visit',$visit);
        //获得代付款的数量
        $df=GoodsOrdersModel::myOrderCount($this->fansInfo['id'],1);
        $this->assign('df',$df);
        //待发货
//        $dfh=GoodsOrdersModel::myOrderCount($this->fansInfo['id'],2);
//        $this->assign('dfh',$dfh);
        //待收货
        $dsh=GoodsOrdersModel::myOrderCount($this->fansInfo['id'],3);
        $this->assign('dsh',$dsh);
//        //退换货
//        $thh=GoodsOrdersModel::myOrderCount($this->fansInfo['id'],4);
//        $this->assign('thh',$thh);
        $goodsOrdersModel=new GoodsOrdersModel();
        $orders=$goodsOrdersModel->myOrder($this->fansInfo['id'],5);
        //读取没有评价的商品
        if(!empty($orders)){
            foreach ($orders as $k => $v){
                $gog =  GoodsOrdersGoodsModel::where(['status'=>0])
                    ->where('oid',$v['id'])
                    ->order('id desc')
                    ->select();
                if(!$gog){
                    unset($orders[$k]);
                }else{
                    $orders[$k]['gog'] = $gog;
                }
            }
        }else{
            $orders=array();
        }
        $dpj = count($orders);
        $this->assign('dpj',$dpj);

        $tksh=Db::name('goods_orders_return')->where(['return_uid'=>$this->fansInfo['id']])->where('return_auditstate !=1 ')->count();
        $this->assign('tksh',$tksh);

        //统计未读消息数
        $msgnums=MsgModel::getNoRead($this->fansInfo['id']);
        $this->assign('msgnums',$msgnums);



        return $this->fetch();
    }


    //个人信息
    public function myinfo()
    {
        return $this->fetch();
    }

    //更新个人信息
    public function myinfoUpdate()
    {
        $data=input('param.');
        if(empty($data['password'])) unset($data['password']);
        Db::name('user_info')->where('wecha_id',$data['wecha_id'])->update($data);
        return array('code'=>'1');
    }

    //我的评论
    public function mycomm()
    {
        $cont_comment=Db::name('cont_comment')->order('id desc')->where('user_id',$this->fansInfo['id'])->limit('20')->select();
        $this->assign('cont_comment',$cont_comment);
        return $this->fetch();
    }


    //意见反馈
    public function myOpinion()
    {
        $request=request();
        if($request->method()=='POST')
        {
            $data=input('param.');
            $data['wecha_id']=$this->fansInfo['wecha_id'];
            $data['create_time']=time();
            Db::name('sys_opinion')->insert($data);
            return array('code'=>'1');
        }
        else
        {
            return $this->fetch();
        }

    }

    //我的消息
    public function mymsg()
    {
        //统计账号未读消息
        $zhmsg=MsgModel::getNoRead($this->fansInfo['id'],1);
        $this->assign('zhmsg',$zhmsg);
        //服药提醒
        $fytxmsg=MsgModel::getNoRead($this->fansInfo['id'],2);
        $this->assign('fytxmsg',$fytxmsg);
        //优惠推荐
        $yhtjmsg=MsgModel::getNoRead($this->fansInfo['id'],3);
        $this->assign('yhtjmsg',$yhtjmsg);
        //订单信息
        $ddxxmsg=MsgModel::getNoRead($this->fansInfo['id'],4);
        $this->assign('ddxxmsg',$ddxxmsg);
        //医师问答
        $yswdmsg=MsgModel::getNoRead($this->fansInfo['id'],5);
        $this->assign('yswdmsg',$yswdmsg);
        //我的社区
        $wdsqmsg=MsgModel::getNoRead($this->fansInfo['id'],6);
        $this->assign('wdsqmsg',$wdsqmsg);

        return $this->fetch();

    }

    //我的积分
    public function myintegral()
    {
        //获得积分记录
        $integral=IntegralModel::getUid($this->fansInfo['id']);
        $this->assign('integral',$integral);
        $this->assign('count',count($integral));
        return $this->fetch();

    }
    //消息提醒页面
    public function msginfo()
    {
        $type=input('param.tid');
        $msglist=MsgModel::getAll($this->fansInfo['id'],$type);
        //更新所有未读消息为已读
        MsgModel::where(['uid'=>$this->fansInfo['id'],'type'=>$type])->update(['is_read'=>1]);
        $this->assign('msglist',$msglist);
        return $this->fetch();
    }


    //下拉加载积分
    public function integralNextPage()
    {
        $offsize=input('param.offsize');
        //获得积分记录
        $integral=IntegralModel::getUid($this->fansInfo['id'],10,$offsize);
        $returnStr="";
        foreach ($integral as $key=>$value)
        {
            $returnStr=$returnStr.'<div class="jfjll">
		        <div class="jfjlmc">'.$value['event'].'</div>
		        <div class="jfjlnum">';
            if($value['type']==0)
            {
                $returnStr=$returnStr.'+';
            }
            else
            {
                $returnStr=$returnStr.'-';
            }

            $returnStr=$returnStr.$value['nums'].'</div>
		        <div class="jfjltime">'.date('Y-m-d',$value['create_time']).'</div>
		    </div>';
        }

        return $returnStr;

    }


    //我的收藏
    public function mycollection()
    {
        $type = input('param.type',1);
        if($type==1){
            $collection = Db::name('collectiongoods cg')
                ->join('goods g','g.id=cg.cg_gid')
                ->where(['cg_uid'=>$this->fansInfo['id']])
                ->order('cg_id desc')
                ->select();
            $this->assign('collection',$collection);
        }elseif($type==2){
            //获得收藏记录
            $collection = Db::name('collection')->where(['uid'=>$this->fansInfo['id'],'type'=>5])->order('create_time desc')->limit(0,20)->select();
//            $collection=CollectionModel::getAll($this->fansInfo['id']);
            $this->assign('collection',$collection);
        }else{
            $collection = Db::name('haircarecollection')->where(['c_uid'=>$this->fansInfo['id']])->order('c_time desc')->limit(0,20)->select();
//            $collection=CollectionModel::getAll($this->fansInfo['id']);
            $this->assign('collection',$collection);
        }
        $this->assign('type',$type);
        return $this->fetch();
    }

    //我的足记
    public function myfotoplace()
    {
        //获得足记记录
        $fotoplace=FotoplaceModel::getAll($this->fansInfo['id']);
        $this->assign('fotoplace',$fotoplace);
        return $this->fetch();
    }

    //我的动态及我的评论
    public function mycomment()
    {
        //获得足记记录
        $comment=CommentModel::getUid($this->fansInfo['id']);
        $this->assign('comment',$comment);
        return $this->fetch();
    }


    //电子病历首页
    public function eprindex()
    {
        $visit=Db::name('visit')->where(['tel'=>$this->fansInfo['phonenum']])->order('sort desc,cz_time desc')->select();

        $this->assign('visit',$visit);
        return $this->fetch();

    }

    //电子病历详情
    public function epr()
    {

        $request=request();
        if($request->method()=='POST')
        {
            $data=input('param.');
            $data['create_time']=time();
            $track=new TrackModel();
            $track->allowField(true)->save($data);

            if(isset($data['images']))
            {

                foreach ($data['images'] as $k=>$v)
                {
                    $return=$this->saveBase64Image($v);
                    if($return['code']==0)
                    {
                        $TrackModel=new TrackImgsModel();
                        $TrackModel->save(['aid'=>$track->id,'pic'=>$return['url']]);
                    }

                }
            }

            return ['code'=>1];
        }
        else
        {
            $id=input('param.id');
            if(!empty($this->fansInfo['phonenum']))
            {
                $visit=Db::name('visit')->where(['tel'=>$this->fansInfo['phonenum'],'id'=>$id])->find();
            }
            else
            {
                $visit='';
            }
            $this->assign('visit',$visit);
            return $this->fetch();
        }
    }

    //疗程跟踪反馈
    public function track()
    {
        //$vid=input('param.vid');
        //通过手机号码获得病历的ID
        $visit=Db::name('visit')->where(['tel'=>$this->fansInfo['phonenum']])->order('id desc')->find();
        if(!empty($visit))
        {
            //$track=VisiBackModel::getAll($visit['id']);
            $track=TrackModel::getAll($visit['id']);
            foreach($track as $k=>$v)
            {
                $track[$k]['images']=TrackImgsModel::where(['aid'=>$v['id']])->select();
            }
        }
        else
        {
            $track="";
        }
        $this->assign('visit',$visit);
        $this->assign('track',$track);
        $this->assign('vid',$visit['id']);
        return $this->fetch();
    }

    //跟踪反馈 详情
    public function trackdetail()
    {

        $id=input('param.id');
        $news=VisiBackModel::find($id);
        $this->assign('news',$news);
        return $this->fetch();
    }


    //跟踪反馈
    public function track_add()
    {

        $vid=input('param.id');
        //获得最后医生回复的日期
        $time="";
        $visiBack=VisiBackModel::where(['del'=>0,'status'=>1,'vid'=>$vid])->order('id desc')->find();
        if(!empty($visiBack))
        {
            $time=get_time($visiBack['create_time']);

        }
        $this->assign('time',$time);

        //通过手机号码获得病历的ID
        $visit=Db::name('visit')->where(['tel'=>$this->fansInfo['phonenum'],'id'=>$vid])->find();
        $this->assign('visit',$visit);
        return $this->fetch();
    }


    //我的订单
    public function mygoods()
    {
        $tid=input('param.tid',0);
        $this->assign('tid',$tid);
        $goodsOrdersModel=new GoodsOrdersModel();
        if($tid==4){
            $this->getreturngoods();
        }else{
            $orders=$goodsOrdersModel->myOrder($this->fansInfo['id'],$tid);
        }
        $this->assign('orders',$orders);
        return $this->fetch();
    }

    public function myreturn(){
        $goodsOrdersModel=new GoodsOrdersModel();
        // $orders=$goodsOrdersModel->myOrder($this->fansInfo['id'],5);
        $where['status']=3;
        $where['cancel']=0;
        $where['user_id']=$this->fansInfo['id'];
        $orderlist = Db::name('goods_orders')->where($where)->order('create_time desc')->limit(0,100)->select();
        foreach ($orderlist as $key => $value) {
            $orderlist[$key]['gog'] = Db::name('goods_orders_goods gog')
                ->join('goods g','g.id=gog.gid')
                ->where(['gog.oid'=>$value['id']])
                ->field('g.pic,g.gdname,gog.id,gog.gid,gog.nums,gog.status as `gogstatus`')
                ->select();
        }
        $this->assign('orders',$orderlist);
        return $this->fetch();
    }

    public function myreturnlist(){
        $tid=input('param.tid',0);
        $uid = $this->fansInfo['id'];
        $this->assign('tid',$tid);
        $GoodsOrdersReturnModel = new GoodsOrdersReturnModel();
        $orders = $GoodsOrdersReturnModel->getall($uid);
        $GoodsOrdersGoodsModel = new GoodsOrdersGoodsModel();
        foreach($orders as $key=>$value){
            $orders[$key]['gog'] = $GoodsOrdersGoodsModel->getgogbyid($value['return_gog_id']);
        }
        $this->assign('orders',$orders);
        // var_dump($orders);die;
        return $this->fetch();
        // $orders=Db::name('goods_orders_return gor')->join('goods_orders_goods gog','gor.return_gog_id=gog.id')->where(['gor.return_uid'=>$this->fansInfo['id']])->select();
        // foreach ($orders as $key => $value) {
        //     $orders[$key]['go'] = Db::name('goods_orders')->field('order_id')->where(['id'=>$value['return_go_id']])->find();
        //     $orders[$key]['g'] = Db::name('goods')->field('gdname,pic')->where(['id'=>$value['gid']])->find();
        // }
        // // var_dump($orders);die;
        // $this->assign('orders',$orders);
        // return $this->fetch();
    }

    public function orderinfo(){
        $oid=input('param.oid/d',0);
        $order = Db::name('goods_orders')->find($oid);
        $gog = Db::name('goods_orders_goods')->where(['oid'=>$order['id']])->select();
        foreach ($gog as $key => $value) {
            $gog[$key]['goods'] = Db::name('goods')->find($value['gid']);
        }
        $this->assign('order',$order);
        $this->assign('oid',$oid);
        $this->assign('gog',$gog);
        return $this->fetch();
    }

    public function myreturninfo(){
        $return_id=input('param.return_id',0);
        $myreturninfo = Db::name('goods_orders_return')->find($return_id);
        $myreturninfo['gog'] = Db::name('goods_orders_goods')->find($myreturninfo['return_gog_id']);
        $myreturninfo['g'] = Db::name('goods')->field('gdname,pic')->find($myreturninfo['gog']['gid']);
        //获取重新提交的参数
        $oid = $myreturninfo['gog']['oid'];
        $order_id = Db::name('goods_orders')->field('order_id')->find($oid);
        $goods_order_goods = $myreturninfo['return_gog_id'];
        $gid = $myreturninfo['gog']['gid'];
        $this->assign('gid',$gid);
        $this->assign('order_id',$order_id['order_id']);
        $this->assign('goods_order_goods',$goods_order_goods);
        $this->assign('return_id',$return_id);

        $this->assign('myreturninfo',$myreturninfo);
        return $this->fetch();
    }

    public function mycancelorder(){
        $tid=input('param.tid',0);
        $this->assign('tid',$tid);
        $orders = Db::name('goods_orders')->where(['cancel'=>1,'user_id'=>$this->fansInfo['id']])->order('id desc')->select();
        // echo Db::getLastSql();die;
        // var_dump($orders);die;
        foreach ($orders as $key => $value) {
            $orders[$key]['gog'] = Db::name('goods_orders_goods gog')
                ->join('goods g','g.id=gog.gid','LEFT')
                ->where('gog.oid',$value['id'])
                ->select();
        }
        $this->assign('orders',$orders);
        return $this->fetch();

    }

    public function docancel(){
        $oid=input('param.order_id',0);
        $goodsOrdersModel = new GoodsOrdersModel();
        $val = $goodsOrdersModel->where(['id'=>$oid,'user_id'=>$this->fansInfo['id']])->find();
        if(!$val){
            $this->error('无法取消他人订单');
        }
        $val = $goodsOrdersModel->where(['id'=>$oid])->update(['cancel'=>1]);
        if($val){
            $this->success('取消成功',url('my/mycancel'));
        }else{
            $this->error('取消失败');
        }
    }

    //我的退货退款
    public function getreturngoods(){
        $tid=input('param.tid',0);
        $uid = $this->fansInfo['id'];
        $this->assign('tid',$tid);
        $GoodsOrdersReturnModel = new GoodsOrdersReturnModel();
        $orders = $GoodsOrdersReturnModel->getall($uid);
        $GoodsOrdersGoodsModel = new GoodsOrdersGoodsModel();
        foreach($orders as $key=>$value){
            $orders[$key]['gog'] = $GoodsOrdersGoodsModel->getgogbyid($value['return_gog_id']);
        }
        $this->assign('orders',$orders);
        // var_dump($orders);die;
        return $this->fetch();
    }

    //确认收货
    public function accept()
    {
        $order_id=input('param.order_id',0);
        GoodsOrdersModel::where(['order_id'=>$order_id])->update(['status'=>3]);
        return ['code'=>1];
    }


    //待评价
    public function myevaluate()
    {
        $tid=input('param.tid',0);
        $this->assign('tid',$tid);   //订单类型 0全部 1待付款 2待发货 3待收货 4退换货 5待评价
        //读取该用户已经购买没有评价的商品
        $goodsOrdersModel=new GoodsOrdersModel();
        $orders=$goodsOrdersModel->myOrder($this->fansInfo['id'],5);


        foreach ($orders as $key=>$value)
        {
            $orders[$key]['goods_ids']=explode(',',$value['goods_ids']);
        }

        //echo "<pre>";
        //print_r($orders[0]['order_id']);die;

        //读取没有评价的商品
        if(!empty($orders))
        {
            foreach ($orders as $k => $v){
                $gog =  GoodsOrdersGoodsModel::where(['status'=>0])
                    ->where('oid',$v['id'])
                    ->order('id desc')
                    ->select();
                if(!$gog){
                    unset($orders[$k]);
                }else{
                    $orders[$k]['gog'] = $gog;
                }
//                $oid[] = $v['id'];
            }
//            $goodsOrdersGoods=GoodsOrdersGoodsModel::where(['status'=>0])
//                ->where('oid','in',$oid)
//                ->order('id desc')
//                ->select();
            //echo GoodsOrdersGoodsModel::getLastSql('oid');die;

        }
        else
        {
//            $goodsOrdersGoods='';
            $orders='';
        }

        $this->assign('goodsOrdersGoods',$orders);

        return $this->fetch();
    }

    //我的推广二维码
    public function myqr()
    {
        $save_path = ROOT_PATH.'public/qrcode/';  //图片存储的绝对路径
        $qr_data = config('domain').url('member/reg',['from'=>$this->fansInfo['id']]);
        $qr_level = 'H';
        $qr_size = 10;
        $save_prefix = 'ZETA';


        //检测并创建生成文件夹
        if (!file_exists($save_path)){
            mkdir($save_path);
        }

        $errorCorrectionLevel = 'L';
        if (isset($qr_level) && in_array($qr_level, array('L','M','Q','H'))){
            $errorCorrectionLevel = & $qr_level;
        }
        $matrixPointSize = 4;
        if (isset($qr_size))
        {
            $matrixPointSize = $qr_size;
        }
        if (isset($qr_data))
        {
            if (trim($qr_data) == '')
            {
                die('data cannot be empty!');
            }
            //生成文件名 文件路径+图片名字前缀+md5(名称)+.png
            $filename = $save_path.$save_prefix.md5($this->fansInfo['id'].'|'.$qr_data.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
            //开始生成
            QRcode::png($qr_data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
        }


        $qr='/qrcode/'.basename($filename);
        $this->assign('qr',$qr);


        return $this->fetch();
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

    //退出
    public function out()
    {
        Session::delete('loginId');
        $this->redirect(url('member/alllogin'));
    }

    //修改密码
    public function alterpass()
    {
        $request=request();
        if($request->method()=='POST')
        {
            $data=input('param.');

            if($data['new1']!=$data['new2'])
            {
                return ['code'=>0,'mst'=>'两次输入密码不一致'];
            }

            $data=input('param.');
            if(empty($data['password'])) unset($data['password']);
            $user_info=Db::name('user_info')->where('id',$this->fansInfo['id'])->find();

            if($user_info['password']!=md5($data['old']))
            {
                return ['code'=>0,'mst'=>'旧密码错误'];
            }
            else
            {
                Db::name('user_info')->where('id',$this->fansInfo['id'])->update(['password'=>md5($data['new1'])]);
                return ['code'=>1];

            }


        }
        else
        {
            $id=input('param.id');
            $this->assign('id',$id);
            return $this->fetch();
        }
    }

    public function setoption(){
        return $this->fetch();
    }

}