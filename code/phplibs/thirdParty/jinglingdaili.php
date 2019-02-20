<?php
//验证码识别
namespace thirdParty;
//http://www.jinglingdaili.com
//账号:17607137932 密码:daiqinger
class jinglingdaili{
	private $username='17607137932';
	private $password='daiqinger';
	private $address='{"\u6e56\u5317\u7701":["\u5341\u5830\u5e02","\u8346\u5dde\u5e02","\u54b8\u5b81\u5e02","\u8944\u9633\u5e02","\u968f\u5dde\u5e02","\u8346\u95e8\u5e02","\u5929\u95e8\u5e02","\u9ec4\u5188\u5e02","\u6b66\u6c49\u5e02","\u9ec4\u77f3\u5e02"],"\u5c71\u4e1c\u7701":["\u83cf\u6cfd\u5e02","\u5a01\u6d77\u5e02","\u6ee8\u5dde\u5e02","\u70df\u53f0\u5e02","\u65e5\u7167\u5e02","\u83b1\u829c\u5e02","\u6dc4\u535a\u5e02","\u67a3\u5e84\u5e02","\u6d4e\u5357\u5e02","\u804a\u57ce\u5e02","\u5fb7\u5dde\u5e02","\u4e34\u6c82\u5e02","\u6d4e\u5b81\u5e02"],"\u5c71\u897f\u7701":["\u5ffb\u5dde\u5e02","\u664b\u4e2d\u5e02"],"\u6d77\u5357\u7701":["\u6d77\u53e3\u5e02"],"\u9655\u897f\u7701":["\u5ef6\u5b89\u5e02"],"\u8fbd\u5b81\u7701":["\u978d\u5c71\u5e02","\u4e39\u4e1c\u5e02","\u76d8\u9526\u5e02","\u94c1\u5cad\u5e02","\u961c\u65b0\u5e02","\u8fbd\u9633\u5e02","\u672c\u6eaa\u5e02","\u629a\u987a\u5e02"],"\u6d59\u6c5f\u7701":["\u8862\u5dde\u5e02","\u6e29\u5dde\u5e02","\u5b81\u6ce2\u5e02","\u53f0\u5dde\u5e02","\u676d\u5dde\u5e02","\u91d1\u534e\u5e02","\u4e3d\u6c34\u5e02","\u821f\u5c71\u5e02","\u7ecd\u5174\u5e02","\u5609\u5174\u5e02","\u6e56\u5dde\u5e02"],"\u4e91\u5357\u7701":["\u897f\u53cc\u7248\u7eb3\u5dde","\u6606\u660e\u5e02","\u7389\u6eaa\u5e02","\u695a\u96c4\u5e02","\u7ea2\u6cb3\u54c8\u5c3c\u65cf\u5f5d\u65cf\u81ea\u6cbb\u5dde"],"\u6cb3\u5317\u7701":["\u4fdd\u5b9a\u5e02","\u90a2\u53f0\u5e02","\u90af\u90f8\u5e02","\u77f3\u5bb6\u5e84\u5e02"],"\u6cb3\u5357\u7701":["\u9e64\u58c1\u5e02","\u6f2f\u6cb3\u5e02","\u5e73\u9876\u5c71\u5e02","\u8bb8\u660c\u5e02","\u5357\u9633\u5e02","\u5f00\u5c01\u5e02","\u4e09\u95e8\u5ce1\u5e02","\u65b0\u4e61\u5e02","\u7126\u4f5c\u5e02","\u6fee\u9633\u5e02","\u9a7b\u9a6c\u5e97\u5e02","\u90d1\u5dde\u5e02","\u5468\u53e3\u5e02","\u6d4e\u6e90\u5e02","\u4fe1\u9633\u5e02"],"\u6c5f\u82cf\u7701":["\u8fde\u4e91\u6e2f\u5e02","\u5e38\u5dde\u5e02","\u6cf0\u5dde\u5e02","\u9547\u6c5f\u5e02","\u76d0\u57ce\u5e02","\u65e0\u9521\u5e02","\u626c\u5dde\u5e02","\u5f90\u5dde\u5e02","\u6dee\u5b89\u5e02","\u82cf\u5dde\u5e02","\u5357\u901a\u5e02","\u5357\u4eac\u5e02","\u5bbf\u8fc1\u5e02"],"\u5b89\u5fbd\u7701":["\u6dee\u5317\u5e02","\u9ec4\u5c71\u5e02","\u6c60\u5dde\u5e02","\u961c\u9633\u5e02","\u6dee\u5357\u5e02","\u5408\u80a5\u5e02","\u5ba3\u57ce\u5e02","\u94dc\u9675\u5e02","\u829c\u6e56\u5e02","\u5bbf\u5dde\u5e02","\u9a6c\u978d\u5c71\u5e02","\u4eb3\u5dde\u5e02"],"\u5e7f\u897f\u58ee\u65cf\u81ea\u6cbb\u533a":["\u6842\u6797\u5e02","\u5357\u5b81\u5e02","\u67f3\u5dde\u5e02"],"\u5409\u6797\u7701":["\u677e\u539f\u5e02"],"\u5e7f\u4e1c\u7701":["\u4e2d\u5c71\u5e02","\u73e0\u6d77\u5e02","\u5e7f\u5dde\u5e02","\u6df1\u5733\u5e02","\u4e91\u6d6e\u5e02","\u6885\u5dde\u5e02","\u4f5b\u5c71\u5e02","\u4e1c\u839e\u5e02","\u60e0\u5dde\u5e02","\u6e5b\u6c5f\u5e02","\u6cb3\u6e90\u5e02","\u6c5f\u95e8\u5e02","\u63ed\u9633\u5e02"],"\u5929\u6d25\u76f4\u8f96\u5e02":["\u5929\u6d25\u5e02"],"\u798f\u5efa\u7701":["\u4e09\u660e\u5e02","\u6cc9\u5dde\u5e02","\u5b81\u5fb7\u5e02","\u8386\u7530\u5e02","\u9f99\u5ca9\u5e02","\u6f33\u5dde\u5e02","\u53a6\u95e8\u5e02"],"\u9752\u6d77\u7701":["\u897f\u5b81\u5e02","\u6d77\u897f\u5e02"],"\u91cd\u5e86\u76f4\u8f96\u5e02":["\u91cd\u5e86\u5e02"],"\u4e0a\u6d77\u76f4\u8f96\u5e02":["\u4e0a\u6d77\u5e02"],"\u5317\u4eac\u76f4\u8f96\u5e02":["\u5317\u4eac\u5e02"],"\u6e56\u5357\u7701":["\u5a04\u5e95\u5e02","\u6e58\u6f6d\u5e02","\u6c38\u5dde\u5e02","\u5409\u9996\u5e02","\u8861\u9633\u5e02","\u6000\u5316\u5e02","\u5f20\u5bb6\u754c\u5e02","\u76ca\u9633\u5e02","\u5e38\u5fb7\u5e02","\u957f\u6c99\u5e02","\u90f4\u5dde\u5e02","\u682a\u6d32\u5e02","\u90b5\u9633\u5e02"],"\u9ed1\u9f99\u6c5f\u7701":["\u54c8\u5c14\u6ee8\u5e02","\u9f50\u9f50\u54c8\u5c14\u5e02"],"\u6c5f\u897f\u7701":["\u8d63\u5dde\u5e02","\u4e0a\u9976\u5e02","\u9e70\u6f6d\u5e02","\u666f\u5fb7\u9547\u5e02","\u65b0\u4f59\u5e02","\u629a\u5dde\u5e02"],"\u56db\u5ddd\u7701":["\u81ea\u8d21\u5e02","\u5fb7\u9633\u5e02","\u7709\u5c71\u5e02","\u6210\u90fd\u5e02","\u6cf8\u5dde\u5e02","\u5e7f\u5b89\u5e02","\u5e7f\u5143\u5e02","\u7ef5\u9633\u5e02","\u8fbe\u5dde\u5e02","\u5357\u5145\u5e02"],"\u5185\u8499\u53e4\u81ea\u6cbb\u533a":["\u9102\u5c14\u591a\u65af\u5e02","\u547c\u548c\u6d69\u7279\u5e02","\u4e4c\u6d77\u5e02"]}';
	public function __construct(){
		$this->address=json_decode($this->address,true);
	}
	//获取代理ip只有1分钟有效
	public function get($province='',$city='',$count=0){
		\http::$agent=array();
		if(!$province||!$city){
			$province=array_rand($this->address);
			$city=$this->address[$province][array_rand($this->address[$province])];
		}
		$data=\http::curl('http://120.25.150.39:8081/index.php/api/entry?method=proxyServer.generate_api_url&packid=&fa=&qty=1&time=100&pro='.urlencode($province).'&city='.urlencode($city).'&port=2&format=json&ss=1&css=&et=1&pi=1&co=1&dt=1');
		$data=@json_decode($data,true);
		if($data['success']=='true'){
			return $this->returnData($data,$province,$city);
		}
		$count++;
		if($count>=5){
			return array('status'=>0,'message'=>'获取ip代理失败','data'=>array());
		}
		sleep(1);
		return $this->get($province,$city,$count);
	}
	//获取代理ip6小时有效
	public function get6th($province='',$city='',$count=0){
		\http::$agent=array();
		if(!$province||!$city){
			$province=array_rand($this->address);
			$city=$this->address[$province][array_rand($this->address[$province])];
		}
		$data=\http::curl('http://120.25.150.39:8081/index.php/api/entry?method=proxyServer.generate_api_url&packid=&fa=&qty=1&time=5&pro='.urlencode($province).'&city='.urlencode($city).'&port=2&format=json&ss=1&css=&et=1&pi=1&co=1&dt=1');
		$data=@json_decode($data,true);
		if($data['success']=='true'){
			if(strtotime($data['data'][0]['ExpireTime'])>time()+6*3600){
				return $this->returnData($data,$province,$city);
			}
		}
		$count++;
		if($count>=5){
			return array('status'=>0,'message'=>'获取ip代理失败','data'=>array());
		}
		sleep(1);
		return $this->get6th($province,$city,$count);
	}
	public function getMoney(){
		return \http::curl('http://www.jinglingdaili.com/Users-getBalanceNew.html?appid=433&appkey=27542739b9be59b8a3348233b714000a');
	}
	private function returnData($data,$province,$city){
		return array(
			'status'=>1,
			'message'=>'获取ip代理成功',
			'data'=>array(
				'ip'=>$data['data'][0]['IP'],
				'port'=>$data['data'][0]['Port'],
				'expiry_time'=>$data['data'][0]['ExpireTime'],
				'province'=>$province,
				'city'=>$city,
				'username'=>$this->username,
				'password'=>$this->password
			)
		);
	}
}
