<?php
/**
* @version		2.1.3
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// Check to ensure this file is within the rest of the framework
defined('_JEXEC') or die;
//defined('JPATH_BASE') or die ();
/**
*/
require_once(dirname(__FILE__).DS.'base'.DS.'extension.php');
/**
 * @package		PagesAndItems
 *	InstallerItemtype
*/

class PiInstallerItemtype extends PiInstallerExtension
{
	function __construct( & $parent)
	{
		parent::__construct($parent);
	}

	/*
	function custom_install()
	{
		if (itemtype_install() === false)
		{
			return false;
		}
		return true;
	}
	*/
}
