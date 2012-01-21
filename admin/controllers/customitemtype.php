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
		//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'extensions'.DS.'helper.php');
		//$extensions = ExtensionHelper::importExtension('fieldtype',null, $fieldPlugins,true,null,true);
		
		$path = realpath(dirname(__FILE__).DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'fieldtypehelper.php');
		$extensions = ExtensionFieldtypeHelper::importExtension(null, $fieldPlugins,true,null,true);
		$dispatcher = &JDispatcher::getInstance();
		return $dispatcher;
	}

	function config_custom_itemtype_save()
	{
		//here we need the model base for future configcustomitemtype
		$model = &$this->getModel('Customitemtype','PagesAndItemsModel');
		//$config = $model->getConfig();
		
		$type_id = JRequest::getVar('type_id', '', 'post');
		$plugin = JRequest::getVar('plugin', '', 'post');
		$name = JRequest::getVar('name', '');
		$read_more = JRequest::getVar('read_more', '');

		$template_intro = addslashes(JRequest::getVar('template_intro','','post','string', JREQUEST_ALLOWRAW));
		$template_full = addslashes(JRequest::getVar('template_full','','post','string', JREQUEST_ALLOWRAW));
		$html_after = addslashes(JRequest::getVar('html_after','','post','string', JREQUEST_ALLOWRAW));
		$html_before = addslashes(JRequest::getVar('html_before','','post','string', JREQUEST_ALLOWRAW));

		$editor_id = JRequest::getVar('editor_id', '');
		$items_are_reordered = JRequest::getVar('items_are_reordered', '');
		$items_total = JRequest::getVar('items_total', '');
		
		$path = realpath(dirname(__FILE__).DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'managerhelper.php');
		ExtensionManagerHelper::importExtension(null, null,true,null,true);
		$dispatcher = &JDispatcher::getInstance();
		if(!$type_id)
		{
			//start insert custom itemtype
			$model->db->setQuery( "INSERT INTO #__pi_customitemtypes SET name='$name', read_more='$read_more', template_intro='$template_intro', template_full='$template_full', editor_id='$editor_id', html_after='$html_after', html_before='$html_before' ");
			if (!$model->db->query()) {
				echo "<script> alert('".$model->db->getErrorMsg()."'); window.history.go(-1); </script>";
				exit();
			}
			
			//get insert id
			$type_id = $model->db->insertid();
			//call to the template manager
			$dispatcher->trigger('onManagerItemtypeConfigSave',array('insert','custom_'.$type_id,$type_id));
			
			
			//end insert custom itemtype
		}
		else
		{
			//start update custom itemtype
			//call to the template manager
			$dispatcher->trigger('onManagerItemtypeConfigSave',array('update','custom_'.$type_id,$type_id));
			
			$model->db->setQuery( "UPDATE #__pi_customitemtypes SET name='$name', read_more='$read_more', template_intro='$template_intro', template_full='$template_full', editor_id='$editor_id', html_after='$html_after', html_before='$html_before' WHERE id='$type_id'");
			if (!$model->db->query()) 
			{
				echo "<script> alert('".$model->db->getErrorMsg()."'); window.history.go(-1); </script>";
				exit();
			}
			
			//check fields function extended_config_custom_itemtype_save($type_id,$name,'update',$plugin, $read_more, $template_intro, $template_full, $html_after, $html_before, $editor_id)
			$model->db->setQuery( "SELECT id, plugin, name "
			. "\nFROM #__pi_custom_fields "
			. "\nWHERE type_id='$type_id' "
			);
			$model->db->query();
			$fields = $model->db->loadObjectList();
			/*
				get an dispatcher with the needed fieldtypes
			*/
			$dispatcher = $this->getDispatcher($type_id);

			foreach($fields as $field)
			{
				$dispatcher->trigger('onExtended_config_custom_itemtype_save',array($type_id,$field->name,'update',$field->plugin, $read_more, $template_intro, $template_full, $html_after, $html_before, $editor_id));
				/*
				//this is the old
				if($class_object = $this->get_field_class_object($field->plugin,'extended_config_custom_itemtype_save'))
				{
					$class_object->extended_config_custom_itemtype_save($type_id,$name,'update',$field->plugin, $read_more, $template_intro, $template_full, $html_after, $html_before, $editor_id);
				}
			*/
			}

	
			//if items where reordered update the ordering of these items
			if($items_are_reordered==1)
			{
				for ($n = 1; $n <= $items_total; $n++)
				{
					$temp_id = intval(JRequest::getVar('reorder_item_id_'.$n, '', 'post'));
					$model->db->setQuery( "UPDATE #__pi_custom_fields SET ordering='$n' WHERE id='$temp_id'");
					if (!$model->db->query()) {
						echo "<script> alert('".$model->db->getErrorMsg()."'); window.history.go(-1); </script>";
						exit();
					}
				}
			}
			
			//if choosen to update items by this itemtype in javascript alert on cit config page
			//update items with ajax render script
			if(JRequest::getVar('update_items', false))
			{
				$url = 'index.php?option=com_pagesanditems&view=render_items_by_custom_itemtype&type_id='.$type_id;
				if(JRequest::getVar('sub_task', '')=='apply')
				{
					$url .= '&futuretask=config_custom_itemtype';
				}else{
					$url .= '&futuretask=config';
				}
				$model->redirect_to_url( $url, '');
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
		$model->redirect_to_url( $url, $message);
		
	}

/*

*/
	function config_custom_itemtype_delete()
	{
		//here we need the model base for future configcustomitemtype
		$model = &$this->getModel('Base','PagesAndItemsModel');
		
		$type_id = JRequest::getVar('type_id');
		$type_name = 'custom_'.$type_id;
		$delete_items = JRequest::getVar('delete_items', false);
		
		//get item-ids
		$model->db->setQuery("SELECT item_id "
		. "\nFROM #__pi_item_index "
		. "\nWHERE itemtype='$type_name' "
		);
		$item_ids_array = $model->db->loadResultArray();
		
		
		
		//ADD MS
		// check fields function extended_config_custom_itemtype_delete($type_id,$fields) 
		// we can make an redirect in the $class_object->extended_config_custom_itemtype_delete function
		// if we do not want that the custom_itemtype are deleted
		//$model->db->setQuery("SELECT * FROM #__pi_custom_fields WHERE type_id='$type_id' LIMIT 1 ");
		//why limit ?
		$model->db->setQuery("SELECT * FROM #__pi_custom_fields WHERE type_id='$type_id' ");
		$model->db->query();
		$rows = $model->db->loadObjectList();
		
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
				//$this->get_field_class_object($field->plugin,'extended_config_custom_itemtype_delete');
				/*
				if($class_object = $this->get_field_class_object($field->plugin,'extended_config_custom_itemtype_delete'))
				{
					$class_object->extended_config_custom_itemtype_delete($type_id,$rows);
				}
				
				*/
			}
			$done = 1;
			
		}
		
		foreach($rows as $field)
		{
			if($delete_items)
			{
				for($n = 0; $n < count($item_ids_array); $n++)
				{
					$dispatcher->trigger('onItem_delete',array($item_ids_array[$n], $type_id, $field));
				}
				//check if the fieldtype has a function for when the item is deleted, if so, do the function
				/*
				if($class_object = $this->get_field_class_object($field->plugin,'item_delete'))
				{
					for($n = 0; $n < count($item_ids_array); $n++)
					{
						$class_object->item_delete($item_ids_array[$n], $type_id, $field);
					}
				}
				*/
			}
		}
			
		//get field-ids
		$model->db->setQuery( "SELECT id "
		. "\nFROM #__pi_custom_fields "
		. "\nWHERE type_id='$type_id' "
		);
		$fields = $model->db->loadResultArray();
		
		$fields = implode(',', $fields);
		
		//delete values
		$model->db->setQuery("DELETE FROM #__pi_custom_fields_values WHERE field_id IN ($fields) ");
		$model->db->query();
		
		//delete fields
		$model->db->setQuery("DELETE FROM #__pi_custom_fields WHERE type_id='$type_id'");
		$model->db->query();
		
		//delete type
		$model->db->setQuery("DELETE FROM #__pi_customitemtypes WHERE id='$type_id'");
		$model->db->query();
		
		$item_ids = implode(',', $item_ids_array);
		
		//do we need to delete all items as well?		
		if($delete_items)
		{
			//delete items
			$model->db->setQuery("DELETE FROM #__content WHERE id IN ($item_ids) ");
			$model->db->query();
			
			//delete item from index
			$model->db->setQuery("DELETE FROM #__pi_item_index WHERE itemtype='$type_name' ");
			$model->db->query();
		}
		else
		{
			//no deleting items, but to make them edittable as normal content, set itemtype to 'text' in itemtype-index
			foreach($item_ids_array as $item_id)
			{
				$model->db->setQuery("UPDATE #__pi_item_index SET itemtype='text' WHERE item_id='$item_id' ");
				$model->db->query();
			}
		}
		
		//take itemtype out of config in case it was published
		$model->take_itemtype_out_of_configuration($type_name);
			
		//redirect
		$url = 'index.php?option=com_pagesanditems&view=config&tab=itemtypes';
		$model->redirect_to_url( $url, JText::_('COM_PAGESANDITEMS_ITEMTYPE_DELETED'));
	}
	
	

	function custom_itemtype_fields_delete()
	{
		//here we need the model base for future configcustomitemtype
		$model = &$this->getModel('CustomItemtype','PagesAndItemsModel');
		$fields_to_delete = JRequest::getVar('items_to_delete', array(0));
		$type_id = JRequest::getVar('type_id','');
		
		$model->db->setQuery("SELECT * FROM #__pi_custom_fields WHERE type_id='$type_id'");
		$model->db->query();
		$rows = $model->db->loadObjectList();

		/*
			get an dispatcher with the needed fieldtypes
		*/
		$dispatcher = $this->getDispatcher($type_id);
		foreach($rows as $field)
		{
			$dispatcher->trigger('onExtended_config_custom_itemtype_field_delete',array($field,$type_id,$fields_to_delete));
			//$this->get_field_class_object($field->plugin,'extended_config_custom_itemtype_field_delete');
			/*
			if($class_object = $this->get_field_class_object($field->plugin,'extended_config_custom_itemtype_field_delete'))
			{
				//TODO check the $xfields... is not $fields...
				$xfields_to_delete = $class_object->extended_config_custom_itemtype_field_delete($type_id,$fields_to_delete);
			}
			*/
		}

		
		for($n = 0; $n < count($fields_to_delete); $n++)
		{
			$row = each($fields_to_delete);
			if($row['value']==true){
				$id = $row['value'];
				
				//do pre-delete field-values event (axample: delete images before the image field is deleted)
				$model->db->setQuery("SELECT * FROM #__pi_custom_fields WHERE id='$id'");
				$model->db->query();
				$fields = $model->db->loadObjectList();
				foreach($fields as $field)
				{
					/*
					//check if the fieldtype has a function for when the item is deleted, if so, do the function
					*/
					$dispatcher->trigger('onField_delete',array($field));
					
					/*
					//check if the fieldtype has a function for when the item is deleted, if so, do the function
					

					if($class_object = $this->get_field_class_object($field->plugin,'field_delete'))
					{
						$class_object->field_delete($field);
					}
					*/
				}
				//delete field
				$model->db->setQuery("DELETE FROM #__pi_custom_fields WHERE id='$id'");
				$model->db->query();
				
				//delete field values
				$model->delete_fields_values($id);
			}
		}
		
		
		//QUESTION use ajax?
		//update items output
		//$model->update_custom_itemtypes_by_type($type_id);
		
		//update items with ajax render script and return to cit-config-page
		$url = 'index.php?option=com_pagesanditems&view=render_items_by_custom_itemtype&type_id='.$type_id;
		$url .= '&futuretask=config_custom_itemtype';
		$model->redirect_to_url( $url, '');
		
	}

}
