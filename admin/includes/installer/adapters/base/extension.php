<?php
/**
* @version		2.1.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// Check to ensure this file is within the rest of the framework
//defined('JPATH_BASE') or die ();
defined('_JEXEC') or die;
/**

 */
$version = new JVersion();
$joomlaVersion = $version->getShortVersion();
if($joomlaVersion < '1.6')
{
	class InstallerExtension extends JObject
	{
	}
}
else
{
	jimport('joomla.base.adapterinstance');
	class InstallerExtension extends JAdapterInstance
	{
	}
}

class PiInstallerExtension extends InstallerExtension //JObject
{
	/** @var string install function routing */
	var $route = 'install';
	protected $manifest = null;
	protected $manifest_script = null;
	protected $name = null;
	protected $scriptElement = null;
	protected $oldFiles = null;

	//protected $parentParent = null;

	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param	object	$parent	Parent object [JInstaller instance]
	 * @return	void
	 * @since	1.5
	 */
	function __construct( & $parent) //, &$parentParent = null)
	{
		$this->parent = & $parent;
		//$this->parentParent = & $parentParent;
	}


		/**
	 * Custom loadLanguage method
	 *
	 * @param   string  $path the path where to find language files
	 * @since   11.1
	 */
	public function loadLanguage($path=null)
	{
		$source = $this->parent->getPath('source');
		if (!$source) 
		{
			$pathExtensions = realpath(dirname(__FILE__).'/../../../../extensions');
			if(isset($this->parent->extension->folder) && $this->parent->extension->folder)
			{
				$path = str_replace('/',DS,$this->parent->extension->folder);
				$this->parent->setPath('source', $pathExtensions.DS.$this->parent->extension->type.'s'.DS.$this->parent->extension->folder.DS.$this->parent->extension->element);
			}
			else
			{
				$this->parent->setPath('source', $pathExtensions.DS.$this->parent->extension->type.'s'.DS.$this->parent->extension->element);
			}
		}
		
		$this->manifest = $this->parent->getManifest();
		$element = $this->manifest->files;
		if ($element)
		{
			$folder = strtolower((string)$this->manifest->attributes()->folder);
			$type = strtolower((string)$this->manifest->attributes()->type);
			$name = '';
			if (count($element->children()))
			{
				foreach ($element->children() as $file)
				{
					if ((string)$file->attributes()->$type)
					{
						$name = strtolower((string)$file->attributes()->$type);
						break;
					}
				}
			}
			if ($name)
			{
				$pathExtensions = realpath(dirname(__FILE__).'/../../../../extensions');
				if($folder)
				{
					//en-GB.pi_extension_piplugin_indicator_codemirror.ini
					$extension = "pi_extension_${type}_${folder}_${name}";
					$source = $path ? $path : $pathExtensions . "/$type".s."/$folder/$name";
				}
				else
				{
					$extension = "pi_extension_${type}_${name}";
					$source = $path ? $path : $pathExtensions. "/$type".s."/$name";
				}
				$lang = JFactory::getLanguage();
				
				
				$folder = (string)$element->attributes()->folder;
				if ($folder && file_exists("$path/$folder"))
				{
					$source = "$path/$folder";
				}
				$lang->load($extension . '.sys', $source, null, false, false)
					||	$lang->load($extension . '.sys', JPATH_ADMINISTRATOR, null, false, false)
					||	$lang->load($extension . '.sys', $source, $lang->getDefault(), false, false)
					||	$lang->load($extension . '.sys', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)||
				$lang->load($extension, $source, null, false, false)
					||	$lang->load($extension, JPATH_ADMINISTRATOR, null, false, false)
					||	$lang->load($extension, $source, $lang->getDefault(), false, false)
					||	$lang->load($extension, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
			}
		}
	}


	/**
	 * Custom install method
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 * Minor alteration - see below
	 */
	function install()
	{

		// Get a database connector object
		$db = & $this->parent->getDBO();
		// Get the extension manifest object

		$this->manifest = $this->parent->getManifest();
		$xml = $this->manifest;

		/**
		 * ---------------------------------------------------------------------------------------------
		 * Manifest Document Setup Section
		 * ---------------------------------------------------------------------------------------------
		 */


		/*
		J1.6 use not JSimpleXMLElement it use SimpleXML
		// Set the extensions name
		$name = (string)$xml->name;
		$name = JFilterInput::getInstance()->clean($name, 'string');
		$this->set('name', $name);

		// Get the component description


		*/

		// Set the extensions name
		/*
		old use
		$name =& $this->manifest->getElementByPath('name');
		$name = JFilterInput::clean($name->data(), 'string');
		$this->set('name', $name);
		*/
		$name = (string)$xml->name;
		$name = JFilterInput::getInstance()->clean($name, 'string');
		$this->set('name', $name);
		$this->parent->set('name', $name);

		// Get the component description
		/*
		old use
		$description = & $this->manifest->getElementByPath('description');
		$description = JFilterInput::clean($description->data(), 'string');
		if ($description != '')
		{

			$this->parent->set('message', $description);

		}
		else
		{
			$this->parent->set('message', '' );
		}
		*/
		$description = trim((string)$xml->description);
		if ($description)
		{
			$this->parent->set('message', JText::_($description));
		}
		else
		{
			$this->parent->set('message', '');
		}

		//$type = $this->manifest->attributes('type');
		$type = (string)$xml->attributes()->type;

		//$folder = $this->manifest->attributes('folder');
		$folder = (string)$xml->attributes()->folder;

		/*
		$version = & $this->manifest->getElementByPath('version');
		if($version)
		{
			$version = JFilterInput::clean($version->data(), 'string');
		}
		else
		{
			$version = '';
		}
		*/
		$version = (string)$xml->version;

		/*
		if($version)
		{
			//$version = JFilterInput::clean($version->data(), 'string');
		}
		else
		{
			$version = '';
		}
		*/
		$required_version = (string)$xml->required_version;
		/*
		$required_version = & $this->manifest->getElementByPath('required_version');
		*/
		//ms: add 14.11.2011
		if($required_version)
		{
			//here we check the $required_version
			if(PagesAndItemsHelper::getPagesAndItemsVersion() < $required_version)
			{
				$this->parent->abort('Extension Install: '.JText::_('COM_PAGESANDITEMS_EXTENSION_VERSION_NOT_OK').$required_version);
				return false;
			}
		}
		else
		{
			//$required_version = '';
		}
		/*
		*/

		// Set the installation path
		/*
		$element =& $this->manifest->getElementByPath('files');
		if (is_a($element, 'JSimpleXMLElement') && count($element->children()))
		{
			$files = $element->children();
			foreach ($files as $file) {
				if ($file->attributes($type)) {
					$pname = $file->attributes($type);
					break;
				}
			}
		}
		*/

		// Set the installation path
		if (count($xml->files->children()))
		{
			foreach ($xml->files->children() as $file)
			{
				if ((string)$file->attributes()->$type)
				{
					$pname = (string)$file->attributes()->$type;
					break;
				}
			}
		}

		if (! empty($type) && !empty($pname))
		{
			if(isset($folder))
			{
				$path = str_replace('/',DS,$folder);
				$this->parent->setPath('extension_root', COM_PAGESANDITEMS_INSTALLER_PATH.DS.$type.'s'.DS.$folder.DS.$pname);
			}
			else
			{
				$this->parent->setPath('extension_root', COM_PAGESANDITEMS_INSTALLER_PATH.DS.$type.'s'.DS.$pname);
			}
		}
		else
		{
			$this->parent->abort('Extension Install: '.JText::_('No type file specified'));
			return false;
		}


		/*
		We want that only core extensions can have an version == 'integrated'
		and only if install from component install
		*/
		if($version == 'integrated' && !defined('COM_PAGESANDITEMS_COMPONENT_INSTALL'))
		{
			$this->parent->abort('Extension Install: '.JText::_('COM_PAGESANDITEMS_NO_CORE'));
			return false;
		}

		/**
		 * ---------------------------------------------------------------------------------------------
		 * Filesystem Processing Section
		 * ---------------------------------------------------------------------------------------------
		 */
		// If the extension directory does not exist, lets create it
		$created = false;
		if (!file_exists($this->parent->getPath('extension_root')))
		{
			if (!$created = JFolder::create($this->parent->getPath('extension_root')))
			{
				$this->parent->abort($type.' Install: '.JText::_('Failed to create directory').': "'.$this->parent->getPath('extension_root').'"');
				return false;
			}
		}
		// Set overwrite flag if not set by Manifest
		//$method = $this->manifest->attributes('method');
		$method = (string)$xml->attributes()->method;
		switch(strtolower($method))
		{
			case 'upgrade':
			$this->parent->setOverwrite(true);
			$upgrade = true;
			break;

			default:
			$this->parent->setOverwrite(false);
			$upgrade = false;
			break;
		}
		/*
		 * If we created the extension directory and will want to remove it if we
		 * have to roll back the installation, lets add it to the installation
		 * step stack
		 */
		if ($created)
		{
			$this->parent->pushStep( array ('type'=>'folder', 'path'=>$this->parent->getPath('extension_root')));
		}


		// Copy all necessary files
		//$files = & $this->manifest->getElementByPath('files');
		$files = $this->manifest->files;
		if ($this->parent->parseFiles($files, -1) === false)
		{
			// Install failed, roll back changes
			$this->parent->abort(JText::_($type).' '.JText::_('Install').': '.JText::_('Could not copy extensions files.'));
			return false;
		}
		// Parse optional tags -- language files for plugins

		$this->parent->addLanguage($this->manifest); //,$this->parentParent);

		//$this->parent->parseLanguages($this->manifest->languages);
		//$this->parent->parseLanguages($this->manifest->administration->languages, 1);

		//how we install the extension language?
		/*
			$package = Array();
			$package['dir'] = $this->parent->getPath('source');
			$package['type'] = 'language';
			$tmpInstaller = new PagesAndItemsInstaller();
			JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
			$files = JFolder::files(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'installer'.DS.'adapters','.php$');
			foreach($files as $file)
			{
				$name = JFile::stripExt($file);
				require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'installer'.DS.'adapters'.DS.strtolower($name.'.php'));

				$class = 'JInstaller'.ucfirst($name);
				if (class_exists($class))
				{
					$adapter = new $class($tmpInstaller);
					$tmpInstaller->setAdapter($name, $adapter);
				}
			}
			if (!$tmpInstaller->install($package['dir']))
			{
				//$this->parent->abort(JText::sprintf('JLIB_INSTALLER_ABORT_PACK_INSTALL_ERROR_EXTENSION', JText::_('JLIB_INSTALLER_'. strtoupper($this->route)), basename($file)));
				//return false;
			}

		ore over
		$this->parent->addLanguage($xml); //?
		*/

		// If there is an install file, lets copy it.
		//$installScriptElement = & $this->manifest->getElementByPath('installfile');
		$installScriptElement = (string)$this->manifest->installfile;
		//if (is_a($installScriptElement, 'JSimpleXMLElement') || is_a($installScriptElement, 'JXMLElement'))
		if($installScriptElement)
		{
			// Make sure it hasn't already been copied (this would be an error in the xml install file)
			//if (!file_exists($this->parent->getPath('extension_root').DS.$installScriptElement->data()))
			if (!file_exists($this->parent->getPath('extension_root').DS.$installScriptElement))
			{
				//$path['src'] = $this->parent->getPath('source').DS.$installScriptElement->data();
				//$path['dest'] = $this->parent->getPath('extension_root').DS.$installScriptElement->data();
				$path['src'] = $this->parent->getPath('source').DS.$installScriptElement;
				$path['dest'] = $this->parent->getPath('extension_root').DS.$installScriptElement;
				if (!$this->parent->copyFiles( array ($path))) {
					// Install failed, rollback changes
					$this->parent->abort(JText::_($type).' '.JText::_('Install').': '.JText::_('Could not copy PHP install file.'));
					return false;
				}
			}
			//$this->set('install.script', $installScriptElement->data());
			$this->set('install.script', $installScriptElement);
		}
		// If there is an uninstall file, lets copy it.
		$uninstallfileScriptElement = (string)$this->manifest->uninstallfile;
		//$uninstallScriptElement = & $this->manifest->getElementByPath('uninstallfile');
		//if (is_a($uninstallScriptElement, 'JSimpleXMLElement') || is_a($uninstallScriptElement, 'JXMLElement'))
		if ($uninstallfileScriptElement)
		{
			// Make sure it hasn't already been copied (this would be an error in the xml install file)
			//if (!file_exists($this->parent->getPath('extension_root').DS.$uninstallScriptElement->data()))
			if (!file_exists($this->parent->getPath('extension_root').DS.$uninstallfileScriptElement))
			{
				//$path['src'] = $this->parent->getPath('source').DS.$uninstallScriptElement->data();
				//$path['dest'] = $this->parent->getPath('extension_root').DS.$uninstallScriptElement->data();
				$path['src'] = $this->parent->getPath('source').DS.$uninstallfileScriptElement;
				$path['dest'] = $this->parent->getPath('extension_root').DS.$uninstallfileScriptElement;
				if (!$this->parent->copyFiles( array ($path))) {
					// Install failed, rollback changes
					$this->parent->abort(JText::_($type).' '.JText::_('Install').': '.JText::_('Could not copy PHP uninstall file.'));
					return false;
				}
			}
		}
		/**
		 * ---------------------------------------------------------------------------------------------
		 * Database Processing Section
		 * ---------------------------------------------------------------------------------------------
		 */
		// Check to see if a plugin by the same name is already installed
		// TODO CHECK
		$query = 'SELECT extension_id'
		.' FROM #__pi_extensions'
		.' WHERE element = '.$db->Quote($pname)
		//or .' WHERE name = '.$db->Quote($plugin)
		.' AND folder = '.$db->Quote($folder)
		.' AND type='.$db->Quote($type)
		;
		$db->setQuery($query);
		if (!$db->Query())
		{
			// Install failed, roll back changes
			$this->parent->abort($type.' Install: '.$db->stderr(true));
			return false;
		}
		$id = $db->loadResult();
		$row = & JTable::getInstance('piextension', 'PagesAndItemsTable');
		if ($id)
		{
			if (!$this->parent->getOverwrite())
			{
				// Install failed, roll back changes
				$this->parent->abort($type.' Install: '.JText::_($type).' "'.$pname.'" '.JText::_('already exists!'));
				return false;
			}
			else
			{

				$row->load($id);
				if(!$row->params)
				{
					//$row->params = $this->getParams();
					$row->params = $this->parent->getParams();
				}
				else
				{
					$row_params = json_decode($row->params);
					$params = json_decode($this->parent->getParams());
					$change = false;
					foreach($params as $key => $value)
					{
						if(!isset($row_params->$key))
						{
							$row_params->$key = $value;
							$change = true;
						}
					}
					if($change)
					{
						$row->params = json_encode($row_params);
					}
				}
				// before we have change a plugin item, we add it to the installation step stack
				// so that if we have to rollback the changes we can undo it.
				//$this->parent->pushStep( array ('type'=>'plugin_row', 'row'=>$row));
				//$row_params = json_decode($row->params);
			}
			$row_extension_id = false;
		}
		else
		{
			$where = 'folder = '.$db->Quote($folder). ' AND type = '.$db->Quote($type);
			$row->ordering = $row->getNextOrder($where);
			//$row->params = $this->getParams();
			$row->params = $this->parent->getParams();

			// here we will set an id
			if($version == 'integrated' && defined('COM_PAGESANDITEMS_COMPONENT_INSTALL'))
			{
				//$where = 'folder = '.$db->Quote($folder). ' AND type = '.$db->Quote($type). ' And version = '.$db->Quote('integrated');
				$where = 'type = '.$db->Quote($type). ' And version = '.$db->Quote('integrated');
				$row_extension_id = $row->getNextTypeId($where,$type);

				$db->setQuery( "INSERT INTO #__pi_extensions SET extension_id='$row_extension_id' ");
				if(!$db->query())
				{
					// Install failed, roll back changes
					$this->parent->abort($type.' Install: '.$db->stderr(true));
					return false;
				}
				$row->extension_id = $row_extension_id;
			}



		}

		$row->name = $name;
		$row->folder = $folder;
		$row->element = $pname;
		$row->version = $version;
		$row->required_version = $required_version;
		$row->description = $description;
		$row->type = $type;


		//$row->manifest_cache = $this->generateManifestCache(); //??
		$row->manifest_cache = $this->parent->generateManifestCache(); //??
		/*
		$manifest_details = JApplicationHelper::parseXMLInstallFile($manifestPath);
		$this->parent->extension->manifest_cache = serialize($manifest_details);
		*/
		//TODO row->params and row->params_fields
		if (!$row->store())
		{
			// Install failed, roll back changes
			$this->parent->abort($type.' Install: '.$db->stderr(true));
			return false;
		}
		/*
		if($row_extension_id)
		{
			//we must change the extension_id here
			$db->setQuery( "UPDATE #__pi_extensions SET extension_id='$row_extension_id' WHERE extension_id='$row->extension_id' ");
			$db->query();
			$row->extension_id = $row_extension_id;
		}
		*/
		// Since we have created a plugin item, we add it to the installation step stack
		// so that if we have to rollback the changes we can undo it.
		if(!$id)
		{
			$this->parent->pushStep( array ('type'=>$type, 'id'=>$row->extension_id));
		}

		/**
		 * ---------------------------------------------------------------------------------------------
		 * Finalization and Cleanup Section
		 * ---------------------------------------------------------------------------------------------
		 */
		// Lastly, we will copy the manifest file to its appropriate place.
		if (!$this->parent->copyManifest(-1))
		{
			// Install failed, rollback changes
			$this->parent->abort($type.' Install: '.JText::_('Could not copy setup file'));
			return false;
		}
		/*
		 * If we have an install script, lets include it, execute the custom
		 * install method, and append the return value from the custom install
		 * method to the installation message.
		 */

		if ($this->get('install.script'))
		{
			if (is_file($this->parent->getPath('extension_root').DS.$this->get('install.script'))) {
				ob_start();
				ob_implicit_flush(false);
				require_once ($this->parent->getPath('extension_root').DS.$this->get('install.script'));
				if (function_exists($type.'_install'))
				{
					$type_install = $type.'_install';
					if($type_install() === false)
					{
						$this->parent->abort(JText::_($type).' '.JText::_('Install').': '.JText::_('Custom install routine failure'));
						return false;
					}
				}
				$msg = ob_get_contents();
				ob_end_clean();
				if ($msg != '')
				{
					$this->parent->set('extension.message', $msg);
				}
			}
		}

		//TODO ms: remove if getItemtypes changed
		/*
		if(!$id && $type == 'itemtype')
		{
			// if($type == 'itemtype') we must add in pi_config
			$componentPath = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'..'.DS.'helpers'.DS.'pagesanditems.php');
			if(file_exists($componentPath))
			{
				require_once ($componentPath);
				PagesAndItemsHelper::changeConfigItemtype(null, $row->element, 'add');
			}
		}
		*/
		return true;
	}

	/**
	 * Custom uninstall method
	 *
	 * @access	public
	 * @param	int		$cid	The id of the plugin to uninstall
	 * @param	int		$clientId	The id of the client (unused)
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function uninstall($id, $clientId = null)
	{
		// Initialize variables
		$row = null;
		$retval = true;
		$db = & $this->parent->getDBO();
		// First order of business will be to load the module object table from the database.
		// This should give us the necessary information to proceed.
		// ^ Changes to plugin parameters. Use JCEPluginsTable class.
		$row = & JTable::getInstance('piextension', 'PagesAndItemsTable');
		$row->load((int)$id);
		// Get the plugin folder so we can properly build the plugin path
		/*
		if (trim($row->folder) == '')
		{
			JError::raiseWarning(100, 'Fieldtype Uninstall: '.JText::_('Plugin field empty, cannot remove files'));
			return false;
		}
		*/

		// Set the plugin root path
		// TODO CHECK
		if($row->folder)
		{
			$path = $row->type.'s'.DS.str_replace('/',DS,$row->folder);
		}
		else
		{
			$path = $row->type.'s';
		}
		$this->parent->setPath('extension_root',COM_PAGESANDITEMS_INSTALLER_PATH.DS.$path.DS.$row->element);//.DS.$row->element;
		//$this->parent->setPath('extension_root', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ginkgo'.DS.'plugins'.DS.$row->name);
		$manifestFile = $this->parent->getPath('extension_root').DS.$row->element.'.xml';
		if (file_exists($manifestFile))
		{
			libxml_use_internal_errors(true);

			//$xml = & JFactory::getXMLParser('Simple');
			// If we cannot load the xml file return null
			//if (!$xml->loadFile($manifestFile))
			if (!$xml = simplexml_load_file($manifestFile))
			{
				JError::raiseWarning(100, $row->type.' Uninstall: '.JText::_('Could not load manifest file'));
				return false;
			}
			/*
			 * Check for a valid XML root tag.
			 * @todo: Remove backwards compatability in a future version
			 * Should be 'install', but for backward compatability we will accept 'mosinstall'.
			 */
			//$type = (string)$xml->attributes()->type;
			$root = & $xml->document;
			/*
			if ($root->name() != 'extension' && $root->name() != 'install')
			{
				JError::raiseWarning(100, $row->type.' Uninstall: '.JText::_('Invalid manifest file'));
				return false;
			}
			*/
			if ( !is_object($xml) || (!is_object($xml->install) && !is_object($xml->extension)) )
			{
				JError::raiseWarning(100, $row->type.' Uninstall: '.JText::_('Invalid manifest file'));
				unset($xml);
				return false;
			}

			// Remove the plugin files
			//$this->parent->removeFiles($root->getElementByPath('files'), -1);
			$this->parent->removeFiles($xml->files,-1);
			JFile::delete($manifestFile);
			// Remove all media and languages as well
			//$this->parent->removeFiles($root->getElementByPath('languages'), 0);


			//$this->parent->removeFiles($xml->languages, 0);
			//TODO remove language
			$this->parent->removeLanguage($xml);


			/**
			 * ---------------------------------------------------------------------------------------------
			 * Custom Uninstallation Script Section
			 * ---------------------------------------------------------------------------------------------
			 */
			// Now lets load the uninstall file if there is one and execute the uninstall function if it exists.
			//$uninstallfileElement = & $root->getElementByPath('uninstallfile');
			//if (is_a($uninstallfileElement, 'JSimpleXMLElement') || is_a($uninstallfileElement, 'JXMLElement'))
			$uninstallfileScriptElement = (string)$root->uninstallfile;
			if ($uninstallfileScriptElement)
			{

				// Element exists, does the file exist?
				//if (is_file($this->parent->getPath('extension_root').DS.$uninstallfileElement->data()))
				if (is_file($this->parent->getPath('extension_root').DS.$uninstallfileElement))
				{
					ob_start();
					ob_implicit_flush(false);
					//require_once ($this->parent->getPath('extension_root').DS.$uninstallfileElement->data());
					require_once ($this->parent->getPath('extension_root').DS.$uninstallfileElement);
					if (function_exists($row->type.'_uninstall'))
					{
						if (com_uninstall() === false)
						{
							JError::raiseWarning(100, JText::_($row->type).' '.JText::_('Uninstall').': '.JText::_('Custom Uninstall routine failure'));
							$retval = false;
						}
					}
					$msg = ob_get_contents();
					ob_end_clean();
					if ($msg != '') {
						$this->parent->set('extension.message', $msg);
					}
				}
			}

			/*// Get the plugin description

			$description = $root->getElementByPath('description');
			if (is_a($description, 'JSimpleXMLElement') || is_a($description, 'JXMLElement'))
			{
				$this->parent->set('message', $description->data());
			}
			else
			{
				$this->parent->set('message', '');
			}
			*/
			$description = (string)$root->description;
			if ($description)
			{
				$this->parent->set('message', JText::_($description));
			}
			else
			{
				$this->parent->set('message', '');
			}
			// Now we will no longer need the plugin object, so lets delete it
			$row->delete($row->extension_id);
			unset ($row);
		}
		else
		{
			JError::raiseWarning(100, 'Plugin Uninstall: Manifest File invalid or not found. Plugin entry removed from database.');
			$row->delete($row->extension_id);
			unset ($row);
			$retval = false;
		}
		// If the folder is empty, let's delete it
		$files = JFolder::files($this->parent->getPath('extension_root'));
		if (!count($files)) {
			JFolder::delete($this->parent->getPath('extension_root'));
		}
		
		//TODO ms: remove if getItemtypes changed
		/*
		if($row->type == 'itemtype')
		{
			if(file_exists(realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'..'.DS.'helpers'.DS.'pagesanditems.php')))
			{
				require_once (realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'..'.DS.'helpers'.DS.'pagesanditems.php'));
				PagesAndItemsHelper::changeConfigItemtype(null, $row->element, 'remove');
			}
		}
		*/
		return $retval;
	}

