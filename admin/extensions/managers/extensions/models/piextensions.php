<?php
/**
* @version		2.1.1
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// No direct access.
defined('_JEXEC') or die;

// Import library dependencies
//require_once dirname(__FILE__) . '/extension.php';
/**
*/
require_once(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'..'.DS.'models'.DS.'base.php');
/**

 */
//class PagesAndItemsModelExtensions extends PagesAndItemsModelBase //JModel
class PagesAndItemsModelPiextensions extends PagesAndItemsModelBase //JModel
{

		/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function XpopulateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		/*
		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$accessId = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', null, 'int');
		$this->setState('filter.access', $accessId);
		*/

		$state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $state);

		$folder = $this->getUserStateFromRequest($this->context.'.filter.folder', 'filter_folder', null, 'cmd');
		$this->setState('filter.folder', $folder);

		$folder = $this->getUserStateFromRequest($this->context.'.filter.type', 'filter_type', 1, 'cmd');
		$this->setState('filter.folder', $folder);
		/*
		$language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);
		*/

		// Load the parameters.
		/*
		$params = JComponentHelper::getParams('com_plugins');
		$this->setState('params', $params);
		*/
		// List state information.
		parent::populateState('type', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string	A prefix for the store id.
	 *
	 * @return	string	A store id.
	 */
	protected function XgetStoreId($id = '')
	{
		// Compile the store id.
		//$id	.= ':'.$this->getState('filter.search');
		//$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.state');
		$id	.= ':'.$this->getState('filter.folder');
		$id	.= ':'.$this->getState('filter.type');
		//$id	.= ':'.$this->getState('filter.language');

		return parent::getStoreId($id);
	}

	/**
	 * Returns an object list
	 *
	 * @param	string The query
	 * @param	int Offset
	 * @param	int The number of records
	 * @return	array
	 */
	protected function _getList($query, $limitstart=0, $limit=0)
	{
		$search = $this->getState('filter.search');
		$ordering = $this->getState('list.ordering', 'ordering');
		if ($ordering == 'name' || (!empty($search) && stripos($search, 'id:') !== 0)) {
			$this->_db->setQuery($query);
			$result = $this->_db->loadObjectList();
			$this->translate($result);
			if (!empty($search)) {
				foreach($result as $i=>$item) {
					if (!preg_match("/$search/i", $item->name)) {
						unset($result[$i]);
					}
				}
			}
			$lang = JFactory::getLanguage();
			JArrayHelper::sortObjects($result,'name', $this->getState('list.direction') == 'desc' ? -1 : 1, true, $lang->getLocale());
			$total = count($result);
			$this->cache[$this->getStoreId('getTotal')] = $total;
			if ($total < $limitstart) {
				$limitstart = 0;
				$this->setState('list.start', 0);
			}
			return array_slice($result, $limitstart, $limit ? $limit : null);
		}
		else {
			if ($ordering == 'ordering') {
				$query->order('folder ASC');
			}
			$query->order($this->_db->nameQuote($ordering) . ' ' . $this->getState('list.direction'));
			if($ordering == 'folder') {
				$query->order('ordering ASC');
			}
			$result = parent::_getList($query, $limitstart, $limit);
			$this->translate($result);
			return $result;
		}
	}

	/**
	 * Translate a list of objects
	 *
	 * @param	array The array of objects
	 * @return	array The array of translated objects
	 */
	protected function Xtranslate(&$items)
	{
		$lang = JFactory::getLanguage();
		foreach($items as &$item)
		{
			if($item->folder)
			{
				$extension_folder = str_replace('/','_',$item->folder);
				$prefix = $item->type.'_'.$extension_folder;
			}
			else
			{
				$prefix = $item->type;
			}
			$extension = 'pi_extension_'.$prefix.'_'.$item->element;
			//if we have long names make it short only for extensions dir
			$extensionShort = 'pi_extension_'.$item->element;

			$defaultLang = $lang->getDefault();
			if($defaultLang != 'en-GB')
			{
				$defaultLang = 'en-GB';
			}

			if(defined('COM_PAGESANDITEMS_DEFAULT_LANG'))
			{
				$defaultLangPI = COM_PAGESANDITEMS_DEFAULT_LANG;
			}
			else
			{
				//PagesAndItemsHelper::getConfig();
				if(defined('COM_PAGESANDITEMS_DEFAULT_LANG'))
				{
					$defaultLangPI = COM_PAGESANDITEMS_DEFAULT_LANG;
				}
				else
				{
					$defaultLangPI = $defaultLang;
				}
			}

			$basePath = JPATH_ADMINISTRATOR;
				$lang->load(strtolower($extension), $path.DS.$element, $defaultLangPI, false)
			||	$lang->load(strtolower($extension), $basePath, $defaultLangPI, false)
			||	$lang->load(strtolower($extensionShort), $path.DS.$element, $defaultLangPI, false)
			||	$lang->load(strtolower($extension), $path.DS.$element, $defaultLang, false)
			||	$lang->load(strtolower($extension), $basePath, $defaultLang, false)
			||	$lang->load(strtolower($extensionShort), $path.DS.$element, $defaultLang, false);
		}
	}
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 */
	protected function XgetListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.extension_id , a.name, a.element, a.folder, a.checked_out, a.checked_out_time,' .
				' a.enabled, a.access, a.ordering'
			)
		);
		$query->from('`#__extensions` AS a');

		$query->where('`type` = '.$db->quote('plugin'));

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('a.access = '.(int) $access);
		}

		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('a.enabled = '.(int) $published);
		} else if ($published === '') {
			$query->where('(a.enabled IN (0, 1))');
		}

		// Filter by state
		$query->where('a.state >= 0');

		// Filter by folder.
		if ($folder = $this->getState('filter.folder')) {
			$query->where('a.folder = '.$db->quote($folder));
		}

		// Filter by search in id
		$search = $this->getState('filter.search');
		if (!empty($search) && stripos($search, 'id:') === 0) {
			$query->where('a.extension_id = '.(int) substr($search, 3));
		}

		return $query;
	}


