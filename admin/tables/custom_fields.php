<?php
/**
* @version		2.1.6
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
class PagesAndItemsTableCustom_fields extends JTable
{
	var $id = null;
	var $name = null;	//
	var $type_id = null;
	var $plugin = null; //???
	var $params = null;
	var $ordering = null;
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
		parent::__construct('#__pi_custom_fields', 'id', $db);


	}

	function Xbind($array, $ignore = '')
	{
		//parent::bind($array, $ignore);

		if (is_array($array))
		{
			if (isset( $array['params']) )
			{
				if(is_array($array['params']) || is_object($array['params']))
				{
					$array['params'] = json_encode($array['params']);
				}
				else
				{
				}
			}
		}
		return parent::bind($array, $ignore);
	}
}
