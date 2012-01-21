if(document.getElementById(field_id).value==""){
	if(is_valid){
		alert(alert_message);
		document.getElementById(field_id).focus();
	}
	is_valid = false; 	
}