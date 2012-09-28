<?php
/**
* @version		2.1.6
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('JPATH_BASE') or die;
/**
*/
require_once(dirname(__FILE__).DS.'extension.php');


abstract class PagesAndItemsExtensionFieldtype extends PagesAndItemsExtension
{
	public $version = '';//

	/**
	 * Constructor
	 *
	 * @param object $subject The object to observe
	 * @param array  $config  An optional associative array of configuration settings.
	 */
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);
		jimport('joomla.filesystem.folder');
		$folder = realpath(dirname(__FILE__).'..'.DS.'..'.DS.'..'.DS);
		$files = JFolder::files($folder,'.xml',false,true);
		if(count($files))
		{
			foreach($files as $file)
			{
				$xml = simplexml_load_file($file);
				if ($xml)
				{
					if ( is_object($xml) && is_object($xml->install))
					{
						//ok we have the install file
						//we will get the version
						$element = (string)$xml->version;
						$this->version = $element ? $element : '';
					}
				}
			}
		}
	}

	function field_params_as_registry($field_params)
	{
		if ($field_params instanceof JRegistry)
		{
			return $field_params;
		}
		if(!is_array($field_params))
		{
			$temp = explode( '[;-)# ]', $field_params);
			$field_params = array();
			for($n = 0; $n < count($temp); $n++)
			{
				$temp2 = explode('-=-',$temp[$n]);
				$var = trim($temp2[0]);
				$value = '';
				if(count($temp2)==2)
				{
					$value = $temp2[1];
				}
				if($var && $var != '')
				{
					$field_params[$var] = trim($value);
				}
			}
		}
		//check for empty key
		foreach($field_params as $key => $value)
		{
			if(!$key)
			{
				unset($field_params[$key]);
			}
		}
		
		$params = new JRegistry;
		$params->loadArray($field_params);
		
		return $params;
	}

	function make_value_into_parameter($parameter, $value)
	{
		return $parameter.'-=-'.$value.'[;-)# ]';
	}

	function onDisplay_config_form(&$html,$plugin, $type_id, $name, $field_params, $field_id)
	{
		if(method_exists($this, 'display_config_form') && $this->_name == $plugin)
		{
			$html = $this->display_config_form($plugin, $type_id, $name, $field_params, $field_id);
			return true;
		}
		return false;
	}

	function display_field_description($field_params)
	{
		//changed on request of Micha for German chapital letters.
		$field_name = JText::_('COM_PAGESANDITEMS_DESCRIPTION');
		$field_content = '<input type="text" class="width200" value="'.(isset($field_params['description']) ? $field_params['description'] : '').'" name="field_params[description]" />';
		$html = $this->display_field($field_name, $field_content);
		/*
		????
		$field_name = JText::_('COM_PAGESANDITEMS_DESCRIPTION').' '.JText::_('JSHOW');
		$value = isset($field_params['show_description']) ? $field_params['show_description'] : 1;
		$field_content = '';
		$field_content .= '<input type="radio" ';
		if($value == -1)
		{
			$field_content .= ' checked="checked"';
		}
		$field_content .= 'id="show_description_1" name="field_params[show_description]" value="-1" />';
		$field_content .= '<label for="show_description_1">'.JText::_('JNO').'</label>';
		
		$field_content .= '<input type="radio" ';
		if($value == 1)
		{
			$field_content .= ' checked="checked"';
		}
		$field_content .= 'id="show_description_2" name="field_params[show_description]" value="1" />';
		$field_content .= '<label for="show_description_2">'.JText::_('JYES').'</label>';
		
		$field_content .= '<input type="radio" ';
		if($value == 2)
		{
			$field_content .= ' checked="checked" ';
		}
		$field_content .= 'id="show_description_3" name="field_params[show_description]" value="2" />';
		$field_content .= '<label title="'.JText::_('COM_PAGESANDITEMS_SHOW_FIELD_NAME_DEFAULT_TIP').'" for="show_description_3">'.JText::_('JDEFAULT').'</label>';
		$html .= $this->display_field($field_name, $field_content);
		*/
		return $html;
	}

	function makeShowFieldName($field_id,$field_params,$description = false)
	{
		$field_name = JText::_('COM_PAGESANDITEMS_SHOW_FIELD_NAME');
		$field_content = '';
		if($this->check_if_field_param_is_present($field_params, 'show_field_name'))
		{
			$value = 2;
		}
		elseif($this->check_if_field_param_is_present($field_params, 'showFieldName'))
		{
			$value = $field_params['showFieldName'];//$this->get_field_param($field_params, 'showFieldName');
		}
		else
		{
			$value = 2;
		}
		$field_content = '';
		$field_content .= '<input type="radio" ';
		if($value == -1)
		{
			$field_content .= ' checked="checked"';
		}
		//$field_content .= 'onclick="newDefault(\'showFieldName_'.$field_id.'\',1);" ';
		$field_content .= 'id="showFieldName_'.$field_id.'1" name="field_params[showFieldName]" value="-1" />';
		$field_content .= '<label for="showFieldName_'.$field_id.'1">'.JText::_('JNO').'</label>';
		
		$field_content .= '<input type="radio" ';
		if($value == 1)
		{
			$field_content .= ' checked="checked"';
		}
		//$field_content .= 'onclick="newDefault(\'showFieldName_'.$field_id.'\',2);" ';
		$field_content .= 'id="showFieldName_'.$field_id.'2" name="field_params[showFieldName]" value="1" />';
		$field_content .= '<label for="showFieldName_'.$field_id.'2">'.JText::_('JYES').'</label>';
		
		$field_content .= '<input type="radio" ';
		if($value == 2)
		{
			$field_content .= ' checked="checked" ';
		}
		//$field_content .= 'onclick="newDefault(\'showFieldName_'.$field_id.'\',3);" ';
		$field_content .= 'id="showFieldName_'.$field_id.'3" name="field_params[showFieldName]" value="2" />';
		$field_content .= '<label title="'.JText::_('COM_PAGESANDITEMS_SHOW_FIELD_NAME_DEFAULT_TIP').'" for="showFieldName_'.$field_id.'3">'.JText::_('JDEFAULT').'</label>';
		
		$html = $this->display_field($field_name, $field_content);
		//an extra div?
		$html .= $this->display_field('', '');
		
		/*
		$html .= '<script language="JavaScript"  type="text/javascript">';
		$html .= "<!--\n";
		$html .= "function newDefault(id,nr)\n";
		$html .= "{\n";
		$html .= "	default_value = document.getElementById(id+nr).checked ? 1 : 0;\n";
		//$html .= "	sizes_value = document.getElementById(id+nr).value;\n";
		$html .= "	var counter = 3;\n";
		$html .= "	for (k = 1; k <= counter; k++)\n";
		$html .= "	{\n";
		$html .= "		document.getElementById(id+k).checked = 0;\n";
		//$html .= "		document.getElementById(id+k).value = 0;\n";
		$html .= "	}\n";
		$html .= "	document.getElementById(id+nr).checked = default_value;\n";
		//$html .= "	document.getElementById(id+nr).value = default_value;\n";
		$html .= "}\n";
		$html .= "-->\n";
		$html .= "</script>\n";
		*/
		if($description)
		{
			$field_name = JText::_('COM_PAGESANDITEMS_DESCRIPTION');
			$field_content = '<input type="text" class="width200" value="'.(isset($field_params['description']) ? $field_params['description'] : '').'" name="field_params[description]" />';
			$html .= $this->display_field($field_name, $field_content);
			$field_name = JText::_('COM_PAGESANDITEMS_DESCRIPTION').' '.JText::_('JSHOW');
			$value = isset($field_params['show_description']) ? $field_params['show_description'] : 1;
			$field_content = '';
			$field_content .= '<input type="radio" ';
			if($value == -1)
			{
				$field_content .= ' checked="checked"';
			}
			$field_content .= 'id="show_description_1" name="field_params[show_description]" value="-1" />';
			$field_content .= '<label for="show_description_1">'.JText::_('JNO').'</label>';
		
			$field_content .= '<input type="radio" ';
			if($value == 1)
			{
				$field_content .= ' checked="checked"';
			}
			$field_content .= 'id="show_description_2" name="field_params[show_description]" value="1" />';
			$field_content .= '<label for="show_description_2">'.JText::_('JYES').'</label>';
		
			$field_content .= '<input type="radio" ';
			if($value == 2)
			{
				$field_content .= ' checked="checked" ';
			}
			$field_content .= 'id="show_description_3" name="field_params[show_description]" value="2" />';
			$field_content .= '<label title="'.JText::_('COM_PAGESANDITEMS_SHOW_FIELD_NAME_DEFAULT_TIP').'" for="show_description_3">'.JText::_('JDEFAULT').'</label>';
			$html .= $this->display_field($field_name, $field_content);
			$html .= $this->display_field('', '');
		}
		return $html;
	}

	

	function onDisplay_item_edit(&$fieldHtml,$field, $field_params, $field_values, $field_value, $new_field, $field_id,$showSliderType = 0,$configSliderType = -1,$showIconField = false,$sliderCookie=false,$sliderOpen = false)
	{
		if(method_exists($this, 'display_item_edit') && $this->_name == $field->plugin)
		{
			$this->loadLanguage();
			
			//$top = '';
			$top = false;
			if($fieldHtml->text == '' || !$fieldHtml->text)
			{
				//$top = '_top';
				$top = true;
			}
			
			
			$fieldContent = $this->display_item_edit($field, $field_params, $field_values, $field_value, $new_field, $field_id);//,$top);
			$html = '';
			if($fieldContent)
			{
				
				
				$image = '';
				if($showIconField)
				{
				//add here icons?
			
					$imageDir = PagesAndItemsHelper::getDirIcons().'ui/';
					$image = $imageDir.'ui-'.$field->plugin.'.png';
					//if(file_exists(JPATH_ROOT.$image))
					//for subdomains we must have
					$jpathRoot = str_ireplace(JURI::root(true),'',str_replace(DS,'/',JPATH_ROOT));
					//if(file_exists(JPATH_ROOT.$image))
					if(file_exists($jpathRoot.$image))
					{
						//$image;
						/*
						$html .= '<div class="width-100">';
						$html .= '<img src="'.$image.'" />';
						$html .= '</div>';
						*/
					}
					else
					{
						//look in field params
						$image = $imageDir.'ui-blank.png';
						
						if($this->params && $uiImage = $this->params->get('uiImage'))
						{
							$folder = '';
							$this->onGetFolder($folder,$field->plugin);
							if(file_exists($folder.DS.$uiImage))
							{
								$folder = JURI::root(true).'/'.str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath($folder)));
								$image = str_replace(DS,'/',$folder.DS.$uiImage);
							}
						}
						/*
						$html .= '<div class="width-100">';
						$html .= '<img src="'.$image.'" />';
						$html .= '</div>';
						*/
					}
				}
				
				// $configSliderType == 2 && $showSliderType
				$html .= ($configSliderType == 2 && $showSliderType) ? '<div class="customfieldsslider">' : '';
				
				$addDiv = false;
				
				// $configSliderType != 2 && $configSliderType != 4
				
				if(!$showSliderType || ($configSliderType != 2 && $configSliderType != 3 && $configSliderType != 4) && $showIconField && $image )
				{
					$html .= '<div style="background-image: url('.$image.')" class="fieldEditIcon '.($top ? 'top' : '').'">';
					$addDiv = true;
					//<div style="background-image: url(/administrator/components/com_pagesanditems/media/images/icons/components/content/article/icon-16-article_edit.png);" class="thIcon16"><p>Beitrag bearbeiten</p></div>
				}
				/*
				elseif(!$showSliderField && !$showIconField && !$showSlider && $top)
				{
					//$html .= '<div class=" '.($top ? 'top' : '').'">';
					$html .= '<div class="fieldtype_top">';
				}
				*/
				// $configSliderType != 2 && $configSliderType != 4 && !$showIconField && $showSliderType
				elseif(($configSliderType != 2 || $configSliderType != 4) && !$showIconField && $showSliderType)
				{
					$html .= '<div class="fieldEdit '.($top ? 'top' : '').'">';
					$addDiv = true;
				}
				elseif($top && ($configSliderType != 2 || $configSliderType != 4) && !$showSliderType)
				{
					//here we add an extra div to hide top line
					$html .= '<div class="fieldtype_top">';
					$addDiv = true;
				}
				elseif($showSliderType && ($configSliderType == 2 || $configSliderType == 3 || $configSliderType == 4))
				{
					
				}
				else
				{
					$html .= '<div class="fieldEdit x">';
					$addDiv = true;
				}
				
				

				

				
				$validation = '';
				if($this->check_if_field_param_is_present($field_params, 'validation'))
				{
					if($field_params['validation'])
					{
						$validation = '<span class="star">&nbsp;*</span>';
					}
				}
				
				if($this->check_if_field_param_is_present($field_params, 'showFieldName'))
				{
					$value = $this->get_field_param($field_params, 'showFieldName');
				}
				else
				{
					$value = 2;
				}
				$fieldName ='';
				if($value == 2)
				{
					$value = $this->params->get('showFieldName',1);
					if($value == 1)
					{
						$fieldName = $field->name;
					}
				}
				elseif($value == 1)
				{
					$fieldName = $field->name;
				}
				
				
				$fieldDescription = '';
				
				if(isset($field_params['description'])){
					$fieldDescription = $field_params['description'] ? $field_params['description'] : '';
				}
				/*
				if($this->check_if_field_param_is_present($field_params, 'show_description'))
				{
					$value = $this->get_field_param($field_params, 'show_description');
				}
				else
				{
					$value = 2;
				}
				if($value == 2)
				{
					$value = $this->params->get('show_description',1);
				}
				if($value != 1)
				{
					$fieldDescription = '';
				}
				
				*/
				//$configSliderType == 2 && $configSliderType == 3
				if($configSliderType != 1 && $configSliderType)
				{
					if($configSliderType != 4)
					$html .= JHtml::_('sliders.start','fieldtype-sliders-'.$field->id, ($sliderCookie ? array('useCookie'=>1) : array('useCookie'=>0,'startOffset'=>($sliderOpen ? 0 : -1))) );
				
						$html .=  JHtml::_('sliders.panel',($image ? '<img src="'.$image.'" />' : '').($fieldName ? $fieldName.' <small> '.$fieldDescription.'</small>' : $fieldDescription), 'fieldtype-panel-'.$field->id);
						//$html .=  JHtml::_('sliders.panel',($image ? '<img src="'.$image.'" />' : '').($fieldName ? $fieldName.' <small> '.$fieldDescription.'</small>' : $field->name), 'fieldtype-panel-'.$field->id);
					$html .= $fieldContent;
				}
				else
				{
					if(!$fieldName && $fieldDescription)
					{
						$fieldName = $fieldDescription;
						$fieldDescription = '&nbsp;';
					}
					//$validation.
					$html .= $fieldName ? '<div class="width-100 pi_fieldname_wrapper"><div class="fieldName pi_width20">'.$fieldName.':</div><div class="fieldDescription pi_width70">'.($fieldDescription ? $fieldDescription : '&nbsp;').'</div></div>'.$fieldContent.'' : $fieldContent; //<br /><div class="">'.$fieldContent.'</div>'
				}

				
				
				//$html .= $fieldName.$fieldContent; //$this->display_item_edit($field, $field_params, $field_values, $field_value, $new_field, $field_id);//,$top);
				/*
				if($top && !$showSliderField )
				{
					$html .= '</div>';
				}
				*/
				if($configSliderType == 2 || $configSliderType == 3)
				{
					//$html .= '</fieldset>';
					$html .= JHtml::_('sliders.end');
				}
				if($addDiv)
				{
					$html .= '</div>';
				}
				$html .= ($configSliderType == 2 && $showSliderType) ? '</div>' : '';
				/*
				elseif($showIconField && $image)
				{
					$html .= '</div>';
				}
				elseif(!$showSliderField && !$showIconField )
				{
					$html .= '</div>';
				}
				*/
			}
			$fieldHtml->text = $fieldHtml->text.$html;
			
			
			
			return true;
		}
		return false;
	}









