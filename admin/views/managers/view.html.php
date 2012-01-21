<?php
/**
* @version		2.1.3
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

class PagesAndItemsViewManagers extends PagesAndItemsViewDefault
{
	function display( $tpl = null )
	{
		PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_MANAGERS').'</small>');
		/*
		if ($model = &$this->getModel('Base'))
		{
			$this->assignRef( 'model',$model);
		}
		*/
		//$types = ...
		$managers = array();
		/*
		manager extension and install have ther own submenu-item
		and there are not extensions so we will not display here
		*/
		/*
		$manager->link = 'index.php?option=com_pagesanditems&view=manage&back=managers';
		$manager->image = PagesAndItemsHelper::getDirIcons().'/base/icon-48-extension_manage.png';
		$manager->alt = JText::_('COM_PAGESANDITEMS_EXTENSIONS');
		$manager->text = JText::_('COM_PAGESANDITEMS_EXTENSIONS');
		$managers[] = $manager;

		$manager = null;
		$manager->link = 'index.php?option=com_pagesanditems&view=install&back=managers';
		$manager->image = PagesAndItemsHelper::getDirIcons().'/base/icon-48-extension_install.png';
		$manager->alt = JText::_('COM_PAGESANDITEMS_INSTALL');
		$manager->text = JText::_('COM_PAGESANDITEMS_INSTALL');

		$managers[] = $manager;
		*/

		$path = realpath(dirname(__FILE__).DS.'..'.DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'managerhelper.php');
		//$typeName = 'ExtensionManagerHelper';
		//$typeName::importExtension(null, null,true,null,true);
		ExtensionManagerHelper::importExtension(null, null,true,null,true);
		
		$dispatcher = &JDispatcher::getInstance();

		$dispatcher->trigger('onGetManager', array ( &$managers));

		/*
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		$this->assignRef( 'joomlaVersion',$joomlaVersion);

		$app = JFactory::getApplication();
		$option = JRequest::getVar('option');
		$db =& JFactory::getDBO();
		*/
		/*
		$manager->link = 'index.php?option=com_pagesanditems&view=';
		$manager->image = PagesAndItemsHelper::getDirIcons().'/icon-48-pi.png';
		$manager->alt = 'alt';
		$manager->text = 'test';
		$managers[] = $manager;
		*/
		/*

		*/
		$this->assignRef('managers', $managers);
		parent::display($tpl);
		JToolBarHelper::cancel( 'page.cancel', JText::_('COM_PAGESANDITEMS_CANCEL') );
	}

}

