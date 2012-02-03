<?php
/**
* @version		2.1.5
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
//EDITOR
class PagesAndItemsExtensionFieldtypeEditor extends PagesAndItemsExtensionFieldtype
{
	function display_config_form($plugin, $type_id, $name, $field_params, $field_id){
		if(!$field_id)
		{
			//new field, set defaults here
			$field_params['showFieldName'] = $this->params->get('showFieldName'); //0
			//$field_params['show_field_name'] = $this->params->get('show_field_name'); //1;
			$field_params['default_value'] = $this->params->get('default_value'); //'';
			$field_params['display_plugin_buttons'] = $this->params->get('display_plugin_buttons'); //1;
			$field_params['height'] = $this->params->get('height'); //'500';
		}
		
		$html = '';
		///New show field name
		$html .= $this->makeShowFieldName($field_id,$field_params);
		//description
		$html .= $this->display_field_description($field_params);
		//show field name
		/*$field_name = JText::_('COM_PAGESANDITEMS_SHOW_FIELD_NAME');
		$field_content = '<input type="checkbox" ';
		if($this->check_if_field_param_is_present($field_params, 'show_field_name')){
			if($field_params['show_field_name']){
				$field_content .= ' checked="checked"';
			}
		}
		$field_content .= 'name="field_params[show_field_name]" value="1" />';
		$html .= $this->display_field($field_name, $field_content);
		*/
		
		//display_plugin_buttons
		$field_name = JText::_('COM_PAGESANDITEMS_DISPLAY_PLUGIN_BUTTONS');
		$field_content = '<input type="checkbox" ';
		if($this->check_if_field_param_is_present($field_params, 'display_plugin_buttons')){
			if($field_params['display_plugin_buttons']){
				$field_content .= ' checked="checked"';
			}
		}
		$field_content .= 'name="field_params[display_plugin_buttons]" value="1" />';
		$html .= $this->display_field($field_name, $field_content);
		//height
		$field_name = JText::_('COM_PAGESANDITEMS_HEIGHT');
		$field_content = '<input type="text" name="field_params[height]" value="';
		$field_content .= $field_params['height'];
		$field_content .= '" />';
		$html .= $this->display_field($field_name, $field_content);
		//default value
		$field_name = JText::_('COM_PAGESANDITEMS_DEFAULT_VALUE');
		$field_content = '<textarea cols="100" rows="25"  name="default_value" >'.$field_params['default_value'].'</textarea>';
		$html .= $this->display_field($field_name, $field_content);
		return $html;
	}


	function params_save($params_string){
		$default_value = JRequest::getVar('default_value','','post','string', JREQUEST_ALLOWRAW);

		$default_value = str_replace('<br>','<br />', $default_value);
		$default_value = 'default_value-=-'.$default_value.'[;-)# ]';
		$default_temp = 'default_value-=-'.$this->get_field_param($params_string, 'default_value').'[;-)# ]';
		if(strpos($params_string, 'default_value-=-')){
			$params_string = str_replace($default_temp, $default_value, $params_string);
		}else{
			$params_string .= $default_value;
		}
		return $params_string;
	}

	function display_item_edit($field, $field_params, $field_values, $field_value, $new_field, $field_id){
		$html = '';
		//if new field, set defaults
		if($new_field){
			$field_value = $field_params['default_value'];
		}

		//$html .= $field_id;
		$html .= '<div class="field_type_editor fieldtype">';
		/*
		if($this->check_if_field_param_is_present($field_params, 'show_field_name')){
			if($field_params['show_field_name']=='1'){
				$html .= '<div>'.$field->name.'</div>';
			}
		}
		*/
		/*
		if($this->check_if_field_param_is_present($field_params, 'description')){
			if($field_params['description']){
				$html .= '<div>'.$field_params['description'].'</div>';
			}
		}
		*/
		//check if buttons should be displayed
		$display_plugin_buttons = false;
		if($this->check_if_field_param_is_present($field_params, 'display_plugin_buttons')){
			if($field_params['display_plugin_buttons']=='1'){
				$display_plugin_buttons = true;
			}
		}
		$html .= stripslashes($this->pi_config['plugin_syntax_cheatcheat']);
		$field_value = stripslashes($field_value);
		$field_value = htmlspecialchars($field_value);
		$editor =& JFactory::getEditor();
		//$html .= $editor->display($field_id,  $field_value , '80%', $field_params['height'], '75', '20', $display_plugin_buttons);
		$html .= $editor->display($field_id,  $field_value , '98%', $field_params['height'], '75', '20', $display_plugin_buttons);
		$html .= '<div style="clear: both; height: 5px; line-height: 1px;">&nbsp;</div>';
		$html .= '</div>';
		return $html;
	}

	function render_field_output($field, $intro_or_full, $readmore_type=0, $editor_id=0)
	{

		$html = $field->value;
		$length = strlen($html);
		$pos = strpos($html, '<hr id="system-readmore" />');
		$pos3 = strpos($html, '<hr id=\"system-readmore\" />');
		if($pos){
			$pos2 = $pos + 27;
			$introtext = substr($html, 0, $pos);
			$fulltext = substr($html, $pos2, $length);
		}elseif($pos3){
			$pos4 = $pos3 + 29;
			$introtext = substr($html, 0, $pos3);

			$fulltext = substr($html, $pos4, $length);
		}else{
			$introtext = $html;
			$fulltext = $html;
		}
		if($intro_or_full=='intro'){
			$html = $introtext;
		}else{
			$html = $fulltext;
		}
		return $html;
	}

	function field_save($field, $insert_or_update){
		$value_name = 'field_values_'.$field->id;

		//get vars
		$value = addslashes(JRequest::getVar($value_name,'','post','string', JREQUEST_ALLOWRAW ));

		$value = str_replace('<br>','<br />', $value);

		return addslashes($value);
	}
}

?>