<?php
/**
* @version		2.0.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2011 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// No direct access.
defined('_JEXEC') or die;

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.'extension.php');
/**
 * @package		PagesAndItems
*/
class PagesAndItemsControllerManagers extends PagesAndItemsControllerExtension
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
	function cancel()
	{
		$app = &JFactory::getApplication();
		$app->redirect(JRoute::_('index.php?option=com_pagesanditems&view=managers',false));
	}
}