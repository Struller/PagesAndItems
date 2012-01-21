str = document.getElementById(field_id).value;
rand_number = Math.random();
var at="@";
var dot=".";
var lat=str.indexOf(at);
var lstr=str.length;
var ldot=str.indexOf(dot);
var mailadres_is_valid;
if (str.indexOf(at)==-1){  
	mailadres_is_valid = rand_number; 	
}

if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr){   
	mailadres_is_valid = rand_number; 
}

if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr){	
	mailadres_is_valid = rand_number;
}

if (str.indexOf(at,(lat+1))!=-1){	
	mailadres_is_valid = rand_number; 
}

if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot){	
	mailadres_is_valid = rand_number; 
}

if (str.indexOf(dot,(lat+2))==-1){	
	mailadres_is_valid = rand_number; 
}

if (str.indexOf(" ")!=-1){	
	mailadres_is_valid = rand_number; 
}

if (str==""){	
	mailadres_is_valid = rand_number; 
}

if(mailadres_is_valid==rand_number){
	if(is_valid){
		alert(alert_message);
		document.getElementById(field_id).focus();
	}
	is_valid = false;	
}