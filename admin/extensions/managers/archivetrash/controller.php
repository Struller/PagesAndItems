<?php
/**
* @version		2.1.5
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


class PagesAndItemsControllerExtensionManagerArchiveTrash extends PagesAndItemsController //JController
{
	//var $helper = null;
	function __construct($config = array())
	{
		parent::__construct($config);
		//$this->registerTask( 'select', 'display' );
		//$this->helper = new PagesAndItemsHelper();
	}

	/**
	 * Display the view
	 */
	function display($tpl = null)
	{
		//$modelBase = &$this->getModel('Base','PagesAndItemsModel');
		$modelName = array();
		$vName = strtolower(JRequest::getCmd('view', 'archivetrash'));
		switch ($vName)
		{
			case 'archivetrash':
				$vLayout = 'default';
				jimport( 'joomla.application.component.model' );
				$path = realpath(dirname(__FILE__).DS.'models');
				JModel::addIncludePath($path);
				require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'menutypes.php');
				//$modelName[] = 'managerarchivetrash';
				$modelName[] = 'archivetrash';
				if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
				{
					$helperName[] = 'helper';
				}
				else
				{
					$helperName[] = 'menus';
				}
				$helperPath[] = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'helpers';
				$this->addModelPath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models' );
				
				
				//$modelName[] = 'Page';
				//$modelName[] = 'Base';
				/*
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
				*/
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
				if($model[$mn] = &$this->getModel($modelName[$mn],'PagesAndItemsModel'))
				{
					// Push the model into the view (as default)
					$view->setModel($model[$mn], false);
				}
			}
		}
		if(is_array($helperPath))
		{
			for($hp = 0; $hp < count($helperPath); $hp++)
			{
				$view->addHelperPath($helperPath[$hp]);
			}
		}
		if(is_array($helperName))
		{
			for($hn = 0; $hn < count($helperPath); $hn++)
			{
				$view->loadHelper($helperName[$hn]);//make sure we have not use helpers/helper.php in pi??
			}
		}
		parent::display($tpl);
		//$view->display();
	}

	function archive()
	{

		$app = &JFactory::getApplication();
		$link = 'index.php?option=com_pagesanditems';
		$link .= '&task=manager.doExecute'; //display';
		$link .= '&extensionName=archivetrash'; //the name
		$link .= '&extensionType=manager'; //the type
		$link .= '&extensionFolder='; //the folder
		$link .= '&extensionTask=display';
		$link .= '&view=archivetrash'; //
		$table_id = JRequest::getVar('table_id',0);
		$link .= '&table_id='.$table_id; //


		jimport( 'joomla.application.component.model' );
		$path = realpath(dirname(__FILE__).DS.'models');
		JModel::addIncludePath($path);

		$model = &$this->getModel('archivetrash');
		$tables = $model->getTables();

		$table = $tables[$table_id];
		$ids	= JRequest::getVar('cid', array(), '', 'array');
		$app->enqueueMessage(JText::_('COM_PAGESANDITEMS_ITEMS_ARCHIVED'));
		/*
		if($table->tableName == 'menu')
		{
			foreach($ids as $id)
			{
				//$helper->archivePage($id);
				$app->enqueueMessage('TEST if work as task Archive Item Id: '.$id);//.' ;-)');
			}

		}
		else
		*/
		if($table->tableName == 'content')
		{
			foreach($ids as $id)
			{

				//if(PagesAndItemsHelper::item_state($id,2) )
				//if(
				//$this->helper->item_state($id,'2');// )
				PagesAndItemsHelper::item_state($id,2);
				$app->enqueueMessage('Id: '.$id);
			}
		}
		else
		{
			if(isset($table->extensionType) && isset($table->extension))
			{
				$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
				require_once($path.DS.'includes'.DS.'extensions'.DS.$table->extensionType.'helper.php');

				//$typeName = 'Extension'.ucfirst($table->extensionType).'Helper';
				//$typeName::importExtension(null, $table->extension,true,null,false);
				switch(strtolower($table->extensionType))
				{
					case 'manager':
						ExtensionManagerHelper::importExtension(null, $table->extension,true,null,false);
					break;
					
					case 'itemtype':
						ExtensionItemtypeHelper::importExtension(null, $table->extension,true,null,false);
					break;
					
				}
				$dispatcher = &JDispatcher::getInstance();
				foreach($ids as $id)
				{
					$dispatcher->trigger('onExtensionArchiveTrashItemArchive', array ( $id));
					//let the extension do the message
				}
			}
		}

		$app->redirect($link);
	}

	function unpublish()
	{

		$app = &JFactory::getApplication();

		$link = 'index.php?option=com_pagesanditems';
		$link .= '&task=manager.doExecute'; //display';
		$link .= '&extensionName=archivetrash'; //the name
		$link .= '&extensionType=manager'; //the type
		$link .= '&extensionFolder='; //the folder
		$link .= '&extensionTask=display';
		$link .= '&view=archivetrash'; //
		$table_id = JRequest::getVar('table_id',0);
		$link .= '&table_id='.$table_id; //

		jimport( 'joomla.application.component.model' );
		$path = realpath(dirname(__FILE__).DS.'models');
		JModel::addIncludePath($path);

		$model = &$this->getModel('archivetrash');
		$tables = $model->getTables();

		$table = $tables[$table_id];
		$ids	= JRequest::getVar('cid', array(), '', 'array');

		$app->enqueueMessage(JText::_('COM_PAGESANDITEMS_ITEMS_UNPUBLISHED'));

		if($table->tableName == 'menu')
		{
			foreach($ids as $id)
			{

				//if(
				//$this->helper->page_state($id,'0'); // )
				PagesAndItemsHelper::page_state($id,'0'); // )
				$app->enqueueMessage('Id: '.$id);
				/*
				{
					$app->enqueueMessage('Id: '.$id);
				}
				*/
			}
		}
		elseif($table->tableName == 'content')
		{
			foreach($ids as $id)
			{
				//if(
				PagesAndItemsHelper::item_state($id,'0');
				// )
				$app->enqueueMessage('Id: '.$id);//.' ;-)');
			}
		}
		else
		{
			if(isset($table->extensionType) && isset($table->extension))
			{
				$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
				require_once($path.DS.'includes'.DS.'extensions'.DS.$table->extensionType.'helper.php');

				//$typeName = 'Extension'.ucfirst($table->extensionType).'Helper';
				//$typeName::importExtension(null, $table->extension,true,null,false);
				switch(strtolower($table->extensionType))
				{
					case 'manager':
						ExtensionManagerHelper::importExtension(null, $table->extension,true,null,false);
					break;
					
					case 'itemtype':
						ExtensionItemtypeHelper::importExtension(null, $table->extension,true,null,false);
					break;
					
				}
				$dispatcher = &JDispatcher::getInstance();

				foreach($ids as $id)
				{
					$dispatcher->trigger('onExtensionArchiveTrashItemUnpublish', array ( $id));
					//let the extension do the message
				}
			}
		}
		$app->redirect($link);
	}

	function trash()
	{

		$app = &JFactory::getApplication();

		$link = 'index.php?option=com_pagesanditems';
		$link .= '&task=manager.doExecute'; //display';
		$link .= '&extensionName=archivetrash'; //the name
		$link .= '&extensionType=manager'; //the type
		$link .= '&extensionFolder='; //the folder
		$link .= '&extensionTask=display';
		$link .= '&view=archivetrash'; //
		$table_id = JRequest::getVar('table_id',0);
		$link .= '&table_id='.$table_id; //

		jimport( 'joomla.application.component.model' );
		$path = realpath(dirname(__FILE__).DS.'models');
		JModel::addIncludePath($path);

		$model = &$this->getModel('archivetrash');
		$tables = $model->getTables();

		$table = $tables[$table_id];
		$ids	= JRequest::getVar('cid', array(), '', 'array');

		$app->enqueueMessage(JText::_('COM_PAGESANDITEMS_ITEMS_TRASHED'));

		if($table->tableName == 'menu')
		{
			foreach($ids as $id)
			{
				//if(
				PagesAndItemsHelper::page_state($id,'-2');
				// )
				$app->enqueueMessage('Id: '.$id);//.' ;-)');
			}

		}
		elseif($table->tableName == 'content')
		{
			foreach($ids as $id)
			{
				//if($
				PagesAndItemsHelper::item_state($id,'-2'); // )
				$app->enqueueMessage('Id: '.$id);//.' ;-)');
			}

		}
		else
		{
			if(isset($table->extensionType) && isset($table->extension))
			{
				$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
				require_once($path.DS.'includes'.DS.'extensions'.DS.$table->extensionType.'helper.php');

				//$typeName = 'Extension'.ucfirst($table->extensionType).'Helper';
				//$typeName::importExtension(null, $table->extension,true,null,false);
				switch(strtolower($table->extensionType))
				{
					case 'manager':
						ExtensionManagerHelper::importExtension(null, $table->extension,true,null,false);
					break;
					
					case 'itemtype':
						ExtensionItemtypeHelper::importExtension(null, $table->extension,true,null,false);
					break;
					
				}
				$dispatcher = &JDispatcher::getInstance();

				foreach($ids as $id)
				{
					$dispatcher->trigger('onExtensionArchiveTrashItemTrash', array ( $id));
					//let the extension do the message
				}
			}
		}

		$app->redirect($link);
	}


	function restore()
	{

		$app = &JFactory::getApplication();

		$link = 'index.php?option=com_pagesanditems';
		$link .= '&task=manager.doExecute'; //display';
		$link .= '&extensionName=archivetrash'; //the name
		$link .= '&extensionType=manager'; //the type
		$link .= '&extensionFolder='; //the folder
		$link .= '&extensionTask=display';
		$link .= '&view=archivetrash'; //
		$table_id = JRequest::getVar('table_id',0);


		jimport( 'joomla.application.component.model' );
		$path = realpath(dirname(__FILE__).DS.'models');
		JModel::addIncludePath($path);

		$model = &$this->getModel('archivetrash');
		$tables = $model->getTables();

		$table = $tables[$table_id];
		$ids	= JRequest::getVar('cid', array(), '', 'array');

		$app->enqueueMessage(JText::_('COM_PAGESANDITEMS_ITEMS_PUBLISHED'));
		if($table->tableName == 'menu')
		{
			foreach($ids as $id)
			{
				//if(
				PagesAndItemsHelper::page_state($id,'1');// )
				//{
					$app->enqueueMessage('Id: '.$id);
				//}
			}
		}
		elseif($table->tableName == 'content')
		{
			foreach($ids as $id)
			{
				//if(
				PagesAndItemsHelper::item_state($id,'1');// )
				$app->enqueueMessage('Id: '.$id);//.' ;-)');
			}
		}
		else
		{
			if(isset($table->extensionType) && isset($table->extension))
			{
				$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
				require_once($path.DS.'includes'.DS.'extensions'.DS.$table->extensionType.'helper.php');

				//$typeName = 'Extension'.ucfirst($table->extensionType).'Helper';
				//$typeName::importExtension(null, $table->extension,true,null,false);
				switch(strtolower($table->extensionType))
				{
					case 'manager':
						ExtensionManagerHelper::importExtension(null, $table->extension,true,null,false);
					break;
					
					case 'itemtype':
						ExtensionItemtypeHelper::importExtension(null, $table->extension,true,null,false);
					break;
					
				}
				$dispatcher = &JDispatcher::getInstance();

				foreach($ids as $id)
				{
					$dispatcher->trigger('onExtensionArchiveTrashItemPublish', array ($id));
					//let the extension do the message
				}
			}
		}

		$link .= '&table_id='.$table_id; //
		$app->redirect($link);
	}

	/*
	the item will delete complete
	*/
	function delete()
	{

		$app = &JFactory::getApplication();

		$link = 'index.php?option=com_pagesanditems';
		$link .= '&task=manager.doExecute'; //display';
		$link .= '&extensionName=archivetrash'; //the name
		$link .= '&extensionType=manager'; //the type
		$link .= '&extensionFolder='; //the folder
		$link .= '&extensionTask=display';
		$link .= '&view=archivetrash'; //
		$table_id = JRequest::getVar('table_id',0);
		$link .= '&table_id='.$table_id; //

		jimport( 'joomla.application.component.model' );
		$path = realpath(dirname(__FILE__).DS.'models');
		JModel::addIncludePath($path);

		$model = &$this->getModel('archivetrash');
		$tables = $model->getTables();

		$table = $tables[$table_id];
		$ids	= JRequest::getVar('cid', array(), '', 'array');
		$app->enqueueMessage(JText::_('COM_PAGESANDITEMS_ITEMS_DELETED'));
		if($table->tableName == 'menu')
		{
			foreach($ids as $id)
			{
				//if(
				PagesAndItemsHelper::page_state($id,'delete');// )
				//{
					$app->enqueueMessage('Id: '.$id);
				//}
			}
		}
		elseif($table->tableName == 'content')
		{
			foreach($ids as $id)
			{
				//if(
				PagesAndItemsHelper::item_state($id,'delete');//)
				//{
					$app->enqueueMessage('Id: '.$id);
				//}
			}
		}
		else
		{
			if(isset($table->extensionType) && isset($table->extension))
			{
				$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
				require_once($path.DS.'includes'.DS.'extensions'.DS.$table->extensionType.'helper.php');

				//$typeName = 'Extension'.ucfirst($table->extensionType).'Helper';
				//$typeName::importExtension(null, $table->extension,true,null,false);
				switch(strtolower($table->extensionType))
				{
					case 'manager':
						ExtensionManagerHelper::importExtension(null, $table->extension,true,null,false);
					break;
					
					case 'itemtype':
						ExtensionItemtypeHelper::importExtension(null, $table->extension,true,null,false);
					break;
					
				}
				$dispatcher = &JDispatcher::getInstance();

				foreach($ids as $id)
				{
					$dispatcher->trigger('onExtensionArchiveTrashItemDelete', array ( $id));
					//let the extension do the message
				}
			}
		}
		$link .= '&table_id='.$table_id; //
		$app->redirect($link);
	}
}
