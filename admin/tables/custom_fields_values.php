<?php
/**
* @version		2.1.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Extension table
 */
class PagesAndItemsTableCustom_fields_values extends JTable
{
	var $id = null;
	var $field_id = null;
	var $item_id = null;
	var $value = null;
	var $state = null;

/*
	var $checked_out = null;
	var $checked_out_time = null;
*/
	/**
	 * Contructor
	 *
	 * @access var
	 * @param database A database connector object
	 */

	function __construct(&$db)
	{
		parent::__construct('#__pi_custom_fields_values', 'id', $db);


	}
}
