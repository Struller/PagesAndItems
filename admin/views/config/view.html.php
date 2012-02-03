<?php
/**
* @version		2.1.5
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


class PagesAndItemsViewConfig extends PagesAndItemsViewDefault
{

	function display( $tpl = null )
	{
		PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_CONFIG').'</small>');
		$app = JFactory::getApplication();
		$option = JRequest::getVar('option');
		$itemtype = JRequest::getVar( 'sub_task', '');
		$app->setUserState( $option.'.refer','');
		
		$this->db = JFactory::getDBO();
		
		//echo $var;
		//if ($model = &$this->getModel('pagesanditems','pagesanditemsModel'))
		/*
		if ($model = &$this->getModel('page'))
		{
			$this->assignRef('model', $model);
		}
		*/
			$menutypes = PagesAndItemsHelper::getMenutypes();
			$menuitems = PagesAndItemsHelper::getMenuitems();
			$current_menutype = PagesAndItemsHelper::getCurrentMenutype();

			//$itemtypes = $model->getItemtypes();
			$itemtypes = PagesAndItemsHelper::getItemtypes();

			$this->assignRef( 'menutypes',$menutypes);
			$this->assignRef( 'menuitems',$menuitems);
			$this->assignRef( 'current_menutype',$current_menutype);
			$this->assignRef( 'itemtypes',$itemtypes);


		

		//get helper
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'pagesanditems.php');
		$helper = new PagesAndItemsHelper();
		$this->assignRef('helper', $helper);

		//load com_content language file
		$lang = &JFactory::getLanguage();
		
		$lang		= JFactory::getLanguage();
		$lang->load('com_content.sys', JPATH_ADMINISTRATOR, null, false, false)
		|| $lang->load('com_content.sys', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
		$lang->load('com_content', JPATH_ADMINISTRATOR, null, false)
			|| $lang->load('com_content', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
		$lang->load('com_menus', JPATH_ADMINISTRATOR, null, false)
		|| $lang->load('com_menus', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);

		parent::display($tpl);
		$this->addToolbar();

	}
	
	protected function addToolbar()
	{

		JToolBarHelper::apply( 'config.config_apply');//, JText::_('COM_PAGESANDITEMS_APPLY') );
		JToolBarHelper::save( 'config.config_save');//, JText::_('COM_PAGESANDITEMS_SAVE') );
		JToolBarHelper::divider();
		JToolBarHelper::cancel( 'config.cancel');//, JText::_('COM_PAGESANDITEMS_CANCEL') );
	}
}