<?php
/**
* @version		2.1.3
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


class PagesAndItemsControllerExtensionHtmlPage_itemsArchive extends PagesAndItemsController
{
	function __construct($config = array())
	{
		parent::__construct($config);
		//$this->registerTask( 'select', 'display' );
	}

	function addToArchive()
	{

		$modelBase = &$this->getModel('Base','PagesAndItemsModel');
		$app = JFactory::getApplication();
		$itemCid = JRequest::getVar('itemCid',array());
		JArrayHelper::toInteger($itemCid, array(0));
		$total		= count( $itemCid );
		//JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
		$row =& JTable::getInstance('content');
		//$conditions = array();
		// update ordering values
		for ( $i=0; $i < $total; $i++ )
		{
			$row->load( (int) $itemCid[$i] );
			$app->enqueueMessage('TEST if work as task addToArchive Item Title: '.$row->title);//.' ;-)');
			//$message[] = 'TEST if work as task addToArchive ;-): '.$row->title;

		}
		$sub_task = JRequest::getVar('sub_task', null, 'edit', 'cmd');
		$menutype = JRequest::getVar('menutype', '');
		$pageType = JRequest::getVar('pageType', '');
		$pageId = JRequest::getVar('pageId', '');
		//$url = PagesanditemsHelper::toogleViewPageCategories('index.php?option=com_pagesanditems&view=page&sub_task='.$sub_task.'&pageId='.$pageId.'&menutype='.$menutype);
		$url = 'index.php?option=com_pagesanditems&view=page&sub_task='.$sub_task.'&pageId='.$pageId.'&menutype='.$menutype;

		$message = '';; //(count($message) ? ''.implode(', ', $message) : '');
		$modelBase->redirect_to_url($url, $message);
	}

	/**
	 * Display the view
	 */
	function display($tpl = null)
	{
		$modelBase = &$this->getModel('Base','PagesAndItemsModel');
		$vName = strtolower(JRequest::getCmd('view', 'archive'));
		switch ($vName)
		{
			case 'archive':
				$vLayout = 'default';
				require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models'.DS.'item.php');
				require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'menutypes.php');
				$modelName[] = 'Page';

				if($modelBase->joomlaVersion < '1.6')
				{
					$helperName[] = 'helper';
				}
				else
				{
					$helperName[] = 'menus';
				}
				$helperPath[] = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'helpers';

				$this->addModelPath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models' );
			break;
		}
		$document = &JFactory::getDocument();
		$vType = $document->getType();

		$this->addViewPath(dirname(__FILE__).'/views');

		// Get/Create the view
		$view = &$this->getView( $vName, $vType);

		// Get/Create the model
		if ($model = &$this->getModel($mName))
		{
			// Push the model into the view (as default)
			$view->setModel($model, true);
		}
		//
		$view->addTemplatePath(dirname(__FILE__).'/views'.DS.$vName.DS.'tmpl');
		// Set the layout
		$view->setLayout($vLayout);
		// Display the view
		$view->assignRef('controller', $this);


		if(is_array($modelName))
		{
			for($n = 0; $n < count($modelName); $n++)
			{
				if ($model = &$this->getModel($modelName[$n],'PagesAndItemsModel'))
				{
					// Push the model into the view (as default)
					$view->setModel($model, false);
				}
			}
		}

		if ($model = &$this->getModel('PagesAndItems','PagesAndItemsModel'))
		{
			// Push the model into the view (as default)
			$view->setModel($model, true);
		}

		parent::display($tpl);
		//$view->display();
	}
}
