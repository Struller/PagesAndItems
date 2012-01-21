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
/**

 */

class PagesAndItemsModelManage extends PagesAndItemsModelBase //JModel
{
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
				$notenable = (($table->type == 'itemtype' && ($table->element == 'content' || $table->element == 'text')) || ($table->type == 'pagetype' && $table->version == 'integrated')  || $table->protected);

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
		//if ($user->authorise('core.delete', 'com_installer')) {

			// Initialise variables.
			$failed = array();
			$success = array();
			$protecte = array();
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
			$files = JFolder::files(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'installer'.DS.'adapters','.php§');
			foreach($files as $file)
			{
				$name = JFile::getName($file); 
				$name = JFile::stripExt($file);
				require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'installer'.DS.'adapters'.DS.strtolower($name.'.php'));
				$class = 'JInstaller'.ucfirst($name);
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

				// There was an error in uninstalling the package
				foreach($failed as $fail)
				{
					//$msg = JText::sprintf('INSTALLEXT', JText::_($package['type']), JText::_('Error'));
					$msg = JText::sprintf('COM_PAGESANDITEMS_UNINSTALLEXT', $rowtype.' id: '.$fail, JText::_('COM_PAGESANDITEMS_INSTALLEXT_FAILED'));
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
					$msg = JText::sprintf('COM_PAGESANDITEMS_UNINSTALLEXT', $rowtype.' id: '.$suc, JText::_('COM_PAGESANDITEMS_INSTALLEXT_SUCCESS'));
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
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	
	 */
	protected function XpopulateState()
	{
		// Initialise variables.
		$app = JFactory::getApplication();
		$filters = JRequest::getVar('filters');
		if (empty($filters)) {
			$data = $app->getUserState($this->context.'.data');
			$filters = $data['filters'];
		}
		else {
			$app->setUserState($this->context.'.data', array('filters'=>$filters));
		}

		$this->setState('message',$app->getUserState('com_installer.message'));
		$this->setState('extension_message',$app->getUserState('com_installer.extension_message'));
		$app->setUserState('com_installer.message','');
		$app->setUserState('com_installer.extension_message','');

		$this->setState('filter.search', isset($filters['search']) ? $filters['search'] : '');
		$this->setState('filter.hideprotected', isset($filters['hideprotected']) ? $filters['hideprotected'] : 0);
		$this->setState('filter.enabled', isset($filters['enabled']) ? $filters['enabled'] : '');
		$this->setState('filter.type', isset($filters['type']) ? $filters['type'] : '');
		$this->setState('filter.group', isset($filters['group']) ? $filters['group'] : '');
		$this->setState('filter.client_id', isset($filters['client_id']) ? $filters['client_id'] : '');
		parent::populateState('name', 'asc');
	}
}
