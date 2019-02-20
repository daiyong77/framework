<?php
class settings extends common{
	public function indexAction(){
		//缓存目录大小
		$size_cache=func::size($this->dirSize('cache'));
		$size_cache_tpl=func::size($this->dirSize(tpl::$path_cache));
		$this->display(array(
			'size_cache'=>$size_cache,
			'size_cache_tpl'=>$size_cache_tpl
		));
	}
	public function clearCacheAction(){
		//查询用户组
		$count=dir::delete('cache');
		if($count){
			$this->success('成功删除'.$count.'个缓存文件');
		}else{
			$this->error('缓存文件不存在');
		}
	}
	public function clearTplCacheAction(){
		//查询用户组
		$count=dir::delete(tpl::$path_cache);
		if($count){
			$this->success('成功删除'.$count.'个缓存文件');
		}else{
			$this->error('缓存文件不存在');
		}
	}
	private function dirSize($path){
		$list=dir::getFileList($path);
		$size=0;
		foreach($list as $k=>$v){
			$size+=filesize(file::path($v));
		}
		return $size;
	}

}
