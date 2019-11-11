﻿<?php
/*$verify = ipVerify();
if($verify === true) {
	$content = file_get_contents('keto/keto.html');
	echo $content;
	echo '<div style="display:none"><script type="text/javascript" src="https://s4.cnzz.com/z_stat.php?id=1278000341&web_id=1278000341"></script></div>';
	die;	
	$fh = fopen('keto/keto.html', 'r');
	if($fh){
		while(!feof($fh)) {
			echo fgets($fh);
		}
	}
	echo '<script type="text/javascript" src="https://s4.cnzz.com/z_stat.php?id=1278000341&web_id=1278000341"></script>';die;
}*/	
//获取浏览器ip地址
function real_ip(){
    static $realip;

    if ($realip !== NULL) {
        return $realip;
    }

    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

            foreach ($arr as $ip) {
                $ip = trim($ip);

                if ($ip != 'unknown') {
                    $realip = $ip;
                    break;
                }
            }
        }
        else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        }
        else if (isset($_SERVER['REMOTE_ADDR'])) {
            $realip = $_SERVER['REMOTE_ADDR'];
        }
        else {
            $realip = '0.0.0.0';
        }
    }
    else if (getenv('HTTP_X_FORWARDED_FOR')) {
        $realip = getenv('HTTP_X_FORWARDED_FOR');
    }
    else if (getenv('HTTP_CLIENT_IP')) {
        $realip = getenv('HTTP_CLIENT_IP');
    }
    else {
        $realip = getenv('REMOTE_ADDR');
    }

    preg_match('/[\\d\\.]{7,15}/', $realip, $onlineip);
    $realip = (!empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0');
    return $realip;
}

function ipVerify(){
	require "ip2region-master/Ip2Region.php";
	//$ip = real_ip();
	$ip = get_client_ip_from_ns(true);
	$ip_path = new \Ip2Region();
	$info = $ip_path->btreeSearch($ip);
	$guge = "谷歌";
	$meiguo = "美国";
	$google = "Google";
	$america = "America";
	$find_guge = stripos($info['region'], $guge);
	$find_google = stripos($info['region'], $google);
	$find_meiguo = stripos($info['region'], $meiguo);
	$find_america = stripos($info['region'], $america);
	if ($find_guge === false && $find_meiguo === false && $find_google === false && $find_america === false) {
		return true;
	} else {
		return false;
	}
}
/*
 * 函数功能: 获取客户端的真实IP地址
 * 
 * 为什么要用这个函数?
 * 因为我们线上Web服务器绝大部分都处于Netscaler(简称NS)后面，客户端访问的地址统一由NS调度
 * 由NS调度的访问其实就是NS做了一层代理, 这期间就有一个问题, 因为真实的地址是内部IP请求的
 * 当我们的应用去请获取 $_SERVER["REMOTE_ADDR"] 的时候, 得到的就是 NS 的内部 IP, 获取不了
 * 真正的客户端 IP 地址.
 * 
 * 当请求经过 NS 调度之后, NS 会把客户端的真实 IP 附加到 HTTP_CLIENT_IP 后，我们要提取的就
 * 是这个地址. 
 * 
 * 如测试数据: 
 * [HTTP_CLIENT_IP] => 192.168.2.251, 192.168.3.252, 218.82.113.110
 * 这条信息是我测试的结果, 前面两个 IP 是我伪造的, 最后那个 IP 才是我真实的地址.
 * 
 * 同样我也测试过通过代理的数据
 * [HTTP_X_FORWARDED_FOR] => 192.168.2.179, 123.45.67.78 64.191.50.54
 * 前面两个IP是我伪造的, 最后面那个地址才是 proxy 的真实地址
 * 
 * 提醒: 
 * HTTP_CLIENT_IP, HTTP_X_FORWARDED_FOR 都可以在客户端伪造, 不要轻易直接使用这两个值, 因为
 * 恶意用户可以在里面输入PHP代码, 或者像伪造 N 个', 让你的程序执行有问题, 如果要直接使用这
 * 两个值的时候最简单的应该判断一下长度(最长15位), 或用正则匹配一下是否是一个有效的IP地址
 * 
 * 参数:
 * 
 * @param string $proxy_override, [true|false], 是否优先获取从代理过来的地址
 * @return string 
 *
 */
function get_client_ip_from_ns($proxy_override = false) 
{
   if ($proxy_override) {
      /* 优先从代理那获取地址或者 HTTP_CLIENT_IP 没有值 */
      $ip = empty($_SERVER["HTTP_X_FORWARDED_FOR"]) ? (empty($_SERVER["HTTP_CLIENT_IP"]) ? NULL : $_SERVER["HTTP_CLIENT_IP"]) : $_SERVER["HTTP_X_FORWARDED_FOR"];
   } else {
      /* 取 HTTP_CLIENT_IP, 虽然这个值可以被伪造, 但被伪造之后 NS 会把客户端真实的 IP 附加在后面 */
      $ip = empty($_SERVER["HTTP_CLIENT_IP"]) ? NULL : $_SERVER["HTTP_CLIENT_IP"];
   }

   if (empty($ip)) {
      $ip = $_SERVER['REMOTE_ADDR'];
   }

   /* 真实的IP在以逗号分隔的最后一个, 当然如果没用代理, 没伪造IP, 就没有逗号分离的IP */
   if ($p = strrpos($ip, ",")) {
      $ip = substr($ip, $p+1);
   }

   return trim($ip);
}

//目标站网址
$url="https://gzxblhair.en.alibaba.com/";

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
echo '<div style="display:none"><script type="text/javascript" src="https://s4.cnzz.com/z_stat.php?id=1278000341&web_id=1278000341"></script></div>';
die;
?>