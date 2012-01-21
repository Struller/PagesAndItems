<?php
/**
* @version		2.0.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2011 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
jimport( 'joomla.application.component.controller' );
//jimport('joomla.filesystem.file');
//jimport('joomla.filesystem.folder');
/**
controller.php
*/
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controller.php');
/**
 * 
 *
 * @package		PagesAndItems
 */
class PagesAndItemsControllerPage extends PagesAndItemsController
{
	public $helper;

	function __construct( $config = array())
	{
		parent::__construct($config);
		
		//$this->registerTask( 'display', 'display' );
		$this->registerTask( 'root', 'display' );
		$this->registerTask( 'page_apply', 'page_save' );
		//$this->registerTask( 'root_underlayingpage', 'root_save' );
		$this->registerTask( 'root_save', 'page_save' );
		$this->registerTask( 'root_apply', 'page_save' );
		//$this->registerTask( 'edit', 'edit' );
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'pagesanditems.php');
		$this->helper = new PagesAndItemsHelper();
	}
	
	function edit()
	{
		//echo 'display';
		//$this->assignRef('edit', true);
		JRequest::setVar('pages_edit',true);
		$this->display();
	}

	/**
	 * Display the view
	 */
	function display($tpl = null)
	{
		$vName = strtolower(JRequest::getCmd('view', 'page'));
		switch ($vName)
		{
			case 'page':
				$mName = 'Page_item';
				//$vLayout = JRequest::getCmd( 'layout', 'pages' );
				//$vLayout = JRequest::getCmd( 'layout', 'edit' );
				$vLayout = 'edit';
				/*
				if(JRequest::getCmd('view', 0) && JRequest::getCmd('sub_task', 0))
				{
					$vLayout = JRequest::getCmd( 'sub_task', 'edit' );
					//JRequest::setVar('pages_edit',true);
				}
				*/
				//$vLayout = JRequest::getCmd( 'sub_task', 'edit' );
				break;
			case 'root_mvc':
				$mName = 'Pages_mvc_item';
				$vLayout = 'edit';
				$vName = 'pages_mvc';
				break;
		}
		$document = &JFactory::getDocument();

		$vType = $document->getType();

		//$this->addViewPath(JPATH_PLUGINS.DS.'pages_and_items'.DS.'fieldtypes'.DS.'pi_fish'.DS.'views');
		
		// Get/Create the view
		$view = &$this->getView( $vName, $vType);
		
		// Get/Create the model
		if ($model = &$this->getModel($mName))
		{
			// Push the model into the view (as default)
			$view->setModel($model, true);
		}

		if ($model = &$this->getModel('PagesAndItems'))
		{
			// Push the model into the view (as default)
			$view->setModel($model, false);
		}

		// Set the layout
		$view->setLayout($vLayout);
		//$view->assignRef('class_pi', $this->class_pi);
		// Display the view
		$view->display();

	}
	
	
	function page_move_save(){
	
		$database = JFactory::getDBO();	
		
		$pageId = JRequest::getVar('pageId',0);
		
		$old_parent_id = JRequest::getVar('old_parent_id', '');
		$new_parent_id = JRequest::getVar('new_parent_id', '');
		$new_menutype = JRequest::getVar('new_menutype', '', 'post');
		$old_menutype = JRequest::getVar('old_menutype', '', 'post');		
		
		//get the model from com_menus
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models'.DS.'item.php');
		$model = new MenusModelItem;
			
		$table = $model->getTable();
		
		$table->load($pageId);
		
		// Set the new location in the tree for the node.
		$table->setLocation($new_parent_id, 'last-child');
		
		// Set the new Parent Id
		$table->parent_id = $new_parent_id;
		
		// Check if we are moving to a different menu
		$children = array();
		if ($new_menutype != $table->menutype) {
			// Add the child node ids to the children array.
			$database->setQuery(
				'SELECT `id`' .
				' FROM `#__menu`' .
				' WHERE `lft` BETWEEN '.(int) $table->lft.' AND '.(int) $table->rgt
			);
			$children = array_merge($children, (array) $database->loadResultArray());
		}						
		
		$table->store();
		
		$table->rebuildPath();
		
		// Process the child rows
		if (!empty($children)) {
			// Remove any duplicates and sanitize ids.
			$children = array_unique($children);
			JArrayHelper::toInteger($children);

			// Update the menutype field in all nodes where necessary.
			$database->setQuery(
			'UPDATE `#__menu`' .
			' SET `menutype` = '.$database->quote($new_menutype).
			' WHERE `id` IN ('.implode(',', $children).')'
			);
			$database->query();			
		}
		
		$model->cleanCache();
		
		//redirect	
		$this->setRedirect("index.php?option=com_pagesanditems&view=page&sub_task=edit&pageId=$pageId&menutype=$new_menutype", JText::_('COM_PAGESANDITEMS_PAGEMOVESAVED'));
	}
	
	function page_trash(){
		$this->page_trash_delete('trash');
	}
	
	function page_delete(){
		$this->page_trash_delete('delete');
	}
	
	function page_trash_delete($trash_or_delete){
				
		$jform = JRequest::getVar('jform', array(), 'post', 'array');		
		$pageType = JRequest::getVar('pageType', '');	
		$menutype = JRequest::getVar('menutype', '');	
		
		//trash or delete this page and all underlying pages and all items on these pages
		if($trash_or_delete=='delete'){
			$this->helper->deletePage($jform['id']);
		}else{
			$this->helper->trashPage($jform['id']);
		}		
		
		if($jform['parent_id']=='1'){
			//go to root			
			$url = 'index.php?option=com_pagesanditems&view=page&layout=root&menutype='.$menutype;
		}else{
			$url = 'index.php?option=com_pagesanditems&view=page&sub_task=edit&pageId='.$jform['parent_id'].'&menutype='.$menutype.'&pageType='.$pageType;
		}
		
		$this->setRedirect($url, JText::_('COM_PAGESANDITEMS_PAGE_TRASHED'));
	}

	
	
	
	
	//called from small buttons on under 'Underlaying Pages'
	function pages_state(){	
		
		$helper = $this->helper;
			
		$pageId = JRequest::getVar('pageId', '', 'post');
		$menutype = JRequest::getVar('menutype', '', 'post');		
		$cid = JRequest::getVar('pageCid', array(), 'post', 'array');
		
		$layout = JRequest::getVar('layout', null, 'post');
		
		$sub_task = JRequest::getVar('sub_task');
		
		if($sub_task=='delete'){
			$message = JText::_('COM_PAGESANDITEMS_PAGES_DELETED');
			$new_state = 'delete';
		}elseif($sub_task=='trash'){
			$message = JText::_('COM_PAGESANDITEMS_PAGES_TRASHED');
			$new_state = '-2';		
		}elseif($sub_task=='archive'){
			$message = JText::_('COM_PAGESANDITEMS_PAGES_ARCHIVED');
			$new_state = '2';			
		}elseif($sub_task=='publish'){
			$message = JText::_('COM_PAGESANDITEMS_PAGES_PUBLISHED');
			$new_state = '1';
		}elseif($sub_task=='unpublish'){
			$message = JText::_('COM_PAGESANDITEMS_PAGES_UNPUBLISHED');
			$new_state = '0';
		}			
		
		if(count($cid)){
			//there are pages
			foreach($cid as $page_id){
				//delete pages
				$helper->page_state($page_id, $new_state);
				/*
				we can set another way for message
				make sure $app is defined
				like $app->enqueueMessage($message.' Item Id = '.$item_id);
				*/
			}
			//$message = '';
		}else{
			//no items			
			$message = JText::_('COM_PAGESANDITEMS_NO_PAGES_SELECTED');
		}		
		
		//ms: &pageType=content_category_blog is (wrong) not need here, we can have varius pageType here
		//$url = 'index.php?option=com_pagesanditems&view=page&sub_task=edit&menutype='.$menutype.'&pageType=content_category_blog&pageId='.$pageId;
		
		if($layout)
		{
			$url = 'index.php?option=com_pagesanditems&view=page&layout=root&menutype='.$menutype;
		}
		else
		{
			$url = 'index.php?option=com_pagesanditems&view=page&sub_task=edit&menutype='.$menutype.'&pageId='.$pageId;
		}
		//$helper->redirect_to_url($url, $message);
		$this->setRedirect(JRoute::_($url, false), $message);
		
	}
	

	function root_cancel()
	{
		$menutype = JRequest::getVar('menutype', '');
		$message = JText::_('COM_PAGESANDITEMS_PAGE_CANCEL'); //JText::_('COM_PAGESANDITEMS_PAGE_NOTHING_TO_DO')
		if($menutype != '')
		{
			$menutype = '&menutype='.$menutype;
		}
		$url = "index.php?option=com_pagesanditems&view=page&layout=root".$menutype; //&menutype=".$menutype;
		$app = JFactory::getApplication();
		$app->redirect($url, $message);
		//$this->redirect_to_url($url, $message);
	}

	function root_save()
	{
		//get vars
		/*
		$menutype = JRequest::getVar('menutype', '');
		$task = JRequest::getVar('task', null, 'page_save', 'cmd');
		$sub_task = JRequest::getVar('sub_task', null, 'edit', 'cmd');
		if($task == 'root_save' && $sub_task && $subsub_task=='save')
		{
		
		}
		
		$pages_total = JRequest::getVar('pages_total',0);
		$pages_are_reordered = JRequest::getVar('pages_are_reordered',0);
		$message = JText::_('COM_PAGESANDITEMS_PAGE_SAVED'); //JText::_('COM_PAGESANDITEMS_PAGE_NOTHING_TO_DO')
		//if pages where reordered update the ordering of these pages
		if($pages_are_reordered==1)
		{
			for ($n = 1; $n <= $pages_total; $n++)
			{
				$temp_id = intval(JRequest::getVar('reorder_page_id_'.$n, '', 'post'));
				$this->db->setQuery( "UPDATE #__menu SET ordering='$n' WHERE id='$temp_id'");
				if (!$this->db->query()) 
				{
					echo "<script> alert('".$this->db->getErrorMsg()."'); window.history.go(-1); </script>";
					exit();
				}
				$message = JText::_('COM_PAGESANDITEMS_PAGEORDER_SAVED');
			}
			
		}
		if($menutype != '')
		{
			$menutype = '&menutype='.$menutype;
		}
		$url = "index.php?option=com_pagesanditems&view=page&layout=root&menutype=".$menutype;
		$this->redirect_to_url($url, $message);
		*/
	}

	//TODO remove?
	function menuItemSave()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$model	=& $this->getModel( 'Item' );
		$post	= JRequest::get('post');
		// allow name only to contain html
		$post['name'] = JRequest::getVar( 'name', '', 'post', 'string', JREQUEST_ALLOWHTML );
		$model->setState( 'request', $post );

		if ($model->store()) 
		{
			$msg = JText::_( 'Menu item Saved' );
		} else {
			$msg = JText::_( 'Error Saving Menu item' );
		}

		$item =& $model->getItem();
		
		switch ( $this->_task ) 
		{
			case 'apply':
				$this->setRedirect( 
					'index.php?option=com_menus&menutype=' . $item->menutype
					. '&task=edit&cid[]=' . $item->id,
					$msg
				);
				break;

			case 'save':
			default:
				$this->setRedirect(
					'index.php?option=com_menus&task=view&menutype=' . $item->menutype,
					$msg
				);
				break;
		}
	}

	/*
	this is the function from com_menus/controllers/item.php
	*/
	public function save($key = null, $urlVar = null)
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		

		// Initialise variables.
		$app		= JFactory::getApplication();
		$model	= $this->getModel( 'Item' ,'MenusModel');
		//$model	= $this->getModel('Item', '', array());
		$data		= JRequest::getVar('jform', array(), 'post', 'array');
		
		//$task		= $this->getTask();
		
		
		$context	= 'com_menus.edit.item';
		$recordId	= JRequest::getInt('id');
		
		//PI ACL		
		if(!$recordId){
			//new page		
			$this->helper->die_when_no_permission('1');
		}else{
			//edit page
			$this->helper->die_when_no_permission('2');
		}
		
		// set the form path
		JForm::addFormPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models'.DS.'forms');

		// set the fields path
		JForm::addFieldPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models'.DS.'fields');
		
		
		if (!$this->checkEditId($context, $recordId)) 
		{
			// Somehow the person just went to the form and saved it - we don't allow that.
			/*
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $recordId));
			$this->setMessage($this->getError(), 'error');
			//$this->setRedirect(JRoute::_('index.php?option=com_menus&view=items'.$this->getRedirectToListAppend(), false));
			dump(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $recordId));
			return false;
			*/
		}
		
		// Populate the row id from the session.
		$data['id'] = $recordId;
		
		$task = JRequest::getVar('task', '');

		// The save2copy task needs to be handled slightly differently.
		if ($task == 'save2copy') {
			// Check-in the original row.
			if ($model->checkin($data['id']) === false) 
			{
				// Check-in failed, go back to the item and display a notice.
				//$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()), 'warning');
				return false;
			}

			// Reset the ID and then treat the request as for Apply.
			$data['id']	= 0;
			$task		= 'apply';
		}

		// Validate the posted data.
		// This post is made up of two forms, one for the item and one for params.
		$form = $model->getForm($data);
		if (!$form)
		{
			JError::raiseError(500, $model->getError());

			return false;
		}

		/*
			check here for pagetype  == 'content_article'
			so we not must have an id here
		*/
		$pageType = JRequest::getVar('pageType', '');
		//dump($pageType);
		//dump(JRequest::get());
		if($pageType == 'content_article')
		{
			$form->setFieldAttribute('id', 'required', false,'request');
		}
		
		/*
			check here for pagetype  == 'content_category_blog'
			so we not must have an id here
		*/
		$pageType = JRequest::getVar('pageType', '');
		if($pageType == 'content_category_blog')
		{
			$form->setFieldAttribute('id', 'required', false,'request');
		}
		
		
		$data = $model->validate($form, $data);

		// Check for the special 'request' entry.
		if ($data['type'] == 'component' && isset($data['request']) && is_array($data['request']) && !empty($data['request'])) 
		{
			// Parse the submitted link arguments.
			$args = array();
			parse_str(parse_url($data['link'], PHP_URL_QUERY), $args);

			// Merge in the user supplied request arguments.
			$args = array_merge($args, $data['request']);
			$data['link'] = 'index.php?'.urldecode(http_build_query($args,'','&'));
			unset($data['request']);
		}

		// Check for validation errors.
		if ($data === false) {
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
			$app->setUserState('com_menus.edit.item.data', $data);

			// Redirect back to the edit screen.
			//$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_item.$this->getRedirectToItemAppend($recordId), false));

			return false;
		}

		// Attempt to save the data.
		if (!$model->save($data)) 
		{
			// Save the data in the session.
			$app->setUserState('com_menus.edit.item.data', $data);

			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
			//$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_item.$this->getRedirectToItemAppend($recordId), false));

			return false;
		}

		// Save succeeded, check-in the row.
		if ($model->checkin($data['id']) === false) {
			// Check-in failed, go back to the row and display a notice.
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()), 'warning');
			//$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_item.$this->getRedirectToItemAppend($recordId), false));

			return false;
		}

		$this->setMessage(JText::_('COM_MENUS_SAVE_SUCCESS'));
		
		$recordId = $model->getState($context.'.id');
		$item = $model->getItem();
		//$this->holdEditId($context, $recordId);
		//$app->setUserState('com_menus.edit.item.data',	null);
		//$app->setUserState('com_menus.edit.item.type',	null);
		//$app->setUserState('com_menus.edit.item.link',	null);
		
		
		return $item; //->id;
		
		$this->holdEditId($context, $recordId);
		$app->setUserState('com_menus.edit.item.data',	null);
		$app->setUserState('com_menus.edit.item.type',	null);
		$app->setUserState('com_menus.edit.item.link',	null);
		
		return $recordId;
		
		// Redirect the user and adjust session state based on the chosen task.
	
		switch ($task) {
			case 'apply':
				// Set the row data in the session.
				$recordId = $model->getState($this->context.'.id');
				$this->holdEditId($context, $recordId);
				$app->setUserState('com_menus.edit.item.data',	null);
				$app->setUserState('com_menus.edit.item.type',	null);
				$app->setUserState('com_menus.edit.item.link',	null);

				// Redirect back to the edit screen.
				//$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_item.$this->getRedirectToItemAppend($recordId), false));
				break;

			case 'save2new':
				// Clear the row id and data in the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState('com_menus.edit.item.data',	null);
				$app->setUserState('com_menus.edit.item.type',	null);
				$app->setUserState('com_menus.edit.item.link',	null);
				$app->setUserState('com_menus.edit.item.menutype',	$model->getState('item.menutype'));

				// Redirect back to the edit screen.
				//$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_item.$this->getRedirectToItemAppend(), false));
				break;

			default:
				// Clear the row id and data in the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState('com_menus.edit.item.data',	null);
				$app->setUserState('com_menus.edit.item.type',	null);
				$app->setUserState('com_menus.edit.item.link',	null);

				// Redirect to the list screen.
				//$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.$this->getRedirectToListAppend(), false));
				break;
		}
		return true;

	}



	function page_save()
	{
		
		//echo 'XXXXXXXXXXXXXXXXXXX';
		//also root_save_new
		//get vars
		//$pageId = JRequest::getVar('pageId', '', 'post');
		//$menutype = JRequest::getVar('menutype', '', 'post');
		//$page_parent = JRequest::getVar('pageParent'); //??
		
		$database = JFactory::getDBO();
		
		$sub_task = JRequest::getVar('sub_task', null, 'edit', 'cmd');
		$subsub_task = JRequest::getVar('subsub_task', null, 'edit', 'cmd');
		
		$menutype = JRequest::getVar('menutype', '');
		$task = JRequest::getVar('task', null, 'page_save', 'cmd');
				
		//declare message
		$message = '';
		
		//if pages where reordered update the ordering of these pages
		$pages_are_reordered = JRequest::getVar('pages_are_reordered',0);
		if($pages_are_reordered==1)
		{
			$pages_total = JRequest::getVar('pages_total',0);
			for ($n = 1; $n <= $pages_total; $n++)
			{
				$temp_id = intval(JRequest::getVar('reorder_page_id_'.$n, '', 'post'));
				if($this->joomlaVersion >= '1.6'){
					$lft = intval(JRequest::getVar('reorder_lft_'.$n, '', 'post'));
					$rgt = intval(JRequest::getVar('reorder_rgt_'.$n, '', 'post'));					
					$database->setQuery( "UPDATE #__menu SET lft='$lft', rgt='$rgt' WHERE id='$temp_id'");
				}else{
					$database->setQuery( "UPDATE #__menu SET ordering='$n' WHERE id='$temp_id'");
				}
				$database->query();				
				$message = JText::_('COM_PAGESANDITEMS_PAGEORDER_SAVED');
			}
		}		
		
		//just saving the reordering in the root, so redirect
		if($task == 'root_save' && $sub_task != 'new')
		{		
			if($menutype != '')
			{
				$menutype = '&menutype='.$menutype;
			}	
			$url = 'index.php?option=com_pagesanditems&view=page&layout=root'.$menutype;
			//$this->redirect_to_url($url, $message);
			
			$app = &JFactory::getApplication();
			$app->redirect($url, $message);
		}
		
		//if items where reordered update the ordering of these items
		$items_are_reordered = JRequest::getVar('items_are_reordered','');
		//dump(JRequest::get());
		if($items_are_reordered==1){
			$items_total = JRequest::getVar('items_total', '');
			for ($n = 1; $n <= $items_total; $n++){
				$temp_id = intval(JRequest::getVar('reorder_item_id_'.$n, '', 'post'));
				//dump($temp_id);
				$database->setQuery( "UPDATE #__content SET ordering='$n' WHERE id='$temp_id'");
				$database->query();
			}
		}
		
		$post	= JRequest::get('post');
		//TODO 
		
		//firstly:

		
		/*
		we look first if we have look if we have an pagetype controller (controller/model/helper?) and an function onBeforeSave
		so we can do things before MenusModelItem doing
		eg. if pageTypeClass = CategoryBlog set in the request another category id if we make an own select in the view=page subtemplate pagepropertys
		so we can make also for other pagetypes
		
		we must do before the model->getItem()
		if we want change everything in the menuitem
		*/
		/*
		TODO remove with trigger
		$class_pagetype = null;
		$pageTypeClass = JRequest::getVar('pageTypeClass', '');
		if(file_exists($this->controller->pathPagetypes.DS.$pageTypeClass.'.php'))
		{
			require_once($this->controller->pathPagetypes.DS.$pageTypeClass.'.php');
			$class_name = 'PagesAndItemsControllerPagetype'.$pageTypeClass;
			$class_pagetype = new $class_name();
			//eg.
			if(method_exists($class_pagetype,'onBeforSave'))
			{
				//$**** = $class_pagetype->onBeforSave(*****);
			}
			
			//and add above onAfterSave
		}
		*/
		
		$pageType = JRequest::getVar('pageType', '');
		//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'extensions'.DS.'helper.php');
		//$pagetypes = ExtensionHelper::importExtension('pagetype',null, $pageType,true,null,true);
		$path = realpath(dirname(__FILE__).DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'pagetypehelper.php');
		$extensions = ExtensionPagetypeHelper::importExtension(null, $pageType,true,null,true);
		
		
		$dispatcher = &JDispatcher::getInstance();
		//TODO what we will send to the extension?
		$data = JRequest::getVar('jform', null, 'post', 'array');
		$isnew = 1;
		if(isset($data['id'])){
			$isnew = 0;
		}
		$dispatcher->trigger('onBeforSave',array($data, $isnew));
		
		$extension = 'com_menus';
		$lang = &JFactory::getLanguage();
		$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false);
		//let do the com_menus/model/item save
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		
		$message = array();
		if($joomlaVersion < '1.6')
		{
			require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'helpers'.DS.'helper.php' );
		}
		else
		{
			require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'helpers'.DS.'menus.php' );
		}
		//require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models'.DS.'item.php' );
		// Check for request forgeries
		//JRequest::checkToken() or jexit( 'Invalid Token' );
		$this->addModelPath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models' );
		
		//com_menus/models/list.php = setHome( $item )
		
		//& $this->getModel( 'Item' );//
		//$model	= new MenusModelItem(); //& $this->getModel( 'Item' ,'MenusModel');
		$model	= & $this->getModel( 'Item' ,'MenusModel');
		
		// allow name only to contain html
		$post['name'] = JRequest::getVar( 'name', '', 'post', 'string', JREQUEST_ALLOWHTML );
		$save = true;
		if($joomlaVersion < '1.6')
		{
			$model->setState( 'request', $post );
			if ($model->store()) 
			{
				$message[] = JText::_( 'Menu item Saved' );
			}
			else
			{
				$message[] = JText::_( 'Error Saving Menu item' );
				$save = false;
			}
		}
		else
		{
			if(!$item = $this->save())
			{
				$save = false;
			}
			//dump($save);
			//dump($post);
			//dump(JRequest::get());
			/*
			//need more complexe ?
			if ($model->save($post)) 
			{
				$message[] = JText::_( 'Menu item Saved' );
			}
			else
			{
				$message[] = JText::_( 'Error Saving Menu item' );
			}
			*/
		}
		if($joomlaVersion < '1.6')
		{
			//if($save)
			//{
				$item = & $model->getItem();
			//}
		}
		else
		{
			/*
			if(isset($post['id']))
			{
				//$item = & $model->getItem($post['id']);
			}
			*/
			//$model->getState('item.id', 0);
			/*
			if($save)
			{
				$item = $model->getItem($save);
			}
			*/
			//$item = $model->getItem();
		}
		if(!$save)
		{
			//dump($item);
			$url = 'index.php?option=com_pagesanditems&view=page&layout=root';
			$app = JFactory::getApplication();
			$app->redirect($url);
		}
		else
		{
			//dump($item);
			//dump($task);
			//dump($sub_task);
			//$url = 'index.php?option=com_pagesanditems&view=page&layout=root';
			//$app = JFactory::getApplication();
			//$app->redirect($url);
		}
		//dump($item);
		//if($item->home)
		//{
			/*
			if($joomlaVersion < '1.6')
			{
				$model = & $this->getModel( 'List' ,'MenusModel');
				if($model->setHome( $item->id ))
				{
					$message[] = JText::_( 'Menu item Saved' ).': '.JText::_( 'DEFAULT MENU ITEM SET');
				}
				else
				{
					$message[] = JText::_( 'Error Saving Menu item' ).': '.JText::_( 'DEFAULT MENU ITEM SET');
				}
			}
			else
			{
				if($model->setHome( array($item->id) ))
				{
					$message[] = JText::_( 'Menu item Saved' ).': '.JText::_( 'DEFAULT MENU ITEM SET');
				}
				else
				{
					$message[] = JText::_( 'Error Saving Menu item' ).': '.JText::_( 'DEFAULT MENU ITEM SET');
				}
			}
			*/

		//}
		
		//TODO what we will send to the extension?
		//answer to Micha: the data and if the page is new		
		if($save)
		{
			//echo $item->id;						
			$dispatcher->trigger('onAfterSave',array($item->id, $data, $isnew));
		}
		/*
		if($class_pagetype && method_exists($class_pagetype,'onAfterSave'))
		{
			//$class_pagetyp->onAfterSave();
		}
		*/
		/*
		switch ( $this->_task ) {
			case 'apply':
				$this->setRedirect( 
					'index.php?option=com_menus&menutype=' . $item->menutype
					. '&task=edit&cid[]=' . $item->id,
					$msg
				);
				break;

			case 'save':
			default:
				$this->setRedirect(
					'index.php?option=com_menus&task=view&menutype=' . $item->menutype,
					$msg
				);
				break;
		}
		
		secondly:
		
		$menu_item_urlparams = $model->getUrlParams();

		//$menu_item_option = $menu_item_urlparams->get('option',null);
		$menu_item_view = $menu_item_urlparams->get('view',null);
		$menu_item_layout = $menu_item_urlparams->get('layout',null);
		$menu_item_id = $menu_item_urlparams->get('id',null);
		
		
		//TODO check this for the other eg category-list section-list ....
		//make alias unique
		if($this->controller->pi_config['make_page_alias_unique'])
		{
			//$item =& $model->getItem();
			if($menu_item_view == ''category' && $menu_item_layout== 'blog' && $menu_item_id)
			{
				$alias = $this->controller->make_alias_unique($item->alias, 'categories', $menu_item_id);
			}
		}
		
		//that becomes in the form hide the radios
		//if user has no rights to publish set to not published
		
		
		//in old we have the category and menuitem so what will we do?
		//if category gets unpublished, menuitem should also unpublish
		if($categoryPublished==0){
			$show_in_menu=0;
		}
		$section_id = JRequest::getVar('section');
		$section_id_old = JRequest::getVar('section_id_current');


		thirdly:
		see also firstly:


		fourthly:
		//clean cache
		$this->clean_cache_menu(); //need??? the model make this to
		//clean item-index
		$this->keep_item_index_clean();
		
		final:

		redirect
		*/
		//echo 'XXXXXXXXXXXXXXXX';
		//dump($item);
		//if(isset($item))
		//{
		if($sub_task=='edit')
		{
			if($task=='page_apply')
			{
				$url = 'index.php?option=com_pagesanditems&view=page&sub_task=edit&pageId='.$item->id.'&menutype='.$item->menutype;
			}
			else
			{
				if($joomlaVersion < '1.6')
				{
					$itemParentId = $item->parent;
					$itemParent = false;
					if($item->parent == 0 )
					{
						$itemParent = true;
					}
				}
				else
				{
					//dump($item);
					$itemParentId = $item->parent_id;
					$itemParent = false;
					if($item->parent_id == 1 )
					{
						$itemParent = true;
					}
				}
				//normal save so go to parent page
				if($itemParent)
				{
					$url = 'index.php?option=com_pagesanditems&view=page&layout=root&menutype='.$item->menutype;
				}
				else
				{
					$url = 'index.php?option=com_pagesanditems&view=page&sub_task=edit&pageId='.$itemParentId.'&menutype='.$item->menutype;//.'&test';
				}
			}
		}
		else
		{
			//new page
			//dump($item);
			if($task=='page_apply' || $task == 'root_apply')
			{
				$url = 'index.php?option=com_pagesanditems&view=page&sub_task=edit&pageId='.$item->id.'&menutype='.$item->menutype;
			}
			else
			{
				
				if($joomlaVersion < '1.6')
				{
					$itemParentId = $item->parent;
					$itemParent = false;
					if($item->parent == 0 )
					{
						$itemParent = true;
					}
				}
				else
				{
					$itemParentId = $item->parent_id;
					$itemParent = false;
					if($item->parent_id == 1 )
					{
						$itemParent = true;
					}
				}
				//normal save so go to parent page
				if($itemParent)
				{
						$url = 'index.php?option=com_pagesanditems&view=page&layout=root&menutype='.$item->menutype;
				}
				else
				{
					$url = 'index.php?option=com_pagesanditems&view=page&sub_task=edit&pageId='.$itemParentId.'&menutype='.$item->menutype;
				}
			}
		}
		//$url = "index.php?option=com_pagesanditems&view=page&layout=root&menutype=".$menutype;
		$message = (count($message) ? ''.implode(', ', $message) : '');
		$app = JFactory::getApplication();
		$app->redirect($url, $message);
		//}
		//$this->redirect_to_url($url, $message);
		
		
		
