<?php
/**
* @version		1.6.0
* @package		PagesAndItems
* @copyright	Copyright (C) 2006-2010 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );
jimport('joomla.environment.response');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'controller.php');
/**
 *menuitemtypeselect Controller
 *
 */


class PagesAndItemsControllerExtensionManagerExtensions extends PagesAndItemsController //JController
{
	function __construct($config = array())
	{
		parent::__construct($config);
		//$this->registerTask( 'select', 'display' );
	}
	
	/**
	 * Display the view
	 */
	function display($tpl = null)
	{
		//$modelBase = &$this->getModel('Base','PagesAndItemsModel');
		$modelName = array();
		$vName = strtolower(JRequest::getCmd('view', 'piextensions'));
		
		switch ($vName)
		{
			case 'manage':
				$vLayout = 'default';
				jimport( 'joomla.application.component.model' );
				$path = realpath(dirname(__FILE__).DS.'models');
				JModel::addIncludePath($path);
				//$modelName[] = 'manage';
				$modelName[] = 'manage';
			break;
			case 'uninstall':
				$vLayout = 'default';
				jimport( 'joomla.application.component.model' );
				$path = realpath(dirname(__FILE__).DS.'models');
				JModel::addIncludePath($path);
				//$modelName[] = 'manage';
				$modelName[] = 'uninstall';
			break;
			case 'piextensions':
				$vLayout = 'default';
				jimport( 'joomla.application.component.model' );
				$path = realpath(dirname(__FILE__).DS.'models');
				JModel::addIncludePath($path);
				$modelName[] = 'piextensions';
				//$modelName[] = 'ExtensionManageExtensionsextensions';
			break;

			case 'piextension':
				$vLayout = 'edit';
				jimport( 'joomla.application.component.model' );
				$path = realpath(dirname(__FILE__).DS.'models');
				JModel::addIncludePath($path);
				$modelName[] = 'piextension';
				//$modelName[] = 'ExtensionManageExtensionsextension';
			break;
			
			case 'install':
				$vLayout = 'default';
				jimport( 'joomla.application.component.model' );
				$path = realpath(dirname(__FILE__).DS.'models');
				JModel::addIncludePath($path);
				$modelName[] = 'install';
				//$modelName[] = 'ExtensionManageExtensionsInstall';
			break;
		}
		$document = &JFactory::getDocument();
		$vType = $document->getType();
		
		$this->addViewPath(realpath(dirname(__FILE__).'/views'));
		
		// Get/Create the view
		$view = &$this->getView( $vName, $vType);
		
		$view->addTemplatePath(realpath(dirname(__FILE__).'/views'.DS.$vName.DS.'tmpl'));
		// Set the layout
		$view->setLayout($vLayout);
		// Display the view
		if(is_array($modelName))
		{
			for($mn = 0; $mn < count($modelName); $mn++)
			{
				//if($model[$mn] = &$this->getModel($modelName[$mn],'PagesAndItemsModel'))
				//if($model[$mn] = &$this->getModel($modelName[$mn],'PagesAndItemsModelExtensionManageExtensions'))
				
				//if($model[$mn] = &$this->getModel($modelName[$mn],'PagesAndItemsModel'))
				//if($model[$mn] = &$this->getModel($modelName[$mn],'PagesAndItemsModelExtensionManageExtensions'))
				//if($model[$mn] = &$this->getModel($modelName[$mn],'PagesAndItemsModel'))
				if($model[$mn] = $this->getModel($modelName[$mn],'PagesAndItemsModel'))
				{
					// Push the model into the view (as default)
					$view->setModel($model[$mn], true);
				}
				
			}
		}
		//dump($view);
		parent::display($tpl);
		//$view->display();
	}
}
