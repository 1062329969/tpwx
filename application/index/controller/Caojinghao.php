<?php
/**
 * Created by yongxianghui.net.
 * User: liuyixin
 * Date: 2018/1/25
 * 药品券
 */
namespace app\index\controller;
use app\index\model\ShipaddressModel;

use app\index\model\SmslogModel;

use app\index\model\UserInfoModel;

use Phpqrcode\QRcode;

use think\Db;

use think\Session;

class Caojinghao extends Wap

{

    protected function _initialize()

    {

        //获得授权

        $this->getUserInfo();

        $this->assign('fansInfo',$this->fansInfo);

    }



    public function demo1(){

        return $this->fetch();

    }

    public function demo2(){

        return $this->fetch();

    }

    public function demo3(){

        return $this->fetch();

    }

    public function demo4(){

        return $this->fetch();

    }

    public function demo5(){

        return $this->fetch();

    }

    public function demo6(){

        return $this->fetch();

    }

    public function demo7(){

        return $this->fetch();

    }

    public function demo8(){

        return $this->fetch();

    }

    public function demo9(){

        return $this->fetch();

    }

    public function demo10(){

        return $this->fetch();

    }
     public function demo11(){

        return $this->fetch();

    }
    public function demo12(){

        return $this->fetch();

    }
    public function demo13(){

        return $this->fetch();

    }
     public function demo14(){

        return $this->fetch();

    }
     public function demo15(){

        return $this->fetch();

    }
     public function demo16(){

        return $this->fetch();

    }
    
}
?>