/*


root and root_save new
# [string] option = "com_pagesanditems"
# [string] view = "page"
# [string] layout = "root"
# [string] sub_task = "new"
# [string] toogle_mvc = "mvc"
# [string] type = "component"
# [array] url
	 * [string] option = "com_acesef"
# [string] menutype = "customcontent"
# [array] cid
    * [string] 0 = ""
# [string] name = ""
# [string] alias = ""
# [string] link = "index.php?option=com_acesef"
# [string] parent = "0"
# [string] published = "1"
# [string] ordering = ""
# [string] access = "0"
# [string] browserNav = "0"
# [array] params
    * [string] page_title = ""
    * [string] show_page_title = "1"
    * [string] pageclass_sfx = ""
    * [string] menu_image = "-1"
    * [string] secure = "0"
# [string] id = ""
# [string] componentid = "324"
# [string] task = "root_save"
# [string] subsub_task = "save"



root and root_apply new
# [string] option = "com_pagesanditems"
# [string] view = "page"
# [string] layout = "root"
# [string] sub_task = "new"
# [string] toogle_mvc = "mvc"
# [string] type = "component"
# [array] url
    * [string] option = "com_acesef"
# [string] menutype = "customcontent"
# [array] cid
    * [string] 0 = ""
# [string] name = ""
# [string] alias = ""
# [string] link = "index.php?option=com_acesef"
# [string] parent = "0"
# [string] published = "1"
# [string] ordering = ""
# [string] access = "0"
# [string] browserNav = "0"
# [array] params
    * [string] page_title = ""
    * [string] show_page_title = "1"
    * [string] pageclass_sfx = ""
    * [string] menu_image = "-1"
    * [string] secure = "0"
# [string] id = ""
# [string] componentid = "324"
# [string] task = "root_apply"
# [string] subsub_task = "apply"
*/		
		
		
		
		

		
		//$pageComponentId = JRequest::getVar('pageComponentId');
		//$titleShort = JRequest::getVar('titleShort');
				
		//$link = JRequest::getVar('link');
		//$content_page = JRequest::getVar('content_page');
		//$show_in_menu = JRequest::getVar('show_in_menu');
		//$categoryPublished = JRequest::getVar('categoryPublished');
		//$subsub_task = JRequest::getVar('subsub_task', '', 'post');
		//$page_parent = JRequest::getVar('pageParent');
		//$cat_id = intval(JRequest::getVar('cat_id'));
		
		//pages_mvc.page_save
		//insert code from class.php and rewrite
		//save menu_item over com_menus/modells/item.php
		// see the com_menus controller
