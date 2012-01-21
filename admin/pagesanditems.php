<?php
/**
* @version		2.0.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2011 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('_JEXEC') or die;

//get helper
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'pagesanditems.php');

// Include dependancies
jimport('joomla.application.component.controller');
	$version = new JVersion();
	
	$config = array('view_path'=>JPATH_COMPONENT_ADMINISTRATOR.DS.'views');
	if($version->getShortVersion() < '1.6')
	{
		/*
		 * Joomla 1.5
		*/
		#
		// sample file #1
		#
		/**
		#
		 * Dummy include value, to demonstrate the parsing power of phpDocumentor
		#
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
	
	$controller->addViewPath( JPATH_COMPONENT_SITE.DS.'views' );
	// Execute the task.
	$controller->execute(JRequest::getCmd('task'));
	$controller->redirect();
	
	


?>