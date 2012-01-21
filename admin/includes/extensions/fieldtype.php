<?php
/**
* @version		2.0.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2011 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('JPATH_BASE') or die;
/**
*/
require_once(dirname(__FILE__).DS.'extension.php');


abstract class PagesAndItemsExtensionFieldtype extends PagesAndItemsExtension
{
	public $version = '';//

	/**
	 * Constructor
	 *
	 * @param object $subject The object to observe
	 * @param array  $config  An optional associative array of configuration settings.
	 */
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);
		jimport('joomla.filesystem.folder');
		$folder = realpath(dirname(__FILE__).'..'.DS.'..'.DS.'..'.DS);
		$files = JFolder::files($folder,'.xml',false,true);
		if(count($files))
		{
			foreach($files as $file)
			{
				$xml = simplexml_load_file($file);
				if ($xml)
				{
					if ( is_object($xml) && is_object($xml->install))
					{
						//ok we have the install file
						//we will get the version
						$element = (string)$xml->version;
						$this->version = $element ? $element : '';
					}
				}
			}
		}
	}
	

	function onDisplay_config_form(&$html,$plugin, $type_id, $name, $field_params, $field_id)
	{
		if(method_exists($this, 'display_config_form') && $this->_name == $plugin)
		{
			$html = $this->display_config_form($plugin, $type_id, $name, $field_params, $field_id);
			return true;
		}
		return false;
	}
	
	function onDisplay_item_edit(&$fieldHtml,$field, $field_params, $field_values, $field_value, $new_field, $field_id)
	{

		if(method_exists($this, 'display_item_edit') && $this->_name == $field->plugin)
		{

			$this->loadLanguage();
			//$top = '';
			$top = false;
			if($fieldHtml->text == '' || !$fieldHtml->text)
			{
				//$top = '_top';
				$top = true;
			}
			$html = '';
			
			//$html .= JHtml::_('sliders.panel',JText::_($field->name), 'pi-fieldtype'.$field->name);
			if($top)
			{
				//here we add an extra div to hide top line
				$html .= '<div class="fieldtype_top">';
			}
			
			//$fieldHtml->text = $fieldHtml->text.$this->display_item_edit($field, $field_params, $field_values, $field_value, $new_field, $field_id);//,$top);
			$html .= $this->display_item_edit($field, $field_params, $field_values, $field_value, $new_field, $field_id);//,$top);
			
			if($top)
			{
				$html .= '</div>';
			}
			
			$fieldHtml->text = $fieldHtml->text.$html;
			return true;
		}
		return false;
	}
	
	function onRender_field_output(&$html,$field, $intro_or_full, $readmore_type=0, $editor_id=0,$language = null)
	{
		if(method_exists($this, 'render_field_output') && $this->_name == $field->plugin)
		{
			$html = $this->render_field_output($field, $intro_or_full, $readmore_type, $editor_id,$language);
			return true;
		}
		return false;
	}
	
	function onParams_save(&$params_string,$plugin)
	{
		if(method_exists($this, 'params_save') && $this->_name == $plugin)
		{
			$params_string = $this->params_save($params_string);
			return true;
		}
		return false;
	}
	
	function onDefault_value(&$default_value,$plugin)
	{
		if(method_exists($this, 'default_value') && $this->_name == $plugin)
		{
			$default_value = $this->default_value($default_value);
			return true;
		}
		return false;
	}
	
	
	function onField_save($field, $insert_or_update)
	{
		if(method_exists($this, 'field_save') && $this->_name == $field->plugin)
		{
			if($insert_or_update == 'insert')
			{
				$value = $this->field_save($field, 'insert');
			}
			else
			{
				$value = $this->field_save($field, 'update');
			}
		}
		else if($this->_name == $field->plugin)
		{
			$field_values_array = 'field_values_'.$field->field_id;
			$field_values = JRequest::getVar($field_values_array, false);
			if(count($field_values)==1)
			{
				$temp = trim(addslashes($field_values[0]));
				$value = str_replace('"','&quot;',$temp);
			}
			else
			{
				$value = '';
				for($n = 0; $n < count($field_values); $n++)
				{
					$row = each($field_values);
					$temp = addslashes("$row[key]-=-$row[value][;-)# ]");
					$value .= str_replace('"','&quot;',$temp);
				}
			}
		}
		else
		{
			return false;
		}
		
		if($insert_or_update == 'insert')
		{
			$this->db->setQuery( "INSERT INTO #__pi_custom_fields_values SET field_id='$field->id', item_id='$field->item_id', value='$value' ");
		}
		else
		{
			/*
			$this->db->setQuery( "SELECT f.*, v.*, v.id AS value_id, f.id AS field_id "
			. "\n FROM #__pi_custom_fields_values AS v "
			. "\n LEFT JOIN #__pi_custom_fields AS f "
			. "\n ON f.id=v.field_id "
			. "\n WHERE f.type_id='$field->type_id' "
			. "\n AND v.item_id='$field->item_id' "
			. "\n AND f.id='$field->id' "
			. "\n ORDER BY f.ordering ASC "
			);
			$field = $this->db->loadObject();
			*/
			$this->db->setQuery( "UPDATE #__pi_custom_fields_values SET value='$value' WHERE id='$field->value_id' ");
		}
		$this->db->query();
		return true;
	}
	
	/*
	i have not convert this 
	extended_editor_tag
	*/	
	
	

	
	function onExtended_save_custom_itemtype_field_save($field, $insert_or_update,$item_id,$type_id,$fields)
	{
		if(method_exists($this, 'extended_save_custom_itemtype_field_save') && $this->_name == $field->plugin)
		{
			//$value = 
			$this->extended_save_custom_itemtype_field_save($field, $insert_or_update, $item_id, $type_id, $fields);
			return true;
		}
		return false;
	}


	function onExtended_save_custom_itemtype_delete_item($field, $item_id,$type_id)
	{
		if(method_exists($this, 'extended_save_custom_itemtype_delete_item') && $this->_name == $field->plugin)
		{
			//$value = 
			$this->extended_save_custom_itemtype_delete_item($item_id,$type_id);
			return true;
		}
		return false;
	}	
	
	function onExtended_config_custom_itemtype_field_save($type_id,$field_id,$name,$plugin,$insert,$default_value) 
	{
		if(method_exists($this, 'extended_config_custom_itemtype_field_save') && $this->_name == $plugin)
		{
			$this->extended_config_custom_itemtype_field_save($type_id,$field_id,$name,$plugin,$insert,$default_value);
			return true;
		}
		return false;
	}
	
	
	function onExtended_config_custom_itemtype_save($type_id,$name,$update,$plugin, $read_more, $template_intro, $template_full, $html_after, $html_before, $editor_id)
	{
		if(method_exists($this, 'extended_config_custom_itemtype_save') && $this->_name == $plugin)
		{
			//$value = 
			$this->extended_config_custom_itemtype_save($type_id,$name,$update,$plugin, $read_more, $template_intro, $template_full, $html_after, $html_before, $editor_id);
			return true;
		}
		return false;
	}
	
	
	function onExtended_config_custom_itemtype_delete($field,$type_id,$rows)
	{
		if(method_exists($this, 'extended_config_custom_itemtype_delete') && $this->_name == $field->plugin)
		{
			//$value = 
			$this->extended_config_custom_itemtype_delete($type_id,$rows);
			return true;
		}
		return false;
	}
	
	function onExtended_config_custom_itemtype_field_delete($field,$type_id,$fields_to_delete)
	{
		if(method_exists($this, 'extended_config_custom_itemtype_field_delete') && $this->_name == $field->plugin)
		{
			//$value = 
			$this->extended_config_custom_itemtype_field_delete($type_id,$fields_to_delete);
			return true;
		}
		return false;
	}
	
	function onItem_delete( $field,$item_id, $type_id)
	{
		if(method_exists($this, 'item_delete') && $this->_name == $field->plugin)
		{
			//$value = 
			$this->item_delete($item_id, $type_id, $field);
			return true;
		}
		return false;
	}
	
	function onField_delete($field)
	{
		if(method_exists($this, 'field_delete') && $this->_name == $field->plugin)
		{
			//$value = 
			$this->field_delete($field);
			return true;
		}
		return false;
	}


