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

jimport( 'joomla.application.component.model' );
require_once(dirname(__FILE__).'/base.php');
/**

 */
 

class PagesAndItemsModelPage extends PagesAndItemsModelBase
{
	var $_menutypes = array();
	var $_menuitems;
	var $_itemtypes  = array();
	var $_currentMenutype = null;
	var $_currentPageId = null;
	var $_allMenuItems;
	

	var $pageId = null;
	var $pageMenuItem = null;
	var $menu_item = 0;
	var $menuItemsTypes = null;
	var $menuItemsType = 0;
	var $lists = null;
	var $pageType = null;
	var $currentMenuitems = null;

	var $canDo = null;
	public $form;
	public $item;
	public $modules;
	public $state;

	public $view;
	/**
	 * Overridden constructor
	 * @access	protected
	 */
	function __construct()
	{
		parent::__construct();

		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models'.DS.'item.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'menutypes.php');
		$modelMenutypes = new PagesAndItemsModelMenutypes();
		$this->menuItemsTypes = $modelMenutypes->getTypeListComponents();

	}

	function setView($view)
	{
		$this->view = $view;
	}
	
		
	//section update for menuitems and all underlying menuitems and their items if category blog page is moved
	function section_update_page($section_update_menu_id, $new_section_id)
	{
		//check if menuitem is content-category-blog, and if so, get cat_id
		$config = $this->getConfig();
		$content_category_blog = false;

		$this->db->setQuery("SELECT link, type FROM #__menu WHERE id='$section_update_menu_id' LIMIT 1");
		$rows = $this->db->loadObjectList();
		$row = $rows[0];
		if((strstr($row->link, 'index.php?option=com_content&view=category&layout=blog') && $row->type!='url')){
			$content_category_blog = true;
			$cat_id = str_replace('index.php?option=com_content&view=category&layout=blog&id=','',$row->link);
		}
	
		//only update items on page when its a content_blog_category
		if($content_category_blog)
		{
			$this->update_items_category($cat_id, $new_section_id);
		}
		
		//update category		
		$this->db->setQuery( "UPDATE #__categories SET section='$new_section_id' WHERE id='$cat_id'");
		$this->db->query();
		
		//update all underlying child-pages
		if($config['child_inherit_from_parent_move'])
		{
			$this->section_update_children($section_update_menu_id, $new_section_id);
		}
	}	
		
		
	//update section id for underlying pages and all items on them	
	function section_update_children($section_update_page_id, $new_section_id)
	{
		//in J1.6 we have no section and the field parent is renamed to parent_id
		//$this->db->setQuery("SELECT id FROM #__menu WHERE parent='$section_update_page_id'"  );
		if($this->joomlaVersion < '1.6')
		{
			$this->db->setQuery("SELECT id FROM #__menu WHERE parent='$section_update_page_id'" );
		}
		else
		{
			$this->db->setQuery("SELECT id FROM #__menu WHERE parent_id='$section_update_page_id'" );
		}
		$rows = $this->db->loadObjectList();
		foreach($rows as $row)
		{
			$this->section_update_page($row->id, $new_section_id);
		}
	}



	function change_menutype_check_children($page_id, $new_menutype)
	{
		//in J1.6 we have no section and the field parent is renamed to parent_id
		//$this->db->setQuery("SELECT id FROM #__menu WHERE parent='$page_id'"  );
		if($this->joomlaVersion < '1.6')
		{
			$this->db->setQuery("SELECT id FROM #__menu WHERE parent='$page_d'" );
		}
		else
		{
			$this->db->setQuery("SELECT id FROM #__menu WHERE parent_id='$page_id'" );
		}
		
		$rows = $this->db-> loadObjectList();
	
		foreach($rows as $row)
		{
			$this->change_menutype($row->id, $new_menutype);
		}
	}

	function change_menutype($page_id, $new_menutype)
	{
		$this->db->setQuery( "UPDATE #__menu SET menutype='$new_menutype' WHERE id='$page_id'");
		if (!$this->db->query()) 
		{
			echo "<script> alert('".$this->db->getErrorMsg()."'); window.history.go(-1); </script>";
			exit();
		}
		
		$this->change_menutype_check_children($page_id, $new_menutype);
	}
	
	function get_sublevel_add_one($menu_id)
	{
		$sublevel = 0;
		$menuitems = $this->getMenuitems();
		foreach($menuitems as $menuitem)
		{
			if($menuitem->id==$menu_id){
				$sublevel = $menuitem->sublevel+1;
				break;
			}
		}
	
	return $sublevel;
	}
/*
*******************
* date
******************
*/

	function get_date_now($with_offset,$format = false)
	{
		require_once(dirname(__FILE__).DS.'..'.DS.'helpers'.DS.'pagesanditems.php');
		return PagesAndItemsHelper::get_date_now($with_offset,$format);
		//get date and time
		/*
		COMMENT joomla 1.6 use another date function
		we can use:
		if($this->joomlaVersion < '1.6')
		{
			//joomla 1.5.x
			
		}
		elseif($this->joomlaVersion == '1.6')
		{
			//joomla 1.6.x
		}
		else
		{
			//
		}
		
		*/
		$JApp =& JFactory::getApplication();
		$JDate = JFactory::getDate();
		jimport('joomla.utilities.date');
		if($with_offset)
		{
			$offset = $JApp->getCfg('offset');
			$summertime = date( 'I' );
			if($summertime)
			{
				$offset = $offset +1;
			}
			//$JDate->setOffset($JApp->getCfg('offset'));
			$JDate->setOffset($offset);
		}
		$date_now = $JDate->toFormat();

		return $date_now;
	}
	
	function get_date_to_format($date,$format = false)
	{
		require_once(dirname(__FILE__).DS.'..'.DS.'helpers'.DS.'pagesanditems.php');
		return PagesAndItemsHelper::get_date_to_format($date,$format);
		/*
		COMMENT joomla 1.6 use another date function
		we can use:
		if($this->joomlaVersion < '1.6')
		{
			//joomla 1.5.x
			
		}
		elseif($this->joomlaVersion == '1.6')
		{
			//joomla 1.6.x
		}
		else
		{
			//
		}
		
		*/
		$JApp =& JFactory::getApplication();
		$date = JFactory::getDate($date);
		
		$offset = $JApp->getCfg('offset');
		$summertime = date( 'I' );
		if($summertime)
		{
			$offset = $offset +1;
		}
		$date->setOffset($offset);
		//$date->setOffset($JApp->getCfg('offset'));
		$date = $date->toFormat();
		return $date;
	}
	
	function get_date_ready_for_database($date)
	{
		require_once(dirname(__FILE__).DS.'..'.DS.'helpers'.DS.'pagesanditems.php');
		return PagesAndItemsHelper::get_date_ready_for_database($date);
		/*
		COMMENT joomla 1.6 use another date function
		we can use:
		if($this->joomlaVersion < '1.6')
		{
			//joomla 1.5.x
			
		}
		elseif($this->joomlaVersion == '1.6')
		{
			//joomla 1.6.x
		}
		else
		{
			//
		}
		
		*/
			//joomla 1.5
			
			/*
			//Joomla object does not seem to be able to convert this to the correct format ?
			$config =& JFactory::getConfig();
			$tzoffset = $config->getValue('config.offset');
			$date =& JFactory::getDate($date, $tzoffset);
			$JApp =& JFactory::getApplication();
			$offset = $JApp->getCfg('offset');
			$summertime = date( 'I' );
			if($summertime){
				$offset = $offset +1;
			}
			$date = JFactory::getDate($date, $offset);
			$date = JFactory::getDate($date, 0);
			$date = $date->toFormat("%Y %m %d %H:%M:%S");
			$date = $date->toMySQL();
			*/
			
			//get date format
			$config = $this->getConfig();
			$dateformat = $config['date_format'];
			$dateformat_array = explode('-', $dateformat);
			$date_array = explode('-', $date);
						
			$year = 0;
			$month = 0;
			$day = 0;
			for($n = 0; $n < 3; $n++)
			{
				$dateformat_row = strtolower($dateformat_array[$n]);
				if(strpos($dateformat_row, 'y')){
					$year = intval($date_array[$n]);
				}
				if(strpos($dateformat_row, 'm')){
					$month = intval($date_array[$n]);
				}
				if(strpos($dateformat_row, 'd')){
					$day = intval($date_array[$n]);
				}
			}
			$timestamp = mktime(0,0,0,$month,$day,$year);
			$date = date("Y-m-d 00:00:00", $timestamp);

		return $date;
	}

	function make_alias($alias)
	{
		$alias = str_replace("'",'',$alias);
		$alias = str_replace('"','',$alias);
		$alias = JFilterOutput::stringURLSafe($alias);
		return $alias;
	}
	
	function make_alias_unique($alias, $tablename, $exclude_id){
		
		//get aliasses, except for the current alias-row
		$where = '';
		if($exclude_id)
		{
			$where = "WHERE id<>$exclude_id ";
		}
		$this->db->setQuery("SELECT alias "
		."FROM #__$tablename "
		.$where
		);
		$rows = $this->db->loadObjectList();
		$aliasses = array();
		foreach($rows as $row){
			$aliasses[] = $row->alias;
		}
		
		if(in_array($alias, $aliasses)){
			$j = 2;
			while (in_array($alias."-".$j, $aliasses)){
				$j = $j + 1;
			}
			$alias = $alias."-".$j;
		}
		
		return $alias;
	}
	
	/*
	function acesef_item($acesef_url_id, $page_id, $cat_id, $item_id, $title, $metadesc, $metakey, $alias){
	
		
		//only if acesef is installed
		if(!file_exists(dirname(__FILE__).'/../com_acesef/controller.php'))
		{
			return true;
		}
		$config = $model->getConfig();
		//category stuff
		$cat_alias = '';
		if($this->_config['sef_url_cat']){
			$this->db->setQuery("SELECT alias "
			."FROM #__categories "
			."WHERE id='$cat_id' "
			."LIMIT 1 "
			);
			$rows = $this->db->loadObjectList();
			foreach($rows as $row){
				$cat_alias = $row->alias;
			}
		}
		
		//item id stuff
		$item_id_sef = '';
		if($this->_config['sef_url_id']){
			$item_id_sef = $item_id.'-';
		}
		
		//suffix stuff
		$suffix = '';
		if($this->_config['sef_url_ext']){
			$suffix = $config['sef_url_ext'];
		}
				
		$url_sef = $cat_alias.'/'.$item_id_sef.$alias.$suffix;
		$url_real = 'index.php?option=com_content&Itemid='.$page_id.'&catid='.$cat_id.'&id='.$item_id.'&view=article';
		$acesef_metatitle = JRequest::getVar('acesef_metatitle', '', 'post');
		$acesef_metatitle = addslashes($acesef_metatitle);
		if($acesef_metatitle==''){
			$acesef_metatitle = $title;
		}
			
		if(!$acesef_url_id){
			//insert the url
			$this->db->setQuery( "INSERT INTO #__acesef_urls SET url_sef='$url_sef', url_real='$url_real', metatitle='$acesef_metatitle', metadesc='$metadesc', metakey='$metakey', published='1' ");
			$this->db->query();
		}else{
			//update the url
			$this->db->setQuery( "UPDATE #__acesef_urls SET url_sef='$url_sef', url_real='$url_real', metatitle='$acesef_metatitle', metadesc='$metadesc', metakey='$metakey' WHERE id='$acesef_url_id'");
			$this->db->query();
		}
	}
	*/


	//copied this function to helper
	//to do find every call to this function and make it go to helper
	function delete_plugin_items($item_id, $item_type)
	{
	
		/*
		over dispatcher
		*/
		$path = realpath(dirname(__FILE__).DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'itemtypehelper.php');
		if(strpos($item_type, 'ustom_'))
		{
			$item_type = 'custom';
		}
		ExtensionItemtypeHelper::importExtension(null, $item_type,true,null,true);
		$dispatcher = &JDispatcher::getInstance();
		$dispatcher->trigger('item_delete',array($item_id));
		
		

		/*
		if(file_exists($this->pathPluginsItemtypes.'/'.$item_type.'/'.$item_type.'.php'))
		{
			$class_name = 'classItemtypeController'.$item_type;
			if(!class_exists($class_name))
			{
				require_once($this->pathPluginsItemtypes.'/'.$item_type.'/'.$item_type.'.php');
			}
			$class_itemtype = new $class_name();
			//check if function 'item_delete' is there, if so, do it
			if(method_exists($class_itemtype,'item_delete'))
			{
				$class_itemtype->item_delete($item_id);
			}
		}
		*/
	}

	//copied this function to helper
	//to do find every call to this function and make it go to helper
	function take_item_off_frontpage($item_id)
	{
		$this->db->setQuery("DELETE FROM #__content_frontpage WHERE content_id='$item_id'");
		if (!$this->db->query())
		{
			echo "<script> alert('"
			.$this->db-> getErrorMsg()
			."'); window.history.go(-1); </script>";
		}
		
		
		$this->db->setQuery( "UPDATE #__content SET featured='0' WHERE id='$item_id' ");
		$this->db->query();
	}
	
	//copied this function to helper
	//to do find every call to this function and make it go to helper
	//trash all items on a page (category)
	function trashItemsCategory($trashCatId)
	{
		//get content id's which are on frontpage
		$this->db->setQuery("SELECT content_id FROM #__content_frontpage");
		$frontpage_items = $this->db->loadResultArray();
		
		//get content-index to know which item has whichitemtype
		$this->db->setQuery("SELECT item_id, itemtype FROM #__pi_item_index");
		$index_items = $this->db->loadObjectList();
		
		//trash all items in the category
		$this->db->setQuery("SELECT id FROM #__content WHERE catid='$trashCatId'" );
		$rows = $this->db->loadObjectList();
		/*
		we need to trigger
		*/
		$path = realpath(dirname(__FILE__).DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'itemtypehelper.php');
		ExtensionItemtypeHelper::importExtension(null, 'other_item',true,null,true);
		$dispatcher = &JDispatcher::getInstance();

		foreach($rows as $row)
		{
			$item_id = $row->id;
			$this->db->setQuery( "UPDATE #__content SET state='-2' WHERE id='$item_id'");
			if (!$this->db->query()) {
				echo "<script> alert('".$this->db->getErrorMsg()."'); window.history.go(-1); </script>";
				exit();
			}
			
			//if item was on frontpage, take it off
			if(in_array($item_id, $frontpage_items))
			{
				$this->take_item_off_frontpage($item_id);
			}
			
			//if item was plugin, delete sub-item rows etc.
			foreach($index_items as $index_item)
			{
				if($item_id==$index_item->item_id && $index_item->itemtype!='text' && $index_item->itemtype!='html' && $index_item->itemtype!='other_item')
				{
					$this->delete_plugin_items($item_id, $index_item->itemtype);
				}
			}
			
			//if item had duplicate-items trash those as well
			$this->db->setQuery("SELECT item_id FROM #__pi_item_other_index WHERE other_item_id='$item_id' ");
			$other_items = $this->db->loadObjectList();
			foreach($other_items as $other_item)
			{
				//update_duplicate_item is in models/page.php but i think it must move to extensions/itemtypes/other_item.php
				//$this->update_duplicate_item($other_item->item_id, $item_id);
				//TODO
				$dispatcher->trigger('update_duplicate_item',array($other_item->item_id, $item_id));
			}
			
			//if item was of itemtype other-item disconnect it from original item by deleting the row in the ohter-item-index
			if($index_item->itemtype=='other_item')
			{
				//delete_other_item_entry is in models/page.php but i think it must move to extensions/itemtypes/other_item.php
				//$this->delete_other_item_entry($item_id);
				//TODO
				$dispatcher->trigger('delete_other_item_entry',array($item_id));
			}
		}
	}
	
	//copied this function to helper
	//to do find every call to this function and make it go to helper
	//trash page and all items on page
	function trashPage($trashPageId)
	{
		//check if menuitem is content-category-blog, and if so, get cat_id
		$content_category_blog = false;
		$this->db->setQuery("SELECT link, type FROM #__menu WHERE id='$trashPageId' LIMIT 1");
		$rows = $this->db->loadObjectList();
		$row = $rows[0];
		if((strstr($row->link, 'index.php?option=com_content&view=category&layout=blog') && $row->type!='url'))
		{
			$content_category_blog = true;
			$cat_id = str_replace('index.php?option=com_content&view=category&layout=blog&id=','',$row->link);
		}
		
		//trash mainmenuitem
		$this->db->setQuery( "UPDATE #__menu SET published='-2' WHERE id='$trashPageId'");
		if (!$this->db->query()) {
			echo "<script> alert('".$this->db->getErrorMsg()."'); window.history.go(-1); </script>";
			exit();
		}
		
		//only trash items on page when its a content_blog_category
		if($content_category_blog)
		{
			//trash all items on the page (category)
			$this->trashItemsCategory($cat_id);
		}
		
		//delete category
		/*
		$query = "DELETE FROM #__categories WHERE id='$cat_id'";
		$this->db->setQuery( $query );
		$this->db->query();
		*/
		//trash all underlying child-pages
		$this->trashPageChildren($trashPageId);
		
		//clean item-index
		$this->keep_item_index_clean();
	}
		
		
	//copied this function to helper
	//to do find every call to this function and make it go to helper
	function trashPageChildren($trashPageId)
	{
		//in J1.6 we have no section and the field parent is renamed to parent_id
		//$this->db->setQuery("SELECT id FROM #__menu WHERE parent='$trashPageId'" );
		if($this->joomlaVersion < '1.6')
		{
			$this->db->setQuery("SELECT id FROM #__menu WHERE parent='$trashPageId'" );
		}
		else
		{
			$this->db->setQuery("SELECT id FROM #__menu WHERE parent_id='$trashPageId'" );
		}
		
		$rows = $this->db-> loadObjectList();
		foreach($rows as $row)
		{
			$this->trashPage($row->id);
		}
	}


