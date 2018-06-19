<?php
/**
 * Created by yongxianghui.net.
 * User: liuyixin
 * Date: 2018/1/25
 * 药品券
 */
namespace app\index\controller;

use app\admin\model\AboutModel;
use app\admin\model\ProtocolModel;
use app\index\model\ShipaddressModel;
use app\index\model\SmslogModel;
use app\index\model\UserInfoModel;
use Phpqrcode\QRcode;
use think\Db;
use think\Session;
class About extends Wap
{
    protected function _initialize()
    {
        //获得授权
        $this->getUserInfo();
        $this->assign('fansInfo',$this->fansInfo);
    }

    public function index(){
        $about = new AboutModel();
        $list = $about->select();
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function aboutinfo(){
        $id = input('param.id');
        $aboutinfo = Db::name('about')->find($id);
        $this->assign('aboutinfo',$aboutinfo);
        return $this->fetch();
    }

    public function uses(){
        $ProtocolModel = new ProtocolModel();
        $pcontent = $ProtocolModel->find();
        $this->assign('pcontent',$pcontent);
        return $this->fetch();
    }

    public function secret(){
        $about = Db::name('about')->find(3);
        $this->assign('about',$about);
        return $this->fetch();
    }
}