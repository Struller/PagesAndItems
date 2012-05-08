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
/**
*/
require_once(dirname(__FILE__).'/../../../includes/extensions/fieldtype.php');

/*
extension fieldtype PHP
*/
class PagesAndItemsExtensionFieldtypePhp extends PagesAndItemsExtensionFieldtype
{
	function params_base()
	{
		$param[] = 'no_pi_fish_table=1';
		return $param;
	}

	function display_config_form($plugin, $type_id, $name, $field_params, $field_id){

		if(!$field_id){
			$field_params['showFieldName'] = $this->params->get('showFieldName'); //0
			$field_params['render'] = $this->params->get('render'); //'on_save'
			$field_params['display_when_edit'] = $this->params->get('display_when_edit'); //'no'
			$field_params['validation'] = $this->params->get('validation'); //false
		}
		
		$html = '';
		//New show field name
		$html .= $this->makeShowFieldName($field_id,$field_params);
		//description
		$html .= $this->display_field_description($field_params);
		
		//render options
		$field_name = JText::_('COM_PAGESANDITEMS_RENDER_OPTIONS');
		//$field_content = 'code is rendered to content on save (best performance)<br />code is called when the item is viewed<br />';
		$field_content = '<label><input type="radio" class="radio" value="on_save" name="field_params[render]"';
		if($field_params['render']=='on_save' || $field_params['render']==''){
			$field_content .= 'checked="checked"';
		}
		$field_content .= ' />';
		$field_content .= JText::_('COM_PAGESANDITEMS_RENDER_WHEN_SAVE');
		$field_content .= '</label><br />';
		$field_content .= '<label><input type="radio" class="radio" value="on_the_fly" name="field_params[render]" ';
		if($field_params['render']=='on_the_fly'){
			$field_content .= 'checked="checked"';
		}
		$field_content .= ' />';
		$field_content .= JText::_('COM_PAGESANDITEMS_RENDER_ON_THE_FLY');
		$field_content .= '</label>';
		$html .= $this->display_field($field_name, $field_content);


		//display textarea for php code when item edit options
		$field_name = JText::_('COM_PAGESANDITEMS_TEXTAREA_FOR_PHP_CODE');

		$field_content = '<label><input type="radio" class="radio" value="yes" name="field_params[display_when_edit]"';
		if($field_params['display_when_edit']=='yes'){
			$field_content .= 'checked="checked"';
		}
		$field_content .= ' onchange="document.getElementById(\'do_validation\').disabled=false;" />';
		$field_content .= JText::_('COM_PAGESANDITEMS_DISPLAY_PHP_WHEN_EDIT');
		$field_content .= '</label><br />';
		$field_content .= '<label><input type="radio" class="radio" value="no" name="field_params[display_when_edit]" ';
		if($field_params['display_when_edit']=='no' || $field_params['display_when_edit']==''){
			$field_content .= 'checked="checked"';
		}
		$field_content .= ' onchange="document.getElementById(\'do_validation\').checked=false;document.getElementById(\'do_validation\').disabled=true;" />';
		$field_content .= JText::_('COM_PAGESANDITEMS_DISPLAY_NO_PHP_WHEN_EDIT');
		$field_content .= '</label>';
		$html .= $this->display_field($field_name, $field_content);


		//description
		//$html .= $this->display_field_description($field_params);
		//validation
		$field_name = JText::_('COM_PAGESANDITEMS_VALIDATION');
		$field_content = '<input type="checkbox" class="checkbox" ';
		if($this->check_if_field_param_is_present($field_params, 'validation')){
			if($field_params['validation']){
				$field_content .= ' checked="checked"';
			}
		}
		$field_content .= 'name="field_params[validation]" value="not_empty" id="do_validation" /> ';
		$html .= $this->display_field($field_name, $field_content);
		//validation_mesage
		$html .= $this->display_field_validation_message($field_params);
		//default value
		$field_name = JText::_('COM_PAGESANDITEMS_DEFAULT_VALUE');
		$field_content = $this->show_example_vars().'<textarea cols="100" rows="25"  name="default_value" >'.$field_params['default_value'].'</textarea>';
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
			//$params_string = 'string zat erin';
		}else{
			$params_string .= $default_value;
			//$params_string = 'string zat er niet in';
		}

