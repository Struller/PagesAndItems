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
jimport( 'joomla.application.component.controller' );

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controller.php');
/**
 * @package		PagesAndItems
*/
class PagesAndItemsControllerPluginItemtype extends PagesAndItemsController
{
	var $pluginController = null;
	
	function __construct( $config = array())
	{
		parent::__construct($config);
		$plugin = JRequest::getVar('plugin', '');
		if(file_exists($this->pathPluginsItemtypes.'/'.$plugin.'/controllers/'.$plugin.'.php'))
		{
			require_once($this->pathPluginsItemtypes.'/'.$plugin.'/controllers/'.$plugin.'.php');
			$className = 'PagesAndItemsController'.$plugin;
			$this->pluginController = new $className();
		}
		//$doExecute = JRequest::getVar('doExecute', null);
		
		
		
		/*
			COMMENT cmd is for subtask so here we can run task from itemtype
			eg. pi_fish.something
			or
			eg. pi_fish.display
			is cmd only eg. pi_fish the display task is run
		*/
		$cmd = JRequest::getVar('sub_task', null);
		if($cmd)
		{
			//TODO 
			//cmd eg. pi_fish.translate
			if (strpos($cmd, '.') != false)
			{
				// We have a defined controller/task pair -- lets split them out
				list($controllerName, $task) = explode('.', $cmd);
				if($controllerName != $plugin)
				{
					//not the plugin controller also we need to create an new controller only the controllers in pi
					//TODO make search in 
					//or message
				}
				else
				{
					//the plugin controller we can execute
					$this->registerTask( $task,  'doExecute' );
				}
			}
			else
			{
				// display need an doExecute?
				$this->registerTask( $cmd,  'doExecute' );
				
			}
			
		}
		/*
		elseif($doExecute)
		{
			//work
			$this->registerTask( $doExecute,  'doExecute' );
		}
		*/
	}

	function display($tpl = null)
	{
		//work :-)
		$this->pluginController->display($tpl);
		//parent::display($tpl);
	}

	function doExecute()
	{
		//work :-)
		$task = JRequest::getVar('doExecute', null);
		$this->pluginController->execute($task);
		// Redirect if set by the controller
		$this->pluginController->redirect();
	}

	
	function doTask()
	{
		$plugin = JRequest::getVar('plugin', '');
		
		//$class_pi->set_title();
		//require_once($this->pathPluginsFieldtypes.'/'.$plugin.'/'.$plugin.'_admin.php');
		/*
			alternative solution we run getInstance() see admin.pages_and_items.php
			but first we look at see //COMMENT cmd
			so if we have in the sub_task if($controllerName != $plugin) we can run another controller
			
			$controller= GinkgoController::getInstance('Ginkgo',array('base_path' => JPATH_COMPONENT_ADMINISTRATOR));

			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.'controllerJ15.php');
			
		*/
		/*
			BEGIN TEST
		*/
		$version = new JVersion();
		// Execute the task.
		if($version->getShortVersion() < '1.6')
		{
			//J15
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.'controllerJ15.php');
		}
		else
		{
		
		}
		
		$newConfig = null; //array('base_path' => $this->pathPluginsFieldtypes.DS.$plugin.DS
		$name = 'PagesAndItems';
		$cmd = JRequest::getVar('sub_task', null);
		if($cmd)
		{
			//TODO 
			//cmd eg. pi_fish.translate ore translate
			if (strpos($cmd, '.') != false)
			{
				// We have a defined controller/task pair -- lets split them out
				list($controllerName, $task) = explode('.', $cmd);
				JRequest::setVar('task', $task);
				if($controllerName != $plugin)
				{
					//not the plugin controller als we need to create an new controller only the controllers in pi
					//TODO
					/*
					if($version->getShortVersion() < '1.6')
					{
						$controller = controllerJ15::getInstance('PagesAndItems')); 
					}
					else
					{
						$controller= JController::getInstance('PagesAndItems'); 
					}
					*/
				}
				else
				{
					//the plugin controller we can execute
					//$this->registerTask( $task,  'doExecute' );
					$newConfig = array('base_path' => $this->pathPluginsFieldtypes.DS.$plugin.DS,'name' => 'pi_fish');
					/*
					if($version->getShortVersion() < '1.6')
					{
						$controller = controllerJ15::getInstance('PagesAndItems')); 
					}
					else
					{
						$controller= JController::getInstance('PagesAndItems'); 
					}
					*/
				}
			}
			else
			{
				//the plugin controller task
				//$this->registerTask( $cmd,  'doExecute' );
				//if($controllerName != $plugin)
				JRequest::setVar('task', $cmd);
				$newConfig = array('base_path' => $this->pathPluginsFieldtypes.DS.$plugin.DS,'name' => 'pi_fish');
				/*
				if($version->getShortVersion() < '1.6')
				{
					$controller = controllerJ15::getInstance($name,array('base_path' => $this->pathPluginsFieldtypes.DS.$plugin.DS)); 
				}
				else
				{
					$controller= JController::getInstance('PagesAndItems'); 
				}
				*/
				
			}
			
		}
		else
		{
			//the plugin controller standard task
			$newConfig = array('base_path' => $this->pathPluginsFieldtypes.DS.$plugin.DS,'name' => 'pi_fish');
			JRequest::setVar('task', $cmd);
		}
		
		if($version->getShortVersion() < '1.6')
		{
			$controller = controllerJ15::getInstance($name,$newConfig); //array('base_path' => $this->pathPluginsFieldtypes.DS.$plugin.DS)); 
		}
		else
		{
			$controller= JController::getInstance($name,$newConfig); //'PagesAndItems'); 
		}
		$controller->execute(JRequest::getCmd('task'));
		$controller->redirect();
	}
	

}
