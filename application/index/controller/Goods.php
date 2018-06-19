<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 商城
 */

namespace app\index\controller;


use app\index\event\Integral;
use app\index\model\CollectiongoodsModel;
use app\index\model\ContAdModel;
use app\index\model\CouponLogModel;
use app\index\model\CouponModel;
use app\index\model\DoctorModel;
use app\index\model\EvaluateModel;
use app\index\model\FotoplaceModel;
use app\index\model\GoodsImgsModel;
use app\index\model\GoodsModel;
use app\index\model\GoodsOrdersGoodsModel;
use app\index\model\GoodsOrdersModel;
use app\index\model\GoodsOrdersReturnModel;
use app\index\model\GoodsTypeModel;
use app\index\model\GoodsCartModel;
use app\index\model\MailAreaModel;
use app\index\model\PurchaseNotesModel;
use ApiOauth\ApiOauth;
use app\index\model\SearchModel;
use app\index\model\UserInfoModel;
use kdniao\Kdniao;
use think\Config;
use app\index\event\Wxpay;
use think\Db;
use think\Log;

class Goods extends Wap
{
    protected $areaArr=array(
        "110000"=>"北京市",
        "120000"=>"天津市",
        "130000"=>"河北省",
        "140000"=>"山西省",
        "150000"=>"内蒙古",
        "210000"=>"辽宁省",
        "220000"=>"吉林省",
        "230000"=>"黑龙江省",
        "310000"=>"上海市",
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
        "450000"=>"广西壮族",
        "460000"=>"海南省",
        "500000"=>"重庆",
        "510000"=>"四川省",
        "520000"=>"贵州省",
        "530000"=>"云南省",
        "540000"=>"西藏",
        "610000"=>"陕西省",
        "620000"=>"甘肃省",
        "630000"=>"青海省",
        "640000"=>"宁夏",
        "650000"=>"新疆",
        "710000"=>"台湾省",
        "810000"=>"香港",
        "820000"=>"澳门",
        "990000"=>"海外"
    );

    protected function _initialize()
    {
        //获得授权
        $this->getUserInfo();
        $this->assign('fansInfo',$this->fansInfo);
    }

    public function index()
    {
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

        $clist = Db::name('collectiongoods')->where(['cg_uid'=>$this->fansInfo['id']])->field('cg_id,cg_gid')->select();
        $clistarr = array_column($clist,'cg_gid');

        $this->assign('clist',$clistarr);

        return $this->fetch();
    }

    public function goodsList()
    {
        $tid=input('param.id');
        $search = input('get.gdname/s');
        $goodType=GoodsTypeModel::get($tid);
        $this->assign('goodType',$goodType);
        $searcharr = array();
        if($tid){
            $searcharr['tid'] = $tid;
        }
        if($search){
            $this->checkLogin();
            $searcharr['gdname'] = array('like','%'.$search.'%');
            $this->savesearch($search);
            $this->assign('goodType',array('typename'=>'搜索'));
        }
        if(!$tid && !$search){
            $this->assign('goodType',array('typename'=>'全部商品'));
        }
        //默认列表数据
        $goods=GoodsModel::getTypeGoods($searcharr);
        //图标
        $jg_icon=0;
        //按价格排序
        $jg=input('param.jg',0);
        if(!empty($jg))
        {
            //1 降序
            if($jg==1)
            {
                $jg=2;
                $goods=GoodsModel::getTypeGoods($searcharr,15,1);
                $jg_icon=1;
            }
            //2 升序
            elseif($jg==2)
            {
                $jg=1;
                $goods=GoodsModel::getTypeGoods($searcharr,15,2);
                $jg_icon=2;
            }

        }
        else
        {
            //默认降序
            $jg=1;
        }
        //按销量排序
        //图标
        $xl_icon=0;
        $xl=input('param.xl',0);
        if(!empty($xl))
        {
            //1 降序
            if($xl==1)
            {
                $xl=2;
                $goods=GoodsModel::getTypeGoods($searcharr,15,0,1);
                $xl_icon=2;
            }
            //2 升序
            elseif($xl==2)
            {
                $xl=1;
                $goods=GoodsModel::getTypeGoods($searcharr,15,0,2);
                $xl_icon=1;
            }

        }
        else
        {
            //默认降序
            $xl=1;
        }


        //按评价排序
        //图标
        $pj_icon=0;
        $pj=input('param.pj',0);
        if(!empty($pj))
        {
            //1 降序
            if($pj==1)
            {
                $pj=2;
                $goods=GoodsModel::getTypeGoods($searcharr,15,0,0,1);
                $pj_icon=2;
            }
            //2 升序
            elseif($pj==2)
            {
                $pj=1;
                $goods=GoodsModel::getTypeGoods($searcharr,15,0,0,2);
                $pj_icon=1;
            }

        }
        else
        {
            //默认降序
            $pj=1;
        }


        //按综合排序
        //图标
        $zh_icon=0;
        $zh=input('param.zh',0);
        if(!empty($zh))
        {
            //1 降序
            if($zh==1)
            {
                $zh=2;
                $goods=GoodsModel::getTypeGoods($searcharr,15,0,0,0,1);
                $zh_icon=2;
            }
            //2 升序
            elseif($zh==2)
            {
                $zh=1;
                $goods=GoodsModel::getTypeGoods($searcharr,15,0,0,0,2);
                $zh_icon=1;
            }

        }
        else
        {
            //默认降序
            $zh=1;
        }


        $this->assign('jg',$jg);
        $this->assign('jg_icon',$jg_icon);

        $this->assign('xl',$xl);
        $this->assign('xl_icon',$xl_icon);

        $this->assign('pj',$pj);
        $this->assign('pj_icon',$pj_icon);

        $this->assign('zh',$zh);
        $this->assign('zh_icon',$zh_icon);

        $this->assign('goods',$goods);
        $this->assign('count',count($goods));
        $this->assign('tid',$tid);

        $clist = Db::name('collectiongoods')->where(['cg_uid'=>$this->fansInfo['id']])->field('cg_id,cg_gid')->select();
        $clistarr = array_column($clist,'cg_gid');

        $this->assign('clist',$clistarr);
        return $this->fetch();
    }

