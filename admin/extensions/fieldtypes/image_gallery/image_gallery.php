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
//IMAGE_GALLERY
class PagesAndItemsExtensionFieldtypeImage_gallery extends PagesAndItemsExtensionFieldtype
{
	function display_config_form($plugin, $type_id, $name, $field_params, $field_id){
		if(!$field_id){
			//new field, set defaults here
			$field_params['show_field_name'] = $this->params->get('show_field_name'); //0
			$field_params['resize'] = $this->params->get('resize'); //0
			$field_params['max_width'] = $this->params->get('max_width'); //''
			$field_params['max_height'] = $this->params->get('max_height'); //''
			$field_params['max_width_thumb'] = $this->params->get('max_width_thumb'); //''
			$field_params['max_height_thumb'] = $this->params->get('max_height_thumb'); //''
			$field_params['delete_old_image'] = $this->params->get('delete_old_image'); //0
			$field_params['show_src'] = $this->params->get('show_src'); //0
			$field_params['class_name'] = $this->params->get('class_name'); //''
			$field_params['class_name_thumb'] = $this->params->get('class_name_thumb'); //''
			$field_params['only_source'] = $this->params->get('only_source'); //0
			$field_params['image_dir'] = $this->params->get('image_dir'); //'images/stories/'
			$field_params['crop'] = $this->params->get('crop'); //0
		}
		
		//description
		$html = $this->display_field_description($field_params);
		//show field name
		$field_name = JText::_('COM_PAGESANDITEMS_SHOW_FIELD_NAME');
		$field_content = '<input type="checkbox" ';
		if($this->check_if_field_param_is_present($field_params, 'show_field_name')){
			$field_content .= ' checked="checked"';
		} 
		$field_content .= 'name="field_params[show_field_name]" value="1" />';
		$html .= $this->display_field($field_name, $field_content);
		//validation
		$field_name = JText::_('COM_PAGESANDITEMS_VALIDATION');
		$field_content = '<input type="checkbox" class="checkbox" ';
		if($this->check_if_field_param_is_present($field_params, 'validation')){
			if($field_params['validation']){
				$field_content .= ' checked="checked"';
			}
		} 
		$field_content .= 'name="field_params[validation]" value="not_empty" /> '.JText::_('COM_PAGESANDITEMS_NEED_IMAGE');
		$html .= $this->display_field($field_name, $field_content);
		//validation_mesage
		$html .= $this->display_field_validation_message($field_params);
		//width
		$field_name = JText::_('COM_PAGESANDITEMS_FIELD_MAX_WIDTH_THUMB3');
		$field_content = '<input type="text" class="width200" value="'.$field_params['max_width_thumb'].'" name="field_params[max_width_thumb]" />';
		$html .= $this->display_field($field_name, $field_content);
		//height
		$field_name = JText::_('COM_PAGESANDITEMS_FIELD_MAX_HEIGHT_THUMB3');
		$field_content = '<input type="text" class="width200" value="'.$field_params['max_height_thumb'].'" name="field_params[max_height_thumb]" />';
		$html .= $this->display_field($field_name, $field_content);
		//resize
		$field_name = JText::_('COM_PAGESANDITEMS_FIELD_RESIZE3');
		$field_content = '<input type="checkbox" class="checkbox" ';
		if($this->check_if_field_param_is_present($field_params, 'resize')){
			if($field_params['resize']){
				$field_content .= ' checked="checked"';
			}
		} 
		$field_content .= 'name="field_params[resize]" value="1" />';
		$html .= $this->display_field($field_name, $field_content);
		//width
		$field_name = JText::_('COM_PAGESANDITEMS_MAX_WIDTH');
		$field_content = '<input type="text" class="width200" value="'.$field_params['max_width'].'" name="field_params[max_width]" />';
		$html .= $this->display_field($field_name, $field_content);
		//height
		$field_name = JText::_('COM_PAGESANDITEMS_MAX_HEIGHT');
		$field_content = '<input type="text" class="width200" value="'.$field_params['max_height'].'" name="field_params[max_height]" />';
		$html .= $this->display_field($field_name, $field_content);
		//crop
		$field_name = JText::_('COM_PAGESANDITEMS_FIELD_CROP3');
		$field_content = '<input type="checkbox" class="checkbox" ';
		if($this->check_if_field_param_is_present($field_params, 'crop')){
			if($field_params['crop']){
				$field_content .= ' checked="checked"';
			}
		} 
		$field_content .= 'name="field_params[crop]" value="1" /> '.JText::_('COM_PAGESANDITEMS_FIELD_CROP3_INFO');
		$html .= $this->display_field($field_name, $field_content);
		//old image
		$field_name = JText::_('COM_PAGESANDITEMS_DELETE_OLD_IMAGE');
		$field_content = '<input type="checkbox" class="checkbox" ';
		if($this->check_if_field_param_is_present($field_params, 'delete_old_image')){
			$field_content .= ' checked="checked"';
		} 
		$field_content .= 'name="field_params[delete_old_image]" value="1" />';
		$html .= $this->display_field($field_name, $field_content);
		//show source
		$field_name = JText::_('COM_PAGESANDITEMS_SHOW_SRC');
		$field_content = '<input type="checkbox" class="checkbox" ';
		if($this->check_if_field_param_is_present($field_params, 'show_src')){
			if($field_params['show_src']){
				$field_content .= ' checked="checked"';
			}
		} 
		$field_content .= 'name="field_params[show_src]" value="1" />';
		$html .= $this->display_field($field_name, $field_content);
		//classname
		$field_name = JText::_('COM_PAGESANDITEMS_CLASS_NAME');
		$field_content = '<input type="text" class="width200" value="'.$field_params['class_name'].'" name="field_params[class_name]" /> '.JText::_('COM_PAGESANDITEMS_CLASS_NAME2');
		$html .= $this->display_field($field_name, $field_content);
		//classname
		$field_name = JText::_('COM_PAGESANDITEMS_FIELD_CLASS_NAME_THUMB3');
		$field_content = '<input type="text" class="width200" value="'.$field_params['class_name_thumb'].'" name="field_params[class_name_thumb]" /> '.JText::_('COM_PAGESANDITEMS_CLASS_NAME2');
		$html .= $this->display_field($field_name, $field_content);
		//output_only_source
		$field_name = JText::_('COM_PAGESANDITEMS_OUTPUT_ONLY_SOURCE');
		$field_content = '<input type="checkbox" class="checkbox" ';
		if($this->check_if_field_param_is_present($field_params, 'only_source')){
			if($field_params['only_source']){
				$field_content .= ' checked="checked"';
			}
		} 
		$field_content .= 'name="field_params[only_source]" value="1" />';
		$html .= $this->display_field($field_name, $field_content);
		//image dir
		$field_name = JText::_('COM_PAGESANDITEMS_IMAGE_DIR');
		$field_content = '<input type="text" class="width200" value="'.$field_params['image_dir'].'" name="field_params[image_dir]" /> '.JText::_('COM_PAGESANDITEMS_IMAGE_DIR_EXAMPLE');
		$html .= $this->display_field($field_name, $field_content);
		return $html;
	}
	
