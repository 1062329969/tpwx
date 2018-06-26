<?php
// 应用公共文件

use think\Route;
use think\Db;
Route::rule('archives','/archives.php/index/login');

/*
 * get请求
 * @param $url 请求的url地址
 * @return string
 */
function httpGet($url)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);

    $res = curl_exec($curl);
    curl_close($curl);

    return $res;
}


/*
 * 获得这个用户这个日期是否签到了
 * @param $uid 用户id
 * @param $year 年份
 * @param $month 月份
 * @param $date 日期
 * @return string
 */
function getQd($uid,$year,$month,$date)
{
    $time=strtotime($year.'-'.$month.'-'.$date);
    $return=\think\Db::name('sign_log')->where(['uid'=>$uid,'sign_time'=>$time])->find();
    if(!empty($return))
    {
        return '<img src="'.HTML_STATIC.'/images/qdbj.png">';
    }
    else
    {
        return '';
    }

}

/**
 * @return int 获取消息数量
 */
function getinformationcount($uid){
    $count = \think\Db::name('information')->where(['i_state'=>1,'i_uid'=>$uid])->count();
    return $count;
}


/*
 * 获得某商品出售的个数
 * @param $gid 商品的id
 * @return int
 */
function getSaleNum($gid)
{
    $return=\think\Db::name('goods_orders')->where(['status'=>1])->select();
    $i=0;
    foreach ($return as $key=>$v)
    {
        $goodsIdArr=explode(',',$v['goods_ids']);
        if(in_array($gid,$goodsIdArr))
        {
            $i=$i+1;
        }
    }
    return $i;
}


/*
 * 去除html标签 并获得字符串截取
 * @param $str 需要处理的字符串
 * @param $start 开始位置
 * @param $length 截取的长度
 * @param $charset 编码
 * @return string
 */
function mysubstr($str, $start=0, $length, $charset="utf-8", $suffix=true)
{
    //去除HTML标签
    $str=strip_tags($str);
    if(function_exists("mb_substr")){
        if($suffix)
            return mb_substr($str, $start, $length, $charset)."...";
        else
            return mb_substr($str, $start, $length, $charset);
    }
    elseif(function_exists('iconv_substr')) {
        if($suffix)
            return iconv_substr($str,$start,$length,$charset)."...";
        else
            return iconv_substr($str,$start,$length,$charset);
    }
    $re['utf-8'] = "/[x01-x7f]|[xc2-xdf][x80-xbf]|[xe0-xef][x80-xbf]{2}|[xf0-xff][x80-xbf]{3}/";
    $re['gb2312'] = "/[x01-x7f]|[xb0-xf7][xa0-xfe]/";
    $re['gbk'] = "/[x01-x7f]|[x81-xfe][x40-xfe]/";
    $re['big5'] = "/[x01-x7f]|[x81-xfe]([x40-x7e]|xa1-xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("",array_slice($match[0], $start, $length));
    if($suffix) return $slice."…";
    return $slice;
}

/**
 * 获取当前页面完整URL地址
 */
function get_url() {
    $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
    $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
    $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
    return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
}


/*
 * 获得微信不同大小的头像
 * @param $url 头像的地址
 * @param $size 头像的大小
 * @return string
 */
function get_wxheadimg($url,$size)
{
//    $urlArr=explode('/',$url);
//    array_pop($urlArr);
//    $url=implode('/',$urlArr);
//    //$url = substr($url,0,strlen($url)-1);
//    $url=$url.'/'.$size;
    return $url;
}
function getuserinfo($uid){
    return \think\Db::name('user_info')->find($uid);
}
function getarticle($type,$id){
    switch ($type)
    {
        case 1:
            $info = \think\Db::name('curing')->find($id);
            return $info;
            break;
        case 2:
            $info = \think\Db::name('hair_curing')->find($id);
            return $info;
            break;
        case 3:
            $info = \think\Db::name('cont_news')->find($id);
            return $info;
            break;
        case 4:
            $info = \think\Db::name('forum')->find($id);
            return $info;
            break;
        case 5:
            $info = \think\Db::name('ask')->find($id);
            return $info;
            break;
        case 6:
            $info = \think\Db::name('doctor')->find($id);
            return $info;
            break;
        case 7:
            $info = \think\Db::name('cases')->find($id);
            return $info;
            break;
        case 8:
            $info = \think\Db::name('video')->find($id);
            return $info;
            break;
        case 9:
            $info = \think\Db::name('ncase')->find($id);
            return $info;
            break;
    }
}

/**
 * 获取养护文章
 * @param $id
 * @return array 文章信息
 */
function gethaircare($id){
    $info = \think\Db::name('haircare')->find($id);
    return $info;
}

/*
 * 通过秒获得分钟
 * @param $second 秒数
 * @return string
 */
function get_minute($second)
{
    if($second<=60)
    {
        return $second.'秒';
    }
    else
    {
        $minute=floor($second/60);
        $second=fmod($second,60);
    }
    if($second==0)
    {
        return $minute.'分钟';
    }
    else
    {
        return $minute.'分钟'.$second.'秒';
    }

}

