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
* Html item UNPUBLISH *
*********************************
*/
class PagesAndItemsExtensionHtmlPage_actionsUnpublish extends PagesAndItemsExtensionHtml
{
	
	//function onGetHtmlelement(&$htmlelement,$htmlelementVars=null,$name = null)
	function onGetHtmlelement(&$htmlelement,$name = null, $htmlOptions = null)
	{
		if($name != 'page_actions' || !$htmlOptions->canDo->get('core.edit.state'))
		{
			return false;
		}
		
		$htmlelementVars->imagePath = $this->dirIcons;
		$htmlelementVars->buttonType = 'input';
		$htmlelementVars->alt = JText::_('COM_PAGESANDITEMS_ITEMS_UNPUBLISH');
		//$htmlelementVars->onclick = 'javascript:Joomla.submitbutton(\'page.pages_unpublish\')';	
		$htmlelementVars->onclick = 'javascript:if (document.adminForm.boxcheckedPage.value==0){alert(\''.JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST').'\');}else{ Joomla.submitbutton(\'page.pages_unpublish\')}';
		$htmlelementVars->imageName = 'base/icon-16-cross.png';
		$htmlelementVars->imageName = 'base/icon-16-cross_switch.png';
		//$htmlelementVars->class = 'button icon-16-unpublish';
		$htmlelement->html = $htmlelement->html.parent::onGetButton($htmlelement,$htmlelementVars,$name); 

		/*
		$htmlelementVars = null;

		$htmlelementVars->buttonType = 'aspan';
		$htmlelementVars->alt = JText::_('COM_PAGESANDITEMS_ITEM_UNPUBLISH');
		$htmlelementVars->alt = JText::_('COM_PAGESANDITEMS_ITEM_UNPUBLISH');
		$htmlelementVars->onclick = 'javascript:Joomla.submitbutton(\'item.items_unpublish\')';
		$htmlelementVars->imageName = 'unpublish';
		$htmlelement->html = $htmlelement->html.parent::onGetButton($htmlelement,$htmlelementVars,$name); 
		*/
		return true;
	}
}

?>