	function display_item_edit($field, $field_params, $field_values, $field_value, $new_field, $field_id){
		
		$html = '';
		
		//javascript to show hide edit fields for already uploaded images
		$html .= '<script language="javascript"  type="text/javascript">'."\n";
		//function to check extension
		$html .= 'function pi_show_hide_edit_fields(id){'."\n";
		$html .= 'state = document.getElementById(id).style.display;'."\n";
		$html .= 'if(state==\'block\'){'."\n";
		$html .= 'document.getElementById(id).style.display = \'none\';'."\n";
		$html .= '}else{'."\n";
		$html .= 'document.getElementById(id).style.display = \'block\';'."\n";
		$html .= '}'."\n";
		$html .= '}'."\n";
		$html .= '</script>'."\n"; 
		
		$image_dir = '';
		if($this->check_if_field_param_is_present($field_params, 'image_dir')){
			$image_dir = $field_params['image_dir'];
		}
				
		$html .= '<div class="field_type_image fieldtype">';
	
		if($this->check_if_field_param_is_present($field_params, 'show_field_name')){
			$html .= '<div>'.$field->name.'</div>';
		}
		if($field_params['description']){
			$html .= '<div>'.$field_params['description'].'</div>';
		} 		
		
		$images_array = explode('[:-)# ]',$field_value);
		for($n = 0; $n < (count($images_array)-1); $n++){
			$image = $images_array[$n];
			$image_array = explode('-;-',$image);
			$temp_src = $image_array[0];
			$temp_alt = $image_array[1];
			$temp_description = $image_array[2];
			$html .= '<div class="pi_form_wrapper">';
			$html .= '<div class="pi_width20">';
			$html .= '<a href="javascript:pi_show_hide_edit_fields(\''.str_replace('.','',$temp_src).'\');">';
			$html .= '<img src="../'.$image_dir.'/'.$temp_src.'" alt="'.$temp_alt.'" style="width: 100px; border: 0;" />';
			$html .= '</a>';
			$html .= '</div>';
			$html .= '<div class="pi_width70">';
			$html .= '<input type="button" onclick="pi_show_hide_edit_fields(\''.str_replace('.','',$temp_src).'\');" value="wijzigen" />';
			$html .= '<input style="margin-left: 50px;" type="text" name="'.$field_id.'_order[]" value="'.($n+1).'" size="3" /> volgorde';
			$html .= '<label style="margin-left: 50px;"><input type="checkbox" name="'.$field_id.'_delete[]" value="'.$n.'" /> verwijderen</label>';
			$html .= '</div>';
			$html .= '</div>';
					
			$html .= '<div style="display: none;" id="'.str_replace('.','',$temp_src).'">';
			$html .= '<div class="pi_form_wrapper">';
			$html .= '<div class="pi_width20">';
			$html .= '<label class="hasTip" title="'.JText::_('COM_PAGESANDITEMS_IMAGE_TIP').'"><span class="editlinktip">'.JText::_('COM_PAGESANDITEMS_IMAGE').'</span></label>';
			$html .= JText::_('COM_PAGESANDITEMS_IMAGE');
			$html .= '</span>';
			$html .= '</div>';
			$html .= '<div class="pi_width70">';
			$html .= '<input type="file" name="'.$field_id.'_src[]" onchange="check_extension_'.$field_id.'(this);" />';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '<div class="pi_form_wrapper">';
			$html .= '<div class="pi_width20">';
			$html .= JText::_('COM_PAGESANDITEMS_ALT_TEXT');
			$html .= ':</div>';
			$html .= '<div class="pi_width70">';
			$html .= '<input type="text" class="width200" value="'.$temp_alt.'" name="'.$field_id.'_alt[]" />';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '<div class="pi_form_wrapper">';
			$html .= '<div class="pi_width20">';
			$html .= strtolower(JText::_('COM_PAGESANDITEMS_DESCRIPTION'));
			$html .= ':</div>';
			$html .= '<div class="pi_width70">';
			$html .= '<textarea class="width200 ig_comment" name="'.$field_id.'_description[]">'.$temp_description.'</textarea>';
			$html .= '<input type="hidden" name="'.$field_id.'_old_src[]" value="'.$temp_src.'" />';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';
		}
		/*
		//display one empty set of fields for one image
		$html .= '<div class="pi_form_wrapper">';
		$html .= '<div class="pi_width20">';
		$html .= '<span onmouseover="return overlib(\''.JText::_('COM_PAGESANDITEMS_IMAGE_TIP').'\', CAPTION, \''.JText::_('COM_PAGESANDITEMS_IMAGE').'\', BELOW, RIGHT);" onmouseout="return nd();"  class="editlinktip">';
		$html .= JText::_('COM_PAGESANDITEMS_IMAGE');
		$html .= '</span>';
		$html .= '</div>';
		$html .= '<div class="pi_width70">';
		$html .= '<input type="file" name="'.$field_id.'_src[]" onchange="check_extension_'.$field_id.'(this);" />';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '<div class="pi_form_wrapper">';
		$html .= '<div class="pi_width20">';
		$html .= JText::_('COM_PAGESANDITEMS_ALT_TEXT');
		$html .= ':</div>';
		$html .= '<div class="pi_width70">';
		$html .= '<input type="text" class="width200" value="" name="'.$field_id.'_alt[]" />';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '<div class="pi_form_wrapper">';
		$html .= '<div class="pi_width20">';
		$html .= strtolower(JText::_('COM_PAGESANDITEMS_DESCRIPTION'));
		$html .= ':</div>';
		$html .= '<div class="pi_width70">';
		$html .= '<textarea class="width200 ig_comment" name="'.$field_id.'_description[]"></textarea>';
		$html .= '</div>';
		$html .= '</div>';
		*/		
		
		$html .= '<script language="javascript"  type="text/javascript">'."\n";
		//function to check extension
		$html .= 'function check_extension_'.$field_id.'(thisthing){'."\n";
		$html .= 'value = thisthing.value.toLowerCase();'."\n";
		$html .= 'pos_jpg = value.indexOf(".jpg");'."\n";
		$html .= 'pos_jpeg = value.indexOf(".jpeg");'."\n";
		$html .= 'pos_gif = value.indexOf(".gif");'."\n";
		$html .= 'if(pos_jpg==-1 && pos_jpeg==-1 && pos_gif==-1){'."\n";
		$html .= 'thisthing.value = \'\';'."\n";
		$html .= 'alert(\'wrong file-type. allowed are: gif, jpg and jpeg\')'."\n";
		$html .= '}'."\n";
		$html .= '}'."\n";
		
		//function to add new image
		$html .= 'function new_image_'.$field_id.'(){'."\n";
		$html .= 'pi_image = \''.JText::_('COM_PAGESANDITEMS_IMAGE').'\';'."\n";
		$html .= 'pi_image_tooltip = \''.JText::_('COM_PAGESANDITEMS_IMAGE_TIP').'\';'."\n";
		$html .= 'extra_code = \'<div class="pi_form_wrapper"><div class="pi_width20">\';'."\n";
		$html .= 'extra_code += \'<label class="hasTip" title="'.JText::_('COM_PAGESANDITEMS_IMAGE_TIP').'"><span class="editlinktip">'.JText::_('COM_PAGESANDITEMS_IMAGE').'</span></label>\';'."\n";
		$html .= 'extra_code += \'</div><div class="pi_width70">\';'."\n";
		$html .= 'extra_code += \'<input type="file" name="'.$field_id.'_src[]" onchange="check_extension_'.$field_id.'(this);" />\''."\n";
		$html .= 'extra_code += \'</div></div><div class="pi_form_wrapper"><div class="pi_width20">\';'."\n";
		$html .= 'extra_code += \''.JText::_('COM_PAGESANDITEMS_ALT_TEXT').'\';'."\n";
		$html .= 'extra_code += \':</div><div class="pi_width70">\';'."\n";
		$html .= 'extra_code += \'<input type="text" class="width200" value="" name="'.$field_id.'_alt[]" />\';'."\n";
		$html .= 'extra_code += \'</div></div><div class="pi_form_wrapper"><div class="pi_width20">\';'."\n";
		$html .= 'extra_code += \''.strtolower(JText::_('COM_PAGESANDITEMS_DESCRIPTION')).'\';'."\n";
		$html .= 'extra_code += \':</div><div class="pi_width70">\';'."\n";
		$html .= 'extra_code += \'<textarea class="width200 ig_comment" rows="10" cols="10" name="'.$field_id.'_description[]"></textarea>\';'."\n";
		$html .= 'extra_code += \'</div></div>\';'."\n";
		$html .= 'var new_div = document.createElement("div");'."\n";
		$html .= 'new_div.innerHTML = extra_code;'."\n";
		$html .= 'var parent = document.getElementById(\''.$field_id.'_new_images\');'."\n";
		$html .= 'parent.appendChild(new_div);'."\n";
		$html .= '}'."\n";
		$html .= '</script>'."\n";
			
		$html .= '<div id="'.$field_id.'_new_images">';
		$html .= '</div>';
		
		$html .= '<div class="new_image_button pi_form_wrapper"><input type="button" value="'.JText::_('COM_PAGESANDITEMS_FIELD_NEW_IMAGE').'" onclick="new_image_'.$field_id.'()" /></div>';
		$html .= '</div>';
		$html .= '<input type="hidden" name="'.$field_id.'_max_width" value="'.$field_params['max_width'].'" />';
		$html .= '<input type="hidden" name="'.$field_id.'_max_height" value="'.$field_params['max_height'].'" />';
		$html .= '<input type="hidden" name="'.$field_id.'_max_width_thumb" value="'.$field_params['max_width_thumb'].'" />';
		$html .= '<input type="hidden" name="'.$field_id.'_max_height_thumb" value="'.$field_params['max_height_thumb'].'" />';
		$html .= '<input type="hidden" name="'.$field_id.'_crop" value="'.$field_params['crop'].'" />';
		$html .= '<input type="hidden" name="'.$field_id.'_resize" value="';
		if($this->check_if_field_param_is_present($field_params, 'resize')){
			$html .= '1';
		}
		$html .=  '" />';
		$html .= '<input type="hidden" name="'.$field_id.'_image_dir" value="';
		if($this->check_if_field_param_is_present($field_params, 'image_dir')){
			$html .= $field_params['image_dir'];
		}
		$html .=  '" />';
		return $html;
	}
	
