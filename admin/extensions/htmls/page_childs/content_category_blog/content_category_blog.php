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
* Html item content_category_blog           *
*********************************
*/
class PagesAndItemsExtensionHtmlPage_childsContent_category_blog extends PagesAndItemsExtensionHtml
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
				/*
COM_CONTENT_ARCHIVE_VIEW_DEFAULT_TITLE="Archivierte Beiträge"
COM_CONTENT_ARTICLE_VIEW_DEFAULT_TITLE="Einzelner Beitrag"
COM_CONTENT_CATEGORIES_VIEW_DEFAULT_TITLE="Alle Kategorien auflisten"
COM_CONTENT_CATEGORY_VIEW_BLOG_TITLE="Kategorie-Blog"
COM_CONTENT_CATEGORY_VIEW_DEFAULT_TITLE="Kategorie Liste"
COM_CONTENT_CATEGORY_VIEW_FEATURED_TITLE="Einzelne vorgestellte Beitragskategorie"
COM_CONTENT_FEATURED_VIEW_DEFAULT_TITLE="Vorgestellte Beiträge"
COM_CONTENT_FORM_VIEW_DEFAULT_TITLE="Beitrag erstellen"

*/
		if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
		{
			$categoryBlogText = JText::_('COM_PAGESANDITEMS_CATEGORY').'-'.JText::_('COM_PAGESANDITEMS_BLOG');
		}
		else
		{
			$categoryBlogText = JText::_('COM_CONTENT_CATEGORY_VIEW_BLOG_TITLE');
		}


		$htmlelementVars->text = $categoryBlogText;
		$htmlelementVars->alt = JText::_('COM_PAGESANDITEMS_MAKE_NEW_MENU_ITEM').': '.$categoryBlogText;
		//if(isset($menuItemsTypes->content_category_blog->icons->add->imageUrl))
		//{
			$htmlelementVars->imageName = $htmlOptions->menuItemsTypes['content_category_blog']->icons->add->imageUrl;
		//}

		//$htmlelementVars->onclick = 'document.location.href=\'index.php?option=com_pagesanditems&pageType=content_category_blog&type=component&amp;view=page'.$layout.'&sub_task=new&amp;pageId='.$pageId.'&amp;menutype='.$current_menutype.'\';';

		$onclick = '';
		$onclick ='document.getElementById(\'pageType\').value = \'content_category_blog\'; ';
		$onclick .='document.getElementById(\'type\').value = \'component\'; ';
		//$onclick .='document.getElementById(\'sub_task\').value = \'new\'; ';
		$onclick .= 'document.getElementById(\'pageTypeType\').value = \''.base64_encode(json_encode(array('id' => null, 'title' => JText::_('COM_PAGESANDITEMS_CATEGORY').'-'.JText::_('COM_PAGESANDITEMS_BLOG'), 'request' => array('option' => 'com_content','view'=>'category','layout'=>'blog'), 'type'=>'component'))).'\';';
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