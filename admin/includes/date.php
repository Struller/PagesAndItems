<?php
/**
 * @version		1.6.2.2$Id: date.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Framework
 * @subpackage	Utilities
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('JPATH_BASE') or die;

/**
 * PDate is a class that stores a date and provides logic to manipulate
 * and render that date in a variety of formats.
 */

$version = new JVersion();
$joomlaVersion = $version->getShortVersion();
if($joomlaVersion < '1.6')
{
	require_once(dirname(__FILE__).DS.'date15.php');
	
}
else
{
	//require_once(dirname(__FILE__).DS.'date16.php');
	jimport( 'joomla.utilities.date' );
	class PagesAndItemsDate extends JDate
	{
	
	}
}

