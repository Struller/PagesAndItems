
if (typeof(PagesAndItemsPages) === 'undefined') {
	var PagesAndItemsPages = {};
}

PagesAndItemsPages.submitbutton = function(pressbutton){

	if (pressbutton == 'page.pages_archive'){
		are_you_sure = Joomla.JText._('COM_PAGESANDITEMS_CONFIRM_PAGES_ARCHIVE');
		if(confirm(are_you_sure)){
			document.getElementById('subsub_task').value = 'archive';
			document.getElementById('task').value = 'page.pages_state';
			document.adminForm.submit();
		}
	}
	if (pressbutton == 'page.pages_trash'){
		
		are_you_sure = Joomla.JText._('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH2') + '?\n\n';
		are_you_sure = are_you_sure + Joomla.JText._('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH3')+':\n';
		are_you_sure = are_you_sure + '- ' + Joomla.JText._('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH4') +'\n';
		if(page_trash_cat){
			are_you_sure = are_you_sure + '- ' + Joomla.JText._('COM_PAGESANDITEMS_IF_PAGE') + ' ' + Joomla.JText._('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH5')+ '\n';
		}
		if(page_trash_items){
			are_you_sure = are_you_sure + '- ' + Joomla.JText._('COM_PAGESANDITEMS_IF_PAGE')+ ' ' + Joomla.JText._('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH6') + '\n';
		}
		/*
		are_you_sure = Joomla.JText._('COM_PAGESANDITEMS_CONFIRM_PAGES_TRASH');
		*/
		if(confirm(are_you_sure)){
			document.getElementById('subsub_task').value = 'trash';
			document.getElementById('task').value = 'page.pages_state';
			document.adminForm.submit();
		}
	}
	if (pressbutton == 'page.pages_delete'){
		are_you_sure = Joomla.JText._('COM_PAGESANDITEMS_CONFIRM_PAGE_DELETE2') + '?\n\n';
		are_you_sure = are_you_sure + Joomla.JText._('COM_PAGESANDITEMS_CONFIRM_PAGE_DELETE1')+':\n';
		are_you_sure = are_you_sure + '- ' + Joomla.JText._('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH4') +'\n';
		if(page_delete_cat){
			are_you_sure = are_you_sure + '- ' + Joomla.JText._('COM_PAGESANDITEMS_IF_PAGE') + ' ' + Joomla.JText._('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH5')+ '\n';
		}
		if(page_delete_items){
			are_you_sure = are_you_sure + '- ' + Joomla.JText._('COM_PAGESANDITEMS_IF_PAGE')+ ' ' + Joomla.JText._('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH6') + '\n';
		}

		/*
		are_you_sure = Joomla.JText._('COM_PAGESANDITEMS_CONFIRM_PAGES_DELETE');
		*/
		if(confirm(are_you_sure)){
			document.getElementById('subsub_task').value = 'delete';
			document.getElementById('task').value = 'page.pages_state';
			document.adminForm.submit();
		}
	}
	if (pressbutton == 'page.pages_publish'){
		document.getElementById('subsub_task').value = 'publish';
		document.getElementById('task').value = 'page.pages_state';
		document.adminForm.submit();
	}
	if (pressbutton == 'page.pages_unpublish'){
		document.getElementById('subsub_task').value = 'unpublish';
		document.getElementById('task').value = 'page.pages_state';
		document.adminForm.submit();
	}
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
		subsub_task = 'publish';
	}else{
		subsub_task = 'unpublish';
	}
	
	//submit
	document.getElementById('subsub_task').value = subsub_task;
	document.getElementById('task').value = 'page.pages_state';
	document.adminForm.submit();
}
