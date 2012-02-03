<?php
/**
* @version		2.1.5
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controller.php');
/**
 * @package		PagesAndItems
*/
class PagesAndItemsControllerExtension extends JController //PagesAndItemsController
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
		$extensionName = JRequest::getVar('extensionName','' ); //is the extensionName //JRequest::getVar('extensionName',JRequest::getVar('extension', '' ));
		if(JRequest::getVar('extension') != '')
		{
			//TODO error warning
			$extensionName = JRequest::getVar('extension','' );
		}
		
		$extensionFolder = JRequest::getVar('extensionFolder', '');
		$extensionType = JRequest::getVar('extensionType', '');
		$extensionController = JRequest::getVar('extensionController', '');
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

		if (strpos($extensionName, '.') != false)
		{
			// We have a defined controller/task pair -- lets split them out
			list($extensionName,$controllerName) = explode('.', $extensionName);
			$path = realpath(dirname(__FILE__).'/../extensions'.DS.$folder.DS.$extensionName.DS.'controllers'.DS.$controllerName.'.php');
			$controllerName = ucfirst($controllerName);
		}
		else
		{
			$path = realpath(dirname(__FILE__).'/../extensions'.DS.$folder.DS.$extensionName.DS.'controller.php');
			$controllerName = '';

		}
		if($extensionController != '')
		{
			$controllerName = $extensionController;
			$path = realpath(dirname(__FILE__).'/../extensions'.DS.$folder.DS.$extensionName.DS.'controllers'.DS.$controllerName.'.php');
			$controllerName = ucfirst($controllerName);
		}
		
		//TODO eliminate extensionSubTask extension_task sub_task
		$cmd = JRequest::getVar('extensionTask', JRequest::getVar('extensionSubTask',JRequest::getVar('extension_task',JRequest::getVar('sub_task',null))));
		if($cmd)
		{
			if (strpos($cmd, '.') != false)
			{
				list($controllerName,$task) = explode('.', $cmd);
				$path = realpath(dirname(__FILE__).'/../extensions'.DS.$folder.DS.$extensionName.DS.'controllers'.DS.$controllerName.'.php');
				$controllerName = ucfirst($controllerName);
			}
			else
			{
				$path = realpath(dirname(__FILE__).'/../extensions'.DS.$folder.DS.$extensionName.DS.'controller.php');
			}
		}
		else
		{
			$path = realpath(dirname(__FILE__).'/../extensions'.DS.$folder.DS.$extensionName.DS.'controller.php');
		}
		jimport('joomla.filesystem.file');
		if(JFile::exists($path))
		{
			PagesAndItemsHelper::loadExtensionLanguage($extensionName,$extensionType,$extensionFolder);
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
			}
			$extension_name = ucfirst($extensionName);

				$className = 'PagesAndItemsControllerExtension'.$prefix.$extension_name.$controllerName;

			$extensionController = new $className();
			$this->extensionController = $extensionController;
			$extensionTask = null;
			if($cmd)
			{
				if (strpos($cmd, '.') != false)
				{
					// We have a defined controller/task pair -- lets split them out
					list($controllerName, $extensionTask) = explode('.', $cmd);
					JRequest::setVar('extensionTask', $extensionTask);
				}
				else
				{
					$extensionTask = $cmd;
					JRequest::setVar('extensionTask', $extensionTask);
				}
			}
		}
		else
		{
			$app = JFactory::getApplication();
			$app->enqueueMessage('no extension controller. extensionType: '.$extensionType.($extensionFolder ? ', extensionFolder: '.$extensionFolder:'').', extensionName: '.$extensionName, 'warning');
			//$this->setMessage(JText::_('COM_MENUS_SAVE_SUCCESS'));
			//TODO enquee Message
			/*
			controller cant load:
			$extensionName,$extensionType,$extensionFolder
			*/
		}
	}

	function display($tpl = null)
	{
		
		$extensionTask = JRequest::getVar('extensionTask',null);
		$extensionName = JRequest::getVar('extensionName',''); //is the extensionName
		if(JRequest::getVar('extension') != '')
		{
			//TODO error warning
			$extensionName = JRequest::getVar('extension','' );
		}

		$extensionFolder = JRequest::getVar('extensionFolder', '');
		$extensionType = JRequest::getVar('extensionType', '');

		if(JRequest::getVar('view', 'extension') != 'managers')
		{
			$lang		= JFactory::getLanguage();
			// Load extension-local file.
			$lang->load('com_pagesanditems.sys', JPATH_ADMINISTRATOR, null, false, false)
			||	$lang->load('com_pagesanditems.sys', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);

			JRequest::setVar('view', JRequest::getVar('view', 'extension'));
			$file = realpath(dirname(__FILE__).DS.'..'.DS.'views'.DS.'extension'.DS.'tmpl'.DS.$extensionType.'.php');
			if(file_exists($file))
			{
				JRequest::setVar( 'layout', $extensionType );
			}
			else
			{
				//?????
				JRequest::setVar( 'layout', 'default' );
			}
		}
		parent::display($tpl);

	}

	function doExecute()
	{
		$task = JRequest::getVar('extensionTask', null);
		if($this->extensionController)
		{
			$lang		= JFactory::getLanguage();
			// Load extension-local file.
			$lang->load('com_pagesanditems.sys', JPATH_ADMINISTRATOR, null, false, false)
			||	$lang->load('com_pagesanditems.sys', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
			$this->extensionController->execute($task);
			// Redirect if set by the controller
			$this->extensionController->redirect();
		}
	}

}