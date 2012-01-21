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
//IMAGE_MULTISIZE
class PagesAndItemsExtensionFieldtypeImage_multisize extends PagesAndItemsExtensionFieldtype
{
	function display_config_form($plugin, $type_id, $name, $field_params, $field_id){
		if(!$field_id){
			//new field, set defaults here
			$field_params['show_field_name'] = $this->params->get('show_field_name'); //0
			$field_params['delete_old_image'] = $this->params->get('delete_old_image'); //0
			$field_params['image_dir'] = $this->params->get('image_dir'); //'images/stories/'
			
			$field_params['max_width_1'] = $this->params->get('max_width_1'); //'400'
			$field_params['max_height_1'] = $this->params->get('max_height_1'); //''
			$field_params['show_src_1'] = $this->params->get('show_src_1'); //0
			$field_params['class_name_1'] = $this->params->get('class_name_1'); //''
			$field_params['only_source_1'] = $this->params->get('only_source_1'); //0
			$field_params['resize_1'] = $this->params->get('resize_1'); //'resize'
			
			$field_params['enable_2'] = $this->params->get('enable_2'); //0
			$field_params['max_width_2'] = $this->params->get('max_width_2'); //''
			$field_params['max_height_2'] = $this->params->get('max_height_2'); //''
			$field_params['show_src_2'] = $this->params->get('show_src_2'); //0
			$field_params['class_name_2'] = $this->params->get('class_name_2'); //''
			$field_params['only_source_2'] = $this->params->get('only_source_2'); //0
			$field_params['resize_2'] = $this->params->get('resize_2'); //'resize'
			
			$field_params['enable_3'] = $this->params->get('enable_3'); //0
			$field_params['max_width_3'] = $this->params->get('max_width_3'); //''
			$field_params['max_height_3'] = $this->params->get('max_height_3'); //''
			$field_params['show_src_3'] = $this->params->get('show_src_3'); //0
			$field_params['class_name_3'] = $this->params->get('class_name_3'); //''
			$field_params['only_source_3'] = $this->params->get('only_source_3'); //0
			$field_params['resize_3'] = $this->params->get('resize_3'); //'resize'
			
			$field_params['enable_4'] = $this->params->get('enable_4'); //0
			$field_params['max_width_4'] = $this->params->get('max_width_4'); //''
			$field_params['max_height_4'] = $this->params->get('max_height_4'); //''
			$field_params['show_src_4'] = $this->params->get('show_src_4'); //0
			$field_params['class_name_4'] = $this->params->get('class_name_4'); //''
			$field_params['only_source_4'] = $this->params->get('only_source_4'); //0
			$field_params['resize_4'] = $this->params->get('resize_4'); //'resize'
			
			$field_params['enable_5'] = $this->params->get('enable_5'); //0
			$field_params['max_width_5'] = $this->params->get('max_width_5'); //''
			$field_params['max_height_5'] = $this->params->get('max_height_5'); //''
			$field_params['show_src_5'] = $this->params->get('show_src_5'); //0
			$field_params['class_name_5'] = $this->params->get('class_name_5'); //''
			$field_params['only_source_5'] = $this->params->get('only_source_5'); //0
			$field_params['resize_5'] = $this->params->get('resize_5'); //'resize'
		}
		
		//description
		$html = $this->display_field_description($field_params);
		//show field name
		$field_name = JText::_('COM_PAGESANDITEMS_SHOW_FIELD_NAME');
		$field_content = '<input type="checkbox" ';
		if(isset($field_params['show_field_name'])){
			$field_content .= ' checked="checked"';
		} 
		$field_content .= 'name="field_params[show_field_name]" value="1" />';
		$html .= $this->display_field($field_name, $field_content);
		//validation
		$field_name = JText::_('COM_PAGESANDITEMS_VALIDATION');
		$field_content = '<input type="checkbox" class="checkbox" ';
		//if($this->check_if_field_param_is_present($field_params, 'validation')){
		if(isset($field_params['validation'])){
			if($field_params['validation']){
				$field_content .= ' checked="checked"';
			}
		} 
		$field_content .= 'name="field_params[validation]" value="not_empty" /> '.JText::_('COM_PAGESANDITEMS_NEED_IMAGE');
		$html .= $this->display_field($field_name, $field_content);
		//validation_mesage
		$html .= $this->display_field_validation_message($field_params);
		//image dir
		$field_name = JText::_('COM_PAGESANDITEMS_IMAGE_DIR');
		$field_content = '<input type="text" class="width200" value="'.$field_params['image_dir'].'" name="field_params[image_dir]" /> '.JText::_('COM_PAGESANDITEMS_IMAGE_DIR_EXAMPLE_B');
		$html .= $this->display_field($field_name, $field_content);
		//old image
		$field_name = JText::_('COM_PAGESANDITEMS_DELETE_OLD_IMAGE');
		$field_content = '<input type="checkbox" class="checkbox" ';
		if(isset($field_params['delete_old_image'])){
			$field_content .= ' checked="checked"';
		} 
		$field_content .= 'name="field_params[delete_old_image]" value="1" />';
		$html .= $this->display_field($field_name, $field_content);
		//show source
		
		$field_name = JText::_('COM_PAGESANDITEMS_SHOW_SRC');
		$field_content = '<input type="checkbox" class="checkbox" ';
		if(isset($field_params['show_src'])){
			if($field_params['show_src']){
				$field_content .= ' checked="checked"';
			}
		} 
		$field_content .= 'name="field_params[show_src]" value="1" />';
		$html .= $this->display_field($field_name, $field_content);
		
		//image 1
		$html .= '<br /><br /><br /><h3>'.JText::_('COM_PAGESANDITEMS_IMAGE').' '.JText::_('COM_PAGESANDITEMS_SIZE').' 1</h3>';
		
			
		//width
		$field_name = JText::_('COM_PAGESANDITEMS_MAX_WIDTH');
		$field_content = '<input type="text" class="width200" value="'.$field_params['max_width_1'].'" name="field_params[max_width_1]" />';
		$html .= $this->display_field($field_name, $field_content);
		//height
		$field_name = JText::_('COM_PAGESANDITEMS_MAX_HEIGHT');
		$field_content = '<input type="text" class="width200" value="'.$field_params['max_height_1'].'" name="field_params[max_height_1]" />';
		$html .= $this->display_field($field_name, $field_content);
		//resize
		
		$field_name = JText::_('COM_PAGESANDITEMS_RESIZE_OPTIONS');
		$field_content = '<label><input type="radio" class="radio" value="none" name="field_params[resize_1]"';
		if($field_params['resize_1']=='none'){
			$field_content .= 'checked="checked"';
		} 
		$field_content .= ' />';
		$field_content .= JText::_('COM_PAGESANDITEMS_NO_RESIZE');
		$field_content .= '</label><br />';
		$field_content .= '<label><input type="radio" class="radio" value="resize" name="field_params[resize_1]"';
		if($field_params['resize_1']=='resize'){
			$field_content .= 'checked="checked"';
		} 
		$field_content .= ' />';
		$field_content .= JText::_('COM_PAGESANDITEMS_DO_RESIZE').'.';
		$field_content .= '</label><br />';
		$field_content .= '<label><input type="radio" class="radio" value="force" name="field_params[resize_1]"';
		if($field_params['resize_1']=='force'){
			$field_content .= 'checked="checked"';
		} 
		$field_content .= ' />';
		$field_content .= JText::_('COM_PAGESANDITEMS_FORCE_RESIZE').'.';
		$field_content .= '</label><br />';
		$html .= $this->display_field($field_name, $field_content);
		//classname
		$field_name = JText::_('COM_PAGESANDITEMS_CLASS_NAME');
		$field_content = '<input type="text" class="width200" value="'.$field_params['class_name_1'].'" name="field_params[class_name_1]" /> '.JText::_('COM_PAGESANDITEMS_CLASS_NAME2');
		$html .= $this->display_field($field_name, $field_content);
		//output_only_source
		$field_name = JText::_('COM_PAGESANDITEMS_OUTPUT_ONLY_SOURCE');
		$field_content = '<input type="checkbox" class="checkbox" ';
		if(isset($field_params['only_source_1'])){
			if($field_params['only_source_1']){
				$field_content .= ' checked="checked"';
			}
		} 
		$field_content .= 'name="field_params[only_source_1]" value="1" />';
		$html .= $this->display_field($field_name, $field_content);
		//template code
		$field_name = JText::_('COM_PAGESANDITEMS_TEMPLATE_CODE');
		if(!$field_id){
			$field_content = '{field_multisize_121 size=1}<br />{field_multisize_121 size=1 output=alt}<br />'.JText::_('COM_PAGESANDITEMS_EXAMPLE_MULTISIZE');
		}else{
			$field_content = '{field_multisize_'.$field_id.' size=1}<br />{field_multisize_'.$field_id.' size=1 output=alt}';
		}
		
		$html .= $this->display_field($field_name, $field_content);
		
		
		//image 2
		$html .= '<br /><br /><br /><h3>'.JText::_('COM_PAGESANDITEMS_IMAGE').' '.JText::_('COM_PAGESANDITEMS_SIZE').' 2</h3>';
		
		//enable
		$field_name = JText::_('COM_PAGESANDITEMS_ENABLE');
		$field_content = '<input type="checkbox" class="checkbox" ';
		if(isset($field_params['enable_2'])){
			if($field_params['enable_2']){
				$field_content .= ' checked="checked"';
			}
		} 
		$field_content .= 'name="field_params[enable_2]" value="1" />';
		$html .= $this->display_field($field_name, $field_content);
		//width
		$field_name = JText::_('COM_PAGESANDITEMS_MAX_WIDTH');
		$field_content = '<input type="text" class="width200" value="'.$field_params['max_width_2'].'" name="field_params[max_width_2]" />';
		$html .= $this->display_field($field_name, $field_content);
		//height
		$field_name = JText::_('COM_PAGESANDITEMS_MAX_HEIGHT');
		$field_content = '<input type="text" class="width200" value="'.$field_params['max_height_2'].'" name="field_params[max_height_2]" />';
		$html .= $this->display_field($field_name, $field_content);
		//resize
		$field_name = JText::_('COM_PAGESANDITEMS_RESIZE_OPTIONS');
		$field_content = '<label><input type="radio" class="radio" value="none" name="field_params[resize_2]"';
		if($field_params['resize_2']=='none'){
			$field_content .= 'checked="checked"';
		} 
		$field_content .= ' />';
		$field_content .= JText::_('COM_PAGESANDITEMS_NO_RESIZE');
		$field_content .= '</label><br />';
		$field_content .= '<label><input type="radio" class="radio" value="resize" name="field_params[resize_2]"';
		if($field_params['resize_2']=='resize'){
			$field_content .= 'checked="checked"';
		} 
		$field_content .= ' />';
		$field_content .= JText::_('COM_PAGESANDITEMS_DO_RESIZE').'.';
		$field_content .= '</label><br />';
		$field_content .= '<label><input type="radio" class="radio" value="force" name="field_params[resize_2]"';
		if($field_params['resize_2']=='force'){
			$field_content .= 'checked="checked"';
		} 
		$field_content .= ' />';
		$field_content .= JText::_('COM_PAGESANDITEMS_FORCE_RESIZE').'.';
		$field_content .= '</label><br />';
		$html .= $this->display_field($field_name, $field_content);
		//classname
		$field_name = JText::_('COM_PAGESANDITEMS_CLASS_NAME');
		$field_content = '<input type="text" class="width200" value="'.$field_params['class_name_2'].'" name="field_params[class_name_2]" /> '.JText::_('COM_PAGESANDITEMS_CLASS_NAME2');
		$html .= $this->display_field($field_name, $field_content);
		//output_only_source
		$field_name = JText::_('COM_PAGESANDITEMS_OUTPUT_ONLY_SOURCE');
		$field_content = '<input type="checkbox" class="checkbox" ';
		if(isset($field_params['only_source_2'])){
			if($field_params['only_source_2']){
				$field_content .= ' checked="checked"';
			}
		} 
		$field_content .= 'name="field_params[only_source_2]" value="1" />';
		$html .= $this->display_field($field_name, $field_content);
		//template code
		$field_name = JText::_('COM_PAGESANDITEMS_TEMPLATE_CODE');
		if(!$field_id){
			$field_content = '{field_multisize_121 size=2}<br />{field_multisize_121 size=1 output=alt}<br />'.JText::_('COM_PAGESANDITEMS_EXAMPLE_MULTISIZE');
		}else{
			$field_content = '{field_multisize_'.$field_id.' size=2}<br />{field_multisize_'.$field_id.' size=2 output=alt}';
		}
		
		$html .= $this->display_field($field_name, $field_content);
		
		//image 3
		$html .= '<br /><br /><br /><h3>'.JText::_('COM_PAGESANDITEMS_IMAGE').' '.JText::_('COM_PAGESANDITEMS_SIZE').' 3</h3>';
		
		//enable
		$field_name = JText::_('COM_PAGESANDITEMS_ENABLE');
		$field_content = '<input type="checkbox" class="checkbox" ';
		if(isset($field_params['enable_3'])){
			if($field_params['enable_3']){
				$field_content .= ' checked="checked"';
			}
		} 
		$field_content .= 'name="field_params[enable_3]" value="1" />';
		$html .= $this->display_field($field_name, $field_content);
		//width
		$field_name = JText::_('COM_PAGESANDITEMS_MAX_WIDTH');
		$field_content = '<input type="text" class="width200" value="'.$field_params['max_width_3'].'" name="field_params[max_width_3]" />';
		$html .= $this->display_field($field_name, $field_content);
		//height
		$field_name = JText::_('COM_PAGESANDITEMS_MAX_HEIGHT');
		$field_content = '<input type="text" class="width200" value="'.$field_params['max_height_3'].'" name="field_params[max_height_3]" />';
		$html .= $this->display_field($field_name, $field_content);
		//resize
		$field_name = JText::_('COM_PAGESANDITEMS_RESIZE_OPTIONS');
		$field_content = '<label><input type="radio" class="radio" value="none" name="field_params[resize_3]"';
		if($field_params['resize_3']=='none'){
			$field_content .= 'checked="checked"';
		} 
		$field_content .= ' />';
		$field_content .= JText::_('COM_PAGESANDITEMS_NO_RESIZE');
		$field_content .= '</label><br />';
		$field_content .= '<label><input type="radio" class="radio" value="resize" name="field_params[resize_3]"';
		if($field_params['resize_3']=='resize'){
			$field_content .= 'checked="checked"';
		} 
		$field_content .= ' />';
		$field_content .= JText::_('COM_PAGESANDITEMS_DO_RESIZE').'.';
		$field_content .= '</label><br />';
		$field_content .= '<label><input type="radio" class="radio" value="force" name="field_params[resize_3]"';
		if($field_params['resize_3']=='force'){
			$field_content .= 'checked="checked"';
		} 
		$field_content .= ' />';
		$field_content .= JText::_('COM_PAGESANDITEMS_FORCE_RESIZE').'.';
		$field_content .= '</label><br />';
		$html .= $this->display_field($field_name, $field_content);
		//classname
		$field_name = JText::_('COM_PAGESANDITEMS_CLASS_NAME');
		$field_content = '<input type="text" class="width200" value="'.$field_params['class_name_3'].'" name="field_params[class_name_3]" /> '.JText::_('COM_PAGESANDITEMS_CLASS_NAME2');
		$html .= $this->display_field($field_name, $field_content);
		//output_only_source
		$field_name = JText::_('COM_PAGESANDITEMS_OUTPUT_ONLY_SOURCE');
		$field_content = '<input type="checkbox" class="checkbox" ';
		if(isset($field_params['only_source_3'])){
			if($field_params['only_source_3']){
				$field_content .= ' checked="checked"';
			}
		} 
		$field_content .= 'name="field_params[only_source_3]" value="1" />';
		$html .= $this->display_field($field_name, $field_content);
		//template code
		$field_name = JText::_('COM_PAGESANDITEMS_TEMPLATE_CODE');
		if(!$field_id){
			$field_content = '{field_multisize_121 size=3}<br />{field_multisize_121 size=3 output=alt}<br />'.JText::_('COM_PAGESANDITEMS_EXAMPLE_MULTISIZE');
		}else{
			$field_content = '{field_multisize_'.$field_id.' size=3}<br />{field_multisize_'.$field_id.' size=3 output=alt}';
		}
		
		$html .= $this->display_field($field_name, $field_content);
		
		//image 4
		$html .= '<br /><br /><br /><h3>'.JText::_('COM_PAGESANDITEMS_IMAGE').' '.JText::_('COM_PAGESANDITEMS_SIZE').' 4</h3>';
		
		//enable
		$field_name = JText::_('COM_PAGESANDITEMS_ENABLE');
		$field_content = '<input type="checkbox" class="checkbox" ';
		if(isset($field_params['enable_4'])){
			if($field_params['enable_4']){
				$field_content .= ' checked="checked"';
			}
		} 
		$field_content .= 'name="field_params[enable_4]" value="1" />';
		$html .= $this->display_field($field_name, $field_content);
		//width
		$field_name = JText::_('COM_PAGESANDITEMS_MAX_WIDTH');
		$field_content = '<input type="text" class="width200" value="'.$field_params['max_width_4'].'" name="field_params[max_width_4]" />';
		$html .= $this->display_field($field_name, $field_content);
		//height
		$field_name = JText::_('COM_PAGESANDITEMS_MAX_HEIGHT');
		$field_content = '<input type="text" class="width200" value="'.$field_params['max_height_4'].'" name="field_params[max_height_4]" />';
		$html .= $this->display_field($field_name, $field_content);
		//resize
		$field_name = JText::_('COM_PAGESANDITEMS_RESIZE_OPTIONS');
		$field_content = '<label><input type="radio" class="radio" value="none" name="field_params[resize_4]"';
		if($field_params['resize_4']=='none'){
			$field_content .= 'checked="checked"';
		} 
		$field_content .= ' />';
		$field_content .= JText::_('COM_PAGESANDITEMS_NO_RESIZE');
		$field_content .= '</label><br />';
		$field_content .= '<label><input type="radio" class="radio" value="resize" name="field_params[resize_4]"';
		if($field_params['resize_4']=='resize'){
			$field_content .= 'checked="checked"';
		} 
		$field_content .= ' />';
		$field_content .= JText::_('COM_PAGESANDITEMS_DO_RESIZE').'.';
		$field_content .= '</label><br />';
		$field_content .= '<label><input type="radio" class="radio" value="force" name="field_params[resize_4]"';
		if($field_params['resize_4']=='force'){
			$field_content .= 'checked="checked"';
		} 
		$field_content .= ' />';
		$field_content .= JText::_('COM_PAGESANDITEMS_FORCE_RESIZE').'.';
		$field_content .= '</label><br />';
		$html .= $this->display_field($field_name, $field_content);
		//classname
		$field_name = JText::_('COM_PAGESANDITEMS_CLASS_NAME');
		$field_content = '<input type="text" class="width200" value="'.$field_params['class_name_4'].'" name="field_params[class_name_4]" /> '.JText::_('COM_PAGESANDITEMS_CLASS_NAME2');
		$html .= $this->display_field($field_name, $field_content);
		//output_only_source
		$field_name = JText::_('COM_PAGESANDITEMS_OUTPUT_ONLY_SOURCE');
		$field_content = '<input type="checkbox" class="checkbox" ';
		if(isset($field_params['only_source_4'])){
			if($field_params['only_source_4']){
				$field_content .= ' checked="checked"';
			}
		} 
		$field_content .= 'name="field_params[only_source_4]" value="1" />';
		$html .= $this->display_field($field_name, $field_content);
		//template code
		$field_name = JText::_('COM_PAGESANDITEMS_TEMPLATE_CODE');
		if(!$field_id){
			$field_content = '{field_multisize_121 size=4}<br />{field_multisize_121 size=4 output=alt}<br />'.JText::_('COM_PAGESANDITEMS_EXAMPLE_MULTISIZE');
		}else{
			$field_content = '{field_multisize_'.$field_id.' size=4}<br />{field_multisize_'.$field_id.' size=4 output=alt}';
		}
		
		$html .= $this->display_field($field_name, $field_content);
		//image 5
		$html .= '<br /><br /><br /><h3>'.JText::_('COM_PAGESANDITEMS_IMAGE').' '.JText::_('COM_PAGESANDITEMS_SIZE').' 5</h3>';
		
		//enable
		$field_name = JText::_('COM_PAGESANDITEMS_ENABLE');
		$field_content = '<input type="checkbox" class="checkbox" ';
		if(isset($field_params['enable_5'])){
			if($field_params['enable_5']){
				$field_content .= ' checked="checked"';
			}
		} 
		$field_content .= 'name="field_params[enable_5]" value="1" />';
		$html .= $this->display_field($field_name, $field_content);
		//width
		$field_name = JText::_('COM_PAGESANDITEMS_MAX_WIDTH');
		$field_content = '<input type="text" class="width200" value="'.$field_params['max_width_5'].'" name="field_params[max_width_5]" />';
		$html .= $this->display_field($field_name, $field_content);
		//height
		$field_name = JText::_('COM_PAGESANDITEMS_MAX_HEIGHT');
		$field_content = '<input type="text" class="width200" value="'.$field_params['max_height_5'].'" name="field_params[max_height_5]" />';
		$html .= $this->display_field($field_name, $field_content);
		//resize
		$field_name = JText::_('COM_PAGESANDITEMS_RESIZE_OPTIONS');
		$field_content = '<label><input type="radio" class="radio" value="none" name="field_params[resize_5]"';
		if($field_params['resize_5']=='none'){
			$field_content .= 'checked="checked"';
		} 
		$field_content .= ' />';
		$field_content .= JText::_('COM_PAGESANDITEMS_NO_RESIZE');
		$field_content .= '</label><br />';
		$field_content .= '<label><input type="radio" class="radio" value="resize" name="field_params[resize_5]"';
		if($field_params['resize_5']=='resize'){
			$field_content .= 'checked="checked"';
		} 
		$field_content .= ' />';
		$field_content .= JText::_('COM_PAGESANDITEMS_DO_RESIZE').'.';
		$field_content .= '</label><br />';
		$field_content .= '<label><input type="radio" class="radio" value="force" name="field_params[resize_5]"';
		if($field_params['resize_5']=='force'){
			$field_content .= 'checked="checked"';
		} 
		$field_content .= ' />';
		$field_content .= JText::_('COM_PAGESANDITEMS_FORCE_RESIZE').'.';
		$field_content .= '</label><br />';
		$html .= $this->display_field($field_name, $field_content);
		//classname
		$field_name = JText::_('COM_PAGESANDITEMS_CLASS_NAME');
		$field_content = '<input type="text" class="width200" value="'.$field_params['class_name_5'].'" name="field_params[class_name_5]" /> '.JText::_('COM_PAGESANDITEMS_CLASS_NAME2');
		$html .= $this->display_field($field_name, $field_content);
		//output_only_source
		$field_name = JText::_('COM_PAGESANDITEMS_OUTPUT_ONLY_SOURCE');
		$field_content = '<input type="checkbox" class="checkbox" ';
		if(isset($field_params['only_source_5'])){
			if($field_params['only_source_5']){
				$field_content .= ' checked="checked"';
			}
		} 
		$field_content .= 'name="field_params[only_source_5]" value="1" />';
		$html .= $this->display_field($field_name, $field_content);
		//template code
		$field_name = JText::_('COM_PAGESANDITEMS_TEMPLATE_CODE');
		if(!$field_id){
			$field_content = '{field_multisize_121 size=5}<br />{field_multisize_121 size=5 output=alt}<br />'.JText::_('COM_PAGESANDITEMS_EXAMPLE_MULTISIZE');
		}else{
			$field_content = '{field_multisize_'.$field_id.' size=5}<br />{field_multisize_'.$field_id.' size=5 output=alt}';
		}
		
		$html .= $this->display_field($field_name, $field_content);
		
		return $html;
	}
	
