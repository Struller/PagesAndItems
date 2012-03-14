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
//IMAGE
class PagesAndItemsExtensionFieldtypeImage extends PagesAndItemsExtensionFieldtype
{
	
	function display_config_form($plugin, $type_id, $name, $field_params, $field_id){
		if(!$field_id)
		{
			//new field, set defaults here
			$field_params['showFieldName'] = $this->params->get('showFieldName'); //0
			//$field_params['show_field_name'] = $this->params->get('show_field_name'); //0
			$field_params['resize'] = $this->params->get('resize'); //0
			$field_params['max_width'] = $this->params->get('max_width'); //''
			$field_params['max_height'] = $this->params->get('max_height'); //''
			$field_params['delete_old_image'] = $this->params->get('delete_old_image'); //0
			$field_params['show_src'] = $this->params->get('show_src'); //0
			$field_params['class_name'] = $this->params->get('class_name'); //''
			$field_params['only_source'] = $this->params->get('only_source'); //0
			$field_params['image_dir'] = $this->params->get('image_dir'); //'images/stories/'
			$field_params['auto_alt'] = $this->params->get('auto_alt'); //1
		}

		$html = '';
		//New show field name
		$html .= $this->makeShowFieldName($field_id,$field_params);
		//description
		$html .= $this->display_field_description($field_params);

		/*
		//show field name
		$field_name = JText::_('COM_PAGESANDITEMS_SHOW_FIELD_NAME');
		$field_content = '<input type="checkbox" ';
		if($this->check_if_field_param_is_present($field_params, 'show_field_name')){
			$field_content .= ' checked="checked"';
		}
		$field_content .= 'name="field_params[show_field_name]" value="1" />';
		$html .= $this->display_field($field_name, $field_content);
		*/
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
		//resize
		$field_name = JText::_('COM_PAGESANDITEMS_RESIZE');
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
		//auto add image source to alt field
		$field_name = JText::_('COM_PAGESANDITEMS_AUTO_ADD_SRC_TO_ALT');
		$field_content = '<input type="checkbox" class="checkbox" ';
		if($this->check_if_field_param_is_present($field_params, 'auto_alt')){
			if($field_params['auto_alt']){
				$field_content .= ' checked="checked"';
			}
		}
		$field_content .= 'name="field_params[auto_alt]" value="1" /> '.JText::_('COM_PAGESANDITEMS_AUTO_ADD_SRC_TO_ALT2');
		$html .= $this->display_field($field_name, $field_content);
		//image dit
		$field_name = JText::_('COM_PAGESANDITEMS_IMAGE_DIR');
		$field_content = '<input type="text" class="width200" value="'.$field_params['image_dir'].'" name="field_params[image_dir]" /> '.JText::_('COM_PAGESANDITEMS_IMAGE_DIR_EXAMPLE');
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
			if($this->check_if_field_param_is_present($field_values, 'src')){
				$src = $field_values['src'];
			}else{
				$src = 0;
			}
			if($this->check_if_field_param_is_present($field_values, 'alt')){
				$alt = $field_values['alt'];
			}else{
				$alt = '';
			}
		}

		$html = '<div class="field_type_image fieldtype">';
		
		/*
		if($this->check_if_field_param_is_present($field_params, 'show_field_name')){
			$html .= '<div>'.$field->name.':</div>';
		}
		*/
		/*
		if($field_params['description']){
			$html .= '<div>'.$field_params['description'].'</div>';
		}
		*/
		$html .= '<div class="pi_form_wrapper">';
		$html .= '<div class="pi_width20">';
		//$html .= '&nbsp;';
		
		$html .= '<label class="hasTip" title="'.JText::_('COM_PAGESANDITEMS_IMAGE_TIP').'"><span class="editlinktip">'.JText::_('COM_PAGESANDITEMS_IMAGE').'</span></label>';
		//$html .= JText::_('COM_PAGESANDITEMS_IMAGE');
		//$html .= '</span>';
		
		if($this->check_if_field_param_is_present($field_params, 'validation')){
			if($field_params['validation']){
				$html .= '<span class="star">&nbsp;*</span>';
			}
		}
		
		$html .= '</div>';
		
		$html .= '<div class="pi_width70">';

