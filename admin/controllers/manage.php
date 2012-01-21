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
class PagesAndItemsControllerManage extends PagesAndItemsController //JController //PagesAndItemsController
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

		$this->registerTask('enable','publish');
		$this->registerTask('disable','publish');

		$this->registerTask( 'apply', 		'save');
		$this->registerTask( 'disable', 	'publish');
		$this->registerTask( 'enable', 	'publish');
		$this->registerTask( 'publish', 	'publish');
		$this->registerTask( 'unpublish', 	'publish');
		//$this->registerTask( 'edit' , 		'display' );
		//$this->registerTask( 'add' , 		'display' );
		$this->registerTask( 'orderup'   , 	'order' );
		$this->registerTask( 'orderdown' , 	'order' );

		$this->registerTask( 'accesspublic' 	, 	'access' );
		$this->registerTask( 'accessregistered'  , 	'access' );
		$this->registerTask( 'accessspecial' 	, 	'access' );
		/*
		$extension = 'com_pagesanditems';
		$lang = &JFactory::getLanguage();
		$lang->load(strtolower($extension), JPATH_COMPONENT_ADMINISTRATOR, null, false, false);
		*/
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

		// Initialise variables.
		//$option = JRequest::getVar('option');
		$user	= JFactory::getUser();
		$ids	= JRequest::getVar('cid', array(), '', 'array');
		
		//$values	= array('enable' => 1, 'disable' => 0);
		$values	= array('publish' => 1, 'unpublish' => 0);
		
		$task	= JRequest::getVar('task'); //$this->getTask();

		$value	= JArrayHelper::getValue($values, $task, 0, 'int');



		if (empty($ids)) 
		{
			JError::raiseWarning(500, JText::_('COM_INSTALLER_ERROR_NO_EXTENSIONS_SELECTED'));
		}
		else
		{
			// Get the model.
			$model	= $this->getModel('manage');

			// Change the state of the records.
			if (!$model->publish($ids, $value)) 
			{
				JError::raiseWarning(500, implode('<br />', $model->getErrors()));
			}
			else
			{
				if ($value == 1)
				{
					$ntext = 'COM_INSTALLER_N_EXTENSIONS_PUBLISHED';
				}
				else if ($value == 0) 
				{
					$ntext = 'COM_INSTALLER_N_EXTENSIONS_UNPUBLISHED';
				}
				//$this->setMessage(JText::plural($ntext, count($ids)));
			}
		}
		$this->setRedirect( 'index.php?option=com_pagesanditems&view=manage',$ntext);
		//$this->setRedirect(JRoute::_('index.php?option=com_pagesanditems&view=manage',$ntext));

	}

	function order(  )
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		//$option = JRequest::getVar('option');
		$db =& JFactory::getDBO();

		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		JArrayHelper::toInteger($cid, array(0));

		$uid    = $cid[0];
		$inc    = ( $this->getTask() == 'orderup' ? -1 : 1 );
		$client = JRequest::getWord( 'filter_client', 'site' );

		if ($client == 'admin') 
		{
			$where = "client_id = 1";
		} 
		else 
		{
			$where = "client_id = 0";
		}
		//$row =& JTable::getInstance('plugin');
		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
		$row =& JTable::getInstance('piextension','PagesAndItemsTable');
		$row->load( $uid );
		
		//$row->reorder( 'type = '.$db->Quote($row->type).' AND folder = '.$db->Quote($row->folder).' AND ordering > -10000 AND ordering < 10000 AND ( '.$where.' )' );
		$row->move( $inc, 'type = '.$db->Quote($row->type).' AND folder='.$db->Quote($row->folder).' AND ordering > -10000 AND ordering < 10000 AND ('.$where.')' );

		$this->setRedirect( 'index.php?option=com_pagesanditems&view=manage' );
	}

	/**
	 * Remove an extension (Uninstall).
	 *
	 * @return	void
	 * @since	1.5
	 */
	public function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		//$option = JRequest::getVar('option');
		$eid	= JRequest::getVar('cid', array(), '', 'array');
		$model	= $this->getModel('manage','PagesAndItemsModel');
		//$model	= &$this->getModel( 'Install','pagesanditemsModel');
		JArrayHelper::toInteger($eid, array());
		$result = $model->remove($eid);
		$this->setRedirect(JRoute::_('index.php?option=com_pagesanditems&view=manage',false));
	}

	function saveorder( )
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		//$option = JRequest::getVar('option');
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		JArrayHelper::toInteger($cid, array(0));

		$db			=& JFactory::getDBO();
		$total		= count( $cid );
		$order 		= JRequest::getVar( 'order', array(0), 'post', 'array' );
		JArrayHelper::toInteger($order, array(0));

		$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		JArrayHelper::toInteger($cid, array(0));

		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
		$row =& JTable::getInstance('piextension','PagesAndItemsTable');
		$conditions = array();

		// update ordering values
		for ( $i=0; $i < $total; $i++ )
		{
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store()) {
					JError::raiseError(500, $db->getErrorMsg() );
				}
				// remember to updateOrder this group
				$condition = 'type = '.$db->Quote($row->type).' AND folder='.$db->Quote($row->folder).' AND ordering > -10000 AND ordering < 10000 AND client_id = ' . (int) $row->client_id;
				$found = false;
				foreach ( $conditions as $cond )
				{
					if ($cond[1]==$condition) {
						$found = true;
						break;
					}
				}
				if (!$found) $conditions[] = array($row->id, $condition);
			}
		}

		// execute updateOrder for each group
		foreach ( $conditions as $cond ) {
			$row->load( $cond[0] );
			$row->reorder( $cond[1] );
		}

		$msg 	= JText::_( 'New ordering saved' );
		$this->setRedirect( 'index.php?option=com_pagesanditems&view=manage', $msg );
	}	

	function refresh()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$uid	= JRequest::getVar('cid', array(), '', 'array');
		$model	= $this->getModel('manage','PagesAndItemsModel');
		JArrayHelper::toInteger($uid, array());
		$result = $model->refresh($uid);
		$this->setRedirect(JRoute::_('index.php?option=com_pagesanditems&view=manage',false));
	}
	
	function cancel()
	{
		$this->setRedirect(JRoute::_('index.php?option=com_pagesanditems&view=managers',false));
	}
	
}