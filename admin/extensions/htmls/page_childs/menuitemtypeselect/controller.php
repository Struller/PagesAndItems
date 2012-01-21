<?php
/**
* @version		2.1.2
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
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


class PagesAndItemsControllerExtensionHtmlPage_childsMenuitemtypeselect extends PagesAndItemsController //JController
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
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		$modelName = array();
		$vName = strtolower(JRequest::getCmd('view', 'menuitemtypeselect'));
		switch ($vName)
		{
			case 'menuitemtypeselect':
				$vLayout = 'default'; //JRequest::getCmd( 'layout', 'default' );
				//$modelName[] = 'Page_items'; //is replace $mName = 'Page_items';
				//$modelName[] = 'MenusModelItem'; //is replace $mNameTwo = 'MenusModelItem';
				//$modelName[] = 'Menutypes';
				require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models'.DS.'item.php');
				require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'menutypes.php');
				$modelName[] = 'Page';
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
		//$view->assignRef('controller', $this);


		if(is_array($modelName))
		{
			for($n = 0; $n < count($modelName); $n++)
			{
				if ($model[$n] = &$this->getModel($modelName[$n],'PagesAndItemsModel'))
				{
					// Push the model into the view (as default)
					$view->setModel($model[$n], false);
				}
				/*
				elseif($model = &$this->getModel($modelName[$n],'PagesAndItemsModel'))
				{
					// Push the model into the view (as default)
					$view->setModel($model, false);
				}
				*/
			}
		}

		if ($model = &$this->getModel('Base','PagesAndItemsModel'))
		{
			// Push the model into the view (as default)
			$view->setModel($model, true);
		}

		parent::display($tpl);
		//$view->display();
	}
}