	function render_field_output($field, $intro_or_full, $readmore_type=0, $editor_id=0){
		$html = '';
		
		$html .= '<div id="pi_image_gallery_wrapper">';
		$html .= '<div id="photos" class="galleryview">';
		
		
		$images_array = explode('[:-)# ]',$field->value);
		for($n = 0; $n < (count($images_array)-1); $n++){
			$image = $images_array[$n];
			$image_array = explode('-;-',$image);
			$temp_src = $image_array[0];
			$temp_alt = $image_array[1];
			$temp_description = $image_array[2];
			$src_id = str_replace('.','',$temp_src);
			$html .= '<div class="panel">';
			$html .= '<img src="'.$this->get_field_param($field->params, 'image_dir').'/'.$temp_src.'" />';
			$html .= '<div class="panel-overlay">';
			$html .= $temp_description;
			$html .= '</div>';
			$html .= '</div>';
		}
		
		$html .= '<ul class="filmstrip">';
		$images_array = explode('[:-)# ]',$field->value);
		for($n = 0; $n < (count($images_array)-1); $n++){
			$image = $images_array[$n];
			$image_array = explode('-;-',$image);
			$temp_src = $image_array[0];
			$temp_alt = $image_array[1];
			$temp_description = $image_array[2];
			$src_id = str_replace('.','',$temp_src);
			$src_array = explode('.',$temp_src);
			$temp_name = $src_array[0];
			$temp_ext = $src_array[1];
			$html .= '<li><img src="'.$this->get_field_param($field->params, 'image_dir').'/'.$temp_name.'_thumb.'.$temp_ext.'" alt="'.addslashes($temp_alt).'" /></li>';
		}
		$html .= '</ul>';
		
		$html .= '</div>';
		$html .= '</div>';
		
		return $html;
	}
	
