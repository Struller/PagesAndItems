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
//RADIO
class PagesAndItemsExtensionFieldtypeRadio extends PagesAndItemsExtensionFieldtype
{
	function display_config_form($plugin, $type_id, $name, $field_params, $field_id){
		if(!$field_id){
			//new field, set defaults here
			$field_params['values'] = $this->params->get('values'); //''
			$field_params['labels'] = $this->params->get('labels'); //''
			$field_params['default_value'] = $this->params->get('default_value'); //''
			$field_params['validation'] = $this->params->get('validation'); //''
		}
		$html = '';
		//show field name
		$field_name = JText::_('COM_PAGESANDITEMS_SHOW_FIELD_NAME');
		$field_content = '<input type="checkbox" ';
		if($this->check_if_field_param_is_present($field_params, 'show_field_name')){
			if($field_params['show_field_name']){
				$field_content .= ' checked="checked"';
			} 
		}
		$field_content .= 'name="field_params[show_field_name]" value="1" />';
		$html .= $this->display_field($field_name, $field_content);
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
		$html .= $this->display_field_default_value($field_params);
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
		$html = '<div class="field_type_radio fieldtype">';
		$field_name = '';
		if($this->check_if_field_param_is_present($field_params, 'show_field_name')){
			if($field_params['show_field_name']=='1'){
				$field_name .= $field->name;
			}
		}
		if($this->check_if_field_param_is_present($field_params, 'validation')){
			if($field_params['validation']){
				$field_name .= '<span class="star">&nbsp;*</span>';
			}
		}
		$field_content = '';
		if($field_params['description']){
			$field_content .= '<div>'.$field_params['description'].'</div>';
		}
					
		$labels_array = explode("\n",$field_params['labels']);
		$values_array = explode("\n",$field_params['values']);
		for($n = 0; $n < count($values_array); $n++){
			$field_content .= '<label>';
			$field_content .= '<input type="radio" class="radio" value="'.trim($values_array[$n]).'" name="'.$field_id.'[]" id="'.$field_id.'_'.$n.'"';
			if(trim($values_array[$n])==$field_value){
				$field_content .= ' checked="checked"';
			}
			$field_content .= ' /> '.trim($labels_array[$n]).'</label><br />';
						
		}
		$html .= $this->display_field($field_name, $field_content);
		$html .= '</div>';
		return $html;
	}
	
	function render_field_output($field, $intro_or_full, $readmore_type=0, $editor_id=0){
		return $field->value;
	}
}

?>