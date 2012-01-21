<?php
/**
* @version		2.1.3
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
jimport( 'joomla.application.component.controller' );

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controller.php');

/**
 * @package		PagesAndItems
*/
class PagesAndItemsControllerCustomItemType extends PagesAndItemsController
{
	function __construct( $config = array())
	{
		parent::__construct($config);

	}

	function getDispatcher($type_id)
	{
		$db = & JFactory::getDBO();
		//get fields plugin we need this to get an array for the extensionHelper
		//this we must add to each function here
		$db->setQuery( "SELECT plugin "
			. "\nFROM #__pi_custom_fields "
			. "\nWHERE type_id='$type_id' "
			. "\nORDER BY ordering ASC"
		);
		$fieldPlugins = $db->loadResultArray();
		$path = realpath(dirname(__FILE__).DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'fieldtypehelper.php');
		$extensions = ExtensionFieldtypeHelper::importExtension(null, $fieldPlugins,true,null,true);
		$dispatcher = &JDispatcher::getInstance();
		return $dispatcher;
	}

	function config_custom_itemtype_save()
	{
		//here we need the model base for future configcustomitemtype
		//$model = &$this->getModel('Customitemtype','PagesAndItemsModel');
		$db = JFactory::getDBO();
		$type_id = JRequest::getVar('type_id', '', 'post');
		$plugin = JRequest::getVar('plugin', '', 'post');
		$name = JRequest::getVar('name', '');
		$read_more = JRequest::getVar('read_more', '');

		$template_intro = addslashes(JRequest::getVar('template_intro','','post','string', JREQUEST_ALLOWRAW));
		$template_full = addslashes(JRequest::getVar('template_full','','post','string', JREQUEST_ALLOWRAW));
		$html_after = addslashes(JRequest::getVar('html_after','','post','string', JREQUEST_ALLOWRAW));
		$html_before = addslashes(JRequest::getVar('html_before','','post','string', JREQUEST_ALLOWRAW));

		$editor_id = JRequest::getVar('editor_id', '');
		/*
		$items_are_reordered = JRequest::getVar('items_are_reordered', '');
		$items_total = JRequest::getVar('items_total', '');
		*/
		$items_are_reordered = JRequest::getVar('items_customfield_are_reordered',0);
		$items_total = JRequest::getVar('items_customfield_total',0);
		
		$data = JRequest::getVar('jform', array(), 'post', 'array');
		$useDefaultParams = isset($data['params']['useDefault']) ? $data['params']['useDefault'] : 0;
		$params = json_encode($useDefaultParams ? array("useDefault"=>"1") : isset($data['params']) ? $data['params'] : '{}');
		
		$path = realpath(dirname(__FILE__).DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'managerhelper.php');
		ExtensionManagerHelper::importExtension(null, null,true,null,true);
		$dispatcher = &JDispatcher::getInstance();
		if(!$type_id)
		{
			//start insert custom itemtype
			$db->setQuery( "INSERT INTO #__pi_customitemtypes SET name='$name', read_more='$read_more', template_intro='$template_intro', template_full='$template_full', editor_id='$editor_id', html_after='$html_after', html_before='$html_before', params='$params' ");
			if (!$db->query()) {
				echo "<script> alert('".$db->getErrorMsg()."'); window.history.go(-1); </script>";
				exit();
			}

			//get insert id
			$type_id = $db->insertid();
			//call to the manager
			$dispatcher->trigger('onManagerItemtypeConfigSave',array('insert','custom_'.$type_id,$type_id));


			//end insert custom itemtype
		}
		else
		{
			//start update custom itemtype
			//call to the template manager
			$dispatcher->trigger('onManagerItemtypeConfigSave',array('update','custom_'.$type_id,$type_id));

			$db->setQuery( "UPDATE #__pi_customitemtypes SET name='$name', read_more='$read_more', template_intro='$template_intro', template_full='$template_full', editor_id='$editor_id', html_after='$html_after', html_before='$html_before' , params='$params' WHERE id='$type_id'");
			if (!$db->query())
			{
				echo "<script> alert('".$db->getErrorMsg()."'); window.history.go(-1); </script>";
				exit();
			}

			//check fields function extended_config_custom_itemtype_save($type_id,$name,'update',$plugin, $read_more, $template_intro, $template_full, $html_after, $html_before, $editor_id)
			$db->setQuery( "SELECT id, plugin, name "
			. "\nFROM #__pi_custom_fields "
			. "\nWHERE type_id='$type_id' "
			);
			$db->query();
			$fields = $db->loadObjectList();
			/*
				get an dispatcher with the needed fieldtypes
			*/
			$dispatcher = $this->getDispatcher($type_id);

			foreach($fields as $field)
			{
				$dispatcher->trigger('onExtended_config_custom_itemtype_save',array($type_id,$field->name,'update',$field->plugin, $read_more, $template_intro, $template_full, $html_after, $html_before, $editor_id));
			}


			//if items where reordered update the ordering of these items
			if($items_are_reordered==1)
			{
				for ($n = 1; $n <= $items_total; $n++)
				{
					//$temp_id = intval(JRequest::getVar('reorder_item_id_'.$n, '', 'post'));
					$temp_id = intval(JRequest::getVar('reorder_customfield_id_'.$n, '', 'post'));
					$db->setQuery( "UPDATE #__pi_custom_fields SET ordering='$n' WHERE id='$temp_id'");
					if (!$db->query()) {
						echo "<script> alert('".$db->getErrorMsg()."'); window.history.go(-1); </script>";
						exit();
					}
				}
			}
			


			//if choosen to update items by this itemtype in javascript alert on cit config page
			//update items with ajax render script
			if(JRequest::getVar('update_items', false))
			{
			
			$database = JFactory::getDBO();		
$database->setQuery( "UPDATE #__debug SET debug='update' WHERE id='1' ");
$database->query();

				$url = 'index.php?option=com_pagesanditems&view=render_items_by_custom_itemtype&type_id='.$type_id;
				if(JRequest::getVar('sub_task', '')=='apply')
				{
					$url .= '&futuretask=config_custom_itemtype';
				}else{
					$url .= '&futuretask=config';
				}
				//$model->redirect_to_url( $url, '');
				$this->setRedirect(JRoute::_($url, false)); //, $message);
				return true;
			}

			//end update custom itemtype
		}

		//redirect
		$message = JText::_('COM_PAGESANDITEMS_CUSTOM_ITEMTYPE_SAVED');
		if(JRequest::getVar('sub_task', '')=='apply')
		{
			$url = 'index.php?option=com_pagesanditems&view=config_custom_itemtype&type_id='.$type_id;
		}else{
			$url = 'index.php?option=com_pagesanditems&view=config&tab=itemtypes';
		}
		//$model->redirect_to_url( $url, $message);
		$this->setRedirect(JRoute::_($url, false), $message);

	}

/*

*/
	function config_custom_itemtype_delete()
	{
		//here we need the model base for future configcustomitemtype
		$model = &$this->getModel('customitemtype','PagesAndItemsModel');
		$db = JFactory::getDBO();
		$type_id = JRequest::getVar('type_id');
		$type_name = 'custom_'.$type_id;
		$delete_items = JRequest::getVar('delete_items', false);

		//get item-ids
		$db->setQuery("SELECT item_id "
		. "\nFROM #__pi_item_index "
		. "\nWHERE itemtype='$type_name' "
		);
		$item_ids_array = $db->loadResultArray();

		//ADD MS
		// check fields function extended_config_custom_itemtype_delete($type_id,$fields)
		// we can make an redirect in the $class_object->extended_config_custom_itemtype_delete function
		// if we do not want that the custom_itemtype are deleted
		//$db->setQuery("SELECT * FROM #__pi_custom_fields WHERE type_id='$type_id' LIMIT 1 ");
		//why limit ?
		$db->setQuery("SELECT * FROM #__pi_custom_fields WHERE type_id='$type_id' ");
		$db->query();
		$rows = $db->loadObjectList();

		/*
			get an dispatcher with the needed fieldtypes
		*/
		$dispatcher = $this->getDispatcher($type_id);
		$path = realpath(dirname(__FILE__).DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'managerhelper.php');
		ExtensionManagerHelper::importExtension(null, null,true,null,true);
		//call to the manager
		$dispatcher->trigger('onManagerItemtypeConfigDelete',array('custom_'.$type_id,$type_id));


		$done = 0;
		foreach($rows as $field)
		{
			if(!$done)
			{
				$dispatcher->trigger('onExtended_config_custom_itemtype_delete',array($field,$type_id,$rows));
			}
			$done = 1;

		}

		foreach($rows as $field)
		{
			if($delete_items)
			{
				//check if the fieldtype has a function for when the item is deleted, if so, do the function
				for($n = 0; $n < count($item_ids_array); $n++)
				{
					$dispatcher->trigger('onItem_delete',array($item_ids_array[$n], $type_id, $field));
				}
			}
		}

		//get field-ids
		$db->setQuery( "SELECT id "
		. "\nFROM #__pi_custom_fields "
		. "\nWHERE type_id='$type_id' "
		);
		$fields = $db->loadResultArray();

		$fields = implode(',', $fields);

		//delete values
		$db->setQuery("DELETE FROM #__pi_custom_fields_values WHERE field_id IN ($fields) ");
		$db->query();

		//delete fields
		$db->setQuery("DELETE FROM #__pi_custom_fields WHERE type_id='$type_id'");
		$db->query();

		//delete type
		$db->setQuery("DELETE FROM #__pi_customitemtypes WHERE id='$type_id'");
		$db->query();

		$item_ids = implode(',', $item_ids_array);

		//do we need to delete all items as well?
		if($delete_items)
		{
			//delete items
			$db->setQuery("DELETE FROM #__content WHERE id IN ($item_ids) ");
			$db->query();

			//delete item from index
			$db->setQuery("DELETE FROM #__pi_item_index WHERE itemtype='$type_name' ");
			$db->query();
		}
		else
		{
			//no deleting items, but to make them edittable as normal content, set itemtype to 'text' in itemtype-index
			foreach($item_ids_array as $item_id)
			{
				$db->setQuery("UPDATE #__pi_item_index SET itemtype='text' WHERE item_id='$item_id' ");
				$db->query();
			}
		}

		//take itemtype out of config in case it was published
		$model->take_itemtype_out_of_configuration($type_name);

		//redirect
		$url = 'index.php?option=com_pagesanditems&view=config&tab=itemtypes';
		//$model->redirect_to_url( $url, JText::_('COM_PAGESANDITEMS_ITEMTYPE_DELETED'));
		$this->setRedirect(JRoute::_($url, false), JText::_('COM_PAGESANDITEMS_ITEMTYPE_DELETED'));
	}



