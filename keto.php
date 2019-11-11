<?php
//目标站网址
$url="https://weichitianchang.en.alibaba.com";

//当前文件名
$thisname='index.php';

header("Content-type: text/html; charset=GBK");
$server_url = preg_replace("/(http|https|ftp|news):\/\//i", "", $url);
$server_url = preg_replace("/\/.*/", "", $server_url);
$server_url = 'http://'.$server_url;
$getid=$_SERVER["REQUEST_URI"];
$scriptname=$_SERVER["SCRIPT_NAME"];
$thisurl="http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
//var_dump($server_url,$thisurl);die;
if( preg_match('#(http|https|ftp|news):#iUs',$getid) ){
 header("Location:".$scriptname);
 exit;
}
if( preg_match('#'.$scriptname.'\/#iUs',$getid) ){
 $url=$server_url.'/'.str_ireplace($scriptname."/",'',stristr($getid,$scriptname."/"));
}
$thismulu=str_ireplace(stristr($_SERVER['PHP_SELF'],$thisname),'',$thisurl);
function curl_get($url){
  if(function_exists('curl_init') && function_exists('curl_exec')){
   $ch = curl_init();
   $user_agent = "Mozilla/5.0+(compatible;+Baiduspider/2.0;++http://www.baidu.com/search/spider.html)";
   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_REFERER, "http://www.baidu.com/s?wd=%CA%B2%C3%B4");
   curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
   $data = curl_exec($ch);
   curl_close($ch);
  }else{
   for($i=0;$i<3;$i++){
    $data = @file_get_contents($url);
    if($data) break;
   }
  }
  return $data;
}
function filter($str){
 global $server_url;
 $str=preg_replace("/<base[^>]+>/si","",$str);
 $str=preg_replace("/\s+/", " ", $str);
 $str=preg_replace("/<\!--.*?-->/si","",$str);

 //$str=preg_replace("/<(script.*?)>(.*?)<(\/script.*?)>/si","",$str);
 //$str=preg_replace("/<(\/?script.*?)>/si","",$str);

 //$str=preg_replace("/javascript/si","Javascript",$str);
 $str=preg_replace("/vbscript/si","Vbscript",$str);
 $str=preg_replace("/on([a-z]+)\s*=/si","On\\1=",$str);

 return $str;
}
function urlchange($str) {
  global $server_url,$scriptname,$thismulu;
  //$str = preg_replace('/src=(["|\']?)\//', 'src=\\1'.$server_url.'/', $str);
	$str = preg_replace('/src=(["|\']?)\/\/?/', 'src=\\1//', $str);
	$str = preg_replace('/src=(["|\']?)\/http?/', 'src=\\1/', $str);
  //$str = preg_replace('/<(link[^>]+)href=(["|\']?)\/?/', '<\\1href=\\2'.$server_url.'/', $str);
	$str = preg_replace('/<(link[^>]+)href=(["|\']?)\/\/?/', '<\\1href=\\2//', $str);
	$str = preg_replace('/<(link[^>]+)href=(["|\']?)\/http?/', '<\\1href=\\2/', $str);
  
  //$str = preg_replace('/<(a[^>]+)href=(["|\']?)\/?/', '<\\1href=\\2'.$scriptname.'/', $str);
	//$str = preg_replace('/<script(?)>(?)</script>/', '<script\\1>\\2</script>', $str);
	//$str = preg_replace('/<(img)usemap="?"/', '<img', $str);
  
  $str=str_ireplace('/javascript:;','#',$str);
  $str=str_ireplace('"'.$scriptname.'/"',$scriptname,$str);
  return $str;
}
function charset($str){
 //if(preg_match('#<meta[^>]*charset\s*=\s*utf-8#iUs',$str)){
  //$str=preg_replace('/charset\s*=\s*utf-8/i','charset=gb2312',$str);
  //$str=iconv("UTF-8", "GB2312//IGNORE", $str);
 //}
 if(preg_match('#<meta[^>]*charset\s*=\s*utf-8#iUs',$str)){
  //$str=preg_replace('/charset\s*=\s*utf-8/i','charset=gb2312',$str);
  $str=iconv("GB2312//IGNORE","UTF-8",  $str);
 }
 return $str;
}
//var_dump(filter(charset(curl_get($url))));die;
$body=urlchange(filter(charset(curl_get($url))));//var_dump($body);die;

//-------------替换开始----------------------

$body=preg_replace('#>精品推荐</a>(.*)</a></p></div><div class="easou_box"><p>" #si',"",$body);

//正则替换
//PS：可写多个

$body=str_ireplace('action="/v"','action="index.php/v"',$body);
//PS:可写多个

//------------替换结束------------------------
echo $body;
?>