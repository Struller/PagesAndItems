/**
* @package Pages-and-Items (com_pagesanditems)
* @version 2.1.0
* @copyright Copyright (C) 2006-2009 Carsten Engel. All rights reserved.
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @author http://www.pages-and-items.com
* @joomla Joomla is Free Software
*/

function reorderItemsRows(oldPosition,newPosition,name,number_of_columns){
	
	//get the id of the item before it gets overwritten
	destination_field = document.id('reorder_'+name+'_id_'+newPosition);
	destination_id = destination_field.value;
	//get id of field of departure
	departure_field = document.id('reorder_'+name+'_id_'+oldPosition);
	departure_id = departure_field.value;
	//move id from departurefield to destinationfield
	destination_field.value = departure_id;
	//put the id of the categorie which had to move into the departurefield
	departure_field.value = destination_id;
	
	//move data from old to new destination
	for (m = 1; m <= number_of_columns; m++){
		//move the td
		//and rename id
		var tempTd = new Element('td');
		var tempTd2 = new Element('td');
		
		var departure_td = document.id('items_'+name+'_column_'+m+'_'+oldPosition);
		var destination_td = document.id('items_'+name+'_column_'+m+'_'+newPosition);
		
		tempTd.inject(departure_td,'before');
		tempTd2.inject(destination_td,'before');
		departure_td.inject(tempTd2,'before');
		destination_td.inject(tempTd,'before');
		tempTd.destroy();
		tempTd2.destroy();
		departure_td.set('id','items_'+name+'_column_'+m+'_'+newPosition);
		destination_td.set('id','items_'+name+'_column_'+m+'_'+oldPosition);
		
		/*
		var temp = new Element('div');
		var temp2 = new Element('div');


		departure_tdChildren = departure_td.getChildren();
		if(departure_tdChildren.length){
			departure_tdChildren.each(function(element){temp.grab(element,'top');});
		}else{
			//an textNode
			temp.set('text',departure_td.get('text'));
		}
		
		destination_tdChildren = destination_td.getChildren();
		if(destination_tdChildren.length){
			destination_tdChildren.each(function(element){temp2.grab(element,'top');});
		}else{
			//an textNode
			temp2.set('text',destination_td.get('text'));
		}
		
		temp2Children = temp2.getChildren();
		if(temp2Children.length){
			temp2Children.each(function(element){departure_td.grab(element,'top');});
		}else{
			//an textNode
			departure_td.set('text',temp2.get('text'));
		}

		tempChildren = temp.getChildren();
		if(tempChildren.length){
			tempChildren.each(function(element){destination_td.grab(element,'top');});
		}else{
			//an textNode
			destination_td.set('text',temp.get('text'));
		}

		temp.destroy();
		temp2.destroy();
		*/
	}
	//mark that items have been reordered
	var reorder = document.id('items_'+name+'_are_reordered');
	if(reorder.value != 1)
	{
		reorder.value = 1;
		reorder.fireEvent('change');
	}
	
}