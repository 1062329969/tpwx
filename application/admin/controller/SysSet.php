<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 系统设置
 */

namespace app\admin\controller;
use think\Db;
use think\Config;

class SysSet extends Admin
{

    public function _initialize()
    {
        parent::_initialize();
    }

  public function index()
  {
      $request=request();
      if($request->method()=='POST')
      {
         
         $data=input('param.');
         //前台参数
         $indexData['sitename']=$adminData['sitename']=$data['sitename'];
         $indexData['domain']=$adminData['domain']=$data['domain'];
         $indexData['Appid']=$data['Appid'];
         $indexData['Appsecret']=$data['Appsecret'];
         $indexData['mchid']=$data['mchid'];
         $indexData['apikey']=$data['apikey'];
	     //$indexData['page']=$data['page'];
	     $indexData['tel']=$data['tel'];
	     $indexData['zoosnet']=$data['zoosnet'];
	     $indexData['Token']=$data['Token'];
	     $indexData['EncodingAESKey']=$data['EncodingAESKey'];
         $indexData['codeCont']=$data['codeCont'];
	      $indexData['sharejf']=$data['sharejf'];
	      $indexData['signjf']=$data['signjf'];
	      $indexData['hpdjf']=$data['hpdjf'];
	      $indexData['conSignjf']=$data['conSignjf'];
	      $indexData['getIntegral']=$data['getIntegral'];
	      $indexData['EBusinessID']=$data['EBusinessID'];
	      $indexData['AppKey']=$data['AppKey'];

	      $indexData['account']=$data['account'];
	      $indexData['password']=$data['password'];
	      $indexData['extno']=$data['extno'];
          $indexData['hudong']=$data['hudong'];

         //后台参数
	     $adminData['Appid']=$data['Appid'];
	     $adminData['Appsecret']=$data['Appsecret'];
	     $adminData['picType']=$data['picType'];
	     $adminData['picSize']=$data['picSize'];
	      $adminData['EBusinessID']=$data['EBusinessID'];
	      $adminData['AppKey']=$data['AppKey'];

         //保存前台配置文件
         $setfile=APP_PATH."index/config.php";
         $settingstr="<?php \n return [\n";
         foreach($indexData as $key=>$v){
            $settingstr.= "\t'".$key."'=>'".$v."',\n";
         }
         $settingstr.="];\n?>\n";
         file_put_contents($setfile,$settingstr);
         
         //保存后台配置文件
         $setfile=APP_PATH."admin/config.php";
         $settingstr="<?php \n return [\n";
         foreach($adminData as $key=>$v){
            $settingstr.= "\t'".$key."'=>'".$v."',\n";
         }
         $settingstr.="];\n?>\n";
         file_put_contents($setfile,$settingstr);
        
         return $this->success('保存成功');
      }
      else
      {
         Config::load(APP_PATH.'index/config.php');
         return $this->fetch();
      }
      
  }
  public function wechataccess(){
//      查看config文件内容是否为空，如果为空则添加
//      $section = file_get_contents(APP_PATH.'/index/config.php', NULL, NULL,null);
//      if(empty($section)){
          $request=request();
          if($request->method()=='POST')
          {
              $data=input('param.');
              if($data['select'] == 1){
                  $a = 'http://';
                  $domain = $a.$data['domain'];//域名
              }else{
                  $a = 'https://';
                  $domain = $a.$data['domain'];//域名
              }
              //如果没上传公众号头像就删除
              if(!empty($smallimg))
              {
                  $data['smallimg']=$smallimg;
              }
              else
              {

                  unset($data['smallimg']);
              }
              //如果没上传二维码就删除
              if(!empty($code_img))
              {
                  $data['code_img']=$code_img;
              }
              else
              {
                  unset($data['code_img']);
              }

              //公众号头像
              $Smallimg = request()->file("smallimg");
              if(!empty($Smallimg)){
                  $smallimg = $Smallimg->move(ROOT_PATH . 'public' . DS . 'uploads');
                  $smallimg =  str_replace('\\','/','/uploads/'.$smallimg->getSaveName());
                  $indexData['smallimg'] = $smallimg;//公众号头像

              }
              //上传二维码
              $Code_img = request()->file("code_img");
              if(!empty($Code_img)){
                  $code_img = $Code_img->move(ROOT_PATH . 'public' . DS . 'uploads');
                  $code_img = str_replace('\\','/','/uploads/'.$code_img->getSaveName());
                  $indexData['code_img'] =$code_img;//二维码

              }

              Config::load(APP_PATH.'admin/config.php');
              $adminData['EBusinessID'] = Config::get('EBusinessID');
              $adminData['AppKey']= Config::get('AppKey');



              Config::load(APP_PATH.'index/config.php');
              //先获取前台配置文件里面的参数
              $indexData['domain']=$res['domain'] = Config::get('domain');//域名
              $indexData['sitename']=$res['sitename'] = Config::get('sitename');//公众号名称
              $indexData['account']=$res['account'] = Config::get('account');//公众号账号
              $indexData['yid']=$res['yid'] = Config::get('yid');//原始Id
              $indexData['number']=$res['number'] = Config::get('number');//公众号类型
              $indexData['Appid']=$res['Appid'] = Config::get('Appid');//开发者id
              $indexData['AppSecret']=$res['AppSecret'] = Config::get('AppSecret');//开发者密码
              $indexData['Token']=$res['Token'] = Config::get('Token');//Token
              $indexData['EncodingAESKey']=$res['EncodingAESKey'] = Config::get('EncodingAESKey');
              $indexData['zoosnet']=$res['zoosnet'] = Config::get('zoosnet');
              $indexData['codeCont']=$res['codeCont'] = Config::get('codeCont');//短信
              $indexData['sharejf']=$res['sharejf'] = Config::get('sharejf');
              $indexData['signjf']=$res['signjf'] = Config::get('signjf');
              $indexData['hpdjf']=$res['hpdjf'] = Config::get('hpdjf');
              $indexData['conSignjf']=$res['conSignjf'] = Config::get('conSignjf');
              $indexData['getIntegral']=$res['getIntegral'] = Config::get('getIntegral');
              $indexData['EBusinessID']=$res['EBusinessID'] = Config::get('EBusinessID');
              $indexData['AppKey']=$res['AppKey'] = Config::get('AppKey');
              $indexData['account']=$res['account'] = Config::get('account');
              $indexData['password']=$res['password'] = Config::get('password');
              $indexData['extno']=$res['extno'] = Config::get('extno');
              $indexData['hudong']=$res['hudong'] = Config::get('hudong');
              $indexData['tel']=$res['tel'] = Config::get('tel');
              $indexData['mchid']=$res['mchid'] = Config::get('mchid');
              $indexData['apikey']=$res['apikey'] = Config::get('apikey');

              //前台参数
              $indexData['domain']=$adminData['domain']=$domain;//域名
              $indexData['sitename']=$adminData['sitename']=$data['sitename'];//公众号名称
              $indexData['account']=$data['account'];//公众号账号
              $indexData['yid'] = $data['yid'];//原始Id
              $indexData['number'] = $data['number'];//公众号类型
              $indexData['Appid'] = $data['kid'];//开发者id
              $indexData['AppSecret'] = $data['kpwd'];//开发者密码
              $indexData['EncodingAESKey']=$data['encodingaeskey'];
              $indexData['info'] = $data['info'];//描述u
              $indexData['EBusinessID'] =  $adminData['EBusinessID'];
              $indexData['AppKey'] = $adminData['AppKey'];

              //          $indexData['Appid']=$data['Appid'];
              //          $indexData['Appsecret']=$data['Appsecret'];
              //          $indexData['mchid']=$data['mchid'];
              //          $indexData['apikey']=$data['apikey'];
              //          //$indexData['page']=$data['page'];
              //          $indexData['tel']=$data['tel'];
              //          $indexData['zoosnet']=$data['zoosnet'];
              //          $indexData['Token']=$data['Token'];
              //          $indexData['codeCont']=$data['codeCont'];
              //          $indexData['sharejf']=$data['sharejf'];
              //          $indexData['signjf']=$data['signjf'];
              //          $indexData['hpdjf']=$data['hpdjf'];
              //          $indexData['conSignjf']=$data['conSignjf'];
              //          $indexData['getIntegral']=$data['getIntegral'];
              //          $indexData['EBusinessID']=$data['EBusinessID'];
              //          $indexData['AppKey']=$data['AppKey'];
              //
              //          $indexData['password']=$data['password'];
              //          $indexData['extno']=$data['extno'];
              //          $indexData['hudong']=$data['hudong'];

              //
              //          //后台参数
              $adminData['Appid']=$data['kid'];
              $adminData['Appsecret']=$data['kpwd'];
              $adminData['smallimg'] = $smallimg;//公众号头像
              $adminData['code_img'] =$code_img;//二维码


              //          $adminData['picType']=$data['picType'];
              //          $adminData['picSize']=$data['picSize'];
              //          $adminData['EBusinessID']=$data['EBusinessID'];
              //          $adminData['AppKey']=$data['AppKey'];
              //          $adminDate['code_img'] =$code_img;//二维码
              //          $adminDate['smallimg'] = $smallimg;//公众号头像



//              //保存前台配置文件
//              $setfile=APP_PATH."index/config.php";
//              $settingstr="<?php \n return [\n";
//              foreach($indexData as $key=>$v){
//                  $settingstr.= "\t'".$key."'=>'".$v."',\n";
//              }
/*              $settingstr.="];\n?>\n";*/
//              file_put_contents($setfile,$settingstr);
              //保存前台配置文件
              $setfile=APP_PATH."index/config.php";
              $str = '<?php return ';
              $str .=
                  var_export($indexData,true);//数组转字符串
              $str .= ';';
              file_put_contents($setfile,$str);


//              //保存后台配置文件
//              $setfile=APP_PATH."admin/demolx.php";
//              $settingstr="<?php \n return [\n";
//              foreach($adminData as $key=>$v){
//                  $settingstr.= "\t'".$key."'=>'".$v."',\n";
//              }
/*              $settingstr.="];\n?>\n";*/
//              file_put_contents($setfile,$settingstr);

              //保存后台配置文件
              $setfile=APP_PATH."admin/demolx.php";
              $str = '<?php return ';
              $str .=
                  var_export($adminData,true);//数组转字符串
              $str .= ';';
              file_put_contents($setfile,$str);


              return $this->success('保存成功');
          }
          else
          {
              Config::load(APP_PATH.'index/config.php');
              return $this->fetch();
          }
//      }else{
//          $this->success('正在为您跳转,请稍等!','step_one');
//      }
  }


