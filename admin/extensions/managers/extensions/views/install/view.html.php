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

class PagesAndItemsViewInstall extends PagesAndItemsViewDefault//InstallerViewInstall
{


	function display( $tpl = null )
	{
		//: ['.JText::_('COM_PAGESANDITEMS_NEW').']
		PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_MANAGERS').': ['.JText::_('COM_PAGESANDITEMS_EXTENSION_INSTALLER').']</small>');

		//if ($model = &$this->getModel('install'))
		//if ($model = &$this->getModel('ExtensionManageExtensionsinstall'))
		/*
		if ($model = &$this->getModel())
		{
			$app = &JFactory::getApplication();
			$model->setState( 'install.directory', $app->getCfg( 'config.tmp_path' ));
			$state = $model->getState();
		}
		*/
		$paths = new stdClass();
		$paths->first = '';

		$state = $this->get('state');
		//$this->assignRef('paths', $paths);
		//$this->assignRef('state', $state);

		//$state = &$this->get('State');
		//$state = &$this->get('State');


		$extension = 'com_installer';
		$lang = &JFactory::getLanguage();
		//$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false);
		$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false) || $lang->load(strtolower($extension), JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
		// Are there messages to display ?
		$showMessage	= false;
		if ( is_object($state) )
		{
			$msg = trim($state->get('msg'));
			$message1		= trim($state->get('message'));
			$message2		= trim($state->get('extension_message'));
			$showMessage	= ( ($msg && $msg != '') || ($message1 && $message1 != '') || ($message2 && $message2 != ''));

		}

		$this->assign('showMessage',	$showMessage);
		$this->assignRef('state',		$state);

		$ftp =& JClientHelper::setCredentialsFromRequest('ftp');
		$this->assignRef('ftp', $ftp);


		$this->assignRef('paths', $paths);

		parent::display($tpl);
		$this->addToolbar();
	}

	protected function addToolbar()
	{
		JToolBarHelper::cancel('managers.cancel', 'COM_PAGESANDITEMS_CANCEL');
	}

}