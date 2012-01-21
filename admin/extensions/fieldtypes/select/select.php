<?php
/**
* @version		2.1.1
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
require_once(dirname(__FILE__).'/../../../includes/extensions/fieldtype.php');
//SELECT
class PagesAndItemsExtensionFieldtypeSelect extends PagesAndItemsExtensionFieldtype
{
	function display_config_form($plugin, $type_id, $name, $field_params, $field_id){
		if(!$field_id){
			//new field, set defaults here
			$field_params['showFieldName'] = $this->params->get('showFieldName'); //0
			$field_params['values'] = $this->params->get('values'); //''
			$field_params['labels'] = $this->params->get('labels'); //''
			$field_params['default_value'] = $this->params->get('default_value'); //''
			$field_params['multiselect'] = $this->params->get('multiselect'); //'0'
		}
		
		$html = '';
		
		//New show field name
		$html .= $this->makeShowFieldName($field_id,$field_params);
		//description
		$html .= $this->display_field_description($field_params);
		
		//validation
		$html .= $this->display_field_validation($field_params);
		//validation_mesage
		$html .= $this->display_field_validation_message($field_params);
		//values
		$field_name = '<label class="hasTip" title="'.JText::_('COM_PAGESANDITEMS_VALUES_TIP').'"><span class="editlinktip">'.JText::_('COM_PAGESANDITEMS_VALUES').'</span></label>';
		$field_content = '<textarea class="width200" name="field_params[values]" />'.$field_params['values'].'</textarea>';
		$html .= $this->display_field($field_name, $field_content);
		//default value
		$field_name = JText::_('COM_PAGESANDITEMS_DEFAULT_VALUE');
		$field_content = '<input type="text" class="width200" value="'.$field_params['default_value'].'"
name="field_params[default_value]" /><br />'.JText::_('COM_PAGESANDITEMS_DEFAULT_VALUE_SELECT_INFO').' " -;-" <br />'.JText::_('COM_PAGESANDITEMS_EXAMPLE').': value1-;-value2-;-value3';
		$html .= $this->display_field($field_name, $field_content);
		//labels
		$field_name = '<label class="hasTip" title="'.JText::_('COM_PAGESANDITEMS_LABELS_TIP').'"><span class="editlinktip">'.JText::_('COM_PAGESANDITEMS_LABELS').'</span></label>';
		$field_content = '<textarea class="width200" name="field_params[labels]" />'.$field_params['labels'].'</textarea>';
		$html .= $this->display_field($field_name, $field_content);
		//multiselect
		$field_name = JText::_('COM_PAGESANDITEMS_MULTISELECT');
		$field_content = '<input type="checkbox" class="checkbox" ';
		if($this->check_if_field_param_is_present($field_params, 'multiselect')){
			if($field_params['multiselect']=='1'){
				$field_content .= ' checked="checked"';
			}
		}
		$field_content .= 'name="field_params[multiselect]" value="1" />';
		$html .= $this->display_field($field_name, $field_content);
		return $html;
	}

	/*
	function display_item_edit($field, $field_params, $field_values, $field_value, $new_field, $field_id){
		if($new_field){
			//new field
			$field_value = $field_params['default_value'];
		}

		//if multiselect make values into array to check for displaying selection
		if($this->check_if_field_param_is_present($field_params, 'multiselect')){
			$field_value = explode('-;-',$field_value);
		}

		$html = '<div class="field_type_select fieldtype">';
		$field_name = $field->name;
		if($this->check_if_field_param_is_present($field_params, 'validation')){
			$field_name .= '<span class="star">&nbsp;*</span>';
		}

		if($this->check_if_field_param_is_present($field_params, 'description')){
			$field_content = '<div>'.$field_params['description'].'</div>';
		}

		if($this->check_if_field_param_is_present($field_params, 'labels')){
			$labels_array = explode("\n",$field_params['labels']);
		}else{
			$labels_array = array();
		}

		if($this->check_if_field_param_is_present($field_params, 'values')){
			$values_array = explode("\n",$field_params['values']);
		}else{
			$values_array = array();
		}

		$field_content .= '<select name="'.$field_id.'[]" id="'.$field_id.'"';
		if($this->check_if_field_param_is_present($field_params, 'multiselect')){
			$field_content .= ' multiple="multiple"';
		}
		$field_content .= '>';
		for($n = 0; $n < count($values_array); $n++){
			$values_array[$n] = str_replace('"','&quote;', $values_array[$n]);
			$values_array[$n] = trim($values_array[$n]);
			$field_content .= '<option value="'.$values_array[$n].'"';
			if($this->check_if_field_param_is_present($field_params, 'multiselect')){
				if(in_array(trim($values_array[$n]), $field_value)){
					$field_content .= ' selected="selected"';
				}
			}else{
				if(trim($values_array[$n])==$field_value){
					$field_content .= ' selected="selected"';
				}
			}
			$field_content .= '>'.trim($labels_array[$n]).'</option>';
		}
		$field_content .= '</select>';
		$html .= $this->display_field($field_name, $field_content);
		$html .= '</div>';
		return $html;
	}
	*/
	function display_item_edit($field, $field_params, $field_values, $field_value, $new_field, $field_id){
		if($new_field){
			//new field
			$field_value = $field_params['default_value'];
		}

		//if multiselect make values into array to check for displaying selection
		if($this->check_if_field_param_is_present($field_params, 'multiselect')){
			$field_value = explode('-;-',$field_value);
			//trim each value
			$temp_array = array();
			foreach($field_value as $temp){
				$temp_array[] = trim($temp);
			}
			$field_value = $temp_array;
		}

		$html = '<div class="field_type_select fieldtype">';
		$field_name = '&nbsp;';

		//$field->name;
		$field_content = '';
		
		if($this->check_if_field_param_is_present($field_params, 'validation')){
			if($field_params['validation']){
				$field_name = '<span class="star">&nbsp;*</span>';
			}
		}
		/*
		if($this->check_if_field_param_is_present($field_params, 'description')){
			$field_content = '<div>'.$field_params['description'].'</div>';
		}
		*/
		if($this->check_if_field_param_is_present($field_params, 'labels')){
			$labels_array = explode("\n",$field_params['labels']);
		}else{
			$labels_array = array();
		}

		if($this->check_if_field_param_is_present($field_params, 'values')){
			$values_array = explode("\n",$field_params['values']);
		}else{
			$values_array = array();
		}

		$field_content .= '<select name="'.$field_id.'[]" id="'.$field_id.'"';
		if($this->check_if_field_param_is_present($field_params, 'multiselect')){
			$field_content .= ' multiple="multiple"';
		}
		$field_content .= '>';
		for($n = 0; $n < count($values_array); $n++){
			$values_array[$n] = str_replace('"','&quote;', $values_array[$n]);
			$field_content .= '<option value="'.$values_array[$n].'"';
			if($this->check_if_field_param_is_present($field_params, 'multiselect')){
				if(in_array(trim($values_array[$n]), $field_value)){
					$field_content .= ' selected="selected"';
				}
			}else{
				if(trim($values_array[$n])==trim($field_value)){
					$field_content .= ' selected="selected"';
				}
			}
			$field_content .= '>'.$labels_array[$n].'</option>';
		}
		$field_content .= '</select>';
		$html .= $this->display_field($field_name, $field_content);
		$html .= '</div>';
		return $html;
	}

	function field_save($field, $insert_or_update){

		$value_name = 'field_values_'.$field->id;

		//get vars
		$selection_array = JRequest::getVar($value_name, null, 'post', 'array');

		$value = '';

		if(is_array($selection_array)){
			$value = implode('-;-',$selection_array);
			$value = str_replace('"','&quot;',$value);
		}

		return $value;
	}

	function render_field_output($field, $intro_or_full, $readmore_type=0, $editor_id=0){
		$values = explode('-;-',$field->value);
		$html = '';
		for($n = 0; $n < count($values); $n++){
			if($this->get_field_param($field->params, 'multiselect') && $n!=0){
				$html .= '<br />';
			}
			$value = addslashes($values[$n]);
			$value = str_replace('&quote;','"',$value);
			$html .= $value;
		}
		return $html;
	}
}

?>