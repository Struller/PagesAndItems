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

class PagesAndItemsControllerExtensionManagerExtensionsPiextension extends PagesAndItemsController//JController //PagesAndItemsController
{
	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$task	= JRequest::getVar('task'); //$this->getTask();

		$this->registerTask( 'apply', 'save');
		/*
		$this->registerTask( 'accesspublic' 	, 	'access' );
		$this->registerTask( 'accessregistered'  , 	'access' );
		$this->registerTask( 'accessspecial' 	, 	'access' );
		*/
	}

	function cancel()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		//$option = JRequest::getVar('option');
		$client  = JRequest::getWord( 'filter_client', 'site' );
		$db =& JFactory::getDBO();
		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
		$row =& JTable::getInstance('piextension','PagesAndItemsTable');
		$row->bind(JRequest::get('post'));
		$row->checkin();
		//http://127.0.0.1:4001/administrator/index.php?option=com_pagesanditems&task=manager.doExecute&extension=extensions&extensionType=manager&extensionFolder=&extension_sub_task=display&view=extensions
		//$this->setRedirect( JRoute::_( 'index.php?option=com_pagesanditems&task=manager.doExecute&extension=extensions&extensionFolder=&extensionType=manager&extension_task=display&view=extensions', false ) );
		//dump('cancel');
		$this->setRedirect( 'index.php?option=com_pagesanditems&task=manager.doExecute&extension=extensions&extensionFolder=&extensionType=manager&extensionTask=display&view=piextensions' );
	}




	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		//$option = JRequest::getVar('option');
		$db = & JFactory::getDBO();
		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
		$row =& JTable::getInstance('piextension','PagesAndItemsTable');
		
		$task = JRequest::getVar('extensionTask','save');
		//dump(JRequest::get());
		//$task = $this->getTask();
		$client = JRequest::getWord( 'filter_client', 'site' );
		
		
		
		//who we handle jform
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		if($joomlaVersion < '1.6')
		{
			$post = JRequest::get('post');
		}
		else
		{
			$data = JRequest::getVar('jform', array(), 'post', 'array');
			if($data && count($data))
			{
				$post = $data;
			}
			else
			{
				$post = JRequest::get('post');
			}
			
		}
		/*
		if(isset($post['jform']))
		{
			$post = $post['jform'];
		}
		*/
		
		
		
		if (!$row->bind($post)) 
		{
			JError::raiseError(500, $row->getError() );
		}
		if (!$row->check()) {
			JError::raiseError(500, $row->getError() );
		}

		if (!$row->store()) {
			JError::raiseError(500, $row->getError() );
		}
		$row->checkin();

		if ($client == 'admin') {
			$where = "client_id=1";
		} else {
			$where = "client_id=0";
		}

		$row->reorder( 'type = '.$db->Quote($row->type).' AND folder = '.$db->Quote($row->folder).' AND ordering > -10000 AND ordering < 10000 AND ( '.$where.' )' );
		/*
		we will trigger onAfterParamsSave
		
		*/
		//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'extensions'.DS.'helper.php');
		//ExtensionHelper::importExtension($row->type,$row->folder, $row->element,true,null,true);
		$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.$row->type.'helper.php');
		//$extensions = 
		
		$extensionHelper = 'Extension'.ucfirst($row->type).'Helper';
		//$extensionHelper::importExtension($row->folder, $row->element,true,null,true);
		$extensionHelper::importExtension($row->folder, $row->element,true,null,true);
		
		
		//$extensionHelper::importExtension($row->folder, $row->element,true,null,true);
		$dispatcher = &JDispatcher::getInstance();
		//$params = $row->params;
		/*
			$param string the extension name
		*/
		$dispatcher->trigger('onAfterParamsSave',array(&$row->params,$row->element));
		
		/*
		require_once($path.DS.'includes'.DS.'extensions'.DS.'managerhelper.php');
		$extensionHelper = 'Extension'.ucfirst('manager').'Helper';
		$extensionHelper::importExtension(null, 'extensions',true,null,true);
		*/
		
		//PagesAndItemsHelper::loadExtensionLanguage('extensions','manager');
		//dump(JText::sprintf( 'PI_EXTENSION_MANAGER_EXTENSIONS_APPLY_EXTENSION', $row->name ));
		//$dispatcher->trigger('loadLanguage');
		//dump(JText::_('PI_EXTENSION_MANAGER_EXTENSIONS_VIEW_INSTALL_NAME'));
		/*
		//$fieldParams = $params;
		*/
		switch ( $task )
		{
			case 'apply':
				$msg = JText::sprintf( 'PI_EXTENSION_MANAGER_EXTENSIONS_APPLY_EXTENSION', $row->name );
				//$msg = JText::_('PI_EXTENSION_MANAGER_EXTENSIONS_APPLY');
				//the lang is load but the JText not
				$this->setRedirect( 'index.php?option=com_pagesanditems&task=manager.doExecute&extension=extensions&extensionFolder=&extensionType=manager&extensionTask=display&view=piextension&client='. $client .'&sub_task=edit&cid[]='. $row->extension_id.'&layout=edit&extension_id='. $row->extension_id, $msg );
				//$this->setRedirect( 'index.php?option=com_pagesanditems&view=manageextension&client='. $client .'&task=edit&cid[]='. $row->extension_id.'&extension_id='. $row->extension_id, $msg );
				break;

			case 'save':
			default:
				$msg = JText::sprintf( 'PI_EXTENSION_MANAGER_EXTENSIONS_SAVE_EXTENSION', $row->name );
				$this->setRedirect( 'index.php?option=com_pagesanditems&task=manager.doExecute&extension=extensions&extensionFolder=&extensionType=manager&extensionTask=display&view=piextensions' , $msg );
				//$this->setRedirect( 'index.php?option=com_pagesanditems&view=manage&client='. $client, $msg );
				break;
		}
	}
}