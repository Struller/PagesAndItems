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
require_once(dirname(__FILE__).'/../../../includes/extensions/itemtype.php');
//OTHER_ITEM
class PagesAndItemsExtensionItemtypeOther_item extends PagesAndItemsExtensionItemtype
{
	/*
	function onItemtypeDisplay_config_form(&$itemtypeHtml,$item_type)
	{

	}
	*/
	function onGetTables(&$tables,$item_type,$item_id)
	{
		if($item_type != 'other_item')
		{
			return false;
		}

		//tables are:
		$table->name = 'content';
		$table->reference_id = 'id';
		$table->reference_id_value = $item_id;
		$tables[] = $table;
		$table = null;

		/*
		$table->name = 'pi_subitem_moduleposition';
		$table->reference_id = 'item_id';
		$table->reference_id_value = $item_id;
		$tables[] = $table;
		$table = null;
		*/
		return true;
	}


	//old onItemSave
	function onItemtypeItemSave($item_type, $delete_item, $item_id, $new_or_edit)
	{


		if($item_type != 'other_item')
		{
			return false;
		}

		$model =& JModel::getInstance('Item', 'PagesAndItemsModel', array());

		//state
		$state = intval(JRequest::getVar('state'));
		$jform = JRequest::getVar('jform', array(), 'post', 'array');
		if($jform && count($jform))
		{
			$state = $jform['state'];
		}
		
		//get other_item_id
		$other_item_id = JRequest::getVar('other_item_id', '');

		if(!$other_item_id){
			return false;
		}



		$show_title_item = intval(JRequest::getVar('show_title_item'));

		$item_on_frontpage = intval(JRequest::getVar('frontpage'));
		if($new_or_edit == 'new')
		{

			$this->db->setQuery( "INSERT INTO #__pi_item_other_index SET item_id='$item_id', other_item_id='$other_item_id'");
			$this->db->query();
			//update other item
			//update_duplicate_item is in models/page.php but i think it must move to extensions/itemtypes/other_item.php
			//$model->update_duplicate_item($item_id, $other_item_id);
			$this->update_duplicate_item($item_id, $other_item_id);
		}
		else
		{
			$this->db->setQuery( "UPDATE #__pi_item_other_index SET other_item_id='$other_item_id' WHERE item_id='$item_id'");
			$this->db->query();
			//update other item
			//update_duplicate_item is in models/page.php but i think it must move to extensions/itemtypes/other_item.php
			//$model->update_duplicate_item($item_id, $other_item_id);
			$this->update_duplicate_item($item_id, $other_item_id);

			//update index item for visiblitity title
			$this->db->setQuery( "UPDATE #__pi_item_index SET show_title='$show_title_item' WHERE item_id='$item_id'");
			$this->db->query();

			//update item state (because it just got overwritten above in update_duplicate_item()
			$this->db->setQuery( "UPDATE #__content SET state='$state' WHERE id='$item_id'");
			$this->db->query();

			//if other-item delete also take out row in other item index
			if($state=='-2')
			{
				//delete_other_item_entry is in models/page.php but i think it must move to extensions/itemtypes/other_item.php
				//$model->delete_other_item_entry($item_id);
				$this->delete_other_item_entry($item_id);
			}
		}


		return true;
	}

