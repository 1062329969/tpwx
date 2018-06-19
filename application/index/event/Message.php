<?php

namespace app\index\event;

class Message
{

    protected $api;
    protected $pid;
    protected $dxappid;
    protected $signature;


    public function __construct()
    {
        $this->api='http://106.1008611sms.com/sms';
        $this->dxappid='';
        $this->signature='';
    }

    public function APIHttpRequestCURL($post_data,$method='post')
    {

        //$post_data['action']='send';
        //$post_data['signature']=$this->signature;
        $post_data['action']='send';
        $post_data['account']='yhyl922123';
        $post_data['password']='56988877';
        $post_data['rt'] = 'json';
//		$post_data['extno']='1069080123';

        if($method!='get')
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->api);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch,CURLOPT_HTTPHEADER,array("X-HTTP-Method-Override: $method"));
            if($method!='post'){
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
            }else{
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            }
        }
        else
        {
            $url=$this->api.'?'.http_build_query($post_data);
            $ch = curl_init($url) ;
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1) ;
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1) ;
        }
        $output = curl_exec($ch);
        curl_close($ch);
        // var_dump($output);die;
        return json_decode($output,true);
        // return $this->xmlToArray($output);
    }


    public function xmlToArray($xml){

        //禁止引用外部xml实体

        libxml_disable_entity_loader(true);

        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);

        $val = json_decode(json_encode($xmlstring),true);

        return $val;

    }

}