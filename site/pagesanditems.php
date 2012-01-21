<?php
/**
* @version		2.1.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// no direct access
defined('_JEXEC') or die();

/*
// Require the base controller
require_once (JPATH_COMPONENT.DS.'controller.php');

// Create the controller
$controller = new pagesanditemsController();

// Perform the Request task
$controller->execute(JRequest::getVar('task', null, 'default', 'cmd'));

// Redirect if set by the controller
$controller->redirect();
*/
/*


*/
//get helper
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'pagesanditems.php');

// Include dependancies
jimport('joomla.application.component.controller');
	
	$version = new JVersion();
	//we work wit the JPATH_COMPONENT_ADMINISTRATOR so we need not an base controller here in frontend as file
	//JController or controllerJ15 will manage this.
	
	$config = array('base_path'=>JPATH_COMPONENT_ADMINISTRATOR,'model_path'=>JPATH_COMPONENT_ADMINISTRATOR.DS.'models'); //,'view_path'=>JPATH_COMPONENT_ADMINISTRATOR.DS.'views');
	if($version->getShortVersion() < '1.6')
	{
		// J15
		//	we work with an extends JController it will work on:
		//	$config = array('base_path'=>JPATH_COMPONENT_ADMINISTRATOR);
		//	this is integratet in the controllerJ15
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.'controllerJ15.php');
		$controller = controllerJ15::getInstance('PagesAndItems',$config); 
	}
	else
	{
		// J1.6
		//	we tell the JController who find the base controller and the other controller
		//$config = array('base_path'=>JPATH_COMPONENT_ADMINISTRATOR,'model_path'=>JPATH_COMPONENT_ADMINISTRATOR.DS.'models');
		$controller= JController::getInstance('PagesAndItems',$config); 
	}
	
	//ADD ms: 03.05.2011 here we need the language from component
	$lang = &JFactory::getLanguage();
	$loaded = $lang->load('com_pagesanditems', JPATH_ADMINISTRATOR); //, null, false, false);
	//ADD ms: 05.05.2011 here we need the language joomla core
	$lang->load('', JPATH_ADMINISTRATOR, null, false, false);
	
	$controller->addModelPath( JPATH_COMPONENT_ADMINISTRATOR.DS.'models' );
	//$view->addHelperPath($path)
	$controller->addViewPath( JPATH_COMPONENT_SITE.DS.'views' );
	$controller->addViewPath( JPATH_COMPONENT_ADMINISTRATOR.DS.'views' );
	// Execute the task.
	$controller->execute(JRequest::getCmd('task'));
	$controller->redirect();

?>