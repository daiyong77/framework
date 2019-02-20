<?php
class func{
	//随机数
	//random(随机数个数,随机字符串)
	//return string
	public static function random($count = 5,$string='123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
	    $random = '';
	    for ($i = 0; $i < $count; $i++) {
	    	if(function_exists('mb_strlen')){
		        $scount=mb_strlen($string,'utf-8');
		    }else{
		    	$scount=strlen($string);
		    }
	        $rand=mt_rand(0,$scount-1);
	        if(function_exists('mb_strlen')){
	        	$random.= mb_substr($string,$rand,1);
	    	}else{
	    		$random.= substr($string,$rand,1);
	    	}
	    }
	    return $random;
	}
	//获取大小带单位显示出来
	//size(number)
	//return 12 KB
	public static function size($size)  {   
		$kb = 1024;         // Kilobyte  
		$mb = 1024 * $kb;   // Megabyte  
		$gb = 1024 * $mb;   // Gigabyte  
		$tb = 1024 * $gb;   // Terabyte  
		if($size < $kb){   
		    return $size." B";  
		} else if($size < $mb){   
		    return round($size/$kb,2)." KB";  
		}else if($size < $gb){   
		    return round($size/$mb,2)." MB";  
		}else if($size < $tb){   
		    return round($size/$gb,2)." GB";  
		}else{   
		    return round($size/$tb,2)." TB";  
		}  
	}  
}