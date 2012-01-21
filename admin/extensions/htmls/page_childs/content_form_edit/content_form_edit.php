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
************************************
* Html item content_form_edit *
************************************
*/
class PagesAndItemsExtensionHtmlPage_childsContent_form_edit extends PagesAndItemsExtensionHtml
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
		$htmlelementVars->buttonType = 'input';
		$htmlelementVars->text = JText::_('COM_CONTENT_FORM_VIEW_DEFAULT_TITLE');
		$htmlelementVars->alt = JText::_('COM_PAGESANDITEMS_MAKE_NEW_MENU_ITEM').': '.JText::_('COM_CONTENT_FORM_VIEW_DEFAULT_TITLE');
		//if(isset($menuItemsTypes->content_form_edit->icons->add->imageUrl))
		//{
				$htmlelementVars->imageName = $htmlOptions->menuItemsTypes['content_form_edit']->icons->add->imageUrl;
		//}

		//$htmlelementVars->onclick = 'document.location.href=\'index.php?option=com_pagesanditems&pageType=content_submission&type=component&view=page'.$layout.'&sub_task=new&pageId='.$pageId.'&menutype='.$current_menutype.'\';';
		//.'url[option]=com_content&url[view]=article&url[layout]=form&edit=0\'';
		$onclick = '';
		$onclick ='document.getElementById(\'pageType\').value = \'content_form_edit\'; ';
		$onclick .='document.getElementById(\'type\').value = \'component\'; ';
		//$onclick .='document.getElementById(\'sub_task\').value = \'new\'; ';
		$onclick .= 'document.getElementById(\'pageTypeType\').value = \''.base64_encode(json_encode(array('id' => null, 'title' => JText::_('COM_CONTENT_FORM_VIEW_DEFAULT_TITLE'), 'request' => array('option' => 'com_content','view'=>'form','layout'=>'edit'), 'type'=>'component'))).'\';';
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