		//return $params_string;
		return $params_string;
	}

	function display_item_edit($field, $field_params, $field_values, $field_value, $new_field, $field_id){

		if($field_params['display_when_edit']=='yes'){
			//if new field, set defaults
			if($new_field){
				$field_value = $field_params['default_value'];
			}

			$html = '<div class="field_type_html fieldtype">';
			$html .= '<div class="pi_form_wrapper">';
			$html .= '<div class="pi_width20">';
			$html .= '&nbsp;';
			//$html .= $field->name;
			
			if($this->check_if_field_param_is_present($field_params, 'validation')){
				if($field_params['validation']){
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
			$html .= $this->show_example_vars();
			$html .= '<textarea class="textarea" cols="60" rows="10" name="'.$field_id.'" id="'.$field_id.'" >'.$field_value.'</textarea>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';
		}else{
			$html = '';
		}

		return $html;
	}

	function show_example_vars(){
		$html = JText::_('COM_PAGESANDITEMS_PHP_VARS_AVAILABLE').': $item_id $field_id $value_id $database. '.JText::_('COM_PAGESANDITEMS_EXAMPLE').': &lt;?php echo \'foo\'; ?&gt;';
		//$html .= '<br />';
		//$html .= 'functions:';
		//$html .= '<br />';
		//$html .= '$this->return_field_value($item_id, $field_id);';
		//$html .= '<br />';
		//$html .= '$this->get_field_value($values_string, $property);';
		return $html;
	}

	function render_field_output($field, $intro_or_full, $readmore_type=0, $editor_id=0){
		//when render output gets triggered is a good point to copy the params default value to the field-value. this exeptional method only happens in this php fieldtype.

		if($this->get_field_param($field->params, 'display_when_edit')=='no'){
			//update value field
			$value = $this->get_field_param($field->params, 'default_value');
			$value = addslashes($value);
			$value_id = $field->value_id;
			$database = JFactory::getDBO();
			$database->setQuery( "UPDATE #__pi_custom_fields_values SET value='$value' WHERE id='$value_id' ");
			$database->query();
		}

		//render html according to render options in field params.
		if($this->get_field_param($field->params, 'render')=='on_save'){
			//pre-render the html
			$value_id = $field->value_id;
			$php_to_render = '<?php $value_id = \''.$value_id.'\';
			$item_id = \''.$field->item_id.'\';
			$field_id = \''.$field->field_id.'\'; ?>';
			if($this->get_field_param($field->params, 'display_when_edit')=='no'){
				//no php-editting on item-level, so grab the default value of field params
				$php_to_render .= $this->get_field_param($field->params, 'default_value');
			}else{
				//php-editting on item level, so get the value from there.
				$php_to_render .= $field->value;
			}


			$html = $this->phpWrapper($php_to_render);
			return $html;
		}else{
			//add a plugin-tag so the content will be rendered when it hits the onpreparecontent event of the pages-and-items content-plugin.
			return '{pi_dynamic_field php '.$field->value_id.'}';
		}
	}

	function field_save($field, $insert_or_update){

		if($this->get_field_param($field->params, 'display_when_edit')=='yes'){

			$value_name = 'field_values_'.$field->id;

			//get vars
			$value = JRequest::getVar($value_name,'','post','string', JREQUEST_ALLOWRAW );
			$value = addslashes($value);
		}else{
			$value = addslashes($this->get_field_param($field->params, 'default_value'));
		}
		$value = str_replace('<br>','<br />', $value);

		return $value;

	}
	
	function phpWrapper($content)
	{
		$database = JFactory::getDBO();
		ob_start();
		eval("?>" . $content);
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
	

	function onDisplay_dynamic_field(&$output, $row, $plugin, $params, $dynamic_field_params)
	{
		//the id of the vlaue field is parsed from the dynamic_field tag: return '{pi_dynamic_field php '.$field->value_id.'}';
		if($this->_name != $plugin)
		{
			return false;
		}
		$value_id = $dynamic_field_params;
		//get database
		$database = JFactory::getDBO();
		//get the value
		$database->setQuery("SELECT field_id, item_id, value FROM #__pi_custom_fields_values WHERE id='$value_id' LIMIT 1");
		$values = $database->loadObjectList();
		$value = $values[0];
		//parse value-id along so we can use this in the php code
		$php_to_render = '<?php $value_id = \''.$value_id.'\';
			$item_id = \''.$value->item_id.'\';
			$field_id = \''.$value->field_id.'\';
			?>';
		$php_to_render .= $value->value;
		//get the output of the processed php
		$output .= $this->phpWrapper($php_to_render);
		//return $html;
		return true;
	}
}

?>