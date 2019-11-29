<?php
/*$name = "Chalkboard DIY Tote Bag- Two Ways.txt";
$str = file_get_contents($name);
file_put_contents($name,str_replace('&amp;','&',unicode_decode($str)));
echo htmlspecialchars(unicode_decode($str));*/
//var_dump($_FILES['file']);die;
//var_dump($_FILES,$_FILES['file']['type']);die;

//var_dump($rar,$zip,$file_name);die;
//move_uploaded_file($_FILES["file"]["tmp_name"],"upload/" . $_FILES["file"]["name"]);
$path = iconv("UTF-8", "GBK", $_FILES["file"]["name"]);
$rar = stripos($path,'.rar');
$zip = stripos($path,'.zip');
if($rar===false && $zip===false){
    echo "<script>alert('请选择项目压缩包文件！');</script>";
    echo "<script>history.back();</script></script>";
    exit;
}
$rar_new = false;
if($rar!==false){
    $file_name = substr($_FILES['file']['name'],0,$rar);
    $rar_new = true;
}
if($zip!==false){
    $file_name = substr($_FILES['file']['name'],0,$zip);
    $zip_new = true;
}
//var_dump($rar,$zip,$file_name);die;
move_uploaded_file($_FILES["file"]["tmp_name"],
    "upload/" . $path);
//实例化ZipArchive类
if($rar_new === true){
    //phpinfo();die;
    $rar_file = rar_open("upload/" . $path) or die("Failed to open Rar archive");
    if(!$rar_file){
        echo 'error';
        exit();
    }
    $entries = rar_list($rar_file);
    foreach ($entries as $entry) {
        $entry->extract('./'); /*/dir/extract/to/换成其他路径即可*/
    }
    rar_close($rar_file);
}else{
    $zip = new ZipArchive();
//打开压缩文件，打开成功时返回true
    if ($zip->open("upload/" . $path) === true) {
        //解压文件到获得的路径a文件夹下
        $zip->extractTo('./');
        //关闭
        $zip->close();
    } else {
        echo 'error';
        exit();
    }
}
//var_dump($file_name);die;
if(traverse($file_name) === false){
    echo "<script>alert('操作失败');</script>";
    echo "<script>history.back();</script></script>";
    exit;
}
zipupload($file_name);
/*if(zipupload() === false){
    echo "<script>alert('下载失败');</script>";
    echo "<script>history.back();</script></script>";
    exit;
}else{
    echo "<script>alert('下载成功');</script>";
}*/

/*
 * 删除目录
 * */
function delDirAndFile($dirName){
    if ( $handle = opendir( "$dirName" ) ) {
        while ( false !== ( $item = readdir( $handle ) ) ) {
            if ( $item != "." && $item != ".." ) {
                if ( is_dir( "$dirName/$item" ) ) {
                    delDirAndFile( "$dirName/$item" );
                } else {
                    unlink( "$dirName/$item" );
                }
            }
        }
        closedir( $handle );
        rmdir( $dirName );
    }
}

/*
 * zip生成并下载
 * */
function zipupload($filename){
    global $rar_new;
    if($rar_new === true) {
        $file_dir = '.rar';
    }else{
        $file_dir = '.zip';
    }
        /*$path="./emptydir";//要压缩的文件的绝对路径
        $filename='niao';   //生成压缩文件名*/
    $path = iconv("UTF-8", "GBK", $filename);//加这行中文文件夹也ok了
    create_zip($path,$filename);
    /*if(!file_exists('./' . $path . '.zip')){
        echo 1;die;
    }*/
    /*header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header('Content-disposition: attachment; filename=' . basename($filename . '.zip')); //文件名
    header("Content-Type: application/zip"); //zip格式的
    header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件
    header('Content-Length: ' . filesize('./' . $filename. '.zip')); //告诉浏览器，文件大小*/

    Header("Content-type: application/octet-stream");
    Header("Accept-Ranges: bytes");
    Header("Accept-Length:" . filesize('./' . $filename. $file_dir));
    Header("Content-Disposition: attachment; filename=" . basename($filename . $file_dir));
    @readfile('./' . $filename . $file_dir);//下载到本地
    @unlink('upload/' . $filename . $file_dir);//删除服务器上生成的这个压缩文件
    @delDirAndFile($filename);
}

function create_zip($path,$filename){
    $zip = new ZipArchive();
    if($zip->open($filename.'.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
        addFileToZip($path, $zip);//调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
        $zip->close(); //关闭处理的zip文件
    }
}

function addFileToZip($path,$zip){
    $handler=opendir($path); //打开当前文件夹由$path指定。
    while(($filename=readdir($handler))!==false){
        if($filename != "." && $filename != ".."){//文件夹文件名字为'.'和‘..’，不要对他们进行操作
            if(is_dir($path."/".$filename)){
                addFileToZip($path."/".$filename, $zip);
            }else{
                $zip->addFile($path."/".$filename);
            }
        }
    }
    @closedir($path);
}

/*function downdetails($file_path){//下载文件
    header("Content-type:text/html;charset=utf-8");
    //$file_path="testMe.txt";
    //用以解决中文不能显示出来的问题
    //$file_name=iconv("utf-8","gb2312",$file_name);
    //$file_sub_path=$_SERVER['DOCUMENT_ROOT']."marcofly/phpstudy/down/down/";
    //$file_path=$file_sub_path.$file_name;
    //首先要判断给定的文件存在与否
    if (!file_exists($file_path)) {
        echo "<script>alert('没有该文件');</script>";
        return false;
    }
    $fp = fopen($file_path, "r");
    $file_size = filesize($file_path);
    //下载文件需要用到的头
    Header("Content-type: application/octet-stream");
    Header("Accept-Ranges: bytes");
    Header("Accept-Length:" . $file_size);
    Header("Content-Disposition: attachment; filename=" . $file_path);
    $buffer = 1024;
    $file_count = 0;
    //向浏览器返回数据
    while (!feof($fp) && $file_count < $file_size) {
        $file_con = fread($fp, $buffer);
        $file_count += $buffer;
        echo $file_con;
    }
    fclose($fp);
    return true;
}*/
/*
 * 替换文件内容
 * */
function traverse($path) {
    $current_dir = opendir($path);    //opendir()返回一个目录句柄,失败返回false
    while(($file = readdir($current_dir)) !== false) {    //readdir()返回打开目录句柄中的一个条目
        $sub_dir = $path . DIRECTORY_SEPARATOR . $file;    //构建子目录路径
        if($file == '.' || $file == '..') {
            continue;
        }else if(is_dir($sub_dir)) {    //如果是目录,进行递归
            traverse($sub_dir); //嵌套遍历子文件夹
        }else{    //如果是文件,直接输出路径和文件名
            $str = file_get_contents($sub_dir);
            file_put_contents($sub_dir,str_replace('&amp;','&',unicode_decode($str)));
        }
    }
    if($current_dir === false){
        return false;
    }
    return true;
}
/*
 * 修改文件编码
 * */
function unicode_decode($unistr) {
    $arr = preg_split("/(&#[0-9]*;)/", $unistr, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    $restr='';
    foreach ($arr as $key => $value) {
        if (strstr($value,'&#')){
            $unistr = '';
            $arruni = explode('&#', $value);
            $arruni = substr($arruni[1], 0, strlen($arruni[1]) - 1);
            $temp = intval($arruni);
            $unistr .= ($temp < 256) ? chr(0) . chr($temp) : chr($temp / 256) . chr($temp % 256);
            $restr .= iconv('UCS-2BE', 'UTF-8', $unistr);
        }else{
            $restr .= $value;
        }
    }
    return $restr;
}
