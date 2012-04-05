<?php
/**
* @version		2.1.5
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
jimport('joomla.application.component.controlleradmin');
jimport('joomla.client.helper');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controller.php');


class PagesAndItemsControllerCategory extends JControllerAdmin
{

	function __construct( $default = array())
	{
		parent::__construct($default);
		$this->registerTask( 'category_apply', 'category_save' );
		$this->registerTask( 'category_reorder_apply', 'category_reorder_save' );
		$this->registerTask( 'reorder_apply', 'reorder_save' );
	}

	function category_checkin()
	{
		JRequest::setVar('task', 'category_apply'); //, 'cmd');
		JRequest::setVar('checkin', true);
		$this->category_save();
	}

	function change_extension()
	{
		$categoryId = '&categoryId=1';
	
		$categoryExtension = JRequest::getVar('categoryExtension', 'com_content');
		$categoryExtension = $categoryExtension ? '&categoryExtension='.$categoryExtension : '';
		
		$url = 'index.php?option=com_pagesanditems&view=category'.$categoryId.$categoryExtension;
		$this->setRedirect(JRoute::_($url, false));
	}

	function category_edit()
	{
		$categoryId = JRequest::getVar('categoryId', 0 );
		
		$categoryId = $categoryId ? '&categoryId='.$categoryId : '';
		$sub_task = '&sub_task=edit';
		
		$categoryExtension = JRequest::getVar('categoryExtension', 'com_content');
		$categoryExtension = $categoryExtension ? '&categoryExtension='.$categoryExtension : '';
		
		$url = 'index.php?option=com_pagesanditems&view=category'.$sub_task.$categoryId.$categoryExtension;
		$this->setRedirect(JRoute::_($url, false));
	}


	function category_create()
	{
		$categoryId = JRequest::getVar('categoryId', 0 );
		$parentCategoryId = JRequest::getVar('parentCategoryId', 0 );
		
		$categoryExtension = JRequest::getVar('categoryExtension', 'com_content');
		$categoryExtension = $categoryExtension ? '&categoryExtension='.$categoryExtension : '';
		
		$url = 'index.php?option=com_pagesanditems&view=category&sub_task=new&categoryId='.$categoryId.'&parentCategoryId='.$parentCategoryId.$categoryExtension;
		$this->setRedirect(JRoute::_($url, false));
	}

	function root_save()
	{
		$this->category_reorder();
		$categoryExtension = JRequest::getVar('categoryExtension', 'com_content');
		$categoryExtension = $categoryExtension ? '&categoryExtension='.$categoryExtension : '';
		$url = 'index.php?option=com_pagesanditems&view=category'.$categoryExtension;
		$this->setRedirect(JRoute::_($url, false));
	}
	
	
	function item_reorder()
	{
		$db = JFactory::getDBO();
		$message = '';
		$items_are_reordered = JRequest::getVar('items_item_are_reordered',0);
		$items_total = JRequest::getVar('items_item_total',0);
		if($items_are_reordered==1){
			for ($n = 1; $n <= $items_total; $n++){
				$temp_id = intval(JRequest::getVar('reorder_item_id_'.$n, '', 'post'));
				$db->setQuery( "UPDATE #__content SET ordering='$n' WHERE id='$temp_id'");
				$db->query();
			}
			$message = JText::_('COM_PAGESANDITEMS_ITEMS_ORDER_SAVED');
		}
		return $message;
	}
	
	function item_reorder_save()
	{
		$message = $this->item_reorder();
		$this->reorder_save('reorder_apply',$message);
	}

	function category_reorder()
	{
		//if categories where reordered update the ordering of these categories
		$categories_are_reordered = JRequest::getVar('items_category_are_reordered',0);
		$categories_total = JRequest::getVar('items_category_total',0);
		
		$categoryExtension = JRequest::getVar('categoryExtension', 'com_content');
		$categoryExtension = $categoryExtension ? '&categoryExtension='.$categoryExtension : '';
		
		$message = '';
		if($categories_are_reordered==1)
		{
			$ids = array();
			$left = array();
			for ($n = 1; $n <= $categories_total; $n++){
				$temp_id = intval(JRequest::getVar('reorder_category_id_'.$n, '', 'post'));
				$lft = intval(JRequest::getVar('reorder_category_lft_'.$n, '', 'post'));
				$ids[] = $temp_id;
				$left[] = $lft;
			}
			//save and rebuild category tree
			//here we get the orginal
			//$this->addModelPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'models');
			//$model = $this->getModel( 'Category' ,'CategoriesModel');
			$model	= $this->getModel( 'CategoriesCategory' ,'PagesAndItemsModel');
			
			//$model->saveorder($ids, $left);
			//$model->rebuild();
			//$message = JText::_('COM_PAGESANDITEMS_CATEGORY_ORDER_SAVED');
			//$app->enqueueMessage($message);
			$return = $model->saveorder($ids, $left);

			if ($return === false)
			{
				// Reorder failed
				$message = JText::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError());
				//$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false), $message, 'error');
				//return false;
			} else
			{
				// Reorder succeeded.
				$message = JText::_('JLIB_APPLICATION_SUCCESS_ORDERING_SAVED');
				//$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
				//return true;
			}
		}
		return $message;
	}
	
	function category_reorder_save()
	{
		$message = $this->category_reorder();
		$this->reorder_save('reorder_apply',$message);
	}
	
	function reorder_save($task = null,$messageString = '')
	{
		$message = array();
		if($task)
		{
			$message[] = $messageString;
			//$task = ;
		}
		else
		{
			if($msg = $this->category_reorder())
			$message[] = $msg;
			if($msg = $this->item_reorder())
			$message[] = $msg;
			$task = JRequest::getVar('task', ''); //, 'category_save', 'cmd');
		}

		$data = JRequest::getVar('jform', array(), 'post', 'array');
		$categoryId = JRequest::getVar('categoryId', 0 );
		$item->id = $data['id'];
		//$item->menutype = $data['menutype'];
		$item->parent_id = $data['parent_id'];
		
		$categoryExtension = JRequest::getVar('categoryExtension', 'com_content');
		$categoryExtension = $categoryExtension ? '&categoryExtension='.$categoryExtension : '';
		
		$hideTree = JRequest::getVar('hideTree', 0 );
		$subsub_task = JRequest::getVar('subsub_task', 'save');
		$parent_id = 1;
		//$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
		if($item)
		{
			$parent_id = $item->parent_id;
			$return = JRequest::getVar('return', '');
			if($task = 'reorder_apply' || $subsub_task == 'apply')
			{
				if($task != 'reorder_apply' && $return != '')
				{
					$return = '&return='.$return;
				}
				//$checkin = JRequest::getVar('checkin', false);
				//here we get $checkin only if we useCheckedOut and task = page_checkin
				//and if so we use no edit sub_task
				$sub_task = ''; //$useCheckedOut ? '' : '&sub_task=edit';
				
				$url = 'index.php?option=com_pagesanditems&view=category'.$sub_task.'&categoryId='.$item->id.'&hideTree='.$hideTree.$return.$categoryExtension;
			}
			else
			{
				if($return != '')
				{
					$url = base64_decode($return);
				}
				else
				{
					if($item->parent_id > 1)
					{
						$sub_task = ''; //$useCheckedOut ? '' : '&sub_task=edit';
						$url = 'index.php?option=com_pagesanditems&view=category'.$sub_task.'&categoryId='.$item->parent_id.$categoryExtension;
					}
					else
					{
						$url = 'index.php?option=com_pagesanditems&view=category&categoryId='.$item->parent_id.$categoryExtension;
					}
				}
			}
		}
		else
		{
			$url = 'index.php?option=com_pagesanditems&view=category&categoryId='.$categoryId.$categoryExtension;
		}
		$message = (count($message) ? ''.implode(', ', $message) : '');
		$this->setRedirect(JRoute::_($url, false), $message);
	}
	
	


	//is not function category_checkin()
	function checkin()
	{
		//JRequest::setVar('task', 'category_apply'); //, 'cmd');
		//JRequest::setVar('checkin', true);
		//JRequest::setVar('useCheckin', 1);
		//$this->category_save();
		$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
		$categoryId = JRequest::getVar('categoryId', 0 );
		$categoryExtension = JRequest::getVar('categoryExtension', 'com_content');

		$categoryExtension = $categoryExtension ? '&categoryExtension='.$categoryExtension : '';
		if($useCheckedOut)
		{
			$user = JFactory::getUser();
			//TODO realice it over the model
			// Get an instance of the row to checkin.
			$table = JTable::getInstance('category'); //, $prefix, $config); //'content';
			if (!$table->load($categoryId)) {
				//$this->setError($table->getError());
				//return false;
			}

			// Check if this is the user having previously checked out the row.
			if ($table->checked_out > 0 && $table->checked_out != $user->get('id') && !$user->authorise('core.admin', 'com_checkin')) {
				//$this->setError(JText::_('JLIB_APPLICATION_ERROR_CHECKIN_USER_MISMATCH'));
				//return false;
			}

			// Attempt to check the row in.
			if (!$table->checkin($categoryId)) {
				//$this->setError($table->getError());
				//return false;
			}
		}
		$return = JRequest::getVar('return', '');
		$url = 'index.php?option=com_pagesanditems&view=category&categoryId='.$categoryId.$return.$categoryExtension;
		$this->setRedirect(JRoute::_($url, false)); //, JText::_('COM_PAGESANDITEMS_ACTION_CANCELED'));
	}


	function cancel()
	{
		//$model = &$this->getModel('Categorie','PagesAndItemsModel');
		$sub_task = JRequest::getVar('sub_task', '');
		$pageId = JRequest::getVar('pageId', 0);
		$menutype = JRequest::getVar('menutype');
		$categoryId = JRequest::getVar('categoryId', 1 );
		$parentCategoryId = JRequest::getVar('parentCategoryId', 1 );
		
		$categoryExtension = JRequest::getVar('categoryExtension', 'com_content');
		$categoryExtension = $categoryExtension ? '&categoryExtension='.$categoryExtension : '';
		
		$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
		if($useCheckedOut && $sub_task == 'edit')
		{
			$user = JFactory::getUser();
			//TODO realice it over the model
			// Get an instance of the row to checkin.
			$table = JTable::getInstance('category'); //, $prefix, $config); //'content';
			if (!$table->load($categoryId)) {
				//$this->setError($table->getError());
				//return false;
			}

			// Check if this is the user having previously checked out the row.
			if ($table->checked_out > 0 && $table->checked_out != $user->get('id') && !$user->authorise('core.admin', 'com_checkin')) {
				//$this->setError(JText::_('JLIB_APPLICATION_ERROR_CHECKIN_USER_MISMATCH'));
				//return false;
			}

			// Attempt to check the row in.
			if (!$table->checkin($categoryId)) {
				//$this->setError($table->getError());
				//return false;
			}
		}
		
		$return = JRequest::getVar('return', '');
		if($return != '')
		{
			$url = base64_decode($return);
		}
		else
		{
			//we must get parent_id and if > 1 we go to parent?
			$str_sub_task = $useCheckedOut ? '' : '&sub_task=edit';
			if($sub_task == 'edit' && $useCheckedOut)
			{
				$url = 'index.php?option=com_pagesanditems&view=category&categoryId='.$categoryId.$categoryExtension;
			}
			elseif($parentCategoryId && $parentCategoryId <> $categoryId)
			{
				//go to parent
				$url = 'index.php?option=com_pagesanditems&view=category'.$str_sub_task.'&categoryId='.$parentCategoryId.$categoryExtension;
			}
			elseif($categoryId == 1 && $sub_task != 'new')
			{
				$url = "index.php?option=com_pagesanditems&view=page&layout=root";
			}
			elseif($sub_task == 'new')
			{
				$url = 'index.php?option=com_pagesanditems&view=category'.$str_sub_task.'&categoryId='.($categoryId ? $categoryId : 1).$categoryExtension;
			}
			else
			{
				//check if we have an paren_id 
				$table = JTable::getInstance('category'); //, $prefix, $config); //'content';
				if (!$table->load($categoryId)) 
				{
					$url = "index.php?option=com_pagesanditems&view=page&layout=root";
				}
				elseif($table->parent_id >= 1)
				{
					$url = 'index.php?option=com_pagesanditems&view=category'.$str_sub_task.'&categoryId='.$table->parent_id.$categoryExtension;
				}
				else
				{
					$url = "index.php?option=com_pagesanditems&view=page&layout=root";
				}
			}
		}
		$this->setRedirect(JRoute::_($url, false), JText::_('COM_PAGESANDITEMS_ACTION_CANCELED'));
		//$model->redirect_to_url($url, JText::_('COM_PAGESANDITEMS_ACTION_CANCELED'));
	}
	
	//
	function category_save()
	{

		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		$message = array();
		if($msg = $this->category_reorder())
			$message[] = $msg;
		if($msg = $this->item_reorder())
			$message[] = $msg;
		
		//$message[] = $this->category_reorder();
		//$message[] = $this->item_reorder();

		$database = JFactory::getDBO();
		$app = JFactory::getApplication();

		$categoryExtension = JRequest::getVar('categoryExtension', 'com_content');
		$categoryExtension = $categoryExtension ? '&categoryExtension='.$categoryExtension : '';

		//$model = &$this->getModel('Categorie','PagesAndItemsModel');
		
		//get data
		$data = JRequest::getVar('jform', array(), 'post', 'array');
		$new_or_edit = 'new';
		if($data['id'])
		{
			$new_or_edit = 'edit';
		}
		$item = $this->item_save($data);
		//$message = 'save';
		$categoryId = JRequest::getVar('categoryId', 0 );
		
		
		$hideTree = JRequest::getVar('hideTree', 0 );
		$subsub_task = JRequest::getVar('subsub_task', 'save');
		$parent_id = 1;
		$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
		if($item)
		{
			$parent_id = $item->parent_id;
			$return = JRequest::getVar('return', '');
			if($subsub_task == 'apply')
			{
				
				if($return != '')
				{
					$return = '&return='.$return;
				}
				$checkin = JRequest::getVar('checkin', false);
				//here we get $checkin only if we useCheckedOut and task = page_checkin
				//and if so we use no edit sub_task
				$sub_task = $checkin ? '' : '&sub_task=edit';
				
				$url = 'index.php?option=com_pagesanditems&view=category'.$sub_task.'&categoryId='.$item->id.'&hideTree='.$hideTree.$return.$categoryExtension;
			}
			else
			{
				if($return != '')
				{
					$url = base64_decode($return);
				}
				else
				{
					if($item->parent_id > 1)
					{
						$sub_task = $useCheckedOut ? '' : '&sub_task=edit';
						$url = 'index.php?option=com_pagesanditems&view=category'.$sub_task.'&categoryId='.$item->parent_id.$categoryExtension;
					}
					else
					{
						$url = 'index.php?option=com_pagesanditems&view=category&categoryId='.$item->parent_id.$categoryExtension;
					}
				}
			}
		}
		else
		{
			$url = 'index.php?option=com_pagesanditems&view=category&categoryId='.$categoryId.$categoryExtension;
		}
		
		$message = (count($message) ? ''.implode(', ', $message) : '');
		if($message)
		$this->setMessage($message.$this->message);

		
		$this->setRedirect(JRoute::_($url, false)); //, $message);
		
	}
	
	
	
	function item_save($data,$new_state = null){

		$app = JFactory::getApplication();
		
		//here we get the orginal
		//$this->addModelPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'models');
		//$model	= $this->getModel( 'Category' ,'CategoriesModel');
		$model = $this->getModel('CategoriesCategory','PagesAndItemsModel');
		
		$lang = &JFactory::getLanguage();
		//$lang->load('com_categories', JPATH_ADMINISTRATOR, null, false);
		$extension = 'com_categories';
		$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false) || $lang->load(strtolower($extension), JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
		
		// set the form path
		JForm::addFormPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'models'.DS.'forms');
		// set the fields path
		JForm::addFieldPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'models'.DS.'fields');
		$changeState = false;
		if(is_array($data))
		{
			// Sometimes the form needs some posted data, such as for plugins and modules.
			$form = $model->getForm($data, false);
			$changeState = true;
		}
		else
		{
			$model->setState('category.extension','');
			
			$newdata['id'] = $data;
			
			//$newdata['extension'] = 'com_content';
			$categoryExtension = JRequest::getVar('categoryExtension', 'com_content');
			$newdata['extension'] = $categoryExtension;
			
			$form = $model->getForm($newdata, false);
			$data = $model->getItem($newdata);
			$data->published = $new_state;
			$changeState = true;
		}
		
		
		
		$new_or_edit = 'new';
		$canDo = PagesAndItemsHelper::canDoContent($data['id']);
		if($changeState)
		{
			if(!$canDo->get('core.edit.state'))
			{
				//
				return false;
			}
		}
		if($data['id'])
		{
			$new_or_edit = 'edit';
			if(!$canDo->get('core.create'))
			{
				//
				return false;
			}
		}
		else
		{
			if(!$canDo->get('core.edit'))
			{
				//
				return false;
			}
		}
		//check the rights
		// Validate the posted data.
		if (!$form) {
			//$app->enqueueMessage($model->getError(), 'error');

			return false;
		}

		// Test whether the data is valid.
		$validData = $model->validate($form, $data);
		
		// Check for validation errors.
		if ($validData === false) {
			// Get the validation messages.
			$errors	= $model->getErrors();

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

			// Save the data in the session.
			//$app->setUserState($context.'.data', $data);

			// Redirect back to the edit screen.
			//$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_item.$this->getRedirectToItemAppend($recordId, $key), false));
			return false;
		}
		
		// Attempt to save the data.
		if (!$model->save($validData)) {
			// Save the data in the session.
			//$app->setUserState($context.'.data', $validData);

			// Redirect back to the edit screen.
			$app->enqueueMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'error');
			//$this->setMessage($this->getError(), 'error');
			//$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_item.$this->getRedirectToItemAppend($recordId, $key), false));

			return false;
		}
		/*
		*/
		$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
		// Save succeeded, so check-in the record.
		if ($useCheckedOut && $model->checkin($validData[$key]) === false) {
			// Save the data in the session.
			//$app->setUserState($context.'.data', $validData);

			// Check-in failed, so go back to the record and display a notice.
			//$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
			//$this->setMessage($this->getError(), 'error');
			//$this->setRedirect('index.php?option='.$this->option.'&view='.$this->view_item.$this->getRedirectToItemAppend($recordId, $key));

			return false;
		}
		$this->setMessage(JText::_('COM_CATEGORIES_SAVE_SUCCESS'), 'info');
		return $model->getItem();
	}



	function category_delete()
	{
		// Check for request forgeries
		JRequest::checkToken() or die(JText::_('JINVALID_TOKEN'));
		$categoryId = JRequest::getVar('categoryId', 1 );
		
		$categoryExtension = JRequest::getVar('categoryExtension', 'com_content');
		$categoryExtension = $categoryExtension ? '&categoryExtension='.$categoryExtension : '';
		
		$cid = array($categoryId);
		$lang		= JFactory::getLanguage();
		// Load extension-local file.
		$lang->load('com_categories', JPATH_ADMINISTRATOR, null, false, false)
		||	$lang->load('com_categories', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
		if($this->item_delete($cid))
		{
			//here we need the parent id
			$data = JRequest::getVar('jform', array(), 'post', 'array');
			$categoryId = $data['parent_id'];
			
			$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
			$sub_task = $useCheckedOut ? '' : '&sub_task=edit';
			
			$sub_task = ($categoryId > 1 ? $sub_task : '');
			
			$url = 'index.php?option=com_pagesanditems&view=category&categoryId='.$categoryId.$sub_task.$categoryExtension;
		}
		else
		{
			//$sub_task = $useCheckedOut ? '' : '&sub_task=edit';
			$sub_task = JRequest::getVar('sub_task');
			$sub_task = $sub_task ? '&sub_task='.$sub_task : '';
			$url = 'index.php?option=com_pagesanditems&view=category'.$sub_task.'&categoryId='.$categoryId.$categoryExtension;
		}
		$this->setRedirect(JRoute::_($url, false)); //, $message);
		
	}

	//called from small buttons on view categorie 
	function categories_state()
	{
		JRequest::checkToken() or die(JText::_('JINVALID_TOKEN'));
		$lang		= JFactory::getLanguage();
		// Load extension-local file.
		$lang->load('com_categories', JPATH_ADMINISTRATOR, null, false, false)
		||	$lang->load('com_categories', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
		
		$cid = JRequest::getVar('categoryCid', array(), 'post', 'array');
		
		$subsub_task = JRequest::getVar('subsub_task');
		if($subsub_task=='delete'){
			$new_state = 'delete';
		}elseif($subsub_task=='trash'){
			$new_state = '-2';
		}elseif($subsub_task=='archive'){
			$new_state = '2';
		}elseif($subsub_task=='publish'){
			$new_state = '1';
		}elseif($subsub_task=='unpublish'){
			$new_state = '0';
		}
		
		if(count($cid) && $subsub_task != 'delete')
		{
			$this->item_publish($cid, $new_state);
		}
		elseif(count($cid) && $subsub_task == 'delete')
		{
			$this->item_delete($cid);
		}
		else{
			//no items
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('COM_PAGESANDITEMS_NO_ITEMS_SELECTED'));
		}

		$categoryId = JRequest::getVar('categoryId', null );
		/*
		we must check if the item id is the categoryID ???
		*/
		/*
		if($sub_task == 'delete' && in_array($categoryId,$cid))
		{
			//here we need the parent id
			$data = JRequest::getVar('jform', array(), 'post', 'array');
			$categoryId = $data['parent_id'];
			$url = 'index.php?option=com_pagesanditems&view=categorie&categoryId='.$categoryId.($categoryId > 1 ? '&sub_task=edit' : '');
		*/
		$pageId = JRequest::getVar('pageId');
		$menutype = JRequest::getVar('menutype');

		$categoryExtension = JRequest::getVar('categoryExtension', 'com_content');
		$categoryExtension = $categoryExtension ? '&categoryExtension='.$categoryExtension : '';
		$sub_task = JRequest::getVar('sub_task');
		//$sub_task = ($sub_task ? '&sub_task='.$sub_task : '');
		if($pageId) //$categoryId == '' || !$categoryId)
		{
			//goto view page
			$url = 'index.php?option=com_pagesanditems&view=page'.($sub_task ? '&sub_task='.$sub_task : '').'&pageId='.$pageId.'&menutype='.$menutype;
			
		}
		elseif($categoryId)
		{
			$url = 'index.php?option=com_pagesanditems&view=category'.($sub_task ? '&sub_task='.$sub_task : '').'&categoryId='.$categoryId.$categoryExtension;
		}
		else
		{
			$url = 'index.php?option=com_pagesanditems&view=category'.$categoryExtension;
		}
		
		$this->setRedirect(JRoute::_($url, false));

	}

	function item_publish($cid, $value)
	{
		// Get the model.
		//here we get the orginal
		//$this->addModelPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'models');
		//$model	= $this->getModel( 'Category' ,'CategoriesModel');
		$model	= $this->getModel( 'CategoriesCategory' ,'PagesAndItemsModel');
		
		// Make sure the item ids are integers
		JArrayHelper::toInteger($cid);

		// Publish the items.
		if (!$model->publish($cid, $value)) 
		{
			JError::raiseWarning(500, $model->getError());
		}
		else 
		{
			if ($value == 1) {
				$ntext = 'COM_CATEGORIES_N_ITEMS_PUBLISHED';
			}
			else if ($value == 0) {
				$ntext = 'COM_CATEGORIES_N_ITEMS_UNPUBLISHED';
			}
			else if ($value == 2) {
				$ntext = 'COM_CATEGORIES_N_ITEMS_ARCHIVED';
			}
			else {
				$ntext = 'COM_CATEGORIES_N_ITEMS_TRASHED';
			}
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::plural($ntext, count($cid)));
		}
	}

	function item_delete($cid)
	{
		$config = array();
		$config['option'] = 'com_categories';
		
		//here we get an extended
		//$model	= $this->getModel( 'CategoryCategory' ,'PagesAndItemsModel',$config);
		$model	= $this->getModel( 'CategoriesCategory' ,'PagesAndItemsModel',$config);
		
		jimport('joomla.utilities.arrayhelper');
		JArrayHelper::toInteger($cid);
		$app = JFactory::getApplication();
		
		$categoryExtension = JRequest::getVar('categoryExtension', 'com_content');
		JRequest::setVar('extension',$categoryExtension); //'com_content');
		
		//load com_categories lang
			
		// Remove the items.
		if ($model->delete($cid)) {
			$app->enqueueMessage(JText::plural('COM_CATEGORIES_N_ITEMS_DELETED', count($cid)));
			return true;
		} elseif($model->getError()) {
			$app->enqueueMessage($model->getError());
			return false;
		}
	}
}
?>