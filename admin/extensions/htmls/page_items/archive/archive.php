<?php
/**
* @version		2.0.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2011 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

//no direct access
if(!defined('_JEXEC'))
{
	die('Restricted access');
}

require_once(dirname(__FILE__).'/../../../../includes/extensions/html.php');

/**
*********************************
* Html item archive           *
*********************************
*/
class PagesAndItemsExtensionHtmlPage_itemsArchive extends PagesAndItemsExtensionHtml
{
	
	//function onGetHtmlelement(&$htmlelement,$htmlelementVars=null,$name = null)
	function onGetHtmlelement(&$htmlelement,$name = null, $htmlOptions = null)
	{
		//TODO visible over extensions/managers/archive ?
		if($name != 'page_items' || !$htmlOptions->canDo->get('core.edit.state'))
		{
			return false;
		}
		
		//if(!isset($htmlelementVars->imagePath))
		//{
			$htmlelementVars->imagePath = $this->dirIcons;
		//}
		$htmlelementVars->alt = JText::_('COM_PAGESANDITEMS_ITEMS_ARCHIVE'); //JText::_('PI_EXTENSION_HTML_PAGE_ITEMS_ARCHIVE_TIP');
		//$htmlelementVars->onclick = 'alert(\'for future\')';
		$link = 'index.php?option=com_pagesanditems';
		
		/*
			here we load the PagesAndItemsControllerExtension (controllers/extension.php)
			only need extension. not extension.display display is the default task
			but without '.' the base controller will get the task
			
			
		*/
		$link .= '&task=extension.doExecute';
		//$link .= '&task=extension.doExecute'; //need to add .display
		
		/*
			we must tell the PagesAndItemsControllerExtension something about the extension
		*/
		$link .= '&extension=archive'; //the name
		$link .= '&extensionType=html'; // the type
		$link .= '&extensionFolder=page_items'; //the folder
		$link .= '&extension_sub_task=addToArchive'; //the task 
		//$htmlelementVars->onclick = 'javascript:Joomla.submitbutton(\'item.items_archive\')';
		$htmlelementVars->onclick = 'javascript:if (document.adminForm.boxcheckedItem.value==0){alert(\''.JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST').'\');}else{ Joomla.submitbutton(\'item.items_archive\')}';
		/*
			here we set something add 
		
		$script="
		<script language=\"javascript\" type=\"text/javascript\">
		function submitbuttonHtmlPage_itemsArchive()
		{
				task = document.getElementById('task');
				task.value = 'extension.doExecute'; //
				
				extension = document.createElement('input');
				extension.setAttribute('type','hidden');
				extension.setAttribute('name','extension');
				extension.setAttribute('value','archive');
				
				extensionType = document.createElement('input');
				extensionType.setAttribute('type','hidden');
				extensionType.setAttribute('name','extensionType');
				extensionType.setAttribute('value','html');
				
				
				extensionFolder = document.createElement('input');
				extensionFolder.setAttribute('type','hidden');
				extensionFolder.setAttribute('name','extensionFolder');
				extensionFolder.setAttribute('value','page_items');
				
				extension_sub_task = document.createElement('input');
				extension_sub_task.setAttribute('type','hidden');
				extension_sub_task.setAttribute('name','extension_sub_task');
				extension_sub_task.setAttribute('value','addToArchive');
				
				task.parentNode.insertBefore( extension, task.nextSibling );
				task.parentNode.insertBefore( extensionType, task.nextSibling );
				task.parentNode.insertBefore( extensionFolder, task.nextSibling );
				task.parentNode.insertBefore( extension_sub_task, task.nextSibling );
				
				document.adminForm.submit();
		}
		</script>";
		*/
		//$htmlelementVars->onclick = 'javascript: submitbuttonHtmlPage_itemsArchive();'; //'alert(\'comming soon
		$htmlelementVars->imageName = 'archive/icon-16-archive.png';
		$htmlelementVars->imageName = 'base/icon-16-archive_switch.png';
		//$htmlelementVars->imageName = 'archive/icon-16-safe.png';
		
		$htmlelement->html = $htmlelement->html.parent::onGetButton($htmlelement,$htmlelementVars,$name); //parent::onGetButton(&$htmlelement,$htmlelementVars,$name);
		//$htmlelement->html = $htmlelement->html.$script;
		//$htmlelement = $htmlelement.parent::onGetHtmlelement(&$htmlelement,$htmlelementVars,$name);
		/*
		$newHtmlelement = parent::onGetHtmlelement(&$htmlelement,$htmlelementVars,$name);
		$htmlelement->html = $newHtmlelement->html;
		*/
		return true;
	}
}

?>