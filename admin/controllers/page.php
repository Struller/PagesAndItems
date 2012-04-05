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
jimport( 'joomla.application.component.controller' );
jimport( 'joomla.database.table');

/**
jimport( 'joomla.application.component.controllerform' );
jimport( 'joomla.form.form' );
*/
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controller.php');

/**
 *
 *
 * @package		PagesAndItems
 */
class PagesAndItemsControllerPage extends PagesAndItemsController //JControllerForm //
{
	public $helper;

	function __construct( $config = array())
	{
		parent::__construct($config);

		//$this->registerTask( 'display', 'display' );
		$this->registerTask( 'root', 'display' );
		$this->registerTask( 'page_apply', 'page_save' );
		$this->registerTask( 'page_reorder_apply', 'page_reorder_save' );
		$this->registerTask( 'reorder_apply', 'reorder_save' );
		
		
		$this->registerTask( 'root_menutype_apply', 'root_menutype_save' );
		//$this->registerTask( 'root_underlayingpage', 'root_save' );
		//$this->registerTask( 'root_save', 'page_save' );
		$this->registerTask( 'root_apply', 'page_save' );
		//$this->registerTask( 'edit', 'edit' );

		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'pagesanditems.php');
		$this->helper = new PagesAndItemsHelper();
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'tables');
	}

	function edit()
	{
		//echo 'display';
		//$this->assignRef('edit', true);
		JRequest::setVar('pages_edit',true);
		$this->display();
	}
	
	function page_new()
	{
		$app = JFactory::getApplication();
		$option = JRequest::getVar('option');
		$type = JRequest::getVar( 'type', '');
		$app->setUserState( $option.'.page.type', $type);
		$pageType = JRequest::getVar( 'pageType', '');
		$app->setUserState( $option.'.page.pageType', $pageType);
		$pageTypeType = JRequest::getVar( 'pageTypeType', '');
		$app->setUserState( $option.'.page.pageTypeType', $pageTypeType);
		$app->setUserState( $option.'.page.task', 'page.page_new');
		//$task = '&task=page.page_new';
		
		
		$menutype = JRequest::getVar('menutype', '' );
		$pageId = JRequest::getVar('pageId', 0 );
		$pageId = $pageId ? '&pageId='.$pageId : '';
		$layout = JRequest::getVar('layout', '' );
		$layout = $layout ? '&layout='.$layout : '';

		$url = 'index.php?option=com_pagesanditems&view=page'.$layout.'&sub_task=new&menutype='.$menutype.$pageId;
		$this->setRedirect(JRoute::_($url, false));
	}
	
	

	/*
		add here for releaseEditId and checkin in all views
		//where we must set the old?
		in all links avaible in view page? (not layout==root)
		and not link (or redirect) to the view page or change the pageId
		
			1. pagetree here we can change the pageId
			2. submenu here we go to other views
			3. ?
			
			but if we make an new Browser tab/window and link to another view?????
		
		*/
		/*
		//ms: com_menus.edit.item.id
		$app	= JFactory::getApplication();
		$oldPageId = $app->getUserState("com_pagesanditems.page.edit.item.id"); //.oldPageId");
		if($pageId)
		{
			$context = 'com_pagesanditems.page.edit.item'; //.oldPageId';
			$app->setUserState('com_pagesanditems.page.edit.item.id',$pageId);
			if($vName == 'page' && $vLayout == 'default')
			{
				$context = 'com_menus.edit.item';
				$this->holdEditId($context, $pageId);
				$context = 'com_pagesanditems.page.edit.item';
				$this->holdEditId($context, $pageId);
			}
		}
		if($oldPageId && $pageId != $oldPageId)
		{
			$context = 'com_menus.edit.item';
			$this->releaseEditId($context, $oldPageId);
			
			$model = $this->getModel( 'Item' ,'MenusModel');
			$model->checkin($oldPageId);
		}
		//ms: end com_menus.edit.item.id
		*/
	
	function page_checkin()
	{
		JRequest::setVar('task', 'page_apply'); //, 'cmd');
		JRequest::setVar('checkin', true);
		//JRequest::setVar('useCheckin', 1);
		$this->page_save();
		
		//$app = JFactory::getApplication();
		//$option = JRequest::getVar('option');
		//$type = JRequest::getVar( 'type', '');
		//$app->setUserState( $option.'.page.type', $type);
		
		//$app->setUserState( $option.'.page.pageType', $pageType);
		//$pageTypeType = JRequest::getVar( 'pageTypeType', '');
		//$app->setUserState( $option.'.page.pageTypeType', $pageTypeType);
		//$app->setUserState( $option.'.page.task', 'page.page_new');
		//$task = '&task=page.page_new';
		//TODO checkout
		/*
		$pageType = JRequest::getVar( 'pageType', '');
		$menutype = JRequest::getVar('menutype', '' );
		$menutype = $menutype ? '&menutype='.$menutype : '';
		$pageId = JRequest::getVar('pageId', 0 );
		$pageId = $pageId ? '&pageId='.$pageId : '';


		$sub_task = '&sub_task=edit';

		$url = 'index.php?option=com_pagesanditems&view=page'.$sub_task.$menutype.$pageId;
		$this->setRedirect(JRoute::_($url, false));
		*/
	}


	function page_edit()
	{
		//$app = JFactory::getApplication();
		//$option = JRequest::getVar('option');
		//$type = JRequest::getVar( 'type', '');
		//$app->setUserState( $option.'.page.type', $type);
		
		//$app->setUserState( $option.'.page.pageType', $pageType);
		//$pageTypeType = JRequest::getVar( 'pageTypeType', '');
		//$app->setUserState( $option.'.page.pageTypeType', $pageTypeType);
		//$app->setUserState( $option.'.page.task', 'page.page_new');
		//$task = '&task=page.page_new';
		//TODO checkout
		
		$pageType = JRequest::getVar( 'pageType', '');
		$menutype = JRequest::getVar('menutype', '' );
		$menutype = $menutype ? '&menutype='.$menutype : '';
		$pageId = JRequest::getVar('pageId', 0 );
		$pageId = $pageId ? '&pageId='.$pageId : '';


		$sub_task = '&sub_task=edit';

		$url = 'index.php?option=com_pagesanditems&view=page'.$sub_task.$menutype.$pageId;
		$this->setRedirect(JRoute::_($url, false));
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

		$db = JFactory::getDBO();

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
			$db->setQuery(
				'SELECT `id`' .
				' FROM `#__menu`' .
				' WHERE `lft` BETWEEN '.(int) $table->lft.' AND '.(int) $table->rgt
			);
			$children = array_merge($children, (array) $db->loadResultArray());
		}

		$table->store();

		$table->rebuildPath();

		// Process the child rows
		if (!empty($children)) {
			// Remove any duplicates and sanitize ids.
			$children = array_unique($children);
			JArrayHelper::toInteger($children);

			// Update the menutype field in all nodes where necessary.
			$db->setQuery(
			'UPDATE `#__menu`' .
			' SET `menutype` = '.$db->quote($new_menutype).
			' WHERE `id` IN ('.implode(',', $children).')'
			);
			$db->query();
		}

		//in 2.5 this function is protected, so can no longer call it like this
		//$model->cleanCache();
		$cache = & JFactory::getCache('com_modules');
		$cache->clean();
		$cache = & JFactory::getCache('mod_menu');
		$cache->clean();
		

		//redirect
		$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
		$sub_task = $useCheckedOut ? '': '&sub_task=edit';
		//$sub_task = JRequest::getVar('sub_task','');
		//$sub_task = $sub_task ? '&sub_task='.$sub_task : '';
		$this->setRedirect("index.php?option=com_pagesanditems&view=page$sub_task&pageId=$pageId&menutype=$new_menutype", JText::_('COM_PAGESANDITEMS_PAGEMOVESAVED'));
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
			PagesAndItemsHelper::deletePage($jform['id']);
		}else{
			PagesAndItemsHelper::trashPage($jform['id']);
		}

		if($jform['parent_id']=='1'){
			//go to root
			$url = 'index.php?option=com_pagesanditems&view=page&layout=root&menutype='.$menutype;
		}else{
			$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
			$sub_task = $useCheckedOut ? '': '&sub_task=edit';
			$url = 'index.php?option=com_pagesanditems&view=page'.$sub_task.'&pageId='.$jform['parent_id'].'&menutype='.$menutype.'&pageType='.$pageType;
		}

		$this->setRedirect($url, JText::_('COM_PAGESANDITEMS_PAGE_TRASHED'));
	}





	//called from small buttons on under 'Underlaying Pages'
	function pages_state(){

		$helper = $this->helper;

		$pageId = JRequest::getVar('pageId', '', 'post');
		$pageType = JRequest::getVar('pageType', '', 'post');
		$menutype = JRequest::getVar('menutype', '', 'post');
		$cid = JRequest::getVar('pageCid', array(), 'post', 'array');

		$layout = JRequest::getVar('layout', null, 'post');

		$sub_task = JRequest::getVar('sub_task');
		$subsub_task = JRequest::getVar('subsub_task');

		if($subsub_task=='delete'){
			$message = JText::_('COM_PAGESANDITEMS_PAGES_DELETED');
			$new_state = 'delete';
		}elseif($subsub_task=='trash'){
			$message = JText::_('COM_PAGESANDITEMS_PAGES_TRASHED');
			$new_state = '-2';
		}elseif($subsub_task=='archive'){
			$message = JText::_('COM_PAGESANDITEMS_PAGES_ARCHIVED');
			$new_state = '2';
		}elseif($subsub_task=='publish'){
			$message = JText::_('COM_PAGESANDITEMS_PAGES_PUBLISHED');
			$new_state = '1';
		}elseif($subsub_task=='unpublish'){
			$message = JText::_('COM_PAGESANDITEMS_PAGES_UNPUBLISHED');
			$new_state = '0';
		}

		if(count($cid)){
			//there are pages
			foreach($cid as $page_id){
				//delete pages
				PagesAndItemsHelper::page_state($page_id, $new_state);
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
			//$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
			$sub_task = $sub_task ? '&sub_task='.$sub_task : '';
			$url = 'index.php?option=com_pagesanditems&view=page'.$sub_task.'&menutype='.$menutype.'&pageId='.$pageId.'&pageType='.$pageType;
		}
		//$helper->redirect_to_url($url, $message);
		$this->setRedirect(JRoute::_($url, false), $message);

	}


	function root_cancel()
	{
		$menutype = JRequest::getVar('menutype', '');
		$message = JText::_('COM_PAGESANDITEMS_PAGE_CANCEL'); //JText::_('COM_PAGESANDITEMS_PAGE_NOTHING_TO_DO')
		/*if($menutype != '')
		{
			$menutype = '&menutype='.$menutype;
		}*/
		$menutype = $menutype ? '&menutype='.$menutype : '';
		$url = "index.php?option=com_pagesanditems&view=page&layout=root".$menutype; //&menutype=".$menutype;
		$app = JFactory::getApplication();
		$app->redirect($url, $message);
		//$this->redirect_to_url($url, $message);
	}


	function root_menutype_new(){
		
		$menutype = JRequest::getVar('menutype', '');
		$menutype = $menutype ? '&menutype='.$menutype : '';
		$url = 'index.php?option=com_pagesanditems&view=page&layout=root'.$menutype.'&sub_task=newMenutype';
		$this->setRedirect($url,'new Menutype');
	
	}
	
	function root_menutype_save(){
		
		$app = &JFactory::getApplication();
		$app->enqueueMessage('save Menutype do nothing');
		$this->root_save();
		
		$url = 'index.php?option=com_pagesanditems&view=page&layout=root'; //&sub_task=editMenutype';
		$this->setRedirect($url,'save Menutype do nothing');
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


		//$config = PagesAndItemsHelper::getConfigAsRegistry();
		//$useCheckedOut = $config->get('useCheckedOut',0);
		$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
		// Initialise variables.
		$app		= JFactory::getApplication();
		$model	= $this->getModel( 'Item' ,'MenusModel');
		//$model	= $this->getModel('Item', '', array());
		$data		= JRequest::getVar('jform', array(), 'post', 'array');

		//$task		= $this->getTask();


		$context	= 'com_menus.edit.item';
		$recordId	= JRequest::getInt('id');
		



		//PI ACL ms???
		if(!$recordId){
			//new page
			/*
			//ms: replace for PI ACL?
			if(!JFactory::getUser()->authorise('core.create', 'com_menus')
			{
				$app->enqueueMessage(JText::_('COM_PAGESANDITEMS_NO_CREATE_PAGE'), 'warning');
				return false;
			}
			*/
			PagesAndItemsHelper::die_when_no_permission('1');
		}else{
			//edit page
			/*
			//ms: replace for PI ACL?
			if(!JFactory::getUser()->authorise('core.edit', 'com_menus')
			{
				$app->enqueueMessage(JText::_('COM_PAGESANDITEMS_NO_EDIT_PAGE'), 'warning');
				return false;
			}
			*/
			PagesAndItemsHelper::die_when_no_permission('2');
		}

		// set the form path
		JForm::addFormPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models'.DS.'forms');

		// set the fields path
		JForm::addFieldPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models'.DS.'fields');

		//
		if (!$this->checkEditId($context, $recordId) && !$this->checkEditId('com_pagesanditems.page.edit.item', $recordId))
		{
			// Somehow the person just went to the form and saved it - we don't allow that.
			/*
			//ms: com_menus.edit.item.id
			$app->enqueueMessage(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $recordId), 'error');
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
		if($pageType == 'content_article')
		{
			$form->setFieldAttribute('id', 'required', false,'request');
			
			//TODO ms: if we have no id for the article we must do disabled/trash the page
			//if(!$form->getValue('id', 'request'))
			//$form->setValue('published', null,'0');
			
		}

		/*
			check here for pagetype  == 'content_category_blog'
			so we not must have an id here
		*/
		//$pageType = JRequest::getVar('pageType', '');
		if($pageType == 'content_category_blog' || $pageType == 'content_category' || $pageType == 'content_categories')
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
			//$app->setUserState('com_menus.edit.item.data', $data);

			// Redirect back to the edit screen.
			//$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
			//$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_item.$this->getRedirectToItemAppend($recordId), false));
			//$app->setUserState('com_menus.edit.item.data', $data);


			$app->enqueueMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
			$menutype = JRequest::getVar('menutype', '');
			$layout = JRequest::getVar('layout', '');
			if($layout != '')
			{
				$layout = '&layout='.$layout;
			}
			
			$sub_task = JRequest::getVar('sub_task', '');
			$sub_task = $sub_task ? '&sub_task='.$sub_task : '';
			if($recordId)
			{
				// Redirect back to the edit screen.
				//$app->setUserState('com_menus.edit.item.data', $data);
				
				$app->redirect(JRoute::_('index.php?option=com_pagesanditems&view=page'.$layout.$sub_task.'&pageId='.$recordId.'&pageType='.$pageType.'&menutype='.$menutype, false));
			}
			else
			{
				//$data['id'] = '';
				//$app->setUserState('com_menus.edit.item.data', $data);
				//$app->redirect(JRoute::_('index.php?option=com_pagesanditems&view=page'.$layout.'&sub_tak=new&pageType='.$pageType.'&menutype='.$menutype.'&pageTypeType='.JRequest::get('pageTypeType','').'&type='.JRequest::get('type',''), false));
			}

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

		if(!$useCheckedOut)
		{
			return $item; //->id;
		}
		

		// Redirect the user and adjust session state based on the chosen task.

		switch ($task) {
			case 'page_apply':
				// Set the row data in the session.
				$recordId = $model->getState($context.'.id');
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
		return $item;
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

		$db = JFactory::getDBO();
		$app = &JFactory::getApplication();



		$sub_task = JRequest::getVar('sub_task', ''); //null, 'edit', 'cmd');
		$subsub_task = JRequest::getVar('subsub_task', ''); //, null, 'edit', 'cmd');

		$menutype = JRequest::getVar('menutype', '');
		$task = JRequest::getVar('task', '') ;//, null, 'page_save', 'cmd');
		//declare message
		$message = array();
		if($msg = $this->page_reorder())
			$message[] = $msg;
		if($msg = $this->item_reorder())
			$message[] = $msg;
		/*
		$message = array();
		if($msg = $this->category_reorder())
			$message[] = $msg;
		if($msg = $this->item_reorder())
			$message[] = $msg;
		
		*/
		
		$post	= JRequest::get('post');

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
		//$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false);
		$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false) || $lang->load(strtolower($extension), JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
		//let do the com_menus/model/item save
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();

		
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
			$url = 'index.php?option=com_pagesanditems&view=page&layout=root';
			$app = JFactory::getApplication();
			$app->redirect($url);
		}
		else
		{
			//$url = 'index.php?option=com_pagesanditems&view=page&layout=root';
			//$app = JFactory::getApplication();
			//$app->redirect($url);
		}

		//TODO what we will send to the extension?
		//answer to Micha: the data and if the page is new
		if($save)
		{
			//echo $item->id;
			$dispatcher->trigger('onAfterSave',array($item->id, $data, $isnew,$item));
		}
		$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
		if($sub_task=='edit')
		{
			if($task=='page_apply')
			{
				//$sub_task = $useCheckedOut ? '&sub_task=edit' : '';
				//
				$checkin = JRequest::getVar('checkin', false);
				//here we get $checkin only if we useCheckedOut and task = page_checkin
				//and if so we use no edit sub_task
				$sub_task = $checkin ? '' : '&sub_task=edit';
				$url = 'index.php?option=com_pagesanditems&view=page'.$sub_task.'&pageId='.$item->id.'&menutype='.$item->menutype;
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
					$sub_task = $useCheckedOut ? '' : '&sub_task=edit';
					$url = 'index.php?option=com_pagesanditems&view=page'.$sub_task.'&pageId='.$itemParentId.'&menutype='.$item->menutype;//.'&test';
				}
			}
		}
		else
		{
			//new page
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
					$sub_task = $useCheckedOut ? '' : '&sub_task=edit';
					$url = 'index.php?option=com_pagesanditems&view=page'.$sub_task.'&pageId='.$itemParentId.'&menutype='.$item->menutype;
				}
			}
		}
		
		
		
