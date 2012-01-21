/**
* @package Pages-and-Items (com_pagesanditems)
* @version 2.0.0
* @copyright Copyright (C) 2009-2010 Michael Struller. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author http://gecko.struller.de
*/


function stringToObject(value)
{
	if(JSON.decode)
	{
		//Mootools 1.2.x
		value = JSON.decode(value.toString());
	}
	else
	{
		//Mootools 1.1x
		value = Json.evaluate(value.toString());
	}
	return value; 
}
	
function objectToString(value)
{
	if(JSON.encode)
	{
		//Mootools 1.2.x
		value = JSON.encode(value);
	}
	else
	{
		//Mootools 1.1x
		value = Json.toString(value);
	}
	return value; 
}

function newVisible(id,nr)
{
	visible = document.getElementById(id).checked ? 1 : 0;
	orderingVisible = controlNamePrefix;
	orderingVisible_value = document.getElementById(orderingVisible).value;
	value = stringToObject(orderingVisible_value);
	
	value[nr].visible = visible;

	value = objectToString(value);
	value = value.replace(/"/g, "'");
	document.getElementById(orderingVisible).value = value;
	//alert(value);
}

function newOrder(oldPosition,newPosition)
{
	orderingVisible = controlNamePrefix;
	orderingVisible_value = document.getElementById(orderingVisible).value;

	value = stringToObject(orderingVisible_value);

	value[oldPosition].value = newPosition;
	value[newPosition].value = oldPosition;
	
	newValue = value[newPosition];
	oldValue = value[oldPosition];
	
	value[newPosition] = oldValue;
	value[oldPosition] = newValue;
	
	/**/
	document.getElementById(namePrefix+"_visible_"+newPosition).value = value[newPosition].value;
	document.getElementById(namePrefix+"_visible_"+newPosition).checked = value[newPosition].visible ? 'checked' : '';
	
	document.getElementById(namePrefix+"_visible_"+oldPosition).value = value[oldPosition].value;
	document.getElementById(namePrefix+"_visible_"+oldPosition).checked = value[oldPosition].visible ? 'checked' : '';
	
	document.getElementById(namePrefix+"_column_2_"+oldPosition).innerHTML = value[oldPosition].displayName;

	document.getElementById(namePrefix+"_column_2_"+newPosition).innerHTML = value[newPosition].displayName;
	/**/
/*
	//get the value of the item before it gets overwritten
	destination_field_checkBox = namePrefix+"_visible_"+newPosition;
	destination_value_checkBox = document.getElementById(destination_field_checkBox).value;
	destination_checked_checkBox = document.getElementById(destination_field_checkBox).checked;

	//get value of field of departure
	departure_field_checkBox = namePrefix+"_visible_"+oldPosition;
	departure_value_checkBox = document.getElementById(departure_field_checkBox).value;
	departure_checked_checkBox = document.getElementById(departure_field_checkBox).checked;
	
	destination_field_name = namePrefix+"_column_2_"+newPosition;
	destination_innerHTML_name = document.getElementById(destination_field_name).innerHTML;

	//get value of field of departure
	departure_field_name = namePrefix+"_column_2_"+oldPosition;
	departure_innerHTML_name = document.getElementById(departure_field_name).innerHTML;
	
	//move value and checked from departurefield to destinationfield
	document.getElementById(destination_field_checkBox).value = departure_value_checkBox;
	document.getElementById(destination_field_checkBox).checked = departure_checked_checkBox;

	//move value and checked from destinationfield to departurefield
	document.getElementById(departure_field_checkBox).value = destination_value_checkBox;
	document.getElementById(departure_field_checkBox).checked = destination_checked_checkBox;
	
	//move innerHTML from departurefield to destinationfield
	document.getElementById(destination_field_name).innerHTML = departure_innerHTML_name;

	//move value and checked from destinationfield to departurefield
	document.getElementById(departure_field_name).innerHTML = destination_innerHTML_name;
*/	
	value = objectToString(value);
	value = value.replace(/"/g, "'");
	document.getElementById(orderingVisible).value = value;
	//alert(value);
}

