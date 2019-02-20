<?php
namespace thirdParty;
//获取ip信息
class ipInfo{
	//从淘宝获取ip信息(获取ip信息比较慢请在后台的时候使用)
	public static function get($ip='myip') {
	    $info= @json_decode(\http::curl('http://ip.taobao.com/service/getIpInfo.php?ip='.$ip),true);
	    if($info['code']===0||$info['code']==='0'){
	    	$data=array(
                'ip'=>$ip,
                'country'=>$info['data']['country'],
                'province'=>$info['data']['region'],
                'city'=>$info['data']['city'],
                'district'=>$info['data']['county'],
                'isp'=>$info['data']['isp']
            );
	    	return array('status'=>1,'message'=>'获取ip信息成功','data'=>$data);
	    }else{
	    	return array('status'=>0,'message'=>'获取ip信息失败','data'=>$info);
	    }
	}
}