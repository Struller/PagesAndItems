<?php
/**
* @version		2.0.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2011 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

//no direct access
if(!defined('_JEXEC'))
{
	die('Restricted access');
}
require_once(dirname(__FILE__).'/../../../includes/extensions/fieldtype.php');
//CHECKBOX
class PagesAndItemsExtensionFieldtypeCheckbox extends PagesAndItemsExtensionFieldtype
{
	function display_config_form($plugin, $type_id, $name, $field_params, $field_id)
	{
		if(!$field_id)
		{
			//new field, set defaults here
			$field_params['values'] = $this->params->get('values'); //'';
			$field_params['labels'] = $this->params->get('labels'); //'';
			$field_params['default_value'] = $this->params->get('default_value'); //'';
			$field_params['number_checkboxes'] = $this->params->get('number_checkboxes'); //'';
			
		}
		//description
		$html = $this->display_field_description($field_params);
		//validation
		$html .= $this->display_field_validation($field_params);
		//validation_mesage
		$html .= $this->display_field_validation_message($field_params);
		//number of checkboxes
		$field_content = '<input type="text" style="width: 20px;" value="'.$field_params['number_checkboxes'].'" name="field_params[number_checkboxes]" />';
		$html .= $this->display_field(JText::_('COM_PAGESANDITEMS_NUMBER_OF_CHECKBOXES'), $field_content);
		//values
		$field_name = '<label class="hasTip" title="'.JText::_('COM_PAGESANDITEMS_VALUES_TIP').'"><span class="editlinktip">'.JText::_('COM_PAGESANDITEMS_VALUES').'</span></label>';
		$field_content = '<textarea class="width200" name="field_params[values]" />'.$field_params['values'].'</textarea>';
		$html .= $this->display_field($field_name, $field_content);
		//default value
		$field_name = JText::_('COM_PAGESANDITEMS_DEFAULT_VALUE');
		$field_content = '<input type="text" class="width200" value="'.$field_params['default_value'].'" name="field_params[default_value]" /><br />'.JText::_('COM_PAGESANDITEMS_DEFAULT_VALUE_CHECKBOX_INFO');
		$html .= $this->display_field($field_name, $field_content);
		//labels
		$field_name = '<label class="hasTip" title="'.JText::_('COM_PAGESANDITEMS_LABELS_TIP').'"><span class="editlinktip">'.JText::_('COM_PAGESANDITEMS_LABELS').'</span></label>';
		$field_content = '<textarea class="width200" name="field_params[labels]" />'.$field_params['labels'].'</textarea>';
		$html .= $this->display_field($field_name, $field_content);
					
		return $html;
	}
	
	function display_item_edit($field, $field_params, $field_values, $field_value, $new_field, $field_id){
	
		if($new_field){
			//new field
			$field_value = $field_params['default_value'];
		}
		$array_checked_boxes = explode('-;-',$field_value);
		$html = '<div class="field_type_checkbox fieldtype">';
		$html .= '<div class="pi_form_wrapper">';
		$html .= '<div class="pi_width20">';
		$html .= $field->name;
		if($this->check_if_field_param_is_present($field_params, 'validation')){
			if($field_params['validation']){
				$html .= '<span class="star">&nbsp;*</span>';
			}
		}
		$html .= '</div>';
		$html .= '<div class="pi_width70">';
		if($field_params['description']){
					$html .= '<div>'.$field_params['description'].'</div>';
				}
				
				$labels_array = explode("\n",$field_params['labels']);
				$values_array = explode("\n",$field_params['values']);
				for($n = 0; $n < count($values_array); $n++){
					$html .= '<label>';
					$html .= '<input type="checkbox" class="checkbox" value="'.trim($values_array[$n]).'" name="'.$field_id.'[]" id="'.$field_id.'_'.$n.'" ';
					if(in_array(trim($values_array[$n]), $array_checked_boxes)){
						$html .= ' checked="checked"';
					}
					$html .= ' /> '.trim($labels_array[$n]).'</label><br />';
								
				}
				$html .= '<input type="hidden" value="'.$n.'" id="total_checkboxes_field'.$field_id.'" />';
				$html .= '<input type="hidden" value="'.$field_params['number_checkboxes'].'" id="min_checkboxes_field'.$field_id.'" />';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';
		
		return $html;
	}
	
	function render_field_output($field, $intro_or_full, $readmore_type=0, $editor_id=0){
		$values = explode('-;-',$field->value);
		/*
		if($field->value==''){
			if($this->get_field_param($field->params, 'default_value')){
				$temp = $this->get_field_param($field->params, 'default_value');
				$values = explode('-;-',$temp);
			}
		}
		*/
		$html = '';
		if(count($values)==1){
			$html .= $values[0];
		}else{
			$first = 1;
			foreach($values as $value){
				if(!$first){
					$html .= '<br />';
					
				}
				$html .= $value;
				$first = 0;
			}
		}
		return $html;
	}
	
	function field_save($field, $insert_or_update){
		
		$value_name = 'field_values_'.$field->id;
		
		$value = '';
		
		//get vars
		$checkboxes = JRequest::getVar($value_name, null, 'post', 'array');
		
		if(is_array($checkboxes)){
			$value = implode('-;-',$checkboxes);
			$value = str_replace('"','&quot;',$value);
		}
		
		return $value;
	}
}

?>