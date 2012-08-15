<?php
/**
* @version		2.1.6
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
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
	function onGetHtmlelement(&$htmlelement,$name = null, $htmlOptions = null)
	{
		if($name != 'page_items' || !$htmlOptions->canDo->get('core.edit.state'))
		{
			return false;
		}

		$htmlelementVars->alt = JText::_('COM_PAGESANDITEMS_ITEMS_ARCHIVE');
		//$htmlelementVars->onclick = 'javascript:if (document.adminForm.boxcheckedItem.value==0){alert(\''.JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST').'\');}else{ Joomla.submitbutton(\'item.items_archive\')}';
		$htmlelementVars->onclick = 'javascript:if (document.adminForm.boxcheckedItem.value==0){alert(\''.JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST').'\');}else{ PagesAndItemsItems.submitbutton(\'item.items_archive\')}';
		//$htmlelementVars->imagePath = PagesAndItemsHelper::getDirIcons(); //$this->dirIcons;
		//$htmlelementVars->imageName = 'base/icon-16-archive_switch.png';
		$htmlelementVars->imageName = 'class:state_archive';

		$htmlelement->html = $htmlelement->html.parent::onGetButton($htmlelement,$htmlelementVars,$name);
		return true;
	}
}

?>