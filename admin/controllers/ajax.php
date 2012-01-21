<?php
/**
* @version		2.1.3
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
jimport('joomla.client.helper');

$database = JFactory::getDBO();
		$database->setQuery( "UPDATE #__pi_config SET config='ajaxcontroller' WHERE id='debug' ");
		$database->query();

/**

*/
require_once(dirname(__FILE__).DS.'customitemtype.php');

/**
 * @package		PagesAndItems
 * Ajax
*/
class PagesAndItemsControllerAjax extends PagesAndItemsController
{
	/**
	 * Custom Constructor
	 */
	function __construct( $default = array())
	{
		parent::__construct($default);
	}

	/*
	does not get triggered here with:
	administrator/index.php?option=com_pagesanditems&task=ajax.ajax_update_cit_item&format=raw&itemtype=1&item_id=141
	so moved to the controller
	function ajax_update_cit_item()
	{

		$database = JFactory::getDBO();
		$database->setQuery( "UPDATE #__pi_config SET config='ajax' WHERE id='debug' ");
		$database->query();

		//check token
		JRequest::checkToken( 'get' ) or die( '<span style="color: red;">Invalid Token</span>' );

		$itemtype = intval(JRequest::getVar('itemtype',''));
		$item_id = intval(JRequest::getVar('item_id',''));
		$itemtype_name = 'custom_'.$itemtype;


		$path = realpath(dirname(__FILE__).DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'itemtypehelper.php');
		ExtensionItemtypeHelper::importExtension(null, array('custom','other_item'),true,null,true);
		$dispatcher = &JDispatcher::getInstance();

		$dispatcher->trigger('update_content_table_from_custom_itemtype',array($item_id, $itemtype_name));
		//check if the saved item has other_items linked to it, if so, update those
		$dispatcher->trigger('update_other_items_if_needed',array($item_id));



		echo '<span style="color: #5F9E30;">'.JText::_('COM_PAGESANDITEMS_UPDATED').'</span>';
		exit;
	}
	*/

	function ajax_make_menu_alias_unique(){

		$db = JFactory::getDBO();
		$updated = 0;

		$menu_item_id = intval(JRequest::getVar('menu_item_id',''));

		$menu_alias_ori = '';
		//get aliasses
		$db->setQuery("SELECT id, alias "
			."FROM #__menu "
		);
		$rows = $db->loadObjectList();
		$aliasses = array();
		foreach($rows as $row){
			if($row->id==$menu_item_id){
				$menu_alias_ori = $row->alias;
			}else{
				$aliasses[] = $row->alias;
			}
		}
		$menu_alias = $menu_alias_ori;
		if(in_array($menu_alias, $aliasses)){
			$j = 2;
			while (in_array($menu_alias."-".$j, $aliasses)){
				$j = $j + 1;
			}
			$menu_alias = $menu_alias."-".$j;
		}

		if($menu_alias!=$menu_alias_ori){
			//do update
			$db->setQuery( "UPDATE #__menu SET alias='$menu_alias' WHERE id='$menu_item_id' ");
			$db->query();
			$updated = 1;
		}

		$message = JText::_('COM_PAGESANDITEMS_ALIAS_IS_OK');
		if($updated){
			$message = JText::_('COM_PAGESANDITEMS_UPDATED').': '.$menu_alias;
		}

		echo '<span style="color: ';
		if($updated){
			echo 'red';
		}else{
			echo '#5F9E30';
		}
		echo ';">'.$message.'</span>';
		exit;
	}
}