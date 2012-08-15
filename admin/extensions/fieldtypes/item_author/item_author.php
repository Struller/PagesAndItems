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
//ITEM_AUTHOR
class PagesAndItemsExtensionFieldtypeItem_author extends PagesAndItemsExtensionFieldtype
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
			$field_params['name_type'] = $this->params->get('name_type','username');
		}
		//display
		$field_name = JText::_('COM_PAGESANDITEMS_DISPLAY');
		$field_content = '<label><input type="radio" class="radio" value="username" name="field_params[name_type]"';
		if($field_params['name_type']=='username'){
			$field_content .= 'checked="checked"';
		}
		$field_content .= ' />';
		$field_content .= JText::_('COM_PAGESANDITEMS_USERNAME');
		$field_content .= '</label><br />';
		$field_content .= '<label><input type="radio" class="radio" value="name" name="field_params[name_type]" ';
		if($field_params['name_type']=='name'){
			$field_content .= 'checked="checked"';
		}
		$field_content .= ' />';
		$field_content .= JText::_('COM_PAGESANDITEMS_NAME');
		$field_content .= '</label>';
		$html = $this->display_field($field_name, $field_content);
		//info
		$html .= '<div class="pi_form_wrapper">';
		$html .= JText::_('COM_PAGESANDITEMS_NOT_SHOW_ON_EDIT').' '.JText::_('COM_PAGESANDITEMS_ITEM_AUTHOR_INFO');
		$html .= '</div>';
		return $html;
	}

	function display_item_edit($field, $field_params, $field_values, $field_value, $new_field, $field_id){
		return '';
	}

	function render_field_output($field, $intro_or_full, $readmore_type=0, $editor_id=0){

		$item_id = $field->item_id;
		$this->db->setQuery("SELECT created_by, created_by_alias FROM #__content WHERE id='$item_id' LIMIT 1");
		$item_authors = $this->db->loadObjectList();
		$author_id = 0;
		foreach($item_authors as $item_author){

			$author_alias = $item_author->created_by_alias;
			if($author_alias){
				$author_name = $author_alias;
			}else{
				$author_id = $item_author->created_by;
				$this->db->setQuery("SELECT name, username FROM #__users WHERE id='$author_id' LIMIT 1");
				$authors = $this->db->loadObjectList();
				$author = $authors[0];
				if($this->get_field_param($field->params,'name_type')=='username'){
					$author_name = $author->username;
				}else{
					$author_name = $author->name;
				}
			}
		}

		return $author_name;
	}
}

?>