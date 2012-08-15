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
* Html item PUBLISH *
*********************************
*/
class PagesAndItemsExtensionHtmlCategory_actionsPublish extends PagesAndItemsExtensionHtml
{
	function onGetHtmlelement(&$htmlelement,$name = null, $htmlOptions = null)
	{
		if($name != 'category_actions' || !$htmlOptions->canDo->get('core.edit.state'))
		{
			return false;
		}
		$htmlelementVars->buttonType = 'input';
		$htmlelementVars->alt = JText::_('COM_PAGESANDITEMS_ITEMS_PUBLISH');
		$htmlelementVars->onclick = 'javascript:if (document.adminForm.boxcheckedCategory.value==0){alert(\''.JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST').'\');}else{ PagesAndItemsCategories.submitbutton(\'category.categories_publish\')}';
		//$htmlelementVars->imagePath = PagesAndItemsHelper::getDirIcons(); //$this->dirIcons;
		//$htmlelementVars->imageName = 'base/icon-16-tick_switch.png';
		$htmlelementVars->imageName = 'class:state_publish';
		$htmlelement->html = $htmlelement->html.parent::onGetButton($htmlelement,$htmlelementVars,$name);
		return true;
	}
}

?>