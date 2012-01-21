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
require_once(dirname(__FILE__).'/../../../includes/extensions/itemtype.php');
//CUSTOM
class PagesAndItemsExtensionItemtypeCustom extends PagesAndItemsExtensionItemtype
{
	
	/*
	add an 
	*/
	function check_fields($fields,$type_id,$item_id)
	{
		$this->db->setQuery( "SELECT f.*, f.id AS field_id "
			. "\n FROM #__pi_custom_fields AS f "
			. "\n WHERE f.type_id='$type_id' "
			. "\n ORDER BY f.ordering ASC "
		);
		$temp_fields = $this->db->loadObjectList();
		if(count($temp_fields) > count($fields))
		{
			//dump(count($temp_fields) , count($fields));
			//we must add the field to custom_fields_values
			foreach($temp_fields as $key => $temp_field)
			{
				foreach($fields as $field)
				{
					if($temp_field->id == $field->field_id)
					{
						unset($temp_fields[$key]);
					}
				}
			}
			//dump($temp_fields);
			foreach($temp_fields as $temp_field)
			{
				$dispatcher = &JDispatcher::getInstance();
				$temp_field->item_id = $item_id;
				$dispatcher->trigger('onField_save', array ($temp_field, 'insert'));
			}
			
			$this->db->setQuery( "SELECT f.*, v.*, v.id AS value_id, f.id AS field_id, f.id AS id "
			. "\n FROM #__pi_custom_fields_values AS v "
			. "\n LEFT JOIN #__pi_custom_fields AS f "
			. "\n ON f.id=v.field_id "
			. "\n WHERE f.type_id='$type_id' "
			. "\n AND v.item_id='$item_id' "
			. "\n AND f.id=v.field_id "
			. "\n ORDER BY f.ordering ASC "
			);
			
			$fields = $this->db->loadObjectList();
		}
		return $fields;
	}
	
	
	//in old save_custom_itemtype
	function onItemtypeItemSave($item_type, $delete_item, $item_id, $new_or_edit)
	{
	
				
		if(strpos($item_type, 'ustom_') === false)
		{
			return false;
		}
		
		jimport( 'joomla.application.component.model' );
		$modelCustomItemtype =& JModel::getInstance('CustomItemtype', 'PagesAndItemsModel', array());
		//state
		$state = intval(JRequest::getVar('state'));

		//get other_item_id
		$other_item_id = JRequest::getVar('other_item_id', '');
		
		$show_title_item = intval(JRequest::getVar('show_title_item'));
		
		$item_on_frontpage = intval(JRequest::getVar('frontpage'));

		$delete_item = intval(JRequest::getVar('delete_item'));
		
		
	//	$results = $dispatcher->trigger('onSave_custom_itemtype', array($item_type, $delete_item, $item_id));
		
		$pos = strpos($item_type, 'ustom_');
		$type_id = substr($item_type, $pos+6, strlen($item_type));
		
		
		
		/*
		here we get the fields if new we return without #__pi_custom_fields_values
		else we get an merged #__pi_custom_fields and #__pi_custom_fields_values
		*/
		$new_cit = true;
		if($new_or_edit == 'new')
		{
			$this->db->setQuery( "SELECT f.*, f.id AS field_id "
			. "\n FROM #__pi_custom_fields AS f "
			. "\n WHERE f.type_id='$type_id' "
			. "\n ORDER BY f.ordering ASC "
			);
			$fields = $this->db->loadObjectList();
		}
		else
		{
			/*
			$this->db->setQuery( "SELECT f.*, v.*, v.id AS value_id, f.id AS field_id "
			. "\n FROM #__pi_custom_fields AS f "
			. "\n LEFT JOIN #__pi_custom_fields_values AS v "
			. "\n ON v.field_id = f.id "
			. "\n WHERE f.type_id='$type_id' "
			. "\n AND v.item_id='$item_id' "
			. "\n AND f.id=v.field_id "
			. "\n ORDER BY f.ordering ASC "
			);
			*/
			$this->db->setQuery( "SELECT f.*, v.*, v.id AS value_id, f.id AS field_id, f.id AS id "
			. "\n FROM #__pi_custom_fields_values AS v "
			. "\n LEFT JOIN #__pi_custom_fields AS f "
			. "\n ON f.id=v.field_id "
			. "\n WHERE f.type_id='$type_id' "
			. "\n AND v.item_id='$item_id' "
			. "\n AND f.id=v.field_id "
			. "\n ORDER BY f.ordering ASC "
			);
			
			$fields = $this->db->loadObjectList();
			if(empty($fields))
			{
				$this->db->setQuery( "SELECT f.*, f.id AS field_id "
				. "\n FROM #__pi_custom_fields AS f "
				. "\n WHERE f.type_id='$type_id' "
				. "\n ORDER BY f.ordering ASC "
				);
				$fields = $this->db->loadObjectList();
			}
			else
			{
				$new_cit = false;
			}
			
			
			/*
			if an field not in #__pi_custom_fields_values this field will not save
			
			*/
		}
		//dump($fields);
		/*
		old
		//get fields
		$this->db->setQuery( "SELECT id, plugin, params "
		. "\nFROM #__pi_custom_fields "
		. "\nWHERE type_id='$type_id' "
		);
		*/
		//get fields plugin as an array for importExtension
		$this->db->setQuery( "SELECT DISTINCT plugin "
		. "\nFROM #__pi_custom_fields "
		. "\nWHERE type_id='$type_id' "
		. "\nORDER BY ordering ASC"
		);
		$fieldPlugins = $this->db->loadResultArray();
		//dump($fieldPlugins);
		/*
		here we need the fieldtypes
		*/
		//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'extensions'.DS.'helper.php');
		//$extensions = ExtensionHelper::importExtension('fieldtype',null, $fieldPlugins,true,null,true);
		$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'fieldtypehelper.php');
		$extensions = ExtensionFieldtypeHelper::importExtension(null, $fieldPlugins,true,null,true);
		/*
		require_once($path.DS.'includes'.DS.'extensions'.DS.'htmlhelper.php');
		ExtensionHtmlHelper::importExtension('template', null,true,null,true);
		*/
		require_once($path.DS.'includes'.DS.'extensions'.DS.'managerhelper.php');
		ExtensionManagerHelper::importExtension(null,null,true,null,true);
		$dispatcher = &JDispatcher::getInstance();
		
