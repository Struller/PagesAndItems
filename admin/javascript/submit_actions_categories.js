
if (typeof(PagesAndItemsCategories) === 'undefined') {
	var PagesAndItemsCategories = {};
}

PagesAndItemsCategories.submitbutton = function(pressbutton){
	if (pressbutton == 'category.categories_archive'){
		are_you_sure = Joomla.JText._('COM_PAGESANDITEMS_CONFIRM_CATEGORIES_ARCHIVE');
		if(confirm(are_you_sure)){
			document.getElementById('subsub_task').value = 'archive';
			document.getElementById('task').value = 'category.categories_state';
			document.adminForm.submit();
		}
	}
	if (pressbutton == 'category.categories_trash'){
		are_you_sure = Joomla.JText._('COM_PAGESANDITEMS_CONFIRM_CATEGORIES_TRASH');
		if(confirm(are_you_sure)){
			document.getElementById('subsub_task').value = 'trash';
			document.getElementById('task').value = 'category.categories_state';
			document.adminForm.submit();
		}
	}
	if (pressbutton == 'category.categories_delete'){
		are_you_sure = Joomla.JText._('COM_PAGESANDITEMS_CONFIRM_CATEGORIES_DELETE');
		if(confirm(are_you_sure)){
			document.getElementById('subsub_task').value = 'delete';
			document.getElementById('task').value = 'category.categories_state';
			document.adminForm.submit();
		}
	}
	if (pressbutton == 'category.categories_publish'){
		document.getElementById('subsub_task').value = 'publish';
		document.getElementById('task').value = 'category.categories_state';
		document.adminForm.submit();
	}
	if (pressbutton == 'category.categories_unpublish'){
		document.getElementById('subsub_task').value = 'unpublish';
		document.getElementById('task').value = 'category.categories_state';
		document.adminForm.submit();
	}
}

function publish_unpublish_category(category_id, new_state)
{
	//unselect all checkboxes
	for (i = 0; i < category_ids.length; i++){
		box_id = 'categoryCid_'+category_ids[i];
		document.getElementById(box_id).checked = false;
	}
	
	//select the checkbox we need
	box_id = 'categoryCid_'+category_id;
	document.getElementById(box_id).checked = 'checked';
	
	
	if(new_state=='1'){
		subsub_task = 'publish';
	}else{
		subsub_task = 'unpublish';
	}
	
	//submit
	document.getElementById('subsub_task').value = subsub_task;
	document.getElementById('task').value = 'category.categories_state';
	document.adminForm.submit();
}
