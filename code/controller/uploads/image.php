<?php
// 图片裁剪与压缩
// $_GET=array(
// 	'cut'=>'1,1,200,200',//图片裁剪 x点1,y点1,宽200,高200
// 	'size'=>'200*200,100*100',//原始压缩比例居中裁切成100*100与200*200
// )
// form提交任意一个文件或者远程链接

class image extends common{
	public function indexPost(){
		$image_tmp=$this->getImageTmpPath();
		$this->resetImage($image_tmp);
		$size=getimagesize($image_tmp);
		$size['mime']=str_replace('image/','',$size['mime']);
		//设置存储路径
		$dir=$this->getDir($this->config['custom']['image_path_save'].date('Ym').'/'.date('d'));
		$filename=date('YmdHis').func::random();
		//根据需要的图片大小压缩图片
		$zoom=explode(',',@$_GET['size']);
		$zoom_array=array();
		foreach($zoom as $v){
			$v=explode('*',$v);
			$zoom_array[]=$v;
		}
		if($zoom_array){
			$editor = Grafika\Grafika::createEditor();
			$zoom_image=array();
			foreach($zoom_array as $k=>$v){
				$editor->open($image, $image_tmp);
				$editor->resizeFill($image , $v[0] , $v[1]);//居中裁剪
				$editor->save($image,$dir.$filename.'_'.$v[0].'_'.$v[1].'.'.$size['mime']);
				$zoom_image[]=$dir.$filename.'_'.$v[0].'_'.$v[1].'.'.$size['mime'];
			}
		}else{
			copy($image_tmp,$dir.$filename.'.'.$size['mime']);
			$zoom_image[]=$dir.$filename.'.'.$size['mime'];
		}
		//删除缓存图片
		file::delete($image_tmp);
		//替换成链接形式
		foreach($zoom_image as $k=>$v){
			$zoom_image[$k]=str_replace($this->config['custom']['image_path_save'], $this->config['custom']['image_path_http'], $v);
		}
		$this->success('上传成功',$zoom_image);
	}
	//将图片裁切成规定大小
	private function resetImage($image_tmp){
		//按照4个点裁切图片
		$size=getimagesize($image_tmp);
		if(!in_array(str_replace('image/','',$size['mime']),$this->config['custom']['image_ext'])){
			unlink($image_tmp);
			$this->error('不支持的图片格式');
		}
		//按照参数裁切图片
		$cut=explode(',',@$_GET['cut']);
		if(count($cut)==4){
			$editor = Grafika\Grafika::createEditor();
			$editor->open($image, $image_tmp);
			$editor->crop($image,$cut[2] ,$cut[3] , 'top-left' , $cut[0] , $cut[1]);
			$editor->save($image,$image_tmp);
		}
		//限制图片最大宽度
		if($size[0]>$this->config['custom']['image_max_width']){
			$editor = Grafika\Grafika::createEditor();
			$editor->open($image, $image_tmp);
			$editor->resizeExactWidth($image ,$this->config['custom']['image_max_width']);
			$editor->save($image,$image_tmp);
		}
	}
	//原始图片保存的位置
	private function getImageTmpPath(){
		$image_tmp=file::path('cache/tmp/'.time().func::random().'.tmp');
		$file=@$_FILES;
		if(!(is_array($file)&&$file)){
			//获取远程图片
			$image=@current($_POST);
			if(strpos($image,'http')!==0){
				$this->error('远程图片链接不可用');
			}
			$ext=@strtolower(end(explode('.',$image)));
			if(!in_array($ext,$this->config['custom']['image_ext'])){
				$this->error('不支持的图片格式');
			}
			$data=http::curl($image);
			if(!$data){
				$this->error('未找到需要上传的图片信息');
			}
			file::put($image_tmp,$data);
			$size=filesize($image_tmp);
			if($size>$this->config['custom']['image_max_size']*1024*1024){
				unlink($image_tmp);
				$this->error('图片过大,最大不能超过'.$this->config['custom']['image_max_size'].'M');
			}
		}else{
			//获取上传图片
			$file=current($file);
			$ext=@strtolower(end(explode('.',$file['name'])));
			if(!in_array($ext, $this->config['custom']['image_ext'])){
				$this->error('不支持的图片格式');
			}
			if(!$file['tmp_name']){
				$this->error('图片信息为空');
			}
			if($file['size']>$this->config['custom']['image_max_size']*1024*1024){
				$this->error('图片过大,最大不能超过'.$this->config['custom']['image_max_size'].'M');
			}
			file::put($image_tmp,'');
			move_uploaded_file($file['tmp_name'], $image_tmp);
		}
		return $image_tmp;
	}
}
