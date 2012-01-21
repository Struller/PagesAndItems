/**
* @package Pages-and-Items (com_pagesanditems)
* @package Pages-and-Items (com_pagesanditems)
* @version 2.1.0
* @copyright Copyright (C) 2006-2009 Carsten Engel. All rights reserved.
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @author http://www.pages-and-items.com
* @joomla Joomla is Free Software
*/

var hide_arrows = hide_arrows;

function print_items()
{
	if(items_total>=1){
		//print header
		htmlItems = '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>';		
		for (k = 1; k <= number_of_columns; k++){
			header_div = 'header_column_'+k;
			header_html = document.getElementById(header_div).innerHTML;
			htmlItems = htmlItems+'<td>'+header_html+'</td>';
		}		
		
		//start reorder columns header
		if(!hide_arrows){
			
			htmlItems = htmlItems+'<td colspan="4">';
			if(items_total!=1){
			htmlItems = htmlItems+'<strong>'+ordering+'</strong>';
			}else{
				htmlItems = htmlItems+"&nbsp;";	
			}
			htmlItems = htmlItems+'</td>';			
		}
		//end reorder columns header
		
		htmlItems = htmlItems+'</tr>';
		//loop through items
		var k = 0;
		for (i = 1; i <= items_total; i++){
			//ms: here we set the class row0 ore row1 for different color
			htmlItems = htmlItems+'<tr class="row'+k+'">';
			for (j = 1; j <= number_of_columns; j++){
				item_column = "item_column_"+j+"_"+i;			
				item_column_content = document.getElementById(item_column).innerHTML;
				htmlItems = htmlItems+'<td id="print_items_'+j+'_'+i+'">'+item_column_content+'</td>';
			}
			
			//start reorder columns items
			if(!hide_arrows){
				
				htmlItems = htmlItems+'<td width="12">';
				if(i!=1)
				{
					if(joomlaVersion >= '1.6')
					{
						htmlItems = htmlItems+'<a title="'+ moveUp +'" class="jgrid" href="javascript: reorderItems('+i+','+(i-1)+');"><span class="state uparrow"><span class="text">'+ moveUp +'</span></span></a>';
					}
					else
					{
						htmlItems = htmlItems+'<a href="javascript: reorderItems('+i+','+(i-1)+');"><img src="/administrator/images/uparrow.png" alt="move up" border="0" /></a>';
					}
				}
				else
				{
					htmlItems = htmlItems+'&nbsp;';
				}
				htmlItems = htmlItems+'</td>';
				htmlItems = htmlItems+'<td width="1">';
				htmlItems = htmlItems+"&nbsp;";
				htmlItems = htmlItems+'</td>';
				htmlItems = htmlItems+'<td width="12">';
				if(i!=items_total)
				{
					if(joomlaVersion >= '1.6')
					{
						htmlItems = htmlItems+'<a title="'+ moveDown +'" class="jgrid" href="javascript: reorderItems('+i+','+(i+1)+');"><span class="state downarrow"><span class="text">'+ moveDown +'</span></span></a>';
					}
					else
					{
						htmlItems = htmlItems+'<a href="javascript: reorderItems('+i+','+(i+1)+');"><img src="/administrator/images/downarrow.png" alt="move down" border="0" /></a>';
					}
				}else{
					htmlItems = htmlItems+'&nbsp;';
				}
				htmlItems = htmlItems+'</td>';
				htmlItems = htmlItems+'<td width="18">';
				htmlItems = htmlItems+"&nbsp;";
				htmlItems = htmlItems+'</td>';
			}
			//end reorder columns items	
			
			htmlItems = htmlItems+'</tr>';
			k = 1 - k;
		}
		htmlItems = htmlItems+'</table>';	
	}else{
		htmlItems = no_items;
	}
	document.getElementById('target_items').innerHTML = htmlItems;
	/*
	if(document.getElementById('original_items'))
	{
		document.getElementById('original_items').innerHTML = '';
	}
	*/
	
}

