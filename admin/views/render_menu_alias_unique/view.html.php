<?php
/**
* @version		2.0.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2011 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'view.html.php');
/**
 * HTML View class for the 
 *

 */

class PagesAndItemsViewRender_menu_alias_unique extends PagesAndItemsViewDefault
{
	function display( $tpl = null )
	{
		if ($model = &$this->getModel('Base')) 
		{
			$this->assignRef( 'model',$model);
		}
		parent::display($tpl);
	}
}