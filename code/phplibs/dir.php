<?php
//文件操作(请注意最好不要用中文)
class dir{
	public static $path='../';//当前项目路径
	public static function path($path){
		if(!preg_match('/\/$/',$path)){
			$path=$path.'/';
		}
		$root=__DIR__.'/'.self::$path;
		if(!$path){
			return $root;
		}else{
			if(!(strpos($path,'/')===0||preg_match('/^[A-Z]:/',$path))){
				return $root.$path;
			}else{
				return $path;
			}
			
		}
	}
	//获取文件夹下的树状目录
	//getTree('文件夹路径')
	//return tree
	public static function getTree($path){
		$path=self::path($path);
		$dir=scandir($path);
		$dir_list=array();
		foreach($dir as $v){
			if($v!='.'&&$v!='..'){
				$dir_list[]=$v;	
			}
		}
		foreach($dir_list as $k=>$v){
			if(!is_file($path.$v)){
				unset($dir_list[$k]);
				$dir_list[$v]=self::getTree($path.$v.'/');
			}
		}
		return $dir_list;
	}
	//获取文件夹下的目录
	//getFileList('文件夹路径')
	//return array
	public static function getFileList($path,$tree=array()){
		if(!preg_match('/\/$/',$path)){
			$path=$path.'/';
		}
		if(!$tree){
			$tree=self::getTree($path);
		}
		$path_father=array();
		$path_child=array();
		foreach($tree as $k=>$v){
			if(is_array($v)){
				$path_child=array_merge($path_child,self::getFileList($path.$k,$v));
			}else{
				$path_father[]=$path.$v;
			}
		}
		return array_merge($path_father,$path_child);
	}
	//删除文件夹下面所有的文件
	//delete('文件夹路径')
	//return count
	public static function delete($path){
		$data=self::getFileList($path);
		$count=0;
		foreach($data as $v){
			$count+=file::delete($v);
		}
		return $count;
	}
}