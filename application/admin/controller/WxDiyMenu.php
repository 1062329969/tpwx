<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * 自定义菜单
 */
namespace app\admin\controller;
use think\Db;
use ApiOauth\ApiOauth;


class WxDiyMenu extends Admin
{

	public function _initialize()
	{
        parent::_initialize();
	}

	public function index()
    {
        //读取一级目录
        $diy_menu=Db::name('diy_menu')->where('topid',0)->order('sort asc')->select();
        //读取非一级
        $diy_menu2=Db::name('diy_menu')->where(array('topid'=>array('neq',0)))->order('sort asc')->select();
        $this->assign('diy_menu',$diy_menu);
        $this->assign('diy_menu2',$diy_menu2);

        return $this->fetch();
    }
    
    public function add()
    {
        $data=input('post.');
        if(empty($data['name']))
        {

	        return ['code'=>0,'msg'=>'菜单名称不能为空'];
        }
        if(empty($data['text']))
        {
	        return ['code'=>0,'msg'=>'菜单内容不能为空'];
        }
        //判断一级栏目有没有超过3个
        if($data['topid']==0)
        {
           $diy_menu=Db::name('diy_menu')->where(array('topid'=>0,'isshow'=>1))->count();
           if($diy_menu>=3)
           {
	           return ['code'=>0,'msg'=>'微信规定一级栏目不能超过三个'];
           }
        }
        else
        {
           $diy_menu=Db::name('diy_menu')->where(array('topid'=>$data['topid'],'isshow'=>1))->count();
           if($diy_menu>=5)
           {

	           return ['code'=>0,'msg'=>'微信规定二级栏目不能超过5个'];
           }
        }
        $data['create_time']=time();
        Db::name('diy_menu')->insert($data);
	    return ['code'=>1,'msg'=>'添加成功'];
    }

    public function alter()
    {
        $request=request();
        if($request->method()=='POST')
        {
           $data=input('post.');
           if(empty($data['name']))
            {
	            return ['code'=>0,'msg'=>'菜单名称不能为空'];
            }
            if(empty($data['text']))
            {
	            return ['code'=>0,'msg'=>'菜单内容不能为空'];
            }
            Db::name('diy_menu')->update($data);

	        return ['code'=>1,'msg'=>'修改成功'];
        }
        else
        {
           $id=input('get.id');
           $diy_menu=Db::name('diy_menu')->where('id',$id)->find();
           return JSON($diy_menu);
        }
    }

    public function del()
    {  
        $id=input('get.id');
        Db::name('diy_menu')->delete($id);
        Db::name('diy_menu')->where('topid',$id)->delete();
	    return ['code'=>1,'msg'=>'删除成功'];
    }

    //生成菜单
    public function sendMenu()
    {

	    $ApiOauth=new ApiOauth();
        $ACCESS_TOKEN=$ApiOauth->update_authorizer_access_token(array('appid'=>config('Appid'),'appsecret'=>config('Appsecret')));
        $url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$ACCESS_TOKEN;
        
        $returnMenuArr=array();
        //形成菜单的数据
        $diy_menu=Db::name('diy_menu')->where(array('topid'=>0,'isshow'=>1))->order('sort asc')->select();
        
        $menuArr=array();
        foreach($diy_menu as $key=>$value)
        {
            $diy_menu2=Db::name('diy_menu')->where(array('topid'=>$value['id'],'isshow'=>1))->order('sort asc')->select(); 

            if(empty($diy_menu2))
            {
                $menuArr[$key]["type"]=($value['type']==1)?"click":"view";
                $menuArr[$key]["name"]=$value['name'];
                if($value['type']==0 || $value['type']==2)
                {
                    $menuArr[$key]["url"]=$value['text'];
                }
                elseif($value['type']==1)
                {
                    $menuArr[$key]["key"]=$value['text'];
                }
            }
            else
            {
                $menuArr[$key]["name"]=$value['name'];
                $subMenuArr=array();
                foreach($diy_menu2 as $key2=>$value2)
                {
                    $subMenuArr[$key2]["type"]=($value2['type']==1)?"click":"view";


                    $subMenuArr[$key2]["name"]=$value2['name'];
                    if($value2['type']==0 || $value2['type']==2)
                    {
                       $subMenuArr[$key2]["url"]=$value2['text'];
                    }
                    elseif($value2['type']==1)
                    {
                       $subMenuArr[$key2]["key"]=$value2['text'];  
                    }
                    
                }
                $menuArr[$key]["sub_button"]=$subMenuArr;
            }
            $returnMenuArr["button"]=$menuArr;
        }

        $data=json_encode($returnMenuArr,JSON_UNESCAPED_UNICODE);


        $returnStr=json_decode(curl_post($url,$data));

        if($returnStr->errmsg=='ok')
        {
            $this->success('生成成功！');
        }
        else
        {
            $this->error('生成失败错误代码：'.$returnStr->errmsg);
        }
        
    }

    //删除菜单
    public function delMenu()
    {

        $ApiOauth=new ApiOauth();
        $ACCESS_TOKEN=$ApiOauth->update_authorizer_access_token(array('appid'=>config('Appid'),'appsecret'=>config('Appsecret')));
        $url='https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.$ACCESS_TOKEN;

        $returnStr=json_decode(httpGet($url));
        if($returnStr->errmsg=='ok')
        {
            $this->success('删除成功！');
        }
        else
        {
            $this->error('删除失败错误代码：',$returnStr->errmsg);
        }

    }
}