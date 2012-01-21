<?php
/**
* @version		2.1.3
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
//require_once(dirname(__FILE__).DS.'base.php');
/**

 */


class PagesAndItemsModelCustomItemtypeField extends JModel //PagesAndItemsModelBase
{
	function get_field_value($values_string, $property)
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
		return $html;
	}

	function get_field_param($values_string, $property)
	{
		return $this->get_field_value($values_string, $property);
	}

	function reorder_fields($type_id)
	{
		$db = JFactory::getDBO();
		$db->setQuery("SELECT id FROM #__pi_custom_fields WHERE type_id='$type_id' ORDER BY ordering ASC" );
		$rows = $db->loadObjectList();
		$counter = 1;
		foreach($rows as $row){
			$id = $row->id;
			$db->setQuery( "UPDATE #__pi_custom_fields SET ordering='$counter' WHERE id='$id'");
			$db->query();
			$counter = $counter + 1;
		}
		return $counter;
	}
}

