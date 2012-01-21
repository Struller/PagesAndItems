<?php
/**
* @version		2.1.2
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
//TEXT
class PagesAndItemsExtensionFieldtypeText extends PagesAndItemsExtensionFieldtype
{
	function display_config_form($plugin, $type_id, $name, $field_params, $field_id){
		if(!$field_id)
		{
			$field_params['showFieldName'] = $this->params->get('showFieldName'); //0
			$field_params['max_chars'] = $this->params->get('max_chars'); //''
			$field_params['validation'] = $this->params->get('validation'); //''
		}
		$html = '';
		
		//New show field name
		$html .= $this->makeShowFieldName($field_id,$field_params);
		//description
		$html .= $this->display_field_description($field_params);
		
		//validation
		$field_name = JText::_('COM_PAGESANDITEMS_VALIDATION');
		$field_content = '<select name="field_params[validation]">';
		$field_content .= '<option value="0" ';
		if($field_params['validation']==0){
			$field_content .= 'selected="selected"';
		}
		$field_content .= '>';
		$field_content .= JText::_('COM_PAGESANDITEMS_NO_VALIDATION');
		$field_content .= '</option>';
		$field_content .= '<option value="not_empty" ';
		if($field_params['validation']=='not_empty'){
			$field_content .= 'selected="selected"';
		}
		$field_content .= '>';
		$field_content .= JText::_('COM_PAGESANDITEMS_NOT_EMPTY');
		$field_content .= '</option>';
		$field_content .= '<option value="emailadress" ';
		if($field_params['validation']=='emailadress'){
			$field_content .= 'selected="selected"';
		}
		$field_content .= '>';
		$field_content .= JText::_('COM_PAGESANDITEMS_EMAILADRESS');
		$field_content .= '</option>';
		$field_content .= '</select>';
		$html .= $this->display_field($field_name, $field_content);
		//validation_mesage
		$html .= $this->display_field_validation_message($field_params);
		//max characters
		$field_name = JText::_('COM_PAGESANDITEMS_FIELD_MAX_CHARS');
		$field_content = '<input type="text" class="width200" value="'.$field_params['max_chars'].'" name="field_params[max_chars]" />';
		$html .= $this->display_field($field_name, $field_content);
		//default value
		$html .= $this->display_field_default_value($field_params);
		return $html;
	}

	function display_item_edit($field, $field_params, $field_values, $field_value, $new_field, $field_id)
	{
		if($new_field)
		{
			//new field
			$field_value = $field_params['default_value'];
		}
		$html = '';
		$html .= '<div class="field_type_text fieldtype">';
		$field_name = '&nbsp;'; //$field->name;
		$field_content = '';
		
		if($this->check_if_field_param_is_present($field_params, 'validation')){
			if($field_params['validation']){
				$field_name = '<span class="star">&nbsp;*</span>';
			}
		}
		/*
		$field_content = '';
		if($field_params['description'])
		{
			$field_content .= '<div>'.$field_params['description'].'</div>';
		}
		*/
		$field_content .= '<input type="text" class="width200" value="'.$field_value.'" ';
		if($field_params['max_chars']!='')
		{
			$field_content .= 'maxlength="'.$field_params['max_chars'].'" ';
		}
		$field_content .= 'name="'.$field_id.'[]" id="'.$field_id.'" />';
		$html .= $this->display_field($field_name, $field_content);
		$html .= '</div>';
		return $html;
	}

	function render_field_output($field, $intro_or_full, $readmore_type=0, $editor_id=0){
		return addslashes($field->value);
	}
}

?>