		//ms: if we have an field not in the custom_fields_value (an error on save or manuall delete the field in the rtable) the field will not save so i have add this
		//$fields = $this->check_fields($fields,$type_id,$item_id);
		
		
		/*
			here we need nothing to return
			we must not use $fieldHtml = & new JObject();
			and $fieldHtml->text = '';
		*/
		if($delete_item==1)
		{
			//first call to the template manager
			//$dispatcher->trigger('onTemplateItemDelete', array ( 'manager',$item_id,$type_id));
			
			// the next line is called in pagesanditemshelper
			// $dispatcher->trigger('onManagerItemtypeItemDelete', array ( $item_type,$item_id,$type_id));
			
			//if fieldtype got a special function for DELET item do that
			foreach($fields as $field)
			{
				$dispatcher->trigger('onExtended_save_custom_itemtype_delete_item', array ($field, $item_id,$type_id));

				$dispatcher->trigger('onItem_delete', array ( $field,$item_id,$type_id));
			}
			//delete custom field values
			$this->db->setQuery("DELETE FROM #__pi_custom_fields_values WHERE item_id='$item_id'");
			$this->db->query();
		}
		else
		{ 
			//save custom itemtype 
			/*
			old
			//get fields 
			$this->db->setQuery( "SELECT * "
			."\nFROM #__pi_custom_fields "
			."\nWHERE type_id='$type_id' "
			);
			$fields = $this->db->loadObjectList();
			
			//get data-fields
			$this->db->setQuery( "SELECT id, field_id "
			. "\nFROM #__pi_custom_fields_values "
			. "\nWHERE item_id='$item_id' "
			);
			$data_fields = $this->db->loadObjectList();
			
			//make array of fields which have a data field and make fields-datafields array
			// have we not the $new_or_edit for check ?
			$new_cit = true;
			$fields_datafields_array = array();
			foreach($fields as $field)
			{	
				foreach($data_fields as $field_data)
				{
					if($field_data->field_id==$field->id)
					{
						$fields_datafields_array[$field_data->field_id] = $field_data->id;
						$new_cit = false;
						break;
					}
				}
			}
			*/
			foreach($fields as $field)
			{
				
				//$field_id = $field->id;
				$field->item_id = $item_id;
				//dump($field);	
				//if(!$fields_datafields_array[$field_id])
				if($new_cit)
				{
					//start insert
					$dispatcher->trigger('onField_save', array ($field, 'insert'));
					//end insert
				}
				else
				{
					//start update	
					$dispatcher->trigger('onField_save', array ($field, 'update'));
					//end update
				}
			}//end field loop
			
			//$modelCustomItemtype->update_content_table_from_custom_itemtype($item_id, $item_type,$new_cit);
			//TODO
			//first call Managers
			//$dispatcher->trigger('onTemplateItemSave', array ('manager',$item_id,$type_id,$item_type,$fields,$new_cit));
			// the next line is called in pagesanditemshelper
			//$dispatcher->trigger('onManagerItemtypeItemSave', array ($item_type,$item_id,$type_id,$new_cit,$fields));
			
			$this->update_content_table_from_custom_itemtype($item_id, $item_type,$new_cit);
			
			foreach($fields as $field)
			{
				//$field_id = $field->id;
				//if(!isset($fields_datafields_array[$field_id]))
				if($new_cit)
				{
					//start insert
					//if fieldtype got a special special function for saving do that
					$dispatcher->trigger('onExtended_save_custom_itemtype_field_save', array ($field, 'insert',$item_id,$type_id,$fields));

				}
				else
				{
					$dispatcher->trigger('onExtended_save_custom_itemtype_field_save', array ($field, 'update',$item_id,$type_id,$fields));
				}
			}
		}//end not item trash
		return true;
	}

	//can we replace the view config_custom_itemtype WITH THIS?
	function onItemtypeDisplay_config_form(&$itemtypeHtml,$item_type)
	{
		if($item_type != 'custom')
		{
			return false;
		}
		$html = '';
		//$this->getConfig();
		$html .= 'onlyTest';
		//$html .= $editor->display( 'text',  $text , '100%', '550', '85', '20' );
		$itemtypeHtml->text = $html;
		return true;
	}

	function onItemtypeDisplay_item_edit(&$itemtypeHtml,$item_type,$item_id,$text,$itemIntroText,$itemFullText)
	{
		if(strpos($item_type, 'ustom_') === false)
		{
			return false;
		}
		
		$html = '';
		//custom itemtype
		//$html .= JText::_('PI_EXTENSION_ITEMTYPE_CUSTOM'); //load language ok
		
		$pos = strpos($item_type, 'ustom_');
		$type_id = substr($item_type, $pos+6, strlen($item_type));
		
		//get fields config
		$this->db->setQuery( "SELECT * "
		. "\nFROM #__pi_custom_fields "
		. "\nWHERE type_id='$type_id' "
		. "\nORDER BY ordering ASC"
		);
		$fields = $this->db->loadObjectList();
		
		//get fields plugin
		$this->db->setQuery( "SELECT DISTINCT plugin "
		. "\nFROM #__pi_custom_fields "
		. "\nWHERE type_id='$type_id' "
		. "\nORDER BY ordering ASC"
		);
		//$fieldPlugins = $this->db->loadObjectList();
		$fieldPlugins = $this->db->loadResultArray();
		

		//get fieldsvalues
		$this->db->setQuery( "SELECT * "
		. "\nFROM #__pi_custom_fields_values "
		. "\nWHERE item_id='$item_id' "
		);
		$fields_data = $this->db->loadObjectList();
		
		$validation_array = array();
		
		
		//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'extensions'.DS.'helper.php');
		//$extensions = ExtensionHelper::importExtension('fieldtype',null, $fieldPlugins,true,null,true);
		$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'fieldtypehelper.php');
		$extensionss = ExtensionFieldtypeHelper::importExtension(null, $fieldPlugins,true,null,true);
		$dispatcher = &JDispatcher::getInstance();
		
		/*
			here we need something to return
			the fieldtypes will write it in $fieldHtml->text
		*/
		$fieldHtml = & new JObject();
		$fieldHtml->text = '';

		foreach($fields as $field)
		{
		
			//explode params
			$field_params = '';
			$temp = $field->params;
			$temp = explode( '[;-)# ]', $temp);
			for($n = 0; $n < count($temp); $n++){
				$temp2 = explode('-=-',$temp[$n]);
				$var = $temp2[0];
				$value = '';
				if(count($temp2)==2)
				{
					$value = $temp2[1];
				}
				$field_params[$var] = trim($value);
			}
				
			//if the field got field data, get values
			$field_values = '';
			$new_field = 1;
			$field_value = '';
			foreach($fields_data as $field_data)
			{
				if($field_data->field_id==$field->id)
				{
					//explode values
					$field_value = $field_data->value;
					$temp = explode( '[;-)# ]', $field_value);
					for($n = 0; $n < count($temp); $n++)
					{
						//list($var,$value) = split('-=-',$temp[$n]); 
						//$field_values[$var] = trim($value);
						$temp2 = explode('-=-',$temp[$n]);
						$var = $temp2[0];
						$value = '';
						if(count($temp2)==2){
							$value = $temp2[1];
						}
						$field_values[$var] = trim($value);
					}
					$new_field = 0;
					break;
				}
			}
			
			$field_id = 'field_values_'.$field->id;
				
			//comment this come from the fieldtype not longer need ?
			//get language for fieldtype plugin, defaults to english
			//$this->controller->get_fieldtype_language($field->plugin);
			
			
			$validation_type = 0;
			
			//check for validation param
			if($this->check_if_field_param_is_present($field_params, 'validation'))
			{
				$validation_type = $field_params['validation'];
			}
			if($validation_type)
			{
				$alert_message = $field_params['alert_message'];
				$temp = array($field->plugin, $field_id, $validation_type, $alert_message, $field->name);
				array_push($validation_array, $temp);
			}
				
			$field->item_id = $item_id;
				
			$results = $dispatcher->trigger('onDisplay_item_edit', array (&$fieldHtml,$field, $field_params,$field_values,$field_value, $new_field, $field_id));
			/*
			if($results[0] || $results[1] )
			{
			}
			*/
		}//end loop fields
		
		/*
		$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'managerhelper.php');
		ExtensionManagerHelper::importExtension(null,null,true,null,true);
		$htmlelement->html = '';
		$managerItemtypeItemEdit->html = '';
		$dispatcher->trigger('onGetManagerItemtypeItemEdit',array(&$managerItemtypeItemEdit,$item_type,$type_id,$item_id,$text,$itemIntroText,$itemFullText));
		$html .= $managerItemtypeItemEdit->html;
		*/	
		$html .= $fieldHtml->text;
		//make javascript formvalidation script
		$html .= "<script language=\"javascript\"  type=\"text/javascript\">\n";
		$html .= "<!--\n";
		$html .= "function validate_custom_itemtype_fields(){\n\n";
		$html .= "is_valid = true;\n";
		for($n = 0; $n < count($validation_array); $n++)
		{
			$html .= 'field_id=\''.$validation_array[$n][1].'\';'."\n";
			$html .= 'field_name=\''.addslashes($validation_array[$n][4]).'\';'."\n";
			$html .= 'alert_message=\''.addslashes($validation_array[$n][3]).'\';'."\n";
			if(file_exists(dirname(__FILE__).'/../../fieldtypes'.DS.$validation_array[$n][0].DS.$validation_array[$n][2].'.js'))
			{
				/*
				must include other way
				*/
				$html .= JFile::read(dirname(__FILE__).'/../../fieldtypes'.DS.$validation_array[$n][0].DS.$validation_array[$n][2].'.js');
				//$html .= include(dirname(__FILE__).'/../../fieldtypes'.DS.$validation_array[$n][0].DS.$validation_array[$n][2].'.js');
			}
			elseif(file_exists(dirname(__FILE__).'/../../fieldtypes'.DS.$validation_array[$n][0].DS.'validation_'.$validation_array[$n][0].'_'.$validation_array[$n][2].'.js'))
			{
				$html .= JFile::read(dirname(__FILE__).'/../../fieldtypes'.DS.$validation_array[$n][0].DS.'validation_'.$validation_array[$n][0].'_'.$validation_array[$n][2].'.js');
				//$html .= include(dirname(__FILE__).'/../../fieldtypes'.DS.$validation_array[$n][0].DS.'validation_'.$validation_array[$n][0].'_'.$validation_array[$n][2].'.js');
			}
			elseif(file_exists(JPATH_COMPONENT_ADMINISTRATOR.'/javascript/validation_'.$validation_array[$n][0].'_'.$validation_array[$n][2].'.js'))
			{
				$html .= JFile::read(JPATH_COMPONENT_ADMINISTRATOR.'/javascript/validation_'.$validation_array[$n][0].'_'.$validation_array[$n][2].'.js');
				//$html .= include(JPATH_COMPONENT_ADMINISTRATOR.'/javascript/validation_'.$validation_array[$n][0].'_'.$validation_array[$n][2].'.js');
			}
			$html .= "\n\n";
		}
		$html .= "return is_valid;\n";
		$html .= "};\n";
		//close javascript
		$html .= "//-->\n";
		$html .= "</script>\n";
		
		$itemtypeHtml->text = $itemtypeHtml->text.$html;
		return true;
	}

	//here we will set the $article_text if trigger, normally it comes from the fieldtype pi_fish
	function onUpdate_content_table_from_custom_itemtype(&$article_text,$item_id, $item_type,$new_item=false,$row = false, $fields = false, $into=false, $language=false)
	{
		if($language)
		{
			/*
			 ok we will check if $language not false
			 and is $language we will set the actual language to $language
			*/
			$lang = &JFactory::getLanguage();
			$lang->setLanguage($language);
			$lang->load();
		}
		$article_text = $this->update_content_table_from_custom_itemtype($item_id, $item_type,$new_item,$row, $fields, $into, $language);
		if($language)
		{
			/*
			 ok we will check if $language not false
			 and is $language we will set the actual language to the default in com_languages
			*/
			$langParams = JComponentHelper::getParams( 'com_languages' );
			$langDefault = $langParams->get( "administrator", 'en-GB' );
			$lang = &JFactory::getLanguage();
			$lang->setLanguage($langDefault);
		}
		
		//echo $article_text['introtext'];
		return true;
		
	}

	function update_content_table_from_custom_itemtype($item_id, $item_type,$new_item=false,$row = false, $fields = false, $into=false, $language=false)
	{
		
		//get type_id
		$pos = strpos($item_type, 'ustom_');
		$type_id = substr($item_type, $pos+6, strlen($item_type));

		//CHANGE MS NOVEMBER 2010
		if(!$row)
		{
			//get item
			$this->db->setQuery( "SELECT * "
			. "\nFROM #__content "
			. "\nWHERE id='$item_id' "
			. "\nLIMIT 1 "
			);
			$items = $this->db->loadObjectList();
			foreach($items as $temp){
				$row = $temp;
			}
		}
		//if the item is somehow still in the item index, but the item has already been deleted
		if(!$row){
			//delete the item from the item_index
			$database->setQuery("DELETE FROM #__pi_item_index WHERE item_id='$item_id'");
			$database->query();			
			//don't go any further
			return true;
		}
		if(!$fields)
		{
			//get fields and values
			$this->db->setQuery( "SELECT f.*, v.*, v.id AS value_id, f.id AS field_id "
			. "\n FROM #__pi_custom_fields_values AS v "
			. "\n LEFT JOIN #__pi_custom_fields AS f "
			. "\n ON f.id=v.field_id "
			. "\n WHERE f.type_id='$type_id' "
			. "\n AND v.item_id='$item_id' "
			. "\n ORDER BY f.ordering ASC "
			);
			$fields = $this->db->loadObjectList();
			
			//ms: if we have an field not in the custom_fields_value (an error on save or manuall delete the field in the table) the field will not save so i have add this
			//$fields = $this->check_fields($fields,$type_id,$item_id);
		}
		//END CHANGE MS NOVEMBER 2010
				
		//get template
		/*
		$this->db->setQuery( "SELECT template_intro, template_full, read_more, editor_id, html_after, html_before "
		. "\nFROM #__pi_customitemtypes "
		. "\nWHERE id='$type_id' "
		. "\nLIMIT 1 "
		);
		//$templates = $this->db->loadObjectList();
		//$template = $templates[0];
		$template = $this->db->loadObject();
		*/
		/*
		IDEA 
		we make an extensions/htmls/cci_template/templates who manage templates
		if add an template first the standard template (is $template) will add with the field default = 1
		all other become default = 0
		
		then we can all other make as in the normal config_customitemtype
		after safe we add an special field to the $item_type or make an new extension type template
		
		this can work with conditions ore can result in item_edit with an select for the template_id/ name 
		
		and is this field avaible we make:
		
		first an check for the template_id
		*/
		/*
		//get fields and values
		$this->db->setQuery( "SELECT v.value "
			. "\n FROM #__pi_custom_fields_values AS v "
			. "\n LEFT JOIN #__pi_custom_fields AS f "
			. "\n ON f.id=v.field_id "
			. "\n WHERE f.type_id='".$type_id."' "
			. "\n AND v.item_id='".$item_id."' "
			. "\n AND f.plugin='template' "
			. "\n ORDER BY f.ordering ASC "
		);
		$template = null;
		$templatefield = $this->db->loadObject();
		if($templatefield && $templatefield->value)
		{
		*/
			//we trigger the extensions/htmls/template
			$template_id = 0; //''; //$templatefield->value;
			$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
			/*
			require_once($path.DS.'includes'.DS.'extensions'.DS.'htmlhelper.php');
			$extensions = ExtensionHtmlHelper::importExtension('template', 'manager',true,null,true);
			$dispatcher = &JDispatcher::getInstance();
			//$dispatcher->trigger('onGetTemplate', array (&$template_id,'manager',$type_id));
			$dispatcher->trigger('onGetTemplate', array (&$template,'manager',$type_id,$item_id, $item_type,$new_item,$row, $fields, $into, $language));
			*/
			require_once($path.DS.'includes'.DS.'extensions'.DS.'managerhelper.php');
			//$extensions = ExtensionManagerHelper::importExtension(null,'template', true,null,true);
			$extensions = ExtensionManagerHelper::importExtension(null,null, true,null,true);
			$dispatcher = &JDispatcher::getInstance();
			//$dispatcher->trigger('onGetTemplate', array (&$template_id,'manager',$type_id));
			$dispatcher->trigger('onGetManagerItemtypeTemplate', array (&$template,$type_id,$item_id, $item_type,$new_item,$row, $fields, $into, $language));
			
			/*
			//get template
			$this->db->setQuery( "SELECT * "
			. "\nFROM #__pi_customitemtypes_templates "
			. "\nWHERE id='".$template_id."' "
			);
			$template = $this->db->loadObject();
			*/
		//}
		if(!$template)
		{
			//we load the normal layout
			//get template
			$this->db->setQuery( "SELECT template_intro, template_full, read_more, editor_id, html_after, html_before "
			. "\nFROM #__pi_customitemtypes "
			. "\nWHERE id='$type_id' "
			. "\nLIMIT 1 "
			);
			//$templates = $this->db->loadObjectList();
			//$template = $templates[0];
			$template = $this->db->loadObject();
		}

		
		
		$template_intro = $template->template_intro;
		$template_full = $template->template_full;
		$read_more = $template->read_more;
		$editor_id = $template->editor_id;
		$html_after = $template->html_after;
		$html_before = $template->html_before;
		
		//function render_html_from_custom_itemtype_template($template, $fields, $row, $intro_or_full, $readmore_type=0, $editor_id=0, $html_after='', $html_before='', $language = null )
		$introtext = $this->render_html_from_custom_itemtype_template($template_intro, $fields, $row, 'intro', $read_more, $editor_id, $html_after, $html_before, $language);
		//exit;
		if($read_more=='1'){
			//no readmore
			$fulltext = '';
		}elseif($read_more=='2'){
			//only read more if not empty
			$fulltext_temp = $this->render_html_from_custom_itemtype_template($template_full, $fields, $row, 'full', 0, 0, '', '',$language);
			$fulltext_temp2 = str_replace(' ','',$fulltext_temp);
			$length_with_content = strlen($fulltext_temp2);
			
			$full_template_temp = $template_full;
			$template_array = explode('{field_',$full_template_temp);
			foreach($template_array as $template_chunk){
				if(strpos($template_chunk,'}')){
					$end_tag = strpos($template_chunk,'}');
					$temp = substr($template_chunk, 0, $end_tag);
					$underscore = strpos($temp,'_');
					$field_id = substr($temp, $underscore+1, strlen($temp));
					$the_rest = substr($template_chunk, $end_tag+1, strlen($template_chunk));
					$full_template_temp2 .= $the_rest;
				}else{
					$full_template_temp2 .= $template_chunk;
				}
			}
			
			$full_template_temp2 = str_replace(' ','',$full_template_temp2);
			$length_without_content = strlen($full_template_temp2);
			
			//if($length_with_content==$length_without_content){
			if($length_with_content==$length_without_content || $length_without_content==0){
				//there is no content in fulltext 
				$fulltext = '';
			}else{
				//there is content in fulltext
				$fulltext = $this->render_html_from_custom_itemtype_template($template_full, $fields, $row, 'full', $read_more, 0, '', '',$language);
			}
		}elseif($read_more=='3'){
			//readmore using read-more button in one editor
			$fulltext = $this->render_html_from_custom_itemtype_template($template_full, $fields, $row, 'full', $read_more, $editor_id, $html_after, $html_before,$language );
		}else{
			//normal read more
			$fulltext = $this->render_html_from_custom_itemtype_template($template_full, $fields, $row, 'full', $read_more, 0, '', '',$language);
		}
		
		//CHANGE MS NOVEMBER 2010
		if(!$into)
		{
			//update item for read_more link
			$this->db->setQuery( "UPDATE #__content SET introtext='$introtext', `fulltext`='$fulltext' WHERE id='$item_id' ");
			if (!$this->db->query()) {
				echo $this->db->getErrorMsg();
				exit();
			}
		}
		else
		{
			return array("introtext"=>$introtext,"fulltext"=>$fulltext);
		}
		//END CHANGE MS NOVEMBER 2010
		
		
		//ADD MS 18.09.2009
//		$fields = $this->db->loadObjectList();
		if(!$new_item && !$into){
			$this->db->setQuery( "SELECT * "
			."\nFROM #__pi_custom_fields "
			."\nWHERE type_id='$type_id' "
			);
			$fields = $this->db->loadObjectList();
			/*//get data-fields
			$this->db->setQuery( "SELECT id, field_id "
			. "\nFROM #__pi_custom_fields_values "
			. "\nWHERE item_id='$item_id' "
			);
			$data_fields = $this->db->loadObjectList();
			//make array of fields which have a data field and make fields-datafields array
			$new_cit = true;
			$fields_datafields_array = array();
			foreach($fields as $field){
				foreach($data_fields as $field_data){
					if($field_data->field_id==$field->id){
						$fields_datafields_array[$field_data->field_id] = $field_data->id;
						$new_cit = false;
						break;
					}
				}
			}*/
			/*
			todo must rewrite
			*/
			
			//get fields plugin
			$this->db->setQuery( "SELECT DISTINCT plugin "
			. "\nFROM #__pi_custom_fields "
			. "\nWHERE type_id='$type_id' "
			. "\nORDER BY ordering ASC"
			);
			$fieldPlugins = $this->db->loadResultArray();
			/*
			$version = new JVersion();
			$joomlaVersion = $version->getShortVersion();
			if($joomlaVersion < '1.6')
			{
				$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems';
			}
			else
			{
				$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems';
			}
			require_once($path.DS.'includes'.DS.'extensions'.DS.'helper.php');
			$fieldtypes = ExtensionHelper::importExtension('fieldtype',null, $fieldPlugins,true,null,true);
			*/
			$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
			require_once($path.DS.'includes'.DS.'extensions'.DS.'fieldtypehelper.php');
			$fieldtypes = ExtensionFieldtypeHelper::importExtension(null, $fieldPlugins,true,null,true);
			$dispatcher = &JDispatcher::getInstance();
			
			foreach($fields as $field)
			{
				//start update
				$dispatcher->trigger('onExtended_save_custom_itemtype_field_save', array ($field, 'update',$item_id,$type_id,$fields));
			}
		}
		//end field loop MS
		//ADD MS 18.09.2009 END
		
	}
	
	
	function render_html_from_custom_itemtype_template($template, $fields, $row, $intro_or_full, $readmore_type=0, $editor_id=0, $html_after='', $html_before='', $language = null )
	{
		$read_more_link_defined = $this->check_for_read_more_link($fields, $editor_id);
				
		//no template specified, so render default template
		if(!$template)
		{
			//ADD MS
			/*
			$template_special = false;
			foreach($fields as $field)
			{
				//$this->get_field_class_object($field->plugin,'template_special_output');
				if($class_object = $this->get_field_class_object($field->plugin,'template_special_output'))
				{
					$template .= $class_object->template_special_output($fields);
				}
			}
			
			//if(!$template_spezial)
			if(!$template)
			{
			//END ADD MS
			*/
				foreach($fields as $field)
				{
					$template .= '<div>';
					//$template .= '{field_'.$field->plugin.'_'.$field->field_id.'}';
					$template .= '{field_'.$field->name.'_'.$field->field_id.'}';
					$template .= '</div>';
				}
			//ADD MS
			//}
		}
		
		$html = addslashes($template);
		//Deal with readmore type 3
		if($readmore_type=='3')
		{
			if($read_more_link_defined)
			{
				$editor_tag="";
				//Find editor_tag - brute force because we do not know field name for editor!!
				foreach($fields as $field)
				{
					if ($field->field_id == $editor_id)
					{
						// CHANGE MS 09.09.2009
						//$editor_tag='{field_'.$field->name.'_'.$field->field_id.'}';
						$editor_tag='\{field_'.$field->name.'_'.$field->field_id.'\}';
						break;
					}
				}
				//ADD MS 09.09.2009
				/*
				foreach($fields as $field)
				{
					//$this->get_field_class_object($field->plugin,'extended_editor_tag');
					if($class_object = $this->get_field_class_object($field->plugin,'extended_editor_tag'))
					{
						$ex_editor_tag = $class_object->extended_editor_tag($fields,$editor_id,$intro_or_full);
						if (isset($ex_editor_tag))
						{
							$editor_tag = $ex_editor_tag;
							//ADDED MS 09.09.2009 16:30
							$html=stripslashes($html);
							break;
						}
					}

				}
				*/
				//ADD MS END 09.09.2009
				if (! empty($editor_tag))
				{
					switch($intro_or_full){
					case 'intro':
						// in intro view, delete everything after the editor (leave editor)
						//CHANGE MS 09.09.2009 16:30
						//$html=preg_replace('/'.$editor_tag.'(.*?)/is',$editor_tag,$html).$html_after;
						//CHANGE MS 10.09.2009 11:00 add div is </div> after $editor_tag
						//$html=preg_replace('/'.$editor_tag.'.*/is',stripslashes($editor_tag'),$html).$html_after;
						//$html=preg_replace('/'.$editor_tag.'(<\/div>{0,1}).*/is',stripslashes($editor_tag.'${1}'),$html).$html_after;
						preg_match('/'.$editor_tag.'(<\/div>?).*/is', $html, $matches);
						if($matches)
						{
							$html=preg_replace('/'.$editor_tag.'(<\/div>{0,1}).*/is',stripslashes($editor_tag.'${1}'),$html).$html_after;
						}
						else
						{
							$html=preg_replace('/'.$editor_tag.'.*/is',stripslashes($editor_tag),$html).$html_after;
						}

						break;
					case 'full':
						// in full view, delete everything before the editor (leave editor)
						//CHANGE MS 09.09.2009
						//$html=$html_before.preg_replace('/(.*?)'.$editor_tag.'/is',$editor_tag,$html);
						//CHANGE MS 10.09.2009 11:00 add </div> is <div> before $editor_tag
						//$html=$html_before.preg_replace('/(.*?)'.$editor_tag.'/is',stripslashes($editor_tag),$html);
						preg_match('/(<\/div>?)'.$editor_tag.'.*/is', $html, $matches);
						if($matches)
						{
							$html=$html_before.preg_replace('/(.*?)(<div>?)'.$editor_tag.'/is',stripslashes('${2}'.$editor_tag),$html);
						}
						else
						{
							$html=$html_before.preg_replace('/(.*?)'.$editor_tag.'/is',stripslashes($editor_tag),$html);
						}
						//check <div>
						break;
					default;
						break;
					}
				}
			}else{
				if ($intro_or_full=='full'){
					$html='';
				}
			}
		}
		
		$replaceRegex=array();
		$replaceValue=array();
		/*
		COMMENT MS do not know why but if the next line here in the fieldtype calendar the summertime will not calculate (only on render_field_output).
		
		if (! empty($row))
		{
			$replaceRegex[]='/{article_id}/is';
			$replaceValue[]=$row->id;
			$replaceRegex[]='/{article_title}/is';
			$replaceValue[]=addslashes($row->title);
			//$replaceRegex[]='/{article_link}/is';
			//$replaceValue[]=JRoute::_('index.php?option=com_content&view=article&id='.$row->id);
			$replaceRegex[]='/{article_created}/is';
			
			//format
			$config = PagesAndItemsHelper::getConfig();
			//$format = $config['date_format'];
			
			$format = 'Y-m-d H:i:s';
			
			$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
			require_once($path.DS.'includes'.DS.'date.php');
			$app =& JFactory::getApplication();
			//$offset = $app->getCfg('offset');
			
			
			$date = new PagesAndItemsDate($row->created);
			$offset = $app->getCfg('offset');
		
			$summertime = date( 'I', $date->toUnix() );
			if($summertime)
			{
				$offset = $offset +1;
			}
			$date->setOffset($offset);
			
			$created = $date->format($format,true);
			
			$date = new PagesAndItemsDate($row->modified);
			$summertime = date( 'I', $date->toUnix() );
			if($summertime)
			{
				$offset = $offset +1;
			}
			$date->setOffset($offset);
			$modified = $date->format($format,true);
			
			$date = new PagesAndItemsDate($row->publish_up);
			$summertime = date( 'I', $date->toUnix() );
			if($summertime)
			{
				$offset = $offset +1;
			}
			$date->setOffset($offset);
			
			$publish_up = $date->format($format,true);
			unset($date);
			//$replaceValue[]=$row->created;
			$replaceValue[]=$created;
			$replaceRegex[]='/{article_modified}/is';
			$replaceValue[]=$modified;
			$replaceRegex[]='/{article_publish_up}/is';
			$replaceValue[]=$publish_up;
			$replaceRegex[]='/{article_rating}/is';
			//get average rating
			$this->db->setQuery( "SELECT rating_sum, rating_count "
			. "FROM #__content_rating "
			. "WHERE content_id='$row->id' "
			. "LIMIT 1 "
			);
			$ratings = $this->db->loadObjectList();
			$rating_ave = '';
			foreach($ratings as $rating){
				$rating_sum = $rating->rating_sum;
				$rating_count = $rating->rating_count;
				$rating_ave = floor($rating_sum/$rating_count);
			}
			$replaceValue[] = $rating_ave;
		}
		*/
		foreach($fields as $field)
		{
			$field_tag="field_".$field->name."_".$field->field_id;
			if($field->plugin=='image_multisize'){
				for ($n = 1; $n <= 5; $n++){
					$field->size = $n;
					$value=$this->get_custom_itemtype_field_content($field, $intro_or_full, $readmore_type, $editor_id,$language);
					$replaceRegex[]="/{".$field_tag." size=".$n."}/is"; 
					$replaceValue[] = $value;
					$field->output = 'alt';
					$value=$this->get_custom_itemtype_field_content($field, $intro_or_full, $readmore_type, $editor_id,$language);
					$replaceRegex[]="/{".$field_tag." size=".$n." output=alt}/is"; 
					$replaceValue[] = $value;
				}
			}else{
				$value=$this->get_custom_itemtype_field_content($field, $intro_or_full, $readmore_type, $editor_id,$language);
				if (empty($field->value)){
					$replaceRegex[]="/{if-not-empty_".$field_tag."}(.*?){\/if-not-empty_".$field_tag."}/is";
					$replaceValue[] = '';
					$replaceRegex[]="/{\/?if-empty_".$field_tag."}/is";
					$replaceValue[] = '';
				}else{
					$replaceRegex[]="/{if-empty_".$field_tag."}(.*?){\/if-empty_".$field_tag."}/is";
					$replaceValue[] = '';
					$replaceRegex[]="/{\/?if-not-empty_".$field_tag."}/is";
					$replaceValue[] = '';
				}
				$replaceRegex[]="/{".$field_tag."}/is"; 
				$replaceValue[] = $value;
				
			}
		}
		
		if (! empty($row))
		{
			$replaceRegex[]='/{article_id}/is';
			$replaceValue[]=$row->id;
			$replaceRegex[]='/{article_title}/is';
			$replaceValue[]=addslashes($row->title);
			//$replaceRegex[]='/{article_link}/is';
			//$replaceValue[]=JRoute::_('index.php?option=com_content&view=article&id='.$row->id);
			$replaceRegex[]='/{article_created}/is';
			
			//format
			$config = PagesAndItemsHelper::getConfig();
			//$format = $config['date_format'];
			
			$format = 'Y-m-d H:i:s';
			
			$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
			require_once($path.DS.'includes'.DS.'date.php');
			$app =& JFactory::getApplication();
			//$offset = $app->getCfg('offset');
			
			
			$date = new PagesAndItemsDate($row->created);
			$offset = $app->getCfg('offset');
		
			$summertime = date( 'I', $date->toUnix() );
			if($summertime)
			{
				$offset = $offset +1;
			}
			$date->setOffset($offset);
			
			$created = $date->format($format,true);
			
			//$createdX = JHTML::_('date', $row->created, JText::_('DATE_FORMAT_LC2'),$offset);
			
			//$config =& JFactory::getConfig();
			

			
			$date = new PagesAndItemsDate($row->modified);
			$summertime = date( 'I', $date->toUnix() );
			if($summertime)
			{
				$offset = $offset +1;
			}
			$date->setOffset($offset);
			$modified = $date->format($format,true);
			
			$date = new PagesAndItemsDate($row->publish_up);
			$summertime = date( 'I', $date->toUnix() );
			if($summertime)
			{
				$offset = $offset +1;
			}
			$date->setOffset($offset);
			$publish_up = $date->format($format,true);
			
			//$replaceValue[]=$row->created;
			$replaceValue[]=$created;
			$replaceRegex[]='/{article_modified}/is';
			$replaceValue[]=$modified;
			$replaceRegex[]='/{article_publish_up}/is';
			$replaceValue[]=$publish_up;
			$replaceRegex[]='/{article_rating}/is';
			//get average rating
			$this->db->setQuery( "SELECT rating_sum, rating_count "
			. "FROM #__content_rating "
			. "WHERE content_id='$row->id' "
			. "LIMIT 1 "
			);
			$ratings = $this->db->loadObjectList();
			$rating_ave = '';
			foreach($ratings as $rating){
				$rating_sum = $rating->rating_sum;
				$rating_count = $rating->rating_count;
				$rating_ave = floor($rating_sum/$rating_count);
			}
			$replaceValue[] = $rating_ave;
		}
		
		$html=preg_replace($replaceRegex, $replaceValue, $html);
		
		return $html;
	}
	
	function check_for_read_more_link($fields, $editor_id)
	{
		$is_defined = 0;
		foreach($fields as $field){
			if($field->field_id==$editor_id){
				$pos1 = strpos($field->value, '<hr id="system-readmore" />');
				$pos2 = strpos($field->value, '<hr id=\"system-readmore\" />');
				if($pos1 || $pos2){
					$is_defined = 1;
				}
				break;
			}
		}
		return $is_defined;
	}
	
	function get_custom_itemtype_field_content($field, $intro_or_full, $readmore_type=0, $editor_id=0,$language)
	{
		//explode params
		$html = '';
		$field_params = '';
		$temp = $field->params;
		$temp = explode( '[;-)# ]', $temp);
		for($n = 0; $n < count($temp)-1; $n++){
			list($var,$value) = split('-=-',$temp[$n]); 
			$field_params[$var] = trim($value); 
		}
					
		//explode values
		$temp = $field->value;
		$temp = explode('[;-)# ]', $temp);
		for($n = 0; $n < count($temp)-1; $n++){
			list($var,$value) = split('-=-',$temp[$n]); 
			$values[$var] = trim($value); 
		}
		
		//get output
		//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'extensions'.DS.'helper.php');
		//$fieldtypes = ExtensionHelper::importExtension('fieldtype',null, $field->plugin,true,null,true);
		$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'fieldtypehelper.php');
		
		$fieldtypes = ExtensionFieldtypeHelper::importExtension(null, $field->plugin,true,null,true);

		$dispatcher = &JDispatcher::getInstance();
		$html = '';
		$results = $dispatcher->trigger('onRender_field_output', array (&$html,$field, $intro_or_full, $readmore_type, $editor_id,$language));
		//fix old editors html
		$html = str_replace('<br>','<br />', $html);
		
		
		return $html;
	}	

}

?>