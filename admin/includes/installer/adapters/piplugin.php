<?php
/**
* @version		2.1.1
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// No direct access
defined('JPATH_BASE') or die;

//jimport('joomla.base.adapterinstance');

/**
 * Language installer
 */

require_once(dirname(__FILE__).DS.'base'.DS.'extension.php');

class PiInstallerPiPlugin extends PiInstallerExtension
{
	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param	object	$parent	Parent object [PiInstaller instance]
	 * @return	void
	 * @since	1.5
	 */
	function __construct( & $parent) //, &$parentParent = null)
	{
		parent::__construct($parent);
	}
}
