<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * 文本回复内容
 */
namespace app\admin\controller;
use think\Db;

class WxTextReply extends Admin
{

    public function _initialize()
    {
        parent::_initialize();
    }

  public function index()
  {
        $text_reply=Db::name('text_reply')->where('del',0)->order('id desc')->select();
        $this->assign('text_reply',$text_reply);
        return $this->fetch();
  }
	
	public function add()
  {
      $request=request();
      if($request->method()=='POST')
      {
          $data=input('post.');

          if(!empty($data['keyword']))
          {

              $keyword=Db::name('keyword')->where(array('del'=>0,'keyword'=>$data['keyword']))->find();

              if(!empty($keyword))
              {
                 $this->error('添加的关键词已经存在！'); 
              }
              else
              {
                 $data['create_time']=time();
                 Db::name('text_reply')->insert($data);
                 $insertId=Db::name('text_reply')->getLastInsID();
          			 $keydata['keyword']=$data['keyword'];
          			 $keydata['keytype']=$data['type'];
          			 $keydata['type']=1;
          			 $keydata['create_time']=$data['create_time'];
          			 $keydata['in_id']=$insertId;
          	
          			 Db::name('keyword')->insert($keydata);
                 $this->success('添加成功');  
              }
          }
          else
          {
              $this->error('关键词不能为空');
          } 
      }
      else
      {
          return $this->fetch();  
      }
      
  }
	
	public function alter()
   {
      $request=request();
      
      if($request->method()=='POST')
      {
          $data=input('post.');

    		  if(!empty($data['keyword']))
    		  {
    			   $keyword=Db::name('keyword')->where('in_id',$data['id'])->find();
				   if($keyword['keyword']!=$data['keyword'])
				   {
					   $keyword=Db::name('keyword')->where(array('del'=>0,'keyword'=>$data['keyword']))->find();
			
					   if(!empty($keyword))
					   {
							$this->error('添加的关键词已经存在！');
					   }
					   else
					   {
						   Db::name('keyword')->where('in_id',$data['id'])->update(array('keyword'=>$data['keyword'],'keytype'=>$data['type']));
						   $this->success('修改成功');
					   }

				   }
             else
             {
                $data['create_time']=time();
                Db::name('keyword')->where('in_id',$data['id'])->update(array('keyword'=>$data['keyword'],'keytype'=>$data['type']));
                
                Db::name('text_reply')->update($data);
                $this->success('修改成功'); 
             }
    		  }
          else
          {
             $this->error('关键词不能为空');
          }
      }
      else
      {
         $id=input('param.id');
         $text_reply=Db::name('text_reply')->where(array('id'=>$id))->find();
         $this->assign('text_reply',$text_reply);
         return $this->fetch(); 
      }
      
  }
  public function del()
  {  
       $id=input('param.id');
       Db::name('keyword')->where('in_id',$id)->update(array('del'=>1));
       Db::name('text_reply')->where('id',$id)->update(array('del'=>1));
       echo json_encode(array('code'=>1));
  }
}