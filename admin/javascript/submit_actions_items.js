
if (typeof(PagesAndItemsItems) === 'undefined') {
	var PagesAndItemsItems = {};
}

PagesAndItemsItems.submitbutton = function(pressbutton){
	if (pressbutton == 'item.items_archive'){
		are_you_sure = Joomla.JText._('COM_PAGESANDITEMS_CONFIRM_ITEMS_ARCHIVE');
		if(confirm(are_you_sure)){
			document.getElementById('subsub_task').value = 'archive';
			document.getElementById('task').value = 'item.items_state';
			document.adminForm.submit();
		}
	}
	if (pressbutton == 'item.items_trash'){
		are_you_sure = Joomla.JText._('COM_PAGESANDITEMS_CONFIRM_ITEMS_TRASH');
		if(confirm(are_you_sure)){
			document.getElementById('subsub_task').value = 'trash';
			document.getElementById('task').value = 'item.items_state';
			document.adminForm.submit();
		}
	}
	if (pressbutton == 'item.items_delete'){
		are_you_sure = Joomla.JText._('COM_PAGESANDITEMS_CONFIRM_ITEMS_DELETE');
		if(confirm(are_you_sure)){
			document.getElementById('subsub_task').value = 'delete';
			document.getElementById('task').value = 'item.items_state';
			document.adminForm.submit();
		}
	}
	if (pressbutton == 'item.items_publish'){
		document.getElementById('subsub_task').value = 'publish';
		document.getElementById('task').value = 'item.items_state';
		document.adminForm.submit();
	}
	if (pressbutton == 'item.items_unpublish'){
		document.getElementById('subsub_task').value = 'unpublish';
		document.getElementById('task').value = 'item.items_state';
		document.adminForm.submit();
	}
}

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
		subsub_task = 'publish';
	}else{
		subsub_task = 'unpublish';
	}

	//submit
	document.getElementById('subsub_task').value = subsub_task;
	document.getElementById('task').value = 'item.items_state';
	//alert('item');
	document.adminForm.submit();
}