	/**
	 * Custom rollback method
	 * 	- Roll back the plugin item
	 *
	 * @access	public
	 * @param	array	$arg	Installation step to rollback
	 * @return	boolean	True on success
	 * @since	1.5
	 * Minor changes to the db query
	 */
	function _rollback_plugin($arg)
	{
		// Get database connector object
		$db = & $this->parent->getDBO();
		// Remove the entry from the #__jce_plugins table
		$query = 'DELETE'.
		' FROM `#__pi_extensions`'.
		' WHERE extension_id='.$db->Quote((int)$arg['id']);
		$db->setQuery($query);
		return ($db->query() !== false);
	}


	public function XXXgenerateManifestCache()
	{

		return serialize($this->parseXMLInstallFile($this->parent->getPath('manifest')));
	}


	function XXXparseXMLInstallFile($path)
	{
		// Read the file to see if it's a valid component XML file
		$xml = simplexml_load_file($path);

		if (!$xml)
		{
			unset($xml);
			return false;
		}

		if ( !is_object($xml) || (!is_object($xml->install) && !is_object($xml->extension)) )
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







	public function XgetParams()
	{
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();

		if($joomlaVersion < '1.6')
		{
			// Get the manifest document root element
			$root = $this->manifest;//& $this->_manifest->document;
			// Get the element of the tag names
			$element =& $root->getElementByPath('params');
			if (!is_a($element, 'JSimpleXMLElement') || !count($element->children()))
			{
				// Either the tag does not exist or has no children therefore we return zero files processed.
				return null;
			}
			// Get the array of parameter nodes to process
			$params = $element->children();
			if (count($params) == 0)
			{
				// No params to process
				return null;
			}
			// Process each parameter in the $params array.
			//$ini = null;
			$ini = array();
			foreach ($params as $param)
			{
				if (!$name = $param->attributes('name'))
				{
					continue;
				}
				if (!$value = $param->attributes('default'))
				{
					continue;
				}
				$ini[(string) $name] = (string) $value;
			}
			return json_encode($ini);
		}
		else
		{
			//Joomla 1.6
			// Validate that we have a param to use
			if(!isset($this->manifest->params->param))
			{
				return '{}';
			}
			// Getting the fieldset tags
			$fieldsets = $this->manifest->params->param;

			// Creating the data collection variable:
			$ini = array();

			// Iterating through the fieldsets:
			foreach($fieldsets as $fieldset)
			{
				if( ! count($fieldset->children()))
				{
					// Either the tag does not exist or has no children therefore we return zero files processed.
					return null;
				}

				// Iterating through the fields and collecting the name/default values:
				foreach ($fieldset as $field)
				{
					// Modified the below if statements to check against the
						// null value since default values like "0" were casuing
					// entire parameters to be skipped.
					if (($name = $field->attributes()->name) === null)
					{
						continue;
					}

					if (($value = $field->attributes()->default) === null)
					{
						continue;
					}
					$ini[(string) $name] = (string) $value;
				}
			}
			return json_encode($ini);
		}

	}

	/**
	 * Custom install method
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 * Minor alteration - see below
	 */
	function Xinstall()
	{
		// Get a database connector object
		$db = & $this->parent->getDBO();
		// Get the extension manifest object
		$manifest = & $this->parent->getManifest();
		$this->manifest = & $manifest->document;
		/*
		J1.6
		// Get the extension manifest object
		$this->manifest = $this->parent->getManifest();
		$xml = $this->manifest;

		*/
		/**
		 * ---------------------------------------------------------------------------------------------
		 * Manifest Document Setup Section
		 * ---------------------------------------------------------------------------------------------
		 */


		/*
		J1.6 use not JSimpleXMLElement it use SimpleXML
		// Set the extensions name
		$name = (string)$xml->name;
		$name = JFilterInput::getInstance()->clean($name, 'string');
		$this->set('name', $name);

		// Get the component description
		$description = (string)$xml->description;
		if ($description) {
			$this->parent->set('message', JText::_($description));
		}
		else {
			$this->parent->set('message', '');
		}

		*/

		// Set the extensions name
		$name =& $this->manifest->getElementByPath('name');
		$name = JFilterInput::clean($name->data(), 'string');
		$this->set('name', $name);
		$this->parent->set('name', $name);
		// Get the component description

		$description = & $this->manifest->getElementByPath('description');
		$description = JFilterInput::clean($description->data(), 'string');
		if ($description != '')
		{

			$this->parent->set('message', $description);

		}
		else
		{
			$this->parent->set('message', '' );
		}
		$type = $this->manifest->attributes('type');
		$folder = $this->manifest->attributes('folder');
		$version = & $this->manifest->getElementByPath('version');
		if($version)
		{
			$version = JFilterInput::clean($version->data(), 'string');
		}
		else
		{
			$version = '';
		}
		$required_version = & $this->manifest->getElementByPath('required_version');
		if($required_version)
		{
			$required_version = JFilterInput::clean($required_version->data(), 'string');
			
			//abort
		}
		else
		{
			$required_version = '';
		}
		// Set the installation path
		$element =& $this->manifest->getElementByPath('files');
		if (is_a($element, 'JSimpleXMLElement') && count($element->children()))
		{
			$files = $element->children();
			foreach ($files as $file) {
				if ($file->attributes($type)) {
					$pname = $file->attributes($type);
					break;
				}
			}
		}

		$files = & $this->manifest->getElementByPath('files');
		if (! empty($type) && !empty($pname))
		{
			if(isset($folder))
			{
				$path = str_replace('/',DS,$folder);
				$this->parent->setPath('extension_root', COM_PAGESANDITEMS_INSTALLER_PATH.DS.$type.'s'.DS.$folder.DS.$pname);
			}
			else
			{
				$this->parent->setPath('extension_root', COM_PAGESANDITEMS_INSTALLER_PATH.DS.$type.'s'.DS.$pname);
			}
		}
		else
		{
			$this->parent->abort('Extension Install: '.JText::_('No fieldtype file specified'));
			return false;
		}
		/**
		 * ---------------------------------------------------------------------------------------------
		 * Filesystem Processing Section
		 * ---------------------------------------------------------------------------------------------
		 */
		// If the extension directory does not exist, lets create it
		$created = false;
		if (!file_exists($this->parent->getPath('extension_root')))
		{
			if (!$created = JFolder::create($this->parent->getPath('extension_root')))
			{
				$this->parent->abort($type.' Install: '.JText::_('Failed to create directory').': "'.$this->parent->getPath('extension_root').'"');
				return false;
			}
		}
		// Set overwrite flag if not set by Manifest
		$method = $this->manifest->attributes('method');
		switch(strtolower($method))
		{
			case 'upgrade':
			$this->parent->setOverwrite(true);
			$upgrade = true;
			break;

			default:
			$this->parent->setOverwrite(false);
			$upgrade = false;
			break;
		}
		/*
		 * If we created the extension directory and will want to remove it if we
		 * have to roll back the installation, lets add it to the installation
		 * step stack
		 */
		if ($created)
		{
			$this->parent->pushStep( array ('type'=>'folder', 'path'=>$this->parent->getPath('extension_root')));
		}
		// Copy all necessary files
		if ($this->parent->parseFiles($files, -1) === false)
		{
			// Install failed, roll back changes
			$this->parent->abort();
			return false;
		}
		// Parse optional tags -- language files for plugins
		$this->parent->parseLanguages($this->manifest->getElementByPath('languages'), 0);
		// If there is an install file, lets copy it.
		$installScriptElement = & $this->manifest->getElementByPath('installfile');
		if (is_a($installScriptElement, 'JSimpleXMLElement') || is_a($installScriptElement, 'JXMLElement'))
		{
			// Make sure it hasn't already been copied (this would be an error in the xml install file)
			if (!file_exists($this->parent->getPath('extension_root').DS.$installScriptElement->data()))
			{
				$path['src'] = $this->parent->getPath('source').DS.$installScriptElement->data();
				$path['dest'] = $this->parent->getPath('extension_root').DS.$installScriptElement->data();
				if (!$this->parent->copyFiles( array ($path))) {
					// Install failed, rollback changes
					$this->parent->abort(JText::_($type).' '.JText::_('Install').': '.JText::_('Could not copy PHP install file.'));
					return false;
				}
			}
			$this->set('install.script', $installScriptElement->data());
		}
		// If there is an uninstall file, lets copy it.
		$uninstallScriptElement = & $this->manifest->getElementByPath('uninstallfile');
		if (is_a($uninstallScriptElement, 'JSimpleXMLElement') || is_a($uninstallScriptElement, 'JXMLElement'))
		{
			// Make sure it hasn't already been copied (this would be an error in the xml install file)
			if (!file_exists($this->parent->getPath('extension_root').DS.$uninstallScriptElement->data()))
			{
				$path['src'] = $this->parent->getPath('source').DS.$uninstallScriptElement->data();
				$path['dest'] = $this->parent->getPath('extension_root').DS.$uninstallScriptElement->data();
				if (!$this->parent->copyFiles( array ($path))) {
					// Install failed, rollback changes
					$this->parent->abort(JText::_($type).' '.JText::_('Install').': '.JText::_('Could not copy PHP uninstall file.'));
					return false;
				}
			}
		}
		/**
		 * ---------------------------------------------------------------------------------------------
		 * Database Processing Section
		 * ---------------------------------------------------------------------------------------------
		 */
		// Check to see if a plugin by the same name is already installed
		// TODO CHECK
		$query = 'SELECT extension_id'
		.' FROM #__pi_extensions'
		.' WHERE element = '.$db->Quote($pname)
		//or .' WHERE name = '.$db->Quote($plugin)
		.' AND folder = '.$db->Quote($folder)
		.' AND type='.$db->Quote($type)
		;
		$db->setQuery($query);
		if (!$db->Query())
		{
			// Install failed, roll back changes
			$this->parent->abort($type.' Install: '.$db->stderr(true));
			return false;
		}
		$id = $db->loadResult();
		$row = & JTable::getInstance('piextension', 'PagesAndItemsTable');
		if ($id)
		{
			if (!$this->parent->getOverwrite())
			{
				// Install failed, roll back changes
				$this->parent->abort($type.' Install: '.JText::_($type).' "'.$pname.'" '.JText::_('already exists!'));
				return false;
			}
			else
			{

				$row->load($id);
				if(!$row->params)
				{
					$row->params = $this->getParams();
				}
				// before we have change a plugin item, we add it to the installation step stack
				// so that if we have to rollback the changes we can undo it.
				//$this->parent->pushStep( array ('type'=>'plugin_row', 'row'=>$row));
				//$row_params = json_decode($row->params);
			}
		}
		else
		{
			$where = 'folder = '.$db->Quote($folder). ' AND type = '.$db->Quote($type);
			$row->ordering = $row->getNextOrder($where);
			$row->params = $this->getParams();
			//$row->params = json_encode($params);
			//$row->params_fields = json_encode($params_fields);
			//$row->params = $this->parent->getParams();
		}

		$row->name = $name;
		$row->folder = $folder;
		$row->element = $pname;
		$row->version = $version;
		$row->required_version = $required_version;
		$row->description = $description;
		$row->type = $type;


		$row->manifest_cache = $this->generateManifestCache(); //??
		/*
		$manifest_details = JApplicationHelper::parseXMLInstallFile($manifestPath);
		$this->parent->extension->manifest_cache = serialize($manifest_details);
		*/
		//TODO row->params and row->params_fields
		if (!$row->store())
		{
			// Install failed, roll back changes
			$this->parent->abort($type.' Install: '.$db->stderr(true));
			return false;
		}
		// Since we have created a plugin item, we add it to the installation step stack
		// so that if we have to rollback the changes we can undo it.
		if(!$id)
		{
			$this->parent->pushStep( array ('type'=>$type, 'id'=>$row->extension_id));
		}

		/**
		 * ---------------------------------------------------------------------------------------------
		 * Finalization and Cleanup Section
		 * ---------------------------------------------------------------------------------------------
		 */
		// Lastly, we will copy the manifest file to its appropriate place.
		if (!$this->parent->copyManifest(-1))
		{
			// Install failed, rollback changes
			$this->parent->abort($type.' Install: '.JText::_('Could not copy setup file'));
			return false;
		}
		/*
		 * If we have an install script, lets include it, execute the custom
		 * install method, and append the return value from the custom install
		 * method to the installation message.
		 */

		if ($this->get('install.script'))
		{
			if (is_file($this->parent->getPath('extension_root').DS.$this->get('install.script'))) {
				ob_start();
				ob_implicit_flush(false);
				require_once ($this->parent->getPath('extension_root').DS.$this->get('install.script'));
				if (function_exists($type.'_install'))
				{
					$type_install = $type.'_install';
					if($type_install() === false)
					{
						$this->parent->abort(JText::_($type).' '.JText::_('Install').': '.JText::_('Custom install routine failure'));
						return false;
					}
				}
				$msg = ob_get_contents();
				ob_end_clean();
				if ($msg != '')
				{
					$this->parent->set('extension.message', $msg);
				}
			}
		}
		return true;
	}

	/**
	 * Custom uninstall method
	 *
	 * @access	public
	 * @param	int		$cid	The id of the plugin to uninstall
	 * @param	int		$clientId	The id of the client (unused)
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function Xuninstall($id, $clientId)
	{
		// Initialize variables
		$row = null;
		$retval = true;
		$db = & $this->parent->getDBO();
		// First order of business will be to load the module object table from the database.
		// This should give us the necessary information to proceed.
		// ^ Changes to plugin parameters. Use JCEPluginsTable class.
		$row = & JTable::getInstance('piextension', 'PagesAndItemsTable');
		$row->load((int)$id);
		// Get the plugin folder so we can properly build the plugin path
		/*
		if (trim($row->folder) == '')
		{
			JError::raiseWarning(100, 'Fieldtype Uninstall: '.JText::_('Plugin field empty, cannot remove files'));
			return false;
		}
		*/

		// Set the plugin root path
		// TODO CHECK
		if($row->folder)
		{
			$path = $row->type.'s'.DS.str_replace('/',DS,$row->folder);
		}
		else
		{
			$path = $row->type.'s';
		}
		$this->parent->setPath('extension_root',COM_PAGESANDITEMS_INSTALLER_PATH.DS.$path.DS.$row->element);//.DS.$row->element;
		//$this->parent->setPath('extension_root', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ginkgo'.DS.'plugins'.DS.$row->name);
		$manifestFile = $this->parent->getPath('extension_root').DS.$row->element.'.xml';
		if (file_exists($manifestFile))
		{
			$xml = & JFactory::getXMLParser('Simple');
			// If we cannot load the xml file return null
			if (!$xml->loadFile($manifestFile)) {
				JError::raiseWarning(100, $row->type.' Uninstall: '.JText::_('Could not load manifest file'));
				return false;
			}
			/*
			 * Check for a valid XML root tag.
			 * @todo: Remove backwards compatability in a future version
			 * Should be 'install', but for backward compatability we will accept 'mosinstall'.
			 */
			$root = & $xml->document;
			if ($root->name() != 'extension' && $root->name() != 'install') {
				JError::raiseWarning(100, $row->type.' Uninstall: '.JText::_('Invalid manifest file'));
				return false;
			}
			// Remove the plugin files
			$this->parent->removeFiles($root->getElementByPath('files'), -1);
			JFile::delete($manifestFile);
			// Remove all media and languages as well
			$this->parent->removeFiles($root->getElementByPath('languages'), 0);
			/**
			 * ---------------------------------------------------------------------------------------------
			 * Custom Uninstallation Script Section
			 * ---------------------------------------------------------------------------------------------
			 */
			// Now lets load the uninstall file if there is one and execute the uninstall function if it exists.
			$uninstallfileElement = & $root->getElementByPath('uninstallfile');
			if (is_a($uninstallfileElement, 'JSimpleXMLElement') || is_a($uninstallfileElement, 'JXMLElement'))
			{
				// Element exists, does the file exist?
				if (is_file($this->parent->getPath('extension_root').DS.$uninstallfileElement->data())) {
					ob_start();
					ob_implicit_flush(false);
					require_once ($this->parent->getPath('extension_root').DS.$uninstallfileElement->data());
					if (function_exists($row->type.'_uninstall'))
					{
						if (com_uninstall() === false)
						{
							JError::raiseWarning(100, JText::_($row->type).' '.JText::_('Uninstall').': '.JText::_('Custom Uninstall routine failure'));
							$retval = false;
						}
					}
					$msg = ob_get_contents();
					ob_end_clean();
					if ($msg != '') {
						$this->parent->set('extension.message', $msg);
					}
				}
			}

			// Get the plugin description
			$description = $root->getElementByPath('description');
			if (is_a($description, 'JSimpleXMLElement') || is_a($description, 'JXMLElement'))
			{
				$this->parent->set('message', $description->data());
			}
			else
			{
				$this->parent->set('message', '');
			}
			// Now we will no longer need the plugin object, so lets delete it
			$row->delete($row->extension_id);
			unset ($row);
		}
		else
		{
			JError::raiseWarning(100, 'Plugin Uninstall: Manifest File invalid or not found. Plugin entry removed from database.');
			$row->delete($row->extension_id);
			unset ($row);
			$retval = false;
		}
		// If the folder is empty, let's delete it
		$files = JFolder::files($this->parent->getPath('extension_root'));
		if (!count($files)) {
			JFolder::delete($this->parent->getPath('extension_root'));
		}
		return $retval;
	}
	public function XgetParamsX()
	{
		// Validate that we have a param to use
		if(!isset($this->manifest->params->param))
		{
			return '{}';
		}
		// Getting the fieldset tags
		$fieldsets = $this->manifest->params->param;

		// Creating the data collection variable:
		$ini = array();

		// Iterating through the fieldsets:
		foreach($fieldsets as $fieldset)
		{
			if( ! count($fieldset->children()))
			{
				// Either the tag does not exist or has no children therefore we return zero files processed.
				return null;
			}

			// Iterating through the fields and collecting the name/default values:
			foreach ($fieldset as $field)
			{
				// Modified the below if statements to check against the
				// null value since default values like "0" were casuing
				// entire parameters to be skipped.
				if (($name = $field->attributes()->name) === null)
				{
					continue;
				}

				if (($value = $field->attributes()->default) === null)
				{
					continue;
				}
				$ini[(string) $name] = (string) $value;
			}
		}
		return json_encode($ini);
	}

}
