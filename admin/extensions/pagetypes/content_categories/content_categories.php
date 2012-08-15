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
require_once(dirname(__FILE__).'/../../../includes/extensions/pagetype.php');
jimport( 'joomla.application.categories' );

class PagesAndItemsExtensionPagetypeContentCategories extends PagesAndItemsExtensionPagetype
{
	/*
	private $_parent = null;

	private $_items = null;
	
	private $_item = null;
	
	private $_maxLevelcat = null;
	*/
	
	function onGetPageTypeIcons($icons,$pageType,$dirIcons, $component)
	{
		if($pageType != 'content_categories')
		{
			return false;
		}
		$icons = parent::onGetPageTypeIcons($icons,$pageType,$dirIcons, $component);
		return true;
	}

	function onGetPageItems(&$html,$model)
	{
		$html = '';
		//in categories we have 'maxLevelcat' and 'maxlevel'
		//TODO from an own class underlyingCategories
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'lists'.DS.'categorieslist.php');
		$CategoriesList = new CategoriesList();
		//
		$html .= $CategoriesList->getCategories($html,$model,'maxLevelcat',true,true,true,'com_content');  //($html,$model,'maxLevel',false) if we want the slider is not open
		
		/*
		only show the categories her at this moment
		
		
		$item = $model->menu_item->getItem();
		$params = new JRegistry();
		$request = new JRegistry();
		
		$params->loadArray($item->params);
		$request->loadArray($item->request);
		
		jimport( 'joomla.application.component.helper' );
		$contentParams  = JComponentHelper::getParams('com_content');
		$content_show_subcategory_content = $contentParams->get('show_subcategory_content','');
		$show_subcategory_content = $params->get('show_subcategory_content');
				
		if($show_subcategory_content == '')
		{
			$show_subcategory_content = $content_show_subcategory_content;
		}
		
		if($show_subcategory_content)
		{
			if(isset($model->menuItemsType->icons->default->imageUrl))
			{
				$image = $model->menuItemsType->icons->default->imageUrl;
			}
			if($image)
			{
				$image = '<img src="'.$image.'" alt="" style="vertical-align: middle;position: relative;" />&nbsp;';
			}
			else
			{
				$image = '<img src="'.PagesAndItemsHelper::getDirIcons().'icon-16-menu.png" alt="" style="vertical-align: middle;" />&nbsp;';
			}
		
			$html .= '<table class="adminform" width="98%">';
				$html .= '<tr>';
					$html .= '<th style="background: none repeat scroll 0 0 #F0F0F0;border-bottom: 1px solid #999999;">';
						$html .= $image;
						$html .= JText::_('COM_PAGESANDITEMS_ITEMS_ON_PAGE');
					$html .= '</th>';
				$html .= '</tr>';
				//$html .= '<tr>';
					//$html .= '<td>';
						//$html .= 'from pagetypes/content_category_blog/content_category_blog.php';
					//$html .= '</td>';
				//$html .= '</tr>';
				$html .= '<tr>';
					$html .= '<td>';
						//TODO toolbar visible itemtypeselect visible
						$html .= $model->getContentItems();
					$html .= '</td>';
				$html .= '</tr>';
			$html .= '</table>';
		}
		*/
		
		return true;
	}

	function onAfterSave($menu_id, $data, $isnew,$item)
	{
		//if we need to create a new category
		if(JRequest::getVar('create_new_category', ''))
		{
			//we need to create a new category from the menu-item title

			$database = JFactory::getDBO();

			//$title = JRequest::getVar('jform[title]', '');
			$jform = JRequest::getVar('jform', array(), 'post', 'array');
			
			
			//ms: change
			$app = JFactory::getApplication();
			//$parent_id = $jform['request']['id'];
			$categoryData['id'] = 0;
			$categoryData['title'] = $item->title;
			$categoryData['alias'] = $item->alias;
			//$categoryData['parent_id'] = $parent_id;
			//ce: when creating a new category, set parent to 1
			//do not try to give the new category the parent_id of the menu-item
			$categoryData['parent_id'] = 1;
			$categoryData['extension'] = 'com_content';
			$categoryData['published'] = 1;
			$categoryData['access'] = 1;
			$categoryData['language'] = '*';
			//." params='{\"category_layout\":\"\",\"image\":\"\"}', "
			//." metadata='{\"author\":\"\",\"robots\":\"\"}', "
			
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'categoriescategory.php');
			jimport( 'joomla.database.table');
			JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'tables');
			
			$modelCategory = new PagesAndItemsModelCategoriesCategory();
			// set the form path
			JForm::addFormPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'models'.DS.'forms');
			// set the fields path
			JForm::addFieldPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'models'.DS.'fields');
			
			$modelCategory->setState('category.id',0);
			$modelCategory->setState('category.component','com_content');
			
			$form = $modelCategory->getForm($categoryData, false);
			$validData = $modelCategory->validate($form, $categoryData);
			// Check for validation errors.
			if ($validData === false) 
			{
				// Get the validation messages.
				$errors	= $modelCategory->getErrors();
				// Push up to three validation messages out to the user.
				for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
				{
					if (JError::isError($errors[$i])) {
						$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
					}
					else {
						$app->enqueueMessage($errors[$i], 'warning');
					}
				}
				return false;
			}
			// Attempt to save the data.
			if (!$modelCategory->save($validData)) {
				$app->enqueueMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $modelCategory->getError()), 'error');
				return false;
			}
			
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.'category.php');
			$controller = new PagesAndItemsControllerCategory();
			if($itemCategory = $modelCategory->getItem()) //$controller->item_save($data))
			{
				//update menu-item to new category id
				$database->setQuery( "UPDATE #__menu SET link='index.php?option=com_content&view=categories&id=$itemCategory->id' WHERE id='$menu_id' ");
				$database->query();
				return true;
			}
			return true;
			////ms: change end
		}
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
	function onGetLists(&$lists,$pageMenuItem,$view)
	{
		/*
		$lists->display->id = 'style="color: green;font-style: oblique;font-size: large;"';
		$addtop = '<tr style="color: blue;font-style: oblique;">';
		$addtop .= '<td colspan="2">';
		$addtop .= 'display from pagetypes/content_categories/content_categories.php lists->add->top';
		$addtop .= '<br />';
		$addtop .= 'the green color in id from pagetypes/content_categories/content_categories.php lists->display-id ';
		$addtop .= '<br />';
		$addtop .= 'we can add something here also script-code';
		$addtop .= '</td>';
		$addtop .= '</tr>';

		$lists->add->top =$addtop;
		$addbottom = '<tr>';
		$addbottom .= '<td>';
		$addbottom .= 'display from pagetypes/content_categories/content_categories.php lists->add->bottom';
		$addbottom .= '</td>';
		$addbottom .= '</tr>';
		$lists->add->bottom =$addbottom;
		*/
		return true;
	}

	function onBeforSave($data, $isnew)
	{

	}
}


?>