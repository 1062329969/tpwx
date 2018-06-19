<?php


namespace app\index\event;


class TemplateMessage
{
	public function sendTempMsg($tempKey,$dataArr,$access_token)
	{

		//积分变动通知
		if($tempKey==1)
		{
			$temp="{{first.DATA}}用户名：{{keyword1.DATA}}时间：{{keyword2.DATA}}积分变动：{{keyword3.DATA}}积分余额：{{keyword4.DATA}}变动原因：{{keyword5.DATA}}{{remark.DATA}}";
		}
		//购买通知
		elseif($tempKey==2) {
			$temp = "{{first.DATA}}商品信息：{{keyword1.DATA}}购买日期：{{keyword2.DATA}}{{remark.DATA}}";
		}
		//病情反馈提醒
		elseif($tempKey==3) {
			$temp = "{{first.DATA}}病人姓名：{{keyword1.DATA}}就诊时间：{{keyword2.DATA}}{{remark.DATA}}";
		}
		//发货提醒
		elseif($tempKey==4) {
			$temp = "{{first.DATA}}订单号：{{keyword1.DATA}}快递公司：{{keyword2.DATA}}快递单号：{{keyword3.DATA}}时间：{{keyword4.DATA}}{{remark.DATA}}";
		}
		//建议反馈回复通知
		elseif($tempKey==5) {
			$temp = "{{first.DATA}}回复内容：{{keyword1.DATA}}时间：{{keyword2.DATA}}{{remark.DATA}}";
		}
        //预约
		elseif($tempKey==6){
            $temp = "{{first.DATA}}就诊人：{{keyword1.DATA}}联系电话：{{keyword2.DATA}}就诊时间：{{keyword3.DATA}}就诊院部：{{keyword4.DATA}}{{remark.DATA}}";
        }




		//准备发送请求的数据
		$requestUrl = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$access_token;

		preg_match_all('{{(\w+)\.DATA}}', $temp, $preg);

		$content = $preg[1];
		$jsonData = '';
		foreach($dataArr as $k => $v)
		{
			if(in_array($k, $content)){
				$jsonData .= '"'.$k.'":{"value":"'.$v.'","color":"#000000"},';
			}
		}
		$jsonData = rtrim($jsonData,',');
		$data = "{".$jsonData."}";
		$sendData = '{"touser":"'.$dataArr["wecha_id"].'","template_id":"'.$dataArr["tempid"].'","url":"'.$dataArr["href"].'","topcolor":"#029700","data":'.$data.'}';
		return $this->postCurl($requestUrl,$sendData);

	}

	// Post Request
	function postCurl($url, $data){
		$ch = curl_init();
		$header = ["Accept-Charset"=>"utf-8"];
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		//curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$tmpInfo = curl_exec($ch);
		$errorno=curl_errno($ch);
		if ($errorno) {

			return array('rt'=>false,'errorno'=>$errorno);
		}else{
			$js=json_decode($tmpInfo,1);
			if ($js['errcode']=='0'){

				return array('rt'=>true,'errorno'=>0);
			}else {
				//exit('模板消息发送失败。错误代码'.$js['errcode'].',错误信息：'.$js['errmsg']);
				return array('rt'=>false,'errorno'=>$js['errcode'],'errmsg'=>$js['errmsg']);

			}
		}
	}

}