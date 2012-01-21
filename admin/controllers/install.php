<?php
/**
* @version		2.0.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2011 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.controller');
jimport('joomla.client.helper');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controller.php');
/**
 * @package		PagesAndItems
*/
class PagesAndItemsControllerInstall extends PagesAndItemsController //JController
{

/**
	 * Install an extension
	 *
	 * @access	public
	 * @return	void
	 */

	function install()
	{
		//$extension = 'com_pagesanditems';
		//$lang = &JFactory::getLanguage();
		//$lang->load(strtolower($extension), JPATH_COMPONENT_ADMINISTRATOR, null, false, false);
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$model = &$this->getModel( 'Install','PagesAndItemsModel');
		//$modelBase = &$this->getModel( 'Base','pagesanditemsModel');
		$document = &JFactory::getDocument();
		$vType = $document->getType();
		$view	= &$this->getView( 'Install',$vType); //,$vType); //,'pagesanditemsView' );
		$ftp =& JClientHelper::setCredentialsFromRequest('ftp');
		$view->assignRef('ftp', $ftp);
		if ($model->install()) {
			$cache = &JFactory::getCache('mod_menu');
			$cache->clean();
		}
		//$view->assignRef('controller', $this);
		$view->setModel( $model, true );
		require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/pagesanditems.php';
		$isAdmin = PagesAndItemsHelper::getIsAdmin();
		$isSuperAdmin = PagesAndItemsHelper::getIsSuperAdmin();
		if($isSuperAdmin)
		{
			// Load the submenu only for super admins
			
			PagesAndItemsHelper::addSubmenu('install');
		}
		//$view->setModel( $modelBase, false );
		/*
		if($modelBase->is_super_admin)
		{
			// Load the submenu only for super admins
			
			PagesAndItemsHelper::addSubmenu('install');
		}
		*/
		
		$view->display();
/*

		$option = JRequest::getVar('option');
		$url = 'index.php?option=com_pagesanditems&view=install';
		$app = &JFactory::getApplication();
		$app->redirect($url);
*/
	}
	function cancel()
	{
		$this->setRedirect(JRoute::_('index.php?option=com_pagesanditems&view=managers',false));
	}
}