    //退款
    public function refund()
    {

        $request=request();
        $order_id=input('param.order_id');
        $goods_order_goods=input('param.goods_order_goods');
        $return_type=input('param.returntype');
        $gid= intval(input('param.gid'));
        if(empty($order_id) || empty($goods_order_goods) || empty($return_type) || empty($gid)){
            $this->error('数据错误',url('my/mygoods'));
        }
        $goods_order_goodsinfo = Db::name('goods_orders_return')->where(['return_gog_id'=>$goods_order_goods])->find();
        if($goods_order_goodsinfo && $goods_order_goodsinfo['return_auditstate']!=2){ $this->error('无法重复申请'); }
        intval($return_type)==1?$return_type = 1:$return_type = 2;
        $orderinfo = GoodsOrdersModel::where(['order_id'=>$order_id])->find();
        if($orderinfo['user_id']!=$this->fansInfo['id']){
            $this->error('无权操作非自己订单',url('my/myreturn'));
        }elseif(($return_type == 1 && $orderinfo['status']!=1) || ($return_type == 2 && ( $orderinfo['status']!=2&&$orderinfo['status']!=3))){
            $this->error('状态错误',url('my/mygoods'));
        }
        if($request->method()=='POST')
        {
            $goods = GoodsOrdersModel::where(['order_id'=>$order_id])->find();
            $goods_ids_arr = explode(',',$goods['goods_ids']);
            //所有商品总价
            $sumprice = GoodsModel::where('id','in',$goods_ids_arr)->sum('price');
            //商品单价

            $goodsinfo = GoodsModel::where('id',$gid)->find();
            $goodsprice = $goodsinfo['price'];
            //除去本商品的价格
            $newsumprice = $sumprice-$goodsprice;
            //获取优惠券使用金额
            $coupon_log_id = $goods['coupon_id'];
            $coupon_log_info = CouponLogModel::where('id',$coupon_log_id)->find();
            $coupon_info = CouponModel::where('id',$coupon_log_info['cid'])->find();

            if($coupon_info['use_price']<=$newsumprice){//剩下的商品总价仍然符合优惠券，全额返还商品金额
                $return_money = floatval($goodsprice);
            }else{//剩下的商品总价不足以优惠券价格，废除优惠券实际负的价格-剩下的商品总价
                $return_money = $goods['price']-$newsumprice;
            }
            $reason=input('param.reason');
            $text=input('param.text');
            $val['return_stype']=intval(input('param.return_stype'));
            $val['return_go_id']=$goods['id'];
            $val['return_gog_id']=$goods_order_goods;
            $val['return_reasontype']=$reason;
            $val['return_uid']=$this->fansInfo['id'];
            $val['return_content']=$text;
            $val['return_addtime']=time();
            $val['return_order_id']=$order_id;
            $val['return_type']=$return_type;
            $val['return_money']=$return_money;
            $val['return_code']=date('YmdHis').rand(1000,9999);
            $val['return_auditstate']=0;
            $images=input('post.images/a');
            if($images){
                $imgarr = array();
                foreach ($images as $k=>$v)
                {
                    $return=$this->saveBase64Images($v);

                    if($return['code']==0)
                    {
                        $imgarr[] = $return['url'];
                    }
                }
                $val['return_img'] = json_encode($imgarr);
            }
            $GoodsOrdersReturnModel = new GoodsOrdersReturnModel();
            $return_id = input('param.return_id');
            if($return_id){
                if($GoodsOrdersReturnModel->allowField(true)->save($val,['return_id'=>$return_id])){
                    if($return_type==1){
                        GoodsOrdersGoodsModel::where('id',$goods_order_goods)->update(['status'=>2]);
                    }else{
                        GoodsOrdersGoodsModel::where('id',$goods_order_goods)->update(['status'=>4]);
                    }
                    $this->success('提交申请成功',url('index'));
                }else{
                    $this->error('提交申请失败',url('index'));
                }

            }else{
                if($GoodsOrdersReturnModel->allowField(true)->save($val)){
                    if($return_type==1){
                        GoodsOrdersGoodsModel::where('id',$goods_order_goods)->update(['status'=>2]);
                    }else{
                        GoodsOrdersGoodsModel::where('id',$goods_order_goods)->update(['status'=>4]);
                    }
                    $this->success('提交申请成功',url('index'));
                }else{
                    $this->error('提交申请失败',url('index'));
                }
            }

        }
        else
        {
            $return_id = input('param.return_id');
            $this->assign('return_id',$return_id);
            $this->assign('order_id',$order_id);
            $this->assign('goods_order_goods',$goods_order_goods);
            $this->assign('return_type',$return_type);
            $this->assign('gid',$gid);
            $goodsinfo = Db::name('goods')->find($gid);
            $gog = Db::name('goods_orders_goods')->find($goods_order_goods);
            $this->assign('goodsinfo',$goodsinfo);
            $this->assign('gog',$gog);
            return $this->fetch();
        }


    }

