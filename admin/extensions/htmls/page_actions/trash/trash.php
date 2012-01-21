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
* Html item trash             *
********************************
*/
class PagesAndItemsExtensionHtmlPage_actionsTrash extends PagesAndItemsExtensionHtml
{
	
	//function onGetHtmlelement(&$htmlelement,$htmlelementVars=null,$name = null)
	function onGetHtmlelement(&$htmlelement,$name = null, $htmlOptions = null)
	{
		//TODO visible over extensions/managers/trash ?
		if($name != 'page_actions' || !$htmlOptions->canDo->get('core.edit.state'))
		{
			return false;
		}
		
		//if(!isset($htmlelementVars->imagePath))
		//{
		$htmlelementVars->imagePath = $this->dirIcons;
		//}
		$htmlelementVars->alt = JText::_('COM_PAGESANDITEMS_ITEMS_TRASH'); //JText::_('PI_EXTENSION_HTML_PAGE_ACTIONS_TRASH_TIP');
		//$htmlelementVars->onclick = 'javascript:Joomla.submitbutton(\'page.pages_trash\')';
		$htmlelementVars->onclick = 'javascript:if (document.adminForm.boxcheckedPage.value==0){alert(\''.JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST').'\');}else{ Joomla.submitbutton(\'page.pages_trash\')}';
		$htmlelementVars->imageName = 'trash/icon-16-trash.png';
		$htmlelementVars->imageName = 'base/icon-16-trash_switch.png';
		$htmlelement->html = $htmlelement->html.parent::onGetButton($htmlelement,$htmlelementVars,$name); //parent::onGetButton(&$htmlelement,$htmlelementVars,$name);
		return true;
	}
}

?>