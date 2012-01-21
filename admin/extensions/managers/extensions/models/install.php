<?php
/**
* @version		2.1.2
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// No direct access.
defined('_JEXEC') or die;
// Import library dependencies

jimport('joomla.application.component.model');
/*
jimport('joomla.installer.installer');
jimport('joomla.installer.helper');
*/
//require_once( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_installer' .DS. 'models'.DS. 'install.php' );

/**

 */
//class PagesAndItemsModelExtensionManageExtensionsInstall extends InstallerModelInstall //JModel
//class PagesAndItemsModelInstall extends InstallerModelInstall //JModel
class PagesAndItemsModelInstall extends JModel
{
/**
	 * @var object JTable object
	 */
	protected $_table = null;

	/**
	 * @var object JTable object
	 */
	protected $_url = null;

	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context = 'com_pagesanditems.installer.install';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState()
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		$this->setState('message',$app->getUserState('com_pagesanditems.installer.message'));
		$this->setState('extension_message',$app->getUserState('com_pagesanditems.installer.extension_message'));

		$app->setUserState('com_pagesanditems.installer.message','');
		$app->setUserState('com_pagesanditems.installer.extension_message','');

		// Recall the 'Install from Directory' path.
		$path = $app->getUserStateFromRequest($this->_context.'.install_directory', 'install_directory', $app->getCfg('tmp_path'));
		$this->setState('install.directory', $path);

