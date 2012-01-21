<?php
/**
* @version		1.6.0
* @package		PagesAndItems
* @copyright	Copyright (C) 2006-2010 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

//no direct access
if(!defined('_JEXEC'))
{
	die('Restricted access');
}

require_once(dirname(__FILE__).'/../../../includes/extensions/manager.php');

/**
*********************************
* Manager Extensions           *
*********************************
*/
class PagesAndItemsExtensionManagerExtensions extends PagesAndItemsExtensionManager
{

	/**
	@param $name string 
	@param $type string 
	*/
	function XonToolbarButton($name,$type)
	{
		if($name != 'extensions')
		{
			return false;
		}
		
		return true;
	}
	
	function onGetManager(&$managers)
	{
		$link = 'index.php?option=com_pagesanditems';
		$link .= '&task=manager.doExecute'; //display';
		$link .= '&extension=extensions'; //the name
		$link .= '&extensionType=manager'; //the type
		$link .= '&extensionFolder='; //the folder
		$link .= '&extensionTask=display';
		$link .= '&view=install'; //
		//$link .= '&view=install';
		$manager->link = $link;
		$manager->tooltip = JText::_('PI_EXTENSION_MANAGER_EXTENSIONS_VIEW_INSTALL_NAME');
		$manager->text = JText::_('PI_EXTENSION_MANAGER_EXTENSIONS_VIEW_INSTALL_NAME');
		$manager->alt = JText::_('PI_EXTENSION_MANAGER_EXTENSIONS_VIEW_INSTALL_NAME');
		$path = JURI::root(true).'/'.str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).DS)));
		$manager->image = $path.'/media/images/icon-48-extension_install.png';
		$managers[] = $manager;
		$manager = null;
		
		/*
		
		*/

		$link = 'index.php?option=com_pagesanditems';
		$link .= '&task=manager.doExecute'; //display';
		$link .= '&extension=extensions'; //the name
		$link .= '&extensionType=manager'; //the type
		$link .= '&extensionFolder='; //the folder
		$link .= '&extensionTask=display';
		$link .= '&view=piextensions'; //
		//$link .= '&view=managers';
		$manager->link = $link;
		$manager->tooltip = JText::_('PI_EXTENSION_MANAGER_EXTENSIONS_VIEW_EXTENSIONS_NAME');
		$manager->text = JText::_('PI_EXTENSION_MANAGER_EXTENSIONS_VIEW_EXTENSIONS_NAME');
		$manager->alt = JText::_('PI_EXTENSION_MANAGER_EXTENSIONS_VIEW_EXTENSIONS_NAME');
		$path = JURI::root(true).'/'.str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).DS)));
		$manager->image = $path.'/media/images/icon-48-extension_edit.png';
		$managers[] = $manager;
		/*
		$manager = null;
		
		$link = 'index.php?option=com_pagesanditems';
		$link .= '&task=manager.doExecute'; //display';
		$link .= '&extension=extensions'; //the name
		$link .= '&extensionType=manager'; //the type
		$link .= '&extensionFolder='; //the folder
		$link .= '&extensionTask=display';
		$link .= '&view=manage'; //
		$manager->link = $link;
		$manager->tooltip = JText::_('PI_EXTENSION_MANAGER_EXTENSIONS_VIEW_MANAGE_NAME');
		$manager->text = JText::_('PI_EXTENSION_MANAGER_EXTENSIONS_VIEW_MANAGE_NAME');
		$manager->alt = JText::_('PI_EXTENSION_MANAGER_EXTENSIONS_VIEW_MANAGE_NAME');
		$path = JURI::root(true).'/'.str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).DS)));
		$manager->image = $path.'/media/images/icon-48-extension_manage.png';
		$managers[] = $manager;
		*/
		return true;
	}
	
	function XonDisplayContent(&$content,$extension,$sub_task,$model)
	{
		//here we set also Toolbar?
		
		$content->text = 'extensions';
		return true;
	}
	
	function XonGetModelName(&$models)
	{
		
		jimport( 'joomla.application.component.model' );
		$path = realpath(dirname(__FILE__).DS.'models');
		JModel::addIncludePath($path);
		$models[] = 'managerextensions';
		return true;
	}
}

?>