		$html .= '<script language="javascript"  type="text/javascript">'."\n";
		//function to check extension
		$html .= 'function check_extension_'.$field_id.'(id)'."\n";
		$html .= '{'."\n";
		$html .= '	var element = document.id(id);'."\n";
		$html .= '	value = element.value.toLowerCase();'."\n";
		$html .= '	pos_jpg = value.indexOf(".jpg");'."\n";
		$html .= '	pos_jpeg = value.indexOf(".jpeg");'."\n";
		$html .= '	pos_gif = value.indexOf(".gif");'."\n";
		$html .= '	pos_png = value.indexOf(".png");'."\n";
		$html .= '	if(pos_jpg==-1 && pos_jpeg==-1 && pos_gif==-1 && pos_png==-1)'."\n";
		$html .= '	{'."\n";
		$html .= '		element.value = \'\';'."\n";
		$html .= '		alert(\'wrong file-type. allowed are: gif, jpg, png and jpeg\')'."\n";
		$html .= '	}'."\n";
		$html .= '}'."\n";
		$html .= '</script>'."\n";
		
		//some error if file to big so for test:
		//require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'helpers'.DS.'file.php');
		//$html .= PagesAndItemsHelperFile::checkUploadScript($field_id,array('gif', 'jpg', 'jpeg', 'png' ));
		
