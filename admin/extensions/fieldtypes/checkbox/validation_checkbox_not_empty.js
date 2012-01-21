hidden_field_id_total = 'total_checkboxes_field'+field_id;
number_of_checkboxes = document.getElementById(hidden_field_id_total).value;
hidden_field_id_min = 'min_checkboxes_field'+field_id;
number_of_needed_checkboxes = document.getElementById(hidden_field_id_min).value;
checked_boxes = 0;
for (i = 0; i < number_of_checkboxes; i++){
	checkbox_id = field_id+'_'+i;
	if(document.getElementById(checkbox_id).checked==true){
		checked_boxes = checked_boxes+1;
	}
}
if(checked_boxes < number_of_needed_checkboxes){
	if(is_valid){
		alert(alert_message);
	}
	is_valid = false;
}