/**
	 * Method to checkin a row.
	 *
	 * @param	integer	$pk The numeric id of the primary key.
	 *
	 * @return	boolean	False on failure or error, true otherwise.
	 */
	public function checkin($pk = null)
	{
		// Only attempt to check the row in if it exists.
		if ($pk) {
			$user = JFactory::getUser();

			// Get a table object for the extension type
			JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
			$table =& JTable::getInstance('piextension','PagesAndItemsTable');
			if (!$table->load($pk)) {
				$this->setError($table->getError());
				return false;
			}

			// Check if this is the user having previously checked out the row.
			if ($table->checked_out > 0 && $table->checked_out != $user->get('id') && !$user->authorise('core.admin', 'com_checkin')) {
				$this->setError(JText::_('JLIB_APPLICATION_ERROR_CHECKIN_USER_MISMATCH'));
				return false;
			}

			// Attempt to check the row in.
			if (!$table->checkin($pk)) {
				$this->setError($table->getError());
				return false;
			}
		}

		return true;
	}


	/**
	 * Enable/Disable an extension.
	 *
	 * @return	boolean True on success
	 * @since	1.5
	 */
	function publish($eid = array(), $value = 1)
	{
		// Initialise variables.
		/*
		$user = JFactory::getUser();
		if ($user->authorise('core.edit.state', 'com_installer'))
		{
		*/
			$result = true;

			/*
			* Ensure eid is an array of extension ids
			*/
			if (!is_array($eid))
			{
				$eid = array($eid);
			}

			// Get a database connector
			$db = JFactory::getDBO();

			// Get a table object for the extension type
			JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
			$table =& JTable::getInstance('piextension','PagesAndItemsTable');

			// Enable the extension in the table and store it in the database
			foreach($eid as $id)
			{
				$table->load($id);

				$notenable = (($table->type == 'itemtype' && ($table->element == 'content' || $table->element == 'text')) || ($table->type == 'pagetype' && $table->version == 'integrated')  || $table->protected || $table->type == 'language' );

				if(!$notenable)
				{
					$table->enabled = $value;
				}
				else
				{
				//TODO store only
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
				else
				{
					//ms: add
					//TODO ms: remove if getItemtypes changed
					/*
					if($table->type == 'itemtype')
					{
						if($value)
						{
							PagesAndItemsHelper::changeConfigItemtype(null, $table->element, 'add');
						}
						else
						{
							PagesAndItemsHelper::changeConfigItemtype(null, $table->element, 'remove');
						}
					}
					*/
				}
			}
		/*
		} else {
			$result = false;
			JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
		}
		*/
		return $result;
	}

	/**
	 * Remove (uninstall) an extension
	 *
	 * @param	array	An array of identifiers
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function remove($eid = array())
	{
		// Initialise variables.
		$user = JFactory::getUser();
		$app = JFactory::getApplication();
		$extension = 'com_installer';
		$lang = &JFactory::getLanguage();
		//$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false);
		$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false) || $lang->load(strtolower($extension), JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
		//if ($user->authorise('core.delete', 'com_installer')) {

			// Initialise variables.
			$failed = array();
			$success = array();
			$protected = array();
			$results = array();
			/*
			* Ensure eid is an array of extension ids in the form id => client_id
			* TODO: If it isn't an array do we want to set an error and fail?
			*/
			if (!is_array($eid)) {
				$eid = array($eid => 0);
			}

			// Get a database connector
			$db = JFactory::getDBO();

			// Get an installer object for the extension type
			//jimport('joomla.installer.installer');
			//$installer = JInstaller::getInstance();
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'installer'.DS.'installer.php');
			$installer = PagesAndItemsInstaller::getInstance();


			JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
			/*
			ms: new method to load the adapter so this we need not here
			$files = JFolder::files(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'installer'.DS.'adapters','.php§');
			foreach($files as $file)
			{
				$name = JFile::getName($file);
				$name = JFile::stripExt($file);
				require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'installer'.DS.'adapters'.DS.strtolower($name.'.php'));
				$class = 'PiInstaller'.ucfirst($name);
				if (class_exists($class))
				{
					$adapter = new $class($installer);
					$version = new JVersion();
					$joomlaVersion = $version->getShortVersion();

					if($joomlaVersion < '1.6')
					{
						$adapter->parent =& $installer;
					}
					$installer->setAdapter($name, $adapter);
				}
			}
			*/
			defined('COM_PAGESANDITEMS_INSTALLER_PATH') or define('COM_PAGESANDITEMS_INSTALLER_PATH', JPATH_COMPONENT_ADMINISTRATOR.DS.'extensions');
			$row = JTable::getInstance('piextension','PagesAndItemsTable');
			// Uninstall the chosen extensions

			foreach($eid as $id)
			{
				$id = trim($id);
				$row->load($id);

				if ($row->type && (!$row->protected && $row->version != 'integrated'))
				{
					$result = $installer->uninstall($row->type, $id);
					//$result = $installer->uninstall('extension', $id);
					// Build an array of extensions that failed to uninstall
					if ($result === false)
					{
						$failed[] = $id;
					}
					else
					{
						$success[] = $id;
					}
				}
				elseif($row->protected || $row->version == 'integrated')
				{
					$protected[] = $id;
				}

			}

			$langstring = 'COM_INSTALLER_TYPE_TYPE_'. strtoupper($row->type);
			$rowtype = JText::_($langstring);
			if(strpos($rowtype, $langstring) !== false)
			{
				$rowtype = $row->type;
			}

			if (count($failed))
			{
/*
$msg = JText::sprintf('COM_INSTALLER_INSTALL_ERROR', JText::_('COM_INSTALLER_TYPE_TYPE_'.strtoupper($package['type'])));
			$result = false;
		} else {
			// Package installed sucessfully
			$msg = JText::sprintf('COM_INSTALLER_INSTALL_SUCCESS', JText::_('COM_INSTALLER_TYPE_TYPE_'.strtoupper($package['type'])));


$msg = JText::sprintf('COM_INSTALLER_UNINSTALL_ERROR', $rowtype);
				$result = false;
			} else {

				// Package uninstalled sucessfully
				$msg = JText::sprintf('COM_INSTALLER_UNINSTALL_SUCCESS', $rowtype);

*/
				// There was an error in uninstalling the package
				foreach($failed as $fail)
				{
					//$msg = JText::sprintf('INSTALLEXT', JText::_($package['type']), JText::_('Error'));
					//$msg = JText::sprintf('COM_PAGESANDITEMS_UNINSTALL_ERROR', $rowtype); //.' id: '.$fail; //, JText::_('COM_PAGESANDITEMS_INSTALLEXT_FAILED'));
					$msg = JText::sprintf('COM_INSTALLER_UNINSTALL_ERROR', $rowtype); //.' id: '.$fail; //, JText::_('COM_PAGESANDITEMS_INSTALLEXT_FAILED'));
					JError::raiseWarning( 100, $msg );
					//$app->enqueueMessage($msg);
					$results[] = false;
				}
			}
			if (count($success))
			{
				foreach($success as $suc)
				{
					// Package uninstalled sucessfully
					//$msg = JText::sprintf('COM_PAGESANDITEMS_UNINSTALL_SUCCESS', $rowtype); //.' id: '.$suc; //, JText::_('COM_PAGESANDITEMS_INSTALLEXT_SUCCESS'));
					$msg = JText::sprintf('COM_INSTALLER_UNINSTALL_SUCCESS', $rowtype); //.' id: '.$suc; //, JText::_('COM_PAGESANDITEMS_INSTALLEXT_SUCCESS'));
					$app->enqueueMessage($msg);
					//JError::raiseWarning( 100, 'Text Warning' );
					$results[] = true;
				}
			}

			if (count($protected))
			{
				foreach($protected as $protect)
				{
					// Package uninstalled sucessfully
					$msg = JText::sprintf('COM_PAGESANDITEMS_UNINSTALLEXT', $rowtype.' id: '.$protect, JText::_('COM_PAGESANDITEMS_INSTALLEXT_PROTECTED'));
					$app->enqueueMessage($msg);
					$results[] = false;
				}
			}
			//$app->enqueueMessage($msg);
			/*
			$this->setState('action', 'remove');
			$this->setState('name', $installer->get('name'));
			$app->setUserState('com_installer.message', $installer->message);
			$app->setUserState('com_installer.extension_message', $installer->get('extension_message'));
			*/
			return $results;
		/*
		}
		else
		{
			$result = false;
			JError::raiseWarning(403, JText::_('JERROR_CORE_DELETE_NOT_PERMITTED'));
		}
		*/
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

		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
		$row = JTable::getInstance('piextension','PagesAndItemsTable');
		$result = 0;
		// refresh cache
		foreach($eid as $id)
		{
			if(!$row->load($id))
			{
				return 0;
			}

			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'installer'.DS.'installer.php');
			$installer = PagesAndItemsInstaller::getInstance();

			$result |= $installer->refreshManifestCache($id); //refreshManifestCache($id);
			/*
			$path = realpath(dirname(__FILE__).'/../extensions');
			if($row->folder)
			{
				$extension_folder = $row->folder;
				$path = str_replace('/',DS,$path.DS.$row->type.'s'.DS.$extension_folder);
			}
			else
			{
				$path = str_replace('/',DS,$path.DS.$row->type.'s');
			}
			$path = $path.DS.$row->element.DS.$row->element.'.xml';
			$result = serialize($this->parseXMLInstallFile($path));
			$row->manifest_cache = $result;
			$row->store();
			*/
		}
		return $result;
	}


	function parseXMLInstallFile($path)
	{
		// Read the file to see if it's a valid component XML file
		$xml = simplexml_load_file($path);

		if (!$xml)
		{
			unset($xml);
			return false;
		}

		if ( !is_object($xml) || !is_object($xml->install))
		{
			unset($xml);
			return false;
		}
		$data = array();
		$type = (string)$xml->attributes()->type;
		$data['type'] = $type;

		if (count($xml->files->children()))
		{
			foreach ($xml->files->children() as $file)
			{
				if ((string)$file->attributes()->$type)
				{
					$element = (string)$file->attributes()->$type;
					$data['name'] = $element;
					break;
				}
			}
		}

		$element = (string)$xml->creationDate;
		$data['creationdate'] = $element ? $element : JText::_('Unknown');

		$element = (string)$xml->author;
		$data['author'] = $element ? $element : JText::_('Unknown');

		$element = (string)$xml->copyright;
		$data['copyright'] = $element ? $element : '';

		$element = (string)$xml->authorEmail;
		$data['authorEmail'] = $element ? $element : '';

		$element = (string)$xml->authorUrl;
		$data['authorUrl'] = $element ? $element : '';

		$element = (string)$xml->version;
		$data['version'] = $element ? $element : '';

		$element = (string)$xml->description;
		$data['description'] = $element ? $element : '';

		$element = (string)$xml->attributes()->folder;
		$data['folder'] = $element ? $element : '';
		unset($xml);
		return $data;
	}


}
