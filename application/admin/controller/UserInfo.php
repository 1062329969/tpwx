<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/13
 * 会员管理
 */
namespace app\admin\controller;
use app\admin\model\OrdersModel;
use app\admin\model\UserInfoModel;
use think\Db;

class UserInfo extends Admin
{

	public function _initialize()
	{
//		parent::_initialize();
	}

  public function index()
  {
      $type = input('param.type');
      if(!isset($type)){
          $user_info=UserInfoModel::getAll();
      }else{
          $user_info=UserInfoModel::getS($type);
      }
      $this->assign('type',$type);
	  $this->assign('user_info',$user_info);
      return $this->fetch();
  }

  public function detail()
  {

      return $this->fetch();
  }

	//添加模拟用户
	public function add()
	{
		$request=request();
		if($request->method()=='POST')
		{
			$data=input('post.');

			$result=$this->validate($data,'ValidateClass.CK17');
			if(true !== $result)
			{
				// 验证失败 输出错误信息
				$this->error($result);
			}

			//上传图片
			$pic=$this->upload($request,'portrait');
			if(!empty($pic))
			{
				$data['portrait']=$pic;
			}
			else
			{
				$this->error('请上传用户头像');
			}

			$data['type']=1; //模拟用户
			$data['regtime']=time();

            $userInfoModel=new UserInfoModel();
			if($userInfoModel->allowField(true)->save($data))
			{
				$this->success('添加成功',url('index'));
			}
			else
			{
				$this->error('添加失败',url('index'));
			}
		}
		else
		{
			return $this->fetch();
		}
	}

    //用户案例 论坛 问答 选择用户
	public function selectuid()
	{
		$user_info=UserInfoModel::getAll();
		$this->assign('user_info',$user_info);
		return $this->fetch();
	}

	public function alter(){
        $id=input('param.id',0);
        $user = Db::name('user_info')->find($id);
        if(!$user){
            $this->error('未找到用户');
        }else{
            $request=request();
            if($request->method()=='POST')
            {
                $data=input('post.');
                //上传图片
                $pic=$this->upload($request,'portrait');
                if(!empty($pic))
                {
                    $data['portrait']=$pic;
                }else{
                    unset($data['portrait']);
                }
                $userInfoModel=new UserInfoModel();
                if($userInfoModel->allowField(true)->save($data,['id'=>$id])){
                    $this->success('修改成功',url('index'));
                }else{
                    $this->error('修改失败',url('index'));
                }
            }else{
                $this->assign('userinfo',$user);
                $this->assign('id',$id);
                return $this->fetch();
            }
        }
    }

    public function del(){
        $id=input('param.id',0);
        $user = Db::name('user_info')->find($id);
        if($user['type']==0){
            $this->error('无法删除真实用户');
        }else{
            $val = Db::name('user_info')->where(['id'=>$id])->delete();
            if($val){
                $this->success('删除成功');
            }else{
                $this->success('删除失败');
            }
        }
    }


}