		parent::populateState();
	}


	/**
	 * Install an extension from either folder, url or upload.
	 *
	 * @return	boolean result of install
	 * @since	1.5
	 */
	function install()
	{
		$extension = 'com_installer';
		$lang = &JFactory::getLanguage();
		//$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false);
		$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false) || $lang->load(strtolower($extension), JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);

		require_once( dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'..'.DS.'includes'.DS.'installer'.DS.'installerhelper.php' );
		require_once( dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'..'.DS.'includes'.DS.'installer'.DS.'installer.php');
		//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'installer'.DS.'installer.php');

		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();

		if($joomlaVersion < '1.6')
		{
			//joomla 1.5.x
			/*
				from J1.5
			*/
			//TODO replace $mainframe with app?
			//global $mainframe;

			$this->setState('action', 'install');
			switch(JRequest::getWord('installtype'))
			{
				case 'folder':
					$package = $this->_getPackageFromFolder();
					break;
				case 'upload':
					$package = $this->_getPackageFromUpload();
					break;
				case 'url':
					$package = $this->_getPackageFromUrl();
					break;
				default:
					$this->setState('message', 'No Install Type Found');
					return false;
					break;
			}
			// Was the package unpacked?
			if (!$package) {
				$this->setState('message', 'Unable to find install package');
				return false;
			}
			// Get a database connector
			//$db = & JFactory::getDBO();
			/*
			make an own installer ?
			*/


			$installer = PagesAndItemsInstaller::getInstance();
			// Get an installer instance
			//$installer =& JInstaller::getInstance();

			/*
			we need the files from adapter
			JPATH_COMPONENT_ADNMINISTRATOR.DS.'includes'.DS.'extensions'.DS.'adapters'.DS
			*/
			jimport('joomla.filesystem.folder');
			/*
			ms: new method to load the adapter so this we need not here, but for J1.5 this must check
			$files = JFolder::files(JPATH_LIBRARIES.DS.'joomla'.DS.'installer'.DS.'adapters','.php');
			foreach($files as $file)
			{
				$name = JFile::getName($file);
				$name = JFile::stripExt($file);
				require_once(JPATH_LIBRARIES.DS.'joomla'.DS.'installer'.DS.'adapters'.DS.strtolower($name.'.php')); //.'.php');
				$class = 'JInstaller'.ucfirst($name);
				if (class_exists($class))
				{
					$adapter = new stdClass();
					$installer->setAdapter($name, $adapter);
				}
			}
			*/
			//jimport('joomla.filesystem.file');

			JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');

			/*
			$files = JFolder::files(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'installer'.DS.'adapters','.php$');
			foreach($files as $file)
			{
				$name = JFile::getName($file);
				$name = JFile::stripExt($file);
				require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'installer'.DS.'adapters'.DS.strtolower($name.'.php')); //.'.php');

				$class = 'PiInstaller'.ucfirst($name);
				if (class_exists($class))
				{
					$adapter = new $class($installer);
					$adapter->parent =& $installer;
					$installer->setAdapter($name, $adapter);
				}
			}
			*/

			/*
			TODO check the type
			if($package['type'] != ?)
			{
				message...
			}
			*/

			defined('COM_PAGESANDITEMS_INSTALLER_PATH') or define('COM_PAGESANDITEMS_INSTALLER_PATH', JPATH_COMPONENT_ADMINISTRATOR.DS.'extensions');
			// Install the package




			if (!$installer->install($package['dir']))
			{
				// There was an error installing the package
				//$msg = JText::sprintf('INSTALLEXT', JText::_('Error'));
				$msg = JText::_('COM_PAGESANDITEMS_INSTALLEXT_FAILED'); //JText::_('Error');
				//JText::_($package['type']), JText::_('Error'));

				$result = false;
			}
			else
			{
				// Package installed sucessfully
				//$msg = JText::sprintf('INSTALLEXT', JText::_('Success'));
				//$msg = JText::_('Success');
				$msg = JText::_('COM_PAGESANDITEMS_INSTALLEXT_SUCCESS');
				//JText::_($package['type']), JText::_('Success'));
				$result = true;
			}

			// Set some model state values
			//$mainframe->enqueueMessage($msg);
			$this->setState('msg', $msg);
			$this->setState('type', $package['type']);
			$this->setState('subtype', $installer->get('subtype'));
			$this->setState('name', $installer->get('name'));
			$this->setState('result', $result);
			$this->setState('message', $installer->message);
			$this->setState('extension_message', $installer->get('extension.message'));
			// Cleanup the install files
			if (!is_file($package['packagefile'])) {
				$config =& JFactory::getConfig();
				$package['packagefile'] = $config->getValue('config.tmp_path').DS.$package['packagefile'];
			}
			JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);

			return $result;
		}
		else
		{
			//return false;
			//joomla 1.6.x
			/*
			from J1.6
			*/
			jimport('joomla.client.helper');
			$this->setState('action', 'install');
			// Set FTP credentials, if given.
			JClientHelper::setCredentialsFromRequest('ftp');
			$app = JFactory::getApplication();
			switch(JRequest::getWord('installtype')) {
				case 'folder':
					// Remember the 'Install from Directory' path.
					$app->getUserStateFromRequest($this->_context.'.install_directory', 'install_directory');
					$package = $this->_getPackageFromFolder();
					break;
				case 'upload':
					$package = $this->_getPackageFromUpload();
					break;
				case 'url':
					$package = $this->_getPackageFromUrl();
					break;
				default:
					$app->setUserState('com_pagesanditems.installer.message', JText::_('COM_INSTALLER_NO_INSTALL_TYPE_FOUND'));
					return false;
					break;
			}
			// Was the package unpacked?
			if (!$package)
			{
				//$app->setUserState('com_installer.message', JText::_('COM_INSTALLER_UNABLE_TO_FIND_INSTALL_PACKAGE'));
				$app->setUserState('com_pagesanditems.installer.message', JText::_('COM_INSTALLER_UNABLE_TO_FIND_INSTALL_PACKAGE'));
				return false;
			}



			/*
			make an own installer ?
			only for J1.5? so we can use tag extension
			*/
			$installer = PagesAndItemsInstaller::getInstance();


			// Get an installer instance
			//$installer = JInstaller::getInstance();


			jimport('joomla.filesystem.folder');

			JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
			/*
			ms: new method to load the adapter so this we need not here

			$files = JFolder::files(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'installer'.DS.'adapters','.php$');
			foreach($files as $file)
			{
				$name = JFile::getName($file);
				$name = JFile::stripExt($file);
				require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'installer'.DS.'adapters'.DS.strtolower($name.'.php'));

				$class = 'PiInstaller'.ucfirst($name);
				if (class_exists($class))
				{

					$adapter = new $class($installer);
					//$adapter->parent =& $installer;
					$installer->setAdapter($name, $adapter);
				}
			}
			*/
			defined('COM_PAGESANDITEMS_INSTALLER_PATH') or define('COM_PAGESANDITEMS_INSTALLER_PATH', JPATH_COMPONENT_ADMINISTRATOR.DS.'extensions');

			// Install the package
			if (!$installer->install($package['dir'])) {
				// There was an error installing the package
				//$msg = JText::_('COM_PAGESANDITEMS_INSTALLEXT_SUCCESS');
				$msg = JText::_('COM_PAGESANDITEMS_INSTALLEXT_FAILED').': '.$package['type'] ;
				//$msg = JText::sprintf('COM_INSTALLER_INSTALL_ERROR', JText::_('COM_INSTALLER_TYPE_TYPE_'.strtoupper($package['type'])));
				$result = false;
			} else {
				// Package installed sucessfully
				$msg = JText::_('COM_PAGESANDITEMS_INSTALLEXT_SUCCESS').': '.$package['type'] ;
				//$msg = JText::sprintf('COM_INSTALLER_INSTALL_SUCCESS', JText::_('COM_INSTALLER_TYPE_TYPE_'.strtoupper($package['type'])));
				$result = true;
			}
			// Set some model state values
			$app= JFactory::getApplication();
			$app->enqueueMessage($msg);

			$this->setState('name', $installer->get('name'));
			$this->setState('result', $result);

			/*
			$this->setState('message', $installer->message);
			$this->setState('extension_message', $installer->get('extension_message'));
			$this->setState('redirect_url', $installer->get('redirect_url'));
			*/

			$app->setUserState('com_pagesanditems.installer.message', $installer->message);
			$app->setUserState('com_pagesanditems.installer.extension_message', $installer->get('extension.message'));
			$app->setUserState('com_pagesanditems.installer.redirect_url', $installer->get('redirect_url'));

			//$app->setUserState('com_pagesanditems.installer.redirect_url', $installer->get('redirect_url'));

			/*
			$app->setUserState($this->_context.'.message', $installer->message);
			$app->setUserState($this->_context.'.extension_message', $installer->get('extension_message'));
			$app->setUserState($this->_context.'.redirect_url', $installer->get('redirect_url'));
			*/
			// Cleanup the install files
			if (!is_file($package['packagefile']))
			{
				$config = JFactory::getConfig();
				$package['packagefile'] = $config->get('tmp_path').DS.$package['packagefile'];
			}
			JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);

			return $result;
		/*
			end from J1.6
		*/
		}
	}

	/**
	 * Works out an installation package from a HTTP upload
	 *
	 * @return package definition or false on failure
	 */
	protected function _getPackageFromUpload()
	{
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();

		if($joomlaVersion < '1.6')
		{
		// Get the uploaded file information
		$userfile = JRequest::getVar('install_package', null, 'files', 'array' );

		// Make sure that file uploads are enabled in php
		if (!(bool) ini_get('file_uploads')) {
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLFILE'));
			return false;
		}

		// Make sure that zlib is loaded so that the package can be unpacked
		if (!extension_loaded('zlib')) {
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLZLIB'));
			return false;
		}

		// If there is no uploaded file, we have a problem...
		if (!is_array($userfile) ) {
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('No file selected'));
			return false;
		}

		// Check if there was a problem uploading the file.
		if ( $userfile['error'] || $userfile['size'] < 1 )
		{
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLUPLOADERROR'));
			return false;
		}

		// Build the appropriate paths
		$config =& JFactory::getConfig();
		$tmp_dest 	= $config->getValue('config.tmp_path').DS.$userfile['name'];
		$tmp_src	= $userfile['tmp_name'];

		// Move uploaded file
		jimport('joomla.filesystem.file');
		$uploaded = JFile::upload($tmp_src, $tmp_dest);

		// Unpack the downloaded package file
		$package = PagesAndItemsInstallerHelper::unpack($tmp_dest);

		return $package;
		}
		else
		{

		// Get the uploaded file information
		$userfile = JRequest::getVar('install_package', null, 'files', 'array');

		// Make sure that file uploads are enabled in php
		if (!(bool) ini_get('file_uploads')) {
			JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLFILE'));
			return false;
		}

		// Make sure that zlib is loaded so that the package can be unpacked
		if (!extension_loaded('zlib')) {
			JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLZLIB'));
			return false;
		}

		// If there is no uploaded file, we have a problem...
		if (!is_array($userfile)) {
			JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_NO_FILE_SELECTED'));
			return false;
		}

		// Check if there was a problem uploading the file.
		if ($userfile['error'] || $userfile['size'] < 1) {
			JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLUPLOADERROR'));
			return false;
		}

		// Build the appropriate paths
		$config		= JFactory::getConfig();
		$tmp_dest	= $config->get('tmp_path').DS.$userfile['name'];
		$tmp_src	= $userfile['tmp_name'];

		// Move uploaded file
		jimport('joomla.filesystem.file');
		$uploaded = JFile::upload($tmp_src, $tmp_dest);

		// Unpack the downloaded package file
		$package = PagesAndItemsInstallerHelper::unpack($tmp_dest);

		return $package;
		}
		/*
		else
		{
			return parent::_getPackageFromUpload();
		}
		*/
	}

	/**
	 * Install an extension from a directory
	 *
	 * @return	Package details or false on failure
	 * @since	1.5
	 */
	protected function _getPackageFromFolder()
	{
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();

		if($joomlaVersion < '1.6')
		{
		// Get the path to the package to install
		$p_dir = JRequest::getString('install_directory');
		$p_dir = JPath::clean( $p_dir );

		// Did you give us a valid directory?
		if (!is_dir($p_dir)) {
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('Please enter a package directory'));
			return false;
		}

		// Detect the package type
		$type = PagesAndItemsInstallerHelper::detectType($p_dir);

		// Did you give us a valid package?
		if (!$type) {
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('Path does not have a valid package'));
			return false;
		}

		$package['packagefile'] = null;
		$package['extractdir'] = null;
		$package['dir'] = $p_dir;
		$package['type'] = $type;

		return $package;
		}
		else
		{

		// Get the path to the package to install
		$p_dir = JRequest::getString('install_directory');
		$p_dir = JPath::clean($p_dir);

		// Did you give us a valid directory?
		if (!is_dir($p_dir)) {
			JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_PLEASE_ENTER_A_PACKAGE_DIRECTORY'));
			return false;
		}

		// Detect the package type
		$type = PagesAndItemsInstallerHelper::detectType($p_dir);

		// Did you give us a valid package?
		if (!$type) {
			JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_PATH_DOES_NOT_HAVE_A_VALID_PACKAGE'));
			return false;
		}

		$package['packagefile'] = null;
		$package['extractdir'] = null;
		$package['dir'] = $p_dir;
		$package['type'] = $type;

		return $package;
		}

		/*
		else
		{
			return parent::_getPackageFromFolder();
		}
		*/
	}

	/**
	 * Install an extension from a URL
	 *
	 * @return	Package details or false on failure
	 * @since	1.5
	 */
	protected function _getPackageFromUrl()
	{
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();

		if($joomlaVersion < '1.6')
		{
		// Get a database connector
		$db = & JFactory::getDBO();

		// Get the URL of the package to install
		$url = JRequest::getString('install_url');

		// Did you give us a URL?
		if (!$url) {
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('Please enter a URL'));
			return false;
		}

		// Download the package at the URL given
		$p_file = JInstallerHelper::downloadPackage($url);

		// Was the package downloaded?
		if (!$p_file) {
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('Invalid URL'));
			return false;
		}

		$config =& JFactory::getConfig();
		$tmp_dest 	= $config->getValue('config.tmp_path');

		// Unpack the downloaded package file
		$package = PagesAndItemsInstallerHelper::unpack($tmp_dest.DS.$p_file);

		return $package;
		}
		else
		{

		// Get a database connector
		$db = JFactory::getDbo();

		// Get the URL of the package to install
		$url = JRequest::getString('install_url');

		// Did you give us a URL?
		if (!$url) {
			JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_ENTER_A_URL'));
			return false;
		}

		// Download the package at the URL given
		$p_file = JInstallerHelper::downloadPackage($url);

		// Was the package downloaded?
		if (!$p_file) {
			JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_INVALID_URL'));
			return false;
		}

		$config		= JFactory::getConfig();
		$tmp_dest	= $config->get('tmp_path');

		// Unpack the downloaded package file
		$package = PagesAndItemsInstallerHelper::unpack($tmp_dest.DS.$p_file);

		return $package;
		}
		/*
		else
		{
			return parent::_getPackageFromUrl();
		}
		*/
	}

}
