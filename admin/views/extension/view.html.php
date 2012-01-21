<?php
/**
* @version		2.1.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
/**
*/
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'view.html.php');
//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'item'.DS.'view.html.php');



/**
 * HTML View class for the

 */

class PagesAndItemsViewExtension extends PagesAndItemsViewDefault //PagesAndItemsViewItem
{
	function display( $tpl = null )
	{
		
		$popup = JRequest::getVar('popup', 0 );
		if(!$popup)
		{
			//$model = PagesAndItemsHelper::toogleModelPageCategories($this);
			$menuItemsTypes = PagesAndItemsHelper::getMenuItemsTypes();
			$this->assignRef( 'menuItemsTypes',$menuItemsTypes);
			$tree = PagesAndItemsHelper::getTree();
			$this->pageTree = $tree->getTree();		
		}
		
		

		//JHTML::script('overlib_mini.js', 'includes/js/',false);
		parent::display($tpl);
	}
}