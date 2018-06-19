<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 首页
 */
namespace app\index\controller;

use app\admin\controller\ContAd;
use app\index\model\CasesModel;
use app\index\model\CasesRecordModel;
use app\index\model\CommentModel;
use app\index\model\ContAdModel;
use app\index\model\ContTextModel;
use app\index\model\ContTypeModel;
use app\index\model\GoodsCartModel;
use app\index\model\GoodsOrdersModel;
use app\index\model\GoodsModel;
use app\index\model\AskModel;
use app\index\model\AskImgsModel;
use app\index\model\CuringModel;
use app\index\model\CuringImgModel;
use app\index\model\HospitalModel;
use app\index\model\PlpraiseModel;
use app\index\model\IndexnewsModel;
use app\index\model\GoodsTypeModel;
use app\index\model\ForumModel;

use think\Db;
use think\session;

class Index extends Wap
{
    protected function _initialize()
    {
        $this->checkLogin();
        //获得授权
        $this->getUserInfo();
        $this->assign('fansInfo',$this->fansInfo);
    }

    //首页更改
    public function home(){
        $portrait = $this->fansInfo['portrait'];
        $portraits = $this->fansInfo['wechaname'];
        $user_type = $this->fansInfo['stage_type'];

        //判断有没有推荐医生
        if(Session::has('rd_id')){
            $this->assign('rd_id',Session::get('rd_id'));
            Session::delete('rd_id');
        }

        //banner时间
        //获取当前时间
        $now_time = time();
        //获取导入病例的时间
        $show_time = $user_type;
        $durs = $now_time - $show_time;
        if ($show_time < 0)
        {
            date('Y-m-d H;i:s',$show_time);
        } else {
            if ($show_time < 259200) {
                $dur = round($durs / 86400). '天';
            } else {
                if ($show_time > 31536000) {
                    $dur = round($durs / 86400). '天';
                }
            }
        }

        //首页banner 推荐小贴士
        if(empty($user_type)){
            $n_type = input('param.n_updown',1);
            //小贴士
            $newss = Db::name('indexnews')->where(['n_type'=>$n_type])-> order('rand()')->find();
            //轮播随机取头条中的内容
            $ask = Db::name('ask')->where(['status'=>1,'del'=>0])-> order('rand()')->select();
            $this->assign('ask',$ask);
            $this->assign('newss',$newss);
        }else{
            //取阶段时间
            $users_type_ss = Db::name('stage')->where('stage_time_start','<',$durs)->where('stage_time','>',$durs)->find();
            //术后的banner
            $users_type_sss = Db::name('stage')
                ->alias('a')
                ->join('stagearticle b','a.s_id = b.a_sid')
                ->where(['a.s_id' => $users_type_ss['s_id']])
                ->find();
            //术后小贴士
            $tpis = Db::name('stage_day')
                ->alias('a')
                ->join('index_tips b','a.x_types = b.i_types')
                ->where(['a.x_type' => $users_type_ss['s_id']])
                ->where('a.x_addtime','ELT',$durs)
                ->where('a.x_updatetime','EGT',$durs)
                ->select();

            //查看用户是否阅读过阶段视频
            $read = Db::name('read')->where(['uid'=>$this->fansInfo['id'],'sid'=>$users_type_ss['s_id']])->find();
            $this->assign('read',$read);
            $this->assign('users_type_sss',$users_type_sss);
            $this->assign('users_type_ss',$users_type_ss);
            $this->assign('tpis',$tpis);
        }

        $this->assign('dur',$dur);
        $this->assign('user_type',$user_type);
        $this->assign('portrait',$portrait);
        $this->assign('portraits',$portraits);


        //案例
        //服务项目
        $pid=input('param.pid',0);
        //脱发等级
        $gid=input('param.gid',0);
        //地区
        $hid=input('param.hid',0);


        //读取首页文字广告图片
        $cont_ad=ContAdModel::getAll(5);

        //植发日记
        $cases= CasesModel::getAll_one($pid,$hid,$gid,10);
//        echo CasesModel::getLastSql();die;

        foreach($cases as $key=>$v)
        {
            //读取该案例的最后一次回复情况
            $casesRecordModel= CasesRecordModel::getOne($v['id']);
            $cases[$key]['pic2']=$casesRecordModel['pic'];
            $cases[$key]['vurl']=$casesRecordModel['vurl'];
            $cases[$key]['info']=$casesRecordModel['info'];
            $cases[$key]['id']=$casesRecordModel['cid'];
            $in = $casesRecordModel['id'];
            $cases[$key]['caser'] = $casesRecordModel;
            $cases[$key]['clicks'] = Db::name('cases_record')->where(['cid'=>$v['id']])->sum('click');
            $cases[$key]['clickss'] = Db::name('cases_record')->where(['cid'=>$v['id']])->sum('clicks');
            //用户是否点赞过
            $cases[$key]['res'] = Db::name('information')->where(['i_ids'=>$in,'i_uids'=>$this->fansInfo['id'],'i_module'=>1,'i_type'=>2])->find();

        }



        //发友说
        $id = input('param.id',0);
        $hair = Db::name('ask')->where(['del'=>0,'status'=>1])->order('create_time desc')->limit('0,5')->select();
        $tid = input('param.tid',0);

        //商城推荐商品  左边大图最后最后上传
        $position=input('param.$position',1);
        $tjGoods=ContAdModel::getAll_tow($position);
        //商城推荐商品下方图
        $res=input('param.$position',11);
        $tj=ContAdModel::getAll_one($res);

        //首页养护
        $vid=0;//input('param.vid',0);
        $news=CuringModel::getAll_tow($tid,$vid);

        //医生说
        $doctor =  Db::name('forum_onetype')
            ->alias('a')
            ->join('ask b','a.id  = b.fid')
            ->where(['b.fid'=>36])
            ->order('create_time desc')
            ->limit('0,3')
            ->select();
        //植发问答
        $hair_askr = Db::name('forum_onetype')
            ->alias('a')
            ->join('ask b','a.id  = b.fid')
            ->where(['b.fid'=>37])
            ->order('create_time desc')
            ->limit('0,3')
            ->select();

        //经验之谈
        $experience = Db::name('forum_onetype')
            ->alias('a')
            ->join('ask b','a.id  = b.fid')
            ->where(['b.fid'=>38])
            ->order('create_time desc')
            ->limit('0,3')
            ->select();

        //头发急救室
        $first_aide = Db::name('forum_onetype')
            ->alias('a')
            ->join('ask b','a.id  = b.fid')
            ->where(['b.fid'=>39])
            ->order('create_time desc')
            ->limit('0,3')
            ->select();

        $this->assign('doctor',$doctor);
        $this->assign('hair_askr',$hair_askr);
        $this->assign('experience',$experience);
        $this->assign('first_aide',$first_aide);

        $this->assign('res',$res);
        $this->assign('cont_ad',$cont_ad);
        $this->assign('pid',$pid);
        $this->assign('gid',$gid);
        $this->assign('hid',$hid);
        $this->assign('tj',$tj);
        $this->assign('$tjGoods',$tjGoods);
        $this->assign('news',$news);
        $this->assign('hair',$hair);
        $this->assign('cases',$cases);
        $this->assign('count',count($cases));
        $this->assign('tjGoods',$tjGoods);
        return $this->fetch();
    }