function reorderItems(oldPosition,newPosition){	
	
	//get the id of the item before it gets overwritten	
	destination_field = "reorder_item_id_"+newPosition;	
	destination_item_id = document.getElementById(destination_field).value;
	//get id of field of departure
	departure_field = "reorder_item_id_"+oldPosition;
	departure_item_id = document.getElementById(departure_field).value;
	//move id from departurefield to destinationfield
	document.getElementById(destination_field).value = departure_item_id;
	//put the id of the item which had to move into the departurefield
	document.getElementById(departure_field).value = destination_item_id;
	
	//move data from old divs to new destination
	for (m = 1; m <= number_of_columns; m++){
		//get destionation content
		destination_div = "item_column_"+m+"_"+newPosition;	
		destination_content = document.getElementById(destination_div).innerHTML;
		
		//get id for div departure
		departure_div = "item_column_"+m+"_"+oldPosition;
		
		//move the data from old to new
		document.getElementById(destination_div).innerHTML = document.getElementById(departure_div).innerHTML;
		
		//paste data from the destination field into the departure field
		document.getElementById(departure_div).innerHTML = destination_content;
		
		destination_td = "print_items_"+m+"_"+newPosition;
		departure_td = "print_items_"+m+"_"+oldPosition;
		
		var departure_div = document.id(departure_div);
		var destination_div = document.id(destination_div);
		
		var departure_td = document.id(departure_td);
		var destination_td = document.id(destination_td);
		
		var temp = new Element('div');
		var temp2 = new Element('div');

		departure_divChildren = departure_div.getChildren();
		if(departure_divChildren.length)
		{
			departure_divChildren.each(function(element){
				temp.grab(element,'top');
			});
		}
		else
		{
			//an textNode
			temp.set('text',departure_div.get('text'));
		}
		
		destination_divChildren = destination_div.getChildren();
		if(destination_divChildren.length)
		{
			destination_divChildren.each(function(element){
				temp2.grab(element,'top');
			});
		}
		else
		{
			//an textNode
			temp2.set('text',destination_div.get('text'));
		}
		
		temp2Children = temp2.getChildren();
		if(temp2Children.length)
		{
			temp2Children.each(function(element){
				departure_div.grab(element,'top');
			});
		}
		else
		{
			//an textNode
			departure_div.set('text',temp2.get('text'));
		}

		tempChildren = temp.getChildren();
		if(tempChildren.length)
		{
			tempChildren.each(function(element){
				destination_div.grab(element,'top');
			});
		}
		else
		{
			//an textNode
			destination_div.set('text',temp.get('text'));
		}
		
		departure_tdChildren = departure_td.getChildren();
		if(departure_tdChildren.length)
		{
			departure_tdChildren.each(function(element){
				temp.grab(element,'top');
			});
		}
		else
		{
			//an textNode
			temp.set('text',departure_td.get('text'));
		}
		
		destination_tdChildren = destination_td.getChildren();
		if(destination_tdChildren.length)
		{
			destination_tdChildren.each(function(element){
				temp2.grab(element,'top');
			});
		}
		else
		{
			//an textNode
			temp2.set('text',destination_td.get('text'));
		}
		
		temp2Children = temp2.getChildren();
		if(temp2Children.length)
		{
			temp2Children.each(function(element){
				departure_td.grab(element,'top');
			});
		}
		else
		{
			//an textNode
			departure_td.set('text',temp2.get('text'));
		}
		
		tempChildren = temp.getChildren();
		if(tempChildren.length)
		{
			tempChildren.each(function(element){
				destination_td.grab(element,'top');
			});
		}
		else
		{
			//an textNode
			destination_td.set('text',temp.get('text'));
		}
		
		temp.destroy();
		temp2.destroy();
		
	}
	
	//refresh list
	//print_items();
	
	//mark that items have been reordered
	document.getElementById("items_are_reordered").value = 1;

}

/*
ms: this is also in views/page/tmpl/default.php but i have comment out 
also here
is in javascript/submit_actions_items.js

function publish_unpublish_item(item_id, new_state){
	//unselect all checkboxes
	for (i = 0; i < item_ids.length; i++){
		box_id = 'itemCid_'+item_ids[i];
		if(document.getElementById(box_id)){
			document.getElementById(box_id).checked = false;
		}
	}

	//select the checkbox we need
	box_id = 'itemCid_'+item_id;
	document.getElementById(box_id).checked = 'checked';

	if(new_state=='1'){
		sub_task = 'publish';
	}else{
		sub_task = 'unpublish';
	}

	//submit
	document.getElementById('sub_task').value = sub_task;
	document.getElementById('task').value = 'item.items_state';
	//alert('item');
	document.adminForm.submit();
}
*/