/*
here are the helper functions for the fields?

*/
	function check_if_field_param_is_present($field_params, $field_param)
	{
		$param_is_present = false;
		for($n = 0; $n < count($field_params); $n++){
			if(is_array($field_params)){
				$row = each($field_params);
				if($row['key']==$field_param){
					$param_is_present = true;
					break;
				}
			}
		}
		return $param_is_present;
	}
	
	function check_if_plugin_lang_var_is_present($pi_lang_plugin, $var)
	{
		$var_is_present = false;
		for($n = 0; $n < count($pi_lang_plugin); $n++)
		{
			$row = each($pi_lang_plugin);
			if($row['key']==$var){
				$var_is_present = true;
				break;
			}
		}
		return $var_is_present;
	}

	function display_field($field_name, $field_content, $width_left='20', $width_right='70')
	{
		$html = '<div class="pi_form_wrapper">';
		$html .= '<div class="pi_width'.$width_left.'">';
		$has_only_star = strpos($field_name, 'img src=');
		if($field_name!='' && $has_only_star!=1){
			$html .= $field_name.':';
		}elseif($has_only_star==1){
			$html .= $field_name;
		}else{
			$html .= '&nbsp;';
		}
		$html .= '</div>';
		$html .= '<div class="pi_width'.$width_right.'">';
		$html .= $field_content;
		$html .= '</div>';
		$html .= '</div>';
		return $html;
	}
	
	function display_field_default_value($field_params)
	{
		$field_content = '<input type="text" class="width200" value="'.$field_params['default_value'].'" name="field_params[default_value]" />';
		return $this->display_field(JText::_('COM_PAGESANDITEMS_DEFAULT_VALUE'), $field_content);
	}
	
	function display_field_description($field_params)
	{
		//$field_name = strtolower(JText::_('COM_PAGESANDITEMS_DESCRIPTION'));
		//changed on request of Micha for German chapital letters.
		$field_name = JText::_('COM_PAGESANDITEMS_DESCRIPTION');
		$field_content = '<input type="text" class="width200" value="'.$field_params['description'].'" name="field_params[description]" />';
		return $this->display_field($field_name, $field_content);
	}
	
	function display_field_validation($field_params)
	{
		$field_name = JText::_('COM_PAGESANDITEMS_VALIDATION');
		$field_content = '<input type="checkbox" class="checkbox"';
		//if($field_params['validation']){
		if($this->check_if_field_param_is_present($field_params, 'validation')){
			if($field_params['validation']){
				$field_content .= ' checked="checked"';
			}
		} 
		$field_content .= 'name="field_params[validation]" value="not_empty" /> '.JText::_('COM_PAGESANDITEMS_NOT_EMPTY');
		return $this->display_field($field_name, $field_content);
	}
	

	
	function display_field_validation_message($field_params)
	{
		$field_content = '<input type="text" class="width200" value="'.$field_params['alert_message'].'" name="field_params[alert_message]" />';
		return $this->display_field(JText::_('COM_PAGESANDITEMS_VALIDATION_ALERT_MESSAGE'), $field_content);
	}
	
	function get_field_param($values_string, $property)
	{
		return $this->get_field_value($values_string, $property);
	}
	
	function get_field_value($values_string, $property)
	{
		$values_array = explode('[;-)# ]', $values_string);
		$property = substr($property,1);
		$html = '';
		foreach($values_array as $value_set){
			if(strpos($value_set, $property)){
				$temp = explode('-=-', $value_set);
				$html = $temp[1];
				break;
			}
		}
		return $html;
	}
	


	function reorder_fields($type_id)
	{
		$this->db->setQuery("SELECT id FROM #__pi_custom_fields WHERE type_id='$type_id' ORDER BY ordering ASC" );
		$rows = $this->db->loadObjectList();
		$counter = 1;
		foreach($rows as $row){
			$id = $row->id;
			$this->db->setQuery( "UPDATE #__pi_custom_fields SET ordering='$counter' WHERE id='$id'");
			$this->db->query();
			$counter = $counter + 1;
		}
		return $counter;
	}
}