/*
 * 通过秒获得多少时间之前
 * @param $the_time 要处理的时间戳
 * @return date
 */
    function get_time($the_time)
{
    $now_time = time();
    $show_time = $the_time;
    $dur = $now_time - $show_time;
    if ($dur < 0)
    {
        date('Y-m-d H;i:s',$the_time);
    } else {
        if ($dur < 60) {
            return $dur . '秒前';
        } else {
            if ($dur < 3600) {
                return floor($dur / 60) . '分钟前';
            } else {
                if ($dur < 86400) {
                    return floor($dur / 3600) . '小时前';
                } else {
                    if ($dur < 259200) {
                        return floor($dur / 86400) . '天前';
                    } else {
                        return date('Y-m-d H:i',$the_time);
                    }
                }
            }
        }
    }
}



/*
 * 评论数超过9999以万为单位，超过一万 保留一位小数
 * @param $the_time 要处理的时间戳
 * @return date
 */
function comment_sum($the_time){ //10000
    if($the_time >= 10000){
        $sum = $the_time % 1000;
        $c = intval($the_time/1000);
        if($sum>0){
            $c++;
        }
        $the_time = ($c / 10) . 'w';
    }

    return $the_time ;
}
/*
 * post请求
 * @param $url 请求的url地址
 * @param $data 提交的数据
 * @return string
 */
function curl_post($url, $data)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    return $data = curl_exec($ch);
}


/*
 * 格式化时间
 * @param $datetime 要处理的日期
 * @return date
 */
function formatDate($datetime)
{

    if($datetime>=strtotime(date('Y-m-d')))
    {
        return '今天';
    }
    elseif($datetime<strtotime(date('Y-m-d')) && $datetime>=strtotime(date("Y-m-d",strtotime("-1 day"))))
    {
        return '昨天';
    }
    else
    {
        return date('Y-m-d',$datetime);
    }
}

/**
 * 获取当前URL
 * @return string
 */
function getSelfUrl($params = array(), $url = '')
{
    $secure = isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'],'on')===0 || $_SERVER['HTTPS']==1)	|| isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'],'https')===0;
    $hostInfo = '';
    if($secure)
    {
        $http='https';
    }
    else
    {
        $http='http';
    }
    if(isset($_SERVER['HTTP_HOST']))
    {
        $hostInfo=$http.'://'.$_SERVER['HTTP_HOST'];
    }
    else
    {
        $hostInfo=$http.'://'.$_SERVER['SERVER_NAME'];
        if ($secure) {
            $port = isset($_SERVER['SERVER_PORT']) ? (int)$_SERVER['SERVER_PORT'] : 443;
        } else {
            $port = isset($_SERVER['SERVER_PORT']) ? (int)$_SERVER['SERVER_PORT'] : 80;
        }
        if(($port!==80 && !$secure) || ($port!==443 && $secure)) {
            $hostInfo.=':'.$port;
        }
    }
    $requestUri = '';
    if(isset($_SERVER['HTTP_X_REWRITE_URL'])) {// IIS
        $requestUri=$_SERVER['HTTP_X_REWRITE_URL'];
    } elseif (isset($_SERVER['REQUEST_URI'])) {
        $requestUri=$_SERVER['REQUEST_URI'];
        if(!empty($_SERVER['HTTP_HOST'])) {
            if(strpos($requestUri,$_SERVER['HTTP_HOST'])!==false) {
                $requestUri=preg_replace('/^\w+:\/\/[^\/]+/','',$requestUri);
            }
        }
        else {
            $requestUri=preg_replace('/^(http|https):\/\/[^\/]+/i','',$requestUri);
        }
    } elseif(isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0 CGI
        $requestUri=$_SERVER['ORIG_PATH_INFO'];
        if(!empty($_SERVER['QUERY_STRING'])) {
            $requestUri.='?'.$_SERVER['QUERY_STRING'];
        }
    } else {
        exit('没有获取到当前的url');
    }
    if (empty($url)) {
        $url =  $hostInfo.$requestUri;
    }
    $parseUrl = parse_url($url);
    if(!empty($parseUrl['query']))
    {
        parse_str(htmlspecialchars_decode($parseUrl['query']), $query);
        foreach ($params as $key => $param) {
            $value = isset($query[$param]) ? $query[$param] : '';
            if (1 == count($query)) {
                $value = '\?'.$param.'='.$value;
            } else {
                $value = '&'.$param.'='.$value.'|'.$param.'='.$value.'&';
            }
            $url = preg_replace("/$value/i", '', $url);
        }
    }

    return $url;
}


/**
 * 过滤掉emoji表情
 * @return $str  要处理的文本
 * @return string
 */
function filterEmoji($str)
{
    $str = preg_replace_callback(
        '/./u',
        function (array $match) {
            return strlen($match[0]) >= 4 ? '' : $match[0];
        },
        $str);

    return $str;
}