//can remove
/*
OLD BEGIN
******************************************************
*/
	function oldonDisplay_item_edit(&$fieldHtml,$field, $field_params, $field_values, $field_value, $new_field, $field_id,$showSlider = false,$showSliderField = false,$showIconField = false,$sliderCookie=false,$sliderOpen = false,$showSliderType = 0,$configSliderType = -1)
	{

		if(method_exists($this, 'display_item_edit') && $this->_name == $field->plugin)
		{

			$this->loadLanguage();
			
			
			//$top = '';
			$top = false;
			if($fieldHtml->text == '' || !$fieldHtml->text)
			{
				//$top = '_top';
				$top = true;
			}
			$fieldContent = $this->display_item_edit($field, $field_params, $field_values, $field_value, $new_field, $field_id);//,$top);
			
			
			$html = '';
			if($fieldContent)
			{
				$image = '';
				if($showIconField)
				{
				//add here icons?
			
					$imageDir = PagesAndItemsHelper::getDirIcons().'ui/';
					$image = $imageDir.'ui-'.$field->plugin.'.png';
					//for subdomains we must have
					$jpathRoot = str_ireplace(JURI::root(true),'',str_replace(DS,'/',JPATH_ROOT));
					//if(file_exists(JPATH_ROOT.$image))
					if(file_exists($jpathRoot.$image))
					{
						//$image;
						/*
						$html .= '<div class="width-100">';
						$html .= '<img src="'.$image.'" />';
						$html .= '</div>';
						*/
					}
					else
					{
						//look in field params
						$image = $imageDir.'ui-blank.png';
						
						if($this->params && $uiImage = $this->params->get('uiImage'))
						{
							$folder = '';
							$this->onGetFolder($folder,$field->plugin);
							if(file_exists($folder.DS.$uiImage))
							{
								$folder = JURI::root(true).'/'.str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath($folder)));
								$image = str_replace(DS,'/',$folder.DS.$uiImage);
							}
						}
						/*
						$html .= '<div class="width-100">';
						$html .= '<img src="'.$image.'" />';
						$html .= '</div>';
						*/
					}
				}
				
				// $configSliderType == 2 && $showSliderType
				$html .= ($showSliderField && $showSlider) ? '<div class="customfieldsslider">' : '';
				
				$addDiv = false;
				
				// $configSliderType != 2 && $configSliderType != 4
				if(!$showSliderField && $showIconField && $image )
				{
					$html .= '<div style="background-image: url('.$image.')" class="fieldEditIcon '.($top ? 'top' : '').'">';
					$addDiv = true;
					//<div style="background-image: url(/administrator/components/com_pagesanditems/media/images/icons/components/content/article/icon-16-article_edit.png);" class="thIcon16"><p>Beitrag bearbeiten</p></div>
				}
				/*
				elseif(!$showSliderField && !$showIconField && !$showSlider && $top)
				{
					//$html .= '<div class=" '.($top ? 'top' : '').'">';
					$html .= '<div class="fieldtype_top">';
				}
				*/
				// $configSliderType != 2 && $configSliderType != 4 && !$showIconField && $showSliderType
				elseif(!$showSliderField && !$showIconField && $showSlider)
				{
					$html .= '<div class="fieldEdit '.($top ? 'top' : '').'">';
					$addDiv = true;
				}
				elseif($top && !$showSliderField && !$showSlider)
				{
					//here we add an extra div to hide top line
					$html .= '<div class="fieldtype_top">';
					$addDiv = true;
				}
				else
				{
					$html .= '<div class="fieldEdit">';
					$addDiv = true;
				}
				
				

				

				
				$validation = '';
				if($this->check_if_field_param_is_present($field_params, 'validation'))
				{
					if($field_params['validation'])
					{
						$validation = '<span class="star">&nbsp;*</span>';
					}
				}
				
				if($this->check_if_field_param_is_present($field_params, 'showFieldName'))
				{
					$value = $this->get_field_param($field_params, 'showFieldName');
				}
				else
				{
					$value = 2;
				}
				$fieldName ='';
				if($value == 2)
				{
					$value = $this->params->get('showFieldName',1);
					if($value == 1)
					{
						$fieldName = $field->name;
					}
				}
				elseif($value == 1)
				{
					$fieldName = $field->name;
				}
				
				
				$fieldDescription = '';
				
				if(isset($field_params['description'])){
					$fieldDescription = $field_params['description'] ? $field_params['description'] : '';
				}
				/*
				if($this->check_if_field_param_is_present($field_params, 'show_description'))
				{
					$value = $this->get_field_param($field_params, 'show_description');
				}
				else
				{
					$value = 2;
				}
				if($value == 2)
				{
					$value = $this->params->get('show_description',1);
				}
				if($value != 1)
				{
					$fieldDescription = '';
				}
				
				*/
				
				if($showSliderField)
				{
					$html .= JHtml::_('sliders.start','fieldtype-sliders-'.$field->id, ($sliderCookie ? array('useCookie'=>1) : array('useCookie'=>0,'startOffset'=>($sliderOpen ? 0 : -1))) );
						$html .=  JHtml::_('sliders.panel',($image ? '<img src="'.$image.'" />' : '').($fieldName ? $fieldName.' <small> '.$fieldDescription.'</small>' : $fieldDescription), 'fieldtype-panel-'.$field->id);
						//$html .=  JHtml::_('sliders.panel',($image ? '<img src="'.$image.'" />' : '').($fieldName ? $fieldName.' <small> '.$fieldDescription.'</small>' : $field->name), 'fieldtype-panel-'.$field->id);
					$html .= $fieldContent;
				}
				else
				{
					if(!$fieldName && $fieldDescription)
					{
						$fieldName = $fieldDescription;
						$fieldDescription = '&nbsp;';
					}
					//$validation.
					$html .= $fieldName ? '<div class="width-100 pi_fieldname_wrapper"><div class="fieldName pi_width20">'.$fieldName.':</div><div class="fieldDescription pi_width70">'.($fieldDescription ? $fieldDescription : '&nbsp;').'</div></div>'.$fieldContent.'' : $fieldContent; //<br /><div class="">'.$fieldContent.'</div>'
				}

				
				
				//$html .= $fieldName.$fieldContent; //$this->display_item_edit($field, $field_params, $field_values, $field_value, $new_field, $field_id);//,$top);
				/*
				if($top && !$showSliderField )
				{
					$html .= '</div>';
				}
				*/
				if($showSliderField)
				{
					//$html .= '</fieldset>';
					$html .= JHtml::_('sliders.end');
				}
				if($addDiv)
				{
					$html .= '</div>';
				}
				$html .= ($showSliderField && $showSlider) ? '</div>' : '';
				/*
				elseif($showIconField && $image)
				{
					$html .= '</div>';
				}
				elseif(!$showSliderField && !$showIconField )
				{
					$html .= '</div>';
				}
				*/
			}
			$fieldHtml->text = $fieldHtml->text.$html;
			
			
			
			return true;
		}
		return false;
	}
