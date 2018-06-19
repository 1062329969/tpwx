<?php

namespace app\index\model;


use think\Db;
use think\Model;

class TipsModel extends Model
{
    protected $name='tips';
    protected $pk='t_id';

    public static function gettipsbyid($tid='')
    {
        return Db::name('tips')->find($tid);
    }

    public function gettipsbysum($sum){
        return Db::name('tips')->where("t_totalscore_s <= ".$sum." and t_totalscore_e>=".$sum)->find();
    }

    public function gettipsbykeyandsum($sum,$t_type){
        return Db::name('tips')->where("t_totalscore_s <= ".$sum." and t_totalscore_e>=".$sum)->where('t_type',$t_type)->find();
    }
}