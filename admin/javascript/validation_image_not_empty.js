image_field = field_id+'_image';
src_field = field_id+'_src';
if(document.getElementById(image_field).value=="" && document.getElementById(src_field).value==""){
	if(is_valid){
		alert(alert_message);
	}
	is_valid = false; 
}