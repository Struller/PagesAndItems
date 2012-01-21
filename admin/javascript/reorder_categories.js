/**
* @package Pages-and-Items (com_pagesanditems)
* @package Pages-and-Items (com_pagesanditems)
* @version 2.1.0
* @copyright Copyright (C) 2006-2009 Carsten Engel. All rights reserved.
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @author http://www.pages-and-items.com
* @joomla Joomla is Free Software
*/

var category_hide_arrows = category_hide_arrows;

function print_categories()
{
	if(items_category_total>=1){
		//print header
		htmlCategories = '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>';		
		for (k = 1; k <= number_of_columns_items_category; k++){
			header_div = 'items_category_header_column_'+k;
			header_html = document.getElementById(header_div).innerHTML;
			htmlCategories = htmlCategories+'<td>'+header_html+'</td>';
		}		
		
		//start reorder columns header
		if(!category_hide_arrows){
			
			htmlCategories = htmlCategories+'<td colspan="4">';
			if(items_category_total!=1){
			//htmlCategories = htmlCategories+'<strong>'+ordering+'</strong>';
			htmlCategories = htmlCategories+'<strong>'+Joomla.JText._('COM_PAGESANDITEMS_ORDERING')+'</strong>';
			}else{
				htmlCategories = htmlCategories+"&nbsp;";	
			}
			htmlCategories = htmlCategories+'</td>';			
		}
		//end reorder columns header
		
		htmlCategories = htmlCategories+'</tr>';
		//loop through categories
		var k = 0;
		for (i = 1; i <= items_category_total; i++){
			//ms: here we set the class row0 ore row1 for different color
			htmlCategories = htmlCategories+'<tr class="row'+k+'">';
			for (j = 1; j <= number_of_columns_items_category; j++){
				category_column = "category_column_"+j+"_"+i;			
				category_column_content = document.getElementById(category_column).innerHTML;
				htmlCategories = htmlCategories+'<td id="print_items_category_'+j+'_'+i+'">'+category_column_content+'</td>';
			}
			
			//start reorder columns categories
			if(!category_hide_arrows){
				
				htmlCategories = htmlCategories+'<td width="12">';
				if(i!=1)
				{
					if(joomlaVersion >= '1.6')
					{
						htmlCategories = htmlCategories+'<a title="'+ Joomla.JText._('JLIB_HTML_MOVE_UP') +'" class="jgrid" href="javascript: reorderCategories('+i+','+(i-1)+');"><span class="state uparrow"><span class="text">'+ Joomla.JText._('JLIB_HTML_MOVE_UP') +'</span></span></a>';
					}
					else
					{
						htmlCategories = htmlCategories+'<a href="javascript: reorderCategories('+i+','+(i-1)+');"><img src="/administrator/images/uparrow.png" alt="move up" border="0" /></a>';
					}
				}
				else
				{
					htmlCategories = htmlCategories+'&nbsp;';
				}
				htmlCategories = htmlCategories+'</td>';
				htmlCategories = htmlCategories+'<td width="1">';
				htmlCategories = htmlCategories+"&nbsp;";
				htmlCategories = htmlCategories+'</td>';
				htmlCategories = htmlCategories+'<td width="12">';
				if(i!=items_category_total)
				{
					if(joomlaVersion >= '1.6')
					{
						htmlCategories = htmlCategories+'<a title="'+ Joomla.JText._('JLIB_HTML_MOVE_DOWN') +'" class="jgrid" href="javascript: reorderCategories('+i+','+(i+1)+');"><span class="state downarrow"><span class="text">'+ Joomla.JText._('JLIB_HTML_MOVE_DOWN') +'</span></span></a>';
					}
					else
					{
						htmlCategories = htmlCategories+'<a href="javascript: reorderCategories('+i+','+(i+1)+');"><img src="/administrator/images/downarrow.png" alt="move down" border="0" /></a>';
					}
				}else{
					htmlCategories = htmlCategories+'&nbsp;';
				}
				htmlCategories = htmlCategories+'</td>';
				htmlCategories = htmlCategories+'<td width="18">';
				htmlCategories = htmlCategories+"&nbsp;";
				htmlCategories = htmlCategories+'</td>';
			}
			//end reorder columns categories	
			
			htmlCategories = htmlCategories+'</tr>';
			k = 1 - k;
		}
		htmlCategories = htmlCategories+'</table>';	
	}else{
		htmlCategories = Joomla.JText._('COM_PAGESANDITEMS_THIS_CATEGORY_NO_UNDERLYING_CATEGORIES'); //no_categories;
	}
	document.getElementById('target_categories').innerHTML = htmlCategories;
}

function reorderCategories(oldPosition,newPosition){
	
	//get the id of the categorie before it gets overwritten
	destination_field = "reorder_category_id_"+newPosition;
	destination_category_id = document.getElementById(destination_field).value;
	//get id of field of departure
	departure_field = "reorder_category_id_"+oldPosition;
	departure_category_id = document.getElementById(departure_field).value;
	//move id from departurefield to destinationfield
	document.getElementById(destination_field).value = departure_category_id;
	//put the id of the categorie which had to move into the departurefield
	document.getElementById(departure_field).value = destination_category_id;
	
	//move data from old divs to new destination
	for (m = 1; m <= number_of_columns_items_category; m++){
		//get destionation content
		destination_div = "category_column_"+m+"_"+newPosition;
		destination_content = document.getElementById(destination_div).innerHTML;
		//get id for div departure
		departure_div = "category_column_"+m+"_"+oldPosition;
		
		/*
		//move the data from old to new
		document.getElementById(destination_div).innerHTML = document.getElementById(departure_div).innerHTML;
		//paste data from the destination field into the departure field
		document.getElementById(departure_div).innerHTML = destination_content;
		*/
		destination_td = "print_items_category_"+m+"_"+newPosition;
		departure_td = "print_items_category_"+m+"_"+oldPosition;
		
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
	//print_categories();
	
	//mark that categories have been reordered
	document.getElementById("items_category_are_reordered").value = 1;

}


//if we use this in other than view categorie we must set another task? ore need an added redirect?
function publish_unpublish_category(category_id, new_state){

	//unselect all checkboxes
	for (i = 0; i < category_ids.length; i++){
		box_id = 'categoryCid_'+category_ids[i];
		document.getElementById(box_id).checked = false;
	}
	
	//select the checkbox we need
	box_id = 'categoryCid_'+category_id;
	document.getElementById(box_id).checked = 'checked';
	
	if(new_state=='1'){
		sub_task = 'publish';
	}else{
		sub_task = 'unpublish';
	}
	
	//submit
	//document.getElementById('task_redirect').value = task_redirect;
	document.getElementById('sub_task').value = sub_task;
	document.getElementById('task').value = 'category.categories_state';
	document.adminForm.submit();
}


