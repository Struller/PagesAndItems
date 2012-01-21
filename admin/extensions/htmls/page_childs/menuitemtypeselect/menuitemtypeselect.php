<?php
/**
* @version		2.1.0
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
**********************************
* Html item menuitemtypeselect *
**********************************
*/
class PagesAndItemsExtensionHtmlPage_childsMenuitemtypeselect extends PagesAndItemsExtensionHtml
{

	//function onGetHtmlelement(&$htmlelement,$htmlelementVars=null,$name = null, $controller = null, $menuItemsTypes = null, $pageId = null, $current_menutype = null)
	//function onGetHtmlelement(&$htmlelement,$name = null, $controller = null, $menuItemsTypes = null, $pageId = null, $current_menutype = null)
	function onGetHtmlelement(&$htmlelement,$name = null,$htmlOptions = null)
	{

		if($name != 'page_childs')
		{
			return false;
		}


		//if(!isset($htmlelementVars->imagePath))
		//{
			$htmlelementVars->imagePath = PagesAndItemsHelper::getDirIcons(); //$this->dirIcons;
		//}

		$sub_task = JRequest::getVar( 'sub_task', 'new');
		$pageType = JRequest::getVar( 'pageType', '' );
		$pageUrl = JRequest::getVar( 'pageUrl', array());
		$pageId = JRequest::getVar( 'pageId', 0);
		/*
		$layout = JRequest::getCmd('layout', '');
		if($layout && $layout != '')
		{
			//$layout = '&pagelayout='.$layout.'';
			$layout='';
		}
		*/
		//$link = 'index.php?option=com_pagesanditems&amp;view=menuitemtypeselect'.$layout.'&amp;tmpl=component&amp;sub_task=new&amp;pageType='.$pageType.'&amp;menutype='.$current_menutype.'&amp;pageId='.$pageId;

		/*

		$link = 'index.php?option=com_pagesanditems&amp;view=menuitemtypeselect&amp;tmpl=component&amp;sub_task=new&amp;pageType='.$pageType.'&amp;menutype='.$current_menutype.'&amp;pageId='.$pageId;
		*/
		/*
		we will test the extension controller
		*/

		$link = 'index.php?option=com_pagesanditems'; //.$option;
		$link .= '&amp;task=extension.doExecute'; //need to add .display?
		$link .= '&amp;extension=menuitemtypeselect';
		$link .= '&amp;extensionType=html';
		$link .= '&amp;extensionFolder=page_childs'; ///menuitemtypeselect';
		//$link .= '&amp;sub_task=display'; //
		//$link .= '&amp;extensionTask=display';
		$link .= '&amp;view=menuitemtypeselect';
		$link .= '&amp;tmpl=component';
		$link .= '&amp;pageType='.$pageType;
		$link .= '&amp;menutype='.$htmlOptions->current_menutype;
		$link .= '&amp;pageId='.$pageId;

		$size_x = '600';
		$size_y = '450';
		$size = 'size: { x: '.$size_x.' , y: '.$size_y.'}';
		//$options = "{handler: 'iframe', ".$size."}"; //GENERALQUESTION with "" we can use {} with '' not why?
		$options = "handler: 'iframe', ".$size;
		$htmlelementVars->text = JText::_('COM_PAGESANDITEMS_SELECT_MENU_ITEM_TYPE');
		$htmlelementVars->alt = JText::_('COM_PAGESANDITEMS_MAKE_NEW_MENU_ITEM').': '.JText::_('COM_PAGESANDITEMS_SELECT_MENU_ITEM_TYPE');
		$htmlelementVars->imageName = 'base/icon-16-menu_add.png';
		$htmlelementVars->rel = $options;
		$htmlelementVars->href = $link;
		$htmlelementVars->modal = true;
		$htmlelement->html = $htmlelement->html.parent::onGetButton($htmlelement,$htmlelementVars,$name); //parent::onGetButton(&$htmlelement,$htmlelementVars,$name);
		return true;
	}

}

?>