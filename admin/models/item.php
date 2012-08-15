<?php
/**
* @version		2.1.6
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
//jimport( 'joomla.form.form' );
//require_once(dirname(__FILE__).DS.'page.php');
/**

 */

class PagesAndItemsModelItem extends JModel //PagesAndItemsModelPage
{
	public function __construct($config = array())
	{
		parent::__construct($config);
		//FB::dump('test');
		//JForm::addFormPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'models'.DS.'forms');
		//JForm::addFieldPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'models'.DS.'fields');
	}
	
	function get_itemtype_config($item_type)
	{
		$path = realpath(dirname(__FILE__).DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'itemtypehelper.php');
		$extensions = ExtensionItemtypeHelper::importExtension(null, $item_type,true,null,true);
		$dispatcher = &JDispatcher::getInstance();
		return $plugin_config;
	}


	function clean_cache_content()
	{
		$app = JFactory::getApplication();
		if ($app->getCfg('caching')) {

			//clean content cache
			$cache = & JFactory::getCache('com_content');
			$cache->clean();
		}
	}

	function keep_item_index_clean()
	{
		$db = JFactory::getDBO();
		//get content id's
		$db->setQuery( "SELECT id, state "
		. "FROM #__content "
		);
		$items = $db->loadObjectList();

		//make nice arrays
		$content_ids = array();
		$content_ids_trashed = array();
		foreach($items as $item){
			$content_ids[] = $item->id;
			if($item->state==-2){
				$content_ids_trashed[] = $item->id;
			}
		}

		//get item index data
		$db->setQuery( "SELECT id, item_id, itemtype "
		. "FROM #__pi_item_index "
		);
		$index_items = $db->loadObjectList();

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
				if(in_array($index_item_id, $content_ids_trashed)){
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
				$db->setQuery("DELETE FROM #__pi_item_index WHERE id='$index_id'");
				$db->query();
			}
		}

		/*
		//clean items which where customitemtypes, but have now become normal text-types, from custom itemtype codes
		foreach($from_cit_to_text as $itemid){
			//get item texts
			$db->setQuery( "SELECT introtext, fulltext "
			."FROM #__content "
			."WHERE id='$itemid' "
			);
			$items = $db->loadObjectList();

			//take the codes out
			foreach($items as $item){
				echo $item->introtext;
				exit;
				//$introtext = $this->take_cit_codes_out($item->introtext);
				//$fulltext = $this->take_cit_codes_out($item->fulltext);
			}

			//update item
			//$db->setQuery( "UPDATE #__content SET introtext='$introtext', fulltext='$fulltext' WHERE id='$itemid'");
			//$db->query();
		}
		*/
	}

	function get_menu_id_from_category_blog($cat_id){
		//get page data
		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__menu ");
		$all_menuitems = $db->loadObjectList();

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
		$db = JFactory::getDBO();
		$db->setQuery("SELECT link FROM #__menu WHERE id='$menu_id' LIMIT 1");
		$menu_items = $db->loadResultArray();
		$menu_url = $menu_items[0].'&Itemid='.$menu_id;
		return $menu_url;
	}


	function reorderItemsCategory($catId)
	{
		$db = JFactory::getDBO();
		$db->setQuery("SELECT id, ordering, catid FROM #__content WHERE catid='$catId' AND (state='0' OR state='1') ORDER BY ordering ASC" );
		$rows = $db-> loadObjectList();
		$counter = 1;
		foreach($rows as $row){
			//reorder to make sure all is well
			$rowId = $row->id;
			$db->setQuery( "UPDATE #__content SET ordering='$counter' WHERE id='$rowId'");
			if (!$db->query()) {
				echo "<script> alert('".$db->getErrorMsg()."'); window.history.go(-1); </script>";
				exit();
			}
			$counter = $counter + 1;
		}
		return $counter;
	}
}