/*
		if(JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.'pagetypes'.DS.$pagetype.'.php')
		{
			include(JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.'pagetypes'.DS.$pagetype.'.php');
			$class_name = 'classPagetypeController'.$pagetype;
			$class_object = new $class_name();
			$class_object->save_page($menuId, $sub_task);
		}
		
		
		orginal code from class.php
		//get vars
		$pageId = JRequest::getVar('pageId', '', 'post');
		$menutype = JRequest::getVar('menutype', '', 'post');
		$pageComponentId = JRequest::getVar('pageComponentId');
		$titleShort = JRequest::getVar('titleShort');
				
		$link = JRequest::getVar('link');
		$content_page = JRequest::getVar('content_page');
		$show_in_menu = JRequest::getVar('show_in_menu');
		$categoryPublished = JRequest::getVar('categoryPublished');
		$subsub_task = JRequest::getVar('subsub_task', '', 'post');
		
		$cat_id = intval(JRequest::getVar('cat_id'));
		if(defined('_JEXEC')){
			//joomla 1.5
			$titleShort = addslashes($titleShort);
		}
		$alias = JRequest::getVar('alias', '');
		if($alias==''){
			$alias = $this->make_alias($titleShort);
		}
		
		//make alias unique
		if($this->pi_config['make_page_alias_unique']){
			$alias = $this->make_alias_unique($alias, 'categories', $cat_id);
		}
		
		
		//if category gets unpublished, menuitem should also unpublish
		if($categoryPublished==0){
			$show_in_menu=0;
		}
		
		//if not content category blog type	
		if(!$content_page){
			$show_in_menu = JRequest::getVar('show_in_menu');
		}
		
		$browserNav = JRequest::getVar('browserNav');
		$access = JRequest::getVar('access');
		$access_category = JRequest::getVar('access_category');
		$params = JRequest::getVar('params');
		
		$pages_are_reordered = JRequest::getVar('pages_are_reordered','');
		$pages_total = JRequest::getVar('pages_total');
		$items_are_reordered = JRequest::getVar('items_are_reordered','');
		$items_total = JRequest::getVar('items_total', '');
		
		$sub_task = JRequest::getVar('sub_task');
		$pageType = JRequest::getVar('pageType');
		$section_id = JRequest::getVar('section');
		$section_id_old = JRequest::getVar('section_id_current');
		if( defined('_JEXEC') ){
			//joomla 1.5
			$default_home = intval(JRequest::getVar('default_home'));
			$default_home_current = intval(JRequest::getVar('default_home_current'));
			if($default_home==1 && $default_home_current==0){
				//this page becomes new homepage so un-homepage the current homepage
				foreach($this->menuitems as $row){
					if($row->home==1){
						$current_home = $row->id;
						break;
					}
				}
				$this->db->setQuery( "UPDATE #__menu SET home='0' WHERE id='$current_home'"	);
				$this->db->query();
			}
		}
		
		//make a nice string from parameters
		$paramsString = '';
		for($n = 0; $n < count($params); $n++){
			$row = each($params);
			$value = $row['value'];
			if(defined('_JEXEC')){
				//joomla 1.5
				$value = addslashes($value);
			}
			$paramsString .="\n$row[key]=$value";
		}
		
		if($sub_task=='new'){
			if($content_page){
				//is content category blog page
					
				$this->db->setQuery("SELECT count FROM #__sections WHERE id='$section_id' LIMIT 1"  );
				$rows = $this->db->loadObjectList();
				$row = $rows[0];
				$numberOfCategories = $row->count;
		
				//new number of categories
				$numberOfCategories = $numberOfCategories+1;
			
				//insert new category
				if( defined('_JEXEC') ){
					//if joomla version 1.5 write page_title to 'alias' column instead of 'name' column in joomla 1.0.x (why?!)
					$this->db->setQuery( "INSERT INTO #__categories SET alias='$alias', title='$titleShort', section='$section_id', image_position='left', published='$categoryPublished', access='$access'");
				}else{
					//category name and title have addslashes in Joomla 1.0.x for some wierd reason. probably double escaped somewhere
					$cat_name = addslashes($titleShort);
					$this->db->setQuery( "INSERT INTO #__categories SET name='$cat_name', title='$cat_name', section='$section_id', image_position='left', published='$categoryPublished', access='$access'");
				}
				if (!$this->db->query()) {
					echo "<script> alert('".$this->db->getErrorMsg()."'); window.history.go(-1); </script>";
					exit();
				}
				$categoryId = $this->db->insertid();
			
				//update section
				//$this->db->setQuery( "UPDATE #__sections SET count='$numberOfCategories' WHERE id='$section_id'"	);
				//if (!$this->db->query()) {
				//	echo "<script> alert('".$this->db->getErrorMsg()."'); window.history.go(-1); </script>";
				//	exit();
				//}
				
				//compose menu-link
				//for joomla 1.5 a new link is used
				if( defined('_JEXEC') ){
					$catbloglink = 'index.php?option=com_content&view=category&layout=blog&id=';
				}else{
					$catbloglink = 'index.php?option=com_content&task=blogcategory&id=';
				}
				$pageLink = $catbloglink.$categoryId;
				
				//add categoryId to params-string
				$paramsString = $paramsString."\ncategoryid=".$categoryId;
			
			}else{//end if content_page
			
				//compose menu-link
				$pageLink = $link;
				$paramsString = JRequest::getVar('parameters');
			}
		
			//reorder mainmenu to make sure new menu-item is last
			//$this->db->setQuery("SELECT id, ordering FROM #__menu WHERE menutype='mainmenu' AND parent='$pageId' ORDER BY ordering ASC");
			$this->db->setQuery("SELECT id, ordering FROM #__menu WHERE parent='$pageId' ORDER BY ordering ASC");
			$rows = $this->db->loadObjectList();
			$counter = 1;
			foreach($rows as $row){
				$rowId = $row->id;
				$order = $row->ordering;
				
				$this->db->setQuery( "UPDATE #__menu SET ordering='$counter' WHERE id='$rowId'"	);
				if (!$this->db->query()) {
					echo "<script> alert('".$this->db->getErrorMsg()."'); window.history.go(-1); </script>";
					exit();
				}
				
				$counter = $counter+1;
			}
			
			//get the parent-menuitem's sublevel. Only for 1.5.x, in 1.0.x this never seemed to have worked at all.			
			$sublevel = $this->get_sublevel_add_one($pageId);
			
			//insert new menuitem in mainmenu
			//for joomla 1.5 component_id for category blog content is 20 and name goes into alias and access goes into 'access', default_home to home.
			if( defined('_JEXEC') ){
				$this->db->setQuery( "INSERT INTO #__menu SET menutype='$menutype', name='$titleShort', alias='$alias', link='$pageLink', browserNav='$browserNav', access='$access_category', type='$pageType', published='$show_in_menu', parent='$pageId', sublevel='$sublevel', componentid='20', ordering='$counter', params='$paramsString', home='$default_home'");
			}else{
				//joomla 1.0.x
				$this->db->setQuery( "INSERT INTO #__menu SET menutype='$menutype', name='$titleShort', link='$pageLink', browserNav='$browserNav', utaccess='$access_category', type='$pageType', published='$show_in_menu', parent='$pageId', componentid='$categoryId', ordering='$counter', params='$paramsString'");
			}
			if (!$this->db->query()) {
				echo "<script> alert('".$this->db->getErrorMsg()."'); window.history.go(-1); </script>";
				exit();
			}
			$menuId = $this->db->insertid();
			
			//if using the user-access component for Pi give this author's usergroup rights for this page
			if($this->pi_config['use_user_access_component']){
				if($this->user_access_config['active_pagesaccess'] && $this->user_type!="Super Administrator"){
					$this->class_ua->set_page_access_group($menuId, $this->usergroup);
				}
				//if using the inherit rights option, let all usergroups inherit rights
				if($this->user_access_config['inherit_rights_parent_page']){
					$this->class_ua->set_page_access_all_groups($menuId, $page_parent);
				}
			}
			
			
		
		// end if new page
		}else{
		//if page update
			
			//for all menulink types other then category blog
			if(!$content_page){
				$paramsString = JRequest::getVar('parameters');
			}
						
			//update menuitem
			//joomla 1.5 also writes name to alias column, acces to 'access', default_home to home
			if( defined('_JEXEC') ){
				$this->db->setQuery( "UPDATE #__menu SET name='$titleShort', alias='$alias', link='$link', browserNav='$browserNav', access='$access', published='$show_in_menu', params='$paramsString', home='$default_home' WHERE id='$pageId'"	);
			}else{
				$this->db->setQuery( "UPDATE #__menu SET name='$titleShort', link='$link', browserNav='$browserNav', utaccess='$access', published='$show_in_menu', params='$paramsString' WHERE id='$pageId'"	);
			}
			if (!$this->db->query()) {
				echo "<script> alert('".$this->db->getErrorMsg()."'); window.history.go(-1); </script>";
				exit();
			}
			
			//update category
			//joomla 1.5 write to alias column and not to name, access to 'access'
			if( defined('_JEXEC') ){
				//joomla 1.5
				$this->db->setQuery( "UPDATE #__categories SET title='$titleShort', alias='$alias', published='$categoryPublished', access='$access_category', section='$section_id' WHERE id='$cat_id'");
			}else{
				//joomla 1.0.x
				$this->db->setQuery( "UPDATE #__categories SET name='$titleShort', title='$titleShort', published='$categoryPublished', access='$access_category', section='$section_id' WHERE id='$cat_id'");
			}
			if (!$this->db->query()) {
				echo "<script> alert('".$this->db->getErrorMsg()."'); window.history.go(-1); </script>";
				exit();
			} 
				
			//if pages where reordered update the ordering of these pages
			if($pages_are_reordered==1){
				for ($n = 1; $n <= $pages_total; $n++){
					$temp_id = intval(JRequest::getVar('reorder_page_id_'.$n, '', 'post'));
					$this->db->setQuery( "UPDATE #__menu SET ordering='$n' WHERE id='$temp_id'"	);
					if (!$this->db->query()) {
						echo "<script> alert('".$this->db->getErrorMsg()."'); window.history.go(-1); </script>";
						exit();
					}
				}
			}
							
			//if items where reordered update the ordering of these items
			if($items_are_reordered==1){
				for ($n = 1; $n <= $items_total; $n++){
					$temp_id = intval(JRequest::getVar('reorder_item_id_'.$n, '', 'post'));
						//exit('temp='.$temp_id.'n='.$n);
					$this->db->setQuery( "UPDATE #__content SET ordering='$n' WHERE id='$temp_id'"	);
					if (!$this->db->query()) {
						echo "<script> alert('".$this->db->getErrorMsg()."'); window.history.go(-1); </script>";
						exit();
					} 								
				}
			}
		
			//send edit-page-notification if configed to do so
			if(!$this->checkUserActionRight(9)  && $this->user_type!='Super Administrator'){
				$subject = JText::_('COM_PAGESANDITEMS_NOTIFY_PAGE_EDIT_1');
				$message = JText::_('COM_PAGESANDITEMS_NOTIFY_HELLO').",\n\n";
				$message .= JText::_('COM_PAGESANDITEMS_NOTIFY_PAGE_EDIT_2')."\n";
				$message .= $this->live_site."\n\n";
				$message .= JText::_('COM_PAGESANDITEMS_NOTIFY_NEW_ITEM_BY').":\n";
				$message .= $this->get_username()."\n\n";
				$message .= JText::_('COM_PAGESANDITEMS_NOTIFY_PAGE_1')."\n";
				if(defined('_JEXEC')){
					//joomla 1.5
					$url = 'index.php?option=com_content&view=category&layout=blog&id='.$cat_id.'&Itemid='.$pageId;
				}else{
					//joomla 1.0.x
					$url = 'index.php?option=com_content&task=blogcategory&id='.$cat_id.'&Itemid='.$pageId;
				}
				//$message .= "$this->live_site/index.php?option=com_content&task=blogcategory&id=$pageComponentId&Itemid=$pageId\n\n";
				$message .= JText::_('COM_PAGESANDITEMS_NOTIFY_PAGE_3').":\n";
				$message .= $this->live_site.'index.php?option=com_pagesanditems&task=page&sub_task=edit&pageId='.$pageId."\n\n";
				$message .= JText::_('COM_PAGESANDITEMS_NOTIFY_TITLE_PAGE')."\n";
				$message .= $titleShort."\n\n";
				$this->send_notification($subject, $message);
			}
			
			//update sectionid content and underlying pages only if section_id changed
			if($section_id_old!=$section_id){
				//update sectionid underlying pages
				if($this->pi_config['child_inherit_from_parent_change']){
					$this->section_update_children($pageId, $section_id);
				}
				//update sectionid of content-items in category
				$this->update_items_category($cat_id, $section_id);
			}
			
		}//end if page update
		
		//clean cache
		$this->clean_cache_menu();
		
		//clean item-index
		$this->keep_item_index_clean();

end 		orginal code from class.php

*/
/*
		if($sub_task=='edit')
		{
			if($task=='apply')
			{
				$url = "index.php?option=com_pagesanditems&view=pages&sub_task=edit&pageId=$pageId";
			}
			else
			{
				//normal save so go to parent page
				if($page_parent==0)
				{
					$url = "index.php?option=com_pagesanditems&view=page&layout==root&menutype=".$menutype;
				}
				else
				{
					$url = "index.php?option=com_pagesanditems&view=page&sub_task=edit&pageId=$page_parent";
				}
			}
		}
		else
		{
			//new page
			if($task=='apply')
			{
				if()
				{
				
				}
				$url = "index.php?option=com_pagesanditems&view=page&sub_task=edit&pageId=$menuId";
			}
			else
			{
				//normal save so go to parent page
				if($page_parent==0)
				{
						$url = "index.php?option=com_pagesanditems&view=page&layout=root&menutype=".$menutype;
				}
				else
				{
					$url = "index.php?option=com_pagesanditems&view=page&sub_task=edit&pageId=$page_parent";
				}
			}
		}
*/
		$url = "index.php?option=com_pagesanditems&view=page&layout=root&menutype=".$menutype;
		$this->redirect_to_url($url, $message);
	}
	
	/*
	function new_item(page_id)
	{
		itemtype = document.getElementById('select_itemtype').value;
		document.location.href='index.php?option=com_pagesanditems&type=content_blog_category&task=item&sub_task=new&pageId='+page_id+'&item_type='+itemtype;
	}

	*/
	function cancel()
	{

		$pageId = intval(JRequest::getVar('pageId', '0'));
		$menutype = JRequest::getVar('menutype', '');
		$view = JRequest::getVar('view', 'page');
		$layout = JRequest::getVar('layout', '');
		if($view)
		{
			$view = '&view='.$view;
		}
		if($layout != '')
		{
			$layout = '&layout='.$layout;
		}

		$app = JFactory::getApplication();
		if($pageId=='0')
		{
			$layout = '&layout=root';
			$app->redirect("index.php?option=com_pagesanditems".$view.$layout, JText::_('COM_PAGESANDITEMS_ACTION_CANCELED'));
		}
		else
		{
			//&pageId=".$pageId."&sub_task=edit&
			$app->redirect("index.php?option=com_pagesanditems".$view."&menutype=".$menutype."&pageId=".$pageId."&sub_task=edit", JText::_('COM_PAGESANDITEMS_ACTION_CANCELED'));
		}
	}
	
	function Xroot()
	{
		echo 'root';
		$doc =& JFactory::getDocument();
		
		JHTML::stylesheet('pagesanditems.css', 'administrator/components/com_pagesanditems/css/');
		
		JHTML::stylesheet('dtree.css', 'administrator/components/com_pagesanditems/css/');

		JHTML::script('dtree.js', 'administrator/components/com_pagesanditems/javascript/',false);
		//JHTML::script('overlib_mini.js', 'includes/js/',false);
		/*
		*/
		//$viewType	= $document->getType();
		//$viewName	= JRequest::getCmd( 'view', $this->getName() );
		//$viewLayout	= JRequest::getCmd( 'layout', 'default' );
		
		//echo 'root';
		
		// Get some data from the request
		/*
		$tmpl	= JRequest::getCmd( 'tmpl' );
		$paths	= JRequest::getVar( 'rm', array(), '', 'array' );
		$folder = JRequest::getVar( 'folder', '', '', 'path');
		*/
	}

}