/*
*辅助函数
*/
function passport_key($str,$encrypt_key){
    $encrypt_key=md5($encrypt_key);
    $ctr=0;
    $tmp='';
    for($i=0;$i<strlen($str);$i++){
        $ctr=$ctr==strlen($encrypt_key)?0:$ctr;
        $tmp.=$str[$i] ^ $encrypt_key[$ctr++];
    }
    return $tmp;
}

/*
*功能：对字符串进行解密处理
*参数一：需要解密的密文
*参数二：密钥
*/
function passport_decrypt($str,$key){ //解密函数
    $str=passport_key(base64_decode($str),$key);
    $tmp='';
    for($i=0;$i<strlen($str);$i++){
        $md5=$str[$i];
        $tmp.=$str[++$i] ^ $md5;
    }
    return $tmp;
}

/*
*功能：对字符串进行加密处理
*参数一：需要加密的内容
*参数二：密钥
*/
function passport_encrypt($str,$key){ //加密函数
    srand((double)microtime() * 1000000);
    $encrypt_key=md5(rand(0, 32000));
    $ctr=0;
    $tmp='';
    for($i=0;$i<strlen($str);$i++){
        $ctr=$ctr==strlen($encrypt_key)?0:$ctr;
        $tmp.=$encrypt_key[$ctr].($str[$i] ^ $encrypt_key[$ctr++]);
    }
    return base64_encode(passport_key($tmp,$key));
}

/*
 * 获取管理员姓名
 */
function getadminname($uid){
    $admin = \think\Db::name('admin')->field('username,realname')->find($uid);
    return $admin['realname'];
}


/**
 * 获取某个分类的所有子分类
 * @param $categorys  數組元素
 * @param int $catId  父類id
 * @param int $level  級別
 * @return array      返回值
 */
function getSubs($categorys,$catId=0,$level=1){
    $subs=array();
    foreach($categorys as $item){
        if($item['plid']==$catId){
            $item['level']=$level;
            $subs[]=$item;
            $subs=array_merge($subs,getSubs($categorys,$item['id'],$level+1));

        }

    }
    return $subs;
}

/**
 * @param $code 编码
 */
function json_str($code){
    echo json_encode(['code' => $code]);
    exit;
}

/**
 * 查询数量问题
 */
function return_asum($id,$pid){
    if($pid==0){
        $counts = \think\Db::name('ask')->where(['class_first'=>$id])->count();
    }else{
        $counts = \think\Db::name('ask')->where(['class_first'=>$pid,'class_second'=>$id])->count();
    }
    // echo  \think\Db::getLastSql();
    return $counts;
}

/**
 * 获取会员分组
 */
function return_vipclass($uid){
    $setting = Db::name('vipclasssetting')->find(1);
    if($setting['vcs_type']==2){
        //根据积分
        $user = Db::name('user_info')->find($uid);
        $integral = $user['integral'];
        $class = Db::name('vipclass')->where('vc_int<='.$integral)->order('vc_int desc')->find();
        if(!$class){
            $class = Db::name('vipclass')->order('vc_int asc')->find();
        }
    }else{
        $user = Db::name('user_info')->find($uid);
        $vip = $user['vipclass'];
        $class = Db::name('vipclass')->find($vip);
        if(!$class){
            $integral = $user['integral'];
            $class = Db::name('vipclass')->where('vc_int<='.$integral)->order('vc_int desc')->find();
            $vc_id = $class['vc_id'];
            Db::name('user_info')->where(['id'=>$vc_id])->update(['vipclass'=>$vc_id]);
        }
    }
    if(!$class){
        $class['vc_name'] = '';
    }
    return $class;
}


//随机生成EncodingAESKey
function generate_password( $length = 8 ) {
// 密码字符集，可任意添加你需要的字符
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $password = '';
    for ( $i = 0; $i < $length; $i++ )
    {
    // 这里提供两种字符获取方式
    // 第一种是使用 substr 截取$chars中的任意一位字符；
    // 第二种是取字符数组 $chars 的任意元素
    // $password .= substr($chars, mt_rand(0, strlen($chars) – 1), 1);
       $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
    }
    return $password;
}

//echo generate_password(43);

function return_vipsum($vip){
    $setting = Db::name('vipclasssetting')->find(1);
    if($setting['vcs_type']==2){
        $class = Db::name('vipclass')->find($vip);
        $intstart = $class['vc_int'];
        $info = Db::name('vipclass')->where('vc_int>'.$intstart)->order('vc_int asc')->find();
//        echo Db::getLastSql();
        $intend = $info['vc_int'];
        $where['integral'] = [
            ['>=',$intstart],
            ['<',$intend],
        ];
        $sum = Db::name('user_info')->where($where)->count();
//        echo Db::getLastSql();
    }else{
        $sum = Db::name('user_info')->where(['vipclass'=>$vip])->count();
    }
    return $sum;
}