<?php
/**
* @version		2.1.6
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'view.html.php');
/**
 * HTML View class for the

 */

class PagesAndItemsViewInstance_select extends PagesAndItemsViewDefault
{
	function display( $tpl = null )
	{
		if(PagesAndItemsHelper::getIsAdmin())
		{
			$menuItemsTypes = PagesAndItemsHelper::getMenuItemsTypes();
			$this->assignRef( 'menuItemsTypes',$menuItemsTypes);
			$tree = PagesAndItemsHelper::getTree();
			$this->pageTree = $tree->getTree();
		}

		if ($model = &$this->getModel('Page'))
		{
			/*
			if($model->isAdmin)
			{
				$menuItemsTypes = PagesAndItemsHelper::getMenuItemsTypes();
				$this->assignRef( 'menuItemsTypes',$menuItemsTypes);
				//$pageTree = $model->getPages();
				//$this->assignRef( 'pageTree',$pageTree);
				$tree = PagesAndItemsHelper::getTree();
				$this->pageTree = $tree->getTree();
			}
				*/
			$this->assignRef( 'model',$model);
		}

		//JHTML::script('dtree.js', 'administrator/components/com_pagesanditems/javascript/',false);
		//JHTML::script('overlib_mini.js', 'includes/js/',false);
		parent::display($tpl);
	}
}