	function onItemtypeDisplay_item_edit(&$itemtypeHtml,$item_type,$item_id,$text,$itemIntroText,$itemFullText)
	{
		//$helper = $this->get_helper();
		if($item_type != 'other_item')
		{
			return false;
		}
		$html = '';
		//get other_items
		$other_item_id = false;
		$this->db->setQuery("SELECT item_id, other_item_id FROM #__pi_item_other_index ");
		$rows_other_items = $this->db->loadObjectList();
		foreach($rows_other_items as $row_other_item)
		{
			if($row_other_item->item_id==$item_id){
				$other_item_id = $row_other_item->other_item_id;
			}
		}

		//make array to filter other_items out of item list
		$other_item_array = array();
		foreach($rows_other_items as $row_other_item)
		{
			$other_item_array[] = $row_other_item->item_id;
		}
		//get all items which store their data in #__content. only those can be duplicated. so excluding the itemtypes which can be installed separately, like plugins, except for custom-itemtypes, they work fine.
		$this->db->setQuery("SELECT id, title, sectionid, catid FROM #__content  WHERE state='0' OR state='1' ORDER BY title");
		$this->db->setQuery( "SELECT c.id, c.title, c.sectionid, c.catid, i.itemtype"
		. "\nFROM #__content AS c"
		. "\nLEFT JOIN #__pi_item_index AS i"
		. "\nON c.id=i.item_id"
		. "\nWHERE (c.state='0' OR c.state='1')"
		. "\nORDER BY c.title ASC"
		);
		$all_items_db = $this->db->loadObjectList();

		/*
		//get sections
		$this->db->setQuery("SELECT id, title FROM #__sections");
		$all_sections_db = $this->db->loadObjectList();
		*/

		//get categories
		$this->db->setQuery("SELECT id, title FROM #__categories");
		$all_categories_db = $this->db->loadObjectList();

		$html .= '<p>'.JText::_('COM_PAGESANDITEMS_OTHER_ITEM_INFO').'</p>';

		if(!$other_item_id)
		{
			$other_item_id = JRequest::getVar('other_item_id',null, 'default', 'none', 0);
		}

		//display select with items
		$html .= '<select name="other_item_id">';
		$html .= '<option value="0">'.JText::_('COM_PAGESANDITEMS_SELECT_AN_ITEM').'</option>';
		foreach($all_items_db as $item_db)
		{
			//if not a other_item or itemtype-plugin which does not save data straight into table #__content
			//QUESTION wghat that
			if(!in_array($item_db->id ,$other_item_array) && !file_exists(dirname(__FILE__).'/../../com_pi_itemtype_'.$item_db->itemtype.'/com_pi_itemtype_'.$item_db->itemtype.'.xml'))
			{
				$html .= '<option value="'.$item_db->id.'"';
				if($item_db->id==$other_item_id)
				{
					$html .= ' selected="selected"';
				}
				$html .= '>';
				$html .= $item_db->title;
				$html .=  ' [';
				/*
				foreach($all_sections_db as $section_row)
				{
					if($section_row->id==$item_db->sectionid)
					{
						$html .= ' ['.$section_row->title.'/';
						break;
					}
				}
				*/
				foreach($all_categories_db as $category_row)
				{
					if($category_row->id==$item_db->catid)
					{
						$html .= $category_row->title.']';
						break;
					}
				}
			}
			$html .= '</option>';
		}
		$html .= '</select>';

		//find the page_id
		//$original_page_id = $helper->get_page_id_from_item_id($other_item_id);
		$original_page_id = PagesAndItemsHelper::get_page_id_from_item_id($other_item_id);		

		if($other_item_id){
			//if($frontend){
				//$html .= '<p><a href="index.php?option=com_pagesanditems&view=item&sub_task=edit&pageId='.$original_page_id.'&item_id='.$other_item_id.'">'.JText::_('COM_PAGESANDITEMS_EDIT_ORIGINAL_ITEM').'</a></p>';
			//}else{
				$html .= '<p><a href="index.php?option=com_pagesanditems&view=item&sub_task=edit&pageId='.$original_page_id.'&itemId='.$other_item_id.'">'.JText::_('COM_PAGESANDITEMS_EDIT_ORIGINAL_ITEM').'</a></p>';
			//}
		}
		$itemtypeHtml->text = $itemtypeHtml->text.$html;
		return true;
	}

	function get_page_id_from_item_id($item_id)
	{

		$cat_id = $this->get_cat_id_from_item($item_id);

		//get menuitems. I need to get these in a new query because calling $this->menuitems does not work when called from class_custom_item_types.php why I do not know.
		$this->db->setQuery( "SELECT id, link, type FROM #__menu ");
		$menuitems = $this->db->loadObjectList();

		$original_page_id = false;
		foreach($menuitems as $menu_item_page)
		{
			$temp_cat_id = 0;
			//if category blog
			if( strstr($menu_item_page->link, 'index.php?option=com_content&view=category&layout=blog') && $menu_item_page->type!='url' && $menu_item_page->type=='component')
			{
				//get the category id of each menu item
				$pos_cat_id = strpos($menu_item_page->link,'id=');
				$temp_cat_id = substr($menu_item_page->link, ($pos_cat_id+3), strlen($menu_item_page->link));
				if($cat_id==$temp_cat_id)
				{
					$original_page_id = $menu_item_page->id;
					break;
				}
			}
			elseif(strstr($menu_item_page->link, 'index.php?option=com_content&view=article&id='.$item_id) || strstr($menu_item_page->link, 'index.php?option=com_content&task=view&id='.$item_id))
			{
				//full item layout
				$original_page_id = $menu_item_page->id;
				break;
			}
		}
		return $original_page_id;
	}