	function display_item_edit($field, $field_params, $field_values, $field_value, $new_field, $field_id){
		if($new_field){
			//new field
			$src = '';
			$alt = '';
		}else{
			//edit field
			if(isset($field_values['src'])){
				$src = $field_values['src'];
			}else{
				$src = 0;
			}
			if(isset($field_values['alt'])){
				$alt = $field_values['alt'];
			}else{
				$alt = '';
			}
		}
				
		$html = '<div class="field_type_image fieldtype">';
		
		if(isset($field_params['show_field_name'])){
			$html .= '<div>'.$field->name.'</div>';
		}
		if($field_params['description']){
			$html .= '<div>'.$field_params['description'].'</div>';
		} 
		
		$html .= '<div class="pi_form_wrapper">';
		$html .= '<div class="pi_width20">';
		$html .= '<label class="hasTip" title="'.JText::_('COM_PAGESANDITEMS_IMAGE_TIP').'"><span class="editlinktip">'.JText::_('COM_PAGESANDITEMS_IMAGE').'</span></label>';
		$html .= JText::_('COM_PAGESANDITEMS_IMAGE');
		$html .= '</span>';
		if(isset($field_params['validation'])){
			$html .= '<span class="star">&nbsp;*</span>';
		}
		$html .= '</div>';
		$html .= '<div class="pi_width70">';
		$html .= '<script language="javascript"  type="text/javascript">'."\n";
		$html .= 'function check_extension_'.$field_id.'(value){'."\n";
		$html .= 'value = value.toLowerCase();'."\n";
		$html .= 'pos_jpg = value.indexOf(".jpg");'."\n";
		$html .= 'pos_jpeg = value.indexOf(".jpeg");'."\n";
		$html .= 'pos_gif = value.indexOf(".gif");'."\n";
		$html .= 'pos_png = value.indexOf(".png");'."\n";
		$html .= 'if(pos_jpg==-1 && pos_jpeg==-1 && pos_gif==-1 && pos_png==-1){'."\n";
		$html .= 'document.getElementById(\''.$field_id.'_image\').value = \'\';'."\n";
		$html .= 'alert(\'wrong file-type. allowed are: gif, png, jpg and jpeg\')'."\n";
		$html .= '}'."\n";
		$html .= '}'."\n";
		$html .= '</script>'."\n";
		$html .= '<input type="file" value="1" name="'.$field_id.'_image" id="'.$field_id.'_image" onchange="check_extension_'.$field_id.'(this.value);" />';
		$html .= '</div>';
		$html .= '</div>';
		if($src){
			$html .= '<div class="pi_form_wrapper">';
			$html .= '<div class="pi_width20">';
			$html .= '&nbsp;';
			$html .= '</div>';
			$html .= '<div class="pi_width70">';
			$html .= '<div style="width: 400px; overflow-x: auto; overflow-y: hidden;">';
			//$html .= '<img src="../images/stories/'.$src.'" alt="'.$src.'" />';
			$image_dir = $this->alter_image_dir($field_params['image_dir'], $field->item_id);
			$html .= '<img src="'.$this->live_site.$image_dir.$src.'" alt="'.$src.'" />';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '<div class="pi_form_wrapper">';
			$html .= '<div class="pi_width20">';
			$html .= JText::_('COM_PAGESANDITEMS_DELETE_IMAGE');
			$html .= ':</div>';
			$html .= '<div class="pi_width70">';
			$html .= '<input type="checkbox" class="checkbox" name="'.$field_id.'_delete_image" value="1" />';
			$html .= '</div>';
			$html .= '</div>';
			if(isset($field_params['show_src'])){
				$html .= '<div class="pi_form_wrapper">';
				$html .= '<div class="pi_width20">';
				$html .= JText::_('COM_PAGESANDITEMS_SRC');
				$html .= ':</div>';
				$html .= '<div class="pi_width70">';
				$html .= '<input type="text" class="width200" value="';
				//$html .= $field_params['image_dir'];
				$html .= $field_values['src'].'" name="'.$field_id.'_src" id="'.$field_id.'_src" />';
				$html .= '</div>';
				$html .= '</div>';
			}else{
				$html .= '<input type="hidden" class="width200" value="';
				//$html .= $field_params['image_dir'];
				$html .= $field_values['src'].'" name="'.$field_id.'_src" id="'.$field_id.'_src" />';
			}
		}else{
			echo '<input type="hidden" class="width200" value=""  name="'.$field_id.'_src" id="'.$field_id.'_src" />';
		}
		$html .= '<div class="pi_form_wrapper">';
		$html .= '<div class="pi_width20">';
		$html .= JText::_('COM_PAGESANDITEMS_ALT_TEXT');
		$html .= ':</div>';
		$html .= '<div class="pi_width70">';
		$html .= '<input type="text" class="width200" value="'.$alt.'" name="'.$field_id.'_alt" />';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';
		//parse general field settings
		$html .= '<input type="hidden" name="'.$field_id.'_delete_old_image" value="';
		if(isset($field_params['delete_old_image'])){
			$html .= '1';
		}
		$html .=  '" />';
		$html .= '<input type="hidden" name="'.$field_id.'_image_dir" value="';
		if(isset($field_params['image_dir'])){
			$html .= $field_params['image_dir'];
		}
		$html .=  '" />';
		//parse field setting for each of the image-sizes
		//image size 1
		$html .= '<input type="hidden" name="'.$field_id.'_max_width_1" value="'.$field_params['max_width_1'].'" />';
		$html .= '<input type="hidden" name="'.$field_id.'_max_height_1" value="'.$field_params['max_height_1'].'" />';
		$html .= '<input type="hidden" name="'.$field_id.'_resize_1" value="'.$field_params['resize_1'].'" />';
		$html .= '<input type="hidden" name="'.$field_id.'_class_name_1" value="'.$field_params['class_name_1'].'" />';
		if(isset($field_params['only_source_1'])){
			$html .= '<input type="hidden" name="'.$field_id.'_only_source_1" value="'.$field_params['only_source_1'].'" />';
		}
		
		//image size 2
		if(isset($field_params['enable_2'])){
			$html .= '<input type="hidden" name="'.$field_id.'_enable_2" value="'.$field_params['enable_2'].'" />';
		}
		$html .= '<input type="hidden" name="'.$field_id.'_max_width_2" value="'.$field_params['max_width_2'].'" />';
		$html .= '<input type="hidden" name="'.$field_id.'_max_height_2" value="'.$field_params['max_height_2'].'" />';
		$html .= '<input type="hidden" name="'.$field_id.'_resize_2" value="'.$field_params['resize_2'].'" />';
		$html .= '<input type="hidden" name="'.$field_id.'_class_name_2" value="'.$field_params['class_name_2'].'" />';
		if(isset($field_params['only_source_2'])){
			$html .= '<input type="hidden" name="'.$field_id.'_only_source_2" value="'.$field_params['only_source_2'].'" />';
		}
		
		//image size 3
		if(isset($field_params['enable_3'])){
			$html .= '<input type="hidden" name="'.$field_id.'_enable_3" value="'.$field_params['enable_3'].'" />';
		}
		$html .= '<input type="hidden" name="'.$field_id.'_max_width_3" value="'.$field_params['max_width_3'].'" />';
		$html .= '<input type="hidden" name="'.$field_id.'_max_height_3" value="'.$field_params['max_height_3'].'" />';
		$html .= '<input type="hidden" name="'.$field_id.'_resize_3" value="'.$field_params['resize_3'].'" />';
		$html .= '<input type="hidden" name="'.$field_id.'_class_name_3" value="'.$field_params['class_name_3'].'" />';
		if(isset($field_params['only_source_3'])){
			$html .= '<input type="hidden" name="'.$field_id.'_only_source_3" value="'.$field_params['only_source_3'].'" />';
		}
		
		//image size 4
		if(isset($field_params['enable_4'])){
			$html .= '<input type="hidden" name="'.$field_id.'_enable_4" value="'.$field_params['enable_4'].'" />';
		}
		$html .= '<input type="hidden" name="'.$field_id.'_max_width_4" value="'.$field_params['max_width_4'].'" />';
		$html .= '<input type="hidden" name="'.$field_id.'_max_height_4" value="'.$field_params['max_height_4'].'" />';
		$html .= '<input type="hidden" name="'.$field_id.'_resize_4" value="'.$field_params['resize_4'].'" />';
		$html .= '<input type="hidden" name="'.$field_id.'_class_name_4" value="'.$field_params['class_name_4'].'" />';
		if(isset($field_params['only_source_4'])){
			$html .= '<input type="hidden" name="'.$field_id.'_only_source_4" value="'.$field_params['only_source_4'].'" />';
		}
		
		//image size 5
		if(isset($field_params['enable_5'])){
			$html .= '<input type="hidden" name="'.$field_id.'_enable_5" value="'.$field_params['enable_5'].'" />';
		}
		$html .= '<input type="hidden" name="'.$field_id.'_max_width_5" value="'.$field_params['max_width_5'].'" />';
		$html .= '<input type="hidden" name="'.$field_id.'_max_height_5" value="'.$field_params['max_height_5'].'" />';
		$html .= '<input type="hidden" name="'.$field_id.'_resize_5" value="'.$field_params['resize_5'].'" />';
		$html .= '<input type="hidden" name="'.$field_id.'_class_name_5" value="'.$field_params['class_name_5'].'" />';
		if(isset($field_params['only_source_5'])){
			$html .= '<input type="hidden" name="'.$field_id.'_only_source_5" value="'.$field_params['only_source_5'].'" />';
		}
		return $html;
	}
	