	function field_save($field, $insert_or_update){
		
		//field identifier
		$value_name = 'field_values_'.$field->id;
		
		//get array of values of the file-elements (upload fields)
		$image = $value_name.'_src';
		
		//get field config things
		$new_width = JRequest::getVar($value_name.'_max_width');
		$new_height = JRequest::getVar($value_name.'_max_height');
		$new_width_thumb = JRequest::getVar($value_name.'_max_width_thumb');
		$new_height_thumb = JRequest::getVar($value_name.'_max_height_thumb');
		$resize = JRequest::getVar($value_name.'_resize');
		$delete_image = JRequest::getVar($value_name.'_delete_image', false);
		$image_dir = JRequest::getVar($value_name.'_image_dir', false);
		$crop = JRequest::getVar($value_name.'_crop', false);
		
		//get arrays
		$description_array = JRequest::getVar($value_name.'_description', array(), 'post', 'array');
		$alt_array = JRequest::getVar($value_name.'_alt', array(), 'post', 'array');
		$old_src_array = JRequest::getVar($value_name.'_old_src', array(), 'post', 'array');
		$order_array = JRequest::getVar($value_name.'_order', array(), 'post', 'array');
		$delete_array = JRequest::getVar($value_name.'_delete', array(), 'post', 'array');
		
		//define allowed extensions
		$allowed_extensions = array('jpg','gif','jpeg');
		
		//make name_array and temp_name_array
		$name_array = array();
		$tmp_name_array = array();
		if($_FILES[$image]['name']){
			//echo 'er is een upload';
			while(list($key,$value) = each($_FILES[$image]['name'])){
				//if($value){
					$name_array[] = $value;
				///}
				//$value = '';
			}
			while(list($key,$value) = each($_FILES[$image]['tmp_name'])){
				$tmp_name_array[] = $value;
			}
		}
		
		//print_r($name_array);
		
		
		
		//make array of already uploaded images for reordering
		$images_array = array();
		$total_old_images = 0;
		for($n = 0; $n < count($old_src_array); $n++){
			$temp_name = $name_array[$n];
			$temp_tmp_name = $tmp_name_array[$n];
			$temp_alt = $alt_array[$n];
			$temp_description = $description_array[$n];
			$temp_order = $order_array[$n];
			if(in_array($n, $delete_array)){
				$temp_delete = 1;
			}else{
				$temp_delete = 0;
			}
			$images_array[] = array($old_src_array[$n], $temp_name, $temp_tmp_name, $temp_alt, $temp_description, $temp_order, $temp_delete);
			$total_old_images = $n+1;
		}
		
		
		//reorder
		if($total_old_images){
			foreach ($images_array as $key => $row) {
				$order[$key]  = $row[5];    
			}
			array_multisort($order, SORT_ASC, $images_array);
		}
		
		//print_r($name_array);
		//exit;
		
		//add the new images
		for($n = $total_old_images; $n < count($alt_array); $n++){
			//echo $n;
			$temp_name = $name_array[$n];
			$temp_tmp_name = $tmp_name_array[$n];
			$temp_alt = $alt_array[$n];
			$temp_description = $description_array[$n];
			$temp_order = '';//new images have no order, its just here to prevent undefined index-notices
			$temp_delete = 0;
			$images_array[] = array('', $temp_name, $temp_tmp_name, $temp_alt, $temp_description, $temp_order, $temp_delete);
		}
			
		//print_r($images_array);
		//exit;
		
		//start doing the actual processing
		$value_string = '';
		for($n = 0; $n < count($images_array); $n++){
		
			//get name and extension
			$file = $images_array[$n][1];
			if($file){
				$temp = explode('.',$file);
				$file_name = strtolower($temp[0]);
				$extension = strtolower($temp[1]);
			}else{
				$file_name = '';
				$extension = '';
			}
			
			//rewrite jpeg to jpg
			if($extension=='jpeg'){
				$extension = 'jpg';
			}
			
			//make file_name unique
			$file_name = $this->make_filename_unique($file_name, $extension, $image_dir);
			
			//if not empty
			if($images_array[$n][0] || $images_array[$n][2]){
			
				//if delete
				if($images_array[$n][6]){
					//delete image
					$this->delete_image($images_array[$n][0], $image_dir);
				}else{
					//do not delete
					
					//if upload
					if($images_array[$n][1] && in_array($extension, $allowed_extensions)){
										
						//upload original image
						$prod_img = dirname(__FILE__).'/../../../../'.$image_dir.'/'.$file_name.'.'.$extension;
						if(move_uploaded_file($images_array[$n][2], $prod_img)){
							
							//get sizes and ratio
							$sizes = getimagesize($prod_img);
							$old_width = $sizes[0];
							$old_height = $sizes[1];
							$aspect_ratio = $sizes[1]/$sizes[0]; 				
							
							//resize small
							$prod_img_small = dirname(__FILE__).'/../../../../'.$image_dir.'/'.$file_name.'_thumb.'.$extension;
							copy($prod_img,$prod_img_small);
							$this->modify_image($prod_img_small, $old_width, $old_height, $new_width_thumb, $new_height_thumb, 1, $aspect_ratio, $extension);
							
							//resize normal
							$this->modify_image($prod_img, $old_width, $old_height, $new_width, $new_height, 1, $aspect_ratio, $extension);
							
						}
						$image_name = $file_name.'.'.$extension;
					}//end if upload
					
					//if update without new upload
					if($images_array[$n][0] && !$images_array[$n][1]){
						$image_name = $images_array[$n][0];
					}
									
					$value_string .= $image_name.'-;-'.$images_array[$n][3].'-;-'.$images_array[$n][4].'[:-)# ]';
				}
			}//end if not empty
		}
		return $value_string;
	}
	
