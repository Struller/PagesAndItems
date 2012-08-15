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
//ITEM_MODIFIED_DATE
class PagesAndItemsExtensionFieldtypeItem_modified_date extends PagesAndItemsExtensionFieldtype
{
	function params_base()
	{
		$param[] = 'only_once=1';
		$param[] = 'no_pi_fish_table=1';
		return $param;
	}

	function display_config_form($plugin, $type_id, $name, $field_params, $field_id){
		if(!$field_id)
		{
			//new field, set defaults here
			$field_params['date_format'] = $this->params->get('date_format'); //'d F Y H:i:s'
		}
		//display
		$field_name = JText::_('COM_PAGESANDITEMS_DATE_FORMAT');
		$field_content = '<input type="text" class="width200" value="';
		$field_content .= $field_params['date_format'];
		$field_content .= '" name="field_params[date_format]" />';
		$field_content .= '<br />';
		$field_content .= JText::_('COM_PAGESANDITEMS_EXAMPLE');
		$field_content .= '<br />d F Y H:i:s <br /><a href="http://php.net/manual/en/function.date.php" target="_blank">';
		$field_content .= 'http://php.net/manual/en/function.date.php</a><br />';
		$field_content .= JText::_('COM_PAGESANDITEMS_NOT_SHOW_ON_EDIT');
		$html = $this->display_field($field_name, $field_content);

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
		return $html;
	}

	function display_item_edit($field, $field_params, $field_values, $field_value, $new_field, $field_id){
		return '';
	}

	function render_field_output($field, $intro_or_full, $readmore_type=0, $editor_id=0,$language = null)
	{

		$item_id = $field->item_id;

		$this->db->setQuery("SELECT modified FROM #__content WHERE id='$item_id' LIMIT 1");
		$items = $this->db->loadResultArray();
		$modified = $items[0];


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
		$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
		require_once($path.DS.'includes'.DS.'date.php');
		$date = new PagesAndItemsDate($modified);
		$app =& JFactory::getApplication();
		$offset = $app->getCfg('offset');
		$summertime = date( 'I', $date->toUnix() );
		if($summertime)
		{
			$offset = $offset +1;
		}
		$date->setOffset($offset);
		$date = $date->format($format,true);

		return $date;

		$date = strtotime($modified);
		$formatted_date = date($this->get_field_param($field->params, 'date_format'), $date);

		return $formatted_date;
	}
}

?>