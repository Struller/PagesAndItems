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

 */


class PagesAndItemsViewConfig extends PagesAndItemsViewDefault
{

	function display( $tpl = null )
	{
		PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_CONFIG').'</small>');
	
		//if ($model = &$this->getModel('pagesanditems','pagesanditemsModel'))
		if ($model = &$this->getModel('page'))
		{
			$this->assignRef('model', $model);

			$menutypes = $model->getMenutypes();
			$menuitems = $model->getMenuitems();
			$current_menutype = $model->getCurrentMenutype();
			$itemtypes = $model->getItemtypes();
			
			$this->assignRef( 'menutypes',$menutypes);
			$this->assignRef( 'menuitems',$menuitems);
			$this->assignRef( 'current_menutype',$current_menutype);
			$this->assignRef( 'itemtypes',$itemtypes);
			

		}
		
		//get helper
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'pagesanditems.php');
		$helper = new PagesAndItemsHelper();
		$this->assignRef('helper', $helper);
		
		//load com_content language file
		$lang = &JFactory::getLanguage();		
		$lang->load('com_content', JPATH_ADMINISTRATOR, null, false);
		$lang->load('com_menus', JPATH_ADMINISTRATOR, null, false);
		
		parent::display($tpl);
	}
}