		$html .= '<input type="file" value="1" name="'.$field_id.'_image" id="'.$field_id.'_image" onchange="check_extension_'.$field_id.'(\''.$field_id.'_image\');" />';
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
			$html .= '<img src="'.$this->live_site.$field_params['image_dir'].$src.'" alt="'.$src.'" />';
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
			if($this->check_if_field_param_is_present($field_params, 'show_src')){
				$html .= '<div class="pi_form_wrapper">';
				$html .= '<div class="pi_width20">';
				$html .= JText::_('COM_PAGESANDITEMS_SRC');
				$html .= ':</div>';
				$html .= '<div class="pi_width70">';
				$html .= '<input type="text" class="width200" value="';
				$html .= $field_params['image_dir'];
				$html .= $field_values['src'].'" name="'.$field_id.'_src" id="'.$field_id.'_src" />';
				$html .= '</div>';
				$html .= '</div>';
			}else{
				$html .= '<input type="hidden" class="width200" value="';
				$html .= $field_params['image_dir'];
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
		$html .= '<input type="hidden" name="'.$field_id.'_max_width" value="'.$field_params['max_width'].'" />';
		$html .= '<input type="hidden" name="'.$field_id.'_max_height" value="'.$field_params['max_height'].'" />';
		$html .= '<input type="hidden" name="'.$field_id.'_delete_old_image" value="';
		if($this->check_if_field_param_is_present($field_params, 'delete_old_image')){
			$html .= '1';
		}
		$html .=  '" />';
		$html .= '<input type="hidden" name="'.$field_id.'_resize" value="';
		if($this->check_if_field_param_is_present($field_params, 'resize')){
			$html .= '1';
		}
		$html .=  '" />';
		$html .= '<input type="hidden" name="'.$field_id.'_auto_alt" value="';
		if($this->check_if_field_param_is_present($field_params, 'auto_alt')){
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
		if($this->get_field_value($field->value, 'src')!=''){
			if($this->get_field_param($field->params, 'only_source')){
				$html .= $this->get_field_value($field->value, 'src');
			}else{
				$html .= '<img src="';
				$html .= $this->get_field_param($field->params, 'image_dir');
				$html .= $this->get_field_value($field->value, 'src').'"';
				$html .= ' alt="'.addslashes($this->get_field_value($field->value, "alt")).'"';
				if($this->get_field_param($field->params, 'class_name')!=''){
					$html .= ' class="'.$this->get_field_param($field->params, 'class_name').'"';
				}
				$html .= ' />';
			}
		}
		return $html;
	}

	function field_save($field, $insert_or_update){

		$value_name = 'field_values_'.$field->id;

		$image = $value_name.'_image';
		$max_width = JRequest::getVar($value_name.'_max_width');
		$max_height = JRequest::getVar($value_name.'_max_height');
		$delete_old_image = JRequest::getVar($value_name.'_delete_old_image');
		$resize = JRequest::getVar($value_name.'_resize');
		$src = JRequest::getVar($value_name.'_src', false);
		$delete_image = JRequest::getVar($value_name.'_delete_image', false);
		$image_dir = JRequest::getVar($value_name.'_image_dir', false);
		$auto_alt = JRequest::getVar($value_name.'_auto_alt', false);
		
		$image_upload = false;
		$imagePathRoot = JPath::clean(JPATH_ROOT.DS.$image_dir);
		//load the file Helper
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'helpers'.DS.'file.php');
		if(!JFolder::exists($imagePathRoot))
		{
			//the $image_dir not exists
			//create it
			if(!JFolder::create($imagePathRoot))
			{
				//JFolder::create make an own error but we must make this:
				$return = array('error'=>'');
				return $return;
			}
			
		}
		//check if there is an image to upload
		$userfile_name = strtolower($_FILES[$image]['name']);
		if($userfile_name && !$delete_image){
			$image_upload = true;
		}

		//if image edit, delete old image
		if(($userfile_name && $src && $delete_old_image) || $delete_image){
			//get the bare src
			$source_bits = explode('/',$src);
			$source_bits = array_reverse($source_bits);
			$src_delete = $source_bits[0];
			
			//if(file_exists(JPATH_ROOT.DS.$image_dir.$src_delete)){
			//	unlink(JPATH_ROOT.DS.$image_dir.$src_delete);
			if(file_exists($imagePathRoot.$src_delete)){
				unlink($imagePathRoot.$src_delete);
			}
		}
		$new_name = '';
		//upload image
		if($image_upload==true)
		{

			$userfile_name = strtolower($_FILES[$image]['name']);
			$userfile_tmp = $_FILES[$image]['tmp_name'];
			$userfile_size = $_FILES[$image]['size'];
			$userfile_type = $_FILES[$image]['type'];
			if (isset($_FILES[$image]['name'])){

				// get extension
				$extension = explode(".", $userfile_name);
				$last = count($extension) - 1;
				$extension = "$extension[$last]";

				//check extension
				$allowed_ext = "jpg jpeg gif png";
				$allowed_extensions = explode(" ", $allowed_ext);
				if (!in_array($extension, $allowed_extensions)){
					//JError::raiseWarning( 100, 'wrong file-type. allowed are: '.$allowed_ext);
					return array('error'=>'wrong file-type. allowed are: '.$allowed_ext);
					//die('wrong file-type. allowed are: '.$allowed_ext);
				}

				//rewrite jpeg to jpg
				if($extension=='jpeg'){
					$extension = 'jpg';
				}

				// get the old name of the file
				$old_name = str_replace('.'.$extension,'',$userfile_name);
				
				
				//$imagePathRoot
				//rename file if already exist
				//if(file_exists(JPATH_ROOT.DS.$image_dir.$old_name.'.'.$extension)){
				if(file_exists($imagePathRoot.$old_name.'.'.$extension)){
					$j = 2;
					//while (file_exists(JPATH_ROOT.DS.$image_dir.$old_name.'-'.$j.".".$extension)){
					while (file_exists($imagePathRoot.$old_name.'-'.$j.".".$extension)){
						$j = $j + 1;
					}
					$new_name = $old_name . "-" . $j;
				}else{
					$new_name = $old_name;
				}

				//replace spaces by underscores
				$new_name = str_replace(' ', '_', $new_name);

				//upload image
				//$imagePathRoot
				//$prod_img = JPATH_ROOT.DS.$image_dir.$new_name.'.'.$extension;
				$prod_img = $imagePathRoot.$new_name.'.'.$extension;
				if(!move_uploaded_file($userfile_tmp, $prod_img)){
					//die('Problem uploading image. File is too big. Try making the image smaller with an image-editor (like Photoshop or Gimp) and try again.');
					$error_message = PagesAndItemsHelperFile::file_upload_error_message($_FILES[$image]['error']);
					return array('error'=>'Problem uploading image. '.$error_message);
					// File is too big. Try making the image smaller with an image-editor (like Photoshop or Gimp) and try again.
				}

				//get sizes and ratio
				$sizes = getimagesize($prod_img);
				$aspect_ratio = $sizes[1]/$sizes[0];



				//resize uploaded image
				if (($sizes[0] > $max_width || $sizes[1] > $max_height) && $resize){

					
					$widthratio = 0;
					if($max_width){					
						$widthratio = $sizes[0]/$max_width;
					}
					$heightratio = 0;
					if($max_height){
						$heightratio = $sizes[1]/$max_height;
					}

					$imgnewwidth = $max_width;
					$imgnewheight = $max_height;

					if($widthratio <= $heightratio){
						$imgnewwidth = $sizes[0]/$heightratio;
						$newwidth = round($imgnewwidth);
						$newheight = round($imgnewheight);

					}else{
						$imgnewheight = $sizes[1]/$widthratio;
						$newwidth = round($imgnewwidth);
						$newheight = round($imgnewheight);
					}

					//ini_set('memory_limit', '120M');
					$imgnew = imagecreatetruecolor($newwidth,$newheight);
					if($extension=='jpg'){
						$srcimg=imagecreatefromjpeg($prod_img);
					}elseif($extension=='gif'){
						$srcimg=imagecreatefromgif($prod_img);
					}elseif($extension=='png'){
						$srcimg=imagecreatefrompng($prod_img);
					}
					
					//restore gif's and png's transparency after resize
					if($extension=='gif' || $extension=='png'){
						$trnprt_indx = imagecolortransparent($srcimg);
						
						// If we have a specific transparent color
						if ($trnprt_indx >= 0) {
						
							// Get the original image's transparent color's RGB values
							$trnprt_color = imagecolorsforindex($srcimg, $trnprt_indx);
							
							// Allocate the same color in the new image resource
							$trnprt_indx = imagecolorallocate($imgnew, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
							
							// Completely fill the background of the new image with allocated color.
							imagefill($imgnew, 0, 0, $trnprt_indx);
							
							// Set the background color for new image to transparent
							imagecolortransparent($imgnew, $trnprt_indx);
						
						
						
						// Always make a transparent background color for PNGs that don't have one allocated already
						}elseif($extension=='png'){
						
							// Turn off transparency blending (temporarily)
							imagealphablending($imgnew, false);
							
							// Create a new transparent color for image
							$color = imagecolorallocatealpha($imgnew, 0, 0, 0, 127);
							
							// Completely fill the background of the new image with allocated color.
							imagefill($imgnew, 0, 0, $color);
							
							// Restore transparency blending
							imagesavealpha($imgnew, true);
						}
					}					
					
					if(function_exists('imagecopyresampled')){
						imagecopyresampled($imgnew,$srcimg,0,0,0,0,$newwidth,$newheight,ImageSX($srcimg),ImageSY($srcimg));
					}else{
						imagecopyresized($imgnew,$srcimg,0,0,0,0,$newwidth,$newheight,ImageSX($srcimg),ImageSY($srcimg));
					}					
					if($extension=='jpg'){
						imagejpeg($imgnew,$prod_img,90);
					}elseif($extension=='gif'){
						imagegif($imgnew,$prod_img);
					}elseif($extension=='png'){
						imagepng($imgnew,$prod_img);
					}
				}
			}
			$src = $new_name.'.'.$extension;
		}else{
			//nothing has been uploaded

			$source_bits = explode('/',$src);
			$source_bits = array_reverse($source_bits);
			$src = $source_bits[0];

		}

		$alt = addslashes(JRequest::getVar($value_name.'_alt'));
		if($auto_alt && $alt=='')
		{
			$alt = $new_name;
		}
		$value = '';
		if($src)
		{
			$value = 'src-=-'.$src.'[;-)# ]alt-=-'.$alt.'[;-)# ]';
		}
		if($delete_image){
			$value = '';
		}
		return $value;
	}

	function item_delete($item_id, $type_id, $field){

		$field_id = $field->id;

		//get value of image
		$this->db->setQuery("SELECT value "
		."FROM #__pi_custom_fields_values "
		."WHERE (field_id='$field_id' AND item_id='$item_id') "
		."LIMIT 1 "
		);
		$images_rows = $this->db->loadObjectList();
		foreach($images_rows as $image_row){
			$image_value = $image_row->value;
		}
		$value_array = explode('[;-)#',$image_value);
		$src_part = $value_array[0];
		$src_array = explode('=-',$src_part);
		$src = $src_array[1];

		//get image dir
		$image_dir = $this->get_field_param($field->params, 'image_dir');

		//delete image
		if(file_exists(JPATH_ROOT.DS.$image_dir.$src)){
			unlink(JPATH_ROOT.DS.$image_dir.$src);
		}
	}

	function field_delete($field){

		$field_id = $field->id;

		//get values of images
		$this->db->setQuery("SELECT value "
		."FROM #__pi_custom_fields_values "
		."WHERE field_id='$field_id' "
		);
		$images_rows = $this->db->loadObjectList();
		foreach($images_rows as $image_row){
			$image_value = $image_row->value;
			$value_array = explode('[;-)#',$image_value);
			$src_part = $value_array[0];
			$src_array = explode('=-',$src_part);
			$src = $src_array[1];

			//get image dir
			$image_dir = $this->get_field_param($field->params, 'image_dir');

			//delete image
			if(file_exists(JPATH_ROOT.DS.$image_dir.$src)){
				unlink(JPATH_ROOT.DS.$image_dir.$src);
			}
		}
	}
}

?>