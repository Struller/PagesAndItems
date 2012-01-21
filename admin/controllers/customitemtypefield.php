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
class PagesAndItemsControllerCustomItemTypeField extends PagesAndItemsController
{
	function __construct( $config = array())
	{
		parent::__construct($config);

	}

	function config_custom_itemtype_field_save()
	{
	
		//here we need the model base for future customitemtypefield
		$model = &$this->getModel('Customitemtypefield','PagesAndItemsModel');
		$type_id = JRequest::getVar('type_id', '');
		$field_id = JRequest::getVar('field_id', '');
		$name = addslashes(JRequest::getVar('name', ''));
		$plugin = JRequest::getVar('plugin', '');
		
		//get params
		$field_params = JRequest::getVar('field_params', '');
			$params_string = '';
		for($n = 0; $n < count($field_params); $n++){
			$row = each($field_params);
			$params_string .= "$row[key]-=-$row[value][;-)# ]";
		}
		//}
			
		//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'extensions'.DS.'helper.php');
		//$fieldtypes = ExtensionHelper::importExtension('fieldtype',null, $plugin,true,null,true);
		$path = realpath(dirname(__FILE__).DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'fieldtypehelper.php');
		$fieldtypes = ExtensionFieldtypeHelper::importExtension(null, $plugin,true,null,true);
		$dispatcher = &JDispatcher::getInstance();
		
		//if there is a special function for the fieldtype when saving the config, do that
		$results = $dispatcher->trigger('onParams_save', array (&$params_string,$plugin));
		$params_string = addslashes($params_string);
		if($field_id=='')
		{
			//new field
			//reorder fields to keep things tidy
			/*
			TODO not from model?
			*/
			$ordering = $model->reorder_fields($type_id);
			
			$model->db->setQuery( "INSERT INTO #__pi_custom_fields SET name='$name', type_id='$type_id', plugin='$plugin', ordering='$ordering', params='$params_string'");
			if (!$model->db->query()) {
				echo "<script> alert('".$model->db->getErrorMsg()."'); window.history.go(-1); </script>";
				exit();
			}
			
			//get insert id
			$field_id = $model->db->insertid();
			
			//insert value-rows with the default value for each item using this field
			$itemtype_name = 'custom_'.$type_id;
			$model->db->setQuery("SELECT item_id "
			."FROM #__pi_item_index "
			."WHERE itemtype='$itemtype_name' "
			);
			$items_array = $model->db->loadResultArray();
			//check if we need to work on the default value (render it as a parameter or so, see example fieldtype link)
			/*
			TODO not from model?
			*/
			$default_value = $model->get_field_param($params_string, 'default_value');
			
			$results = $dispatcher->trigger('onDefault_value', array (&$default_value,$plugin));
			foreach($items_array as $item_id)
			{
				
				$model->db->setQuery( "INSERT INTO #__pi_custom_fields_values SET field_id='$field_id', item_id='$item_id', value='$default_value' ");
				$model->db->query();
			}
			
			// check fields function extended_config_custom_itemtype_field_save($type_id,$field_id,$name,$plugin,'insert',$default_value)
			
			$model->db->setQuery("SELECT * FROM #__pi_custom_fields WHERE type_id='$type_id' ");
			$model->db->query();
			$rows = $model->db->loadObjectList();
			/*
			$fieldsArray = array();
			foreach($rows as $row)
			{
				$fieldsArray[] = $row->plugin;
			}
			*/
			$model->db->setQuery( "SELECT plugin "
			. "\nFROM #__pi_custom_fields "
			. "\nWHERE type_id='$type_id' "
			. "\nORDER BY ordering ASC"
			);
			$fieldPlugins = $model->db->loadResultArray();
			
			//$fieldtypes = ExtensionHelper::importExtension('fieldtype',null, $fieldPlugins,true,null,true);
			$fieldtypes = ExtensionFieldtypeHelper::importExtension(null, $fieldPlugins,true,null,true);
			foreach($rows as $row)
			{
				//$results = $dispatcher->trigger('onExtended_config_custom_itemtype_field_save', array ($row->type_id,$row->id,$row->name,$row->plugin,'insert',$default_value));
				$results = $dispatcher->trigger('onExtended_config_custom_itemtype_field_save', array ($row->type_id,$row->id,$row->name,$row->plugin,'update',$default_value));
			}
			//ADD MS END
		
		}else{
			//edit field
			$model->db->setQuery( "UPDATE #__pi_custom_fields SET name='$name', params='$params_string' WHERE id='$field_id'");
			if (!$model->db->query()) {
				echo "<script> alert('".$model->db->getErrorMsg()."'); window.history.go(-1); </script>";
				exit();
			}

			// check fields function extended_config_custom_itemtype_field_save($type_id,$field_id,$name,$plugin,'insert',$default_value)
			
			$model->db->setQuery("SELECT * FROM #__pi_custom_fields WHERE type_id='$type_id' ");
			$model->db->query();
			$rows = $model->db->loadObjectList();
			/*
			$fieldsArray = array();
			foreach($rows as $row)
			{
				$fieldsArray[] = $row->plugin;
			}
			*/
			$model->db->setQuery( "SELECT plugin "
			. "\nFROM #__pi_custom_fields "
			. "\nWHERE type_id='$type_id' "
			. "\nORDER BY ordering ASC"
			);
			$fieldPlugins = $model->db->loadResultArray();
			//$fieldtypes = ExtensionHelper::importExtension('fieldtype',null, $fieldPlugins,true,null,true);
			$fieldtypes = ExtensionFieldtypeHelper::importExtension(null, $fieldPlugins,true,null,true);
			foreach($rows as $row)
			{
				$default_value = '';
				$results = $dispatcher->trigger('onExtended_config_custom_itemtype_field_save', array ($type_id,$row->id,$row->name,$row->plugin,'update',$default_value));
			}
			//ADD MS END
			
		}
		
		//if choosen to update items by this itemtype in javascript alert on field config page
		//update items with ajax render script
		if(JRequest::getVar('update_items', false)){
			//update all items of this type
			$url = 'index.php?option=com_pagesanditems&view=render_items_by_custom_itemtype&type_id='.$type_id;
			if(JRequest::getVar('sub_task', '')=='apply'){
				$url .= '&futuretask=config_custom_itemtype_field&field_id='.$field_id;
			}else{
				$url .= '&futuretask=config_custom_itemtype&from=field';
			}
			$model->redirect_to_url( $url, '');
		}else{
			//redirect without updateing items
			$message = JText::_('COM_PAGESANDITEMS_FIELD_SAVED');
			if(JRequest::getVar('sub_task', '')=='apply'){
				$url = 'index.php?option=com_pagesanditems&view=config_custom_itemtype_field&field_id='.$field_id;
			}else{
				$url = 'index.php?option=com_pagesanditems&view=config_custom_itemtype&type_id='.$type_id;
			}
			$model->redirect_to_url( $url, $message);
		}
		
	}
}
