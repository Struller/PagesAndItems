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
require_once(dirname(__FILE__).'/../../../includes/extensions/fieldtype.php');
//ITEM_TITLE
class PagesAndItemsExtensionFieldtypeItem_title extends PagesAndItemsExtensionFieldtype
{
	function params_base()
	{
		$param[] = 'only_once=1';
		$param[] = 'no_pi_fish_table=1';
		return $param;
	}

	function display_config_form($plugin, $type_id, $name, $field_params, $field_id){
		$html = '<div class="pi_form_wrapper">';
		$html .= JText::_('COM_PAGESANDITEMS_NOT_SHOW_ON_EDIT').' '.JText::_('COM_PAGESANDITEMS_ITEM_TITLE_INFO');
		$html .= '</div>';
		return $html;
	}

	function display_item_edit($field, $field_params, $field_values, $field_value, $new_field, $field_id){
		return '';
	}

	function render_field_output($field, $intro_or_full, $readmore_type=0, $editor_id=0)
	{

		$item_id = $field->item_id;
		//make this for pi_fish ready
		if(!$field->value || $field->value =='')
		{
			$this->db->setQuery("SELECT title FROM #__content WHERE id='$item_id' LIMIT 1");
			$items = $this->db->loadResultArray();
			$title = $items[0];
			return addslashes($title);
		}
		else
		{
			return addslashes($field->value);
		}
	}
}

?>