/*
pageDelete
*/
	//delete all items on a page (category)
	function deleteItemsCategory($deleteCatId)
	{
		//get content id's which are on frontpage
		$this->db->setQuery("SELECT content_id FROM #__content_frontpage");
		$frontpage_items = $this->db->loadResultArray();
		
		//get content-index to know which item has whichitemtype
		$this->db->setQuery("SELECT item_id, itemtype FROM #__pi_item_index");
		$index_items = $this->db->loadObjectList();
		
		//delete all items in the category
		$this->db->setQuery("SELECT id FROM #__content WHERE catid='$deleteCatId'" );
		$rows = $this->db->loadObjectList();
		/*
		we need to trigger
		*/
		$path = realpath(dirname(__FILE__).DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'itemtypehelper.php');
		ExtensionItemtypeHelper::importExtension(null, 'other_item',true,null,true);
		$dispatcher = &JDispatcher::getInstance();

		foreach($rows as $row)
		{
			$item_id = $row->id;
			$this->db->setQuery( "UPDATE #__content SET state='-2' WHERE id='$item_id'");
			if (!$this->db->query()) {
				echo "<script> alert('".$this->db->getErrorMsg()."'); window.history.go(-1); </script>";
				exit();
			}
			
			//if item was on frontpage, take it off
			if(in_array($item_id, $frontpage_items))
			{
				$this->take_item_off_frontpage($item_id);
			}
			
			//if item was plugin, delete sub-item rows etc.
			foreach($index_items as $index_item)
			{
				if($item_id==$index_item->item_id && $index_item->itemtype!='text' && $index_item->itemtype!='html' && $index_item->itemtype!='other_item')
				{
					$this->delete_plugin_items($item_id, $index_item->itemtype);
				}
			}
			
			//if item had duplicate-items delete those as well
			$this->db->setQuery("SELECT item_id FROM #__pi_item_other_index WHERE other_item_id='$item_id' ");
			$other_items = $this->db->loadObjectList();
			foreach($other_items as $other_item)
			{
				//update_duplicate_item is in models/page.php but i think it must move to extensions/itemtypes/other_item.php
				//$this->update_duplicate_item($other_item->item_id, $item_id);
				//TODO
				$dispatcher->trigger('update_duplicate_item',array($other_item->item_id, $item_id));
			}
			
			//if item was of itemtype other-item disconnect it from original item by deleting the row in the ohter-item-index
			if($index_item->itemtype=='other_item')
			{
				//delete_other_item_entry is in models/page.php but i think it must move to extensions/itemtypes/other_item.php
				//$this->delete_other_item_entry($item_id);
				//TODO
				$dispatcher->trigger('delete_other_item_entry',array($item_id));
			}
		}
	}
	
	//Comment MS move this to page controller||helper||view.html.php?
	//delete page and all items on page
	function deletePage($deletePageId)
	{
		//check if menuitem is content-category-blog, and if so, get cat_id
		$content_category_blog = false;
		$this->db->setQuery("SELECT link, type FROM #__menu WHERE id='$deletePageId' LIMIT 1");
		$rows = $this->db->loadObjectList();
		$row = $rows[0];
		if((strstr($row->link, 'index.php?option=com_content&view=category&layout=blog') && $row->type!='url'))
		{
			$content_category_blog = true;
			$cat_id = str_replace('index.php?option=com_content&view=category&layout=blog&id=','',$row->link);
		}
		
		//delete mainmenuitem
		$this->db->setQuery( "UPDATE #__menu SET published='-2' WHERE id='$deletePageId'");
		if (!$this->db->query()) {
			echo "<script> alert('".$this->db->getErrorMsg()."'); window.history.go(-1); </script>";
			exit();
		}
		
		//only delete items on page when its a content_blog_category
		if($content_category_blog)
		{
			//delete all items on the page (category)
			$this->deleteItemsCategory($cat_id);
		}
		
		//delete category
		$query = "DELETE FROM #__categories WHERE id='$cat_id'";
		$this->db->setQuery( $query );
		$this->db->query();
		
		//delete all underlying child-pages
		$this->deletePageChildren($deletePageId);
		
		//clean item-index
		$this->keep_item_index_clean();
	}
		
	function deletePageChildren($deletePageId)
	{
		//in J1.6 we have no section and the field parent is renamed to parent_id
		if($this->joomlaVersion < '1.6')
		{
			$this->db->setQuery("SELECT id FROM #__menu WHERE parent='$deletePageId'" );
		}
		else
		{
			$this->db->setQuery("SELECT id FROM #__menu WHERE parent_id='$deletePageId'" );
		}
		$rows = $this->db-> loadObjectList();
		foreach($rows as $row)
		{
			$this->deletePage($row->id);
		}
	}

/*
end pageDelete
*/
	//TODO only for wich pagetype?
	function get_categories()
	{
		static $pi_category_array;
		if(!$pi_category_array)
		{
			$this->db->setQuery("SELECT id, title, section FROM #__categories ");
			$pi_categories_object = $this->db->loadObjectList();
			
			$pi_category_array = array();
			foreach($pi_categories_object as $category)
			{
				$pi_category_array[] = array($category->id, $category->title, $category->section);
			}
		}
		return $pi_category_array;
	}
	
	//TODO only for wich pagetype?  and Joomla 1.6 have no sections
	function get_sections()
	{
		static $pi_sections_array;
		if(!$pi_sections_array)
		{
			$this->db->setQuery("SELECT id, title FROM #__sections ");
			$pi_sections_object = $this->db->loadObjectList();
			
			$pi_sections_array = array();
			foreach($pi_sections_object as $pi_section)
			{
				$pi_sections_array[] = array($pi_section->id, $pi_section->title);
			}
		}
		return $pi_sections_array;
	}



	function check_display_item_property($property, $right)
	{
		$display = false;
			
		if($this->user_type=='Super Administrator' && !$this->_config['item_props_hideforsuperadmin'])
		{
			$display = true;
		}
		else
		{
			//check configuration
			if($this->_config[$property])
			{
				$display = true;
			}
		}
		//$display = true;
		return $display;
	}


/*

		$pageId = JRequest::getVar('pageId',0);
		$this->assignRef( 'pageId', $pageId);
*/	
	function getPageId()
	{
		if(!$this->pageId)
		{
			if($this->joomlaVersion < '1.6'){
				$root_id = 0;
			}else{
				$root_id = 1;
			}
			$this->pageId = JRequest::getVar('pageId', $root_id);
		}
		return $this->pageId;
	
	}

	
	function truncate_string($string, $length)
	{
		$dots='...';
		$string = trim($string);
		if(strlen($string)<=$length){
			return $string;
		}
		if(!strstr($string," ")){
			return substr($string,0,$length).$dots;
		}
		$lengthf = create_function('$string','return substr($string,0,strrpos($string," "));');
		$string = substr($string,0,$length);
		$string = $lengthf($string);
		while(strlen($string)>$length){
			$string=$lengthf($string);
		}
		return $string.$dots;
	}
	
	function checkItemTypeInstall($item_type)
	{
		//here we call the database #__extensions
		//JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
		//$row = JTable::getInstance('piextension','PagesAndItemsTable');
		if(strpos($item_type, 'ustom_'))
		{
			$item_type = 'custom';
		}
		
		$query = 'SELECT * ';
		$query .='FROM #__pi_extensions ';
		$query .='WHERE type='.$this->db->Quote('itemtype').' ';
		$query .='AND element='.$this->db->Quote($item_type);
		$this->db->setQuery( $query );
		$row = $this->db->loadObject( );
		return $row;
	}
	
	//copied to helper file
	//to do, check all files for calls to this function and move the call to the helper
	function translate_item_type($item_type)
	{
		/*
		all itemtypes are extensions so we can add language to each itemtype
		if we want so all itemtype JText can remove from the base language file
		and only the custom_* need extra routine here
		
		*/
		if($item_type=='text')
		{
			$plugin_name = 'Joomla '.JText::_('COM_PAGESANDITEMS_ITEMTYPE_JOOMLA_ARTICLE');
		}
		elseif($item_type=='html')
		{
			$plugin_name = 'HTML';
			//$plugin_name = 'html';
		}
		elseif($item_type=='Xcontent')
		{
			//but here for test content renamed to Xcontent
			$plugin_name = 'content'; //ADD to see if not an pi item
		}
		elseif($item_type=='other_item')
		{
			$plugin_name = JText::_('COM_PAGESANDITEMS_ITEMTYPE_OTHER_ITEM');
		}
		elseif(strpos($item_type, 'ustom_'))
		{
			//custom itemtype
			$pos = strpos($item_type, 'ustom_');
			$type_id = substr($item_type, $pos+6, strlen($item_type));
			$this->db->setQuery("SELECT name FROM #__pi_customitemtypes WHERE id='$type_id' LIMIT 1");
			$rows = $this->db->loadObjectList();
			$row = $rows[0];
			$plugin_name = $row->name;
		}
		else
		{
			//
			/*
			
			$translated = JText::_('PI_EXTENSION_ITEMTYPE_'.strtoupper($item_type).'_NAME');
			if($translated <> 'PI_EXTENSION_ITEMTYPE_'.strtoupper($item_type).'_NAME')
			{
				
			}
			*/
			/*
			we will load the extension
			if we have $itemtypeHtml == '' the extension are not installed ore not published?
			*/
			
			//$itemtype = ExtensionHelper::importExtension('itemtype',null, $item_type,true,null,true);
			
			$path = realpath(dirname(__FILE__).DS.'..');
			require_once($path.DS.'includes'.DS.'extensions'.DS.'itemtypehelper.php');
			$itemtype = ExtensionItemtypeHelper::importExtension(null, $item_type,true,null,true);
			$dispatcher = &JDispatcher::getInstance();
			//$itemtypeHtml = & new JObject();
			$itemtypeHtml = ''; //->text = '';
			
			$results = $dispatcher->trigger('onGetPluginName', array(&$itemtypeHtml,$item_type));
			//$dispatcher->trigger('onDetach',array($item_type));
			//if($itemtypeHtml->text != '')
			if($itemtypeHtml != '')
			{
				//$plugin_name = $itemtypeHtml->text;
				$plugin_name = $itemtypeHtml;
			}
			else
			{
				$plugin_name = false;
				$plugin_name = $item_type;
				
			}
			//
			//echo ' itemtype: '.$item_type.' itemtypeHtml: '.$itemtypeHtml.' plugin_name: '.$plugin_name.'  </ br>';
		}
		return $plugin_name;
	}
	
	function getMenutypeTitle($menutype)
	{
		$menutype_title = '';
		$temp_menus = explode(',',$this->_config['menus']);
		for($n = 0; $n < count($temp_menus); $n++)
		{
			$menutype_temp = explode(';',$temp_menus[$n]);
			if($menutype_temp[0]==$menutype){
			
				$menutype_title = $menutype_temp[1];
				break;
			}
		}

		return $menutype_title;
		//return strtolower($menutype_title);
	}
	
	function getMenutypes()
	{
		if(!$this->_menutypes)
		{
			$this->setMenutypes();
		}
		return $this->_menutypes;
	}
	
	function setMenutypes() //$id = null, $edit = null)
	{
		//check to see which menutypes we need
		$config = $this->getConfig();
		if($config['menus'] != '')
		{
			$temp_menus = explode(',',$config['menus']);
			for($n = 0; $n < count($temp_menus); $n++)
			{
				$menutype = explode(';',$temp_menus[$n]);
				array_push($this->_menutypes,$menutype[0]);
			}
		}
		else
		{
		
		}
	}

	function getCurrentMenutype()
	{

		if(!$this->_currentMenutype)
		{
			$this->setCurrentMenutype();
			
		}
		return $this->_currentMenutype;
	}
	
	
	function setCurrentMenutype()
	{
		$config = $this->getConfig();
		
		$temp_menus = explode(',',$config['menus']);
		//if(!$view || ($view == 'page' && $layout == 'root' && !$menutype ))
		//get the current pages menutype
		if(count($temp_menus)!=0)
		{
			if(!JRequest::getVar('view') || (JRequest::getVar('view') == 'page' && JRequest::getVar('layout') == 'root' || !JRequest::getVar('menutype',0)) ) //
			{
				$menu_in_url = JRequest::getVar('menutype');
				if(!$menu_in_url)
				{
					$this->getMenutypes();
					$menu_in_url = $this->_menutypes[0];
				}
				$this->_currentMenutype = $menu_in_url;
			}
			else
			{
				foreach($this->getMenuitems() as $menuitem)
				{
					if($menuitem->id == JRequest::getVar('pageId'))
					{
						$this->_currentMenutype = $menuitem->menutype;
						break;
					}
				}
			}
		}
		/*
		else
		{
			//TODO send $message
			return  '<div class="warning">'.JText::_('COM_PAGESANDITEMS_NO_MENUS_SELECTED').'</div>';
		}
		*/
	}

	function getCurrentPageId()
	{
		if(!$this->_currentPageId)
		{
			$this->setCurrentPageId();
		}
		return $this->_currentPageId;
	}

	function setCurrentPageId()
	{
		//$db =& JFactory::getDBO();
		$menutype = $this->getCurrentMenutype();
		
		$this->db->setQuery("SELECT * FROM #__menu WHERE (published='0' OR published='1') AND menutype='$menutype' ORDER BY ordering ASC LIMIT 1" );
		$menuitem = $this->db->loadObject();
		if($menuitem)
		{
			$this->_currentPageId = $menuitem->id;
		}
	}

	function getMenuitems($state = "(published='0' OR published='1')")
	{
		if(!$this->_menuitems)
		{
			$this->setMenuitems($state);
		}
		return $this->_menuitems;
	}
	
	function setMenuitems($state = "(published='0' OR published='1')")
	{
		$where = array();
		$where[] = $state;
		//Where is use view pages and _currentMenutype and...?
		//get menuitems (to be recycled in different functions)
		$temp_menus = $this->getMenutypes();
		$menutypes = '';
		if(count($temp_menus))
		{
			$menutypes = "AND (";
			$where_menutypes = "(";
			for($n = 0; $n < count($temp_menus); $n++)
			{
				if($n!=0)
				{
					$menutypes .= " OR ";
					$where_menutypes .= " OR ";
				}
				$menutype = explode(';',$temp_menus[$n]);
				$menutypes .= "menutype='".$menutype[0]."'";
				$where_menutypes .= "menutype='".$menutype[0]."'";
			}
			$menutypes .= ")";
			$where_menutypes .= ")";
			$where[] = $where_menutypes;
		}
		
		
		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		//dump($where);
		
		//$db =& JFactory::getDBO();
		if($this->joomlaVersion < '1.6')
		{
			//$this->db->setQuery("SELECT * FROM #__menu  WHERE (published='0' OR published='1') $menutypes ORDER BY menutype ASC, sublevel ASC, ordering ASC"  );
			$this->db->setQuery("SELECT * FROM #__menu $where ORDER BY menutype ASC, sublevel ASC, ordering ASC"  );
		}
		else
		{
			//$this->db->setQuery("SELECT *, title as name, parent_id as parent FROM #__menu  WHERE (published='0' OR published='1') $menutypes ORDER BY menutype ASC, level ASC, ordering ASC"  );
			
			//$this->db->setQuery("SELECT *, title as name, parent_id as parent FROM #__menu  WHERE (published='0' OR published='1') $menutypes ORDER BY lft ASC "  );
			$this->db->setQuery("SELECT *, title as name, parent_id as parent FROM #__menu $where ORDER BY lft ASC "  );
		}
		
		$this->_menuitems = $this->db->loadObjectList();
	}

	function getMenutypeMenuitems($menutype,$state = "(published='0' OR published='1')",$return = 'object')
	{
		$menutypes = "AND (menutype='".$menutype."')";
		$where = array();
		$where[] = $state;
		$where[] = "(menutype='".$menutype."')";
		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		//dump($where,'2');
		if($this->joomlaVersion < '1.6')
		{
			$this->db->setQuery("SELECT * FROM #__menu $where ORDER BY menutype ASC, sublevel ASC, ordering ASC"  );
			//$this->db->setQuery("SELECT * FROM #__menu  WHERE (published='0' OR published='1') $menutypes ORDER BY menutype ASC, sublevel ASC, ordering ASC"  );
		}
		else
		{
			//$this->db->setQuery("SELECT *, title as name, parent_id as parent FROM #__menu  WHERE (published='0' OR published='1') $menutypes ORDER BY menutype ASC, level ASC, ordering ASC"  );
			//$this->db->setQuery("SELECT *, title as name, parent_id as parent FROM #__menu  WHERE (published='0' OR published='1') $menutypes ORDER BY menutype ASC, level ASC, lft ASC"  );
			$this->db->setQuery("SELECT *, title as name, parent_id as parent FROM #__menu  $where ORDER BY menutype ASC, level ASC, lft ASC"  );
		}
		if($return == 'object')
		{
			return $this->db->loadObjectList();
		}
		else
		{
			return $this->db->loadAssocList('id');
			return $this->db->loadResultArray();
		}
	}

	function getAllMenuItems($state = "(published='0' OR published='1')")
	{
		if(!$this->_allMenuItems)
		{
			$this->setAllMenuItems($state);
		}
		return $this->_allMenuItems;
	}
	
	function setAllMenuItems($state = "(published='0' OR published='1')")
	{
		//get allMenuItems (to only test)
		$where = array();
		$where[] = $state;
		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		if($this->joomlaVersion < '1.6')
		{
			$this->db->setQuery("SELECT * FROM #__menu $where ORDER BY menutype ASC, sublevel ASC, ordering ASC"  );
		}
		else
		{
			$this->db->setQuery("SELECT *, title as name, parent_id as parent FROM #__menu  $where ORDER BY menutype ASC, level ASC, lft ASC"  );
		}
		$this->_allMenuItems = $this->db->loadObjectList();
	}


	//copied to helper
	//to do, find all calls to this function and make it go to the helper file
	function getItemtypes()
	{
		if(!$this->_itemtypes)
		{
			$this->setItemtypes();
		}
		return $this->_itemtypes;
	}
	
	//copied to helper
	//to do, find all calls to this function and make it go to the helper file
	function setItemtypes()
	{
		$config = $this->getConfig();
		$temp_itemtypes = explode(',',$config['itemtypes']);
		$temp = array();
		for($n = 0; $n < count($temp_itemtypes); $n++)
		{
			//array_push($this->_itemtypes,$temp_itemtypes[$n]);
			//make type 'content' and 'text' the same
			$type = $temp_itemtypes[$n];
			if($type=='content'){
				$type = 'text';
			}
			$temp[] = $type;
		}
		//make unique
		$temp = array_unique($temp);
		$this->_itemtypes = $temp;
	}
	