//		$message = (count($message) ? ''.implode(', ', $message) : '');
		$message = (count($message) ? ''.implode(', ', $message) : '');
		if($message)
		$this->setMessage($message.$this->message);
		$this->setRedirect(JRoute::_($url, false)); //, $message);
		//$app->redirect($url, $message);

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
		$pageType = JRequest::getVar('pageType', 'pageType');
		$pageType = $pageType ? '&pageType='.$pageType : '';
		//if we make J1.5 we must get the parent_id in other way
		
		$jform = JRequest::getVar('jform', null, 'post', 'array');
		$parent_id = 1;
		if($jform && $jform['parent_id'])
		$parent_id = $jform['parent_id'];
		
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

		$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
		
		$sub_task = JRequest::getVar('sub_task', '');
		
		if($useCheckedOut && $sub_task == 'edit')
		{
			$user = JFactory::getUser();
			//TODO realice it over the model
			// Get an instance of the row to checkin.
			$table = JTable::getInstance('menu'); //, $prefix, $config); //'content';
			if (!$table->load($pageId)) {
				//$this->setError($table->getError());
				//return false;
			}

			// Check if this is the user having previously checked out the row.
			if ($table->checked_out > 0 && $table->checked_out != $user->get('id') && !$user->authorise('core.admin', 'com_checkin')) {
				//$this->setError(JText::_('JLIB_APPLICATION_ERROR_CHECKIN_USER_MISMATCH'));
				//return false;
			}

			// Attempt to check the row in.
			if (!$table->checkin($pageId)) {
				//$this->setError($table->getError());
				//return false;
			}
		/*
		}

		if($useCheckedOut && $sub_task == 'edit')
		{
		*/
			$url = 'index.php?option=com_pagesanditems&view=page&pageId='.$pageId.'&menutype='.$menutype.$pageType;
			$message = JText::_('COM_PAGESANDITEMS_ACTION_CANCELED');
		}
		elseif($parent_id == '1')
		{
			//go to root
			$url = 'index.php?option=com_pagesanditems&view=page&layout=root&menutype='.$menutype;
			$message = JText::_('COM_PAGESANDITEMS_ACTION_CANCELED');
		}
		else
		{
			//$config = PagesAndItemsHelper::getConfigAsRegistry();
			//$useCheckedOut = $config->get('useCheckedOut',0);
			//$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
			//$sub_task = $useCheckedOut ? '' : '&sub_task=edit';
			$sub_task = $sub_task ? '&sub_task='.$sub_task : '';
			$url = 'index.php?option=com_pagesanditems&view=page'.$sub_task.'pageId='.$parent_id.'&menutype='.$menutype; //.'&pageType='.$pageType;
			$message = JText::_('COM_PAGESANDITEMS_ACTION_CANCELED');
		}
		//$app = JFactory::getApplication();
		//$app->redirect($url,$message);
		$this->setRedirect($url,$message);
		/*
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
		*/
	}

	//is not function page_checkin()
	function checkin()
	{
		//JRequest::setVar('task', 'category_apply'); //, 'cmd');
		//JRequest::setVar('checkin', true);
		//JRequest::setVar('useCheckin', 1);
		//$this->category_save();
		$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
		$pageId = JRequest::getVar('pageId', 0 );
		
		$menutype = JRequest::getVar('menutype', 'menutype');
		$menutype = $menutype ? '&menutype='.$menutype : '';

		$pageType = JRequest::getVar('pageType', 'pageType');
		$pageType = $pageType ? '&pageType='.$pageType : '';
		
		if($useCheckedOut)
		{
			$user = JFactory::getUser();
			//TODO realice it over the model
			// Get an instance of the row to checkin.
			$table = JTable::getInstance('menu'); //, $prefix, $config); //'content';
			if (!$table->load($pageId)) {
				//$this->setError($table->getError());
				//return false;
			}

			// Check if this is the user having previously checked out the row.
			if ($table->checked_out > 0 && $table->checked_out != $user->get('id') && !$user->authorise('core.admin', 'com_checkin')) {
				//$this->setError(JText::_('JLIB_APPLICATION_ERROR_CHECKIN_USER_MISMATCH'));
				//return false;
			}

			// Attempt to check the row in.
			if (!$table->checkin($pageId)) {
				//$this->setError($table->getError());
				//return false;
			}
		}
		$url = 'index.php?option=com_pagesanditems&view=page&pageId='.$pageId.$menutype.$pageType;
		$this->setRedirect(JRoute::_($url, false)); //, JText::_('COM_PAGESANDITEMS_ACTION_CANCELED'));
	}

	function page_reorder()
	{
		$db = JFactory::getDBO();
		$app = &JFactory::getApplication();
		//$sub_task = JRequest::getVar('sub_task', null, 'edit', 'cmd');
		//$subsub_task = JRequest::getVar('subsub_task', null, 'edit', 'cmd');

		//$menutype = JRequest::getVar('menutype', '');
		//$task = JRequest::getVar('task', null, 'page_save', 'cmd');
		//declare message
		$message = '';
		//if pages where reordered update the ordering of these pages
		/*
		$pages_are_reordered = JRequest::getVar('pages_are_reordered',0);
		$pages_total = JRequest::getVar('pages_total',0);
		*/
		$pages_are_reordered = JRequest::getVar('items_page_are_reordered',0);
		$pages_total = JRequest::getVar('items_page_total',0);
		//items_page_are_reordered
		//items_page_total
		/*
		// Initialise variables.
		$ids = JRequest::getVar('cid', null, 'post', 'array');
		$inc = ($this->getTask() == 'orderup') ? -1 : +1;

		$model = $this->getModel();
		$return = $model->reorder($ids, $inc);
		
		
		*/
		
		
		if($pages_are_reordered==1){
			for ($n = 1; $n <= $pages_total; $n++){
				$temp_id = intval(JRequest::getVar('reorder_page_id_'.$n, '', 'post'));
				if(PagesAndItemsHelper::getIsJoomlaVersion('>=','1.6')){
					$lft = intval(JRequest::getVar('reorder_lft_'.$n, '', 'post'));
					$db->setQuery( "UPDATE #__menu SET lft='$lft' WHERE id='$temp_id'");
				}else{
					$db->setQuery( "UPDATE #__menu SET ordering='$n' WHERE id='$temp_id'");
				}
				$db->query();
				//$message = JText::_('COM_PAGESANDITEMS_PAGEORDER_SAVED');
				if($n == 1)
				$app->enqueueMessage(JText::_('COM_PAGESANDITEMS_PAGEORDER_SAVED'));
			}

				//require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models'.DS.'item.php');
				//$model = new MenusModelItem;
				//$model->rebuild();
		}
		return $message;
	}

	function item_reorder()
	{
		$db = JFactory::getDBO();
		$app = &JFactory::getApplication();
		//$sub_task = JRequest::getVar('sub_task', null, 'edit', 'cmd');
		//$subsub_task = JRequest::getVar('subsub_task', null, 'edit', 'cmd');

		//$menutype = JRequest::getVar('menutype', '');
		//$task = JRequest::getVar('task', null, 'page_save', 'cmd');
		//declare message
		$message = '';
		//if items where reordered update the ordering of these items
		/*
		$items_are_reordered = JRequest::getVar('items_are_reordered','');
		$items_total = JRequest::getVar('items_total', '');
		*/
		$items_are_reordered = JRequest::getVar('items_item_are_reordered',0);
		$items_total = JRequest::getVar('items_item_total',0);
		//items_page_are_reordered
		//items_page_total
		
		if($items_are_reordered==1){
			for ($n = 1; $n <= $items_total; $n++){
				$temp_id = intval(JRequest::getVar('reorder_item_id_'.$n, '', 'post'));
				$db->setQuery( "UPDATE #__content SET ordering='$n' WHERE id='$temp_id'");
				$db->query();
			}
			//$message = JText::_('COM_PAGESANDITEMS_ITEMS_ORDER_SAVED');
			$app->enqueueMessage(JText::_('COM_PAGESANDITEMS_ITEMS_ORDER_SAVED'));
		}
		return $message;
	}
	
	function item_reorder_save()
	{
		$message = $this->item_reorder();
		$this->reorder_save('reorder_apply',$message);
	}
	

	function page_reorder_save()
	{
		$message = $this->page_reorder();
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
			$message[] = $this->page_reorder();
			$message[] = $this->item_reorder();
			$task = JRequest::getVar('task', ''); //null, 'page_save', 'cmd');
		}
		
		$sub_task = JRequest::getVar('sub_task', ''); //null, 'edit', 'cmd');
		$menutype = JRequest::getVar('menutype', '');

		
		$app = &JFactory::getApplication();
		
		/*
		$data = JRequest::getVar('jform', null, 'post', 'array');
		$item->id = $data['id'];
		$item->menutype = $data['menutype'];
		$item->parent_id = $data['parent_id'];
		if($task=='reorder_apply')
		{
				$sub_task = ''; //'&sub_task=edit';
				$url = 'index.php?option=com_pagesanditems&view=page'.$sub_task.'&pageId='.$item->id.'&menutype='.$item->menutype;
		}
		else
		{
			$itemParentId = $item->parent_id;
			$itemParent = false;
			if($item->parent_id == 1 )
			{
				$itemParent = true;
			}
			//normal save so go to parent page
			if($itemParent)
			{
				$url = 'index.php?option=com_pagesanditems&view=page&layout=root&menutype='.$item->menutype;
			}
			else
			{
				$sub_task = ''; //$useCheckedOut ? '' : '&sub_task=edit';
				$url = 'index.php?option=com_pagesanditems&view=page'.$sub_task.'&pageId='.$itemParentId.'&menutype='.$item->menutype;//.'&test';
			}
		}
		*/
		$layout = JRequest::getVar('layout', '');
		$app = &JFactory::getApplication();
		if(!$layout)
		{
			$data = JRequest::getVar('jform', null, 'post', 'array');
			$item->id = $data['id'];
			$item->menutype = $data['menutype'];
			$item->parent_id = $data['parent_id'];
			if($task=='reorder_apply')
			{
					//$sub_task = ''; //'&sub_task=edit';
					$url = 'index.php?option=com_pagesanditems&view=page'.$sub_task.'&pageId='.$item->id.'&menutype='.$item->menutype;
			}
			else
			{
				$itemParentId = $item->parent_id;
				$itemParent = false;
				if($item->parent_id == 1 )
				{
					$itemParent = true;
				}
				//normal save so go to parent page
				if($itemParent)
				{
					$url = 'index.php?option=com_pagesanditems&view=page&layout=root&menutype='.$item->menutype;
				}
				else
				{
					//$sub_task = ''; //$useCheckedOut ? '' : '&sub_task=edit';
					$url = 'index.php?option=com_pagesanditems&view=page'.$sub_task.'&pageId='.$itemParentId.'&menutype='.$item->menutype;//.'&test';
				}
			}
		}
		else
		{
			$url = 'index.php?option=com_pagesanditems&view=page&layout=root&menutype='.$menutype;
		}
		//$message = (count($message) ? ''.implode(', ', $message) : '');
		$message = (count($message) ? ''.implode(', ', $message) : '');
		$app->redirect($url, $message);
	}


	function root_save(){
		$db = JFactory::getDBO();
		$app = &JFactory::getApplication();

		//if pages where reordered update the ordering of these pages
		/*
		$pages_are_reordered = JRequest::getVar('pages_are_reordered',0);
		$pages_total = JRequest::getVar('pages_total',0);
		*/
		$pages_are_reordered = JRequest::getVar('items_page_are_reordered',0);
		$pages_total = JRequest::getVar('items_page_total',0);
		//items_page_are_reordered
		//items_page_total

		$message = '';
		$message = $this->page_reorder();
		$menutype = JRequest::getVar('menutype', '') ? '&menutype='.JRequest::getVar('menutype', '') : '';
		$url = 'index.php?option=com_pagesanditems&view=page&layout=root'.$menutype;
		$app->redirect($url, $message);

	}




	function Xroot()
	{
		echo 'root';
		$doc =& JFactory::getDocument();

		JHTML::stylesheet('pagesanditems2.css', 'administrator/components/com_pagesanditems/css/');

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