	function alter_image_dir($image_dir, $item_id){
		$image_dir = str_replace('$item_id', $item_id, $image_dir);
		return $image_dir;
	}
	
	function render_field_output($field, $intro_or_full, $readmore_type=0, $editor_id=0){
		$html = '';
				
		if($this->get_field_value($field->value, 'src')!=''){
			$file_name_src = $this->get_field_value($field->value, 'src');
			$file_name_array = explode('.',$file_name_src);
			$file_name_stuff = $file_name_array[0];
			$file_name_base_array = explode('_size',$file_name_stuff);
			$file_name_base = $file_name_base_array[0];
			$file_name_extension = $file_name_array[1];
			if(isset($field->output)){
				if($field->output=='alt'){
					
					$html .= $this->get_field_value($field->value, 'alt');
					
				}
			}else{
				$only_source = $this->get_field_param($field->params, 'only_source_'.$field->size);
				$image_dir = $this->get_field_param($field->params, 'image_dir');
				$image_dir = $this->alter_image_dir($image_dir, $field->item_id);
				if(!$only_source){
					//output full image
					$html .= '<img src="';
					$html .= $image_dir;
					$html .= $file_name_base.'_size'.$field->size.'.'.$file_name_extension;
					$html .= '" alt="'.$this->get_field_value($field->value, 'alt').'"';
					if($this->get_field_param($field->params, 'class_name')!=''){
						$html .= ' class="'.$this->get_field_param($field->params, 'class_name').'"';
					}
					$html .= ' />';
				}else{
					//only output source
					$html .= $image_dir;
					$html .= '/'.$file_name_base.'_size'.$field->size.'.'.$file_name_extension;
				}
			}
		}
		
		return $html;
	}
	