/*
OLD END
******************************************************
*/








	function onRender_field_output(&$html,$field, $intro_or_full, $readmore_type=0, $editor_id=0,$language = null)
	{
		if(method_exists($this, 'render_field_output') && $this->_name == $field->plugin)
		{
			$html = $this->render_field_output($field, $intro_or_full, $readmore_type, $editor_id,$language);
			return true;
		}
		return false;
	}

	function onParams_save(&$params_string,$plugin)
	{
		if(method_exists($this, 'params_save') && $this->_name == $plugin)
		{
			$params_string = $this->params_save($params_string);
			return true;
		}
		return false;
	}

	function onDefault_value(&$default_value,$plugin)
	{
		if(method_exists($this, 'default_value') && $this->_name == $plugin)
		{
			$default_value = $this->default_value($default_value);
			return true;
		}
		return false;
	}


	function onField_save($field, $insert_or_update)
	{
		if(method_exists($this, 'field_save') && $this->_name == $field->plugin)
		{
			if($insert_or_update == 'insert')
			{
				$value = $this->field_save($field, 'insert');
			}
			else
			{
				$value = $this->field_save($field, 'update');
			}
			if(is_array($value) && isset($value['error']))
			{
				if($value['error'] != '')
				{
					JError::raiseWarning( 100, JText::_('COM_PAGESANDITEMS_FIELD').' ('.$field->name.'): '.$value['error']);
				}
				else
				{
					//only test
					JError::raiseWarning( 100, JText::_('COM_PAGESANDITEMS_FIELD').' ('.$field->name.'): undefined');
				}
				return false;
			}
		}
		else if($this->_name == $field->plugin)
		{
			$field_values_array = 'field_values_'.$field->field_id;
			$field_values = JRequest::getVar($field_values_array, false);
			if(count($field_values)==1)
			{
				$temp = trim(addslashes($field_values[0]));
				$value = str_replace('"','&quot;',$temp);
			}
			else
			{
				$value = '';
				for($n = 0; $n < count($field_values); $n++)
				{
					$row = each($field_values);
					$temp = addslashes("$row[key]-=-$row[value][;-)# ]");
					$value .= str_replace('"','&quot;',$temp);
				}
			}
		}
		else
		{
			return false;
		}

		if($insert_or_update == 'insert')
		{
			$this->db->setQuery( "INSERT INTO #__pi_custom_fields_values SET field_id='$field->id', item_id='$field->item_id', value='$value' ");
		}
		else
		{
			/*
			$this->db->setQuery( "SELECT f.*, v.*, v.id AS value_id, f.id AS field_id "
			. "\n FROM #__pi_custom_fields_values AS v "
			. "\n LEFT JOIN #__pi_custom_fields AS f "
			. "\n ON f.id=v.field_id "
			. "\n WHERE f.type_id='$field->type_id' "
			. "\n AND v.item_id='$field->item_id' "
			. "\n AND f.id='$field->id' "
			. "\n ORDER BY f.ordering ASC "
			);
			$field = $this->db->loadObject();
			*/
			$this->db->setQuery( "UPDATE #__pi_custom_fields_values SET value='$value' WHERE id='$field->value_id' ");
		}
		$this->db->query();
		return true;
	}

	/*
	i have not convert this
	extended_editor_tag
	*/




	function onExtended_save_custom_itemtype_field_save($field, $insert_or_update,$item_id,$type_id,$fields)
	{
		if(method_exists($this, 'extended_save_custom_itemtype_field_save') && $this->_name == $field->plugin)
		{
			//$value =
			$this->extended_save_custom_itemtype_field_save($field, $insert_or_update, $item_id, $type_id, $fields);
			return true;
		}
		return false;
	}


	function onExtended_save_custom_itemtype_delete_item($field, $item_id,$type_id)
	{
		if(method_exists($this, 'extended_save_custom_itemtype_delete_item') && $this->_name == $field->plugin)
		{
			//$value =
			$this->extended_save_custom_itemtype_delete_item($item_id,$type_id);
			return true;
		}
		return false;
	}

	function onExtended_config_custom_itemtype_field_save($type_id,$field_id,$name,$plugin,$insert,$default_value)
	{
		if(method_exists($this, 'extended_config_custom_itemtype_field_save') && $this->_name == $plugin)
		{
			$this->extended_config_custom_itemtype_field_save($type_id,$field_id,$name,$plugin,$insert,$default_value);
			return true;
		}
		return false;
	}


	function onExtended_config_custom_itemtype_save($type_id,$name,$update,$plugin, $read_more, $template_intro, $template_full, $html_after, $html_before, $editor_id)
	{
		if(method_exists($this, 'extended_config_custom_itemtype_save') && $this->_name == $plugin)
		{
			//$value =
			$this->extended_config_custom_itemtype_save($type_id,$name,$update,$plugin, $read_more, $template_intro, $template_full, $html_after, $html_before, $editor_id);
			return true;
		}
		return false;
	}


	function onExtended_config_custom_itemtype_delete($field,$type_id,$rows)
	{
		if(method_exists($this, 'extended_config_custom_itemtype_delete') && $this->_name == $field->plugin)
		{
			//$value =
			$this->extended_config_custom_itemtype_delete($type_id,$rows);
			return true;
		}
		return false;
	}

	function onExtended_config_custom_itemtype_field_delete($field,$type_id,$fields_to_delete)
	{
		if(method_exists($this, 'extended_config_custom_itemtype_field_delete') && $this->_name == $field->plugin)
		{
			//$value =
			$this->extended_config_custom_itemtype_field_delete($type_id,$fields_to_delete);
			return true;
		}
		return false;
	}

	function onItem_delete( $field,$item_id, $type_id)
	{
		if(method_exists($this, 'item_delete') && $this->_name == $field->plugin)
		{
			//$value =
			$this->item_delete($item_id, $type_id, $field);
			return true;
		}
		return false;
	}

	function onField_delete($field)
	{
		if(method_exists($this, 'field_delete') && $this->_name == $field->plugin)
		{
			//$value =
			$this->field_delete($field);
			return true;
		}
		return false;
	}


