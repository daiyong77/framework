<?php
//必须在上级目录有cache文件夹并且权限为777
//模板中引用的路径请用__STATIC__开头,后面为view根目录
//模板变量书写详见第66行
//页面引用方法：<include path="common/header.html" /> 或作为base <include path="__CONTENT__"/>
//页面引用传值方法：<include path="common/header.html" title="{$title}" xxx="xxx" /> common/header.html中直接使用{:title} 即可获取到传递过来的参数
//页面之间传递变量方法2：当前页随便一个位置写入（最好放在开头） <!--{:title($title.' - 式例')}--> 其他或当前页面直接为 {:title} 即可获取到参数
class tpl{
    public static $path_cache='cache/view/';//相对项目的缓存路径
    public static $style='view/';//相对于项目的模板路径（该值请在config.php中设置）
    public static $base='';//base设置模板路径下的文件
    public static $replace=array();//模板替换标签
    //显示模版信息
    //display($path_html下的文件路径不带后缀,$path_html下的base文件目录)
    //无返回直接输出模板
    public static function display($tpl='',$data=array()){
        if(is_array($data)){
            foreach($data as $k=>$v){
                $$k=$v;
            }
        }
        require_once(self::getCache($tpl,self::$base));
        exit;
    }
    //生成模板缓存$tpl为display传过来的参数
    //getCache(display传过来的参数,$base)
    //return 缓存文件路径
    private static function getCache($tpl,$base){
        $path=file::path();
        $path_cache_file=$path.self::$path_cache."{$tpl}.php";
        if(file_exists($path_cache_file)&&$_SERVER['REMOTE_ADDR']!='127.0.0.1'){
            return $path_cache_file;
        }
        if($base){
            $base=@file_get_contents($path.$base);
            if(!$base||!strpos($base,'__CONTENT__')){
                exit('未找到base文件或base文件中没有__CONTENT__变量'.PHP_EOL);
            }
            $content=str_replace('__CONTENT__',$tpl.'.html',$base);
        }else{
            $path_file=$path.self::$style.$tpl.'.html';
            if(!file_exists($path_file)){
                exit('未找到模板文件'.$path_file.PHP_EOL);
            }
            $content=file_get_contents($path_file);
        }
        $content=self::getIncludeFile($content,$path.self::$style);
        //替换（$title|<!--{:title($title)}-->）页面中附值的参数
        preg_match_all('/{:([\w]+)}/',$content,$match);
        preg_match_all('/<!--{:([\w]+)\((.*?)\)}-->/',$content,$match_r);
        foreach($match_r[1] as $k=>$v){
            $replace[$v]=$match_r[2][$k];
        }
        foreach($match[1] as $v){
            if(isset($replace[$v])&&$replace[$v]){
                $content=preg_replace('/{:'.$v.'}/', '<?php echo '.htmlspecialchars($replace[$v]).'; ?>',$content, 1);
            }
        }
        $content=preg_replace('/<!\-\-{:([\w]+)\(.*?\)}\-\->/','',$content);
        //判断domain替换引用
        if(self::$replace){
            foreach(self::$replace as $k=>$v){
                $content=str_replace($k,$v,$content);
            }
        }
        $content=preg_replace(array(
            '/<foreach condition="\(\$(.*?) as \$(.*?)\)">/',
            '/<(foreach|if|for) condition="\((.*?)\)">/',
            '/<else(| )\/>/',
            '/<\/(foreach|if|for)>/',
            '/<elseif condition="\((.*?)\)"(| )\/>/',

            '/{if\((.*?)\)}/',
            '/{else(| )if\((.*?)\)}/',
            '/{else}/',
            '/{\/if}/',

            '/{\$(.*?)}/',
            '/{([\w]+)\((.*?)\)}/',
            '/{\(([\w]+)\)\$(.*?)}/',

            '/<php>/',
            '/<\/php>/',

            '/{:([\w]+)}/',//将模板传参中未定义的数据去除
        ),array(
            '<?php if(!isset($$1)||!is_array($$1))$$1=array();foreach($$1 as $$2){ ?>',
            '<?php $1($2){ ?>',
            '<?php }else{ ?>',
            '<?php } ?>',
            '<?php }elseif($1){ ?>',

            '<?php if($1){ ?>',
            '<?php }else$1if($2){ ?>',
            '<?php }else{ ?>',
            '<?php } ?>',

            '<?php echo htmlspecialchars($$1); ?>',
            '<?php echo $1($2); ?>',
            '<?php echo ($1)$$2; ?>',

            '<?php ',
            ' ?>',

            '',
        ),$content);
        //建立缓存文件
        // $content=preg_replace('/>.*?</s','><',$content);
        file::put($path_cache_file,$content);
        return $path_cache_file;
    }
    //循环获取模板中被引用的模板信息
    //getIncludeFile(内容,文件目录)
    //return string
    private static function getIncludeFile($content,$path_dir){
        preg_match_all('/<include path="([\w\/\.\-]+)"(.*?)\/>/',$content,$matchs);
        foreach($matchs[1] as $k=>$v){
            if(!file_exists($path_dir.$v)){
                exit('未找到被引用的模板'.$path_dir.$v.PHP_EOL);
            }
            $include=file_get_contents($path_dir.$v);
            if(strlen($matchs[2][$k])>=5){
                preg_match_all('/([\w]+)="(.*?)"/',$matchs[2][$k],$data);
                if(count($data[1])>0){
                    foreach($data[1] as $k2=>$v2){
                        $data[1][$k2]='{:'.$v2.'}';
                    }
                    $include=str_replace($data[1], $data[2], $include);
                }
            }
            $content=str_replace($matchs[0][$k],$include,$content);
        }
        preg_match_all('/<include path="([\w\/\.\-]+)"(.*?)\/>/',$content,$matchs);
        if($matchs[1]){
            $content=self::getIncludeFile($content,$path_dir);
        }
        return $content;
    }
}