	function field_save($field, $insert_or_update){
		
		$value_name = 'field_values_'.$field->id;
		
		$image = $value_name.'_image';
		$src = JRequest::getVar($value_name.'_src', false);
		$alt = JRequest::getVar($value_name.'_alt');
		$delete_old_image = JRequest::getVar($value_name.'_delete_old_image');
		$delete_image = JRequest::getVar($value_name.'_delete_image', false);
		$image_dir = JRequest::getVar($value_name.'_image_dir', false);
		$image_dir = $this->alter_image_dir($image_dir, $field->item_id);
		
		$max_width_1 = JRequest::getVar($value_name.'_max_width_1');
		$max_height_1 = JRequest::getVar($value_name.'_max_height_1');
		$resize_1 = JRequest::getVar($value_name.'_resize_1');
		$class_name_1 = JRequest::getVar($value_name.'_class_name_1');
		$only_source_1 = JRequest::getVar($value_name.'_only_source_1');
		
		$enable_2 = JRequest::getVar($value_name.'_enable_2');
		$max_width_2 = JRequest::getVar($value_name.'_max_width_2');
		$max_height_2 = JRequest::getVar($value_name.'_max_height_2');
		$resize_2 = JRequest::getVar($value_name.'_resize_2');
		$class_name_2 = JRequest::getVar($value_name.'_class_name_2');
		$only_source_2 = JRequest::getVar($value_name.'_only_source_2');
		
		$enable_3 = JRequest::getVar($value_name.'_enable_3');
		$max_width_3 = JRequest::getVar($value_name.'_max_width_3');
		$max_height_3 = JRequest::getVar($value_name.'_max_height_3');
		$resize_3 = JRequest::getVar($value_name.'_resize_3');
		$class_name_3 = JRequest::getVar($value_name.'_class_name_3');
		$only_source_3 = JRequest::getVar($value_name.'_only_source_3');
		
		$enable_4 = JRequest::getVar($value_name.'_enable_4');
		$max_width_4 = JRequest::getVar($value_name.'_max_width_4');
		$max_height_4 = JRequest::getVar($value_name.'_max_height_4');
		$resize_4 = JRequest::getVar($value_name.'_resize_4');
		$class_name_4 = JRequest::getVar($value_name.'_class_name_4');
		$only_source_4 = JRequest::getVar($value_name.'_only_source_4');
		
		$enable_5 = JRequest::getVar($value_name.'_enable_5');
		$max_width_5 = JRequest::getVar($value_name.'_max_width_5');
		$max_height_5 = JRequest::getVar($value_name.'_max_height_5');
		$resize_5 = JRequest::getVar($value_name.'_resize_5');
		$class_name_5 = JRequest::getVar($value_name.'_class_name_5');
		$only_source_5 = JRequest::getVar($value_name.'_only_source_5');
		
		$image_upload = false;
		
		//check if there is an image to upload
		$userfile_name = strtolower($_FILES[$image]['name']);
		if($userfile_name && !$delete_image){
			$image_upload = true;
		}
		
		//get file name and extension
		$old_name = '';
		$extension = '';
		if($userfile_name){
			$file_temp = explode('.',$userfile_name);
			$old_name = $file_temp[0];
			$extension = $file_temp[1];
		}
		
		//if image edit, delete old image
		if(($userfile_name && $src) || $delete_image){
			$source_bits = explode('/',$src);
			$source_bits = array_reverse($source_bits);
			$src_delete = $source_bits[0];
			$src_file_temp = explode('.',$src_delete);
			$src_old_name = $src_file_temp[0];
			$src_extension = $src_file_temp[1];
			$src_old_name2 = explode('_size',$src_old_name);
			$src_old_name_part = $src_old_name2[0];
			if(file_exists(JPATH_ROOT.'/'.$image_dir.'/'.$src_old_name_part.'_size1.'.$src_extension)){
				unlink(JPATH_ROOT.'/'.$image_dir.'/'.$src_old_name_part.'_size1.'.$src_extension);
			}
			if(file_exists(JPATH_ROOT.'/'.$image_dir.'/'.$src_old_name_part.'_size2.'.$src_extension)){
				unlink(JPATH_ROOT.'/'.$image_dir.'/'.$src_old_name_part.'_size2.'.$src_extension);
			}
			if(file_exists(JPATH_ROOT.'/'.$image_dir.'/'.$src_old_name_part.'_size3.'.$src_extension)){
				unlink(JPATH_ROOT.'/'.$image_dir.'/'.$src_old_name_part.'_size3.'.$src_extension);
			}
			if(file_exists(JPATH_ROOT.'/'.$image_dir.'/'.$src_old_name_part.'_size4.'.$src_extension)){
				unlink(JPATH_ROOT.'/'.$image_dir.'/'.$src_old_name_part.'_size4.'.$src_extension);
			}
			if(file_exists(JPATH_ROOT.'/'.$image_dir.'/'.$src_old_name_part.'_size5.'.$src_extension)){
				unlink(JPATH_ROOT.'/'.$image_dir.'/'.$src_old_name_part.'_size5.'.$src_extension);
			}
		}
		
		//upload image
		if($image_upload==true){
						
			$userfile_tmp = $_FILES[$image]['tmp_name'];
			$userfile_size = $_FILES[$image]['size'];
			$userfile_type = $_FILES[$image]['type'];
			
			if (isset($_FILES[$image]['name'])){
			
				//upload so make the folder here if we need to
				if (!JFolder::exists(JPATH_SITE.DS.$image_dir)){
					JFolder::create(JPATH_SITE.DS.$image_dir);
				}
				
				//check extension
				$allowed_ext = "jpg jpeg gif png";
				$allowed_extensions = explode(" ", $allowed_ext);
				if (!in_array($extension, $allowed_extensions)){
					die('wrong file-type. allowed are: '.$allowed_ext);
				}
				
				//rewrite jpeg to jpg
				if($extension=='jpeg'){
					$extension = 'jpg';
				}
				
				//rename file if already exist
				if(file_exists(JPATH_ROOT.'/'.$image_dir.'/'.$old_name.'_size1.'.$extension)){
					$j = 2;
					while (file_exists(JPATH_ROOT.'/'.$image_dir.'/'.$old_name.'-'.$j."_size1.".$extension)){
						$j = $j + 1;
					}
					$new_name = $old_name . "-" . $j;
				}else{
					$new_name = $old_name;
				}
				
				//replace spaces by underscores
				$new_name = str_replace(' ', '_', $new_name);
				
				//upload original image
				$prod_img = JPATH_ROOT.'/'.$image_dir.'/'.$new_name.'_ori.'.$extension;
				
				
				if(!move_uploaded_file($userfile_tmp, $prod_img)){
					//die('Problem uploading image. File is too big. Try making the image smaller with an image-editor (like Photoshop or Gimp) and try again.');
				}
				
				//get sizes and ratio
				$sizes = getimagesize($prod_img);
				$old_width = $sizes[0];
				$old_height = $sizes[1];
				$aspect_ratio = $sizes[1]/$sizes[0]; 
				
				//size 1
				$prod_img_size_1 = JPATH_ROOT.'/'.$image_dir.'/'.$new_name.'_size1.'.$extension;
				copy($prod_img,$prod_img_size_1);
				$this->modify_image($prod_img_size_1, $old_width, $old_height, $max_width_1, $max_height_1, $resize_1, $aspect_ratio, $extension);
				
				//size 2
				if($enable_2){
					$prod_img_size_2 = JPATH_ROOT.'/'.$image_dir.'/'.$new_name.'_size2.'.$extension;
					copy($prod_img,$prod_img_size_2);
					$this->modify_image($prod_img_size_2, $old_width, $old_height, $max_width_2, $max_height_2, $resize_2, $aspect_ratio, $extension);
				}
				
				//size 3
				if($enable_3){
					$prod_img_size_3 = JPATH_ROOT.'/'.$image_dir.'/'.$new_name.'_size3.'.$extension;
					copy($prod_img,$prod_img_size_3);
					$this->modify_image($prod_img_size_3, $old_width, $old_height, $max_width_3, $max_height_3, $resize_3, $aspect_ratio, $extension);
				}
				
				//size 4
				if($enable_4){
					$prod_img_size_4 = JPATH_ROOT.'/'.$image_dir.'/'.$new_name.'_size4.'.$extension;
					copy($prod_img,$prod_img_size_4);
					$this->modify_image($prod_img_size_4, $old_width, $old_height, $max_width_4, $max_height_4, $resize_4, $aspect_ratio, $extension);
				}
				
				//size 5
				if($enable_5){
					$prod_img_size_5 = JPATH_ROOT.'/'.$image_dir.'/'.$new_name.'_size5.'.$extension;
					copy($prod_img,$prod_img_size_5);
					$this->modify_image($prod_img_size_5, $old_width, $old_height, $max_width_5, $max_height_5, $resize_5, $aspect_ratio, $extension);
				}
									
				//delete original image
				if(file_exists($prod_img)){
					unlink($prod_img);
				}
				
			}
			$src = $new_name.'_size1.'.$extension;
							
		}//end if an image has been uploaded
		
		
		$value = 'src-=-'.$src.'[;-)# ]alt-=-'.$alt.'[;-)# ]';
		if($delete_image){
			$value = '';
		}
		return $value;
	}
	
