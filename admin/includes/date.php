<?php
/**
* @version		2.1.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
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

