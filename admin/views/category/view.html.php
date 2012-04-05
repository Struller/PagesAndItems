<?php
/**
* @version		2.1.5
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'view.html.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'page'.DS.'view.html.php');

/**
 * HTML View class for the  component

 */


class PagesAndItemsViewCategory extends PagesAndItemsViewDefault
{
	protected $items;
	protected $pagination;
	protected $state;
	
	function display($tpl = null)
	{
		//if ($model = $this->getModel('contentcategory'))
		if ($model = $this->getModel('category'))
		{
			$this->assignRef( 'model',$model);
		}
		$lang = &JFactory::getLanguage();
		//$lang->load('com_categories', JPATH_ADMINISTRATOR, null, false);
		$extension = 'com_categories';
		$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false) || $lang->load(strtolower($extension), JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
		// set the form path
		JForm::addFormPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'models'.DS.'forms');
		// set the fields path
		JForm::addFieldPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'models'.DS.'fields');
		
		
		/*
		$this->addModelPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'models');
		$model	= $this->getModel( 'Category' ,'CategoriesModel');
		
		*/
		//if ($modelCategory = $this->getModel('Category'))
		
		$return = JRequest::getVar('return', '');
		$this->assignRef( 'return',$return);
		
		$hideTree = JRequest::getVar('hideTree', 0);
		$this->assignRef( 'hideTree',$hideTree);
		
		$categoryExtension = JRequest::getVar('categoryExtension', 'com_content'); // 'com_banners'); //'content');
		$this->assignRef( 'categoryExtension',$categoryExtension);
		$categoryId = JRequest::getVar('categoryId', 1);
		
		$this->inputCategoryExtension = '';
		//$this->inputCategoryExtension = $tree->getSelect();
		//$treeClass = $tree->getTreeClass();

		
		$sub_task = JRequest::getVar('sub_task', '');
		$this->useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
		
		$tree = PagesAndItemsHelper::getTree();
		$treeClass = $tree->getTreeClass();
		//$this->tree = $tree->getTree($categoryId);
		
		
		$languageSelect = PagesAndItemsHelper::makeLanguageSelect();
		
		$this->tree = $tree->getTree();
		
		if($this->useCheckedOut && $sub_task != '')
		{
			$this->languageSelect = '';
			$this->inputCategoryExtension = $treeClass->getHiddenCategoryExtension();
		}
		else
		{
			$this->languageSelect = $languageSelect;
			$this->inputCategoryExtension = $treeClass->getSelectCategoryExtension();
		}
		
		
		
		
		$this->icons = $treeClass->icons;
		
		//if ($modelCategory = $this->getModel('Category'))
		if ($modelCategory = $this->getModel('CategoriesCategory'))
		{
			$component = (strpos($categoryExtension,'com_') !== false) ? strtolower($categoryExtension) : 'com_'.strtolower($categoryExtension);
			JRequest::setVar('extension',$component);//'com_content');
			$this->assignRef( 'modelCategories',$modelCategory);
			
			$parentCategoryId = JRequest::getVar('parentCategoryId', 1);
			if($categoryId == 1)
			{
				$categoryId = 0;
			}
			JRequest::setVar('id',$categoryId);
			$modelCategory->setState('category.id',$categoryId);
			$modelCategory->setState('category.component',$component);//'com_content');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'helpers'.DS.'categories.php');

			

			if($sub_task == 'new')
			{
				JRequest::setVar('id',0);
				
				$modelCategory->setState('category.id',0);
				$this->item = $modelCategory->getItem(0);
				$this->form = $modelCategory->getForm();
				$this->form->setValue('parent_id',null,$parentCategoryId);
				$this->categoryId = $categoryId;
				$this->parentCategoryId = $parentCategoryId;
			}
			else
			{
				$this->form = $modelCategory->getForm();
				
				$this->form->setFieldAttribute('parent_id','parent','true');
				
				$this->item = $modelCategory->getItem($categoryId);
				$this->categoryId = $categoryId;
				$this->parentCategoryId = $categoryId;
				

				if($this->useCheckedOut && $sub_task=='edit')
				{
					$modelCategory->checkout();
				}
			}
			$this->state	= $modelCategory->getState();
			
			//must get from com_content??
			//$this->canDo	= PagesAndItemsHelper::getCanDoContent($categoryId);
			$this->canDo	= CategoriesHelper::getActions($this->state->get('category.component'));


			$db = JFactory::getDbo();
			$user = JFactory::getUser();
		/*
		$query = $db->getQuery(true);
		$query->select('s.time, s.client_id, u.id, u.name, u.username');
		$query->from('#__session AS s');
		$query->leftJoin('#__users AS u ON s.userid = u.id');
		$query->where('s.guest = 0');
		$query->where('s.client_id = 1');// AND 
		$query->where('u.id <> '.$user->get('id'));
		
		$db->setQuery($query); //, 0, $params->get('count', 5));
		$results = $db->loadObjectList();
		$countAdminUsers = count($results);
		*/
			$app		= JFactory::getApplication();
			$userId		= $user->get('id');
			$userName	= $user->get('name');
			$this->canCreate	= $this->canDo->get('core.create');
			$this->canEdit	= $this->canDo->get('core.edit');
			$this->canCheckin	= $user->authorise('core.manage', 'com_checkin') && ($this->item->checked_out==$user->get('id')|| $this->item->checked_out==0);
			// || !$countAdminUsers);
			$this->canChange	= $this->canDo->get('core.edit.state') && $this->canCheckin;

		}
		else
		{
		
		}
		
		//$path = str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).DS.'..'.DS.'..')));
		$path = PagesAndItemsHelper::getDirComponentAdmin();
		/*
		JHTML::_('behavior.framework'); //first we must load mootools
			
			JHTML::script('Mif.Tree.js', $path.'/media/js/Core/',false);
			JHTML::script('Mif.Tree.Node.js', $path.'/media/js/Core/',false);
			JHTML::script('Mif.Tree.Hover.js', $path.'/media/js/Core/',false);
			JHTML::script('Mif.Tree.Selection.js', $path.'/media/js/Core/',false);
			JHTML::script('Mif.Tree.Load.js', $path.'/media/js/Core/',false);
			JHTML::script('Mif.Tree.Draw.js', $path.'/media/js/Core/',false);

			JHTML::script('Mif.Tree.KeyNav.js', $path.'/media/js/More/',false);
			JHTML::script('Mif.Tree.Sort.js', $path.'/media/js/More/',false);
			JHTML::script('Mif.Tree.Transform.js', $path.'/media/js/More/',false);
			//JHTML::script('Mif.Tree.Drag.js', $path.'/media/js/More/',false);
			JHTML::script('Mif.Tree.Element.js', $path.'/media/js/More/',false);
			JHTML::script('Mif.Tree.Checkbox.js', $path.'/media/js/More/',false);
			JHTML::script('Mif.Tree.Rename.js', $path.'/media/js/More/',false);
			JHTML::script('Mif.Tree.CookieStorage.js', $path.'/media/js/More/',false);


			JHTML::stylesheet('mif-tree_checkboxes.css', $path.'/media/css/');
		*/
		JHTML::stylesheet('dtree.css', $path.'/css/');
		JHtml::_('behavior.formvalidation');
		/*
		$categories = $model->getCategories();
		$this->assignRef( 'categories',$categories);
		*/
		/*
		$menuItemsTypes = PagesAndItemsHelper::getMenuItemsTypes();
		$this->assignRef( 'menuItemsTypes',$menuItemsTypes);
		*/
	
		
		$childs = $model->getChilds($this->icons);
		$this->assignRef( 'childs',$childs);
		
		$categoryItems = $model->getCategoryItems();
		$this->assignRef( 'categoryItems',$categoryItems);
		


		//PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_CATEGORIEANDITEMS_CATEGORY').'</small>');
		$sub_task = JRequest::getVar('sub_task','');
		if($sub_task=='new')
		{
			PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('JCATEGORY').': ['.JText::_('COM_PAGESANDITEMS_NEW').']</small>');
		}
		elseif($sub_task=='edit')
		{
			PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('JCATEGORY').': ['.JText::_('COM_PAGESANDITEMS_EDIT').']</small>');
		}
		else
		{
			PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('JCATEGORY').'</small>');
		}
		
		
			
		
		JHTML::_('behavior.tooltip');

		parent::display($tpl);
		$this->addToolbar();

	}
	

	protected function addToolbar()
	{
		$sub_task = JRequest::getVar('sub_task', '');
		//$vName = 'page'
		
		if($this->canDo->get('core.create') && ($sub_task != 'new' && ($sub_task != 'edit' && $this->useCheckedOut)))
		{
			//JToolBarHelper::custom('category.category_create','new','new','JTOOLBAR_NEW',false);
			//JToolBarHelper::create('category.category_create');//,'new','new','JTOOLBAR_NEW',false);
			JToolBarHelper::addNew('category.category_create');
			JToolBarHelper::divider();
		}
		if($sub_task=='new')
		{
			JToolBarHelper::apply( 'category.category_apply'); //, JText::_('COM_PAGESANDITEMS_APPLY') );
			JToolBarHelper::save( 'category.category_save'); //, JText::_('COM_PAGESANDITEMS_SAVE') );
			JToolBarHelper::divider();
			JToolBarHelper::cancel( 'category.cancel'); //, JText::_('COM_PAGESANDITEMS_CANCEL') );
		}
		elseif($sub_task=='edit')
		{
			if($this->useCheckedOut) 
			{
				if($this->canDo->get('core.edit') ) 
				{
					//JToolBarHelper::custom('category.category_checkin','checkin','checkin', JText::_('JTOOLBAR_APPLY').' & '.JText::_('JTOOLBAR_CHECKIN'), false);
					JRequest::setVar('hidemainmenu', true);
				}
				if($this->canDo->get('core.edit'))
				{
					JToolBarHelper::apply( 'category.category_apply'); //, JText::_('COM_PAGESANDITEMS_APPLY') );
					JToolBarHelper::save( 'category.category_checkin'); //category.category_save'); //, JText::_('COM_PAGESANDITEMS_SAVE') );
					JToolBarHelper::divider();
				}
			}
			else
			{
				if($this->canDo->get('core.edit'))
				{
					JToolBarHelper::apply( 'category.category_apply'); //, JText::_('COM_PAGESANDITEMS_APPLY') );
					JToolBarHelper::save( 'category.category_save'); //, JText::_('COM_PAGESANDITEMS_SAVE') );
				}
				if($this->canDo->get('core.delete'))
				{
					JToolBarHelper::custom('category.category_delete','delete','delete','JTOOLBAR_DELETE',false);
				}
				if($this->canDo->get('core.delete') && $this->canDo->get('core.edit'))
				{
					JToolBarHelper::divider();
				}
			}
		
			JToolBarHelper::cancel( 'category.cancel'); //, JText::_('COM_PAGESANDITEMS_CANCEL') );
		}
		else
		{
			$categoryId = JRequest::getVar('categoryId',1);
			if($this->useCheckedOut && $categoryId > 1)
			{
				if ($this->canDo->get('core.edit') && $this->canCheckin) 
				{
					JToolBarHelper::custom('category.category_edit','edit','edit', 'JTOOLBAR_EDIT', false);
					JToolBarHelper::divider();
				}
				if($this->canDo->get('core.edit') )
				{
					/*
					Alternativ 1
					JToolBarHelper::apply( 'category.reorder_apply', JText::_('JGRID_HEADING_ORDERING').' '.JText::_('JTOOLBAR_APPLY'));//, JText::_('COM_PAGESANDITEMS_APPLY') );
					JToolBarHelper::save( 'category.reorder_save', JText::_('JGRID_HEADING_ORDERING').' '.JText::_('JTOOLBAR_SAVE'));
					*/
					/*
					Alternativ 2
					JToolBarHelper::custom('category.reorder_apply', 'apply_order','apply_order', JText::_('JTOOLBAR_APPLY'),false);//, 
					JToolBarHelper::custom('category.reorder_save', 'save_order','save_order',JText::_('JTOOLBAR_SAVE'),false);
					*/
					/*
					Alternativ 3 added an icon in the lists header
					*/
					
				}
				if($this->canDo->get('core.delete'))
				{
					JToolBarHelper::custom('category.category_delete','delete','delete','JTOOLBAR_DELETE',false);
				}
				if($this->canDo->get('core.delete') || $this->canDo->get('core.edit') )
				{
					JToolBarHelper::divider();
				}
				

				JToolBarHelper::cancel( 'category.cancel'); //, JText::_('COM_PAGESANDITEMS_CANCEL') );
			}
			else
			{
				if($this->canDo->get('core.edit'))
				{
					JToolBarHelper::apply( 'category.root_save', JText::_('JGRID_HEADING_ORDERING').' '.JText::_('JTOOLBAR_APPLY')); //, JText::_('COM_PAGESANDITEMS_APPLY') );
					//JToolBarHelper::save( 'category.root_save', JText::_('COM_PAGESANDITEMS_SAVE') );
					JToolBarHelper::divider();
				}	
				JToolBarHelper::cancel( 'category.cancel'); //, JText::_('COM_PAGESANDITEMS_CANCEL') );
			}
		}
	}
}