    public function home2018_1_30(){


        return $this->fetch('cases/detail');

    }

    public function home2018_111(){
        //读取焦点商品
        //$jdGoods=GoodsModel::getJd();
        //$this->assign('jdGoods',$jdGoods);
        //读取商城首页广告
        $adtop=ContAdModel::getAll(14);
        $this->assign('adtop',$adtop);
        //商城首页中间广告
        $adcenter=ContAdModel::getOne(15);
        $this->assign('adcenter',$adcenter);




        //读取商城分类 图标
        $goodsType=GoodsTypeModel::getAll();
        $this->assign('goodsType',$goodsType);
        //读取首页推荐商品
        $tjGoods=GoodsModel::getTj();
        // echo GoodsModel::getLastSql();die;
        $this->assign('tjGoods',$tjGoods);
        //读取首页中间推荐商品
        $stjGoods=GoodsModel::getsTj();
        $this->assign('stjGoods',$stjGoods);
        //读取商城首页广告
        $ad1=ContAdModel::getOne(2);
        //$ad2=ContAdModel::getOne(6);
        //$ad3=ContAdModel::getOne(7);
        $ad4=ContAdModel::getOne(8);
        $ad5=ContAdModel::getOne(9);

        $this->assign('ad1',$ad1);
        $this->assign('ad4',$ad4);
        $this->assign('ad5',$ad5);
        return $this->fetch('home2018_1_30');
    }