/*
**********
from view/page/view.html.php
**********
*/


	function reload()
	{
		$html ='';
		$html .='<div class="page_reload" id="page_reload" style="display:none;">';
			$html .='<div>';
				$html .= JText::_('COM_PAGESANDITEMS_RELOAD');
			$html .='</div>';
			$html .='<div>';
				$html .='<img src="'.$this->dirIcons.'processing.gif" >';
			$html .='</div>';
		$html .='</div>';
		return $html;
	}

	function getMenuItem()
	{
		$sub_task = JRequest::getVar( 'sub_task', 'edit');
		$menutype = JRequest::getVar( 'menutype');
		$type = JRequest::getVar( 'type');
		$extension = 'com_menus';
		$lang = &JFactory::getLanguage();
		$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false);
		$pageType = JRequest::getVar( 'pageType', '' );
		$menu_item = null;
		
		if($sub_task=='new')
		{
			$pageTypeType = JRequest::getVar('pageTypeType');
			$pageTypeType = json_decode(base64_decode($pageTypeType));
			if(isset($pageTypeType->request))
			{
				$url = null;
				foreach($pageTypeType->request as $key => $value)
				{
					$url[$key] = $value;
				}
				
				if($this->joomlaVersion < '1.6')
				{
					JRequest::setVar( 'url',  $url);
					JRequest::setVar( 'edit', false );
					$menu_item = &new MenusModelItem();
					$item = $menu_item->getItem();
				}
				else
				{
					$app	= JFactory::getApplication();
					// Push the new ancillary data into the session.
					$app->setUserState('com_menus.edit.item.type',	null);
					$app->setUserState('com_menus.edit.item.link',	null);
					
					$parent_id = JRequest::getVar('pageId', '');
					$app->setUserState('com_menus.edit.item.parent_id',	$parent_id);
					
					$menu_item = &new MenusModelItem();
					JRequest::setVar( 'id', 0 );
					$menu_item->setState('item.type',$pageTypeType->type);
					$menu_item->setState('item.menutype',$menutype);
					
					

// Check if the link is in the form of index.php?...
					
					if($url)
					{
						if (is_string($url))
						{
							$args = array();
							if (strpos($url, 'index.php') === 0) 
							{
								parse_str(parse_url(htmlspecialchars_decode($url), PHP_URL_QUERY), $args);
							}
							else 
							{
								parse_str($url, $args);
							}
							$url = $args;
						}
						// Only take the option, view and layout parts.
						$filter = array('option', 'view', 'layout');
						foreach ($url as $name => $value)
						{
							if (!in_array($name, $filter))
							{
								// Remove the variables we want to ignore.
								unset($url[$name]);
							}
						}
						$link = 'index.php?'.http_build_query($url,'','&');
					}
					else
					{
						$link = '';
					}

		//ksort($request);

		


					
					
					$menu_item->setState('item.link', $link); //MenusHelper::getLinkKey($url));
					
					$menu_item->setState('item.id',0);
					$item = $menu_item->getItem();
					//dump($item);
					/*
					$this->form		= $menu_item->getForm();
					$this->item		= $item;
					$this->modules	= $menu_item->getModules();
					$this->state	= $menu_item->getState();
					*/
				}
			}
		}
		else
		{
			if($this->joomlaVersion < '1.6')
			{
				JRequest::setVar( 'edit', true );
				JRequest::setVar( 'cid',  array($this->getPageId()));
				$menu_item = &new MenusModelItem();
				$item = $menu_item->getItem();
			}
			else
			{
				$app	= JFactory::getApplication();
				// Push the new ancillary data into the session.
				$app->setUserState('com_menus.edit.item.type',	null);
				$app->setUserState('com_menus.edit.item.link',	null);

				$menu_item = &new MenusModelItem();
				

				//dump($this->getPageId());
				$getPageId = $this->getPageId();
				JRequest::setVar( 'id', $getPageId );
				$menu_item->setState('item.id',$getPageId);
				//dump($menu_item->getState());
				//$menu_item->setState('item.type',$pageTypeType->type);
				$menu_item->setState('item.menutype',$menutype);
				//$menu_item->setState('item.link', MenusHelper::getLinkKey($url));
				//dump($this->getPageId());
				
				//
				$item = $menu_item->getItem($getPageId);
				
				$menu_item->setState('item.link',$item->link);
				//dump($menu_item->getState());
				/*
				
						// Load the User state.
		$pk = (int) JRequest::getInt('id');
		$this->setState('item.id', $pk);

		if (!($parentId = $app->getUserState('com_menus.edit.item.parent_id'))) {
			$parentId = JRequest::getInt('parent_id');
		}
		$this->setState('item.parent_id', $parentId);

		if (!($menuType = $app->getUserState('com_menus.edit.item.menutype'))) {
			$menuType = JRequest::getCmd('menutype', 'mainmenu');
		}
		$this->setState('item.menutype', $menuType);

		if (!($type = $app->getUserState('com_menus.edit.item.type'))){
			$type = JRequest::getCmd('type');
			// Note a new menu item will have no field type.
			// The field is required so the user has to change it.
		}
		$this->setState('item.type', $type);

		if ($link = $app->getUserState('com_menus.edit.item.link')) {
			$this->setState('item.link', $link);
		}
				*/
/*
com_menus
# [string] task = ""
# [integer] item.id = 290
# [integer] item.parent_id = 0
# [string] item.menutype = "mainmenu"
# [string] item.type = "component"
# [JRegistry object] params

# [string] item.link = "index.php?option=com_content&view=categories&id=0"

page
# [integer] item.id = 0
# [string] item.menutype = "mainmenu"
# [integer] item.parent_id = 0
# [string] item.type = "component"
*/
			}
		}

		if(!$pageType)
		{
			//if ($this->joomlaVersion < '1.6')
			//{
			//	$model = &$this->getModel('menutypes','pagesanditemsModel');
			//}
			//else
			//{
			$model = new PagesAndItemsModelMenutypes();
			//}
			
			if($model)
			$pageType =$model->buildPageType($item->link);
			if(!isset($this->menuItemsTypes[$pageType]))
			{
				$pageType = null;
			}
		}
		
		/*
		if we have no $pageType that must be the root in tree
		*/
		if($pageType)
		{
			//we have an pageType
			$this->pageType = $pageType;
			$this->menuItemsType = $this->menuItemsTypes[$pageType];
			$this->menu_item = $menu_item;
			$this->pageMenuItem = $item;
			$dispatcher = &JDispatcher::getInstance();
			/*
			we want not get the other loaded pagetypes so we detach 
			so only the $pageType raise the event
			*/
			$dispatcher->trigger('onDetach',array($pageType));
			$name = '';
			$results = $dispatcher->trigger('onGetPagetype',array(&$name,$pageType));
		}
	}
	



	//USED
	function getLists()
	{
		// Was showing up null in some cases....
		//pageMenuItem is an #__menus table object
		
		if ($this->joomlaVersion < '1.6' && !$this->pageMenuItem->published) 
		{
			$this->pageMenuItem->published = 0;
		}
		
		$lists = new stdClass();
		
		//this can be '' ore 'style="display:none;"';
		$lists->display->id = '';
		$lists->display->title = '';
		$lists->display->alias = '';
		$lists->display->link = '';
		$lists->display->menutype = '';
		$lists->display->parent = '';
		$lists->display->published = '';
		$lists->display->ordering = '';
		$lists->display->accesslevel = '';
		$lists->display->menulink = '';
		$lists->display->params = ''; //'style="display:none;"';//'';
		$lists->display->advancedparams = '';
		$lists->display->componentparams = '';
		$lists->display->systemparams = '';
		$lists->hideAll = '';
		
		//free html
		$lists->add->bottom = '';
		$lists->add->top = '';
		
		
		//other way for joomla 1.6
		//
		if($this->joomlaVersion < '1.6')
		{
			$lists->published = MenusHelper::Published($this->pageMenuItem); //return radiolist
		}
		else
		{
			$lists->published = $this->form->getInput('published');
			$this->pageMenuItem->name = $this->pageMenuItem->title;
		}
		$this->pageMenuItem->expansion = null;
		if ($this->pageMenuItem->type != 'url') 
		{
			$lists->disabled->link = 'readonly="true"';
			$this->pageMenuItem->linkfield = '<input type="hidden" name="link" value="'.$this->pageMenuItem->link.'" />';
			if (($this->pageMenuItem->id) && ($this->pageMenuItem->type == 'component') && (isset($this->pageMenuItem->linkparts['option']))) 
			{
				$this->pageMenuItem->expansion = '&amp;expand='.trim(str_replace('com_', '', $this->pageMenuItem->linkparts['option']));
			}
		}
		else
		{
			$lists->disabled->link = null;
			$this->pageMenuItem->linkfield = null;
		}
		
		
		if(!$this->pageMenuItem->home)
		{
			$this->pageMenuItem->home = 0;
		}
		$put[] = JHTML::_('select.option',  '0', JText::_( 'No' ));
		$put[] = JHTML::_('select.option',  '1', JText::_( 'Yes' ));
		$lists->home = JHTML::_('select.radiolist',  $put, 'home', '', 'value', 'text', $this->pageMenuItem->home );
		$lists->pageType->html = '';
		$dispatcher = &JDispatcher::getInstance();
		$dispatcher->trigger('onGetLists',array(&$lists,$this->pageMenuItem,$this));
		$this->lists = $lists;
	}
	
	//used
	function getPages()
	{
		/*
		ADD ms: 23.03.2011
		get all featured articles
		*/
		$this->db->setQuery("SELECT id FROM #__content  WHERE featured='1' " );
		$featureds = $this->db->loadResultArray();
		//ADD END ms: 23.03.2011

		$doc =& JFactory::getDocument();
		$model = new PagesAndItemsModelMenutypes();
		//see how many loops we need
		$menutypes = $this->getMenutypes();
		$loops = count($menutypes);
		$extension = 'com_menus';
		$lang = &JFactory::getLanguage();
		$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false);
		$html = '';

		//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'extensions'.DS.'helper.php');
		//$htmlelements = ExtensionHelper::importExtension('html','page_tree', null,true,null,true);
		$path = realpath(dirname(__FILE__).DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'htmlhelper.php');
		$htmlelements = ExtensionHtmlHelper::importExtension('page_tree',null,true,null,true);
		$dispatcher = &JDispatcher::getInstance();
		//$htmlelementVars = null;
		$htmlelement->html = '';
		$dispatcher->trigger('onGetHtmlelement',array(&$htmlelement,'page_tree'));
		//$html .= $htmlelement->html;
		$html .= '<div class="page_tree">';
		if($htmlelement->html != '')
		{
			$html .= '<div class="dtree dtree_container">';
				
					$html .= $htmlelement->html;
				//$html .= '<div class="pi_wrapper">';
				//$html .= '</div>';
			$html .= '</div>';
			//add an line?
		}
		//if($model)
		for($m = 0; $m < $loops; $m++)
		{
			$menuitems = $this->getMenutypeMenuitems($menutypes[$m]);
			//dump('X');
			//dump($menuitems);
			$script = '';
			$html .= '<div class="dtree dtree_container">';
			$html .= '<p><a href="javascript: d'.$m.'.openAll();">'.JText::_('COM_PAGESANDITEMS_OPEN_ALL').'</a> | <a href="javascript: d'.$m.'.closeAll();">'.JText::_('COM_PAGESANDITEMS_CLOSE_ALL').'</a></p>';
			$script .= "d$m = new dTree('d$m');\n";
			/*
			COMMENT:
				here we set the icons for dTree not in dTree.js
				so we can change the dir aeasy
			*/
			$script .= PagesAndItemsHelper::getdTreeIcons("d".$m);
/*			$script .= "d$m.icon = {";
			$script .= "root		: '".$this->dirIcons."icon-16-menu.png',
			folder	: '".$this->dirIcons."folder.gif',
			folderOpen	: '".$this->dirIcons."folderopen.gif',
			node		: '".$this->dirIcons."page.gif',
			empty		: '".$this->dirIcons."empty.gif',
			line		: '".$this->dirIcons."line.gif',
			join		: '".$this->dirIcons."join.gif',
			joinBottom	: '".$this->dirIcons."joinbottom.gif',
			plus		: '".$this->dirIcons."plus.gif',
			plusBottom	: '".$this->dirIcons."plusbottom.gif',
			minus		: '".$this->dirIcons."minus.gif',
			minusBottom	: '".$this->dirIcons."minusbottom.gif',
			nlPlus	: '".$this->dirIcons."nolines_plus.gif',
			nlMinus	: '".$this->dirIcons."nolines_minus.gif'
			};\n";
*/
			/*
				COMMENT
				in Joomla 1.6 
				we have one parent_id=0 in table #__menus
				but more parent_id=1 in table #__menus
				
				parent_id=1 in table #__menus = menutype:'', title:Menu_Item_Root, alias:root
				
			*/
			if ($this->joomlaVersion < '1.6')
			{
				$script .= "d$m.add(0,-1,'";
			}
			else
			{
				$script .= "d$m.add(1,-1,'";
			}
			$script .= $this->getMenutypeTitle($menutypes[$m]);
			$script .= "','index.php?option=com_pagesanditems&view=page&layout=root&menutype=";
			$script .= strtolower($menutypes[$m]);
			$script .= "','','','','',true);\n";
			
			//$imagePath = $this->dirIcons;
			//make javascript-array from menu-items
			$counter = 0;
			//loop menuitems
			foreach($menuitems as $row)
			{
				$counter++;
				$image = '';
				$imageNoAccess = '';
				$row->menu_item_article_pi = false;
				$row->menu_item_article_no_access = false;
				$itemtype_no_access = array();
				$not_installed_no_access = false;
				$pageType = null;
				if($row->type != 'component')
				{
					$pageType = $row->type;
				}
				else
				{
					$pageType =$model->buildPageType($row->link);
					if(!isset($this->menuItemsTypes[$pageType]))
					{
						$pageType = null;
					}
				}
				if(!$pageType)
				{
					//we have an component without option???
					//i think is an unistallet component
					//we set the image to component_no_access
					//we need an $this->menuItemsTypes->not_installed_no_access
					$pageType = 'not_installed_no_access';
					$not_installed_no_access = true;
				}
				$menuItemsType = $this->menuItemsTypes[$pageType];
				//dump($menuItemsType);
				if(isset($menuItemsType->icons->default->imageUrl))
				{
					$image = $menuItemsType->icons->default->imageUrl;
				}
				else
				{
					if(isset($menuItemsType->icons->componentDefault->default->imageUrl))
					{
						$image = $menuItemsType->icons->componentDefault->default->imageUrl;
					}
				}
				/*
				ADD ms: 23.03.2011
				only if $pageType == content_article
				for featured article add an own icon
				*/
				if($pageType == 'content_article')
				{
					if($contentId = $model->getId($row->link))
					{
						if(in_array($contentId,$featureds))
						{
							/*
							ok we will look at an extra icon
							only for pageTree and pageChilds
							*/
							if(isset($menuItemsType->icons->featured_default->imageUrl))
							{
								$image = $menuItemsType->icons->featured_default->imageUrl;
							}
						}
					}
				}

				//ADD END ms: 23.03.2011

				if(isset($menuItemsType->icons->no_access->imageUrl))
				{
					$imageNoAccess = $menuItemsType->icons->no_access->imageUrl;
				}
				else
				{
					if(isset($menuItemsType->icons->componentDefault->no_access->imageUrl))
					{
						$imageNoAccess = $menuItemsType->icons->componentDefault->no_access->imageUrl;
					}
				}
				if($not_installed_no_access)
				{
					$image = $imageNoAccess;
					$itemtype_no_access[] = addslashes(JText::_('COM_PAGESANDITEMS_COMPONENT_NOT_INSTALLED_NO_ACCESS'));
					$row->dtree_no_access = 1;
				}
				$row->dtree_image = $image;
				$row->dtree_imageNoAccess = $imageNoAccess;
				$row->pageType = $pageType;
				
				//here we check for an empty separator
				if($row->type == 'separator' )
				{
					$name = ''; 
					if($row->name != '')
					{
						//$name .= ' ('.$row->name.')';
						$name .= $row->name;
					}
					else
					{
						$name .= ' (empty)';
					}
					$menuName = $name;
				}
				else
				{
					$menuName = $row->name;
				}
				$row->dtree_menuName = $menuName;
				/*
				in Joomla 1.6 we have parent_id not parent
				but this is fixet in models/page.php
				*/
				$menuName = addslashes($menuName);
				$title = '';
				$script .= "d$m.add(".$row->id;//." id
				$script .= ",".$row->parent;//." , pid
				$script .= ",'".($menuName)."'"; //, name
				
				/*
				TODO
				user_access
				if(! ....)
				{
					$itemtype_no_access[] = ...
					$image = ...
				}
				*/

				if($itemtype_no_access != '' && !is_array($itemtype_no_access))
				{
					$title = $itemtype_no_access;
					$script .= ",'";
				}
				elseif($itemtype_no_access != '' && is_array($itemtype_no_access) && count($itemtype_no_access))
				{
					$title = implode(', ',$itemtype_no_access);
					$script .= ",'";
				}
				else
				{
					//$itemtype_no_access = '';
					$script .= ",'index.php?option=com_pagesanditems&view=page&menutype=".$row->menutype."&pageId=".$row->id."&sub_task=edit&pageType=".$pageType;
				}
				$script .= "','".$title."','','".$image."','".$image;
				$script .= "');\n";
				if($this->pageId == $row->id)
				{
					$this->pageMenuItem = $row;
				}
				if( ($row->parent == $this->getPageId() && $row->menutype == $this->getCurrentMenutype()) || (!$this->getPageId() && $row->menutype == $this->getCurrentMenutype()) )
				{
					$this->currentMenuitems = $menuitems;
				}
			}
			//end loop menuitems
			$doc->addScriptDeclaration($script);
			//open javascript
			$html .= '<script language="javascript" type="text/javascript">'."\n";
			$html .= "<!--\n";
			$html .= "document.write(d".$m.");\n";
			//if on a certain page, make tree-menu-button selected
			if($menutypes[$m] == $this->getCurrentMenutype())
			{
				if($this->getPageId())
				{
					$html .= "d$m.openTo(";
					$html .= $this->getPageId();
					$html .= ", true);\n";
				}
			}
			//close javascript
			$html .=  "-->\n";
			$html .=  '</script>'."\n";
			$html .= '</div>';
			

		}//end loops menutype
		$html .= '</div>';
		return $html;
	}
	
	
	function getChilds()
	{
		$html = '';
		$html .= '<script src="components/com_pagesanditems/javascript/reorder_pages.js" language="JavaScript" type="text/javascript">';
		$html .= '</script>';
		$html .= '<tbody id="underlayingPages">';
			$html .= '<tr>';
				$html .= '<th style="background: none repeat scroll 0 0 #F0F0F0;border-bottom: 1px solid #999999;">';
				if($this->pageMenuItem && $this->menuItemsType)
				{
					$menuItemsType = $this->menuItemsType;
					if(isset($menuItemsType->icons->default->imageUrl))
					{
						$image = $menuItemsType->icons->default->imageUrl;
					}
					if($image)
					{
						
						$imgClass = explode("class:",$image);
						if(count($imgClass) && count($imgClass) == 2)
						{
							//we have an class
							$html .= '<a ';
							$html .= 'class="icon '.$imgClass[1].'" ';
							$html .= 'alt="" >&nbsp;';
							$html .= '<span> ';
							$html .= '</span>';
							$html .= '</a>';
						}
						else
						{
						
						$html .= '<img src="'.$image.'" alt="" style="vertical-align: middle;position: relative;" />&nbsp;';
						}
						$html .= JText::_('COM_PAGESANDITEMS_UNDERLYING_PAGES');
					}
					else
					{
						$html .= '<img src="'.$this->dirIcons.'icon-16-menu.png" alt="" style="vertical-align: middle;" />&nbsp;';
							$html .= JText::_('COM_PAGESANDITEMS_UNDERLYING_PAGES');
					}
				}
				else
				{
					$html .= '<img src="'.$this->dirIcons.'icon-16-menu.png" alt="" style="vertical-align: middle;" />&nbsp;';
						$html .= JText::_('COM_PAGESANDITEMS_UNDERLYING_PAGES');
				}
				$menutype = JRequest::getVar('menutype', null);
				$pageId = JRequest::getVar('pageId', null);
				if($menutype && !$pageId)
				{
					$html .= '&nbsp;['.$menutype.']';
				}
				$html .= '</th>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td>';
					$html .=  $this->getUnderlyingPages();
				$html .= '</td>';
				$html .= '</tr>';
		$html .= '</tbody>';
		return $html;
	}
	
	//ms: add	
	function getCanDo($component = 'com_menus') 
	{
		if(!isset($this->canDo->$component))
		{
			switch($component)
			{
				case 'com_menus':
					require_once JPATH_ADMINISTRATOR.'/components/com_menus/helpers/menus.php';
					//$canDo	= MenusHelper::getActions($this->getPageId());
					$this->canDo->$component = MenusHelper::getActions();//$this->getPageId());
					return $this->canDo->$component;
				break;
				case 'com_content':
					require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'content.php');
					$this->canDo->$component = ContentHelper::getActions();
					return $this->canDo->$component;
				break;
				
				default: 
					return null;
				break;
			}
		}
		else
		{
			return $this->canDo->$component;
		}
	}
	
	//underlyingPages
	function getUnderlyingPages() 
	{
		
		$doc =& JFactory::getDocument();
		$html = '';
		$html .= '<div class="paddingList">';
		$imagePath = $this->dirIcons;
		$layout = JRequest::getCmd('layout', '');
		if($layout && $layout != '')
		{
			$layout = '&layout='.$layout.'';
		}
		
		//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'extensions'.DS.'helper.php');
		//$htmlelements = ExtensionHelper::importExtension('html','page_childs', null,true,null,true);
		$path = realpath(dirname(__FILE__).DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'htmlhelper.php');
		$htmlelements = ExtensionHtmlHelper::importExtension('page_childs',null,true,null,true);
		
		$dispatcher = &JDispatcher::getInstance();
		//$htmlelementVars = null;
		$htmlelement->html = '';
		$option = JRequest::getCmd('option', '');
		
		$this->getCanDo('com_menus');
		
		
		//hide the data on the page
		$html .= '<div style="display: none;">';
		//headers
		$html .= '<div id="pagesheader_column_1"><strong>'.JText::_('COM_PAGESANDITEMS_TITLE').'</strong></div>';
		$html .= '<div id="pagesheader_column_2"><strong>'.JText::_('COM_PAGESANDITEMS_PUBLISHED').'</strong></div>';
		//$html .= '<div id="pagesheader_column_3"><strong>'.JText::_('COM_PAGESANDITEMS_TYPE').'</strong></div>';

		//loop through items and echo data to hidden fields
		$counter = 0;
		$page_ids = array();
		if($this->currentMenuitems)
		{
			foreach($this->currentMenuitems as $row)
			{
			if($row->parent == $this->getPageId())
			{
				$page_ids[] = $row->id;
				$areThereUnderlyingPages = true;
				$counter = $counter + 1;
				
				if($row->type == 'separator' )
				{
					$name = JText::_('COM_PAGESANDITEMS_MENU_ITEM_TYPE').': '.JText::_('SEPARATOR');
					if($row->name != '')
					{
						$name .= ' ('.$row->name.')';
					}
					else
					{
						$name .= ' (empty)';
					}
					$menuName = $name;
				}
				else
				{
					$menuName = $row->name;
				}
				$image = $row->dtree_image;
				$page_title = htmlspecialchars($menuName);
				if(isset($row->dtree_menuName))
				{
					$page_title = stripslashes($row->dtree_menuName);
				}
				$no_access = '';
				
				/*
				TODO must rewrite
				check page-access
				
				if(!$this->check_page_access($row->id))
				{
					$no_access = JText::_('COM_PAGESANDITEMS_NOPAGEACCESS');
				}
				*/
				//check workflow access
				/*
				if(((strstr($row->link, 'index.php?option=com_content&view=category&layout=blog') && $row->type=='url') || !strstr($row->link, 'index.php?option=com_content&view=category&layout=blog')) && $row->type!='content_blog_category')
				{
					
				}
				else
				{
					//$pagetype = 'contentblog';
					
					
				}
				*/
				if(isset($row->dtree_no_access) && $row->dtree_no_access)
				{
					$no_access = addslashes(JText::_('COM_PAGESANDITEMS_COMPONENT_NOT_INSTALLED_NO_ACCESS')); //Component not_installed_no_access)
				}
				$html .= '<input name="reorder_page_id_'.$counter.'" id="reorder_page_id_'.$counter.'" type="hidden" value="'.$row->id.'" />';
				
				if ($this->joomlaVersion >= '1.6'){
					$html .= 'lft:<input name="reorder_lft_'.$counter.'" id="reorder_lft_'.$counter.'" type="hidden" value="'.$row->lft.'" />';
					$html .= 'rgt:<input name="reorder_rgt_'.$counter.'" id="reorder_rgt_'.$counter.'" type="hidden" value="'.$row->rgt.'" />';
				}
				
				//column 1
				$html .= '<div id="page_column_1_'.$counter.'">';
					$html .= '<div class="row_items">';
					
					$html .= '<input type="checkbox" name="pageCid[]" onclick="isCheckedPage(this.checked);" id="pageCid_'.$row->id.'" value="'.$row->id.'" />';
					
					if($no_access)
					{
						$html .= '<label class="hasTip" title="'.$no_access.'" style="display: inline; clear: none;">';
							$image = $row->dtree_imageNoAccess;
							$html .= '<img src="'.$image.'" alt="'.$no_access.'" />&nbsp;';
							$html .= '<span class="editlinktip">';
							$html .= $page_title;
							$html .= '</span>';
						$html .= '</label>&nbsp;';
					}
					else
					{
						//CHANGE m: 22.03.2011
						//dump($image);
						$imgClass = explode("class:",$image);
						if(count($imgClass) && count($imgClass) == 2)
						{
							//we have an class
							$html .= '<a ';
							$html .= 'class="icon '.$imgClass[1].'" ';
							$html .= 'alt="" >&nbsp;';
							$html .= '<span> ';
							$html .= '</span>';
							$html .= '</a>';
						}
						else
						{
						
							$html .= '<img src="'.$image.'" alt="'.$no_access.'" />&nbsp;';
						}
						//$html .= '<img src="'.$image.'" alt="'.$no_access.'" />&nbsp;';
						//END CHANGE m: 22.03.2011
						$html .= '<a href="index.php?option=com_pagesanditems&view=page&sub_task=edit&pageId='.$row->id.'&pageType='.$row->pageType.'&menutype='.$row->menutype.'" alt="'.$no_access.'">';
							$html .= $page_title;
						$html .= '</a>';
					}
					$html .= '</div>';
				$html .= '</div>';
				
				//column 2
				$html .= '<div id="page_column_2_'.$counter.'">';
				if($row->published){
					$alt = JText::_('COM_PAGESANDITEMS_UNPUBLISH');
					$image = 'tick';
					$new_state = '0';
				}
				else
				{
					$alt = JText::_('COM_PAGESANDITEMS_UNPUBLISH');
					$image = 'cross';
					$new_state = '1';
				}
			
			/*
			ms: i have replace so also trash and archiv as icon 
			$html .= '<a href="javascript:publish_unpublish_item('.$row->id.','.$new_state.');">';
			$html .= '<img src="components/com_pagesanditems/media/images/icons/base/icon-16-'.$image.'.png" alt="'.$alt.'" />';
			$html .= '</a>';
			*/
			
			
					
					switch($row->published)
					{
						case '1':
							//$state = 'published';
							$state = '<span class="state publish"></span>';
							$title = 'title="'.JText::_('JLIB_HTML_UNPUBLISH_ITEM').'"';
							$alt = JText::_('COM_PAGESANDITEMS_UNPUBLISH');
							$image = 'tick';
							$new_state = '0';
							$onclick = 'onclick="publish_unpublish_page('.$row->id.','.$new_state.');"';
						break;

						case '0':
							//$state = 'unpublished';
							$state = '<span class="state unpublish"></span>';
							$title = 'title="'.JText::_('JLIB_HTML_PUBLISH_ITEM').'"';
							$alt = JText::_('COM_PAGESANDITEMS_PUBLISH');
							$image = 'cross';
							$new_state = '1';
							$onclick = 'onclick="publish_unpublish_page('.$row->id.','.$new_state.');"';
						break;
					
						case '2':
							//$state = 'archive';
							$state = '<span class="state archive"></span>';
							$title = 'title="'.JText::_('JLIB_HTML_PUBLISH_ITEM').'"';
							$alt = JText::_('COM_PAGESANDITEMS_PUBLISH');
							$image = 'archive';
							$new_state = '1';
							$onclick = 'onclick="publish_unpublish_page('.$row->id.','.$new_state.');"';
						break;
		
						case '-1':
							//$state = 'archive';
							$state = '<span class="state archive"></span>';
							$title = 'title="'.JText::_('JLIB_HTML_PUBLISH_ITEM').'"';
							$alt = JText::_('COM_PAGESANDITEMS_PUBLISH');
							$image = 'archive';
							$new_state = '1';
							$onclick = 'onclick="publish_unpublish_page('.$row->id.','.$new_state.');"';
						break;
						case '-2':
							//$state = 'trash';
							$state = '<span class="state trash"></span>';
							$title = 'title="'.JText::_('JLIB_HTML_PUBLISH_ITEM').'"';
							$alt = JText::_('COM_PAGESANDITEMS_PUBLISH');
							$image = 'trash';
							$new_state = '1';
							$onclick = 'onclick="publish_unpublish_page('.$row->id.','.$new_state.');"';
						break;
					}
					/*
					ms: the next lines use the core images one as onclick one as href i dont like the icons from the core template but i think is it better for the user
					but i must fin an way if we use the core icons we can use it also on the buttons
					and if we use the pi icons we must replace the toolbar (bigger) buttons with the pi icons
					
					I have import in pagesanditems.css an additional css for the icons so we can override some core css
					and get the images we want if you want to use the core images comment out the import in pagesanditems.css
					
					*/
					$html .= ' <a class="jgrid hasTip pi_hand" '.$title.' '.$onclick.' > '.$state.'</a>';
					
					//ms: hasTip and href make some curios in tooltip
					//$html .= ' <a class="jgrid hasTip" '.$title.' href="publish_unpublish_page('.$row->id.','.$new_state.');"> '.$state.'</a>';
					/*
					$html .= '<a class="hasTip" '.$title.' onclick="javascript:publish_unpublish_page('.$row->id.','.$new_state.');">';
					//$html .= '<a href="javascript:publish_unpublish_page('.$row->id.','.$new_state.');">';
					
					$html .= '<img src="components/com_pagesanditems/media/images/icons/base/icon-16-'.$image.'.png" alt="'.$alt.'" />';
					$html .= '</a>';
					*/
					$html .= '</div>';
				
			}
			}
		}
		$html .= '</div>';


		//ms: add slider
		$showSlider = true;
		//$showSlider = false;
		if( !$counter)
		{
			$showSlider = false;
		}
		//dump($this->canDo->com_menus->get('core.edit.state'));
		//ms: add we must check first if user have right to create com_menus
		if($this->canDo->com_menus->get('core.create'))
		{
			//ok user can create
			$htmlOptions->menuItemsTypes = $this->menuItemsTypes;
			$htmlOptions->current_menutype = $this->getCurrentMenutype();
			$dispatcher->trigger('onGetHtmlelement',array(&$htmlelement,'page_childs', $htmlOptions));
			$html .= $htmlelement->html;
		
			//$html .= '<br />';
			if(!$showSlider)
			{
				$html .= '<div class="pi_wrapper">';
					$html .= '<div class="line_top paddingList">';
					$html .= '</div>';
				$html .= '</div>';
			}
		}
		
		//ms: add slider
		if($showSlider)
		{
			/*
			$html .= JHtml::_('sliders.start','sliders-underlayingPages');
			$html .=  JHtml::_('sliders.panel',JText::_('COM_PAGESANDITEMS_ITEMS'), 'sliders-underlayingPages-options');
			$html .= '<div class="pi_wrapper">';
			$html .= '</div>';
			*/
			$html .= '<fieldset style="padding:0px;margin:0px;" class="adminform">';
			//class="pane-toggler
			$html .= '<legend id="toggle_pages" class="pane-toggler-down" style="padding-left:4px;padding-right:4px;margin-left:10px;border-bottom:0;">';
				$html .= '<a onclick="javascript:toggelPages();">';
				$html .= JText::_('COM_PAGESANDITEMS_PAGES');
				$html .= '<span></span>';
				$html .= '</a>';
				
			$html .= '</legend>';
			//$html .= '</div>';
		}

		//2 hidden fields which are usefull for updating the ordering when submitted
		$html .= '<input name="pages_are_reordered" id="pages_are_reordered" type="hidden" value="false" />';
		$html .= '<input name="pages_total" id="pages_total" type="hidden" value="'.$counter.'" />';
		
		/*
		ms: here i have add trigger for page_actions
		eg publish/unpublish/trash/delete for the underlaying pages like page_items
		 i have make the extensions
		 and add in install
		 
		 but in views/page/tmpl/default.php must add javascript like page.items_delete
		 and for the page states we need also functions like the item states
		 
		 and also must add an chackbox and the state buttons/links
		*/
		
		
		if($counter)
		{
			$html .= '<div id="target_pages_actions">';
				$html .= '<div style="float:right;">';
				$htmlelements = ExtensionHtmlHelper::importExtension('page_actions',null,true,null,true);
				$htmlelement->html = '';
				$htmlOptions = null;
				$htmlOptions->canDo = $this->canDo->com_menus;
				$dispatcher->trigger('onGetHtmlelement',array(&$htmlelement,'page_actions', $htmlOptions));
				$html .= $htmlelement->html;
				$html .= '</div>';
			$html .= '</div>';
			
			$html .= '<div class="clr">';
			$html .= '</div>';
		}
		$html .= '<div id="target_pages" ></div>';
		$script = '';
		$html .= '<script language="JavaScript"  type="text/javascript">';
		$html .= "<!--\n";
		$html .= "function toggelPages() {\n";
		$html .= "	if(document.id('toggle_pages').hasClass('pane-toggler-down')){\n";
		$html .= "	document.id('toggle_pages').addClass('pane-toggler')\n";
		$html .= "	document.id('toggle_pages').removeClass('pane-toggler-down')\n";
		$html .= "	document.id('target_pages').setProperty('style','display:none;')\n";
		$html .= "	document.id('target_pages_actions').setProperty('style','display:none;')\n";
		//
		$html .= "	}\n";
		$html .= "	else{\n";
		$html .= "	document.id('toggle_pages').addClass('pane-toggler-down')\n";
		$html .= "	document.id('toggle_pages').removeClass('pane-toggler')\n";
		$html .= "	document.id('target_pages').setProperty('style','display:block;')\n";
		$html .= "	document.id('target_pages_actions').setProperty('style','display:block;')\n";
		$html .= "	}\n";
		$html .= "}\n";
		$html .= "var pages_total = ".$counter.";\n";
		$html .= "var joomlaVersion = '".$this->joomlaVersion."';\n";
		
		//if we add an state column we must set to 2
		//$html .= "var number_of_columns_pages = '1';\n";
		$html .= "var number_of_columns_pages = '2';\n";
		
		$html .= "var ordering = '".JText::_('COM_PAGESANDITEMS_ORDERING')."';\n";
		$html .= "var no_pages = '".JText::_('COM_PAGESANDITEMS_THISPAGENOUNDERLYINGPAGES')."';\n";
		$html .= "document.onload = print_pages();\n";
		
		$html .= "var page_ids = new Array(";
		$first = 1;
		foreach($page_ids as $page_ids_page){
			if(!$first){
				$html .= ",";
			}
			$html .= "'".$page_ids_page."'";
			$first = 0;
		}
		$html .= ");\n";
		
		$html .= "function isCheckedPage(isitchecked) {
	if (isitchecked == true) {
		
		document.adminForm.boxcheckedPage.value++;
	} else {
		document.adminForm.boxcheckedPage.value--;
	}
}\n";
		$html .= "-->\n";
		$html .= "</script>\n";
		$html .= '<input type="hidden" name="boxcheckedPage" id="boxcheckedPage" value="0" />';
		
		//ms: add slider
		if($showSlider)
		{
			//$html .= JHtml::_('sliders.end');
			$html .= '</fieldset>';
		}
		$html .= '</div>';
		return $html;
	}
	
	
	

	function getPageItems()
	{
		$html = '';
		$layout = JRequest::getVar('layout',null);
		if(!$layout)
		{
			$dispatcher = &JDispatcher::getInstance();
			$dispatcher->trigger('onGetPageItems',array(&$html,$this));
		}
		
		return  $html;
	}


	
	/*
	 * @params mixed array||boolean $editToolbarButtons eg. array('new',delete'..) ore true for all
	 * @param mixed array||string $newToolbarButtons eg. array('new',delete'..) ore true for all
	
	
	
	getContentItems||getContentItem is call from the pageType
	if we make other pageTypes we must not use getContentItems||getContentItem
	is only for pageTypes that handle content
	
	*/
	function getContentItems($editToolbarButtons = true, $newToolbarButtons = true,$showItemtype_select=true)
	{
		$html = '';
		$html .= '<div class="paddingList">';
		//itemtype select and button
		$this->getCanDo('com_content');
		//dump($this->canDo->com_content->get('core.edit.state'));
		if($showItemtype_select && $this->canDo->com_content->get('core.create'))
		{
			$html .= $this->itemtype_select($this->getPageId());
			/*
			$html .= '<div class="pi_wrapper">';
				$html .= '<div class="line_top paddingList">';
				$html .= '</div>';
			$html .= '</div>';
			*/
		}
		
		//$html .= $this->addMiniToolbar($editToolbarButtons,$newToolbarButtons);
		$toolbar = $this->addMiniToolbar($editToolbarButtons,$newToolbarButtons);
		
		// ms: here can the error Call to a member function getItem() on a non-object (e-mail from cs)
		// but if the $this->menu_item not exist we must get an error before see line 1438
		// function getContentItems is call from the extensions/pagetype
		$pageMenuItem = $this->menu_item->getItem($this->getPageId());
		
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		if($joomlaVersion < '1.6')
		{
			$menu_item_urlparams = $this->menu_item->getUrlParams();
			$menu_item_id = $menu_item_urlparams->get('id',null);
		
			$menu_item_view = $menu_item_urlparams->get('view',null);
			$menu_item_layout = $menu_item_urlparams->get('layout',null);
		}
		else
		{
			//dump($pageMenuItem );
			//dump($this->menu_item);
			if(isset($pageMenuItem->request['id']))
			{
				$menu_item_id = $pageMenuItem->request['id'];
			}
			elseif(isset($pageMenuItem->params['id']))
			{
				$menu_item_id = $pageMenuItem->params['id'];
			}
			else
			{
				$menu_item_id = 0;
			}

			if(isset($pageMenuItem->request['view']))
			{
				$menu_item_view = $pageMenuItem->request['view'];
			}
			elseif(isset($pageMenuItem->params['view']))
			{
				$menu_item_view = $pageMenuItem->params['view'];
			}
			else
			{
				$menu_item_view = null;
			}
			
			if(isset($pageMenuItem->request['layout']))
			{
				$menu_item_layout = $pageMenuItem->request['layout'];
			}
			elseif(isset($pageMenuItem->params['layout']))
			{
				$menu_item_layout = $pageMenuItem->params['layout'];
			}
			else
			{
				$menu_item_layout = null;
			}
		}
		
	
		if($menu_item_view == 'category')
		{
			$where[] = "c.catid='".$menu_item_id."'";
		}
		elseif($menu_item_view == 'section')
		{
			$where[] = "c.sectionid='".$menu_item_id."'";
		}
		
		if($menu_item_view == 'archive')
		{
			if($joomlaVersion < '1.6')
			{
				$where[] = "c.state='-1'";
			}
			else
			{
				$where[] = "c.state='2'";
			}
		}
		else
		{
			$where[] = "(c.state='0' OR c.state='1')";
		}
		
		/*
		here are the code from com_content to get frontpage
		$params = $this->state->params;
		$articleOrderby = $params->get('orderby_sec', 'rdate');
		$articleOrderDate = $params->get('order_date');
		$categoryOrderby = $params->def('orderby_pri', '');
		$secondary = ContentHelperQuery::orderbySecondary($articleOrderby, $articleOrderDate) . ', ';
		$primary = ContentHelperQuery::orderbyPrimary($categoryOrderby);

		$orderby = $primary . ' ' . $secondary . ' a.created DESC ';
		$this->setState('list.ordering', $orderby);
		$this->setState('list.direction', '');
		// Create a new query object.
		$query = parent::getListQuery();

		// Filter by frontpage.
		if ($this->getState('filter.frontpage'))
		{
			$query->join('INNER', '#__content_frontpage AS fp ON fp.content_id = a.id');
		}
		
		
		// Filter by categories
		if (is_array($featuredCategories = $this->getState('filter.frontpage.categories'))) {
			$query->where('a.catid IN (' . implode(',',$featuredCategories) . ')');
		}
		//follow three lines is in com_content/models/featured.php function populateState
		//// check for category selection
		//if (is_array($featuredCategories = $params->get('featured_categories'))) {
		//	$this->setState('filter.frontpage.categories', $featuredCategories);
		//}
		//i can not find an field/param: $params->get('featured_categories') in any com_content ore com_categories
		
		*/
		if($menu_item_view == 'frontpage' || $menu_item_view == 'featured')
		{
			$frontpage = "\n INNER JOIN #__content_frontpage AS f ON c.id=f.content_id ";
		}
		else
		{
			$frontpage = '';
		}
		
		if($joomlaVersion < '1.6')
		{
			$menu_item_advancedparams = $this->menu_item->getAdvancedParams();
			$orderByPri = $menu_item_advancedparams->get('orderby_pri',null);
			$orderBySec = $menu_item_advancedparams->get('orderby_sec',null);
			$orderBy = $menu_item_advancedparams->get('orderby',null);
		}
		else
		{
			/*
			# [string] orderby_pri = ""
			# [string] orderby_sec = "front"
			# [string] order_date = ""
			*/
			//$menu_item_advancedparams = $this->menu_item->getAdvancedParams();
			if(isset($pageMenuItem->params['orderby_pri']))
			{
				$orderByPri = $pageMenuItem->params['orderby_pri'];
			}
			else
			{
				$orderByPri = null;
			}
			
			if(isset($pageMenuItem->params['orderby_sec']))
			{
				$orderBySec = $pageMenuItem->params['orderby_sec'];
			}
			else
			{
				$orderBySec = null;
			}

			if(isset($pageMenuItem->params['orderby']) && $pageMenuItem->params['orderby'] != '')
			{
				$orderBy = $pageMenuItem->params['orderby'];
			}
			else
			{
				$orderBy = null;
			}
		}
		$ordering = false;
		if($orderBy)
		{
			$orderBySec = $orderBy;
		}
		else
		{
			switch($orderByPri)
			{
				case 'alpha':
					$order = "c.title ASC";
					$orderBySec = null;
				break;
				case 'ralpha':
					$order = "c.title DESC";
					$orderBySec = null;
				break;
				case 'order':
					$ordering = true;
				break;
				default:
				break;
			}
		}
		switch ($orderBySec) 
		{
			case 'date':
				$order = 'c.created ASC';
			break;
			case 'rdate':
				$order = 'c.created DESC';
			break;
			case 'alpha':
			default:
				$order = 'c.title ASC';
			break;
			case 'ralpha':
				$order = 'c.title DESC';
			break;
			case 'author':
				$order = 'u.username ASC';
			break;
			case 'rauthor':
				$order = 'u.username DESC';
			break;
			case 'hits':
				$order = 'c.hits ASC';
			break;
			case 'rhits':
				$order = 'c.hits DESC';
			break;
			case 'order':
				$order = 'c.ordering ASC';
				$ordering = true;
			break;
			case 'front':
				$order = 'c.ordering ASC';
				$ordering = true;
			break;
		}
		/*
		
		
		*/
		$where = (count($where) ? ' WHERE '.implode(' AND ', $where) : '');
		$query = "SELECT c.id, c.title, c.state, c.catid, i.itemtype, c.created_by, u.username "
		. "\nFROM #__content AS c "
		. $frontpage
		. "\nLEFT JOIN #__pi_item_index AS i ON c.id=i.item_id "
		. "\nLEFT JOIN #__users AS u ON u.id=c.created_by "
		. "\n $where "
		. "\nORDER BY $order ";
		$this->db->setQuery( $query );
		$rows = $this->db->loadObjectList();
		//hide the data on the page
		return $this->renderItems($html,$rows, $toolbar,$ordering);
	}
	
	/*
	 * @params mixed array||boolean $editToolbarButtons eg. array('new',delete'..) ore true for all
	 * @param mixed array||string $newToolbarButtons eg. array('new',delete'..) ore true for all
	*/
	function getContentItem($editToolbarButtons = true, $newToolbarButtons = true)
	{
		$pageMenuItem = $this->menu_item->getItem($this->getPageId());
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		if($joomlaVersion < '1.6')
		{
			$menu_item_urlparams = $this->menu_item->getUrlParams();
			$menu_item_id = $menu_item_urlparams->get('id',null);
		}
		else
		{
			//dump($pageMenuItem );
			//dump($this->menu_item);
			if(isset($pageMenuItem->request['id']))
			{
				$menu_item_id = $pageMenuItem->request['id'];
			}
			elseif(isset($pageMenuItem->params['id']))
			{
				$menu_item_id = $pageMenuItem->params['id'];
			}
			else
			{
				$menu_item_id = 0;
			}
		}
		//dump($menu_item_id);
		//dump($pageMenuItem->params);
		$html = '';
		//$html .= 'ccccccccccccccccccccc'.$pageMenuItem->id;
		$html .= '<div class="paddingList">';
			//itemtype select and button only display if an single Content typ and no id
			$this->getCanDo('com_content');
			if(!$menu_item_id && $this->canDo->com_content->get('core.create'))
			{
				$html .= $this->itemtype_select($this->getPageId());
				
				$html .= '<div class="pi_wrapper">';
					$html .= '<div class="line_top paddingList">';
					$html .= '</div>';
				$html .= '</div>';
				
			}
			//line_top
			//$html .= $this->addMiniToolbar($editToolbarButtons,$newToolbarButtons);
			$toolbar = $this->addMiniToolbar($editToolbarButtons,$newToolbarButtons);
			$this->db->setQuery( "SELECT c.id, c.state,c.title, c.catid, i.itemtype, c.created_by, u.username "
			. "\nFROM #__content AS c "
			. "\nLEFT JOIN #__pi_item_index AS i ON c.id=i.item_id "
			. "\nLEFT JOIN #__users AS u ON u.id=c.created_by "
			. "\nWHERE c.id='$menu_item_id' "
			//. "\nAND (c.state='0' OR c.state='1' ) " //
			);
			$rows = $this->db->loadObjectList();
		//return $this->renderItems($html,$rows,0);
		return $this->renderItems($html,$rows,$toolbar);
	}

	function renderItems($html, $rows, $toolbar, $ordering=0)
	{
		//get helper
		//include com_content helper
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'content.php');
		$ContentHelper = new ContentHelper;
		
		$imagePath = $this->dirIcons; //'components/com_pagesanditems/images/icons/';		
		$html .= '<div id="original_items" style="display: none;">';
		
		//headers
		$html .= '<div id="header_column_1"><strong>'.JText::_('COM_PAGESANDITEMS_TITLE').'</strong></div>';
		$html .= '<div id="header_column_2"><strong>'.JText::_('COM_PAGESANDITEMS_PUBLISHED').'</strong></div>';
		$html .= '<div id="header_column_3"><strong>'.JText::_('COM_PAGESANDITEMS_TYPE').'</strong></div>';
		
		$item_ids = array();
	
		//loop through items and echo data to hidden fields
		$counter = 0;
		foreach($rows as $row)
		{
			$item_ids[] = $row->id;	
			$content_creat_with = 'pi';
			$image = $imagePath.'base/icon-16-pi_black.png';
			$areThereItems = true;
			$counter = $counter + 1;
			$title = $row->title;
			$title = str_replace('"','&quot;',$title);
			$item_type = $row->itemtype;
			if($item_type=='content'){
				$item_type = 'text';
			}
			$pi_config = $this->getConfig();
			if($pi_config['truncate_item_title'])
			{
				$title = $this->truncate_string($title, $pi_config['truncate_item_title']);
			}
			
			if($item_type == '' )
			{
				$item_type = 'text';				
				$content_creat_with = 'joomla';
				$image = $imagePath.'base/icon-16-joomla_black.png';
			}		
			
			
			//TODO make $no_access as array so we can have more then one text
			$no_access = '';
			
			
			//check Joomla ACL
			//if user has no access to item, grey out the link and set no-access icon
			$acl = ContentHelper::getActions($row->catid, $row->id);
			if(!$acl->get('core.edit')){
				$no_access = JText::_('COM_PAGESANDITEMS_NO_PERMISSION_TO_EDIT_THIS_ITEM');
			}
			
			//check if itemtype is installed
			//comment for check is installed we need an simple query from #__extensions
			// als here we can get publish status 
			
			//the translate_item_type can not return the installed ore publish status
			$item_typename = $this->translate_item_type($item_type);
			//echo ' TRANS itemtype: '.$item_type.' TRANS  plugin_name: '.$item_typename.'</ br>';
			//if()
			if(!$this->checkItemTypeInstall($item_type))
			{
				$no_access = JText::_('COM_PAGESANDITEMS_ITEMTYPENOTINSTALLED2');
			}
			/*
			if($item_typename == '')
			{
				$no_access = JText::_('COM_PAGESANDITEMS_ITEMTYPENOTINSTALLED2');
			}
			*/
			//check if itemtype is published
			//COMMENT if we use extensions we check the publish state $item_typename?
			if(!in_array($item_type,$this->getItemtypes()))
			{
				$no_access = JText::_('COM_PAGESANDITEMS_ITEMTYPENOTPUBLISHED').' '.$item_type;
			}			
			
			
			//ms: move to other place?
			$html .='<input name="reorder_item_id_'.$counter.'" id="reorder_item_id_'.$counter.'" type="hidden" value="'.$row->id.'" />';
			
			if($no_access != '')
			{
				/*
				if($content_creat_with == 'joomla')
				{
					$image = $imagePath.'base/icon-16-joomla_black_no_access.png';
				}
				else
				{
					$image = $imagePath.'base/icon-16-pi_black_no_access.png';
				}
				*/
				$image = $imagePath.'base/icon-16-no_access.png';
				$image = $imagePath.'base/icon-16-no_access_slash_button.png';
				$image = $imagePath.'base/icon-16-no_access_slash.png';
				$image = $imagePath.'base/icon-16-no_access_slash_small.png';
			}
			/*
		$html = '';
		$html .= '<div>';
		$html .= '<input type="button" value="JText::_('COM_PAGESANDITEMS_DELETE_ITEM')(s)" class="button" title="JText::_('COM_PAGESANDITEMS_DELETE_ITEM')(s)" onclick="alert(\'for future\');" style="background: url('.$imagePath.'icon-16-trash.png) no-repeat #d4d0c8 3px 2px; margin-left: 10px;" />';
		
		$html .= '<input type="button" value="JText::_('COM_PAGESANDITEMS_CONVERT_TO_PI_ITEM')(s)" class="button" title="JText::_('COM_PAGESANDITEMS_CONVERT_TO_PI_ITEM')(s)" onclick="alert(\'for future\');" style="background: url('.$imagePath.'icon-16-pi_black.png) no-repeat #d4d0c8 3px 2px; margin-left: 10px;" />';

		$html .= '<input type="button" value="JText::_('COM_PAGESANDITEMS_CONVERT_TO_JOOMLA_ITEM')(s)" class="button" title="JText::_('COM_PAGESANDITEMS_CONVERT_TO_JOOMLA_ITEM')(s)" onclick="alert(\'for future\');" style="background: url('.$imagePath.'icon-16-joomla_black.png) no-repeat #d4d0c8 3px 2px; margin-left: 10px;" />';
		$html .= '</div>';
			
			*/
			//column 1
			$html .= '<div id="item_column_1_'.$counter.'">';
			if($no_access)
			{
				$html .= '<table style="border-spacing: 0px;">';
					$html .= '<tbody>';
						$html .= '<tr>';
							$html .= '<td style="vertical-align:middle;padding-bottom: 0;padding-top: 0;">';
								$html .= '<input disabled="disabled" type="checkbox" onclick="isCheckedItem(this.checked);" name="itemCid[]" value="'.$row->id.'" />';
							$html .= '</td>';
							$html .= '<td style=" vertical-align:middle;padding-bottom: 0;padding-top: 0;" colspan="1">';
								
									$html .= '<img src="'.$image.'" alt="'.$no_access.'" />';
								//$html .= '</span>&nbsp;';
							$html .= '</td>';
							//$html .= '<td style=" vertical-align:middle;">';
							$html .= '<td style=" vertical-align:middle;padding-bottom: 0;padding-top: 0;">';
								$html .= '<label class="hasTip" title="'.$no_access.'">';
									$html .= '<span class="editlinktip">';
										$html .= $title;
									$html .= '</span>';
								$html .= '</label>&nbsp;';
							$html .= '</td>';
						$html .= '</tr>';
					$html .= '</tbody>';
				$html .= '</table>';
				
			}
			else
			{
				
				$html .= '<table style="border-spacing: 0px;">';
					$html .= '<tbody>';
						$html .= '<tr>';
							$html .= '<td style=" vertical-align:middle;padding-bottom: 0;padding-top: 0;">';
								$html .= '<input type="checkbox" onclick="isCheckedItem(this.checked);" name="itemCid[]" id="itemCid_'.$row->id.'" value="'.$row->id.'" />';
							$html .= '</td>';
							$html .= '<td style="vertical-align:middle;padding-bottom: 0;padding-top: 0;">';
								$html .= '<span>';
									$html .= '<img src="'.$image.'" alt="'.$content_creat_with.'" />';
								$html .= '</span>';
							$html .= '</td>';
							$html .= '<td style=" vertical-align:middle;padding-bottom: 0;padding-top: 0;">';
								$html .= '<a href="index.php?option=com_pagesanditems&view=item&sub_task=edit&pageId='.$this->getPageId().'&itemId='.$row->id.'&item_type='.$item_type.'">'.$title.'</a>';
							//$html .= '</span>';
							//$html .= '</span>&nbsp;';
							$html .= '</td>';
						$html .= '</tr>';
					$html .= '</tbody>';
				$html .= '</table>';
			}
			/*
				$itemCid = $_GET['itemCid'];
				$itemCid_text = implode(', ',$itemCid);
				the way to get the array cid
			*/
			//$html .='<input name="reorder_item_id_'.$counter.'" id="reorder_item_id_'.$counter.'" type="hidden" value="'.$row->id.'" />';
			
			$html .= '</div>';
			
			//column 2
			$html .= '<div id="item_column_2_'.$counter.'">';
			if($row->state){
				$alt = JText::_('COM_PAGESANDITEMS_UNPUBLISH');
				$image = 'tick';
				$new_state = '0';
			}else{
				$alt = JText::_('COM_PAGESANDITEMS_UNPUBLISH');
				$image = 'cross';
				$new_state = '1';
			}
			
			/*
			ms: i have replace so also trash and archiv as icon 
			$html .= '<a href="javascript:publish_unpublish_item('.$row->id.','.$new_state.');">';
			$html .= '<img src="components/com_pagesanditems/media/images/icons/base/icon-16-'.$image.'.png" alt="'.$alt.'" />';
			$html .= '</a>';
			*/
			
			
					
					switch($row->state)
					{
						case '1':
							//$state = 'published';
							$state = '<span class="state publish"></span>';
							$title = 'title="'.JText::_('JLIB_HTML_UNPUBLISH_ITEM').'"';
							$alt = JText::_('COM_PAGESANDITEMS_UNPUBLISH');
							$image = 'tick';
							$new_state = '0';
							$onclick = 'onclick="publish_unpublish_item('.$row->id.','.$new_state.');"';
						break;

						case '0':
							//$state = 'unpublished';
							$state = '<span class="state unpublish"></span>';
							$title = 'title="'.JText::_('JLIB_HTML_PUBLISH_ITEM').'"';
							$alt = JText::_('COM_PAGESANDITEMS_PUBLISH');
							$image = 'cross';
							$new_state = '1';
							$onclick = 'onclick="publish_unpublish_item('.$row->id.','.$new_state.');"';
						break;
					
						case '2':
							//$state = 'archive';
							$state = '<span class="state archive"></span>';
							$title = 'title="'.JText::_('JLIB_HTML_PUBLISH_ITEM').'"';
							$alt = JText::_('COM_PAGESANDITEMS_PUBLISH');
							$image = 'archive';
							$new_state = '1';
							$onclick = 'onclick="publish_unpublish_item('.$row->id.','.$new_state.');"';
						break;
		
						case '-1':
							//$state = 'archive';
							$state = '<span class="state archive"></span>';
							$title = 'title="'.JText::_('JLIB_HTML_PUBLISH_ITEM').'"';
							$alt = JText::_('COM_PAGESANDITEMS_PUBLISH');
							$image = 'archive';
							$new_state = '1';
							$onclick = 'onclick="publish_unpublish_item('.$row->id.','.$new_state.');"';
						break;
						case '-2':
							//$state = 'trash';
							$state = '<span class="state trash"></span>';
							$title = 'title="'.JText::_('JLIB_HTML_PUBLISH_ITEM').'"';
							$alt = JText::_('COM_PAGESANDITEMS_PUBLISH');
							$image = 'trash';
							$new_state = '1';
							$onclick = 'onclick="publish_unpublish_item('.$row->id.','.$new_state.');"';
						break;
					}
					/*
					ms: the next lines use the core images one as onclick one as href i dont like the icons from the core template but i think is it better for the user
					but i must fin an way if we use the core icons we can use it also on the buttons
					and if we use the pi icons we must replace the toolbar (bigger) buttons with the pi icons
					
					I have import in pagesanditems.css an additional css for the icons so we can override some core css
					and get the images we want if you want to use the core images comment out the import in pagesanditems.css
					
					*/
					
					$hand = 'pi_hand';
					//Joomla ACL					
					if(!$acl->get('core.edit.state')){
						$onclick = '';
						$title = 'title="'.JText::_('COM_PAGESANDITEMS_NO_PERMISSION_TO_EDIT_THE_STATE').'"';
						$hand = '';
					}
					
					
					$html .= ' <a class="jgrid hasTip '.$hand.'" '.$title.' '.$onclick.'>'.$state.'</a>';
					
					//ms: hasTip and href make some curios in tooltip
					//$html .= ' <a class="jgrid hasTip" '.$title.' href="publish_unpublish_item('.$row->id.','.$new_state.');"> '.$state.'</a>';
					/*
					$html .= '<a class="hasTip" '.$title.' onclick="javascript:publish_unpublish_item('.$row->id.','.$new_state.');">';
					//$html .= '<a href="javascript:publish_unpublish_item('.$row->id.','.$new_state.');">';
					
					$html .= '<img src="components/com_pagesanditems/media/images/icons/base/icon-16-'.$image.'.png" alt="'.$alt.'" />';
					$html .= '</a>';
					*/
		
		
		
	
			
			
			
			$html .= '</div>';
			
			//column 3
			$html .= '<div id="item_column_3_'.$counter.'">';
			$html .= $item_typename.'</div>';
			
				
		}
		$html .= '</div>';
	
		if($counter > 1)
		{
			$html .= '<fieldset style="padding:0px;margin:0px;" class="adminform">';
			//class="pane-toggler
			$html .= '<legend id="toggle_items" class="pane-toggler-down" style="padding-left:4px;padding-right:4px;margin-left:10px;border-bottom:0;">';
				$html .= '<a onclick="javascript:toggelItems();">';
				$html .= JText::_('COM_PAGESANDITEMS_ITEMS');
				$html .= '<span></span>';
				$html .= '</a>';
				
			$html .= '</legend>';
		}
		$html .= '<div id="target_items_actions">';
		if($toolbar)
		$html .= $toolbar;
		
		$html .= '</div>';
		//2 hidden fields which are usefull for updating the ordering when submitted
		$html .= '<input name="items_are_reordered" id="items_are_reordered" type="hidden" value="false" />';
		$html .= '<input name="items_total" id="items_total" type="hidden" value="'.$counter.'" />';
		
		$html .= '<div id="target_items"></div>';
		if($counter > 1)
		{
			$html .= '</fieldset>';
		}
		$html .= '<script src="components/com_pagesanditems/javascript/reorder_items.js" language="JavaScript" type="text/javascript"></script>';
		$html .= '<script language="JavaScript"  type="text/javascript">';
		$html .= "<!--\n";
		$html .= "function toggelItems() {\n";
		$html .= "	if(document.id('toggle_items').hasClass('pane-toggler-down')){\n";
		$html .= "	document.id('toggle_items').addClass('pane-toggler')\n";
		$html .= "	document.id('toggle_items').removeClass('pane-toggler-down')\n";
		$html .= "	document.id('target_items').setProperty('style','display:none;')\n";
		$html .= "	document.id('target_items_actions').setProperty('style','display:none;')\n";
		//
		$html .= "	}\n";
		$html .= "	else{\n";
		$html .= "	document.id('toggle_items').addClass('pane-toggler-down')\n";
		$html .= "	document.id('toggle_items').removeClass('pane-toggler')\n";
		$html .= "	document.id('target_items').setProperty('style','display:block;')\n";
		$html .= "	document.id('target_items_actions').setProperty('style','display:block;')\n";
		$html .= "	}\n";
		$html .= "}\n";
		
		$html .= "var items_total = ".$counter.";\n";
		$html .= "var joomlaVersion = '".$this->joomlaVersion."';\n";
		$html .= "var number_of_columns = '3';\n";
		$html .= "var ordering = '".JText::_('COM_PAGESANDITEMS_ORDERING')."';\n";
		$html .= "var no_items = '".JText::_('COM_PAGESANDITEMS_NOITEMSONTHISPAGE')."';\n";
		
		
		$html .= "var item_ids = new Array(";
		$first = 1;
		foreach($item_ids as $item_ids_item){
			if(!$first){
				$html .= ",";
			}
			$html .= "'".$item_ids_item."'";			
			$first = 0;
		}
		$html .= ");\n";
		
		
		//check what sort of item ordering and if not by ordering hide arrows
		if(!$ordering)
		{
			$html .= "var hide_arrows = true;\n";
			
		}
		$html .= "document.onload = print_items();\n";

		$html .= "function isCheckedItem(isitchecked) {
	if (isitchecked == true) {
		
		document.adminForm.boxcheckedItem.value++;
	} else {
		document.adminForm.boxcheckedItem.value--;
	}
}\n";
		$html .= "-->\n";
		$html .= "</script>\n";
		$html .= '<input type="hidden" name="boxcheckedItem" id="boxcheckedItem" value="0" />';
		$html .= '</div>';
		return $html;
	}

	function getPagePropertys()
	{
		
		$sub_task = JRequest::getVar( 'sub_task', 'edit');
		$this->getMenuItem();
		$html = '';
		if(($this->pageId || $sub_task == 'new') && ($this->pageMenuItem || $this->menu_item))
		{
			//at this moment in J1.6 to many errors
			if($this->joomlaVersion < '1.6')
			{
				$html = $this->getFormPagePropertys();
			}
			else
			{
				//dump($this->pageMenuItem);
				//dump($this->pageMenuItem->request['option']);
				//dump($this->menu_item->getForm());//$this->pageMenuItem
				//print_r($this->menu_item);
				$this->form = $this->menu_item->getForm();
				$this->modules = $this->menu_item->getModules();
				//dump($this->menu_item->getModules());
				//dump($this->menu_item->getForm());
				
				//dump($this->menu_item->getItem());
				$html = $this->getFormPagePropertys();
			}
		}
		return $html;
		
	}	

	function getFormPagePropertys()
	{
		$html = '';
		$sub_task = JRequest::getVar( 'sub_task', 'edit');
		$lang = &JFactory::getLanguage();
	
		$menu_item = $this->menu_item;
		//print_r($menu_item);
		if($this->joomlaVersion < '1.6')
		{
			$menu_item_component = $menu_item->getComponent();
			$lang->load($menu_item_component->option, JPATH_ADMINISTRATOR);
			$menu_item_urlparams		= $menu_item->getUrlParams();
			$menu_item_params			= $menu_item->getStateParams();
			$menu_item_systemparams		= $menu_item->getSystemParams();
			$menu_item_advancedparams	= $menu_item->getAdvancedParams();
			$menu_item_componentparams	= $menu_item->getComponentParams();
		
			$menu_item_name			= $menu_item->getStateName();
			$menu_item_description		= $menu_item->getStateDescription();
		
			$menu_menuTypes 			= MenusHelper::getMenuTypeList();
			$menu_components			= MenusHelper::getComponentList();
		}
		else
		{
			if(isset($this->pageMenuItem->request['option']))
			$lang->load($this->pageMenuItem->request['option'], JPATH_ADMINISTRATOR);
			
			$menu_item_name = $this->pageMenuItem->title; //must change tothe ???
			$menu_item_description = $this->form->getInput('type');
			$menu_item_parent_id = $this->form->getInput('parent_id');
			
			/*
			
							<li><?php echo $this->form->getLabel('type'); ?>
				<?php echo $this->form->getInput('type'); ?></li>

				<li><?php echo $this->form->getLabel('title'); ?>
				<?php echo $this->form->getInput('title'); ?></li>

				<?php if ($this->item->type =='url'): ?>
					<?php $this->form->setFieldAttribute('link','readonly','false');?>
					<li><?php echo $this->form->getLabel('link'); ?>
					<?php echo $this->form->getInput('link'); ?></li>
				<?php endif ?>
			
			
			//$menu_item_component 		= $menu_item->getComponent();
			

			
			$menu_item_urlparams		= $menu_item->getUrlParams();
			$menu_item_params			= $menu_item->getStateParams();
			$menu_item_systemparams		= $menu_item->getSystemParams();
			$menu_item_advancedparams	= $menu_item->getAdvancedParams();
			$menu_item_componentparams	= $menu_item->getComponentParams();
		
			$menu_item_name				= $menu_item->getStateName();
			$menu_item_description		= $menu_item->getStateDescription();
		
			$menu_menuTypes 			= MenusHelper::getMenuTypes();
			$menu_components			= MenusHelper::getComponentList();
			
			
			
			
			
			*/
			//$menu_item = $this->pageMenuItem; //menuItemsType;
			//
			//$lang->load($menu_item_component->option, JPATH_ADMINISTRATOR);
			//$menu_item_description = ''; //$menu_item->description;
		}
		$buttonLinkMenutype = '';
		$buttonLinkMenutype .= '<div>';
			//here we can set an select type to change se type?
			$buttonLinkMenutype .= $menu_item_description;
		$buttonLinkMenutype .= '</div>';
		$this->getLists();
		//TODO $this->getPageTypeHtml
		$html .= '<script language="JavaScript" type="text/javascript">';
			$html .= '<!--';
			$html .= 'function popupPageBrowser(url)';
			$html .= '{';
				$html .= 'var winl = (screen.width - 400) / 2;';
				$html .= 'var wint = (screen.height - 400) / 2;';
				$html .= "winprops = 'height=400,width=400,top='+wint+',left='+winl+',scrollbars=yes,resizable';";
				$html .= "linkValue = document.getElementById('link').value;";
				$html .= 'linkValue = escape(linkValue);';
				$html .= "urlString = url+'&url='+linkValue;";
				$html .= "win = window.open(urlString, 'pages', winprops);";
				$html .= 'if (parseInt(navigator.appVersion) >= 4)';
				$html .= '{';
					$html .= 'win.window.focus();';
				$html .= '}';
			$html .= '}';
			$html .= '-->';
		$html .= '</script>';
		
		//if($this->pageMenuItem && $this->menuItemsTypes)
		if($this->pageMenuItem && $this->menuItemsType)
		{
			$menuItemsType = $this->menuItemsType;
			$image = false;
			$imageNew = false;
			$imageEdit = false;
			$imageBulletNew = '';
			$imageBulletEdit = '';
			if(isset($menuItemsType->icons->default->imageUrl))
			{
				$image = $menuItemsType->icons->default->imageUrl;
			}
			if(isset($menuItemsType->icons->new->imageUrl))
			{
				$imageNew = $menuItemsType->icons->new->imageUrl;
			}
			else
			{
				$imageNew = $image;
				$imageBulletNew = $this->dirIcons.'base/bullets/icon-16-bullet_new.png';
			}
			if(isset($menuItemsType->icons->edit->imageUrl))
			{
				$imageEdit = $menuItemsType->icons->edit->imageUrl;
			}
			else
			{
				$imageEdit = $image;
				$imageBulletEdit = $this->dirIcons.'base/bullets/icon-16-bullet_edit.png';
			}
			if($sub_task=='new')
			{
				if($imageNew)
				{
					$bevore = '';
					$after = '';
					//COMMENT TODO only test for add bullets to image
					if($imageBulletNew !='')
					{
						$bevore = '<div>';
							$imageBulletNew = '<img src="'.$imageBulletNew.'" alt="" style="float: left;left: 0;position: absolute;vertical-align: middle;z-index: 101;" />&nbsp;';
						$after = '</div>';
					}
					$imageDisplay = $bevore;
					//echo '<img src="'.$this->dirIcons.'icon-16-menu.png" alt="" style="vertical-align: middle;float: left;" />&nbsp;';
					$imageDisplay .= '<div style="margin-left: 4px;float: left;left: 0;position: relative;vertical-align: middle;">';
						$imageDisplay .= '<img src="'.$imageNew.'" alt="" style="vertical-align: middle;position: relative;" />&nbsp;';
						
						
						$imageDisplay .= $imageBulletNew;
					$imageDisplay .= '</div>';					
					//$imageDisplay .= JText::_('COM_PAGESANDITEMS_NEW_PAGE').' ( '.$menu_item_name.' )';
					$imageDisplay .= JText::_('COM_PAGESANDITEMS_NEW_PAGE');
					$imageDisplay .= $after;
				}
				else
				{
					$imageDisplay ='<img src="'.$this->dirIcons.'icon-16-menu.png" alt="" style="vertical-align: middle;" />&nbsp;';
					//$imageDisplay .= JText::_('COM_PAGESANDITEMS_NEW_PAGE').' ( '.$menu_item_name.' )';
					$imageDisplay .= JText::_('COM_PAGESANDITEMS_NEW_PAGE');
				}
			}
			else
			{
				if($imageEdit)
				{
					$bevore = '';
					$after = '';
					//COMMENT TODO only test for add bullets to image
					if($imageBulletEdit !='')
					{
						$bevore = '<div>';
							$imageBulletEdit = '<img src="'.$imageBulletEdit.'" alt="" style="float: left;left: 0;position: absolute;vertical-align: middle;z-index: 101;" />&nbsp;';
						$after = '</div>';
					}
					$imageDisplay = $bevore;
					//echo '<img src="'.$this->dirIcons.'icon-16-menu.png" alt="" style="vertical-align: middle;float: left;" />&nbsp;';
					$imageDisplay .= '<div style="margin-left: 4px;float: left;left: 0;position: relative;vertical-align: middle;">';
					
						$imgClass = explode("class:",$imageEdit);
						if(count($imgClass) && count($imgClass) == 2)
						{
							//we have an class
							$imageDisplay .= '<a ';
							$imageDisplay .= 'class="icon '.$imgClass[1].'" ';
							$imageDisplay .= 'alt="" >';
							$imageDisplay .= '<span>&nbsp;';
							$imageDisplay .= '</span>';
							$imageDisplay .= '</a>';
						}
						else
						{
					
						$imageDisplay .= '<img src="'.$imageEdit.'" alt="" style="vertical-align: middle;position: relative;" />&nbsp;';
						}
					
						$imageDisplay .= $imageBulletEdit;
					$imageDisplay .= '</div>';
					//echo JText::_('COM_PAGESANDITEMS_UNDERLYING_PAGES');
					$imageDisplay .= JText::_('COM_PAGESANDITEMS_PAGE_PROPERTIES').' ( '.$menu_item_name.' )';
					$imageDisplay .= $after;
				}
				else
				{
					$imageDisplay ='<img src="'.$this->dirIcons.'icon-16-menu.png" alt="" style="vertical-align: middle;" />&nbsp;';
					$imageDisplay .= JText::_('COM_PAGESANDITEMS_PAGE_PROPERTIES').' ( '.$menu_item_name.' )';
				}
			}
		}
		else
		{
			if($sub_task=='new')
			{
				$imageDisplay ='<img src="'.$this->dirIcons.'icon-16-menu.png" alt="" style="vertical-align: middle;" />&nbsp;';
				$imageDisplay .= JText::_('COM_PAGESANDITEMS_NEW_PAGE').' ( '.$menu_item_name.' )';
			}
			else
			{
				$imageDisplay ='<img src="'.$this->dirIcons.'icon-16-menu.png" alt="" style="vertical-align: middle;" />&nbsp;';
				$imageDisplay .= JText::_('COM_PAGESANDITEMS_PAGE_PROPERTIES').' ( '.$menu_item_name.' )';
			}
		}
		
		
		
		$html .='<table class="adminform" width="98%">';
			$html .='<tr>';
				$html .='<th style="background: none repeat scroll 0 0 #F0F0F0;border-bottom: 1px solid #999999;">';
					 //TODO image from pageType if exist
					/*
					if($sub_task=='new')
					{
						echo '<img src="'.$this->dirIcons.'icon-16-category.png" alt="" style="vertical-align: middle;" />&nbsp;';
						echo JText::_('COM_PAGESANDITEMS_NEW_PAGE');
					}
					else
					{
							echo '<img src="'.$this->dirIcons.'icon-16-category.png" alt="" style="vertical-align: middle;" />&nbsp;'; //TODO image from this->pageMenuItem->pageType
						echo JText::_('COM_PAGESANDITEMS_PAGE_PROPERTIES');
					}
					*/
					//echo ' ( '.$menu_item_name.' )';
					$html .= $imageDisplay;

				$html .='</th>';
			$html .='</tr>';
			$html .='<tr>';
				$html .='<td>';
				/*
				ok different J1.5 and J1.6
				*/
				if($this->joomlaVersion < '1.6')
				{
					$html .='<table border="0" cellspacing="0" cellpadding="0" class="paramlist" width="98%"'.$this->lists->hideAll.' >';
					
					$html .='<!-- $this->lists->display-> -->';
					
					$html .='<tr id="menu_item_id"'.$this->lists->display->id.' >';
						$html .='<td class="key" width="20%" align="right">';
							$html .= JText::_( 'ID' ).': ';
						$html .='</td>';
						if ($this->pageMenuItem->id)
						{
							$html .='<td width="80%">';
								$html .='<strong>'.$this->pageMenuItem->id.'</strong>';
							$html .='</td>';
						}
						$html .= $this->lists->add->top;
					$html .='</tr>';
						
					$html .='<tr id="menu_item_title"'.$this->lists->display->title.' >';
						$html .='<td class="key" align="right">';
							$html .= JText::_( 'Title' ).': ';
						$html .='</td>';
						$html .='<td>';
							$html .='<input class="inputbox" type="text" name="name" size="150" maxlength="255" value="'.$this->pageMenuItem->name.'" />';
						$html .='</td>';
					$html .='</tr>';
						
					$html .='<tr id="menu_item_alias">';
						$html .='<td class="key" align="right">';
							$html .= JText::_( 'Alias' ).': ';
						$html .='</td>';
						$html .='<td>';
							$html .='<input class="inputbox" type="text" name="alias" size="150" maxlength="255" value="'.$this->pageMenuItem->alias.'" />';
						$html .='</td>';
					$html .='</tr>';
						
					$html .='<tr id="menu_item_link">';
						$html .='<td class="key" align="right">';
							$html .= JText::_( 'Link' ).': ';
						$html .='</td>';
						$html .='<td>';
							//$html .='<input class="inputbox" type="text" name="link" size="150" maxlength="255" value="'.$this->pageMenuItem->link.'" $html .= $this->lists->disabled->link.' />';
								if($this->joomlaVersion < '1.6')
								{
									if ($this->pageMenuItem->type =='url')
									{
									//$html .='<input class="inputbox" type="text" name="link" size="150" maxlength="255" value="'.$this->pageMenuItem->link.'" $html .= $this->lists->disabled->link.' />';
									}
									else
									{
									
									}
								}
								else
								{
									if ($this->pageMenuItem->type =='url')
									{
										$this->form->setFieldAttribute('link','readonly','false');
									}
									/*<li><?php echo $this->form->getLabel('link'); ?>*/
									$html .= $this->form->getInput('link');
								}
							
							$html .= '</td>';
						$html .= '</tr>';
						$html .= '<tr style="display: none;visibility: hidden;">';
							$html .= '<td class="key" align="right">';
								$html .= JText::_( 'Display in' ).': ';
							$html .= '</td>';
							$html .= '<td>';
								if($this->joomlaVersion < '1.6')
								{
									$menuTypes = MenusHelper::getMenuTypeList();
								}
								else
								{
									$menuTypes = MenusHelper::getMenuTypes();
								}
								$html .= JHTML::_('select.genericlist', $menuTypes, 'menutype', 'class="inputbox" size="1"', 'menutype', 'title', $this->pageMenuItem->menutype );
							$html .= '</td>';
						$html .= '</tr>';
						$html .= '<tr style="display: none;visibility: hidden;">';
							$html .= '<td class="key" align="right" valign="top">';
								$html .= JText::_( 'Parent Item' ).': ';
							$html .= '</td>';
							$html .= '<td>';
								if($this->joomlaVersion < '1.6')
								{
									$html .= MenusHelper::Parent( $this->pageMenuItem );
								}
								else
								{
									$html .= $menu_item_parent_id;
								}
							$html .= '</td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td class="key" valign="top" align="right">';
								$html .= JText::_( 'Published' ).': ';
							$html .= '</td>';
							$html .= '<td>';
								$html .= $this->lists->published;
							$html .= '</td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td class="key" valign="top" align="right">';
								$html .= JText::_('DEFAULT').' '.JText::_( 'MENU ITEM' ).': ';
							$html .= '</td>';
							$html .= '<td>';
								$html .= $this->lists->home; //'todo defaultPage';//$this->lists->published
							$html .= '</td>';
						$html .= '</tr>';
						$html .= '<tr style="display: none;visibility: hidden;">';
							$html .= '<td class="key" valign="top" align="right">';
								$html .= JText::_( 'Ordering' ).': ';
							$html .= '</td>';
							$html .= '<td>';
								$html .= JHTML::_('menu.ordering', $this->pageMenuItem, $this->pageMenuItem->id );
							$html .= '</td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td class="key" valign="top" align="right">';
								$html .= JText::_( 'Access Level' ).': ';
							$html .= '</td>';
							$html .= '<td>';
								$html .= JHTML::_('list.accesslevel',  $this->pageMenuItem );
							$html .= '</td>';
						$html .= '</tr>';
						if ($this->pageMenuItem->type != "menulink")
						{
						$html .= '<tr>';
							$html .= '<td class="key" valign="top" align="right">';
								$html .= JText::_( 'On Click, Open in' ).': ';
							$html .= '</td>';
							$html .= '<td>';
								if($this->joomlaVersion < '1.6')
								{
									$html .= MenusHelper::Target( $this->pageMenuItem );
								}
								else
								{
									$html .= $this->form->getInput('browserNav');
									/*<li><?php echo $this->form->getLabel('browserNav'); ?>
									<?php echo 
									$this->form->getInput('browserNav');
									 ?></li>
									*/
								}
								
							$html .= '</td>';
						$html .= '</tr>';
						}
						$html .= '<tr>';
							$html .= '<td colspan="2" style="line-height: 3px; height: 3px;">&nbsp;';
							$html .= '</td>';
						$html .= '</tr>';
			
						$html .= '<tr>';
							$html .= '<td valign="top">';
								$html .= '<label class="hasTip" title="'.JText::_('COM_PAGESANDITEMS_MENUTYPE_TIP').'">';
									$html .= '<span class="editlinktip" >';
										$html .= JText::_('COM_PAGESANDITEMS_MENUTYPE');
									$html .= '</span>';
								$html .= '</label>';
							$html .= '</td>';
							$html .= '<td>';
								//only display the menutype == $menu_item_description;
								$html .= $buttonLinkMenutype; 
								//$html .= '<br>';
							$html .= '</td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<!-- Menu Item Parameters Section-->';
							$html .= '<td>';
								$html .= '<!-- Menu Item Parameters Section blank column-->';
							$html .= '</td>';
							$html .= '<td >';
								$html .= '<!-- Menu Item Parameters Section content-->';
								if($this->joomlaVersion < '1.6')
								{
								jimport('joomla.html.pane');
								$pane = &JPane::getInstance('sliders', array('allowAllClose' => true));
								$html .= $pane->startPane("menu-pane");
								$html .= '<div '.$this->lists->display->params.' >';
									$html .= $pane->startPanel(JText :: _('Parameters - Basic'), "param-page");
									$html .= $menu_item_urlparams->render('urlparams');
									if(count($menu_item_params->getParams('params'))) 
									{
										$html .= $menu_item_params->render('params');
									}
					
									if(!count($menu_item_params->getNumParams('params')) && !count($menu_item_urlparams->getNumParams('urlparams')))
									{
										$html .= '<div style="text-align: center; padding: 5px; ">';
											$html .= JText::_('There are no parameters for this item');
										$html .= '</div>';
									}
								$html .= $pane->endPanel();
					
								$html .= '</div>';
								if($params = $menu_item_advancedparams->render('params'))
								{
									$html .= $pane->startPanel(JText :: _('Parameters - Advanced'), "advanced-page");
									$html .= $params;
									$html .= $pane->endPanel();
								}
								if ($menu_item_componentparams && ($params = $menu_item_componentparams->render('params')))
								{
									$html .= $pane->startPanel(JText :: _('Parameters - Component'), "component-page");
									$html .= $params;
									$html .= $pane->endPanel();
								}
								if ($menu_item_systemparams && ($params = $menu_item_systemparams->render('params')))
								{
									$html .= $pane->startPanel(JText :: _('Parameters - System'), "system-page");
									$html .= $params;
									$html .= $pane->endPanel();
								}
								$html .= $pane->endPane();
								}
								else
								{
									$html .= '<div class="width-100 fltlft">';
										$html .= JHtml::_('sliders.start','menu-sliders-'.$this->pageMenuItem->id);
											
											//$html .= $this->loadTemplate('options');
											$fieldSets = $this->form->getFieldsets('request');
											if (!empty($fieldSets)) 
											{
												
												$fieldSet = array_shift($fieldSets);
												$label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_MENUS_'.$fieldSet->name.'_FIELDSET_LABEL';
												$html .=  JHtml::_('sliders.panel',JText::_($label), 'request-options');
												if (isset($fieldSet->description) && trim($fieldSet->description)) :
													//$html .= '<p class="tip">'.$this->escape(JText::_($fieldSet->description)).'</p>';
													$html .= '<p class="tip">'.JText::_($fieldSet->description).'</p>';
												endif;

												$html .= '<fieldset class="panelform">';
													$hidden_fields = '';
													$html .= '<ul class="adminformlist">';
													foreach ($this->form->getFieldset('request') as $field)
													{
														if (!$field->hidden)
														{
															$html .= '<li>';
																$html .= $field->label;
																$html .= $field->input;
															$html .= '</li>';
														}
														else
														{
															$hidden_fields.= $field->input;
														}
													}
													$html .= '</ul>';
													$html .= $hidden_fields;;
												$html .= '</fieldset>';
											}
											$fieldSets = $this->form->getFieldsets('params');
											foreach ($fieldSets as $name => $fieldSet)
											{
												$label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_MENUS_'.$name.'_FIELDSET_LABEL';
												$html .= JHtml::_('sliders.panel',JText::_($label), $name.'-options');
													if (isset($fieldSet->description) && trim($fieldSet->description))
													{
														//$html .= '<p class="tip">'.$this->escape(JText::_($fieldSet->description)).'</p>';
														$html .= '<p class="tip">'.JText::_($fieldSet->description).'</p>';
													}
													$html .= '<fieldset class="panelform">';
														$html .= '<ul class="adminformlist">';
														foreach ($this->form->getFieldset($name) as $field)
														{
															$html .= '<li>';
																$html .= $field->label;
																$html .=  $field->input;
															$html .= '</li>';
														}
														$html .= '</ul>';
													$html .= '</fieldset>';
											}
											$html .= '<div class="clr"></div>';
											if (!empty($this->modules))
											{
												$html .=  JHtml::_('sliders.panel',JText::_('COM_MENUS_ITEM_MODULE_ASSIGNMENT'), 'module-options');
													$html .= '<fieldset>';
															$html .= '<table class="adminlist">';
																$html .= '<thead>';
															$html .= '<tr>';
																	$html .= '<th class="left">';
																		$html .= JText::_('COM_MENUS_HEADING_ASSIGN_MODULE');
																	$html .= '</th>';
																	$html .= '<th>';
																		$html .= JText::_('COM_MENUS_HEADING_DISPLAY');
																	$html .= '</th>';
																$html .= '</tr>';
																$html .= '</thead>';
																$html .= '<tbody>';
																foreach ($this->modules as $i => &$module)
																{
																	$html .= '<tr class="row<?php echo $i % 2;?>">';
																		$html .= '<td>';
																			$link = 'index.php?option=com_modules&amp;client_id=0&amp;task=module.edit&amp;id='. $module->id.'&amp;tmpl=component&amp;view=module&amp;layout=modal' ;
																			$html .= '<a class="modal" href="'. $link.'" rel="{handler: \'iframe\', size: {x: 900, y: 550}}" title="'.JText::_('COM_MENUS_EDIT_MODULE_SETTINGS').'">';
																				$html .= JText::sprintf('COM_MENUS_MODULE_ACCESS_POSITION', $this->view->escape($module->title), $this->view->escape($module->access_title), $this->view->escape($module->position));
																				//$html .= JText::sprintf('COM_MENUS_MODULE_ACCESS_POSITION', $module->title, $module->access_title, $module->position);
																				$html .='</a>';

																		$html .= '</td>';
																		$html .= '<td class="center">';
																		if (is_null($module->menuid))
																		{
																			$html .= JText::_('JNONE');
																		}
																		elseif ($module->menuid != 0)
																		{
																			$html .= JText::_('COM_MENUS_MODULE_SHOW_VARIES');
																		}
																		else
																		{
																			$html .= JText::_('JALL');
																		}
																		$html .= '</td>';
																	$html .= '</tr>';
																}
																$html .= '</tbody>';
															$html .= '</table>';
														
														//$html .= $this->loadTemplate('modules');
												$html .= '</fieldset>';
											}
											$html .= JHtml::_('sliders.end');
											$html .= '<input type="hidden" name="task" value="" />';
											$html .= $this->form->getInput('component_id');
											$html .=  JHtml::_('form.token');
									$html .= '</div>';
									$html .= '<input type="hidden" id="fieldtype" name="fieldtype" value="" />';
								}
								$html .= '<!-- END Menu Item Parameters Section-->';
							$html .= '</td>';
						$html .= '</tr>';
						$html .= $this->lists->add->bottom;
						$html .= $this->pageMenuItem->linkfield;
						//replace with 
						$html .= $this->lists->pageType->html;
						//echo $this->lists->pageTypeClass->html;
						$html .= '<input type="hidden" name="id" value="'.$this->pageMenuItem->id.'" />';
						if($this->joomlaVersion < '1.6')
						{
							$html .= '<input type="hidden" name="componentid" value="'.$this->pageMenuItem->componentid.'" />';
						}
						else
						{
							$html .= '<input type="hidden" name="component_id" value="'.$this->pageMenuItem->component_id.'" />';
						}
						$html .= '<input type="hidden" name="type" value="'.$this->pageMenuItem->type.'" />';
					$html .= '</table>';
					}
					else
					{
						//J1.6
						//$html .='<table border="0" cellspacing="0" cellpadding="0" class="paramlist" width="98%"'.$this->lists->hideAll.' >';

						//$html .= $this->lists->add->top;
						JHtml::_('behavior.tooltip');
						JHtml::_('behavior.formvalidation');
						JHTML::_('behavior.modal');
						
						//ms: need? 
						/*
						$html .='<table border="0" cellspacing="0" cellpadding="0" class="paramlist" width="98%" >';
							$html .= $this->lists->add->top;
						$html .= '</table>';
						*/
						
						$html .='<!-- $this->lists->display-> -->';

						$html .='<div class="width-60 fltlft">';
							$html .='<fieldset class="adminform">';
								$html .='<legend>'.JText::_('COM_MENUS_ITEM_DETAILS').'</legend>';

									$html .='<ul class="adminformlist">';
										//do not display when new 
										//echo '$this->pageMenuItem->id'.$this->pageMenuItem->id;
										if($this->pageMenuItem->id){
											$html .='<li '.$this->lists->display->id.'>'.$this->form->getLabel('id');
											$html .= $this->form->getInput('id').'</li>';
										}
										//$this->form->setFieldAttribute('link','readonly','false');
										$this->form->setFieldAttribute('type', 'type', 'pimenutype');
										
										$html .='<li>'.$this->form->getLabel('type');
										$html .= $this->form->getInput('type').'</li>';

										$html .='<li>'.$this->form->getLabel('title');
										$html .= $this->form->getInput('title').'</li>';

										if ($this->pageMenuItem->type =='url'):
											$this->form->setFieldAttribute('link','readonly','false');
											$html .='<li>'.$this->form->getLabel('link');
											$html .= $this->form->getInput('link').'</li>';
										endif;

										$html .='<li>'.$this->form->getLabel('alias');
										$html .= $this->form->getInput('alias').'</li>';

										$html .='<li>'.$this->form->getLabel('note');
										$html .= $this->form->getInput('note').'</li>';

										if ($this->pageMenuItem->type !=='url'):
											$html .='<li>'.$this->form->getLabel('link');
											$html .= $this->form->getInput('link').'</li>';
										endif;
										
										//JGLOBAL_STATE will not load. Would like to change this to JSTATUS 
										//but in jfroms this seems not possible
										//replacement in system plugin or 
										/*
										COMMENT ms: this is fixed 
										i have add in views/page.view.html.php
										JForm::addFormPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models'.DS.'forms');
										so the corect xml is loaded
										
										$this->form->setFieldAttribute('published', 'label', JText::_('JSTATUS'));
										 */
										

										
										
										$html .='<li>'.$this->form->getLabel('published');
										$html .= $this->form->getInput('published').'</li>';

										$html .='<li>'.$this->form->getLabel('access');
										$html .= $this->form->getInput('access').'</li>';

										$html .='<li>'.$this->form->getLabel('menutype');
										$html .= $this->form->getInput('menutype').'</li>';

										$html .='<li>'.$this->form->getLabel('parent_id');
										$html .= $this->form->getInput('parent_id').'</li>';

										$html .='<li>'.$this->form->getLabel('browserNav');
										$html .= $this->form->getInput('browserNav').'</li>';

										if ($this->pageMenuItem->type == 'component') :
											$html .='<li>'.$this->form->getLabel('home');
											$html .= $this->form->getInput('home').'</li>';
										endif;

										$html .='<li>'.$this->form->getLabel('language');
										$html .= $this->form->getInput('language').'</li>';

										$html .='<li>'.$this->form->getLabel('template_style_id');
										$html .= $this->form->getInput('template_style_id').'</li>';

								$html .='</ul>';
							$html .='</fieldset>';
						$html .='</div>';

						$html .= '<!-- Menu Item Parameters Section content-->';
						$html .= '<div class="width-40 fltrt">'; //width-100 fltlft">';
							$html .= JHtml::_('sliders.start','menu-sliders-'.$this->pageMenuItem->id);
								/*
									ms:
									check here for pagetype  == 'content_article'
									so we must not add an article here
								*/
								if($this->pageType == 'content_article')
								{
									$this->form->setFieldAttribute('id', 'required', false,'request');
								}
								
								/*
									ms:
									check here for pagetype  == 'content_category_blog' and sub_tak == 'new'
									so we can make an new category
								*/
								if($this->pageType == 'content_category_blog' && $sub_task == 'new')
								{
									$this->form->setFieldAttribute('id', 'required', false,'request');
									
									/*
									//$element = new JXMLElement
									$element = new JXMLElement('<field ></field>');
									<field name="id" type="radio"
				description="JGLOBAL_CHOOSE_CATEGORY_DESC"
				extension="com_content"
				label="JGLOBAL_CHOOSE_CATEGORY_LABEL"
				required="true"
			/>
									
									
									$this->form->setField($element, 'request')
									*/
								}
								
								
								//$html .= $this->loadTemplate('options');
								$fieldSets = $this->form->getFieldsets('request');
								if (!empty($fieldSets)) 
								{
									$fieldSet = array_shift($fieldSets);
									$label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_MENUS_'.$fieldSet->name.'_FIELDSET_LABEL';
									$html .=  JHtml::_('sliders.panel',JText::_($label), 'request-options');
										if (isset($fieldSet->description) && trim($fieldSet->description)) :
											//$html .= '<p class="tip">'.$this->escape(JText::_($fieldSet->description)).'</p>';
											$html .= '<p class="tip">'.JText::_($fieldSet->description).'</p>';
										endif;

										$html .= '<fieldset class="panelform">';
											$hidden_fields = '';
											$html .= '<ul class="adminformlist">';
											foreach ($this->form->getFieldset('request') as $field)
											{
												if (!$field->hidden)
												{
													if($this->pageType == 'content_category_blog' && $sub_task == 'new')
													{
														//$html .= '<li>';
														$html .= '<table>';
															$html .= '<tr>';
																$html .= '<td>';
																	$html .= '<input type="radio" name="create_new_category" value="0" id="create_new_category_0" />';
																$html .= '</td>';
																$html .= '<td>';
																	$html .= $field->label;
																$html .= '</td>';
																$html .= '<td>';
																	$html .= $field->input;
																$html .= '</td>';
															$html .= '</tr>';
													//	$html .= '</li>';
													//	$html .= '<li>';
															$html .= '<tr>';
																$html .= '<td>';
																	//the checked part will be configurable in the pagetype config
																	$html .= '<input type="radio" name="create_new_category" value="1" id="create_new_category_1" checked="checked" />';
																$html .= '</td>';
																$html .= '<td colspan="2">';
																	$html .= '<label class="hasTip" for="create_new_category_1" title="';
																		$html .= JText::_('COM_PAGESANDITEMS_CREATE_NEW_CATEGORY');
																		$html .= '::';
																		$html .= JText::_('COM_PAGESANDITEMS_CREATE_NEW_CATEGORY_TIP');
																		$html .= '">';
																		$html .= JText::_('COM_PAGESANDITEMS_CREATE_NEW_CATEGORY');
																	$html .= '</label>';
																$html .= '</td>';
															$html .= '</tr>';
														$html .= '</table>';
														//$html .= '</li>';
													}
													else
													{
														$html .= '<li>';
															$html .= $field->label;
															$html .= $field->input;
														$html .= '</li>';
													}
													
													
												}
												else
												{
													$hidden_fields.= $field->input;
												}
											}
											$html .= '</ul>';
											$html .= $hidden_fields;
										$html .= '</fieldset>';
								}
									$fieldSets = $this->form->getFieldsets('params');
									foreach ($fieldSets as $name => $fieldSet)
									{
										$label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_MENUS_'.$name.'_FIELDSET_LABEL';
										$html .= JHtml::_('sliders.panel',JText::_($label), $name.'-options');
											if (isset($fieldSet->description) && trim($fieldSet->description))
											{
												//$html .= '<p class="tip">'.$this->escape(JText::_($fieldSet->description)).'</p>';
												$html .= '<p class="tip">'.JText::_($fieldSet->description).'</p>';
											}
											$html .= '<fieldset class="panelform">';
												$html .= '<ul class="adminformlist">';
												foreach ($this->form->getFieldset($name) as $field)
												{
													$html .= '<li>';
														$html .= $field->label;
														$html .=  $field->input;
													$html .= '</li>';
												}
												$html .= '</ul>';
											$html .= '</fieldset>';
									}

									$html .= '<div class="clr"></div>';
									if (!empty($this->modules))
									{
										$html .=  JHtml::_('sliders.panel',JText::_('COM_MENUS_ITEM_MODULE_ASSIGNMENT'), 'module-options');
											$html .= '<fieldset>';
												$html .= '<table class="adminlist">';
													$html .= '<thead>';
														$html .= '<tr>';
															$html .= '<th class="left">';
																$html .= JText::_('COM_MENUS_HEADING_ASSIGN_MODULE');
															$html .= '</th>';
															$html .= '<th>';
																$html .= JText::_('COM_MENUS_HEADING_DISPLAY');
															$html .= '</th>';
														$html .= '</tr>';
													$html .= '</thead>';
													$html .= '<tbody>';
													foreach ($this->modules as $i => &$module)
													{
														$html .= '<tr class="row<?php echo $i % 2;?>">';
															$html .= '<td>';
																$link = 'index.php?option=com_modules&amp;client_id=0&amp;task=module.edit&amp;id='. $module->id.'&amp;tmpl=component&amp;view=module&amp;layout=modal' ;
																$html .= '<a class="modal" href="'. $link.'" rel="{handler: \'iframe\', size: {x: 900, y: 550}}" title="'.JText::_('COM_MENUS_EDIT_MODULE_SETTINGS').'">';
																	//$html .= JText::sprintf('COM_MENUS_MODULE_ACCESS_POSITION', $this->escape($module->title), $this->escape($module->access_title), $this->escape($module->position));
																	$html .= JText::sprintf('COM_MENUS_MODULE_ACCESS_POSITION', $module->title, $module->access_title, $module->position);
																$html .='</a>';

														$html .= '</td>';
														$html .= '<td class="center">';
														if (is_null($module->menuid))
														{
															$html .= JText::_('JNONE');
														}
														elseif ($module->menuid != 0)
														{
															$html .= JText::_('COM_MENUS_MODULE_SHOW_VARIES');
														}
														else
														{
															$html .= JText::_('JALL');
														}
														$html .= '</td>';
													$html .= '</tr>';
													}
													$html .= '</tbody>';
												$html .= '</table>';
											$html .= '</fieldset>';
											}
										$html .= JHtml::_('sliders.end');
										$html .= '<input type="hidden" name="task" value="" />';
										$html .= $this->form->getInput('component_id');
										$html .=  JHtml::_('form.token');
									$html .= '</div>';
									$html .= '<input type="hidden" id="fieldtype" name="fieldtype" value="" />';
								$html .= '<!-- END Menu Item Parameters Section-->';
						//	$html .= '</td>';
						//$html .= '</tr>';
						$html .='<table border="0" cellspacing="0" cellpadding="0" class="paramlist" width="98%" >';
						$html .= $this->lists->add->bottom;
						$html .= '</table>';
						
						$html .= $this->pageMenuItem->linkfield;
						//replace with 
						$html .= $this->lists->pageType->html;
						$html .= '<input type="hidden" name="id" value="'.$this->pageMenuItem->id.'" />';
						if($this->joomlaVersion < '1.6')
						{
							$html .= '<input type="hidden" name="componentid" value="'.$this->pageMenuItem->componentid.'" />';
						}
						else
						{
							$html .= '<input type="hidden" name="component_id" value="'.$this->pageMenuItem->component_id.'" />';
							
							$html .= $this->form->getInput('component_id');
							$html .= JHtml::_('form.token');
							$html .= '<input type="hidden" id="fieldtype" name="fieldtype" value="" />';
						
							
						}
						//$html .= '<input type="hidden" id="pageType" name="pageType" value="'.$this->pageType.'" />';
						$html .= '<input type="hidden" name="type" value="'.$this->pageMenuItem->type.'" />';
					//$html .= '</table>';

					}
				$html .= '</td>';
			$html .= '</tr>';
		$html .= '</table>';
		return $html;
	}


	function addMiniToolbar($editToolbarButtons = true, $newToolbarButtons = true)
	{
		
		$imagePath = $this->dirIcons;
		$html = '';
		$html .= '<div>';
		$html .= '<div style="float:right;">';
		
		//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'extensions'.DS.'helper.php');
		$path = realpath(dirname(__FILE__).DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'htmlhelper.php');
		//load single
		//$buttons = ExtensionHelper::importExtension('button','page_items', 'publish',true,null,true);
		
		//load multiple
		//$buttons = ExtensionHelper::importExtension('button','page_items', $editToolbarButtons,true,null,true);
		
		//load all
		/*
		$buttons = ExtensionHelper::importExtension('button','page_items', null,true,null,true);
		$dispatcher = &JDispatcher::getInstance();
		$buttonVars = null;
		$button->html = '';
		$dispatcher->trigger('onGetButton',array(&$button,$buttonVars,'page_items'));
		$html .= $button->html;
		*/
		if(is_array($editToolbarButtons))
		{
			//we load only in array
			//$htmlelements = ExtensionHelper::importExtension('html','page_items', $editToolbarButtons,true,null,true);
			$htmlelements = ExtensionHtmlHelper::importExtension('page_items', $editToolbarButtons,true,null,true);
		}
		else
		{
			//we load all
			//$htmlelements = ExtensionHelper::importExtension('html','page_items', null,true,null,true);
			$htmlelements = ExtensionHtmlHelper::importExtension('page_items',null,true,null,true);
		}
		$dispatcher = &JDispatcher::getInstance();
		//$htmlelementVars = null;
		$htmlelement->html = '';
		//$dispatcher->trigger('onGetHtmlelement',array(&$htmlelement,$htmlelementVars,'page_items'));
		$this->getCanDo('com_content');
		$htmlOptions = null;
		$htmlOptions->canDo = $this->canDo->com_content;
		$dispatcher->trigger('onGetHtmlelement',array(&$htmlelement,'page_items', $htmlOptions));
		$html .= $htmlelement->html;
		$html .= '</div>';
		$html .= '</div>';
		$html .= '<div class="clr">';
		$html .= '</div>';
		return $html;
	}
	
	//moved a copy to helper
	//to do: find where else there is a call to this function and make it go to the helper
	function itemtype_select($menu_id)
	{
		$html = '';
		$html .= JText::_('COM_PAGESANDITEMS_ITEMTYPE').': ';
		//get itemtype aliasses in new array
		$itemtypes = array();			
		foreach($this->getItemtypes() as $type)
		{
			/*
			TODO rewrite 
			*/			
			
			$type_array = array($type, $this->translate_item_type($type));
			
			
			array_push($itemtypes, $type_array);			
		}
		
		//sort array on alias
		foreach ($itemtypes as $key => $row) 
		{
			$order[$key]  = strtolower($row[1]);    
		}
		array_multisort($order, SORT_ASC, $itemtypes);

		//print_r($itemtypes);
		

		//$html .= '<select name="select_itemtype" id="select_itemtype">';
		$html .= '<select id="select_itemtype" ';
		if(!$menu_id)
		{
			$link = 'index.php?option=com_pagesanditems'; //.$option;
			//$link .= '&amp;task=item.doExecute';
			//$link .= '&amp;extension=menuitemtypeselect';
			//$link .= '&amp;extensionType=html';
			//$link .= '&amp;extensionFolder=page_childs'; ///menuitemtypeselect';
			$link .= '&amp;view=item';
			$link .= '&amp;sub_task=new';
			$link .= '&amp;tmpl=component';
			$link .= '&amp;pageType=content_article';
			$link .= '&amp;menutype='.$this->pageMenuItem->menutype;
			$link .= '&amp;pageId='.$menu_id;
			//$link .= '&amp;select_itemtype=';
			
			$html .= 'name="select_itemtype" ';
			//$html .= 'onchange="document.getElementById(\'button_new_itemtype\').href.value = \''.$link.'\'+this.value\';" ';
			
		}
		$html .= '>';
		
		foreach($itemtypes as $type)
		{
			
			if($type[1])
			{
				//only show if itemtype is installed
				$html .= '<option value="'.$type[0].'"';
				if($type[0]=='text')
				{
					$html .= ' selected="selected"';
					if(!$menu_id)
					{
						$link .= '&amp;select_itemtype='.$type[0];
					}
				}
				$html .= '>'.$type[1];
				if($type[0]=='text')
				{
					$html .= ' ('.JText::_('COM_PAGESANDITEMS_DEFAULT').')';
				}
				$html .= '</option>';
			}
			
		}
		$html .= '</select>';
		$html .= '&nbsp;&nbsp;';

		$button = PagesAndItemsHelper::getButtonMaker();
		$button->imagePath = $this->dirIcons;
		$button->buttonType = 'input';
		$button->text = JText::_('COM_PAGESANDITEMS_NEW_ITEM');
		$button->alt = JText::_('COM_PAGESANDITEMS_MAKE_NEW_ITEM');
		
		
		if(!$menu_id)
		{
			//here we make an modal window
			// with



			$size_x = '1024';
			$size_y = '800';
			$size = 'size: { x: '.$size_x.' , y: '.$size_y.'}';
			$options = "handler: 'iframe', ".$size;
			$button->rel = $options;
			$button->href = $link;
			$button->modal = true;
			$button->id = 'button_new_itemtype';
			//$button->onclick = 'alert(\'new_item('.$menu_id.')\');';
		}
		else
		{
			$button->onclick = 'new_item('.$menu_id.');';
		}
		
		$button->imageName = 'base/icon-16-add.png';
		$html .= $button->makeButton();
		
		return $html;
	}
	
	/*
	only from frontend
	*/
	function itemtype_select_frontend($pageId,$pageType)
	{
		$frontend = true;
		if(!$this->_config['item_type_select_frontend'])
		{
			exit('itemtype selection from the frontend is disabled');
		}
		
		$allowed_user_types = array('Author','Editor','Publisher','Manager','Administrator','Super Administrator');
		if(!in_array($this->user_type, $allowed_user_types))
		{
			//error
			JError::raiseWarning( 100, JText::_('COM_PAGESANDITEMS_NO_MENUS_SELECTED') );
			echo 'You have no permission to edit content';
			//exit('You have no permission to edit content');
		}
		else
		{
		$js = 'function new_item(page_id){'."\n";
		$js .= 'itemtype = document.getElementById(\'select_itemtype\').value;'."\n";
		$js .= 'document.location.href=\'index.php?option=com_pagesanditems&view=item&sub_task=new&pageId=\'+page_id+\'&item_type=\'+itemtype;'."\n";
		$js .= '}'."\n";
		
		$doc =&JFactory::getDocument();
		//$doc->addScriptdeclaration($js);
		
		/*
		
		itemtype = document.getElementById('select_itemtype').value;
						pageType = document.getElementById('pageType').value; //like content_category_blog
						//alert();
						document.getElementById('view').value = 'item';
						document.getElementById('sub_task').value = 'new';
						document.getElementById('item_type').value = itemtype;
						document.getElementById('pageId').value = page_id;
						
						document.adminForm.submit();
		
		
		
		
		*/
		/*
		*/
		/*
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'extensions'.DS.'helper.php');
		
		$htmlelements = ExtensionHelper::importExtension('html','item_base', null,true,null,true);
		$dispatcher = &JDispatcher::getInstance();
		$htmlelementVars = null;
		$htmlelement->html = '';
		$dispatcher->trigger('onGetHtmlelement',array(&$htmlelement,$htmlelementVars,'item_base'));
		$html = $htmlelement->html;
		*/
		
		echo '<div class="componentheading" style="margin-bottom: 20px;">';
			echo JText::_('COM_PAGESANDITEMS_SELECT_ITEMTYPE_FOR_NEW_ITEM');
		echo '</div>';
		echo $this->itemtype_select($pageId);
		echo '<br /><br /><br /><br /><br /><br />';
		}
	
	}

	//delete_other_item_entry is in models/page.php but i think it must move to extensions/itemtypes/other_item.php
	//TODO remove
	function Xdelete_other_item_entry($item_id)
	{
		$this->db->setQuery("DELETE FROM #__pi_item_other_index WHERE item_id='$item_id'");
		$this->db->query();
	}

	//update_duplicate_item is in models/page.php but i think it must move to extensions/itemtypes/other_item.php
	//TODO remove
	function Xupdate_duplicate_item($item_id, $other_item_id)
	{
		//get the content and title of the original item
		$this->db->setQuery("SELECT * FROM #__content WHERE id='$other_item_id' LIMIT 1");
		$other_items = $this->db->loadObjectList();
		$other_item = $other_items[0];
		$other_item_title = addslashes($other_item->title);
		$other_item_introtext = addslashes($other_item->introtext);
		$other_item_fulltext = addslashes($other_item->fulltext);
		$other_item_state = $other_item->state;
		$other_item_created = $other_item->created;
		$other_item_created_by = $other_item->created_by;
		$other_item_modified = $other_item->modified;
		$other_item_modified_by = $other_item->modified_by;
		$other_item_publish_up = $other_item->publish_up;
		$other_item_publish_down = $other_item->publish_down;
		$other_item_attribs = $other_item->attribs;
		$other_item_access = $other_item->access;
		$other_item_metakey = $other_item->metakey;
		$other_item_metadesc = $other_item->metadesc;
		$other_item_metadata = $other_item->metadata;
		
		//update the other item
		$this->db->setQuery( "UPDATE #__content SET title='$other_item_title', introtext='$other_item_introtext', `fulltext`='$other_item_fulltext', state='$other_item_state', created='$other_item_created', created_by='$other_item_created_by', modified='$other_item_modified', modified_by='$modified_by', publish_up='$other_item_publish_up', publish_down='$other_item_publish_down', attribs='$other_item_attribs', access='$other_item_access', metakey='$other_item_metakey', metadesc='$other_item_metadesc', metadata='$other_item_metadata' WHERE id='$item_id'");
		
		if (!$this->db->query()) 
		{
			echo "<script> alert('".$this->db->getErrorMsg()."'); window.history.go(-1); </script>";
			exit();
		}
	}
	
	

}

?>