  //
  public function step_one(){
      $request=request();
      if($request->method()=='POST'){
          $dada = input('param.');//参数
          $type=input('param.types');//key

          Config::load(APP_PATH.'admin/config.php');


          Config::load(APP_PATH.'index/config.php');
          //先获取前台配置文件里面的参数
          $res['domain'] = Config::get('domain');//域名
          $res['sitename'] = Config::get('sitename');//公众号名称
          $res['account'] = Config::get('account');//公众号账号
          $res['yid'] = Config::get('yid');//原始Id
          $res['number'] = Config::get('number');//公众号类型
          $res['Appid'] = Config::get('Appid');//开发者id
          $res['AppSecret'] = Config::get('AppSecret');//开发者密码
          $res['Token'] = Config::get('Token');//Token
          $res['EncodingAESKey'] = Config::get('EncodingAESKey');
          $res['zoosnet'] = Config::get('zoosnet');
          $res['codeCont'] = Config::get('codeCont');//短信
          $res['sharejf'] = Config::get('sharejf');
          $res['signjf'] = Config::get('signjf');
          $res['hpdjf'] = Config::get('hpdjf');
          $res['conSignjf'] = Config::get('conSignjf');
          $res['getIntegral'] = Config::get('getIntegral');
          $res['EBusinessID'] = Config::get('EBusinessID');
          $res['AppKey'] = Config::get('AppKey');
          $res['account'] = Config::get('account');
          $res['password'] = Config::get('password');
          $res['extno'] = Config::get('extno');
          $res['hudong'] = Config::get('hudong');

          $res['EBusinessID'] =   Config::get('EBusinessID');
          $res['AppKey'] =  Config::get('AppKey');
          $res['mchid'] = Config::get('mchid');
          $res['apikey'] = Config::get('apikey');
          $res['tel'] = Config::get('tel');

          $res['smallimg'] = Config::get('smallimg');
          $res['code_img'] = Config::get('code_img');
          //查看要修改的是头像，还是二维码
          if($type == 'smallimg'){
              //公众号头像
              $url = $this->saveBase64Images($dada['files']);
              $res['smallimg'] = $url;
          }elseif($type == 'code_img') {
              //二维码
              $url = $this->saveBase64Images($dada['filess']);
              $res['code_img'] = $url;
          }elseif($type == 'domain'){
              //域名
              $res['domain'] = $dada['domain'];
          }elseif($type == 'sitename'){
              //公众号名称
              $res['sitename'] = $dada['sitename'];
          }elseif($type == 'account'){
              //公众号账号
              $res['account'] = $dada['account'];
          }elseif($type == 'yid'){
              //原始Id
              $res['yid'] = $dada['yid'];
          }elseif($type == 'number'){
              $res['number'] = $dada['number'];
          }elseif($type == 'Appid'){
              //开发者id
              $res['Appid'] = $dada['Appid'];
          }elseif($type == 'AppSecret'){
              //开发者密码
              $res['AppSecret'] = $dada['AppSecret'];
          }elseif($type == 'Token'){
              //Token
              $res['Token'] = $dada['Token'];
          }elseif($type == 'EncodingAESKey'){
              //EncodingAESKey
              $res['EncodingAESKey'] = $dada['EncodingAESKey'];
          }


        $adminData['EBusinessID'] = $res['EBusinessID'];
        $adminData['AppKey'] = $res['AppKey'];
        $adminData['sitename'] = $res['sitename'];
        $adminData['domain'] = $res['domain'];
        $adminData['Appid'] = $res['Appid'];
        $adminData['AppSecret'] = $res['AppSecret'];
        $adminData['smallimg']=$res['smallimg'];
        $adminData['code_img']=$res['code_img'];


//      //前台参数
//      $indexData['domain']=$adminData['domain']=$domain;//域名
//      $indexData['sitename']=$adminData['sitename']=$data['sitename'];//公众号名称
//      $indexData['account']=$data['account'];//公众号账号
//      $indexData['yid'] = $data['yid'];//原始Id
//      $indexData['number'] = $data['number'];//公众号类型
//      $indexData['Appid'] = $data['kid'];//开发者id
//      $indexData['AppSecret'] = $data['kpwd'];//开发者密码
//      $indexData['EncodingAESKey']=$data['encodingaeskey'];

          //保存前台配置文件
          $setfile=APP_PATH."index/config.php";
          $str = '<?php return ';
          $str .=
              var_export($res,true);//数组转字符串
          $str .= ';';
          file_put_contents($setfile,$str);

//
//          //保存前台配置文件
          $setfiles=APP_PATH."admin/config.php";
          $strs = '<?php return ';
          $strs .=
              var_export($adminData,true);//数组转字符串
          $strs .= ';';
          file_put_contents($setfiles,$strs);


          $this->success('保存成功',url('step_one'));

      }
      Config::load(APP_PATH.'index/config.php');
      return $this->fetch();
  }

//修改
  public function editval(){
      $type=input('param.type');
      $dada = input('param.');
      if(in_array($type,['domain','sitename','account','yid','number','Appid','AppSecret','Token','EncodingAESKey'])){
          Config::load(APP_PATH.'index/config.php');
          //先获取前台配置文件里面的参数
          $info['domain'] = $res['domain']          = Config::get('domain');//域名
          $info['sitename'] = $res['sitename']        = Config::get('sitename');//公众号名称
          $info['account'] = $res['account']         = Config::get('account');//公众号账号
          $info['yid'] = $res['yid']             = Config::get('yid');//原始Id
          $info['number'] = $res['number']          = Config::get('number');//公众号类型
          $info['Appid'] = $res['Appid']           = Config::get('Appid');//开发者id
          $info['AppSecret'] = $res['AppSecret']       = Config::get('AppSecret');//开发者密码
          $info['Token'] = $res['Token']           = Config::get('Token');//Token
          $info['EncodingAESKey'] = $res['EncodingAESKey']  = Config::get('EncodingAESKey');
          $info['zoosnet'] = $res['zoosnet']         = Config::get('zoosnet');
          $info['codeCont'] = $res['codeCont']        = Config::get('codeCont');//短信
          $info['sharejf'] = $res['sharejf']         = Config::get('sharejf');
          $info['signjf'] = $res['signjf']          = Config::get('signjf');
          $info['hpdjf'] = $res['hpdjf']           = Config::get('hpdjf');
          $info['conSignjf'] = $res['conSignjf']       = Config::get('conSignjf');
          $info['getIntegral'] = $res['getIntegral']     = Config::get('getIntegral');
          $info['EBusinessID'] = $res['EBusinessID']     = Config::get('EBusinessID');
          $info['AppKey'] = $res['AppKey']          = Config::get('AppKey');
          $info['account'] = $res['account']         = Config::get('account');
          $info['password'] = $res['password']        = Config::get('password');
          $info['extno'] = $res['extno']           = Config::get('extno');
          $info['hudong'] = $res['hudong']          = Config::get('hudong');

          $info['tel']=$res['tel'] = Config::get('tel');
          $info['mchid']=$res['mchid'] = Config::get('mchid');
          $info['apikey']=$res['apikey'] = Config::get('apikey');
      }elseif($type == 'smallimg'){
          //公众号头像
          $info['smallimg']  = $type;
//          $info['smallimg'] = $this->saveBase64Images($info['smallimg']);

      }elseif($type == 'code_img'){
          //二维码
          $info['code_img'] = $type;
//          $info['code_img'] = $this->saveBase64Images($dada['code_img']);


      }
      $this->assign('val',$info);
      $this->assign('type',$type);

      Config::load(APP_PATH.'index/config.php');
      return $this->fetch();
  }