    /**
     * 保存64位编码图片
     */

    public function saveBase64Images($base64_image_content){

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

    public function detail()
    {
        $id=input('param.id');
        $goods=GoodsModel::get($id);
        if($goods['number']<=0)
        {
            $this->error('库存不足，无法购买');
        }
        //记录足记
        $FotoplaceModel=new FotoplaceModel();
        $FotoplaceModel->save(['uid'=>$this->fansInfo['id'],'aid'=>$id,'type'=>6]);
        //读取该商品的展示图列表
        $goodsImgs=GoodsImgsModel::getAll($goods['id']);
        $this->assign('goodsImgs',$goodsImgs);
        //读取购物车是否有没有该买的商品
        $goodsCartCount=GoodsCartModel::getcartCount($this->fansInfo['id']);
        $this->assign('goodsCartCount',$goodsCartCount);
        $this->assign('goods',$goods);
        //读取商品的规格
        $this->assign('gg',json_decode($goods['gg'],true));

        //读取购买须知
        //$this->assign('purchaseNotes',$this->getPurchaseNotes());


        //读取推荐的专家
        //if(!empty($goods['doc_ids']))
        //{
        //$docArr=DoctorModel::getIds($goods['doc_ids']);
        //$this->assign('docArr',$docArr);
        //}

        //推荐商品
        $tjGoods=GoodsModel::getZq($goods['tid']);
        $this->assign('tjGoods',$tjGoods);
        $this->assign('count',count($tjGoods));
        //读取可用的优惠券
        $coupon=CouponModel::getAll();
        $this->assign('coupon',$coupon);
        //计算邮费价格
        //if(!empty($this->fansInfo['area']))
        //{
        //$areaArray=explode(' ',$this->fansInfo['area']);
        //$area=$areaArray['0'];
        //foreach ($this->areaArr as $k=>$v)
        //{

        //if($v==$area)
        //{
        //$area=$k;
        //break;
        //}
        //}

        //$MailAreaModel=new MailAreaModel;
        //$MailArea=$MailAreaModel->where(['area'=>['like','%'.$area.'%']])->find();
        //if(!empty($MailArea))
        //{
        //$mailPrice=$MailArea['price'];
        //$this->assign('mailPrice',$mailPrice);
        //}

        //}
        //查询是否收藏本商品
        $collect = Db::name('collectiongoods')->where(['cg_gid'=>$id,'cg_uid'=>$this->fansInfo['id']])->find();

        $this->assign('collect',$collect);
        $this->assign('id',$id);

        //获得评价内容
        $evaluate=EvaluateModel::getAll($id,5);
        foreach ($evaluate as $key=>$value)
        {
            if(!empty($value['pics']))
            {
                $evaluate[$key]['pics']=json_decode($value['pics']);
            }
            else
            {
                $evaluate[$key]['pics']='';
            }
        }
        $this->assign('evaluate',$evaluate);
        //评价总数
        $count=EvaluateModel::getCount($id);
        $this->assign('count',$count);
        //好评数
        $hp=EvaluateModel::getHpCount($id);
        $this->assign('hp',$hp);
        return $this->fetch();
    }

    public function detail2()
    {
        $id=input('param.id');
        $goods=GoodsModel::get($id);
        if($goods['number']<=0)
        {
            $this->error('库存不足，无法购买');
        }
        //读取购物车是否有没有该买的商品
        $goodsCartCount=GoodsCartModel::getCount($this->fansInfo['id']);
        $this->assign('goodsCartCount',$goodsCartCount);
        $this->assign('goods',$goods);
        if($this->request->isAjax()){
            return json($goods);
        }else{
            return $this->fetch();
        }
    }





    //添加到购物车
    public function addCart()
    {
        //检查是否登录
        $this->checkLoginAjax();


        $data['goods_id']=input('post.goods_id');
        $data['nums']=input('post.nums');
        $data['gg']=input('post.gg');          //商品规格
        $data['uid']=$this->fansInfo['id'];

        //$result =$this->validate($data,'ValidateClass.AddCart');
        //if(true !== $result)
        //{
        // 验证失败 输出错误信息
        //return ['code'=>0,'msg'=>$this->error($result)];
        //}

        $goodsCart=new GoodsCartModel();
        //如果购物车存在该商品直接修改这次加入购物车的数量

        $getOne=$goodsCart->getOne($data['uid'],$data['goods_id'],$data['gg']);

        if(empty($getOne))
        {
            if($goodsCart->save($data))
            {
                return ['code'=>1];
            }
            else
            {
                return ['code'=>0,'msg'=>'添加失败'];
            }
        }
        else
        {
            $data['nums'] = $data['nums'] + $getOne['nums'];
            if($goodsCart->allowField(true)->save(['nums'=>$data['nums']],['id'=>$getOne['id']]))
            {
                return ['code'=>1];
            }
            else
            {
                return ['code'=>0,'msg'=>'添加失败'];
            }
        }


    }

    //删除购物车里面的产品
    public function cartDel(){
        //检查是否登录
        $this->checkLoginAjax();

        $data=input('post.');
        $id = $data['id'];
        if(is_array($id)){
            $ids = implode(',',$id);
        }else{
            $ids = $id;
        }
        $goodsCart=new GoodsCartModel;
        if($goodsCart->where('id','in',$ids)->delete()){
            return ['code'=>1];
        }else{
            return ['code'=>0,'msg'=>'删除失败'];
        }
    }

    public function deloldcart(){
        //检查是否登录
        $this->checkLoginAjax();
        $data=input('post.');
        $id = $data['id'];
        $idarr = explode(',',$id);
        foreach($idarr as $k=>$v){
            $idarr[$k] = intval($v);
        }
        $ids = implode(',',$idarr);
        $goodsCart=new GoodsCartModel;
        if($goodsCart->where('id','in',$ids)->where(['uid'=>$this->fansInfo['id']])->delete()){
            return ['code'=>1];
        }else{
            return ['code'=>0,'msg'=>'删除失败'];
        }
    }

    //购物车首页
    public function cart()
    {
        //检查是否登录
        $this->checkLogin();

        //读取该用户的购物车内容
        $goodsCart=GoodsCartModel::getAll($this->fansInfo['id']);
        //计算用户选择商品规格的价格
        foreach ($goodsCart as $key=>$value)
        {
            if($value['gg']!='')
            {
                $goods=GoodsModel::get($value['goods_id']);
                $goodsGg=json_decode($goods['gg'],true);
                $goodsCart[$key]['price']=sprintf('%.2f', $goodsGg[$value['gg']]['ggprice']);
                $goodsCart[$key]['ggName']=$goodsGg[$value['gg']]['gg'];
            }
            else
            {
                $goods=GoodsModel::get($value['goods_id']);
                $goodsCart[$key]['price']=$goods['price'];
                $goodsCart[$key]['ggName']="";
            }

        }
        $this->assign('goodsCart',$goodsCart);


        return $this->fetch();
    }

    //修改购物车里面的商品数量
    public function alterNums()
    {
        $data['id']=input('post.id');
        $data['nums']=input('post.nums');
        $goodsCart=new GoodsCartModel;
        if(!$goodsCart->save(['nums'=>$data['nums']],['id'=>$data['id']]))
        {
            return ['code'=>0,'msg'=>'更新数量失败'];
        }
    }


    //订单购买页
    public function buy()
    {

        //检查是否登录
        $this->checkLogin2();

        $data=input('post.');
        if(!$data){
            redirect(url('goods/cart'));
        }
        if(isset($data['order_data_id'])){
            $order_data_id = $data['order_data_id'];
            unset($data['order_data_id']);
            $goodsordersinfo = GoodsOrdersModel::find($order_data_id);
            $this->assign('goodsordersinfo',$goodsordersinfo);
        }
        $gg=$data['gg'];      //商品规格
        $num=$data['nums'];   //购买的数量
        $num_sum = array_sum($num);
        $this->assign('num_sum',$num_sum);
        //立即购买 规格不是数组转成数组
        if(!is_array($gg))
        {
            $gg=(array)$gg;
        }
//        积分
//      $integral=0;
        $express=0;
        $totalPrice=0;
        $coupon=0;
        //计算用户选择商品规格的价格
        foreach ($data['id'] as $key=>$value)
        {
            $goodsOne=GoodsModel::getOne($value);
            $goods[$key]=$goodsOne;
            if($gg[$key]!='')
            {
                $goodsGg=json_decode($goodsOne['gg'],true);
                $goods[$key]['price']=sprintf('%.2f', $goodsGg[$gg[$key]]['ggprice']);
                $goods[$key]['ggName']=$goodsGg[$gg[$key]]['gg'];
                $goods[$key]['gg']=$gg[$key];
                $goods[$key]['nums']=$num[$key];
            }
            else
            {
                $goods[$key]['ggName']="";
                $goods[$key]['gg']=$gg[$key];
                $goods[$key]['nums']=$num[$key];
            }
            //积分抵现数
//          $integral=$integral+$goodsOne['integral'];
            //计算快递费
            $express=$express+$goodsOne['postage'];
            //计算商品的价格
            $totalPrice=$totalPrice+$goods[$key]['nums']*$goods[$key]['price'];

        }
        $express=intval($express/count($data['id']));
        $this->assign('express',$express);
        $this->assign('goods',$goods);
//        积分
//      $this->assign('integral',$integral);
//        积分
//      $integralPrice=intval($integral/config('getIntegral'));
//      $this->assign('integralPrice',$integralPrice);

        //获得当前用户的优惠券
        $couponLogModel=CouponLogModel::getUser($this->fansInfo['id']);
        //判断优惠券是否过期 是否满足使用条件
        $allCoupon = array();
        foreach ($couponLogModel as $key=>$v)
        {
            $CouponModel=CouponModel::getOne($v['cid']);

            if(!empty($CouponModel) && $CouponModel['use_price']<=$totalPrice)
            {
                $CouponModel['cl_id'] = $v['id'];
                $allCoupon[] =  $CouponModel;
                $this->assign('coupon',$CouponModel);
                $coupon=$CouponModel['cprice'];
//              break;
            }
        }
//      $this->assign('totalPrice',$totalPrice+$express-$integralPrice-$coupon);
        $this->assign('totalPrice',$totalPrice+$express-$coupon);
        $allCoupon = array_reverse($allCoupon,true);
        $this->assign('allCoupon',$allCoupon);

        //微信jdk 验证
        $apiOauth=new apiOauth();
        $ticket = $apiOauth->getJsApiTicket(Config::get('Appid'),Config::get('Appsecret'));
        $this->timeStamp = time();
        $this->nonceStr  = rand(100000,999999);
        $url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        //获得jsdk签名
        $this->signature = $apiOauth->getSignature($this->nonceStr,$ticket,$this->timeStamp,$url);

        $this->assign('timeStamp',$this->timeStamp);
        $this->assign('nonceStr',$this->nonceStr);
        $this->assign('signature',$this->signature);


        //查询收货地址
        $shipaddress = Db::name('shipaddress')
            ->where(['sd_uid'=>$this->fansInfo['id'],'sd_state'=>1])
            ->order('sd_default desc,sd_id desc')
            ->field('sd_username,sd_mobile,sd_area,sd_address,sd_zipcode')
            ->find();
        $shipaddresslist = Db::name('shipaddress')
            ->where(['sd_uid'=>$this->fansInfo['id'],'sd_state'=>1])
            ->order('sd_default desc,sd_id desc')
            ->field('sd_username,sd_mobile,sd_area,sd_address,sd_zipcode')
            ->select();
        // echo Db::getLastSql();die;
        //$this->assign('balance',$this->fansInfo['balance']);  //账号余额
        $this->assign('shipaddress',$shipaddress);  //账号余额
        $this->assign('shipaddresslist',$shipaddresslist);  //账号余额
        return $this->fetch();
    }

    //订单提交
    public function addOrder()
    {
        $data=input('param.');

        if(empty($data['address']))
        {
            return ['code'=>0,'msg'=>'请选择邮寄地址'];
        }

        $totalPrice=0;
        $price=[];
        $gg=$data['gg'];
        //        积分
//      $integral=0;
        $express=0;
        $coupon=0;
        foreach ($data['goods_id'] as $key=>$v)
        {
            $goods=GoodsModel::getOne($v);

            if(empty($goods))
            {
                return ['code'=>0,'msg'=>'有商品下架不能购买！请刷新页面'];
            }
            if($goods['number']<=0)
            {
                $i=$key+1;
                return ['code'=>0,'msg'=>'第'.$i.'个商品库存不足,不能购买'];
            }


            //计算当前商品规格的价格
            if($gg[$key]!='')
            {
                $ggArr=json_decode($goods['gg'],true);
                $goods['price']=$ggArr[$gg[$key]]['ggprice'];
                $ggName[$key]=$ggArr[$gg[$key]]['gg'];
            }
            else
            {
                $ggName[$key]="";
            }

            $totalPrice=$totalPrice+$goods['price']*$data['nums'][$key];
            $price[$key]=$goods['price'];

            //删除购物车的商品
            GoodsCartModel::where(['uid'=>$this->fansInfo['id'],'goods_id'=>$v,'gg'=>$gg[$key]])->delete();

            //积分抵现数
//          $integral=$integral+$goods['integral'];
            //计算快递费
            $express=$express+$goods['postage'];
        }


        //邮费
        $express=intval($express/count($data['goods_id']));
        //积分抵现
//      $integralPrice=intval($integral/config('getIntegral'));
        $totalPrice=$totalPrice+$express;
//      $totalPrice=$totalPrice-$integralPrice;
//
//
        //如果有优惠券
        $checkCoupon = $data['checkCoupon'];
        if($checkCoupon){
            $checklog = CouponLogModel::find($checkCoupon);
            $checkcm = CouponModel::find($checklog['cid']);
            $totalPrice -= $checkcm['cprice'];
            $data['coupon_id'] = $checkCoupon;
        }else{
            $data['coupon_id'] = 0;
        }
        //获得当前用户的优惠券
        // $couponLogModel=CouponLogModel::getUser($this->fansInfo['id']);
        // //判断优惠券是否过期 是否满足使用条件
        // foreach ($couponLogModel as $key=>$v)
        // {
        //  $CouponModel=CouponModel::getOne($v['cid']);
        //  if(!empty($CouponModel) && $CouponModel['use_price']<=$totalPrice)
        //  {
        //      //减优惠券的价格
        //      $totalPrice=$totalPrice-$CouponModel['cprice'];
        //      $coupon=$CouponModel['cprice'];
        //      break;
        //  }
        // }

        $wxPay=new Wxpay;
        $order_id=$wxPay->orderId('xq');

        $data['order_id']=$order_id;
        $data['price']=$totalPrice;
        $data['user_id']=$this->fansInfo['id'];


        $goodsOrders=new GoodsOrdersModel();
        //判断是否有重复的订单
        if(is_array($data['goods_id']))
        {
            $d=implode(',',$data['goods_id']);
            $data['goods_ids']=$d;
        }
        else
        {
            $d=$data['goods_id'];//
            $data['goods_ids']=$d;
        }
        //记录积分抵现的钱数
//      $data['integral_money']=$integralPrice;
        //支付的邮费
        $data['postage']=$express;
        //优惠券的金额
        $data['coupon']=$coupon;

        $goodsOrdersOne=$goodsOrders->where(['user_id'=>$this->fansInfo['id'],'goods_ids'=>$d,'status'=>0,'cancel'=>0])->find();

        if(!empty($goodsOrdersOne))
        {
            $order_id = $goodsOrdersOne['order_id'];
            unset($data['order_id']);
            if($goodsOrders->allowField(true)->save($data,['id'=>$goodsOrdersOne['id']]))
            {

                foreach($data['goods_id'] as $key=>$v)
                {
                    $goodsOrdersGoods=new GoodsOrdersGoodsModel();
                    $goodsOrdersGoods->where(['oid'=>$goodsOrdersOne['id']])->update(['nums'=>$data['nums'][$key],'price'=>$price[$key],'ggName'=>$ggName[$key],'gg'=>$gg[$key]]);
                }
            }

        }
        else
        {
            if($goodsOrders->allowField(true)->save($data))
            {
                $oid=$goodsOrders->id;
                foreach($data['goods_id'] as $key=>$v)
                {
                    $goodsOrdersGoods=new GoodsOrdersGoodsModel;
                    $goodsOrdersGoods->save(['oid'=>$oid,'gid'=>$v,'nums'=>$data['nums'][$key],'price'=>$price[$key],'ggName'=>$ggName[$key],'gg'=>$gg[$key]]);
                }
            }
        }

        //生成微信支付的参数
        $body='商品支付';
        $money=number_format($totalPrice,2,".","");
        $notify_url= Config::get('domain')."/index.php/notify/goods.html";
        $return=$wxPay->pay(Config::get('Appid'),Config::get('apikey'),$this->fansInfo['wecha_id'],Config::get('mchid'),$body,$order_id,$money,$notify_url);
        if(!empty($return))
        {
            return array('code'=>1,'data'=>$return,'order_id'=>$order_id);
        }

    }

    //余额支付
    public function balanceBuy()
    {
        $order_id=input('param.order_id');

        if(empty($order_id))
        {
            return ['code'=>0,'msg'=>'参数错误，请刷新页面！'];
        }

        $goodsOrders=GoodsOrdersModel::where(['order_id'=>$order_id])->find();
        if(empty($goodsOrders))
        {
            return ['code'=>0,'msg'=>'订单不存在，请刷新页面！'];
        }

        if($this->fansInfo['balance']>=$goodsOrders['price'])
        {

            //查找该订单的商品
            $GoodsOrdersGoods=GoodsOrdersGoodsModel::field('gid,nums')->where(['oid'=>$goodsOrders['id']])->select();
            if(empty($GoodsOrdersGoods))
            {
                return ['code'=>0,'msg'=>'该订单不存在的商品，请刷新页面！'];
            }
            $goodsId=array();
            foreach ($GoodsOrdersGoods as $key=>$v)
            {
                $goods=GoodsModel::getOne($v['gid']);

                if(empty($goods))
                {
                    return ['code'=>0,'msg'=>'有商品下架不能购买！请刷新页面'];
                }

                if($goods['number']<=$v['nums'])
                {
                    $i=$key+1;
                    return ['code'=>0,'msg'=>'第'.$i.'个商品库存不足,不能购买'];
                }

                $goodsId[$key]['id']=$goods['id'];
                $goodsId[$key]['nums']=$v['nums'];
            }

            //如果是积分抵现的减对应的积分
            if($goodsOrders['integral_money']>0)
            {

                if(config('getIntegral')>0)
                {
                    //计算减少的积分数
                    $integralNums=floor($goodsOrders['integral_money']*config('getIntegral'));   //啥去取整
                    $event = new Integral();
                    if($integralNums>0)
                    {
                        $total=$event->reduce($goodsOrders['user_id'],$integralNums,'购买商品抵现');
                        $smsg='消费减少'.$integralNums."积分，积分尚余：".$total;
                    }
                }

            }

            //更新支付状态
            if(Db::name('goods_orders')->where('id',$goodsOrders['id'])->update(array('status'=>1,'update_time'=>time())))
            {

                //减余额
                UserInfoModel::where(['id'=>$this->fansInfo['id']])->setDec('balance',$goodsOrders['price']);
                //加消费总额
                UserInfoModel::where(['id'=>$this->fansInfo['id']])->setInc('consumption',$goodsOrders['price']);

                //减库存
                foreach($goodsId as $key=>$v)
                {
                    GoodsModel::where(['id'=>$v['id']])->setDec('number',$v['nums']);
                }

                //提示成功
                return ['code'=>1,'msg'=>'支付成功'];
            }


        }
        else
        {
            return ['code'=>2,'msg'=>'账号余额不足！'];
        }
    }

    //读取购买须知
    protected function getPurchaseNotes()
    {
        return PurchaseNotesModel::get(1);
    }


    //物流信息查询
    public function express()
    {
        $oid=input('param.oid');
        $goodsOrders=GoodsOrdersModel::where(['order_id'=>$oid])->find();

        $kdniao=new Kdniao(config('EBusinessID'),config('AppKey'));
        $return=$kdniao->orderTracesSubByJson('text'.time(),$goodsOrders['shipper_code'],$goodsOrders['express_code']);
        $info=json_decode($return);

        $reStr='快件揽收中...';
        if($info->Success==true)
        {

            if(empty($info->Traces))
            {
                $reStr=$info->Reason;
            }

            $reStr=array_reverse(json_decode( json_encode($info->Traces),true));
        }
        else
        {
            $reStr='快件揽收中...';
        }


        $this->assign('reStr',$reStr);
        return $this->fetch();

    }

    //商品评价
    public function evaluate()
    {

        $request=request();
        if($request->method()=='POST')
        {

            $data=input('post.');
            $evaluate=new EvaluateModel();
            $data['uid']=$this->fansInfo['id'];

            $picArr=array();
            if(isset($data['images']) && !empty($data['images']))
            {

                foreach ($data['images'] as $k=>$v)
                {
                    $return=$this->saveBase64Image($v);
                    $picArr[]=$return['url'];
                }
            }



            if(!empty($picArr))
            {
                $data['pics']=json_encode($picArr);
            }

            $data['create_time']=time();

            if($evaluate->allowField(true)->save($data))
            {

                //更新该商品已经评价
                GoodsOrdersGoodsModel::where(['oid'=>$data['oid'],'gid'=>$data['goods_id']])->update(['status'=>1]);
                //更新商品的评价数
                GoodsModel::where(['id'=>$data['goods_id']])->setInc('pj');
                return ['code'=>'1','id'=>$data['goods_id']];
            }

        }
        else
        {
            $goods_id=input('param.goods_id');
            $oid=input('param.oid');
            $this->assign('oid',$oid);

            $goods=Db::name('goods')->find($goods_id);
            $this->assign('goods',$goods);
            return $this->fetch();
        }

    }

    //商品评价
    public function comment()
    {
        $id=input('param.id');
        $page=input('param.page');
        $goods=GoodsModel::get($id);
        $this->assign('goods',$goods);

        //获得评价内容
        $evaluate=EvaluateModel::getAll($id,$page);
//        echo EvaluateModel::getLastSql();
        foreach ($evaluate as $key=>$value)
        {
            if(!empty($value['pics']))
            {
                $evaluate[$key]['pics']=json_decode($value['pics']);
            }
            else
            {
                $evaluate[$key]['pics']='';
            }
        }

        //评价总数
        $count=EvaluateModel::getCount($id);
        $this->assign('count',$count);
        //差评数
        $cp=EvaluateModel::getCpCount($id);
        $this->assign('cp',$cp);
        //中评数
        $zp=EvaluateModel::getZpCount($id);
        $this->assign('zp',$zp);
        //好评数
        $hp=EvaluateModel::getHpCount($id);
        $this->assign('hp',$hp);

        $this->assign('evaluate',$evaluate);
        return $this->fetch();
    }

    public function addAddress()
    {
        $address=input('param.address');
        //计算邮费价格

        //保存用户选择的地址
        Db::name('user_info')->where(['id'=>$this->fansInfo['id']])->update(['area2'=>$address]);
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
//                $image = \think\Image::open('.'.$data['url']);
//                // 按照原图的比例生成一个最大为300*300的缩略图并保存为thumb.png
//                $image->thumb(300,300,\think\Image::THUMB_CENTER)->save('.'.$image_path.'m_'.$image_name);
//                $data['url']=$image_path.'m_'.$image_name;
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
    //保存搜索记录
    private function savesearch($search){
        $uid = $this->fansInfo['id'];
        $data['so_name'] = $search;
        $data['so_type'] = 1;
        $data['so_uid'] = $uid;
        $Search = new SearchModel();
        $info = Db::name('search')->where($data)->find();
        if(!$info){
            $Search->allowField(true)->save($data);
        }
    }

    public function collectiongoods()
    {
        //检查是否登录
        $this->checkLogin();
        $collectiongoods = new CollectiongoodsModel();
        $id=input('param.id');
        if(!$id){
            $this->error('数据错误');
        }
        $data=input('param.data');
        if($data==0){
            $datas['cg_gid'] = $id;
            $datas['cg_uid'] = $this->fansInfo['id'];
            $datas['cg_addtime'] = time();
            if($collectiongoods->allowField(true)->save($datas)){
                $this->success('收藏成功');
            }
        }else{
            $datas['cg_uid'] = $this->fansInfo['id'];
            $datas['cg_gid'] = $id;
            $collectiongoods->where($datas)->delete();
            $this->success('取消成功');
        }
    }
    public function search(){
        $list = Db::name('search')->where(['so_uid'=>$this->fansInfo['id'],'so_type'=>1])->order('so_id desc')->limit(0,5)->select();
        $this->assign('list',$list);
        return $this->fetch();
    }
    public function searchdel(){
        $val = Db::name('search')->where(['so_uid'=>$this->fansInfo['id']])->delete();
        if($val){
            $this->success('success');
        }
    }
    public function ajaxexpress()
    {
        $oid=input('param.oid');
        $goodsOrders=GoodsOrdersModel::where(['order_id'=>$oid,'user_id'=>$this->fansInfo['id']])->find();

        $kdniao=new Kdniao(config('EBusinessID'),config('AppKey'));
        $return=$kdniao->orderTracesSubByJson('text'.time(),$goodsOrders['shipper_code'],$goodsOrders['express_code']);
        $info=json_decode($return);

        $reStr='快件揽收中...';
        if($info->Success==true)
        {

            if(empty($info->Traces))
            {
                $reStr=$info->Reason;
            }

            $reStr=array_reverse(json_decode( json_encode($info->Traces),true));
        }
        else
        {
            $reStr='快件揽收中...';
        }

        return $reStr;

    }
    public function demo(){
        //微信jdk 验证
        $apiOauth=new apiOauth();
        $ticket = $apiOauth->getJsApiTicket(Config::get('Appid'),Config::get('Appsecret'));
        $this->timeStamp = time();
        $this->nonceStr  = rand(100000,999999);
        $url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        //获得jsdk签名
        $this->signature = $apiOauth->getSignature($this->nonceStr,$ticket,$this->timeStamp,$url);

        $this->assign('timeStamp',$this->timeStamp);
        $this->assign('nonceStr',$this->nonceStr);
        $this->assign('signature',$this->signature);

        return $this->fetch();
    }
}