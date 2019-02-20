<?php
namespace thirdParty;
//获取快递信息
class kdniao{
	//http://www.kdniao.com/
	//账号:daiyong 密码:daiqinger

	private $id='1322481';//商户id
	private $apikey='aafea5de-ec5b-46c8-8cc9-9b3acd1f44f5';
	private $url_search_one='http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx';//单条查询接口

	//更具快递单号获取信息
	//getInfoByOrder(快递公司code,订单号)
	//return array
	public function getInfoByOrder($code,$order,$type='1002'){
		$request=json_encode(array(
			'OrderCode'=>'',
			'ShipperCode'=>$code,
			'LogisticCode'=>$order
		));
		$datas = array(
	        'EBusinessID' => $this->id,
	        'RequestType' => $type,
	        'RequestData' => urlencode($request) ,
	        'DataType' => '2',
	    );
	    $datas['DataSign'] = urlencode(base64_encode(md5($request.$this->apikey)));
		$result=\http::curl($this->url_search_one,array(
			'post'=>$datas,
			'timeout'=>3,
			'repeat'=>1
		));
		$data=@json_decode($result,true);
		$data=$this->returnList($data)
		if(!$data){
			return array('status'=>0,'message'=>'未获取到快递信息','data'=>array());
		}else{
			return array('status'=>1,'message'=>'获取成功','data'=>$data);
		}
		
	}
	private function returnList($data){
		if(!$data['Traces'])return array();
		$list=array();
		foreach($data['Traces'] as $k=>$v){
			$list[]=array(
				'time'=>$v['AcceptTime'],
				'info'=>$v['AcceptStation']
			);
		}
		return array_reverse($list);
	}
}

//基本快递:
// 顺丰速运	SF
// 百世快递	HTKY
// 中通快递	ZTO
// 申通快递	STO
// 圆通速递	YTO
// 韵达速递	YD
// 邮政快递包裹	YZPY
// EMS	EMS
// 天天快递	HHTT
// 京东快递	JD
// 优速快递	UC
// 德邦快递	DBL
//更多:http://www.kdniao.com/file/2018%E5%BF%AB%E9%80%92%E9%B8%9F%E6%8E%A5%E5%8F%A3%E6%94%AF%E6%8C%81%E5%BF%AB%E9%80%92%E5%85%AC%E5%8F%B8%E7%BC%96%E7%A0%81.xlsx