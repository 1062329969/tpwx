<?php
/**
 * Created by PhpStorm.
 * User: thinkpad
 * Date: 2018/3/26
 * Time: 12:46
 */
namespace app\index\controller;
use think\Controller;
use think\Db;
use app\index\model\DoctorModel;
use app\index\model\HospitalModel;
use app\index\model\HospitalRegionModel;
use app\index\model\CasesModel;
use app\index\model\CasesRecordModel;

class Yard extends Wap{

    protected function _initialize()
    {
        //获得授权
        $this->getUserInfo();
        $this->assign('fansInfo',$this->fansInfo);
    }
    //各地院区列表
    public function indexs(){
        //返回全部医院列表
        $hospital = HospitalModel::getDropDown1();
        $this -> assign('hospital',$hospital);
        $this->assign('count',count($hospital));
        return $this->fetch("index");
        //nihao
    }
    //下拉获取信息
    public function getDrop(){
        $offsize = input('param.offsize', 0);
        //查询从 $offsize 开始的十条数据
        $hospital = HospitalModel::getDropDown($offsize);
        if (empty($hospital)) echo 0;
        //把数据放入一个字符串里
        $str = '';
        foreach($hospital as $k => $v){
            $str .= '<a href="'.url('detail',['id'=>$v['id']]).'">
            <li>
            <div class="cj_yard_banner">
            <img src="'.$v['pic'].'"/>
            </div>
            <div class="cj_yard_name">'.$v['hosname'].'</div>
            <div class="cj_position">
            <img src="'.HTML_STATIC.'/images/74.png"/>'.$v['address'].'</div>
            </li>
            </a>';
        }
        echo $str;
    }
    //
    public function detail(){
        $id = input('param.id',0);
        //$id = 29;
        if(!$id)$this -> error("数据传输错误");
        //根据id 查询单个医院的数据
        $hosData = HospitalModel::getOne($id);
        //根据医院id查询出所有的医生
        $docData = DoctorModel::getHosDoc($id);

        //根据id获取当前医院下的两个植发日记
        $cases= CasesModel::getHosCases($id);
        foreach($cases as $key=>$v)
        {
            //读取该案例的最后一次回复情况
            $casesRecordModel= CasesRecordModel::getOne($v['id']);
            $cases[$key]['pic2']=$casesRecordModel['pic'];
            $cases[$key]['vurl']=$casesRecordModel['vurl'];
            $cases[$key]['info']=$casesRecordModel['info'];
            $cases[$key]['caser'] = $casesRecordModel;
            $cases[$key]['id']=$casesRecordModel['cid'];
            $cases[$key]['res'] = $res  =  Db::name('praise_reg')->where(['aid'=>$v['id'],'type'=>6,])->find();
            //用户是否点赞过
            $cases[$key]['praise'] = Db::name('information')->where(['i_uid'=>$v['id'],'i_uids'=>$this->fansInfo['id'],'i_module'=>1,'i_type'=> 2])->find();
        }
        $hosArray = [];
        if($hosData['bus'])$hosArray[]['name'] = $hosData['bus'];
        if($hosData['train'])$hosArray[]['name'] = $hosData['train'];
        if($hosData['plane'])$hosArray[]['name'] = $hosData['plane'];
        if($hosData['metro'])$hosArray[]['name'] = $hosData['metro'];

        $this -> assign('hosArray',$hosArray);
        $this -> assign("hosData",$hosData);
        $this -> assign("docData",$docData);
        $this -> assign("cases",$cases);
        return $this->fetch();
    }
    //
    public function search(){
        return $this->fetch();
    }
    //
    public function getAjaxSearch(){
        $str = trim(input('post.data'));
        //echo $str;exit;
//        $str = "北京";
        $ajaxStr = '';
        if(!empty($str)){
//            //检索模糊查询返回的医院id
//            $dataArr = HospitalModel::getLikeSelect($str);
//            //转为一维数组
//            $arr = array();
//            foreach($dataArr as $k => $v){
//                $arr[] = $v['id'];
//            }
//            //根据医院返回医生
//            $HosDoc = DoctorModel::getYardDoctor($arr);
//            //根据医院id查询医生
//            if(!empty($HosDoc)){
//                $dataHList = db("doctor") -> alias("d")
//                    -> join('yh_hospital h','d.hid = h.id','LEFT')
//                    -> field("d.id,d.docname,h.hosname,d.pic")
//                    -> where([ 'd.id' => ['in',$HosDoc],'d.status' => 1 , 'd.del' => 0 ]) -> select();
//
//                //循环放入 字符串
//                $ajaxStr .= '<div class="add" id="address"><p>院部</p><ul>';
//                foreach ($dataHList as $k => $v){//HTML_STATIC
//                    $url = url('doctor/detail',['id'=>$v['id']]);
//                    $ajaxStr .= '<li><a onclick="window.location.href=\''.$url.'\'"><div><img src="'.$v['pic'].'"></div><div><p>'.$v['docname'].'</p><p>'.$v['hosname'].'</p></div></a></li>';
//                }
//                $ajaxStr .= '</ul></div>';
//            }
            //根据字符串查询 相关医院
            $HosData = HospitalModel::getHosSelect($str);
            if(!empty($HosData)){
                //连接院部html
                $ajaxStr .= '<div class="add" id="address"><p>院部</p><ul>';
                //循环放入 字符串
                foreach($HosData as $k => $v){
                    //生成url
                    $url = url('detail',['id'=>$v['id']]);
//                    $ajaxStr .= '<li><a onclick="window.location.href=\''.$url.'\'"><div><img src="'.$v['pic'].'"></div><div><p>'.$v['hosname'].'</p><p>'.$v['address'].'</p></div></a></li>';
                    $ajaxStr .= '<li class="li-hos"><a onclick="window.location.href=\''.$url.'\'" class="hos-a"><div class="hos-search"><p>'.$v['hosname'].'</p><p><img src="'.HTML_STATIC.'/images/74.png" class ="img-icon" >'.$v['address'].'</p></div></a></li>';
//                    <img src="{$Think.HTML_STATIC}/images/74.png"/>
                }
                $ajaxStr .= '</ul></div>';
            }
            //模糊查询返回医生 //这步可以省略  直接在下面模糊查询 字符串
            $DocLike = DoctorModel::getLikeDoctor($str);
            //根据字符串查询医生
            if(!empty($DocLike)){
                //echo "456"."<br />";
                $dataDList = db("doctor") -> alias("d")
                    -> join('yh_hospital h','d.hid = h.id','LEFT')
                    -> field("d.id,d.docname,h.hosname,d.pic")
                    -> where([ 'd.id' => ['in',$DocLike],'d.status' => 1 , 'd.del' => 0 ]) -> select();
                //print_r($dataDList);

                //循环放入 字符串
                $ajaxStr .= '<div class="add doc" id="doctor"><p>医生</p><ul>';
                foreach ($dataDList as $k => $v){//HTML_STATIC
                    $url = url('doctor/detail',['id'=>$v['id']]);
                    $ajaxStr .= '<li><a onclick="window.location.href=\''.$url.'\'"><div><img src="'.$v['pic'].'"></div><div><p>'.$v['docname'].'</p><p>'.$v['hosname'].'</p></div></a></li>';
                }
                $ajaxStr .= '</ul></div>';
                //$this -> assign('dataD',$dataDList);
            }
            if($ajaxStr){
                echo $ajaxStr;
            }else{
                echo 3;//返回字符串为空
            }
//            //合并数组 去除重复值
//            $data = array_merge($HosDoc , $DocLike);
//            $data = array_unique($data);
//            //查询与字符串有关联的 医生
//            $dataList = db("doctor") -> alias("d")
//                -> join('yh_hospital h','d.hid = h.id','LEFT')
//                -> field("d.id,d.docname,h.hosname")
//                -> where([ 'd.id' => ['in',$data],'d.status' => 1 , 'd.del' => 0 ]) -> select();
//            $this -> assign('data',$dataList);
        }else{
            echo 2;//接收字符串为空
        }
        exit;
        return $this->fetch();
    }
    //医生详情
//    public function doctor(){
//        return $this->fetch();
//    }
//    //
//    public function doctor1(){
//        //$id = input('param.id',0);
//        $id = 28;
//        if(!$id) $this -> error("数据传输错误");
//        $docList = db("doctor") -> alias("d")
//            -> join('yh_hospital h','d.hid = h.id','LEFT')
//            -> field("d.id,d.docname,h.hosname,d.pic,d.good_at,d.positional_titles,d.info,d.text,d.text2")
//            -> where([ 'd.id' => $id,'d.status' => 1 , 'd.del' => 0 ]) -> find();
//            $docList['good_at'] = explode("、",$docList['good_at']);
//        echo "<pre>";
//        print_r($docList);exit;
//        return $this->fetch();
//    }
    //地图页面
    public function map(){
        $map = input('param.map');
        $name = input('param.name');
//        echo $map;exit;
        $this -> assign("map",$map);
        $this -> assign("name",$name);
        return $this->fetch();
    }
//    //地图页面
//    public function map1(){
////        $map = input('param.map');
////        $name = input('param.name');
////        echo $map;exit;
//        $this -> assign("map",'39.937396,116.460927');
//        $this -> assign("name",'39.937396,116.460927');
//        return $this->fetch();
//    }
    public function index(){
        $xy = input('param.xy');
        //如果为空直接重定向
        if(empty($xy))$this->redirect('yard/indexs');
        //不为空分割字符串
        $arr = explode(",",$xy);
        //如果数组不是两个元素，直接重定向
        $count = count($arr);
        if($count != 2)$this->redirect('yard/indexs');
        //返回全部医院列表
        $hospital = HospitalModel::getDropDown1();
        foreach($hospital as $k => $v){
//            echo $v['coordinate']."<br />";
            if(!empty($v['coordinate'])){
                $err = explode(",",$v['coordinate']);
                $xys = $this -> getdistance($arr[0],$arr[1],$err[0],$err[1]);

                $xys=round($xys,2);
            }else{
                $xys = -1;
            }
            $hospital[$k]['xyMi'] = $xys;
        }
        $sort = array(
            'direction' => 'SORT_ASC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
            'field'     => 'xyMi',       //排序字段
        );
        $arrSort = array();
        foreach($hospital AS $uniqid => $row){
            foreach($row AS $key=>$value){
                $arrSort[$key][$uniqid] = $value;
            }
        }
        if($sort['direction']){
            array_multisort($arrSort[$sort['field']], constant($sort['direction']), $hospital);
        }
//        echo "<pre>";
//        print_r($hospital);
//        exit;
        $this -> assign('hospital',$hospital);
        $this->assign('count',count($hospital));
        return $this->fetch();
    }


    /**
     * 求两个已知经纬度之间的距离,单位为米
     *
     * @param lng1 $ ,lng2 经度
     * @param lat1 $ ,lat2 纬度
     * @return float 距离，单位米
     * @author www.Alixixi.com
     */
    public function getdistance($lng1, $lat1, $lng2, $lat2) {
        // 将角度转为狐度
        $radLat1 = deg2rad($lat1); //deg2rad()函数将角度转换为弧度
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;
        return $s;
    }

    public function xy(){
        return $this->fetch();
    }
}