	function custom_itemtype_fields_delete()
	{
		//here we need the model base for future configcustomitemtype
		$model = &$this->getModel('CustomItemtype','PagesAndItemsModel');
		$db = JFactory::getDBO();
		$fields_to_delete = JRequest::getVar('items_to_delete', array(0));
		$type_id = JRequest::getVar('type_id','');

		$db->setQuery("SELECT * FROM #__pi_custom_fields WHERE type_id='$type_id'");
		$db->query();
		$rows = $db->loadObjectList();

		/*
			get an dispatcher with the needed fieldtypes
		*/
		$dispatcher = $this->getDispatcher($type_id);
		foreach($rows as $field)
		{
			$dispatcher->trigger('onExtended_config_custom_itemtype_field_delete',array($field,$type_id,$fields_to_delete));
		}
		
		$path = realpath(dirname(__FILE__).DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'managerhelper.php');
		ExtensionManagerHelper::importExtension(null, null,true,null,true);
		//call to the manager
		$dispatcher->trigger('onManager_custom_itemtype_fields_delete',array($type_id,$fields_to_delete));

		for($n = 0; $n < count($fields_to_delete); $n++)
		{
			$row = each($fields_to_delete);
			if($row['value']==true){
				$id = $row['value'];

				//do pre-delete field-values event (axample: delete images before the image field is deleted)
				$db->setQuery("SELECT * FROM #__pi_custom_fields WHERE id='$id'");
				$db->query();
				$fields = $db->loadObjectList();
				foreach($fields as $field)
				{
					/*
					//check if the fieldtype has a function for when the item is deleted, if so, do the function
					*/
					$dispatcher->trigger('onField_delete',array($field));

				}
				//delete field
				$db->setQuery("DELETE FROM #__pi_custom_fields WHERE id='$id'");
				$db->query();

				//delete field values
				$model->delete_fields_values($id);
			}
		}

		//update items with ajax render script and return to cit-config-page
		$url = 'index.php?option=com_pagesanditems&view=render_items_by_custom_itemtype&type_id='.$type_id;
		$url .= '&futuretask=config_custom_itemtype';
		//$model->redirect_to_url( $url, '');
		$this->setRedirect(JRoute::_($url, false));

	}

}