    //微官网首页
    public function home_tow()
    {

        //读取首页焦点图广告图片
        $cont_ad=ContAdModel::getAll(1);
        $this->assign('cont_ad',$cont_ad);
        //读取首页文字广告图片
        $cont_ad2=ContAdModel::getAll(5);
        $this->assign('cont_ad2',$cont_ad2);
        //获得用户的订单数
        $goodsOrdersCount=GoodsOrdersModel::myOrderCount($this->fansInfo['id']);
        $this->assign('goodsOrdersCount',$goodsOrdersCount);
        //获得该用户的购物车商品数
        $goodsCart=GoodsCartModel::getCount($this->fansInfo['id']);
        $this->assign('goodsCart',$goodsCart);
        return $this->fetch();
    }

    //常规栏目
    public function type()
    {
        $id=input('param.id');
        $contType=ContTypeModel::get($id);

        if(empty($contType))
        {
            $this->error('参数错误!');
        }

        //判断是不是跳转栏目 是直接跳走
        if(!empty($contType['ulr']))
        {
            $this->redirect($contType['ulr']);
        }

        $this->assign('contType',$contType);

        //判断是否是单页和列表 1：列表  2：单页
        if($contType['type']==2)
        {
            $contText=ContTextModel::getOne($id);
            $this->assign('contText',$contText);
            return $this->fetch('single');
        }
        elseif($contType['type']==1)
        {
            $contText=ContTextModel::getAll($id);
            $this->assign('contText',$contText);
            return $this->fetch();
        }

    }

    //文章页
    public function article()
    {
        $id=input('param.id');
        $contText=ContTextModel::get($id);
        $this->assign('contText',$contText);
        return $this->fetch();
    }

    //支付测试
    //测试支付的回调
    function testNotify()
    {
        $order_id='xq151085236781604532';   //测试的订单号
        $openid='opBq_v4QZaYM82e0yURHJsfdbKOM';           //测试的个人微信号
        $url=config('domain')."/index.php/notify/recharge.html";  //测试的回调地址
        $str='<xml>
			  <appid><![CDATA[wx2421b1c4370ec43b]]></appid>
			  <attach><![CDATA[支付测试]]></attach>
			  <bank_type><![CDATA[CFT]]></bank_type>
			  <fee_type><![CDATA[CNY]]></fee_type>
			  <is_subscribe><![CDATA[Y]]></is_subscribe>
			  <mch_id><![CDATA[10000100]]></mch_id>
			  <nonce_str><![CDATA[5d2b6c2a8db53831f7eda20af46e531c]]></nonce_str>
			  <openid><![CDATA['.$openid.']]></openid>
			  <out_trade_no><![CDATA['.$order_id.']]></out_trade_no>
			  <result_code><![CDATA[SUCCESS]]></result_code> 
			  <return_code><![CDATA[SUCCESS]]></return_code>
			  <sign><![CDATA[B552ED6B279343CB493C5DD0D78AB241]]></sign>
			  <sub_mch_id><![CDATA[10000100]]></sub_mch_id>
			  <time_end><![CDATA[20140903131540]]></time_end>
			  <total_fee>100</total_fee>
			  <trade_type><![CDATA[JSAPI]]></trade_type>
			  <transaction_id><![CDATA[1004400740201409030005092168]]></transaction_id>
			</xml>';

        echo $this->testXMLPost($url,$str);

    }

    public function testXMLPost($url,$data)
    {
        $ch = curl_init();
        $header[] = "Content-type: text/xml";//定义content-type为xml
        curl_setopt($ch, CURLOPT_URL, $url); //定义表单提交地址
        curl_setopt($ch, CURLOPT_POST, 1);   //定义提交类型 1：POST ；0：GET
        curl_setopt($ch, CURLOPT_HEADER, 1); //定义是否显示状态头 1：显示 ； 0：不显示
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//定义请求类型
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);//定义是否直接输出返回流
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //定义提交的数据，这里是XML文件

        $tmpInfo = curl_exec($ch);
        if (curl_errno($ch)) {
            return $tmpInfo;
        }
        curl_close($ch);
        return $tmpInfo;
    }

}