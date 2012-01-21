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

require_once(dirname(__FILE__).'/../../../includes/extensions/pagetype.php');

//
class PagesAndItemsExtensionPagetypeContentFeatured extends PagesAndItemsExtensionPagetype
{

	function onGetPageTypeIcons($icons,$pageType,$dirIcons, $component)
	{
		if($pageType != 'content_featured')
		{
			return false;
		}
		$icons = parent::onGetPageTypeIcons($icons,$pageType,$dirIcons, $component);
		return true;
	}

	function onGetContentItems(&$ContentItems,$model)
	{
	
		$ContentItems = $this->getContentItems($model);
	}
	
	
	
	function getContentItems($model)
	{
		$menuItem = $model->menuItem;
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'lists'.DS.'itemslist.php');
		$ItemsList = new ItemsList();
		return $ItemsList->getContentItems(true,true,true,$menuItem,'COM_PAGESANDITEMS_ITEMS');
	}

	function onGetPageItems(&$html,$model)
	{
		if(isset($model->menuItemsType->icons->default->imageUrl))
		{
			$image = $model->menuItemsType->icons->default->imageUrl;
		}
/*
		if($image)
		{
			$image = '<img src="'.$image.'" alt="" style="vertical-align: middle;position: relative;" />&nbsp;';
		}
		else
		{
			$image = '<img src="'.PagesAndItemsHelper::getDirIcons().'icon-16-menu.png" alt="" style="vertical-align: middle;" />&nbsp;';
		}
		*/
		if(!$image)
		{
			$image = PagesAndItemsHelper::getDirIcons().'icon-16-menu.png';
		}
		
		$html .= '<table class="piadminform xadminform" width="98%">';
			$html .= '<thead class="piheader">';
				$html .= '<tr>';
					$html .= '<th>'; // class="piheader">';//style="background: none repeat scroll 0 0 #F0F0F0;border-bottom: 1px solid #999999;">';
						//$html .= $image;
						//$html .= JText::_('COM_PAGESANDITEMS_ITEMS_ON_PAGE');
						$html .= PagesAndItemsHelper::getThImageTitle($image,JText::_('COM_PAGESANDITEMS_ITEMS_ON_PAGE'));
					$html .= '</th>';
				$html .= '</tr>';
			$html .= '</thead>';
			$html .= '<tbody>';
				/*
				$html .= '<tr>';
					$html .= '<td>';
						$html .= 'from pagetypes/content_featured/content_featured.php';
					$html .= '</td>';
				$html .= '</tr>';
				*/
				$html .= '<tr>';
					$html .= '<td>';
						//$html .= $model->getContentItems();//($editToolbarButtons = true, $newToolbarButtons = true) oder (array(),array()
						$html .= $this->getContentItems($model);
					$html .= '</td>';
				$html .= '</tr>';
			$html .= '</tbody>';
		$html .= '</table>';
		return true;

	}

	function subLayoutItems()
	{
		return 1;
	}

	function onGetListItems(&$listItems)
	{
		$listItems = 1;
		return true;
	}


	// hide some in the pagepropertys if need
	// or add
	function onGetLists(&$lists,$pageMenuItem,$model)
	{
		/*
		$lists->display->id = 'style="color: green;font-style: oblique;font-size: large;"';
		$addtop = '<tr style="color: blue;font-style: oblique;">';
		$addtop .= '<td colspan="2">';
		$addtop .= 'display from pagetypes/components/content/frontpage/frontpage.php lists->add->top';
		$addtop .= '<br />';
		$addtop .= 'the green color in id from pagetypes/components/content/frontpage/frontpage.php lists->display-id ';
		$addtop .= '<br />';
		$addtop .= 'we can add something here also script-code';
		$addtop .= '</td>';
		$addtop .= '</tr>';

		$lists->add->top =$addtop;
		$addbottom = '<tr>';
		$addbottom .= '<td>';
		$addbottom .= 'display from pagetypes/components/content/frontpage/frontpage.php lists->add->bottom';
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