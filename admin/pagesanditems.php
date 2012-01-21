<?php
/**
* @version		2.1.2
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('_JEXEC') or die;

//get helper
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'pagesanditems.php');

// Include dependancies
jimport('joomla.application.component.controller');
	$version = new JVersion();
	/*
	$view = JRequest::getVar('view',null);
	if($view)
	{
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'includes'.DS.'extensions'.DS.'managerhelper.php');
		ExtensionManagerHelper::importExtension(null, null,true,null,true);
		$dispatcher = &JDispatcher::getInstance();
		$dispatcher->trigger('onStartView',array());
		$dispatcher->trigger('detach',array('manager'));
	}
*/
	$config = array(); //'view_path'=>JPATH_COMPONENT_ADMINISTRATOR.DS.'views');
	if($version->getShortVersion() < '1.6')
	{
		/*
		 * Joomla 1.5
		*/
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.'controllerJ15.php');
		$controller = controllerJ15::getInstance('PagesAndItems',$config);
	}
	else
	{
		/*
		 * Joomla 1.6
		*/
		$controller= JController::getInstance('PagesAndItems',$config);
	}

	//$controller->addViewPath( JPATH_COMPONENT_SITE.DS.'views' );
	// Execute the task.
	$controller->execute(JRequest::getCmd('task'));
	$controller->redirect();




?>