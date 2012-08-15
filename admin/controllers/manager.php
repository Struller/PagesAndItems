<?php
/**
* @version		2.1.6
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// No direct access.
defined('_JEXEC') or die;

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.'extension.php');
/**
 * @package		PagesAndItems
*/
class PagesAndItemsControllerManager extends PagesAndItemsControllerExtension
{
	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 */
	function __construct( $config = array())
	{
		parent::__construct($config);
	}

	function display($tpl = null)
	{
		$extensionTask = JRequest::getVar('extensionTask',null);
		$extensionName = JRequest::getVar('extensionName','' ); //JRequest::getVar('extensionName',JRequest::getVar('extension', '' )); //is the extensionName
		if(JRequest::getVar('extension') != '')
		{
			//TODO error warning
			$extensionName = JRequest::getVar('extension','' );
		}
		$extensionFolder = JRequest::getVar('extensionFolder', '');
		$extensionType = JRequest::getVar('extensionType', '');
		JRequest::setVar('view', JRequest::getVar('view', 'managers'));
		JRequest::setVar( 'layout', 'manager' );
		parent::display($tpl);
	}
}