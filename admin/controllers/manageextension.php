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

class PagesAndItemsControllerManageextension extends PagesAndItemsController//JController //PagesAndItemsController
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

		$this->registerTask( 'apply', 		'save');
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
		$this->setRedirect( JRoute::_( 'index.php?option=com_pagesanditems&view=manage&client='. $client, false ) );
	}




	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		//$option = JRequest::getVar('option');
		$db = & JFactory::getDBO();
		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
		$row =& JTable::getInstance('piextension','PagesAndItemsTable');
		
		$task = JRequest::getVar('task','save');
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
		$path = realpath(dirname(__FILE__).DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.$row->type.'helper.php');
		//$extensions = 
		
		$extensionHelper = 'Extension'.ucfirst($row->type).'Helper';
		$extensionHelper::importExtension($row->folder, $row->element,true,null,true);
		$dispatcher = &JDispatcher::getInstance();
		//$params = $row->params;
		/*
			$param string the extension name
		*/
		$dispatcher->trigger('onAfterParamsSave',array(&$row->params,$row->element));
		
		
		/*
		//$fieldParams = $params;
		*/
		switch ( $task )
		{
			case 'apply':
				$msg = JText::sprintf( 'Successfully Saved changes to Plugin', $row->name );
				$this->setRedirect( 'index.php?option=com_pagesanditems&view=manageextension&client='. $client .'&task=edit&cid[]='. $row->extension_id.'&extension_id='. $row->extension_id, $msg );
				break;

			case 'save':
			default:
				$msg = JText::sprintf( 'Successfully Saved Plugin', $row->name );
				$this->setRedirect( 'index.php?option=com_pagesanditems&view=manage&client='. $client, $msg );
				break;
		}
	}

	/**
	 * Enable/Disable an extension (if supported).
	 *
	 * @since	1.6
	 */
	public function publish()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		//$option = JRequest::getVar('option');
		// Initialise variables.
		$user	= JFactory::getUser();
		$client = JRequest::getVar('client');
		
		$ids	= JRequest::getVar('cid', array(), '', 'array');
		$id = JRequest::getVar('id');
		
		$values	= array('publish' => 1, 'unpublish' => 0);
		$task	= JRequest::getVar('task'); //$this->getTask();

		$value	= JArrayHelper::getValue($values, $task, 0, 'int');
		/*
		if (empty($ids)) {
			JError::raiseWarning(500, JText::_('COM_INSTALLER_ERROR_NO_EXTENSIONS_SELECTED'));
		} else {
			// Get the model.
			$model	= $this->getModel('manage');

			// Change the state of the records.
			if (!$model->publish($ids, $value)) {
				JError::raiseWarning(500, implode('<br />', $model->getErrors()));
			} else {
				if ($value == 1) {
					$ntext = 'COM_INSTALLER_N_EXTENSIONS_PUBLISHED';
				} else if ($value == 0) {
					$ntext = 'COM_INSTALLER_N_EXTENSIONS_UNPUBLISHED';
				}
				$this->setMessage(JText::plural($ntext, count($ids)));
			}
		}
		*/
		$this->setRedirect(JRoute::_('index.php?option=com_pagesanditems&view=manageextension',false));

	}

}