	function modify_image($prod_img, $old_width, $old_height, $new_width, $new_height, $crop, $aspect_ratio, $extension){
		 
		$crop_width = $new_width;
		$crop_height = $new_height;
		
		$widthratio = $old_width/$new_width; 			
		$heightratio = $old_height/$new_height;
		
		if($crop){
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
		}else{
			if($widthratio <= $heightratio){ 
				//vertical image or squere
				$new_width = $old_width/$heightratio;
			}else{ 
				//horizontal image
				$new_height = $old_height/$widthratio;
			}
		}
		$new_width = round($new_width); 
		$new_height = round($new_height); 				
		
		$imgnew = imagecreatetruecolor($new_width,$new_height);
		if($extension=='jpg'){
			$srcimg=imagecreatefromjpeg($prod_img)or die('Problem In opening Source Image');
		}elseif($extension=='gif'){
			$srcimg=imagecreatefromgif($prod_img)or die('Problem In opening Source Image');
		}
		if(function_exists('imagecopyresampled')){
			imagecopyresampled($imgnew,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg))or die('Problem In resizing');
		}else{
			imagecopyresized($imgnew,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg))or die('Problem In resizing');
		}
		if($extension=='jpg'){
			imagejpeg($imgnew,$prod_img,90)or die('Problem In saving');
		}elseif($extension=='gif'){
			imagegif($imgnew,$prod_img)or die('Problem In saving');
		}
		
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
	}
	
	function make_filename_unique($file_name, $extension, $image_dir){
		if($file_name){
			//rename file if already exist
			if(file_exists(dirname(__FILE__).'/../../../../'.$image_dir.'/'.$file_name.'.'.$extension)){
				$j = 2;
				while (file_exists(dirname(__FILE__).'/../../../../'.$image_dir.'/'.$file_name.'-'.$j.".".$extension)){
					$j = $j + 1;
				}
				$new_name = $file_name . "-" . $j;
			}else{
				$new_name = $file_name;
			}
			
			//replace spaces by underscores
			$new_name = str_replace(' ', '_', $new_name);
		}else{
			$new_name = '';
		}
		return $new_name;
	}
	
	function delete_image($temp_old_src, $image_dir){
		$temp_old_src_array = explode('.',$temp_old_src);
		$temp_old_src_name = $temp_old_src_array[0];
		$temp_old_src_extension = $temp_old_src_array[1];
		if(file_exists(dirname(__FILE__).'/../../../../'.$image_dir.'/'.$temp_old_src_name.'.'.$temp_old_src_extension)){
			unlink(dirname(__FILE__).'/../../../../'.$image_dir.'/'.$temp_old_src_name.'.'.$temp_old_src_extension);
		}
		if(file_exists(dirname(__FILE__).'/../../../../'.$image_dir.'/'.$temp_old_src_name.'_thumb.'.$temp_old_src_extension)){
			unlink(dirname(__FILE__).'/../../../../'.$image_dir.'/'.$temp_old_src_name.'_thumb.'.$temp_old_src_extension);
		}
	}
	
	//function to delete images when item is deleted, from PI version 1.4.7
	function item_delete($item_id, $type_id, $field){
	
		//get image dir
		$image_dir = $this->get_field_param($field->params, 'image_dir');
		
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
		$images_array = explode('[:-)# ]',$field_value);
		
		for($n = 0; $n < count($images_array); $n++){
			$image_stuff = $images_array[$n];
			$image_stuff_array = explode('-;-',$image_stuff);
			$image = $image_stuff_array[0];
			//echo $image.'<br />';
			if($image){
				$this->delete_image($image, $image_dir);
			}
		}
	}
	
	//function to delete images when field or itemtype is deleted, from PI version 1.4.7
	function field_delete($field){
	
		//get image dir
		$image_dir = $this->get_field_param($field->params, 'image_dir');
	
		$field_id = $field->id;
		
		//get values of images
		$this->db->setQuery("SELECT value "
		."FROM #__pi_custom_fields_values "
		."WHERE field_id='$field_id' "
		);
		$field_rows = $this->db->loadObjectList();
		foreach($field_rows as $field_row){
			$field_value = $field_row->value;
			$images_array = explode('[:-)# ]',$field_value);
			for($n = 0; $n < count($images_array); $n++){
				$image_stuff = $images_array[$n];
				$image_stuff_array = explode('-;-',$image_stuff);
				$image = $image_stuff_array[0];
				if($image){
					$this->delete_image($image, $image_dir);
				}
			}
			
			
		}
	}
}

?>