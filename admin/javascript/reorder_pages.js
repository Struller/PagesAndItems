/**
* @package Pages-and-Items (com_pagesanditems)
* @version 2.1.0
* @copyright Copyright (C) 2006-2009 Carsten Engel. All rights reserved.
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @author http://www.pages-and-items.com
* @joomla Joomla is Free Software
*/

function print_pages()
{	

	//alert(eval('number_of_columns_'+name));
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
		var k = 0;
		//loop through items
		for (i = 1; i <= pages_total; i++){
			//ms: here we set the class row0 ore row1  for different color
			html_pages = html_pages+'<tr class="row'+k+'">';
			for (j = 1; j <= number_of_columns_pages; j++){
				page_column = "page_column_"+j+"_"+i;			
				page_column_content = document.getElementById(page_column).innerHTML;
				html_pages = html_pages+'<td id="print_pages_'+j+'_'+i+'">'+page_column_content+'</td>';
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
					html_pages = html_pages+'<a title="'+ moveUp +'" class="jgrid" href="javascript: reorder_pages('+i+','+(i-1)+');"><span class="state uparrow"><span class="text">'+ moveUp +'</span></span></a>';
				}
				else
				{
					html_pages = html_pages+'<a href="javascript: reorder_pages('+i+','+(i-1)+');"><img src="/administrator/images/uparrow.png" alt="move up" border="0" /></a>';
				}
			}else{
				html_pages = html_pages+'&nbsp;';
			}
			html_pages = html_pages+'</td>';
			html_pages = html_pages+'<td width="1">';
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
					html_pages = html_pages+'<a title="'+ moveDown +'" class="jgrid" href="javascript: reorder_pages('+i+','+(i+1)+');"><span class="state downarrow"><span class="text">'+ moveDown +'</span></span></a>';
				}
				else
				{
					html_pages = html_pages+'<a href="javascript: reorder_pages('+i+','+(i+1)+');"><img src="/administrator/images/downarrow.png" alt="move down" border="0" /></a>';
				}
			
			}else{
				html_pages = html_pages+'&nbsp;';
			}
			html_pages = html_pages+'</td>';
			html_pages = html_pages+'<td width="18">';
			html_pages = html_pages+"&nbsp;";
			html_pages = html_pages+'</td>';
			html_pages = html_pages+'</tr>';
			k = 1 - k;
		}
		html_pages = html_pages+'</table>';	
	}else{
		html_pages = no_pages;
	}
	
	document.getElementById('target_pages').innerHTML = html_pages;
	//problems with tooltips
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
	

	//move data from old divs to new destination
	for (m = 1; m <= number_of_columns_pages; m++){
		//get destionation content
		destination_div = "page_column_"+m+"_"+newPosition;	
		destination_content = document.getElementById(destination_div).innerHTML;
		//get id for div departure
		departure_div = "page_column_"+m+"_"+oldPosition;
		
		destination_td = "print_pages_"+m+"_"+newPosition;
		departure_td = "print_pages_"+m+"_"+oldPosition;
		
		
		//move the data from old to new
		//document.getElementById(destination_div).innerHTML = document.getElementById(departure_div).innerHTML;
		
		//paste data from the destination field into the departure field
		//document.getElementById(departure_div).innerHTML = destination_content;
		
		//ms: the tooltip is lost here other way to change
		
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
	//refresh list make problems with tooltips at this point only
	//print_pages();
	
	//mark that items have been reordered
	document.getElementById("pages_are_reordered").value = 1;

}
/*
ms: this is also in views/page/tmpl/default.php but i have comment out 
also here
is in javascript/submit_actions_pages.js

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
*/