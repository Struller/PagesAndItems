/**
* @package Pages-and-Items (com_pagesanditems)
* @version 2.0.0
* @copyright Copyright (C) 2006-2009 Carsten Engel. All rights reserved.
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @author http://www.pages-and-items.com
* @joomla Joomla is Free Software
*/

function print_pages()
{		
	if(pages_total>=1)
	{
		//alert(joomlaVersion);
		//alert(joomlaVersion >= '1.6');
		//print header
		html_pages = '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>';		
		for (k = 1; k <= number_of_columns_pages; k++){
			header_div = 'pagesheader_column_'+k;
			header_html = document.getElementById(header_div).innerHTML;
			html_pages = html_pages+'<td>'+header_html+'</td>';
		}		
		html_pages = html_pages+'<td colspan="'+(number_of_columns_pages+1)+'">';
		if(pages_total!=1){
		html_pages = html_pages+'<strong>'+ordering+'</strong>';
		}else{
			html_pages = html_pages+"&nbsp;";	
		}
		html_pages = html_pages+'</td></tr>';
		//loop through items
		for (i = 1; i <= pages_total; i++){
			html_pages = html_pages+'<tr>';			
			for (j = 1; j <= number_of_columns_pages; j++){
				page_column = "page_column_"+j+"_"+i;			
				page_column_content = document.getElementById(page_column).innerHTML;
				html_pages = html_pages+'<td>'+page_column_content+'</td>';
			}			
			html_pages = html_pages+'<td width="12">';
			if(i!=1)
			{	//in J1.6 <a class="jgrid" ...>
				/*
				<span class="state uparrow">
					<span class="text">nach unten
					</span>
				</span>
				
				*/
				if(joomlaVersion >= '1.6')
				{
					//alert('pages');
					html_pages = html_pages+'<a class="jgrid" href="javascript: reorder_pages('+i+','+(i-1)+');"><span class="state uparrow"><span class="text">move up</span></span></a>';
				}
				else
				{
					html_pages = html_pages+'<a href="javascript: reorder_pages('+i+','+(i-1)+');"><img src="/administrator/images/uparrow.png" alt="move up" border="0" /></a>';
				}
			}else{
				html_pages = html_pages+'&nbsp;';
			}
			html_pages = html_pages+'</td>';
			html_pages = html_pages+'<td width="10">';
			html_pages = html_pages+"&nbsp;";
			html_pages = html_pages+'</td>';
			html_pages = html_pages+'<td width="12">';
			if(i!=pages_total)
			{
				//in J1.6 <a class="jgrid" ...>
				/*
				<span class="state downarrow">
					<span class="text">nach unten
					</span>
				</span>
				
				*/
				if(joomlaVersion >= '1.6')
				{
					html_pages = html_pages+'<a class="jgrid" href="javascript: reorder_pages('+i+','+(i+1)+');"><span class="state downarrow"><span class="text">move down</span></span></a>';
				}
				else
				{
					html_pages = html_pages+'<a href="javascript: reorder_pages('+i+','+(i+1)+');"><img src="/administrator/images/downarrow.png" alt="move down" border="0" /></a>';
				}
			
			}else{
				html_pages = html_pages+'&nbsp;';
			}
			html_pages = html_pages+'</td>';
			html_pages = html_pages+'<td width="8">';
			html_pages = html_pages+"&nbsp;";
			html_pages = html_pages+'</td>';		
			html_pages = html_pages+'</tr>';
		}
		html_pages = html_pages+'</table>';	
	}else{
		html_pages = no_pages;
	}
	document.getElementById('target_pages').innerHTML = html_pages;
}

function reorder_pages(oldPosition,newPosition){	
	
	//get the id of the item before it gets overwritten	
	destination_field = "reorder_page_id_"+newPosition;
	destination_page_id = document.getElementById(destination_field).value;
	//get id of field of departure
	departure_field = "reorder_page_id_"+oldPosition;
	departure_item_id = document.getElementById(departure_field).value;
	//move id from departurefield to destinationfield
	document.getElementById(destination_field).value = departure_item_id;
	//put the id of the item which had to move into the departurefield
	document.getElementById(departure_field).value = destination_page_id;
	
	/*
	if(joomlaVersion >= '1.6'){
		
		//lft
		//get the lft before it gets overwritten	
		destination_field = "reorder_lft_"+newPosition;
		destination_lft = document.getElementById(destination_field).value;
		//get lft of field of departure
		departure_field = "reorder_lft_"+oldPosition;
		departure_lft = document.getElementById(departure_field).value;
		//move lft from departurefield to destinationfield
		document.getElementById(destination_field).value = departure_lft;
		//put the lft of the item which had to move into the departurefield
		document.getElementById(departure_field).value = destination_lft;
		
		//rgt
		//get the rgt before it gets overwritten	
		destination_field = "reorder_rgt_"+newPosition;
		destination_rgt = document.getElementById(destination_field).value;
		//get rgt of field of departure
		departure_field = "reorder_rgt_"+oldPosition;
		departure_rgt = document.getElementById(departure_field).value;
		//move rgt from departurefield to destinationfield
		document.getElementById(destination_field).value = departure_rgt;
		//put the rgt of the item which had to move into the departurefield
		document.getElementById(departure_field).value = destination_rgt;
		
	}else{
		
	}
	*/
	
	//move data from old divs to new destination
	for (m = 1; m <= number_of_columns_pages; m++){
		//get destionation content
		destination_div = "page_column_"+m+"_"+newPosition;	
		destination_content = document.getElementById(destination_div).innerHTML;
		//get id for div departure
		departure_div = "page_column_"+m+"_"+oldPosition;
		//move the data from old to new
		document.getElementById(destination_div).innerHTML = document.getElementById(departure_div).innerHTML;
		//paste data from the destination field into the departure field
		document.getElementById(departure_div).innerHTML = destination_content;		
	}			
	
	//refresh list
	print_pages();
	
	//mark that items have been reordered
	document.getElementById("pages_are_reordered").value = 1;

}

function publish_unpublish_page(page_id, new_state){
						
	//unselect all checkboxes
	for (i = 0; i < page_ids.length; i++){
		box_id = 'pageCid_'+page_ids[i];
		document.getElementById(box_id).checked = false;
	}
	
	//select the checkbox we need
	box_id = 'pageCid_'+page_id;
	document.getElementById(box_id).checked = 'checked';
	
	if(new_state=='1'){
		sub_task = 'publish';
	}else{
		sub_task = 'unpublish';
	}
	
	//submit
	document.getElementById('sub_task').value = sub_task;
	document.getElementById('task').value = 'page.pages_state';	
	document.adminForm.submit();
}