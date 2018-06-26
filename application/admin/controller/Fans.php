<?php
/**
 * Created by yongxianghui.net.
 * User: wafu7969
 * Date: 2018/1/11
 * 问答管理控制器
 */
namespace app\admin\controller;

use app\admin\controller\Wxuserinfo;
use app\admin\model\WxUserInfoModel;
use app\admin\model\FansModel;
use think\Db;
use think\Validate;
use think\Session;
use app\admin\model\LogModel;

class Fans extends Admin

{
    protected $modelName = '';
    protected $wxmodel = '';

    public function _initialize()
    {
        parent::_initialize();
        //$this->modelName=new FansModel;
        $this->wxmodel = new Wxuserinfo();
    }

    //首页列表页面
    public function index()
    {
        $request = request();
        if ($request->method() == 'POST') {
            $param = input('post.');
            //定义 条件数组 规则数组
            $data_check=$this->get_where_serch($param);
            //条件验证合法性
            $validate = new Validate($data_check[1]);

            if (!$validate->check($data_check[0]))
            {
                dump($this->error($validate->getError()));return;
            }
        }else{
            $where='1=1';
        }

        //查询分页数据
        $wx_user_info = WxUserInfoModel::get_where($where);
        //传递分页数据
        $this->assign('wx_user_info', $wx_user_info);

        return $this->fetch();
    }

    //添加粉丝
    public function add()
    {
        $this->fetch();
    }

    //删除粉丝
    public function del()
    {
        $this->fetch();
    }

    //同步粉丝数据
    public function alter()
    {
        $request = request();
        //post请求处理
        if ($request->method() == 'POST')
        {
            $param = input('post.');
            if(!empty($param['openids']))
            {
                //解析数组组装openid条件
                $open_ids=$param['openids'];
                //根据指定条件更新数据
                $up_user_ino=$this->always_get_wx_user($open_ids);
                //成功跳转
                if(true==$up_user_ino)
                {
                    $this->success("同步成功");
                }
            }
        }
        $this->error("缺少参数");
        $this->fetch();
    }

    //分组页面查询
    public function group_tag()
    {
        $request = request();
        if ($request->method() == 'POST')
        {
            $param = input('post.');

            $group_id = $param['group_id'];

            $group_id = intval($group_id);

            //检查有没有分组标签id
            if ($group_id >= 0)
            {
                $regex['groupid'] = $param['groupid'];
            }
            //检查有没有关注状态
            if (true == $param['subscribe'])
            {
                $regex['subscribe'] = $param['subscribe'];
            }
            //检查并且生成模糊查找条件
            if (isset($param['find_type']))
            {
                //如果是数字
                if (is_int($param['find_type']))
                {
                    $where['id'] = ['like', '%%'];
                }
                //如果有汉子字母下滑线
                if (preg_match('/^[\x{4e00}-\x{9fa5}a-zA-Z0-9\_\-]+$/u', $param['find_type']))

                {
                    $where['nickname'] = ['like', '%%'];
                }
            }
        }
        else
        {
            $param = input('post.');
            if (isset($param['group_id']))
            {
                $where['groupid'] = $param['group_id'];
            } else {
                $where = [1 => 1];
            }
        }
        //根据条件查询数据
        $wx_user_info = WxUserInfoModel::get_where($where);
        //穿死数据到视图
        $this->assign('wx_user_info', $wx_user_info);

        $this->fetch();
    }


    /**
     * 拼接搜索条件
     * @param $param
     * @return array  返回值
     */
    public function get_where_serch($param){
        $where = $regex = [];
        //判断有没有姓名
        if (true == $param['name']) {
            $where['nickname'] = $param['name'];
            $regex['nickname'] = 'require|max:50';
        }
        //判断有没有会员ID
        if (true == $param['id']) {
            $where['id'] = $param['id'];
            $regex['id'] = 'number';
        }
        //判断有没有手机
        if (true == $param['phone']) {
            $where['phone'] = $param['phone'];
            $regex['phone'] = 'number|length:11';
        }
        //判断有没有粉丝昵称
        if (true == $param['nickname']) {
            $where['nickname'] = $param['nickname'];
        }
        //判断有没有openid
        if (true == $param['openid']) {
            $where['openid'] = $param['openid'];
            $regex['openid'] = 'length:28';
        }
        //判断有没有关注时间开始时间
        if (true == $param['start_time']) {
            $where['subscribe_time'] = ['>=', strtotime($param['start_time'])];
        }
        //判断有没有关注时间结束时间
        if (true == $param['over_time']) {
            $where['subscribe_time'] = ['>=', strtotime($param['over_time'])];
        }
        //判有没有关注状态
        if (true == $param['fido'] && 2 !=$param['fido']) {
            $where['subscribe'] = $param['fido'];
            $regex['subscribe'] = 'number';
        }
        return [$where,$regex];
    }
    /**
     * 读取微信粉丝用户插入数据库
     * @return array
     */
    private function wx_user_get_insert($openid = [])
    {
        //实例化微信用户管理类
        $wx_model = new Wxuserinfo();
        //获取全部用户数据
        $user_lists = $wx_model->get_user_openid_Lists();

        //组装openid数组
        foreach ($user_lists as $wx_k => $wx_v) {

            if (isset($wx_v['data']) && !empty($wx_v['data']['openid'])) {
                foreach ($wx_v['data']['openid'] as $wx_k_c => $wx_v_c) {
                    $wx_user_openids[] = $wx_v_c;
                }
            }
        }
        return !empty($wx_user_openids) ? $wx_user_openids : [];
    }


