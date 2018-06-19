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

}