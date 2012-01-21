<?php
/**
* @version		2.0.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2011 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// No direct access.
defined('_JEXEC') or die;

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controller.php');
/**
 * @package		PagesAndItems
*/
class PagesAndItemsControllerExtension extends PagesAndItemsController
{
	var $extensionController = null;

	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 */
	function __construct( $config = array())
	{
		parent::__construct($config);
		$extension = JRequest::getVar('extension', ''); //is the extensionName
		$extensionFolder = JRequest::getVar('extensionFolder', '');
		$extensionType = JRequest::getVar('extensionType', '');
		$extensionController = JRequest::getVar('extensionController', '');
		//dump($extension);
		if($extensionFolder && $extensionFolder != '')
		{
			$extension_folder = str_replace('/',DS,$extensionFolder);
			$folder = $extensionType.'s'.DS.$extension_folder;//.DS;
			$prefix = str_replace('/',DS,$extensionFolder);
		}
		else
		{
			$folder = $extensionType.'s';
		}

		if (strpos($extension, '.') != false)
		{
			// We have a defined controller/task pair -- lets split them out
			list($extension,$controllerName) = explode('.', $extension);
			$path = realpath(dirname(__FILE__).'/../extensions'.DS.$folder.DS.$extension.DS.'controllers'.DS.$controllerName.'.php');
			$controllerName = ucfirst($controllerName);
		}
		else
		{
			//$extensionTask = $cmd;
			$path = realpath(dirname(__FILE__).'/../extensions'.DS.$folder.DS.$extension.DS.'controller.php');
			//dump(dirname(__FILE__).'/../extensions'.DS.$folder.DS.$extension.DS.'controller.php');
			//dump($extension);
			//$controllerName = $extension
			$controllerName = '';
			
		}
		if($extensionController != '')
		{
			//dump($extensionController);
			$controllerName = $extensionController;
			$path = realpath(dirname(__FILE__).'/../extensions'.DS.$folder.DS.$extension.DS.'controllers'.DS.$controllerName.'.php');
			$controllerName = ucfirst($controllerName);
		}
		$cmd = JRequest::getVar('extensionTask', JRequest::getVar('extension_sub_task',JRequest::getVar('extension_task',JRequest::getVar('sub_task',null))));
		//dump($cmd);
		if($cmd)
		{
			if (strpos($cmd, '.') != false)
			{
				list($controllerName,$task) = explode('.', $cmd);
				$path = realpath(dirname(__FILE__).'/../extensions'.DS.$folder.DS.$extension.DS.'controllers'.DS.$controllerName.'.php');
				$controllerName = ucfirst($controllerName);
			}
			else
			{
				$path = realpath(dirname(__FILE__).'/../extensions'.DS.$folder.DS.$extension.DS.'controller.php');
			}
		}
		else
		{
			$path = realpath(dirname(__FILE__).'/../extensions'.DS.$folder.DS.$extension.DS.'controller.php');
		}
		
		//$path = realpath(dirname(__FILE__).'/../extensions'.DS.$folder.DS.$extension.DS.'controllers'.DS.$extension.'.php');
		/*
		dump($path);
		dump($cmd);
		*/
		//dump($controllerName);
		jimport('joomla.filesystem.file');
		if(JFile::exists($path))
		{
		PagesAndItemsHelper::loadExtensionLanguage($extension,$extensionType,$extensionFolder);
			//dump('exist');
			require_once($path);
			if($extensionFolder && $extensionFolder != '')
			{
				$extension_folders = explode('/',$extensionFolder);
				if(count($extension_folders))
				{
					$folders = array();
					for($n = 0; $n < (count($extension_folders)); $n++)
					{
						$folders[] = ucfirst($extension_folders[$n]);
					}
					$extension_folder = implode($folders);
				}
				else
				{
					$extension_folder = ucfirst($extensionFolder);
				}
				$prefix = ucfirst($extensionType).$extension_folder;
			}
			else
			{
				$prefix = ucfirst($extensionType);
				//$folder = $extension->type.DS.$extension_folder.DS;
			}
			$extension_name = ucfirst($extension);
			
			//$className = 'PagesAndItemsControllerExtension'.$prefix.$extension_name;
			$className = 'PagesAndItemsControllerExtension'.$prefix.$extension_name.$controllerName;

			$extensionController = new $className(); //PagesAndItemsControllerPI_Fish();
			$this->extensionController = $extensionController;
			$extensionTask = null;
			//$cmd = JRequest::getVar('extension_sub_task',JRequest::getVar('extension_task',JRequest::getVar('sub_task',null)));
			if($cmd)
			{
				//TODO 
				//cmd eg. pi_fish.translate
				if (strpos($cmd, '.') != false)
				{
					// We have a defined controller/task pair -- lets split them out
					list($controllerName, $extensionTask) = explode('.', $cmd);
					//$this->registerTask( $extensionTask,  'doTheExecute' );
					//JRequest::setVar('doTheExecute', $extensionTask);
					JRequest::setVar('extensionTask', $extensionTask);
				}
				else
				{
					//display need doExecute? NO
					$extensionTask = $cmd;
					JRequest::setVar('extensionTask', $extensionTask);
					//$this->registerTask( $cmd, 'doTheExecute' );
					//JRequest::setVar('doTheExecute', $cmd);
				}
			}
			//dump($cmd);
			/*
			if(!$extensionTask)
			{
				$view = JRequest::getVar('view', null);
				//$extensionTask = 'display';
				
				
				if($extensionType == 'itemtype')
				{
					JRequest::setVar('extensionTask', 'display');
					JRequest::setVar('view', 'extension');
				}
				
			}
			
			if($extensionTask && $extensionTask == 'display')
			{
				//???
				JRequest::setVar('extensionTask', 'display');
			}

			//work :-) without doExecute()
			//but we will handle itemtype inanother way if we need display
			//&& $extensionType != 'itemtype' && $extensionTask != 'display'
			
			$task = JRequest::getVar('task', ''); //is the extensionName
			$display = false;
			if (strpos($task, '.') !== false && $extensionTask != 'display')
			{
				// We have a defined controller/task pair -- lets split them out
				list($controllerName, $task) = explode('.', $task);
				if($task == 'display')
				{
					$display = true;
					JRequest::setVar('view', 'extension');
				}
			}
			elseif($task == 'display'  && $extensionTask != 'display')
			{
				$display = true;
				JRequest::setVar('view', 'extension');
			}
			if($extensionController && $extensionTask && (!$display || $extensionTask == 'display')) //($extensionTask == 'display' || !$display ))
			{
				//this will execute the task also display?
				//$extensionController->execute($extensionTask);
				// Redirect if set by the controller
				//$extensionController->redirect();
			}
			*/
		}
	}

	function display($tpl = null)
	{
		$extensionTask = JRequest::getVar('extensionTask',null);
		$extension = JRequest::getVar('extension', ''); //is the extensionName
		$extensionFolder = JRequest::getVar('extensionFolder', '');
		$extensionType = JRequest::getVar('extensionType', '');
		
		if(JRequest::getVar('view', 'extension') != 'managers')
		{
			JRequest::setVar('view', JRequest::getVar('view', 'extension'));
			$file = realpath(dirname(__FILE__).DS.'..'.DS.'views'.DS.'extension'.DS.'tmpl'.DS.$extensionType.'.php');
			if(file_exists($file))
			{
				JRequest::setVar( 'layout', $extensionType );
			}
			else
			{
				JRequest::setVar( 'layout', 'default' );
			}
		}
		parent::display($tpl);

	}

	function doExecute()
	{
		//work :-)
		//$task = $this->getTask();
		//$task = JRequest::getVar('doTheExecute', null);
		$task = JRequest::getVar('extensionTask', null);
		//var_dump($this->extensionController);
		//var_dump($task);
		if($this->extensionController)
		{
			$this->extensionController->execute($task);
			// Redirect if set by the controller
			$this->extensionController->redirect();
		}
	}
	
}