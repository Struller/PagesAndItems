/**
* @package Pages-and-Items (com_pagesanditems)
* @version 2.1.0
* @copyright Copyright (C) 2006-2009 Carsten Engel. All rights reserved.
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @author http://www.pages-and-items.com
* @joomla Joomla is Free Software
*/

function saveReorderItemsRows(name,task){
	
	//var reordered =document.id('items_'+name+'_are_reordered').value;
	//alert('saveReorder Name: ' + name + ', Task:' + task + ', TaskValue: ' + task+'.'+name+'_reorder_save');
	//category_reorder_save

	document.getElementById('task').value = task+'.'+name+'_reorder_save';
	document.adminForm.submit();

	
	
	
	//document.getElementById('sub_task').value = 'edit';
	//document.getElementById('subsub_task').value = 'apply';
	//document.getElementById('task').value = pressbutton;
	//document.adminForm.submit();
}