/*
here are the helper functions for the fields?

*/
	function check_if_field_param_is_present($field_params, $field_param)
	{
		$param_is_present = false;
		for($n = 0; $n < count($field_params); $n++){
			if(is_array($field_params)){
				$row = each($field_params);
				if($row['key']==$field_param){
					$param_is_present = true;
					break;
				}
			}
		}
		return $param_is_present;
	}

	function check_if_plugin_lang_var_is_present($pi_lang_plugin, $var)
	{
		$var_is_present = false;
		for($n = 0; $n < count($pi_lang_plugin); $n++)
		{
			$row = each($pi_lang_plugin);
			if($row['key']==$var){
				$var_is_present = true;
				break;
			}
		}
		return $var_is_present;
	}

	function display_field($field_name, $field_content, $width_left='20', $width_right='70')
	{
		$html = '<div class="pi_form_wrapper">';
		$html .= '<div class="pi_width'.$width_left.'">';
		$has_only_star = strpos($field_name, 'img src=');
		
		if(($field_name!='' && $field_name!='&nbsp;') && $has_only_star!=1){
			$html .= $field_name.':';
		}elseif($has_only_star==1){
			$html .= $field_name;
		}else{
			$html .= '&nbsp;';
		}
		$html .= '</div>';
		$html .= '<div class="pi_width'.$width_right.'">';
		$html .= $field_content;
		$html .= '</div>';
		$html .= '</div>';
		return $html;
	}

	function display_field_default_value($field_params)
	{
		$field_content = '<input type="text" class="width200" value="'.$field_params['default_value'].'" name="field_params[default_value]" />';
		return $this->display_field(JText::_('COM_PAGESANDITEMS_DEFAULT_VALUE'), $field_content);
	}

	function display_field_validation($field_params)
	{
		$field_name = JText::_('COM_PAGESANDITEMS_VALIDATION');
		$field_content = '<input type="checkbox" class="checkbox"';
		//if($field_params['validation']){
		if($this->check_if_field_param_is_present($field_params, 'validation')){
			if($field_params['validation']){
				$field_content .= ' checked="checked"';
			}
		}
		$field_content .= 'name="field_params[validation]" value="not_empty" /> '.JText::_('COM_PAGESANDITEMS_NOT_EMPTY');
		return $this->display_field($field_name, $field_content);
	}



	function display_field_validation_message($field_params)
	{
		$field_content = '<input type="text" class="width200" value="'.$field_params['alert_message'].'" name="field_params[alert_message]" />';
		return $this->display_field(JText::_('COM_PAGESANDITEMS_VALIDATION_ALERT_MESSAGE'), $field_content);
	}

	function get_field_param($values_string, $property)
	{
		return $this->get_field_value($values_string, $property);
	}

	function get_field_value($values_string, $property)
	{
		if(is_array($values_string))
		{
			if(isset($values_string[$property]))
			{
				$html = $values_string[$property];
			}
		}
		else
		{
			$values_array = explode('[;-)# ]', $values_string);
			$property = substr($property,1);
			$html = '';
			foreach($values_array as $value_set){
				if(strpos($value_set, $property)){
					$temp = explode('-=-', $value_set);
					$html = $temp[1];
					break;
				}
			}
		}
		return $html;
	}



	function reorder_fields($type_id)
	{
		$this->db->setQuery("SELECT id FROM #__pi_custom_fields WHERE type_id='$type_id' ORDER BY ordering ASC" );
		$rows = $this->db->loadObjectList();
		$counter = 1;
		foreach($rows as $row){
			$id = $row->id;
			$this->db->setQuery( "UPDATE #__pi_custom_fields SET ordering='$counter' WHERE id='$id'");
			$this->db->query();
			$counter = $counter + 1;
		}
		return $counter;
	}
}
