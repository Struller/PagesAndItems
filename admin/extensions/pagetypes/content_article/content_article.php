<?php
/**
* @version		2.0.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2011 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

//no direct access
if(!defined('_JEXEC')){
	die('Restricted access');
}

require_once(dirname(__FILE__).'/../../../includes/extensions/pagetype.php');
//
class PagesAndItemsExtensionPagetypeContentArticle extends PagesAndItemsExtensionPagetype
{
	function onGetPageTypeIcons($icons,$pageType,$dirIcons, $component)
	{
		if($pageType != 'content_article')
		{
			return false;
		}
		$icons = parent::onGetPageTypeIcons($icons,$pageType,$dirIcons, $component);
		return true;
	}
	
	function onGetPageItems(&$html,$view)
	{
		if(isset($view->menuItemsType->icons->default->imageUrl))
		{
			$image = $view->menuItemsType->icons->default->imageUrl;
		}
		if($image)
		{
			$image = '<img src="'.$image.'" alt="" style="vertical-align: middle;position: relative;" />&nbsp;';
		}
		else
		{
			$image = '<img src="'.PagesAndItemsHelper::getDirIcons().'icon-16-menu.png" alt="" style="vertical-align: middle;" />&nbsp;';
		}
		$html = '';
		$html .= '<table class="adminform" width="98%">';
			$html .= '<tr>';
				$html .= '<th style="background: none repeat scroll 0 0 #F0F0F0;border-bottom: 1px solid #999999;">';
				$html .= $image;
				$html .= JText::_('COM_PAGESANDITEMS_ITEM_ON_PAGE');
				$html .= '</th>';
			$html .= '</tr>';
			/*
			$html .= '<tr>';
				$html .= '<td>';
					$html .= 'from pagetypes/content_article/content_article.php';
				$html .= '</td>';
			$html .= '</tr>';
			*/
			$html .= '<tr>';
				$html .= '<td>';
					$html .= $view->getContentItem();//($editToolbarButtons = true, $newToolbarButtons = true) oder (array(),array()
				$html .= '</td>';
			$html .= '</tr>';
		$html .= '</table>';
		return true;
		//return $html;
	}

	function subLayoutItems()
	{
		return 1;
	}
	
	function onGetListItems(&$listItems)
	{
		$listItems = 2;
		return true;
		//return 2;
	}
	
	
	// hide some in the pagepropertys if need
	// or add 
	function onGetLists(&$lists,$pageMenuItem,$model)
	{
		//see views/page/view.html.php function getLists
		//$lists->pageTypeClass = '<input type="hidden" name="pageTypeClass" value="Categoryblog" />';
		//$lists->display->id = 'style="display:none;"';
		/*
		$lists->display->id = 'style="color: green;font-style: oblique;font-size: large;"';
		//$lists->hideAll = 'style="display:none;"';
		// TODO add button new category and new section
		$addtop = '<tr style="color: blue;font-style: oblique;">';
		$addtop .= '<td colspan="2">';
		$addtop .= 'display from pagetypes/content_article/content_article.php lists->add->top';
		$addtop .= '<br />';
		$addtop .= 'the green color in id from pagetypes/content_article/content_article.php lists->display-id ';
		$addtop .= '<br />';
		$addtop .= 'we can add something here also script-code';
		
		$addtop .= '<br />';
		$addtop .= 'only in Joomla 1.6 we add here an button to create and save PI-Article';
		$addtop .= '<br />';
		$addtop .= 'and set the corect link and in: ';
		$addtop .= '<br />';
		$addtop .= 'input id="jform_request_id_name" value= ';
		$addtop .= '<br />';
		$addtop .= 'input name="jform[request][id]" id="jform_request_id_id';
		$addtop .= '</td>';
		$addtop .= '</tr>';
		//if(!$pageMenuItem->id)
		//{
		$addtop .= '<tr>';
			$addtop .= '<td colspan="2">';
				$addtop .= '<div class="paddingList">';
					$addtop .= $model->itemtype_select(0);
				$addtop .= '</div>';
			$addtop .= '</td>';
		$addtop .= '</tr>';
		//}
		//$addtop .= '<tr>';
		
		$lists->add->top =$addtop;
		
		$addbottom = '<tr>';
		$addbottom .= '<td>';
		$addbottom .= 'display from pagetypes/content_article/content_article.php lists->add->bottom';
		$addbottom .= '</td>';
		$addbottom .= '</tr>';
		$lists->add->bottom =$addbottom;
		*/
		return true;
	}
	
	function onBeforSave($data, $isnew)
	{
	
	}
	
	function onAfterSave($menu_id, $data, $isnew)
	{
	
	}
}

?>