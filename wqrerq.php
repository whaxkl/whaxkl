if(value == ''){
	alert("数据为空”);
}else{
	if(str_num != ''){
		var arr_num = str_num.split('');
		var value_num = Number(arr_num[0]) + Number(arr_num[2]);
		if(value_num == value){
			alert("成功");
		}else{
			alert("失败");
			//var cookie = document.cookie;
			//if(cookie.length == 0){
				//$.cookie('num', 1);
				//alert("数据错误”);
			//}else{
				//alert("失败");
			//}
		}	
	}else{
		alert("dasfdaf");
	}
}