  //自动保存Token，EncodingAESKey
  public function step_two(){
      $data = input('param.');
      Config::load(APP_PATH.'index/config.php');
      //先获取前台配置文件里面的参数
      $info['domain'] = $res['domain']          = Config::get('domain');//域名
      $info['sitename'] =$res['sitename']        = Config::get('sitename');//公众号名称
      $info['account'] =$res['account']         = Config::get('account');//公众号账号
      $info['yid'] =$res['yid']             = Config::get('yid');//原始Id
      $info['number'] =$res['number']          = Config::get('number');//公众号类型
      $info['Appid'] =$res['Appid']           = Config::get('Appid');//开发者id
      $info['AppSecret'] =$res['AppSecret']       = Config::get('AppSecret');//开发者密码
      $info['Token'] =$res['Token']           = Config::get('Token');//Token
      $info['EncodingAESKey'] =$res['EncodingAESKey']  = Config::get('EncodingAESKey');
      $info['zoosnet'] =$res['zoosnet']         = Config::get('zoosnet');
      $info['codeCont'] =$res['codeCont']        = Config::get('codeCont');//短信
      $info['sharejf'] =$res['sharejf']         = Config::get('sharejf');
      $info['signjf'] =$res['signjf']          = Config::get('signjf');
      $info['hpdjf'] =$res['hpdjf']           = Config::get('hpdjf');
      $info['conSignjf'] =$res['conSignjf']       = Config::get('conSignjf');
      $info['getIntegral'] =$res['getIntegral']     = Config::get('getIntegral');
      $info['EBusinessID'] =$res['EBusinessID']     = Config::get('EBusinessID');
      $info['AppKey'] =$res['AppKey']          = Config::get('AppKey');
      $info['account'] =$res['account']         = Config::get('account');
      $info['password'] =$res['password']        = Config::get('password');
      $info['extno'] =$res['extno']           = Config::get('extno');
      $info['hudong'] =$res['hudong']          = Config::get('hudong');
      $info['smallimg'] = $res['smallimg']  = Config::get('smallimg');
      $info['code_img'] =$res['code_img']   = Config::get('code_img');

      $info['tel']=$res['tel'] = Config::get('tel');
      $info['mchid']=$res['mchid'] = Config::get('mchid');
      $info['apikey']=$res['apikey'] = Config::get('apikey');

      if(!empty($data['EncodingAESKey'])){
        $info['EncodingAESKey']  = $data['EncodingAESKey'];
      }else{
          unset($data['EncodingAESKey']);
      }

      if(!empty($data['Token'])){
          $info['Token'] = $data['Token'];
      }else{
          unset($data['Token']);
      }

      //保存后台配置文件
      $setfile=APP_PATH."index/config.php";
      $str = '<?php return ';
      $str .=
          var_export($info,true);//数组转字符串
      $str .= ';';
//      file_put_contents($setfile,$str);
      if(file_put_contents($setfile,$str)){
        $this->success('修改成功','step_one');
      }
      return $this->fetch();
  }



    public function saveBase64Images($base64_image_content){

        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
            //图片后缀
            $type = $result[2];
            //保存位置--图片名
            $image_name=date('His').str_pad(mt_rand(1, 99999999), 5, '0', STR_PAD_LEFT).".".$type;
            $image_url = '/uploads/'.date('Ymd').'/'.$image_name;
            $image_path = '/uploads/'.date('Ymd').'/';
            if(!is_dir(dirname('.'.$image_url))){
                mkdir(dirname('.'.$image_url),0700,true);
            }
            //解码
            $decode=base64_decode(str_replace($result[1], '', $base64_image_content));
            if (file_put_contents('.'.$image_url, $decode))
            {
                $data['url']=$image_url;
            }else{
                $data['url']='';
            }
        }else{
            $data['url']=$base64_image_content;
        }
        return $data['url'];
    }

}