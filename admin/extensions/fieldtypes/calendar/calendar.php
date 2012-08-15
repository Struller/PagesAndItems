<?php
/**
* @version		2.1.6
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
//CALENDAR
class PagesAndItemsExtensionFieldtypeCalendar extends PagesAndItemsExtensionFieldtype
{
	function params_base()
	{
		$param[] = 'only_once=1';
		$param[] = 'no_pi_fish_table=0';
		return $param;
	}

	function display_config_form($plugin, $type_id, $name, $field_params, $field_id)
	{
		if(!$field_id)
		{
			//new field, set defaults here
			$field_params['showFieldName'] = $this->params->get('showFieldName'); //0
			$field_params['date_format'] = $this->params->get('date_format');
			$field_params['display_time'] = $this->params->get('display_time');
			//$field_params['date_format'] = '%d-%m-%Y';
		}
		$html = '';
		//New show field name
		$html .= $this->makeShowFieldName($field_id,$field_params);
		//description
		$html .= $this->display_field_description($field_params);
		//display
		$field_name = JText::_('COM_PAGESANDITEMS_DATE_FORMAT');
		$field_content = '<input type="text" class="width200" value="';
		$field_content .= $field_params['date_format'];
		$field_content .= '" name="field_params[date_format]" />';
		$field_content .= '<br />';
		$field_content .= JText::_('COM_PAGESANDITEMS_EXAMPLE');
		$field_content .= '<br />%d-%m-%Y<br /><a href="http://linux.about.com/od/commands/l/blcmdl1_date.htm" target="_blank">';
		$field_content .= 'http://linux.about.com/od/commands/l/blcmdl1_date.htm</a><br />';
		$html .= $this->display_field($field_name, $field_content);

		$this->db->setQuery( "SELECT plugin "
			. "\nFROM #__pi_custom_fields "
			. "\nWHERE type_id='$type_id' "
			. "\nORDER BY ordering ASC"
		);
		$fieldPlugins = $this->db->loadResultArray();

		if(in_array('pi_fish',$fieldPlugins))
		{
			$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
			require_once($path.DS.'includes'.DS.'extensions'.DS.'fieldtypehelper.php');
			$extensions = ExtensionFieldtypeHelper::importExtension(null, 'pi_fish',true,null,true);
			$dispatcher = &JDispatcher::getInstance();

			$fieldHtml = '';
			$dispatcher->trigger('onSetLanguageHtml', array (&$fieldHtml,JText::_('COM_PAGESANDITEMS_DATE_FORMAT'),'date_format',$field_params));
			$html .= $fieldHtml;
		}

		$field_name = JText::_('PI_EXTENSION_FIELDTYPE_CALENDAR_DISPLAY_TIME');
		$field_content = '<input type="checkbox" ';
		if($this->check_if_field_param_is_present($field_params, 'display_time'))
		{
			if($field_params['display_time'])
			{
				$field_content .= ' checked="checked"';
			}
		}
		$field_content .= 'name="field_params[display_time]" value="1">&nbsp';

		$html .= $this->display_field($field_name, $field_content);

		return $html;
	}

	function display_item_edit($field, $field_params, $field_values, $field_value, $new_field, $field_id)
	{
		$html = '<div class="field_type_text fieldtype">';
		$field_name = '&nbsp;'; //$field->name;

		$format = $field_params['date_format'];
		require_once(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'helpers'.DS.'pagesanditems.php');

		$html .= '<style type="text/css">';
		$html .= '#calendar'.$field_id."\n";
		$html .= '{'."\n";
		$html .= '	width: 50%;'."\n";
		$html .= '}'."\n";
		$html .= '</style>';

		//function calendar($value, $name, $id, $format = '%Y-%m-%d', $attribs = null)
		//$config = PagesAndItemsHelper::getConfig();
		//$format = $config['date_format'];
		//ce: no. don't get the date format from the pi-config. it has been deprecated AND the dateformat is configurable in the fields config, so lets use that. Is declared above.

		$database = JFactory::getDBO();
		$database->setQuery( "UPDATE #__pi_config SET config='$format' WHERE id='debug' ");
		$database->query();

		if($this->check_if_field_param_is_present($field_params, 'display_time'))
		{
			if($field_params['display_time'])
			{
				$display_time =  'true';
			}
			else
			{
				$display_time = 'false';
			}
		}
		else
		{
			$display_time = 'false';
		}
		//here we get the right date
		$date = PagesAndItemsHelper::get_date_to_format($field_value,$format);

		// Get some system objects.
		$config = JFactory::getConfig();
		$user	= JFactory::getUser();
		// Convert a date to UTC based on the user timezone.
		if (intval($field_value)) {
			// Get a date object based on the correct timezone.
			$jDate = JFactory::getDate($field_value, 'UTC');
			$jDate->setTimezone(new DateTimeZone($user->getParam('timezone', $config->get('offset'))));
			// Transform the date string.
			$date = $jDate->toMySQL(true);
		}
		//here only for edit
		$format="%Y-%m-%d %H:%M:%S";

		$field_content = PagesAndItemsHelper::calendar($date,'calendar'.$field_id, 'calendar'.$field_id,$format, null,array('showsTime'=>$display_time));//JHTML::calendar($date,'calendar'.$field_id, 'calendar'.$field_id, $format);


		$html .= $this->display_field($field_name, $field_content);
		$html .= '</div>';
		return $html;

	}

	function field_save($field, $insert_or_update)
	{
		/*
		//$date = strtotime($field->value);
		$test = strtotime('2011-07-03');
		$summertime = date( 'I', $test );
		if($summertime)
		{
			//$yoffset = $yoffset +1;
		}
		*/

		//here we get the right date
		require_once(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'helpers'.DS.'pagesanditems.php');


		$format = $field->params['date_format'];
		if($this->check_if_field_param_is_present($field->params, 'display_time'))
		{
			if($field->params['display_time'])
			{
				//$format .= ' %H:%M:%S';
			}
		}

		$value_name = 'calendarfield_values_'.$field->id;

		$value = JRequest::getVar($value_name,'','post');
		if($value==JText::_('COM_PAGESANDITEMS_NOW') || $value=='')
		{
			$value = PagesAndItemsHelper::get_date_now(true,$format);
		}
		else
		{
			if ($value && strlen(trim($value)) <= 10)
			{
				$value .= ' 00:00:00';
			}
		}
		//$value = $this->get_date_ready_for_database($value);



		$value = PagesAndItemsHelper::get_date_ready_for_database($value,false);
		return $value;
	}


	function render_field_output($field, $intro_or_full, $readmore_type=0, $editor_id=0, $language = null)
	{

		//$date = JFactory::getDate($date);
		$format = $this->get_field_param($field->params, 'date_format');


		if($language)
		{

			$this->db->setQuery( "SELECT plugin "
				. "\nFROM #__pi_custom_fields "
				. "\nWHERE type_id='$field->type_id' "
				. "\nORDER BY ordering ASC"
			);
			$fieldPlugins = $this->db->loadResultArray();
			if(in_array('pi_fish',$fieldPlugins))
			{
				$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
				require_once($path.DS.'includes'.DS.'extensions'.DS.'fieldtypehelper.php');
				$extensions = ExtensionFieldtypeHelper::importExtension(null, 'pi_fish',true,null,true);
				$dispatcher = &JDispatcher::getInstance();
				$dispatcher->trigger('onGetLanguageHtml', array (&$format,'date_format',$field->params,$language));
			}
		}

		//require_once(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'helpers'.DS.'pagesanditems.php');
		//$date = strtotime($field->value);

		/*
		//here we get not the right date
		$date = PagesAndItemsHelper::get_date_to_format($field->value,$format);
		return $date;

		//$date = PagesAndItemsHelper::get_date_to_format($date,$format);

		$app =& JFactory::getApplication();
		//$date = strtotime($field->value);
		$date = JFactory::getDate($field->value); //,$offset);
		$offset = $app->getCfg('offset');
		//JFactory::getDate($date)->toUnix()
		$summertime = date( 'I', $date->toUnix() );
		if($summertime)
		{
			$offset = $offset +1;
		}
		$date->setOffset($offset);
		$date = $date->toFormat($format);

		return $date;
		*/
		//$date	= JFactory::getDate();
		$date = new JDate($field->value);
		$formatted_date = $date->toFormat($format);

		return $formatted_date;
	}
}

?>