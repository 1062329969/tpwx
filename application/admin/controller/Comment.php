<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 评论管理控制器
 */
namespace app\admin\controller;
use app\admin\model\CommentModel;
use think\Db;

class Comment extends Admin
{

	public function _initialize()
	{
        parent::_initialize();
	}


    public function index()
    {

	  $comment=CommentModel::getAll();
      $this->assign('comment',$comment);

      return $this->fetch();
    }


   public function isstatus()
   {
      $type=input('param.type');
      if($type==1)
      {
         $id=input('param.id');
         Db::name('comment')->where('id',$id)->update(array('status'=>0));
         return $this->success('审核成功');
      }
      else
      {
         $id=input('param.id');
         Db::name('comment')->where('id',$id)->update(array('status'=>1));
         return $this->success('取消审核成功');
      }
      
   }

    public function del()
    {
        if (isset($_REQUEST['type'])) {
            $id = input('param.id');
            if (Db::name('comment')->where('id', $id)->update(array('del' => 1)))
            {
                json_str(1);
            } else {
                json_str(0);
            }
        }
        $id = input('param.id');
        Db::name('comment')->where('id', $id)->update(array('del' => 1));
        return $this->success('删除成功');
    }

}