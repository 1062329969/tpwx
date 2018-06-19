<?php

namespace kdniao;

/**
 * Created by aibx.net.
 * User: wafu7969
 * Date: 2017/6/24
 * Time: 22:42
 * 快递鸟即时查询API
 * EBusinessID   电商ID
 * AppKey        电商加密私钥
 * ReqURL         请求url
 */

class Kdniao
{

	private $EBusinessID,$AppKey,$ReqURL;

	public function __construct($EBusinessID,$AppKey,$ReqURL="http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx")
	{
		$this->EBusinessID=$EBusinessID;
		$this->AppKey=$AppKey;
		$this->ReqURL=$ReqURL;

	}

	/**
	 * @param $orderCode订单编号
	 * @param $shipperCode快递公司编码
	 * @param $logisticCode物流单号
	 * @return
	 */

	public function orderTracesSubByJson($orderCode,$shipperCode,$logisticCode){

		$requestData="{'OrderCode': '".$orderCode."',".
			"'ShipperCode':'".$shipperCode."',".
			"'LogisticCode':'".$logisticCode."'}";

		$datas = array(
			'EBusinessID' => $this->EBusinessID,
			'RequestType' => '1002',
			'RequestData' => urlencode($requestData) ,
			'DataType' => '2',
		);
		$datas['DataSign'] = $this->encrypt($requestData, $this->AppKey);
		$result=$this->sendPost($this->ReqURL, $datas);

		return $result;
	}

	/**
	 *  post提交数据
	 * @param  string $url 请求Url
	 * @param  array $datas 提交的数据
	 * @return
	 */
	public function sendPost($url, $datas) {
		$temps = array();
		foreach ($datas as $key => $value) {
			$temps[] = sprintf('%s=%s', $key, $value);
		}
		$post_data = implode('&', $temps);
		$url_info = parse_url($url);
		if(empty($url_info['port']))
		{
			$url_info['port']=80;
		}
		$httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
		$httpheader.= "Host:" . $url_info['host'] . "\r\n";
		$httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
		$httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
		$httpheader.= "Connection:close\r\n\r\n";
		$httpheader.= $post_data;
		$fd = fsockopen($url_info['host'], $url_info['port']);
		fwrite($fd, $httpheader);
		$gets = "";
		$headerFlag = true;
		while (!feof($fd)) {
			if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
				break;
			}
		}
		while (!feof($fd)) {
			$gets.= fread($fd, 128);
		}
		fclose($fd);

		return $gets;
	}

	/**
	 * 电商Sign签名生成
	 * @param data内容
	 * @param appkey
	 * @returnDataSign签名
	 */
	public function encrypt($data, $AppKey)
    {
		return urlencode(base64_encode(md5($data.$AppKey)));
	}

}