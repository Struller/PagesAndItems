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
//TEXTAREA
class PagesAndItemsExtensionFieldtypeTextarea extends PagesAndItemsExtensionFieldtype
{
	function display_config_form($plugin, $type_id, $name, $field_params, $field_id){
		if(!$field_id){
			//new field, set defaults here
			$field_params['showFieldName'] = $this->params->get('showFieldName'); //0
			$field_params['no_lines_to_breaks'] = $this->params->get('no_lines_to_breaks'); //0
			$field_params['limit_characters'] = $this->params->get('limit_characters'); //'0'
		}
		$html = '';
		/*
		//New show field name && description
		$html .= $this->makeShowFieldName($field_id,$field_params,true);
		//description
		//$html .= $this->display_field_description($field_params);
		*/
		//New show field name
		$html .= $this->makeShowFieldName($field_id,$field_params);
		//description
		$html .= $this->display_field_description($field_params);


		//validation
		$html .= $this->display_field_validation($field_params);
		//validation_mesage
		$html .= $this->display_field_validation_message($field_params);
		//no_lines_to_breaks
		$field_name = JText::_('COM_PAGESANDITEMS_NO_LINES_TO_BREAKS');
		$field_content = '<input type="checkbox" ';
		if($this->check_if_field_param_is_present($field_params, 'no_lines_to_breaks')){
			if($field_params['no_lines_to_breaks']){
				$field_content .= ' checked="checked"';
			}
		}
		$field_content .= 'name="field_params[no_lines_to_breaks]" value="1" />';
		$html .= $this->display_field($field_name, $field_content);
		//limit_characters
		$field_name = JText::_('COM_PAGESANDITEMS_LIMIT_CHARACTERS');
		$field_content = '<input type="text" value="';
		if($this->check_if_field_param_is_present($field_params, 'limit_characters')){
			$field_content .= $field_params['limit_characters'];
			if($field_params['limit_characters']==''){
				$field_content .= '0';
			}
		}
		$field_content .= '" name="field_params[limit_characters]" /> ';
		$field_content .= JText::_('COM_PAGESANDITEMS_LIMIT_CHARACTERS2');
		$html .= $this->display_field($field_name, $field_content);
		//default value
		//$html .= $this->display_field_default_value($field_params);
		$field_name = JText::_('COM_PAGESANDITEMS_DEFAULT_VALUE');
		//$value = '';
		//if($this->check_if_field_param_is_present($field_params, 'default_value'))
		//{
			$value = $field_params['default_value'];
		//}
		//$value = str_replace('<br />', "\n", $value);
		$field_content = '<textarea cols="100" rows="25"  name="default_value" >'.$value.'</textarea>';
		$html .= $this->display_field($field_name, $field_content);

		//no_lines_to_breaks
		return $html;
	}


	function display_item_edit($field, $field_params, $field_values, $field_value, $new_field, $field_id){
		//if new field, set defaults
		if($new_field){
			$field_value = $field_params['default_value'];
		}

		//make html-breaks into line-breaks
		//$field_value = str_replace('<br />', "\n", $field_value);

		$html = '<div class="field_type_text fieldtype">';
		$html .= '<div class="pi_form_wrapper">';
		$html .= '<div class="pi_width20">';
		$html .= ''; //$field->name;
		
		if($this->check_if_field_param_is_present($field_params, 'validation')){
			if($field_params['validation']){
				$html .= '<span class="star">&nbsp;*</span>';
			}
		}
		else
		{
			$html .= '&nbsp;';
		}
		$html .= '</div>';
		$html .= '<div class="pi_width70">';
		/*
		if($field_params['description']){
			$html .= '<div>'.$field_params['description'].'</div>';
		}
		*/
		$limit_textfield_tags = '';
		if($field_params['limit_characters']){
			$html .= '<script language="javascript" type="text/javascript">'."\n";
			//function to check chars left
			$html .= 'function limitText_'.$field_id.'(limitField, limitCount, limitNum) {'."\n";
			$html .= 'if (limitField.value.length > limitNum) {'."\n";
			$html .= 'limitField.value = limitField.value.substring(0, limitNum);'."\n";
			$html .= '} else {'."\n";
			$html .= ' limitCount.value = limitNum - limitField.value.length;'."\n";
			$html .= '}'."\n";
			$html .= '}'."\n";
			$html .= '</script>'."\n";
			$limit_textfield_tags = 'onKeyDown="limitText_'.$field_id.'(this.form.'.$field_id.',this.form.countdown_'.$field_id.','.$field_params['limit_characters'].');"
onKeyUp="limitText_'.$field_id.'(this.form.'.$field_id.',this.form.countdown_'.$field_id.','.$field_params['limit_characters'].');"';
		}
		$html .= '<textarea class="textarea" cols="60" rows="10" name="'.$field_id.'" id="'.$field_id.'" '.$limit_textfield_tags.'>'.$field_value.'</textarea>';
		if($field_params['limit_characters']){
			$html .= '<br><input readonly type="text" name="countdown_'.$field_id.'" size="3" value="'.$field_params['limit_characters'].'"> '.JText::_('COM_PAGESANDITEMS_CHARACTERS_LEFT');
		}
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	function field_save($field, $insert_or_update){

		$value_name = 'field_values_'.$field->id;
		//get vars
		$value = JRequest::getVar($value_name,'','post','string', JREQUEST_ALLOWRAW );
		$value = addslashes($value);

		//make linebreaks into html-breaks
		//$value = str_replace("\n",'<br />', $value);
		//take out all other html tags
		//$value = strip_tags($value, '<br>');

		return $value;
	}


	function params_save($params_string)
	{
		$default_value = JRequest::getVar('default_value','','post','string', JREQUEST_ALLOWRAW);

		//$default_value = str_replace("\n",'<br />', $default_value);

		$default_value = 'default_value-=-'.$default_value.'[;-)# ]';
		$default_temp = 'default_value-=-'.$this->get_field_param($params_string, 'default_value').'[;-)# ]';
		if(strpos($params_string, 'default_value-=-')){
			$params_string = str_replace($default_temp, $default_value, $params_string);
		}else{
			$params_string .= $default_value;
		}

		//return $params_string;
		return $params_string;
	}



	function render_field_output($field, $intro_or_full, $readmore_type=0, $editor_id=0){
		$value = $field->value;
		if(!$this->get_field_param($field->params, 'no_lines_to_breaks')){
			$value = str_replace("\n",'<br />',$value);
		}
		return addslashes($value);
	}

}

?>