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

// Import library dependencies
//require_once dirname(__FILE__) . '/extension.php';
/**
*/


require_once(dirname(__FILE__).DS.'base.php');

//class PagesAndItemsModelManageextension extends PagesAndItemsModelManageextensionBase
class PagesAndItemsModelManageextension extends PagesAndItemsModelBase //JModel
{

	/**
	 * Enable/Disable an extension.
	 *
	 * @return	boolean True on success
	 * @since	1.5
	 */
	function publish($id , $value = 1)
	{
		$result = true;

		// Get a database connector
		$db = JFactory::getDBO();

		// Get a table object for the extension type
		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
		$table =& JTable::getInstance('piextension','PagesAndItemsTable');

		$table->load($id);
		$notenable = (($table->type == 'itemtype' && ($table->element == 'content' || $table->element == 'text')) || ($table->type == 'pagetype' && $table->version == 'integrated') || $table->protected);

		if(!$notenable)
		{
			$table->enabled = $value;
		}
		//$table->enabled = $value;
		/*
		if(!$table->protected)
		{
			$table->enabled = $value;
		}
		*/
		if (!$table->store()) 
		{
			$this->setError($table->getError());
			$result = false;
		}

		return $result;

	}

	/**
	 * Refreshes the cached manifest information for an extension.
	 *
	 * @param	int		extension identifier (key in #__extensions)
	 * @return	boolean	result of refresh
	 * @since	1.6
	 */
	function refresh($eid)
	{
		if (!is_array($eid)) 
		{
			$eid = array($eid => 0);
		}

		// Get a database connector
		$db = JFactory::getDBO();

		// Get an installer object for the extension type
		//jimport('joomla.installer.installer');
		//$installer = JInstaller::getInstance();
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'installer'.DS.'installer.php');
		$installer = PagesAndItemsInstaller::getInstance();
		
		$row = JTable::getInstance('extension');
		$result = 0;

		// refresh the chosen extensions
		foreach($eid as $id) 
		{
			$result |= $installer->refreshManifestCache($id); //refreshManifestCache($id);
		}
		return $result;
	}



	/**
	 * Method to get the database query
	 *
	 * @return	JDatabaseQuery	The database query
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		$enabled= $this->getState('filter.enabled');
		$type = $this->getState('filter.type');
		$client = $this->getState('filter.client_id');
		$group = $this->getState('filter.group');
		$hideprotected = $this->getState('filter.hideprotected');
		$query = new JDatabaseQuery;
		$query->select('*');
		$query->from('#__pi_extensions');
		$query->where('state=0');
		if ($hideprotected) {
			$query->where('protected!=1');
		}
		if ($enabled != '') {
			$query->where('enabled=' . intval($enabled));
		}
		if ($type) {
			$query->where('type=' . $this->_db->Quote($type));
		}
		if ($client != '') {
			$query->where('client_id=' . intval($client));
		}
		if ($group != '' && in_array($type, array('plugin', 'library', ''))) {

			$query->where('folder=' . $this->_db->Quote($group == '*' ? '' : $group));
		}

		// Filter by search in id
		$search = $this->getState('filter.search');
		if (!empty($search) && stripos($search, 'id:') === 0) {
			$query->where('extension_id = '.(int) substr($search, 3));
		}

		return $query;
	}
}