	function modify_image($prod_img, $old_width, $old_height, $new_width, $new_height, $resizetype, $aspect_ratio, $extension){ 
		
		
		if($resizetype=='none'){
			//no resize for this image
			$new_width = $old_width;
			$new_height = $old_height;
			
		}elseif($resizetype=='resize'){
			
						
				
			//widthratio
			if($new_width){
				$widthratio = $old_width/$new_width; 
			}else{
				$widthratio = 0;
			}
			
			//height ratio
			if($new_height){
				$heightratio = $old_height/$new_height;
			}else{
				$heightratio = 0;
			}
			
			if($widthratio <= $heightratio){ 
				//vertical image or squere
				$new_width = $old_width/$heightratio;
			}else{ 
				//horizontal image
				$new_height = $old_height/$widthratio;
			}
			
			//round numbers
			$new_width = round($new_width); 
			$new_height = round($new_height); 
			
			if($new_width>$old_width){
				//no resize for this image
				$new_width = $old_width;
				$new_height = $old_height;
			}
				
		}elseif($resizetype=='force'){
			$new_width = $new_width;
			$new_height = $new_height;
		}
		
		$imgnew = imagecreatetruecolor($new_width,$new_height);
		if($extension=='jpg'){
			$srcimg=imagecreatefromjpeg($prod_img);
		}elseif($extension=='gif'){
			$srcimg=imagecreatefromgif($prod_img);
		}elseif($extension=='png'){
			$srcimg=imagecreatefrompng($prod_img);
		}
		if(function_exists('imagecopyresampled')){
			imagecopyresampled($imgnew,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg));
		}else{
			imagecopyresized($imgnew,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg));
		}
		if($extension=='jpg'){
			imagejpeg($imgnew,$prod_img,90);
		}elseif($extension=='gif'){
			imagegif($imgnew,$prod_img);
		}elseif($extension=='png'){
			imagepng($imgnew,$prod_img);
		}
		
		
		/*
		if($crop){
				
			$left = 0;
			$top = 0;
			$need_cropping = 0;
			if($new_width>$crop_width){
				//need to crop horizontally
				$need_cropping = 1;
				$extra_space = $new_width-$crop_width;
				$left = round($extra_space/2);
			}
			
			if($new_height>$crop_height){
				//need to crop vertically
				$need_cropping = 1;
				$extra_space = $new_height-$crop_height;
				$top = round($extra_space/2);
			}
			
			if($need_cropping){
				//set new size
				$canvas = imagecreatetruecolor($crop_width, $crop_height);
				
				//get image
				if($extension=='jpg'){
					$current_image=imagecreatefromjpeg($prod_img)or die('Problem In opening Source Image');
				}elseif($extension=='gif'){
					$current_image=imagecreatefromgif($prod_img)or die('Problem In opening Source Image');
				}
				
				//do crop
				imagecopy($canvas, $current_image, 0, 0, $left, $top, $crop_width, $crop_height);
				
				//put image away
				if($extension=='jpg'){
					imagejpeg($canvas,$prod_img,100)or die('Problem In saving');
				}elseif($extension=='gif'){
					imagegif($canvas,$prod_img)or die('Problem In saving');
				}
			}
		}//end crop
		
		
		$size_ratio_upload = $old_width/$old_height;
			
			//if ratio==1 it is a square, if bigger then 1 image is wide, if smaller then 1 image is tall
			if($size_ratio_upload <= 1){ 
				//vertical image or square
				
				//resize to the thumbnail-width, so only adjust the height
				$resize_ratio = $old_width/$new_width;
				$new_height = $old_height/$resize_ratio;
				if($new_height<$crop_height){
					$resize_ratio = $old_height/$new_height;
					$new_width = $old_width/$resize_ratio;
					$new_height = $crop_height;
				}
										
			}else{ 
				//horizontal image
				
				//resize to the thumbnail-height, so only adjust the width
				$resize_ratio = $old_height/$new_height;
				$new_width = $old_width/$resize_ratio;
				if($new_width<$crop_width){
					$resize_ratio = $old_width/$new_width;
					$new_height = $old_height/$resize_ratio;
					$new_width = $crop_width;
				}
											
			}
		*/
	}
	
	function delete_image($temp_old_src, $image_dir){
		$temp_old_src_array = explode('.',$temp_old_src);
		$temp_old_src_name = $temp_old_src_array[0];
		$temp_old_src_name = explode('_size',$temp_old_src_name);
		$temp_old_src_name = $temp_old_src_name[0];
		$temp_old_src_extension = $temp_old_src_array[1];
		if(file_exists(JPATH_ROOT.'/'.$image_dir.'/'.$temp_old_src_name.'_size1.'.$temp_old_src_extension)){
			unlink(JPATH_ROOT.'/'.$image_dir.'/'.$temp_old_src_name.'_size1.'.$temp_old_src_extension);
		}
		if(file_exists(JPATH_ROOT.'/'.$image_dir.'/'.$temp_old_src_name.'_size2.'.$temp_old_src_extension)){
			unlink(JPATH_ROOT.'/'.$image_dir.'/'.$temp_old_src_name.'_size2.'.$temp_old_src_extension);
		}
		if(file_exists(JPATH_ROOT.'/'.$image_dir.'/'.$temp_old_src_name.'_size3.'.$temp_old_src_extension)){
			unlink(JPATH_ROOT.'/'.$image_dir.'/'.$temp_old_src_name.'_size3.'.$temp_old_src_extension);
		}
		if(file_exists(JPATH_ROOT.'/'.$image_dir.'/'.$temp_old_src_name.'_size4.'.$temp_old_src_extension)){
			unlink(JPATH_ROOT.'/'.$image_dir.'/'.$temp_old_src_name.'_size4.'.$temp_old_src_extension);
		}
		if(file_exists(JPATH_ROOT.'/'.$image_dir.'/'.$temp_old_src_name.'_size5.'.$temp_old_src_extension)){
			unlink(JPATH_ROOT.'/'.$image_dir.'/'.$temp_old_src_name.'_size5.'.$temp_old_src_extension);
		}
	}
	
	//function to delete images when item is deleted, from PI version 1.4.7
	function item_delete($item_id, $type_id, $field){
	
		//get image dir
		$image_dir = $this->get_field_param($field->params, 'image_dir');
		$image_dir = $this->alter_image_dir($image_dir, $item_id);
		
		$field_id = $field->id;
		
		//get value of image
		$this->db->setQuery("SELECT value "
		."FROM #__pi_custom_fields_values "
		."WHERE (field_id='$field_id' AND item_id='$item_id') "
		."LIMIT 1 "
		);
		$field_rows = $this->db->loadObjectList();
		foreach($field_rows as $field_row){
			$field_value = $field_row->value;
		}
		$images_array = explode('[;-)# ]',$field_value);
		$image_stuff = $images_array[0];
		$image_stuff_array = explode('-=-',$image_stuff);
		$image = $image_stuff_array[1];
		if($image){
			$this->delete_image($image, $image_dir);
		}
		
	}
	
	//function to delete images when field or itemtype is deleted, from PI version 1.4.7
	function field_delete($field){
	
		//get image dir
		$image_dir = $this->get_field_param($field->params, 'image_dir');
		
	
		$field_id = $field->id;
		
		//get values of images
		$this->db->setQuery("SELECT item_id, value "
		."FROM #__pi_custom_fields_values "
		."WHERE field_id='$field_id' "
		);
		$field_rows = $this->db->loadObjectList();
		foreach($field_rows as $field_row){
			$field_value = $field_row->value;
			$item_id = $field_row->item_id;
		}
		
		
		$images_array = explode('[;-)# ]',$field_value);
		$image_stuff = $images_array[0];
		$image_stuff_array = explode('-=-',$image_stuff);
		$image = $image_stuff_array[1];
		if($image){
			$image_dir = $this->alter_image_dir($image_dir, $item_id);
			$this->delete_image($image, $image_dir);
		}
	}
}

?>