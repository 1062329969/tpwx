<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 问答管理控制器
 */

namespace app\admin\controller;

use app\admin\model\AnswerImgsModel;
use app\admin\model\AnswerModel;
use app\admin\model\AskModel;
use app\admin\model\CommentModel;
use app\admin\model\AskImgsModel;
use app\admin\model\UserInfoModel;
use app\admin\model\DoctorModel;
use app\admin\model\ForumOnetypeModel;
use app\admin\model\ProjectModel;
use think\Db;
use think\Session;
use app\admin\model\LogModel;

class Members extends Admin

{

    protected $modelName = '';

    public function _initialize()
    {
        parent::_initialize();
        // $this->modelName = new MembersModel;
    }


    //列表
    private function index()
    {
        return $this->fetch();
    }

    //列表
    private function add()
    {
        return $this->fetch();
    }

    //列表
    private function alter()
    {
        return $this->fetch();
    }

    //列表
    private function del()
    {
        return $this->fetch();
    }
}