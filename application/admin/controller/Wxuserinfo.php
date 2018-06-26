<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

class Wxuserinfo
{
    //微信APPID
    public $appid = "wxcfe75992652aa90d";
    //微信appsecret
    public $appsecret = "edd9392d71737a06afdcdad026c111cc";
    //微信access_token
    public $get_access_token_url = "";
    //微信全部用户openid列表请求地址
    public $get_user_openid_Lists_url = "";
    //创建标签请求地址
    public $create_tag_url = "";
    //获取已创建标签请求地址
    public $get_tags_url = "";
    //删除标签请求地址
    public $del_tags_url = "";
    //获取指定用户下所有的标签请求地址
    public $get_tag_user_url = "";
    //创建标签请求地址
    public $create_some_tags_url = "";
    //批量取消标签请求地址哦
    public $cannel_some_tags_url = "";
    //获取用户身上所有标签请求地址
    public $get_user_tags_url = "";
    //根据用户openID批量获取用户基本信息
    public $get_user_json_url = "";
    //批量获取用户基本信息
    public $get_user_info = "";
    //微信用户openid
    public $openid = "";


    
    public function __construct()
    {
        //access_token请求地址
        $this->get_access_token_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $this->appid . "&secret=" . $this->appsecret;
        //获取access_token
        $this->access_expire();
        //获取用户列表URL
        $this->get_user_openid_Lists_url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=" . $this->access_token;
        //创建标签请求地址
        $this->create_tag_url = "https://api.weixin.qq.com/cgi-bin/tags/create?access_token=" . $this->access_token;
        //获取标签请求地址
        $this->get_tags_url = "https://api.weixin.qq.com/cgi-bin/tags/get?access_token=" . $this->access_token;
        //编辑标签请求地址
        $this->editor_tags_url = "https://api.weixin.qq.com/cgi-bin/tags/update?access_token=" . $this->access_token;
        //删除标签请求地址
        $this->del_tags_url = "https://api.weixin.qq.com/cgi-bin/tags/delete?access_token=" . $this->access_token;
        //获取指定标签下的所有用户请求地址
        $this->get_tag_user_url = "https://api.weixin.qq.com/cgi-bin/user/tag/get?access_token=" . $this->access_token;
        //批量创建标签请求地址
        $this->create_some_tags_url = "https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token=" . $this->access_token;
        //批量取消一些标签请求地址
        $this->cannel_some_tags_url = "https://api.weixin.qq.com/cgi-bin/tags/members/batchuntagging?access_token=" . $this->access_token;
        //获取用户身上所有标签请求地址
        $this->get_user_tags_url = "https://api.weixin.qq.com/cgi-bin/tags/getidlist?access_token=" . $this->access_token;
        //根据openID批量获取用户基本信息
        $this->get_user_json_url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=" . $this->access_token;
        //批量获取用户基本信息
        $this->get_user_info_url = "https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=" . $this->access_token;
        //获取一个用户基本信息表请求地址
        $this->get_one_user_info="https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=zh_CN";

    }

    /**
     * 判断有效期没有重新获取access_token
     */
    public function access_expire()
    {

        if (!isset($_SESSION['access_token']) || $_SESSION['access_token'][1] < time()) {
            $this->get_access_token();
            $_SESSION['access_token'] = [$this->access_token, time() + 7200];
        } else {
            $this->access_token = $_SESSION['access_token'][0];
        }
    }


    /**
     * 获取access_token
     */
    public function get_access_token()
    {
        $info = httpGet($this->get_access_token_url);
        $jsoninfo = json_decode($info, true);
        $this->access_token = $jsoninfo["access_token"];
    }

    /**
     * 获取全部用户列表的openid
     * @return array
     */
    public function get_user_openid_Lists()
    {

        $openids = httpGet($this->get_user_openid_Lists_url);

        if (isset($openids)) {
            $openids_arr = json_decode($openids, true);
        }
        if (isset($openids_arr['data']) && !empty($openids_arr['data']['openid'])) {
            $user_info_list[] = $openids_arr;
        }
        //使用next_openid循环读取全部用户
        if (!empty($openids_arr['next_openid'])) {
            while (true) {
                $this->get_user_openid_Lists_url = $this->get_user_openid_Lists_url . "&next_openid=" . $openids_arr['next_openid'];
                $next_user_open_lists = httpGet($this->get_user_openid_Lists_url);
                $user_info_list[] = json_decode($next_user_open_lists, true);
                if (empty($next_user_open_lists['data']['openid'])) {
                    break;
                }
            }
        }
        return !empty($user_info_list) ? $user_info_list : [];
    }

