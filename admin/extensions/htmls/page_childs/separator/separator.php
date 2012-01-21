<?php
/**
* @version		2.1.1
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
* Html item separator         *
*********************************
*/
class PagesAndItemsExtensionHtmlPage_childsSeparator extends PagesAndItemsExtensionHtml
{

	//function onGetHtmlelement(&$htmlelement,$htmlelementVars=null,$name = null, $controller = null, $menuItemsTypes = null, $pageId = null, $current_menutype = null)
	//function onGetHtmlelement(&$htmlelement,$name = null, $controller = null, $menuItemsTypes = null, $pageId = null, $current_menutype = null)
	function onGetHtmlelement(&$htmlelement,$name = null,$htmlOptions = null)
	{

		if($name != 'page_childs')
		{
			return false;
		}

		/*
		if(!isset($htmlelementVars->imagePath))
		{
			$htmlelementVars->imagePath = PagesAndItemsHelper::getDirIcons(); //$this->dirIcons;
		}
		*/
		if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
		{
			$JTextSeparator = JText::_('SEPARATOR');
		}
		else
		{
			$JTextSeparator = JText::_('COM_MENUS_TYPE_SEPARATOR');
		}

		$htmlelementVars->text = $JTextSeparator;
		$htmlelementVars->alt = JText::_('COM_PAGESANDITEMS_MAKE_NEW_MENU_ITEM').': '.$JTextSeparator;
		//if(isset($menuItemsTypes->separator->icons->add->imageUrl))
		//{
			$htmlelementVars->imageName = $htmlOptions->menuItemsTypes['separator']->icons->add->imageUrl;
		//}

		//$htmlelementVars->onclick = 'document.location.href=\'index.php?option=com_pagesanditems&pageType=separator&type=separator&view=page'.$layout.'&sub_task=new&pageId='.$pageId.'&menutype='.$current_menutype.'\';';
		//.'&edit=0\'';
		$onclick = '';
		$onclick ='document.getElementById(\'pageType\').value = \'separator\'; ';
		$onclick .='document.getElementById(\'type\').value = \'separator\'; ';
		//$onclick .='document.getElementById(\'sub_task\').value = \'new\'; ';
		$onclick .= 'document.getElementById(\'pageTypeType\').value = \''.base64_encode(json_encode(array('id' => null, 'title' => $JTextSeparator, 'request' => array(), 'type'=>'separator'))).'\';';
		if(PagesAndItemsHelper::getIsJoomlaVersion('>=','1.6'))
		{
			$onclick .='Joomla.';
		}
		$onclick .='submitbutton(\'newMenuItem\'); ';
		$htmlelementVars->onclick = $onclick;



		$htmlelement->html = $htmlelement->html.parent::onGetButton($htmlelement,$htmlelementVars,$name); //parent::onGetButton(&$htmlelement,$htmlelementVars,$name);
		return true;
	}
}

?>