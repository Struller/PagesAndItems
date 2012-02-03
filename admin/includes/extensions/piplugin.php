<?php
/**
* @version		2.1.5
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('JPATH_BASE') or die;

require_once(dirname(__FILE__).DS.'extension.php');

abstract class PagesAndItemsExtensionPiPlugin extends PagesAndItemsExtension
{
	/**
	 * Constructor
	 *
	 * @param object $subject The object to observe
	 * @param array  $config  An optional associative array of configuration settings.
	 */
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);

	}
}
