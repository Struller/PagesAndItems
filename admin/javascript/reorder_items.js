/**
* @package Pages-and-Items (com_pagesanditems)
* @package Pages-and-Items (com_pagesanditems)
* @version 2.0.0
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
		for (i = 1; i <= items_total; i++){
			htmlItems = htmlItems+'<tr>';			
			for (j = 1; j <= number_of_columns; j++){
				item_column = "item_column_"+j+"_"+i;			
				item_column_content = document.getElementById(item_column).innerHTML;
				htmlItems = htmlItems+'<td>'+item_column_content+'</td>';
			}
			
			//start reorder columns items
			if(!hide_arrows){
				
				htmlItems = htmlItems+'<td width="12">';
				if(i!=1)
				{
					if(joomlaVersion >= '1.6')
					{
						htmlItems = htmlItems+'<a class="jgrid" href="javascript: reorderItems('+i+','+(i-1)+');"><span class="state uparrow"><span class="text">move up</span></span></a>';
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
				htmlItems = htmlItems+'<td width="10">';
				htmlItems = htmlItems+"&nbsp;";
				htmlItems = htmlItems+'</td>';
				htmlItems = htmlItems+'<td width="12">';
				if(i!=items_total)
				{
					if(joomlaVersion >= '1.6')
					{
						htmlItems = htmlItems+'<a class="jgrid" href="javascript: reorderItems('+i+','+(i+1)+');"><span class="state downarrow"><span class="text">move down</span></span></a>';
					}
					else
					{
						htmlItems = htmlItems+'<a href="javascript: reorderItems('+i+','+(i+1)+');"><img src="/administrator/images/downarrow.png" alt="move down" border="0" /></a>';
					}
				}else{
					htmlItems = htmlItems+'&nbsp;';
				}
				htmlItems = htmlItems+'</td>';
				htmlItems = htmlItems+'<td width="8">';
				htmlItems = htmlItems+"&nbsp;";
				htmlItems = htmlItems+'</td>';
			}
			//end reorder columns items	
			
			htmlItems = htmlItems+'</tr>';
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
	}			
	
	//refresh list
	print_items();
	
	//mark that items have been reordered
	document.getElementById("items_are_reordered").value = 1;

}

