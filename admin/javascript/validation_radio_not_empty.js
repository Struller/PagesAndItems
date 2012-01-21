radio_buttons = document.adminForm.elements[field_id+"[]"];
radio_is_checked = false;
for(i=0;i<radio_buttons.length;i++){
	radio_id = field_id+'_'+i;	
	if(document.getElementById(radio_id).checked==true){
		radio_is_checked = true;
	}
}
if(!radio_is_checked){
	if(is_valid){
		alert(alert_message);
	}
	is_valid = false;
}