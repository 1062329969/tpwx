<?php


namespace app\index\event;


class Wxpay
{

	public function pay($appid,$apikey,$openid,$mch_id,$body,$order_id,$money,$notify_url)
	{
		//获得支付的参数
		$rand = md5(time().rand(1000,9999));
		$param["appid"] = $appid;
		$param["openid"] = $openid;
		$param["mch_id"] = $mch_id; //商户ID
		$param["nonce_str"] = $rand;
		$param["body"] = $body;
		$param["out_trade_no"] = $order_id; //订单编号，要保证不重复
		$param["total_fee"] = $money*100; //支付金额
		$param["spbill_create_ip"] = $_SERVER["REMOTE_ADDR"];
		$param["notify_url"] = $notify_url;
		$param["trade_type"] = "JSAPI";

		//签名算法：http://pay.weixin.qq.com/wiki/doc/api/index.php?chapter=4_3
		$signStr = 'appid='.$param["appid"]."&body=".$param["body"]."&mch_id=".$param["mch_id"]."&nonce_str=".$param["nonce_str"]."&notify_url=".$param["notify_url"]."&openid=".$param["openid"]."&out_trade_no=".$param["out_trade_no"]."&spbill_create_ip=".$param["spbill_create_ip"]."&total_fee=".$param["total_fee"]."&trade_type=".$param["trade_type"];
		$signStr = $signStr."&key=".$apikey; //apikey
		$param["sign"] = strtoupper(MD5($signStr));
		$data = '<xml>
					  <appid><![CDATA['.$param["appid"].']]></appid>
					  <openid><![CDATA['.$param["openid"].']]></openid>
					  <mch_id>'.$param["mch_id"].'</mch_id>
					  <nonce_str><![CDATA['.$param["nonce_str"].']]></nonce_str>
					  <body><![CDATA['.$param["body"].']]></body>
					  <out_trade_no><![CDATA['.$param["out_trade_no"].']]></out_trade_no>
					  <total_fee>'.$param["total_fee"].'</total_fee>
					  <spbill_create_ip><![CDATA['.$param["spbill_create_ip"].']]></spbill_create_ip>
					  <notify_url><![CDATA['.$param["notify_url"].']]></notify_url>
					  <trade_type><![CDATA['.$param["trade_type"].']]></trade_type>
					  <sign><![CDATA['.$param["sign"].']]></sign>
					</xml>';
		$postResult = $this->myCurl("https://api.mch.weixin.qq.com/pay/unifiedorder",$data);
		$postObj = simplexml_load_string($postResult, 'SimpleXMLElement', LIBXML_NOCDATA);

		$msg = "".$postObj->return_msg;

		if($msg == "OK")
		{

			$result["timestamp"] = time();
			$result["nonceStr"] = "".$postObj->nonce_str;  //不加""拿到的是一个json对象
			$result["package"] = "prepay_id=".$postObj->prepay_id;
			$result["signType"] = "MD5";
			$paySignStr = 'appId='.$appid.'&nonceStr='.$result["nonceStr"].'&package='.$result["package"].'&signType='.$result["signType"].'&timeStamp='.$result["timestamp"];
			$paySignStr = $paySignStr."&key=".$apikey;
			$result["paySign"] = strtoupper(MD5($paySignStr));
			return $result;
		}
		else
		{
			return false;
		}

	}

    //生成订单id
	/**
	 * @param $prefix  订单前缀
	 */

	public function orderId($prefix)
    {
        return $prefix.date('YmdHis',time()).rand(1000,9999).rand(1000,9999);  //订单Id
    }


	public function myCurl($url,$data)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$tmpInfo = curl_exec($ch);
		if (curl_errno($ch)) {
			return $tmpInfo;
		}
		curl_close($ch);
		return $tmpInfo;
	}

}