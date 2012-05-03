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
jimport('joomla.client.helper');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controller.php');


class PagesAndItemsControllerItem extends PagesAndItemsController{

	public $helper;

	function __construct( $default = array())
	{
		//get helper
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'pagesanditems.php');
		$this->helper = new PagesAndItemsHelper();

		parent::__construct($default);
		$this->registerTask( 'item_apply', 'item_save' );
		$this->registerTask( 'item_checkin', 'item_save' );
		//$this->registerTask( 'item_save2new', 'item_save' );
		$this->registerTask( 'save2copy', 'item_save' );
	}

	function item_edit()
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
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$pageType = JRequest::getVar( 'pageType', '');
		$menutype = JRequest::getVar('menutype', '' );
		$menutype = $menutype ? '&menutype='.$menutype : '';
		$pageId = JRequest::getVar('pageId', 0 );
		$pageId = $pageId ? '&pageId='.$pageId : '';
		$categoryId = JRequest::getVar('categoryId', 0 );
		$categoryId = $categoryId ? '&categoryId='.$categoryId : '';
		$itemId = JRequest::getVar('itemId', 0 );
		$itemId = $itemId ? '&itemId='.$itemId : '';
		$sub_task = '&sub_task=edit';

		$url = 'index.php?option=com_pagesanditems&view=item'.$sub_task.$itemId.$menutype.$pageId.$categoryId;
		$this->setRedirect(JRoute::_($url, false));
	}


	function item_new()
	{
		//$model = &$this->getModel('Item','PagesAndItemsModel');
		$pageId = JRequest::getVar('pageId',0);
		$item_type = JRequest::getVar('select_itemtype', ''); //, 'post');
		$categoryId = JRequest::getVar('categoryId',1);
		$pageType = JRequest::getVar('pageType',1);
		$menutype = JRequest::getVar('menutype',1);
		
		//$item_id = JRequest::getVar('item_id', '', 'post');
		
		if(!$pageId && !$menutype && $categoryId)
		{
			//$url = 'index.php?option=com_pagesanditems&view=categorie&sub_task=edit&categoryId='.$categoryId;
			$url = 'index.php?option=com_pagesanditems&view=item&sub_task=new&item_type='.$item_type.'&categoryId='.$categoryId;
		}
		elseif($menutype && $pageId)
		{
			//$url = "index.php?option=com_pagesanditems&view=page&sub_task=edit&menutype=".$menutype."&pageId=".$pageId;
			$url = 'index.php?option=com_pagesanditems&view=item&sub_task=new&item_type='.$item_type.'&pageId='.$pageId.'&menutype='.$menutype.'&pageType='.$pageType;
		}
		elseif($menutype)
		{
			$url = 'index.php?option=com_pagesanditems&view=item&sub_task=new&item_type='.$item_type.'&menutype='.$menutype;
			//$url = "index.php?option=com_pagesanditems&view=page&layout=root&menutype=".$menutype;
		}
		else
		{
			$url = 'index.php?option=com_pagesanditems&view=item&sub_task=new&item_type='.$item_type;
			//$url = "index.php?option=com_pagesanditems&view=page&layout=root";
		}
		//$url = 'index.php?option=com_pagesanditems&view=item&sub_task=new&pageId='.$pageId.'&item_type='.$item_type.'&categoryId='.$categoryId;
		
		$this->setRedirect(JRoute::_($url, false));
		//$model->redirect_to_url( $url);
	}

	function checkin()
	{
		//JRequest::setVar('task', 'category_apply'); //, 'cmd');
		//JRequest::setVar('checkin', true);
		//JRequest::setVar('useCheckin', 1);
		//$this->category_save();
		$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
		$pageId = JRequest::getVar('pageId', 0 );
		
		$menutype = JRequest::getVar('menutype', '');
		$menutype = $menutype ? '&menutype='.$menutype : '';

		$itemId = JRequest::getVar('itemId', '');
		//$itemId = $itemId ? '&itemId='.$itemId : '';
		
		if($useCheckedOut)
		{
			$user = JFactory::getUser();
			//TODO realice it over the model
			// Get an instance of the row to checkin.
			$table = JTable::getInstance('content'); //, $prefix, $config); //'content';
			if (!$table->load($itemId)) 
			{
				//$this->setError($table->getError());
				//return false;
			}

			// Check if this is the user having previously checked out the row.
			if ($table->checked_out > 0 && $table->checked_out != $user->get('id') && !$user->authorise('core.admin', 'com_checkin')) 
			{
				//$this->setError(JText::_('JLIB_APPLICATION_ERROR_CHECKIN_USER_MISMATCH'));
				//return false;
			}

			// Attempt to check the row in.
			if (!$table->checkin($itemId)) 
			{
				//$this->setError($table->getError());
				//return false;
			}
		}
		$url = 'index.php?option=com_pagesanditems&view=item&pageId='.$pageId.'&itemId='.$itemId.$menutype;
		$this->setRedirect(JRoute::_($url, false)); //, JText::_('COM_PAGESANDITEMS_ACTION_CANCELED'));
	}

	
	function item_save(){

		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();

		//here we need the model page or item
		$model = &$this->getModel('Item','PagesAndItemsModel');
		$config = PagesAndItemsHelper::getConfig();

		$item_id = JRequest::getVar('id', '', 'post');
		$item_type = JRequest::getVar('item_type', '', 'post');
		$show_title_item = intval(JRequest::getVar('show_title_item'));
		$message = '';

		//get data
		$data = JRequest::getVar('jform', array(), 'post', 'array');

		$recordId	= JRequest::getInt('id');
		// Populate the row id from the session.
		$data['id'] = $recordId;

		
		$task = $this->getTask();
		
		$apply = JRequest::getVar('item_apply', '', 'post');
		if($task == 'save2copy' ) //|| $task == 'save2new')
		{
			//$oldarticle = $ContentModelArticle->getItem();
			$old_item_id = $item_id;
			// Reset the ID and then treat the request as for Apply.
			$data['id'] = 0;
			$data['associations'] = array();
			$item_id = 0;
			$apply = 1;
		}

		//get new or edit
		$new_or_edit = 'edit';
		if(!$item_id){
			$new_or_edit = 'new';
		}


		

		//get category_id
		$cat_id = 0;
		$created_by = 0;
		$db->setQuery("SELECT * "
		." FROM #__content "
		." WHERE id='$item_id' "
		." LIMIT 1 "
		);
		/*
		$rows = $db->loadObjectList();
		foreach($rows as $row){
			$cat_id = $row->catid;
		}
		*/
		$row = $db->loadObject();
		if($row)
		{
			$cat_id = $row->catid;
			$created_by = $row->created_by;
		}

		$canDo_delete = 0;
		if($new_or_edit=='edit'){
			//get Joomla ACL for this article
			//include com_content helper
			//require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'content.php');
			//$ContentHelper = new ContentHelper;
			//$canDo = ContentHelper::getActions($cat_id, $item_id);
			
			$canDoContent = PagesAndItemsHelper::canDoContent($cat_id, $item_id);
			$user		= JFactory::getUser();
			$userId		= $user->get('id');
			$canEdit	= $canDoContent->get('core.edit'); //$user->authorise('core.edit',			'com_content.article.'.$row->id);
			$canEditOwn	= $canDoContent->get('core.edit.own') && $created_by == $userId;
			
			//if(!$canDo->get('core.edit'))
			if((!$canEdit && !$canEditOwn))
			{
				echo JText::_('COM_PAGESANDITEMS_NO_PERMISSION_TO_EDIT_THIS_ITEM');
				exit;
			}

			if($canDoContent->get('core.delete')){
				$canDo_delete = 1;
			}
		}
		//ms: ???
		//PI ACL
		if(!$item_id){
			//new item
			PagesAndItemsHelper::die_when_no_permission('3');
		}else{
			//edit item
			PagesAndItemsHelper::die_when_no_permission('4');
		}

		//workaround to get past validation in com_content
		$text = $data['articletext'];
		if($text==''){
			$data['articletext'] = '&nbsp;';
		}

		//get alias
		$alias = $data['alias'];
		if($alias=='')
		{
			$alias = PagesAndItemsHelper::make_alias($data['title']);
		}else{
			$alias = PagesAndItemsHelper::make_alias($alias);
		}
		$alias = addslashes($alias);

		//make alias unique
		if($config['make_article_alias_unique'])
		{
			$alias = PagesAndItemsHelper::make_alias_unique($alias, 'content', $item_id);
		}
		$data['alias'] = $alias;

		//take 'featured' out of array as com_contents model chokes on that on line 223
		//after the article is saved we do the feature-stuff
		$featured_was_parsed = 0;
		if(isset($data['featured'])){
			//need to check for this as registered users do not get the featured option when submitting an article
		$featured = $data['featured'];
		unset($data['featured']);
			$featured_was_parsed = 1;
		}

		//get the com_content model (controller?) and save the article
		//then update later if the article is a CCK
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.php');

		//ms: ADD Begin
		// set the form path
		JForm::addFormPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms');
		$ContentModelArticle = new ContentModelArticle();
		// Validate the posted data.
		// Sometimes the form needs some posted data, such as for plugins and modules.
		
		

		
		$form = $ContentModelArticle->getForm($data, false);

		if (!$form) {
			$app->enqueueMessage($ContentModelArticle->getError(), 'error');
			//TODO ms: ??
			//return false;
		}

		// Test whether the data is valid.
		$validData = $ContentModelArticle->validate($form, $data);

		// Check for validation errors.
		if ($validData === false) {
			// Get the validation messages.
			$errors	= $ContentModelArticle->getErrors();

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
			//TODO ms: ??
			//$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_item.$this->getRedirectToItemAppend($recordId, $key), false));

			//return false;
		}
		//dump($data);
		$data = $validData;
		//dump($data);
		$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
		if($useCheckedOut)
		{
			//$context	= 'com_content.edit.item';
			//$recordId	= JRequest::getInt('id');
			/*
			TODO
			if(JRequest::getVar('edit_from_frontend', '', 'post'))
			{
			//redirect to item in full view
				$url = 'index.php?option=com_content&amp;view=article&amp;id='.$item_id.'&amp;catid='.$cat_id.'&amp;Itemid='.$pageId;
			}
			else
			{
				$pageId = JRequest::getVar('pageId', null, 'post');
				$menutype = JRequest::getVar('menutype', null, 'post');
				$pageType = JRequest::getVar('pageType', null, 'post');
				$categoryId = JRequest::getVar('categoryId', '' );
				$url = 'index.php?option=com_pagesanditems&view=item&sub_task=edit'.($pageId ? '&pageId='.$pageId : '').'&itemId='.$item_id.($categoryId ? '&categoryId='.$categoryId : '');
			}
			
			*/
			if ($task == 'save2copy') {
				// Check-in the original row.
				if ($ContentModelArticle->checkin($old_item_id) === false)
				{
					// Check-in failed, go back to the item and display a notice.
					//$app->enqueueMessage(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $ContentModelArticle->getError()), 'warning');
					//return false;
				}
			}
			

			
			
			if (!$ContentModelArticle->save($data))
			{
				$app->enqueueMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $ContentModelArticle->getError()), 'warning');
				/*
				$app->redirect(JRoute::_($url, false));
				*/
			}
			
			
			
			/*
			if ($ContentModelArticle->checkin($data['id']) === false)
			{
				// Check-in failed, go back to the row and display a notice.
				$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $ContentModelArticle->getError()), 'warning');
				//$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_item.$this->getRedirectToItemAppend($recordId), false));
				//$app->redirect(JRoute::_($url, false));
			}
			*/
		}
		else
		{
			$ContentModelArticle->save($data);
		}

		if($new_or_edit=='new'){

			$article = $ContentModelArticle->getItem();
			$item_id = $article->id;

			//get cat_id
			$cat_id = $data['catid'];

			//get the order of the last article in the category
			if($cat_id){
				$db->setQuery("SELECT ordering FROM #__content WHERE catid='$cat_id' ORDER BY ordering DESC LIMIT 1 ");
				$rows = $db->loadObjectList();
				$new_order = 0;
				foreach($rows as $row){
					$new_order = $row->ordering+1;
				}

				$db->setQuery( "UPDATE #__content SET ordering='$new_order' WHERE id='$item_id' ");
				$db->query();
			}

			//insert new item in item index
			$db->setQuery( "INSERT INTO #__pi_item_index SET item_id='$item_id', itemtype='$item_type', show_title='$show_title_item'");
			$db->query();
		}

		if($new_or_edit=='edit'){

			//check if item has a entry in item-index (if so, its been created or editted with PI before) if not, make a new index row
			$db->setQuery("SELECT id FROM #__pi_item_index WHERE item_id='$item_id' LIMIT 1");
			$rows = $db->loadObjectList();
			$id = 0;
			foreach($rows as $row){
				$id = $row->id;
			}
			if(!$id){
				$db->setQuery( "INSERT INTO #__pi_item_index SET item_id='$item_id', itemtype='$item_type', show_title='$show_title_item'");
				$db->query();
			}else{
				$db->setQuery( "UPDATE #__pi_item_index SET itemtype='$item_type', show_title='$show_title_item' WHERE id='$id' ");
				$db->query();
			}

			
		}
		
		//update featured as that creates an error if that is processed the normal way (see line 252)
		if($featured_was_parsed){
			//only consider featured action when user had the featured select on the edit page, like for 'registrered' users 			
			$db->setQuery("SELECT content_id FROM #__content_frontpage WHERE content_id='$item_id' LIMIT 1");
			$rows_frontpage = $db->loadObjectList();
			$is_on_frontpage = false;
			foreach($rows_frontpage as $row_frontpage){
				$is_on_frontpage = $row_frontpage->content_id;
			}
			if($is_on_frontpage){
				if($featured==0){
					$this->take_item_off_frontpage($item_id);
				}
			}else{
				if($featured==1){
					$this->put_item_on_frontpage($item_id);
				}
			}
		}


		//update assets properly
		//only at admin
		/*
		ms: without will save corect
		ms: user with core.admin have not the $data['rules'] in the view
		and $this->clean_rules work not correct
		if($app->isAdmin()){
			//just making this json does not take the empties out!!
			$rules = $data['rules'];
			$rules_delete = $this->clean_rules($rules['core.delete']);
			$rules_edit = $this->clean_rules($rules['core.edit']);
			$rules_edit_state = $this->clean_rules($rules['core.edit.state']);
			$rules_string = '{"core.delete":'.$rules_delete.',"core.edit":'.$rules_edit.',"core.edit.state":'.$rules_edit_state.'}';
			$asset_name = 'com_content.article.'.$item_id;
			$db->setQuery( "UPDATE #__assets SET rules='$rules_string' WHERE name='$asset_name' ");
			$db->query();
		}
		
		*/

		$path = realpath(dirname(__FILE__).DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'itemtypehelper.php');
		if(strpos($item_type, 'ustom_'))
		{
			/*
				here we will load the itemtype custom
			*/
			//$itemtype =
			ExtensionItemtypeHelper::importExtension(null, 'custom',true,null,true);
		}
		else
		{
			/*
				here we will load all the other
				content, text, html and other_item are integrated
				also itemtype there are in old PI called plugin (plugins/pages_and_items/itemtypes)
			*/
			//$itemtype = 
			ExtensionItemtypeHelper::importExtension(null, $item_type,true,null,true);
		}
		$dispatcher = &JDispatcher::getInstance();


		//if delete check for right and delete
		$delete_item = intval(JRequest::getVar('delete_item',null));
		if($delete_item && $canDo_delete){
			//ms: comment all trigger are in the helper
			PagesAndItemsHelper::item_state($item_id, 'delete');
		}

		if(!$delete_item)
		{
			//trigger itemtype specific item save event
			$dispatcher->trigger('onItemtypeItemSave', array ($item_type, $delete_item, $item_id, $new_or_edit));

			//check for dependant items of type 'other item' and update those if needed
			ExtensionItemtypeHelper::importExtension(null, 'other_item',true,null,true);
			$dispatcher->trigger('update_other_items_if_needed', array($item_id));

			require_once($path.DS.'includes'.DS.'extensions'.DS.'managerhelper.php');
			$extensions = ExtensionManagerHelper::importExtension(null,null, true,null,true);
			//ms: here we trigger the extensins type manager like template who can have own fields to save
			$dispatcher->trigger('onManagerItemtypeItemSave', array ($item_type,$item_id, $new_or_edit)); //$delete_item

		
		

			 
		}

		

		//sanitize item-index-table
		$model->keep_item_index_clean();
		//clean cache
		$model->clean_cache_content();

		$pageId = JRequest::getVar('pageId', null, 'post');
		$menutype = JRequest::getVar('menutype', null, 'post');
		$pageType = JRequest::getVar('pageType', null, 'post');
		$manager = JRequest::getVar('manager',0);
		//$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
		//redirect
		if(JRequest::getVar('edit_from_frontend', '', 'post'))
		{
			//frontend
			if($new_or_edit=='new'){
				$pageId = $model->get_menu_id_from_category_blog($cat_id);
			}else{
				$pageId = JRequest::getVar('pageId', 0, 'post');
			}
			if($config['item_save_redirect']=='category_blog'){
				//redirect to categroy blog
				if($pageId){
					$url = $model->get_url_from_menuitem($pageId);
				}else{
					$url = 'index.php';
				}
			}elseif($config['item_save_redirect']=='url'){
				//redirect to custom url
				$url = $config['item_save_redirect_url'];
			}
			elseif($config['item_save_redirect']=='current'){
				//redirect to refer
				$referer = JRequest::getString('return', base64_encode(JURI::base()));
				$referer = base64_decode($referer);
				if (JURI::isInternal($referer))
				{
					$url = $referer;
				}
			}
			else{
				//redirect to item in full view
				$url = 'index.php?option=com_content&amp;view=article&amp;id='.$item_id.'&amp;catid='.$cat_id.'&amp;Itemid='.$pageId;
			}
		}
		else
		{
			//backend
			$categoryId = JRequest::getVar('categoryId', '' );
			
			
			//TODO item_save2new item_save2copy
			if($useCheckedOut)
			{
				//item_checkin same as apply
				//
				//
				//$task = $this->task(); 
				//JRequest::getVar('task', '', 'post');
				//$task = JRequest::getVar('task', '');
				$task = JRequest::getVar('task', '');
				
				//$sub_task = JRequest::getVar('sub_task', '', 'post');
				//$stringsub_task = $sub_task ? '&sub_task='.$sub_task;
				if($task == 'item_apply' || $task == 'save2copy')
				{
					$url = 'index.php?option=com_pagesanditems&view=item&sub_task=edit'.($pageId ? '&pageId='.$pageId : '').'&itemId='.$item_id.($categoryId ? '&categoryId='.$categoryId : ''); //.'&manager='.$manager;
				}
				elseif($task == 'item_checkin')
				{
					$url = 'index.php?option=com_pagesanditems&view=item'.($pageId ? '&pageId='.$pageId : '').'&itemId='.$item_id.($categoryId ? '&categoryId='.$categoryId : ''); //.'&manager='.$manager;
					$ContentModelArticle->checkin($item_id);
				}
				else
				{
					$url = PagesanditemsHelper::toogleViewPageCategories('index.php?option=com_pagesanditems&view=page&pageId='.$pageId.'&menutype='.$menutype.'&categoryId='.$categoryId);
					$ContentModelArticle->checkin($item_id);
				}
			}
			else
			{
				$url = PagesanditemsHelper::toogleViewPageCategories('index.php?option=com_pagesanditems&view=page&sub_task=edit&pageId='.$pageId.'&menutype='.$menutype.'&categoryId='.$categoryId);
			}
			
			//$url = 'index.php?option=com_pagesanditems&view=page&sub_task=edit&pageId='.$pageId;

			/*
			$url = 'index.php?option=com_pagesanditems&view=page&sub_task=edit&pageId='.$pageId;
			*/
		}


		if($pageId && $pageType && $item_id && $pageType == 'content_article' && $new_or_edit =='new')
		{
			$db->setQuery("SELECT link FROM #__menu WHERE id='$pageId' ");
			$menu = $db-> loadObject();
			$link = $menu->link;
			if (is_string($link))
			{
				$args = array();
				if (strpos($link, 'index.php') === 0)
				{
					parse_str(parse_url(htmlspecialchars_decode($link), PHP_URL_QUERY), $args);
				}
				else
				{
					parse_str($link, $args);
				}
				$link = $args;
			}
			foreach ($link as $name => $value)
			{
				if ($name == 'id' && $value == '')
				{
					$link[$name] = $item_id;
				}
			}

			$link = 'index.php?'.http_build_query($link,'','&');
			$db->setQuery("UPDATE #__menu SET link='$link' WHERE id='$pageId' ");
			$db->query();

		}
		else
		{

		}

		$message = JText::_('COM_PAGESANDITEMS_ITEM_SAVED');
		
		if($apply && !$useCheckedOut)
		{
			$categoryId = JRequest::getVar('categoryId', '' );
			$url = 'index.php?option=com_pagesanditems&view=item&sub_task=edit&pageId='.$pageId.'&itemId='.$item_id.'&categoryId='.$categoryId; //.'&manager='.$manager;
		}
		//$model->redirect_to_url( $url, $mesage);
		$this->setRedirect(JRoute::_($url, false), $message);
	}


	function cancel()
	{
		$model = &$this->getModel('Item','PagesAndItemsModel');
		$pageId = JRequest::getVar('pageId', 0);
		$itemId = JRequest::getVar('itemId', 0);
		$menutype = JRequest::getVar('menutype');
		$sub_task = JRequest::getVar('sub_task');
		
		$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
		if($useCheckedOut)
		{
			$pk = JRequest::getVar('itemId',0);
			$user = JFactory::getUser();

			// Get an instance of the row to checkin.
			$table = JTable::getInstance('content'); //, $prefix, $config); //'content';
			if (!$table->load($pk)) {
				//$this->setError($table->getError());
				//return false;
			}

			// Check if this is the user having previously checked out the row.
			if ($table->checked_out > 0 && $table->checked_out != $user->get('id') && !$user->authorise('core.admin', 'com_checkin')) {
				//$this->setError(JText::_('JLIB_APPLICATION_ERROR_CHECKIN_USER_MISMATCH'));
				//return false;
			}

			// Attempt to check the row in.
			if (!$table->checkin($pk)) {
				//$this->setError($table->getError());
				//return false;
			}
		}
		
		if(JRequest::getVar('edit_from_frontend', '', 'post'))
		{
			//frontend
			$catid = JRequest::getVar('cat_id', 0 );
			//$catid = JRequest::getVar('catid', 0 );
			$model = &$this->getModel('Item','PagesAndItemsModel');
			$config = PagesAndItemsHelper::getConfig();
			$pageId = JRequest::getVar('pageId', 0, 'post');
			
			if($config['item_save_redirect']=='category_blog'){
				//redirect to categroy blog
				if($pageId){
					$url = $model->get_url_from_menuitem($pageId);
				}else{
					$url = 'index.php';
				}
			}elseif($config['item_save_redirect']=='url'){
				//redirect to custom url
				$url = $config['item_save_redirect_url'];
			}
			elseif($config['item_save_redirect']=='current'){
			
				//redirect to refer
				$referer = JRequest::getString('return', base64_encode(JURI::base()));
				$referer = base64_decode($referer);
				if (JURI::isInternal($referer))
				{
					$url = $referer;
				}
				if(!$url)
				{
					$app = JFactory::getApplication();
					$app->redirect('index.php');
					//$url = 'index.php';
				}
			
			}
			else{
				//redirect to item in full view
				$url = 'index.php?option=com_content&amp;view=article&amp;id='.$itemId.'&amp;catid='.$catid.'&amp;Itemid='.$pageId;
			}
			
		}
		else
		{
			//backend
			$categoryId = JRequest::getVar('categoryId', 0 ); //need for backend
		$subTask = $useCheckedOut ? '' : '&sub_task=edit';
		if($useCheckedOut && $sub_task == 'edit')
		{
			$url = 'index.php?option=com_pagesanditems&view=item'.($pageId ? '&pageId='.$pageId : '').($itemId ? '&itemId='.$itemId : '').($categoryId ? '&categoryId='.$categoryId : '').($menutype ? '&menutype='.$menutype : '');
		}
		elseif(!$pageId && !$menutype && $categoryId)
		{
			$url = 'index.php?option=com_pagesanditems&view=category'.$subTask.'&categoryId='.$categoryId;
		}
		elseif($menutype && $pageId)
		{
			$url = 'index.php?option=com_pagesanditems&view=page'.$subTask.'&menutype='.$menutype.'&pageId='.$pageId;
		}
		elseif($menutype)
		{
			$url = "index.php?option=com_pagesanditems&view=page&layout=root&menutype=".$menutype;
		}
		else
		{
			$url = "index.php?option=com_pagesanditems&view=page&layout=root";
		}
		}
		

		
		//$model->redirect_to_url($url, JText::_('COM_PAGESANDITEMS_ACTION_CANCELED'));

		$this->setRedirect(JRoute::_($url, false), JText::_('COM_PAGESANDITEMS_ACTION_CANCELED'));


		/*
		
		*/
		/*
		if(!$pageId || !$menutype)
		{
			$model->redirect_to_url("index.php?option=com_pagesanditems&view=page&layout=root", JText::_('COM_PAGESANDITEMS_ACTION_CANCELED'));
		}
		else
		{
			$model->redirect_to_url("index.php?option=com_pagesanditems&view=page&sub_task=edit&menutype=".$menutype."&pageId=".$pageId, JText::_('COM_PAGESANDITEMS_ACTION_CANCELED'));
		}
		*/
		
		
	}

	function put_item_on_frontpage($item_id){

		$db = JFactory::getDBO();

		//do the insert
		$db->setQuery( "INSERT INTO #__content_frontpage SET content_id='$item_id', ordering='1'");
		$db->query();

		//first get the current order
		$db->setQuery("SELECT content_id, ordering FROM #__content_frontpage ORDER BY ordering ASC");
		$rows = $db-> loadObjectList();
		$counter = 2;
		foreach($rows as $row){
			//reorder to make sure all is well
			$rowContentId = $row->content_id;
			$db->setQuery( "UPDATE #__content_frontpage SET ordering='$counter' WHERE content_id='$rowContentId'"	);
			$db->query();
			$counter = $counter + 1;
		}

		//do update of article
		$db->setQuery( "UPDATE #__content SET featured='1' WHERE id='$item_id'"	);
		$db->query();

	}

	function take_item_off_frontpage($item_id){

		$db = JFactory::getDBO();
		$db->setQuery("DELETE FROM #__content_frontpage WHERE content_id='$item_id'");
		$db->query();

		//do update of article
		$db->setQuery( "UPDATE #__content SET featured='0' WHERE id='$item_id'"	);
		$db->query();
	}

	//called from item-edit page with toolbar buttons
	function state(){

		$helper = $this->get_helper();

		$item_id = JRequest::getVar('item_id');
		$menutype = JRequest::getVar('menutype');
		$sub_task = JRequest::getVar('sub_task');
		$subsub_task = JRequest::getVar('subsub_task');
		$pageType = JRequest::getVar('pageType', '', 'post');
		
		switch($subsub_task)
		{
			case 'delete':
				$message = JText::_('COM_PAGESANDITEMS_ITEM_DELETED');
				$new_state = 'delete';
			break;
			case 'trash':
			$message = JText::_('COM_PAGESANDITEMS_ITEM_TRASHED');
			$new_state = '-2';
			break;
			case 'archive':
			$message = JText::_('COM_PAGESANDITEMS_ITEM_ARCHIVED');
			$new_state = '2';
			break;
			case 'publish':
			$message = JText::_('COM_PAGESANDITEMS_ITEM_PUBLISHED');
			$new_state = '1';
			break;
			case 'unpublish':
			$message = JText::_('COM_PAGESANDITEMS_ITEM_UNPUBLISHED');
			$new_state = '0';
			break;
		}

		PagesAndItemsHelper::item_state($item_id, $new_state);

		//redirect
		$pageId = JRequest::getVar('pageId', 0);
		$categoryId = JRequest::getVar('categoryId', 0 );
		if(!$pageId && !$menutype && $categoryId)
		{
			//$url = PagesanditemsHelper::toogleViewPageCategories('index.php?option=com_pagesanditems&view=page&categoryId='.$categoryId);
			$sub_task = $sub_task ? '&sub_task='.$sub_task : '';
			//$categoryExtension = JRequest::getVar('categoryExtension', 'com_content');
			//$categoryExtension = $categoryExtension ? '&categoryExtension='.$categoryExtension : '';
			$url = 'index.php?option=com_pagesanditems&view=category'.$sub_task.'&categoryId='.$categoryId;//.$categoryExtension;
		}
		elseif(!$pageId && $menutype)
		{
			$url = 'index.php?option=com_pagesanditems&view=page&layout=root&menutype='.$menutype;
		}
		elseif(!$pageId)
		{
			$url = 'index.php?option=com_pagesanditems&view=page&layout=root';
		}
		else{
			//$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
			$sub_task = $sub_task ? '&sub_task='.$sub_task : '';
			$url = 'index.php?option=com_pagesanditems&view=page'.$sub_task.'&menutype='.$menutype.'&pageId='.$pageId.'&pageType='.$pageType;
		}
		$this->setRedirect(JRoute::_($url, $message));
	}

	function item_move_save(){
		$item_id = JRequest::getVar('item_id', '', 'post');
		$new_menu_id = JRequest::getVar('new_parent_id');
		$old_cat_id = JRequest::getVar('old_cat_id');
		$this->item_move($item_id, $new_menu_id, $old_cat_id);
	}

	function item_move($item_id, $new_menu_id, $old_cat_id){

		$db = JFactory::getDBO();

		//find category corresponding to page from where item is moving towards
		$db->setQuery("SELECT link, menutype "
		." FROM #__menu "
		." WHERE id='$new_menu_id' "
		." LIMIT 1 "
		);
		$rows = $db->loadObjectList();
		foreach($rows as $row){
			$new_cat_id = str_replace('index.php?option=com_content&view=category&layout=blog&id=','',$row->link);
			$menutype = $row->menutype;
		}

		if(!isset($new_cat_id)){
			return true;
		}

		//reorder new category to make sure moved item ends up last
		$new_order = PagesAndItemsHelper::reorderItemsCategory($new_cat_id);

		//update item
		$db->setQuery( "UPDATE #__content SET catid='$new_cat_id', ordering='$new_order' WHERE id='$item_id'"	);
		$db->query();

		//reorder old category so as to leave things nicely
		PagesAndItemsHelper::reorderItemsCategory($old_cat_id);

		$categoryId = JRequest::getVar('categoryId', 0 );
		
		//$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
		//$subTask = $useCheckedOut ? '' : '&sub_task=edit';
		$sub_task = JRequest::getVar('sub_task', '');
		$sub_task = $sub_task ? '&sub_task='.$sub_task : '';
		if($categoryId)
		{
			//
			$url = 'index.php?option=com_pagesanditems&view=item'.$sub_task.'&categoryId='.$new_cat_id.'&itemId='.$item_id;
		}
		else
		{
			//we move the article and edit so we will back to view item edit
			//
			$url = 'index.php?option=com_pagesanditems&view=item'.$sub_task.'&menutype='.$menutype.'&pageId='.$new_menu_id.'&itemId='.$item_id;
			//$url = 'index.php?option=com_pagesanditems&view=page&sub_task=edit&menutype='.$menutype.'&pageId='.$new_menu_id.'&itemId='.$item_id;
		}

		/*
		$url = 'index.php?option=com_pagesanditems&view=page&sub_task=edit&menutype='.$menutype.'&pageId='.$new_menu_id;
		*/
		$this->setRedirect(JRoute::_($url, false), JText::_('COM_PAGESANDITEMS_ITEM_MOVED'));

	}

	//called from small buttons on category blog pages under 'Items on this page'
	function items_state(){

		$helper = $this->get_helper();

		$pageId = JRequest::getVar('pageId', '', 'post');
		$menutype = JRequest::getVar('menutype', '', 'post');
		$cid = JRequest::getVar('itemCid', array(), 'post', 'array');
		$pageType = JRequest::getVar('pageType', '', 'post');
		
		$sub_task = JRequest::getVar('sub_task');
		$subsub_task = JRequest::getVar('subsub_task');
		
		if($subsub_task=='delete'){
			$message = JText::_('COM_PAGESANDITEMS_ITEMS_DELETED');
			$new_state = 'delete';
		}elseif($subsub_task=='trash'){
			$message = JText::_('COM_PAGESANDITEMS_ITEMS_TRASHED');
			$new_state = '-2';
		}elseif($subsub_task=='archive'){
			$message = JText::_('COM_PAGESANDITEMS_ITEMS_ARCHIVED');
			$new_state = '2';
		}elseif($subsub_task=='publish'){
			$message = JText::_('COM_PAGESANDITEMS_ITEMS_PUBLISHED');
			$new_state = '1';
		}elseif($subsub_task=='unpublish'){
			$message = JText::_('COM_PAGESANDITEMS_ITEMS_UNPUBLISHED');
			$new_state = '0';
		}

		if(count($cid)){
			//there are items
			foreach($cid as $item_id){
				//delete items
				PagesAndItemsHelper::item_state($item_id, $new_state);
				/*
				we can set another way for message
				make sure $app is defined
				like $app->enqueueMessage($message.' Item Id = '.$item_id);
				*/
			}
			//$message = '';
		}else{
			//no items
			$message = JText::_('COM_PAGESANDITEMS_NO_ITEMS_SELECTED');
		}

		$categoryId = JRequest::getVar('categoryId', 0 );
		$sub_task = $sub_task ? '&sub_task='.$sub_task : '';
		if($categoryId)
		{
			//$categoryExtension = JRequest::getVar('categoryExtension', 'com_content');
			//$categoryExtension = $categoryExtension ? '&categoryExtension='.$categoryExtension : '';
			$url = 'index.php?option=com_pagesanditems&view=category'.$sub_task.'&categoryId='.$categoryId;//.$categoryExtension;
		}
		else
		{
			$url = 'index.php?option=com_pagesanditems&view=page'.$sub_task.'&menutype='.$menutype.'&pageId='.$pageId.'&pageType='.$pageType;
		}
		$this->setRedirect(JRoute::_($url, false), $message);

	}

	function get_helper(){
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'pagesanditems.php');
		$helper = new PagesAndItemsHelper();
		return $helper;
	}

	function clean_rules($rules_array){
		$rules_string = '';
		$return = '[]';
		$first = 1;
		for($n = 0; $n < count($rules_array); $n++){
			$row = each($rules_array);
			$key = $row['key'];
			$value = $row['value'];

			if($value=='1' || $value=='0'){
				if(!$first){
					$rules_string .= ',';
				}
				$rules_string .= '"'.$key.'":'.$value;
				$first = 0;
			}
		}
		if($rules_string!=''){
			$return = '{'.$rules_string.'}';
		}
		return $return;
	}
}
?>