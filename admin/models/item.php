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
require_once(dirname(__FILE__).DS.'page.php');
/**

 */

class PagesAndItemsModelItem extends PagesAndItemsModelPage
{
	
	function get_itemtype_config($item_type)
	{
		/*
		//get plugin-specific config
		$pluginstable = 'plugins';
		$database = JFactory::getDBO();
		$this->db->setQuery( "SELECT params FROM #__".$pluginstable." WHERE element='itemtype_".$item_type."' LIMIT 1 ");
		$temp = $database->loadObjectList();
		$temp = $temp[0];
		$raw = $temp->params;
		
		//make string to array
		$params = explode( "\n", $raw);
		for($n = 0; $n < count($params); $n++){
			$temp = explode('=',$params[$n]);
			$var = $temp[0];
			$value = '';
			if(count($temp)==2){
				$value = trim($temp[1]);
				if($value=='false'){
					$value = false;
				}
				if($value=='true'){
					$value = true;
				}
			}
			$plugin_config[$var] = $value;
		}
		*/
/*
we have own table for extensions
and the params are json
*/
		//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'extensions'.DS.'helper.php');
		//$extensions = ExtensionHelper::importExtension('itemtype',null, $item_type,true,null,true);
		$path = realpath(dirname(__FILE__).DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'itemtypehelper.php');
		$extensions = ExtensionItemtypeHelper::importExtension(null, $item_type,true,null,true);
		$dispatcher = &JDispatcher::getInstance();
		return $plugin_config;
	}
	
	
	function clean_cache_content()
	{
		if ($this->app->getCfg('caching')) {
			
			//clean content cache
			$cache = & JFactory::getCache('com_content');
			$cache->clean();
		}
	}
	
	function keep_item_index_clean()
	{
		//get content id's
		$this->db->setQuery( "SELECT id, state "
		. "FROM #__content "
		);
		$items = $this->db->loadObjectList();
		
		//make nice arrays
		$content_ids = array();
		$content_ids_tashed = array();
		foreach($items as $item){
			$content_ids[] = $item->id;
			if($item->state==-2){
				$content_ids_tashed[] = $item->id;
			}
		}
		
		//get item index data
		$this->db->setQuery( "SELECT id, item_id, itemtype "
		. "FROM #__pi_item_index "
		);
		$index_items = $this->db->loadObjectList();
		
		$from_cit_to_text = array();
		
		//loop through item index data. 
		//delete rows which item in #__content has been deleted and 
		foreach($index_items as $index_item)
		{
			$index_id = $index_item->id;
			$index_item_id = $index_item->item_id;
			
			$delete_index_row = 0;
			
			//customitemtypes which have been trashed, so delete it from index (makes it a normal item)
			$itemtype = $index_item->itemtype;
						
			if(strpos($itemtype, 'ustom_')){
				//custom itemtype
				if(in_array($index_item_id, $content_ids_tashed)){
					//trashed
					$delete_index_row = 1;
					//to make it a normal item, take out the custom-itemtype-codes
					$from_cit_to_text[] = $index_item_id;
				}
			}
			
			//if item is no longer in content table, take it out of index.
			if(!in_array($index_item_id, $content_ids)){
				$delete_index_row = 1;
				
			}
			
			//delete the index row if needed
			if($delete_index_row){
				$this->db->setQuery("DELETE FROM #__pi_item_index WHERE id='$index_id'");
				$this->db->query();
			}
		}
		
		/*
		//clean items which where customitemtypes, but have now become normal text-types, from custom itemtype codes
		foreach($from_cit_to_text as $itemid){
			//get item texts
			$this->db->setQuery( "SELECT introtext, fulltext "
			."FROM #__content "
			."WHERE id='$itemid' "
			);
			$items = $this->db->loadObjectList();
			
			//take the codes out
			foreach($items as $item){
				echo $item->introtext;
				exit;
				//$introtext = $this->take_cit_codes_out($item->introtext);
				//$fulltext = $this->take_cit_codes_out($item->fulltext);
			}
			
			//update item
			//$this->db->setQuery( "UPDATE #__content SET introtext='$introtext', fulltext='$fulltext' WHERE id='$itemid'");
			//$this->db->query();
		}
		*/
	}
	
	function get_menu_id_from_category_blog($cat_id){
		//get page data
		$this->db->setQuery("SELECT * FROM #__menu ");
		$all_menuitems = $this->db->loadObjectList();
		
		//make a new array from all categories which are used as category-blog-pages in menu
		foreach($all_menuitems as $menuitem){
			if(((strstr($menuitem->link, 'com_content&view=category&layout=blog') && $menuitem->type=='url') || !strstr($menuitem->link, 'com_content&view=category&layout=blog')) && $menuitem->type!='content_blog_category'){
				//something else
			}else{
				//category-blog-page
				$cat_id_row = str_replace('index.php?option=com_content&view=category&layout=blog&id=','',$menuitem->link);
			}
		}
		return $menu_id;
	}
	
	function get_url_from_menuitem($menu_id){
		$this->db->setQuery("SELECT link FROM #__menu WHERE id='$menu_id' LIMIT 1");
		$menu_items = $this->db->loadResultArray();
		$menu_url = $menu_items[0].'&Itemid='.$menu_id;
		return $menu_url;
	}
	
	//update_other_items_if_needed is in models/customitemtype.php but i think it must move to extensions/itemtypes/other_item.php
	//update_duplicate_item is in models/page.php but i think must move to extensions/itemtypes/other_item.php
	//TODO remove
	function Xupdate_other_items_if_needed($item_id){
		$this->db->setQuery("SELECT item_id FROM #__pi_item_other_index WHERE other_item_id='$item_id' ");
		$other_items = $this->db->loadObjectList();
		foreach($other_items as $other_item){
			$this->update_duplicate_item($other_item->item_id, $item_id);
		}
	}
	
	function reorderItemsCategory($catId) 
	{
		$this->db->setQuery("SELECT id, ordering, catid FROM #__content WHERE catid='$catId' AND (state='0' OR state='1') ORDER BY ordering ASC" );
		$rows = $this->db-> loadObjectList();
		$counter = 1;
		foreach($rows as $row){
			//reorder to make sure all is well
			$rowId = $row->id;
			$this->db->setQuery( "UPDATE #__content SET ordering='$counter' WHERE id='$rowId'");
			if (!$this->db->query()) {
				echo "<script> alert('".$this->db->getErrorMsg()."'); window.history.go(-1); </script>";
				exit();
			}
			$counter = $counter + 1;
		}
		return $counter;
	}
	
	
}

