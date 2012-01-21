select_value = document.adminForm.elements[field_id+"[]"].value;
if(select_value==0 || select_value==''){
	if(is_valid){
		alert(alert_message);
		document.getElementById(field_id).focus();
	}
	is_valid = false; 
	
}