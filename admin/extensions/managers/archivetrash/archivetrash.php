<?php
/**
* @version		2.1.6
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
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
* Manager Archive           *
*********************************
*/
class PagesAndItemsExtensionManagerArchiveTrash extends PagesAndItemsExtensionManager
{

	/**
	@param $name string
	@param $type string
	*/
	function onToolbarButton($name,$type)
	{
		if($name != 'archivetrash')
		{
			return false;
		}

		return true;
	}

	function onGetManager(&$managers)
	{
		$link = 'index.php?option=com_pagesanditems';
		$link .= '&task=manager.doExecute'; //display';
		$link .= '&extensionName=archivetrash'; //the name
		$link .= '&extensionType=manager'; //the type
		$link .= '&extensionFolder='; //the folder
		$link .= '&extensionSubTask=display';
		$link .= '&view=archivetrash'; //


		$manager->link = $link;
		$manager->tooltip = JText::_('PI_EXTENSION_MANAGER_ARCHIVETRASH_NAME');
		$manager->text = JText::_('PI_EXTENSION_MANAGER_ARCHIVETRASH_NAME');
		$manager->alt = JText::_('PI_EXTENSION_MANAGER_ARCHIVETRASH_NAME');
		//$path = str_replace(DS,'/',str_replace(JPATH_ROOT.DS,JURI::root(),realpath(dirname(__FILE__).DS)));
		//$path = str_replace(DS,'/',str_replace(JPATH_BASE.DS,'',realpath(dirname(__FILE__).DS)));
		$path = JURI::root(true).'/'.str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).DS)));
		$manager->image = $path.'/media/images/icon-48-archivetrash.png';
		//$lang =
		$managers[] = $manager;
		return true;
	}

	function onDisplayContent(&$content,$extension,$sub_task) //,$model)
	{
		//here we set also Toolbar?

		$content->text = 'archivetrash';
		return true;
	}

	//????
	function onGetModelName(&$models)
	{
		jimport( 'joomla.application.component.model' );
		$path = realpath(dirname(__FILE__).DS.'models');
		JModel::addIncludePath($path);
		$models[] = 'archivetrash';
		return true;
	}
	/*
	jimport( 'joomla.application.component.model' );
		JModel::addIncludePath($path);
	*/

}

?>