    /**
     * 获取请求条件
     * @param $open_ids
     * @return array
     */
    private function get_request_str($open_ids)
    {
        if (!empty($open_ids)) {
            foreach ($open_ids as $wx_r => $wx_v) {
                $user_list["user_list"][] = ["openid" => $wx_v, "lang" => "zh_CN"];
            }
        }
        return !empty($user_list) ? $user_list : [];
    }

    /**
     * 批量验证数据
     * @param $rule  验证规则
     * @param $msg   错误提示信息
     * @param $data  验证数据
     * @return mixed 返回值bool
     */
    public function check_some($data, $rule = null, $msg = null)
    {
        $rule = [
            'subscribe' => 'require|number|length:1',
            'openid' => 'require|length:28',
            'nickname' => 'require|max:50',
            'sex' => 'require|max:4',
            'language' => 'require|max:50',
            'city' => 'max:50',
            'province' => 'max:50',
            'country' => 'require|max:50',
            'headimgurl' => 'require|max:200',
            'subscribe_time' => 'require|number|max:11',
            'remark' => 'length:0,59',
            'groupid' => 'require|max:11',

        ];
        $validate = new Validate($rule);
        $result = $validate->batch()->check($data[0], $rule);
        return $result;
    }

    /**
     *实时更新微信用户数据数据
     */
    public function always_get_wx_user()
    {
        //接受参数判断是否有openid
        $open_ids = func_get_args();
        //判断是否设置指定openid
        if (isset($open_ids[0]) && isset($open_ids[0]['openid'])) {
            //传递了直接获取指定的
            $open_ids = $open_ids[0];
        } else {
            //获取用户列表全部openID
            $open_ids = $this->wx_user_get_insert();
        }

        //没有获取到直接返回
        if (!isset($open_ids) && empty($open_ids)) {
            return false;
        }
        //生成请求数据
        $data = $this->get_request_str($open_ids);

        //根据openid获取用户基本信息
        $user_info = $this->wxmodel->get_user_info($data);

        //插入数据
        if (!empty($user_info['user_info_list'])) {

            //检查数据
            $res = $this->check_some($user_info['user_info_list']);

            if (true == $res) {

                //实例化微信用户类
                $wx_user_info_model = new WxUserInfoModel();

                //异常处理
                try
                {
                    //循环遍历查询有就更新无则插入
                    foreach($user_info['user_info_list'] as $m_k=>$m_v)
                    {
                        if($user_info=Db::name("wx_user_info")->where('openid',$m_v['openid'])->find())
                        {
                            $user_id=$user_info['id'];
                            $wx_user_info_model->where('id',$user_id)->update($m_v);
                        }else{
                            Db::name("wx_user_info")->insert($m_v);
                        }

                    }
                }
                catch (\Exception $e)
                {
                    return false;
                }
                return true;
            }
        }
        return false;
    }


    /*执行定时任务*/
    public function timer()
    {
        ignore_user_abort();//关掉浏览器，PHP脚本也可以继续执行.
        set_time_limit(3000);// 通过set_time_limit(0)可以让程序无限制的执行下去
        $interval = 5;// 每隔5s运行
        do {
            echo '测试' . time() . '<br/>';
            sleep($interval);// 等待5s
        } while (true);

    }

    /*打标签*/
    public function add_tags()
    {
        $request = request();
        if ($request->method() == 'POST') {
            //接受数据
            $openids = input('post.openids');
            //要打印的tagid
            $tag_id = input('post.tagid');
            //获取请求求条件
            $request_openid_str = $this->add_tag_str($openids, $tag_id);

            if (true == $request_openid_str) {
                $this->success("打印成功");
            }
        }
        $this->success("缺少参数");

    }

    /*同步粉丝*/
    public function synchro_fans()
    {
        //接受要同步用户的openid
        $request = request();
        if ($request->method() == 'POST') {
            //接收参数
            $openids = input('post.openids');
            //实时更新数据
            $results = $this->always_get_wx_user($openids);
            if (true == $results) {
                $this->success("同步成功");
            } else {
                $this->error("同步失败");
            }
        }
    }

    /*拼接批量打印标签条件*/
    private function add_tag_str($openid, $tag_id)
    {
        if (empty($openid)) return false;

        foreach ($openid as $k => $v) {

            $openids[] = $v;
        }

        if (!emmpty($openids)) return ["openid_list" => $openids, "tagid" => $tag_id];

    }


}