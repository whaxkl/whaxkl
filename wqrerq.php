if(value == ''){
	alert("����Ϊ�ա�);
}else{
	if(str_num != ''){
		var arr_num = str_num.split('');
		var value_num = Number(arr_num[0]) + Number(arr_num[2]);
		if(value_num == value){
			alert("�ɹ�");
		}else{
			alert("ʧ��");
			//var cookie = document.cookie;
			//if(cookie.length == 0){
				//$.cookie('num', 1);
				//alert("���ݴ���);
			//}else{
				//alert("ʧ��");
			//}
		}	
	}else{
		alert("dasfdaf");
	}
}