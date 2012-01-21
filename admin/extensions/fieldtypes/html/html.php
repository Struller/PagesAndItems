<?php
/**
* @version		2.1.3
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
/**

*/
require_once(dirname(__FILE__).'/../../../includes/extensions/fieldtype.php');
/*
* @package		PagesAndItems
HTML
*/

class PagesAndItemsExtensionFieldtypeHtml extends PagesAndItemsExtensionFieldtype
{


	function display_config_form($plugin, $type_id, $name, $field_params, $field_id)
	{
		if(!$field_id)
		{
			$field_params['showFieldName'] = $this->params->get('showFieldName'); //0
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
		//default value
		$field_name = JText::_('COM_PAGESANDITEMS_DEFAULT_VALUE');
		$field_content = '<textarea cols="100" rows="25"  name="default_value" >'.$field_params['default_value'].'</textarea>';
		$html .= $this->display_field($field_name, $field_content);
		return $html;
	}

	function params_save($params_string)
	{
		//$default_value = JRequest::getVar('default_value','','post','string', JREQUEST_ALLOWHTML);
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


	function display_item_edit($field, $field_params, $field_values, $field_value, $new_field, $field_id)
	{
		if($new_field)
		{
			$field_value = $field_params['default_value'];
		}

		$html = '<div class="field_type_html fieldtype">';
		$html .= '<div class="pi_form_wrapper">';
		$html .= '<div class="pi_width20">';



		$html .= '&nbsp;'; //$field->name.':';

		
		if($this->check_if_field_param_is_present($field_params, 'validation'))
		{
			if($field_params['validation'])
			{
				$html .= '<span class="star">&nbsp;*</span>';
			}
		}
		
		$html .= '</div>';
		$html .= '<div class="pi_width70">';
		/*
		if($field_params['description']){
			$html .= '<div>'.$field_params['description'].'</div>';
		}
		*/
		if(strpos($field_value, '</textarea>')){
			$field_value = str_replace('</textarea>', '&lt;&#47;textarea&gt;', $field_value);
		}
		$html .= '<textarea class="textarea" cols="60" rows="10" name="'.$field_id.'" id="'.$field_id.'" >'.$field_value.'</textarea>';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';


		return $html;
	}

	function render_field_output($field, $intro_or_full, $readmore_type=0, $editor_id=0)
	{
		return addslashes($field->value);
	}

	function field_save($field, $insert_or_update){

		$value_name = 'field_values_'.$field->id;

		//get vars
		//$value = JRequest::getVar($value_name,'','post','string', JREQUEST_ALLOWHTML );
		$value = JRequest::getVar($value_name,'','post','string', JREQUEST_ALLOWRAW);
		$value = addslashes($value);
		$value = str_replace('<br>','<br />', $value);
		//$value = 'soep';
		return $value;

	}
}

?>