	function get_cat_id_from_item($item_id)
	{
		$this->db->setQuery( "SELECT catid FROM #__content WHERE id='$item_id' LIMIT 1 ");
		$items = $this->db->loadObjectList();
		$catid = false;
		foreach($items as $item)
		{
			$catid = $item->catid;
		}
		return $catid;
	}


	function delete_other_item_entry($item_id)
	{
		$this->db->setQuery("DELETE FROM #__pi_item_other_index WHERE item_id='$item_id'");
		$this->db->query();
	}


	function update_other_items_if_needed($item_id)
	{
		$this->db->setQuery("SELECT item_id FROM #__pi_item_other_index WHERE other_item_id='$item_id' ");
		$other_items = $this->db->loadObjectList();
		foreach($other_items as $other_item)
		{
			//update_duplicate_item is in models/page.php but i think it must move to extensions/itemtypes/other_item.php
			$this->update_duplicate_item($other_item->item_id, $item_id);
		}
	}

	function update_duplicate_item($item_id, $other_item_id)
	{

		$database = JFactory::getDBO();



		//get the content and title of the original item
		$database->setQuery("SELECT * FROM #__content WHERE id='$other_item_id' LIMIT 1");
		$other_items = $database->loadObjectList();
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
		$database->setQuery( "UPDATE #__content SET title='$other_item_title', introtext='$other_item_introtext', `fulltext`='$other_item_fulltext', state='$other_item_state', created='$other_item_created', created_by='$other_item_created_by', modified='$other_item_modified', modified_by='$other_item_modified_by', publish_up='$other_item_publish_up', publish_down='$other_item_publish_down', attribs='$other_item_attribs', access='$other_item_access', metakey='$other_item_metakey', metadesc='$other_item_metadesc', metadata='$other_item_metadata' WHERE id='$item_id'");
		$database->query();

		//get the original items ACL
		//ms: 10.10.2011 
		$asset_name = 'com_content.article.'.$other_item_id;
		$database->setQuery("SELECT * "
		." FROM #__assets "
		." WHERE name='$asset_name' "
		." LIMIT 1 "
		);
		$rowOrg = $database->loadObject();
		//we have the permission
		if($rowOrg)
		{
			$asset_name = 'com_content.article.'.$item_id;
			$database->setQuery("SELECT * "
			." FROM #__assets "
			." WHERE name='$asset_name' "
			." LIMIT 1 "
			);
			$row = $database->loadObject();
			if($row)
			{
				//update
				$database->setQuery( "UPDATE #__assets SET parent_id='$rowOrg->parent_id', lft='$rowOrg->lft', rgt='$rowOrg->rgt', level='$rowOrg->level', title='$rowOrg->title', rules='$rowOrg->rules' ,name='$asset_name' WHERE id='$row->id' ");
				$database->query();
			}
			else
			{
				//insert
				$database->setQuery( "INSERT INTO #__assets SET parent_id='$row->parent_id', lft='$row->lft', rgt='$row->rgt', level='$row->level', title='$row->title', rules='$row->rules' ,name='$asset_name' ");
				$database->query();
				
			}
		
		}
		
		
		
		
		/*
		//get the original items ACL
		$asset_name = $asset_name = 'com_content.article.'.$other_item_id;
		$database->setQuery("SELECT * "
		." FROM #__assets "
		." WHERE name='$this->usergroup' "
		." LIMIT 1 "
		);
		$rows = $database->loadObjectList();
		$parent_id = 0;
		$level = 0;
		$title = 0;
		$rules = 0;
		foreach($rows as $row){
			$parent_id = $row->parent_id;
			$level = $row->level;
			$title = $row->title;
			$rules = $row->rules;
		}

		//check if the new item has a row in the assets table

		//insert ACL row

		//update ACL row
		$database->setQuery( "UPDATE #__pi_config SET config='oonitemsave' WHERE id='debug' ");
		$database->query();
		*/
	}
	
	function get_helper(){
		require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_pagesanditems'.DS.'helpers'.DS.'pagesanditems.php');
		$helper = new pagesanditemsHelper();
		return $helper;
	}

}

?>