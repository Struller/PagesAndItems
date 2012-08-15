<?php
/**
* @version		2.1.6
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
jimport( 'joomla.application.component.controller' );
//jimport('joomla.filesystem.file');
//jimport('joomla.filesystem.folder');
/**
 * @package		PagesAndItems
*/

class controllerJ15 extends JController
{
	function __construct( $config = array())
	{
		parent::__construct($config);
	}

	/*
	here we make the function from Joomla 1.6
	*/
	/**
	 * Method to get a singleton controller instance.
	 * from Joomla 1.6
	 *
	 * @param	string	$name		The prefix for the controller.
	 * @param	array		$config	An array of optional constructor options.
	 * @return	mixed		JController derivative class or JException on error.
	 */
	function getInstance($prefix, $config = array())
	{
		static $instance;
		if (!empty($instance))
		{
			return $instance;
		}
		// Get the environment configuration.
		$basePath	= array_key_exists('base_path', $config) ? $config['base_path'] : JPATH_COMPONENT_ADMINISTRATOR;
		$protocol	= JRequest::getWord('protocol');
		$command	= JRequest::getCmd('task', 'display');
		// Check for a controller.task command.
		if (strpos($command, '.') !== false)
		{
			// Explode the controller.task command.
			list($type, $task) = explode('.', $command);
			// Define the controller filename and path.
			$file	= controllerJ15::createFileName('controller', array('name' => $type, 'protocol' => $protocol));
			$path	= $basePath.DS.'controllers'.DS.$file;

			// Reset the task without the contoller context.
			JRequest::setVar('task', $task);
		}
		else
		{
			// Base controller.
			$type	= null;
			$task	= $command;

			// Define the controller filename and path.
			$file	= controllerJ15::createFileName('controller', array('name' => 'controller', 'protocol' => $protocol));
			$path	= $basePath.DS.$file;
		}
		// Get the controller class name.
		$class = ucfirst($prefix).'Controller'.ucfirst($type);
		// Include the class if not present.
		if (!class_exists($class))
		{
			// If the controller file path exists, include it.
			if (file_exists($path))
			{
				require_once $path;
			}
			else
			{
				//throw new JException(JText::sprintf('INVALID CONTROLLER', $type), 500, E_ERROR, $type, true);
				JError::raiseError(500, 'Invalid Controller: '.$type);
			}
		}

		// Instantiate the class.
		if (class_exists($class))
		{
			$instance = new $class($config);
		}
		else
		{
			//throw new JException(JText::sprintf('INVALID CONTROLLER CLASS', $class), 500, E_ERROR, $class, true);
			JError::raiseError(500, 'Invalid Controller CLASS: '.$class);
		}

		return $instance;
	}

	/**
	 * Create the filename for a resource.
	 *
	 * @param	string	The resource type to create the filename for.
	 * @param	array	An associative array of filename information. Optional.
	 * @return	string	The filename.
	 * @since	1.5
	 */
	function createFileName($type, $parts = array())
	{
		$filename = '';
		switch ($type)
		{
			case 'controller':
				if (!empty($parts['protocol']))
				{
					$parts['protocol'] = '.'.$parts['protocol'];
				}

				$filename = strtolower($parts['name']).$parts['protocol'].'.php';
				break;

			case 'view':
				if (!empty($parts['type']))
				{
					$parts['type'] = '.'.$parts['type'];
				}
					$filename = strtolower($parts['name']).DS.'view'.$parts['type'].'.php';
			break;
		}
		return $filename;
	}
}