    /**
     * 微信创建标签
     * @param $tag_name 标签名
     * @return bool|mixed
     */
    public function create_tag($tag_name)
    {
        if (empty($tag_name)) {
            return false;
        }
        $tag_data_json = json_encode(['tag' => ['name' => $tag_name]]);
        $results = curl_post($this->create_tag_url, $tag_data_json);
        return $results;
    }

    /**
     * 获取所有标签
     * @return bool|mixed
     */
    public function get_tags()
    {
        $results_tags = httpGet($this->get_tags_url);
        return $results_tags;
    }

    /**
     * 编辑标签
     * @return bool|mixed
     */
    public function editor_tags($tag_json)
    {
        if (empty($tag_json)) {
            return false;
        }
        $results_edit_tags = json_decode(curl_post($this->editor_tags_url, $tag_json), true);
        return $results_edit_tags['errcode'] == 0 ? true : false;
    }

    /**
     * 删除标签
     * @param $tag_json
     * @return bool
     */
    public function del_tags($tag_json)
    {
        if (empty($tag_json)) {
            return false;
        }
        $results_edit_tags = json_decode(curl_post($this->del_tags_url, $tag_json), true);
        return $results_edit_tags['errcode'] == 0 ? true : false;
    }

    /**
     * 获取标签下粉丝列表
     * @param string $tag_id
     * @return bool
     */
    public function get_tag_user($tag_id = '')
    {
        if (empty($tag_id)) {
            return false;
        }
        $results_tags_user = curl_post($this->get_tag_user_url, $tag_id);
        return $results_tags_user ? $results_tags_user : false;
    }

    /**
     * //批量打印标签
     * @param $openid_lists openid列表}
     */
    public function create_some_tags($openid_lists)
    {
        if (empty($openid_lists)) {
            return false;
        }
        $some_user = curl_post($this->create_some_tags_url, $openid_lists);
        $some_user = json_decode($some_user, true);
        if (isset($some_user['errcode']) && $some_user['errcode'] == 0) {
            return true;
        } else {
            return false;
        }

    }


    /**
     * //批量为用户取消标签
     * @param $openid_lists openid列表
     */
    public function cannel_some_tags($openid_lists)
    {
        if (empty($openid_lists)) {
            return false;
        }
        $some_user = curl_post($this->cannel_some_tags_url, $openid_lists);
        $some_user = json_decode($some_user, true);
        if (isset($some_user['errcode']) && $some_user['errcode'] == 0) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * 获取用户身上的标签
     * @param $openid
     * @return bool
     */
    public function get_user_tags($openid)
    {
        if (empty($openid)) {
            return false;
        }
        $some_user = curl_post($this->get_user_tags_url, $openid);
        $some_user = json_decode($some_user, true);
        if (isset($some_user['tagid_list']) && !empty($some_user['tagid_list'])) {
            return $some_user;
        } else {
            return false;
        }
    }


    /**
     * 批量获取openid
     * @return mixed
     */
    public function get_user_json($data)
    {
        if(empty($data)){
            return false;
        }
        $data=json_encode($data);
        $result = curl_post($this->get_user_json_url,$data);
        $jsoninfo1 = json_decode($result, true);
        return $jsoninfo1;
    }

    /**
     * 批量获取用户基本信息
     * @return mixed
     */
    public function get_user_info($data)
    {
        if(empty($data)){
            return false;
        }
        $data=json_encode($data);
        $result = curl_post($this->get_user_info_url,$data);
        $jsoninfo1 = json_decode($result, true);
        return $jsoninfo1;
    }

    /**
     * 获取一个用户基本信息
     * @param $openid
     */
    public  function get_one_user_info($openid)
    {
        //替换变量
        $usr=sprintf($this->get_one_user_info,$this->access_token,$openid);
        //发起请求
        $wx_user_info=httpGet($usr);
        //返回值
        return $wx_user_info;
    }

}

?>
