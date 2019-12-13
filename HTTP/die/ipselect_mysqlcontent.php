<?php
$servername = "localhost";
$server_user = "root";
$server_pass = "abc123456";
$dbname = "orders";
$con = new mysqli($servername,$server_user,$server_pass,$dbname);
$data = $_REQUEST;
if(isset($data['black_ip'])){
	$black_sql = "select * from `black_ip`";
	$black_temp = $con->query($black_sql);
    if($black_temp == true){
        return $black_temp;
    }else{
        return false;
    }
}else if(isset($data['white_ip'])){
	$white_sql = "select * from `white_ip`";
	$white_temp = $con->query($white_sql);
    if($white_temp == true){
        return $white_temp;
    }else{
        return false;
    }
}else{
	return false;
}	
?>