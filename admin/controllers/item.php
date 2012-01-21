<?php
/**
* @version		2.0.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2011 Carsten Engel. All rights reserved.
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
	}

	function item_new()
	{
		$model = &$this->getModel('Item','PagesAndItemsModel');
		$config = $model->getConfig();
		$pageId = JRequest::getVar('pageId',0);
		$item_type = JRequest::getVar('item_type', '', 'post');
		$item_id = JRequest::getVar('item_id', '', 'post');
		$url = 'index.php?option=com_pagesanditems&view=item&sub_task=new&pageId='.$pageId.'&item_type='.$item_type;
		$model->redirect_to_url( $url);
	}

	function item_save(){

		// Check for request forgeries 
		JRequest::checkToken() or jexit('Invalid Token');

		$database = JFactory::getDBO();
		$app = JFactory::getApplication();

		//here we need the model page or item
		$model = &$this->getModel('Item','PagesAndItemsModel');
		$config = $model->getConfig();

		$item_id = JRequest::getVar('id', '', 'post');
		$item_type = JRequest::getVar('item_type', '', 'post');
		$show_title_item = intval(JRequest::getVar('show_title_item'));

		//get new or edit
		$new_or_edit = 'edit';
		if(!$item_id){
			$new_or_edit = 'new';
		}		
		
		//get data
		$data = JRequest::getVar('jform', array(), 'post', 'array');		
		
		//get category_id
		$cat_id = 0;
		$database->setQuery("SELECT catid "
		." FROM #__content "
		." WHERE id='$item_id' "
		." LIMIT 1 "
		);
		$rows = $database->loadObjectList();
		foreach($rows as $row){
			$cat_id = $row->catid;
		}		

		$canDo_delete = 0;					
		if($new_or_edit=='edit'){
			//get Joomla ACL for this article
			//include com_content helper
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'content.php');
			$ContentHelper = new ContentHelper;
			$canDo = ContentHelper::getActions($cat_id, $item_id);

			if(!$canDo->get('core.edit')){
				echo JText::_('COM_PAGESANDITEMS_NO_PERMISSION_TO_EDIT_THIS_ITEM');
				exit;
			}

			if($canDo->get('core.delete')){
				$canDo_delete = 1;
			}
		}

		//PI ACL
		if(!$item_id){
			//new item
			$this->helper->die_when_no_permission('3');
		}else{
			//edit item
			$this->helper->die_when_no_permission('4');
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
			$alias = $model->make_alias($data['title']);
		}else{
			$alias = $model->make_alias($alias);
		}
		$alias = addslashes($alias);

		//make alias unique
		if($config['make_article_alias_unique'])
		{
			$alias = $model->make_alias_unique($alias, 'content', $item_id);
		}
		$data['alias'] = $alias;

		//take 'featured' out of array as com_contents model chokes on that on line 223
		//after the article is saved we do the feature-stuff
		$featured = $data['featured'];
		unset($data['featured']);


		//ms: remove next 7 lines see lines 166-168
		/*if($new_or_edit=='new'){
			//make array or article-id's to find the id of the new article after the insert
			//else there is no way to get the item_id after com_content has saved the article, as this function only returns 'true'.
			//crazy workarounds!
			$this->helper->db->setQuery( "SELECT id FROM #__content ");
			$article_ids_array_old = $this->helper->db->loadResultArray();
		}*/


		//get the com_content model (controller?) and save the article
		//then update later if the article is a CCK
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.php');
		$ContentModelArticle = new ContentModelArticle();
		$ContentModelArticle->save($data);




		if($new_or_edit=='new'){			

			$article = $ContentModelArticle->getItem();
			$item_id = $article->id;
			
			//get cat_id
			$cat_id = $data['catid'];			
			
			//get the order of the last article in the category
			if($cat_id){
				$database->setQuery("SELECT ordering FROM #__content WHERE catid='$cat_id' ORDER BY ordering DESC LIMIT 1 ");
				$rows = $database->loadObjectList();
				$new_order = 0;
				foreach($rows as $row){
					$new_order = $row->ordering+1;					
				}				
				
				$database->setQuery( "UPDATE #__content SET ordering='$new_order' WHERE id='$item_id' ");
				$database->query();
			}

			//insert new item in item index
			$database->setQuery( "INSERT INTO #__pi_item_index SET item_id='$item_id', itemtype='$item_type', show_title='$show_title_item'");
			$database->query();
		}

		if($new_or_edit=='edit'){

			//check if item has a entry in item-index (if so, its been created or editted with PI before) if not, make a new index row
			$database->setQuery("SELECT id FROM #__pi_item_index WHERE item_id='$item_id' LIMIT 1");
			$rows = $database->loadObjectList();
			$id = 0;
			foreach($rows as $row){
				$id = $row->id;
			}
			if(!$id){
				$database->setQuery( "INSERT INTO #__pi_item_index SET item_id='$item_id', itemtype='$item_type', show_title='$show_title_item'");
				$database->query();
			}else{
				$database->setQuery( "UPDATE #__pi_item_index SET itemtype='$item_type', show_title='$show_title_item' WHERE id='$id' ");
				$database->query();
			}

			//'featured' stuff
			$database->setQuery("SELECT content_id FROM #__content_frontpage WHERE content_id='$item_id' LIMIT 1");
			$rows_frontpage = $database->loadObjectList();
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
		if($app->isAdmin()){
			//just making this json does not take the empties out!!
			$rules = $data['rules'];
			$rules_delete = $this->clean_rules($rules['core.delete']);
			$rules_edit = $this->clean_rules($rules['core.edit']);
			$rules_edit_state = $this->clean_rules($rules['core.edit.state']);
			$rules_string = '{"core.delete":'.$rules_delete.',"core.edit":'.$rules_edit.',"core.edit.state":'.$rules_edit_state.'}';
			$asset_name = 'com_content.article.'.$item_id;
			$database->setQuery( "UPDATE #__assets SET rules='$rules_string' WHERE name='$asset_name' ");
			$database->query();
		}


		$path = realpath(dirname(__FILE__).DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'itemtypehelper.php');
		if(strpos($item_type, 'ustom_'))
		{
			/*
				here we will load the itemtype custom
			*/
			$itemtype = ExtensionItemtypeHelper::importExtension(null, 'custom',true,null,true);
		}
		else
		{
			/*
				here we will load all the other 
				content, text, html and other_item are integrated
				also itemtype there are in old PI called plugin (plugins/pages_and_items/itemtypes)
			*/
			$itemtype = ExtensionItemtypeHelper::importExtension(null, $item_type,true,null,true);
		}
		$dispatcher = &JDispatcher::getInstance();


		//if delete check for right and delete
		$delete_item = intval(JRequest::getVar('delete_item',null));
		if($delete_item && $canDo_delete){
			//ms: comment all trigger are in the helper
			$this->helper->item_state($item_id, 'delete');
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
		$pageType = JRequest::getVar('pageType', null, 'post');

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
			$url = 'index.php?option=com_pagesanditems&view=page&sub_task=edit&pageId='.$pageId;
		}


		if($pageId && $pageType && $item_id && $pageType == 'content_article' && $new_or_edit =='new')
		{
			$model->db->setQuery("SELECT link FROM #__menu WHERE id='$pageId' ");
			$menu = $model->db-> loadObject();
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
			$model->db->setQuery("UPDATE #__menu SET link='$link' WHERE id='$pageId' ");
			$model->db->query();

		}
		else
		{

		}

		$mesage = JText::_('COM_PAGESANDITEMS_ITEM_SAVED');
		$apply = JRequest::getVar('item_apply', '', 'post');
		if($apply)
		{
			$url = 'index.php?option=com_pagesanditems&view=item&sub_task=edit&pageId='.$pageId.'&itemId='.$item_id;
		}
		$model->redirect_to_url( $url, $mesage);
	}


	function cancel()
	{
		$model = &$this->getModel('Item','PagesAndItemsModel');
		$pageId = JRequest::getVar('pageId', 0);
		if(!$pageId)
		{
			$model->redirect_to_url("index.php?option=com_pagesanditems&view=page&layout=root", JText::_('COM_PAGESANDITEMS_ACTION_CANCELED'));
		}
		else
		{
			$model->redirect_to_url("index.php?option=com_pagesanditems&view=page&sub_task=edit&pageId=".$pageId, JText::_('COM_PAGESANDITEMS_ACTION_CANCELED'));
		}
	}

	function put_item_on_frontpage($item_id){

		$database = JFactory::getDBO();

		//do the insert
		$database->setQuery( "INSERT INTO #__content_frontpage SET content_id='$item_id', ordering='1'");
		$database->query();

		//first get the current order
		$database->setQuery("SELECT content_id, ordering FROM #__content_frontpage ORDER BY ordering ASC");
		$rows = $database-> loadObjectList();
		$counter = 2;
		foreach($rows as $row){
			//reorder to make sure all is well
			$rowContentId = $row->content_id;
			$database->setQuery( "UPDATE #__content_frontpage SET ordering='$counter' WHERE content_id='$rowContentId'"	);
			$database->query();
			$counter = $counter + 1;
		}

		//do update of article
		$database->setQuery( "UPDATE #__content SET featured='1' WHERE id='$item_id'"	);
		$database->query();

	}

	function take_item_off_frontpage($item_id){

		$database = JFactory::getDBO();
		$database->setQuery("DELETE FROM #__content_frontpage WHERE content_id='$item_id'");
		$database->query();

		//do update of article
		$database->setQuery( "UPDATE #__content SET featured='0' WHERE id='$item_id'"	);
		$database->query();
	}

	//called from item-edit page with toolbar buttons
	function state(){

		$helper = $this->get_helper();

		$item_id = JRequest::getVar('item_id');
		$menutype = JRequest::getVar('menutype');
		$sub_task = JRequest::getVar('sub_task');

		switch($sub_task)
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

		$helper->item_state($item_id, $new_state);

		//redirect
		$page_id = JRequest::getVar('page_id', 0);
		if(!$page_id){
			$url = 'index.php?option=com_pagesanditems&view=page&layout=root';
		}else{
			$url = 'index.php?option=com_pagesanditems&view=page&sub_task=edit&menutype='.$menutype.'&pageId='.$page_id;
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

		$database = JFactory::getDBO();

		//find category corresponding to page from where item is moving towards
		$database->setQuery("SELECT link, menutype "
		." FROM #__menu "
		." WHERE id='$new_menu_id' "
		." LIMIT 1 "
		);
		$rows = $database->loadObjectList();
		foreach($rows as $row){
			$new_cat_id = str_replace('index.php?option=com_content&view=category&layout=blog&id=','',$row->link);
			$menutype = $row->menutype;
		}

		if(!isset($new_cat_id)){
			return true;
		}

		//reorder new category to make sure moved item ends up last
		$new_order = $this->helper->reorderItemsCategory($new_cat_id);

		//update item
		$database->setQuery( "UPDATE #__content SET catid='$new_cat_id', ordering='$new_order' WHERE id='$item_id'"	);
		$database->query();

		//reorder old category so as to leave things nicely
		$this->helper->reorderItemsCategory($old_cat_id);

		$url = 'index.php?option=com_pagesanditems&view=page&sub_task=edit&menutype='.$menutype.'&pageId='.$new_menu_id;
		$this->setRedirect(JRoute::_($url, false), JText::_('COM_PAGESANDITEMS_ITEM_MOVED'));

	}

	//called from small buttons on category blog pages under 'Items on this page'
	function items_state(){

		$helper = $this->get_helper();

		$pageId = JRequest::getVar('pageId', '', 'post');
		$menutype = JRequest::getVar('menutype', '', 'post');
		$cid = JRequest::getVar('itemCid', array(), 'post', 'array');

		$sub_task = JRequest::getVar('sub_task');

		if($sub_task=='delete'){
			$message = JText::_('COM_PAGESANDITEMS_ITEMS_DELETED');
			$new_state = 'delete';
		}elseif($sub_task=='trash'){
			$message = JText::_('COM_PAGESANDITEMS_ITEMS_TRASHED');
			$new_state = '-2';
		}elseif($sub_task=='archive'){
			$message = JText::_('COM_PAGESANDITEMS_ITEMS_ARCHIVED');
			$new_state = '2';
		}elseif($sub_task=='publish'){
			$message = JText::_('COM_PAGESANDITEMS_ITEMS_PUBLISHED');
			$new_state = '1';
		}elseif($sub_task=='unpublish'){
			$message = JText::_('COM_PAGESANDITEMS_ITEMS_UNPUBLISHED');
			$new_state = '0';
		}

		if(count($cid)){
			//there are items
			foreach($cid as $item_id){
				//delete items
				$helper->item_state($item_id, $new_state);
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

		$url = 'index.php?option=com_pagesanditems&view=page&sub_task=edit&menutype='.$menutype.'&pageId='.$pageId;
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