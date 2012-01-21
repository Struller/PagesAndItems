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
class PagesAndItemsControllerExtensionManagerExtensionsInstall extends PagesAndItemsController //JController
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
		$path = realpath(dirname(__FILE__).DS.'..'.DS.'models');
		JModel::addIncludePath($path);
		//$model = &$this->getModel( 'Install','PagesAndItemsModel');
		$model = $this->getModel( 'Install','PagesAndItemsModel');
		if ($model->install())
		{
			$cache = &JFactory::getCache('mod_menu');
			$cache->clean();
		}
		$app = JFactory::getApplication();
		//dump('test');
		//dump($app->getUserState('com_pagesanditems.installer.message'),'app');
		
		/*
		$app = JFactory::getApplication();
		$redirect_url = $app->getUserState('com_pagesanditems.installer.redirect_url');
		if(empty($redirect_url)) 
		{
			$redirect_url = JRoute::_('index.php?option=com_pagesanditems&task=manager.doExecute&extension=extensions&extensionFolder=&extensionType=manager&extensionTask=display&view=install',false);
		} 
		else
		{
			// wipe out the user state when we're going to redirect
			$app->setUserState('com_pagesanditems.installer.redirect_url', '');
			
			//$app->setUserState('com_pagesanditems.installer.message', '');
			//$app->setUserState('com_pagesanditems.installer.extension_message', '');
		}
		$this->setRedirect($redirect_url);
		*/
		
		//$modelBase = &$this->getModel( 'Base','pagesanditemsModel');
		$this->addViewPath(realpath(dirname(__FILE__).'/views'));
		$document = &JFactory::getDocument();
		
		$vType = $document->getType();
		$view	= &$this->getView( 'Install',$vType); //,$vType); //,'pagesanditemsView' );
		$view->addTemplatePath(realpath(dirname(__FILE__).'/views'.DS.'install'.DS.'tmpl'));
		// Set the layout
		$view->setLayout('default');
		$ftp =& JClientHelper::setCredentialsFromRequest('ftp');
		$view->assignRef('ftp', $ftp);
		//$view->assignRef('controller', $this);
		
		$view->setModel( $model, true );
		require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/pagesanditems.php';
		

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