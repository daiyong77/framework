<?php
//文件操作(请注意最好不要用中文)
class file{
	public static $path='../';//当前项目路径
	//获取当前项目的绝对地址
	//path('文件相对项目地址或者绝对地址','没有文件夹则创建')
	//return 绝对地址
	public static function path($file=''){
		$root=__DIR__.'/'.self::$path;
		if(!$file){
			return $root;
		}else{
			if(!(strpos($file,'/')===0||preg_match('/^[A-Z]:/',$file))){
				return $root.$file;
			}else{
				return $file;
			}
			
		}
	}
	//写入文件
	//put('文件路径','文件内容','是否追加')
	//return 是否成功
	public static function put($file = '', $content = '',$append='') {
		$file=self::path($file);
		if (!is_dir(dirname($file))){
			mkdir(dirname($file), 0777, true);
		}
	    if(!is_file($file)){
	         touch($file);
	         chmod($file, 0777);
	    }
	    if($append){
	        $return=file_put_contents($file,$content.PHP_EOL,FILE_APPEND);
	    }else{
	        $return=file_put_contents($file,$content);
	    }
	    if(!$return&&$return!==0){
	        return false;
	    }
	    return true;
	}
	//获取文件信息
	public static function get($file){
		return file_get_contents(self::path($file));
	}
	//删除文件
	public static function delete($file){
		return unlink(self::path($file));
	}
	//导出或存储csv文件
	//exportCSV(路径,数据,是否需要有header设置)
	//return 输出或存储结果
	public static function exportCSV($file = '',$data=array(),$header_no=1) {
		//判断是导出还是保存
		$strpos=strpos($file,'/');
	    if(!$strpos&&$strpos!==0){//导出
	    	$char='utf-8';
			if(@strpos($_SERVER['HTTP_USER_AGENT'], 'Windows')){
				$char='gbk';
			}
	    }else{//保存(最终还是要到windows上看的所以默认保存为gbk)
	    	// $char='utf-8';
	    	// if(strtoupper(substr(PHP_OS,0,3))==='WIN'){
				$char='gbk';
			// }
	    }
	    $file=str_replace(array('\\',':','*','?','"','<','>','|',' '),'',$file);
	    $content=array();
	    foreach ($data as $k => $v) {
	        foreach($v as $k2=>$v2){
	            $v[$k2]=str_replace('"','""',$v[$k2]);
	            $v[$k2]=str_replace(PHP_EOL,' ',$v[$k2]);
	            $v[$k2]='"'.$v[$k2].'"';
	        }
	        $v=implode(',', $v);
	        if($char=='gbk') $v=iconv('utf-8','gbk',$v);
	        $content[]=$v;
	    }
	    $content=implode(PHP_EOL,$content);
	    if(!$strpos&&$strpos!==0){//导出
	        if(!$header_no){
	        	if($char=='gbk')$file=iconv('utf-8','gbk',$file);
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="'.$file.'"');
				header('Cache-Control: max-age=0');
	    	}
	        echo $content.PHP_EOL;
	    }else{//保存
			if($char=='gbk'){
				$file=iconv('utf-8', 'gbk', $file);
			}
	        return file::put($file,$content.PHP_EOL,'append');
	    }
	}
	//获取csv文件数据
	//getCsv(路径)
	//return array
	public static function getCSV($path){
		$data=self::get($path);
		$data=explode(PHP_EOL,$data);
		//判断编码
		$iconv=0;
		if($data[0]){
			$mbstring=mb_detect_encoding($data[0], array('UTF-8','GBK'));
			if($mbstring!='UTF-8'){
				$iconv=1;
			}
		}
		$csv=array();
		foreach($data as $k=>$v){
			//处理特殊字符
			$v=str_replace('""','__code[DoubleQuotationMark]__',$v);
			preg_match('/(^|,)"(.*?)"(,|$)/',$v,$match);
			if(strpos($match[2],',')&&$match[2]){
				$match[2]=str_replace(',','__code[comma]__',$match[2]);
				$v=str_replace($match[0],$match[1].'"'.$match[2].'"'.$match[3],$v);
			}
			if($iconv){
				$v=iconv('gbk','utf-8',$v);
			}
			//拆分
			$v=explode(',',$v);
			foreach($v as $k2=>$v2){
				$v2=str_replace(array('__code[DoubleQuotationMark]__','__code[comma]__'),array('"',','),$v2);
				$v2=preg_replace('/^"(.*?)"$/is','$1',$v2);
				$csv[$k][$k2]=$v2;
			}
		}
		return $csv;
	}
}