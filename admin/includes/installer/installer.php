<?php
/**
 * @version		1.6.2.2$Id: installer.php 20196 2011-01-09 02:40:25Z ian $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('JPATH_BASE') or die;

jimport('joomla.installer.installer');
jimport('joomla.installer.helper');

/**
 * Joomla base installer class
 *
 * @package		Joomla.Framework
 * @subpackage	Installer
 * @since		1.5
 */
class PagesAndItemsInstaller extends JInstaller
{
	
	/**
	 * Set an installer adapter by name
	 *
	 * @access	public
	 * @param	string	$name		Adapter name
	 * @param	object	$adapter	Installer adapter object
	 * @return	boolean True if successful
	 * @since	1.5
	 */
	function setAdapterJ15($name, $adapter = null)
	{
		if (!is_object($adapter))
		{
			// Try to load the adapter object
			require_once(dirname(__FILE__).DS.'adapters'.DS.strtolower($name).'.php');
			$class = 'PiInstaller'.ucfirst($name);
			if (!class_exists($class)) {
				return false;
			}
			$adapter = new $class($this);
			$adapter->parent =& $this;
		}
		$this->_adapters[$name] =& $adapter;
		return true;
	}
	
	/**
	 * Constructor
	 *
	 * @access protected
	 */
	public function __construct()
	{
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		
		if($joomlaVersion < '1.6')
		{
			parent::__construct();
			$files = JFolder::files(dirname(__FILE__).DS.'adapters','.php$');
			foreach($files as $file)
			{
				$name = JFile::getName($file); 
				$name = JFile::stripExt($file);
				require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'installer'.DS.'adapters'.DS.strtolower($name.'.php')); //.'.php');
				
				$class = 'PiInstaller'.ucfirst($name);
				if (class_exists($class)) 
				{
					$this->setAdapterJ15($name);
					/*
					$adapter = new $class($installer);
					$adapter->parent =& $installer;
					$installer->setAdapter($name, $adapter);
					*/
				}
			}
			
		}
		else
		{
			//parent::__construct();
			//JAdapter::__construct(dirname(__FILE__),'PagesAndItemsInstaller');
			JAdapter::__construct(dirname(__FILE__),'PiInstaller');
		/*
		// here is parent JInstaller
		can we call JAdapter::__construct(dirname(__FILE__),'PagesAndItemsInstaller');
		
		
		public function __construct($basepath, $classprefix = null, $adapterfolder = null)
		{
			$this->_basepath		= $basepath;
			$this->_classprefix		= $classprefix ? $classprefix : 'J';
			$this->_adapterfolder	= $adapterfolder ? $adapterfolder : 'adapters';
			$this->_db = JFactory::getDBO();
		}
		
		// in J1.6 parent::parent:: = JAdapter
		//parent::__construct(dirname(__FILE__),'JInstaller');
		call_user_func(array(get_parent_class(get_parent_class($this)), '__construct'));

		*/
		}
	}

	public function install($path=null)
	{
		//var_dump('install');
		return parent::install($path);
	}

	function addLanguageToTable($type,$name,$element,$version,$client_id = 1,$check = false)
	{
		
		$row = & JTable::getInstance('piextension', 'PagesAndItemsTable');
		$db = $this->getDBO();
		$query = "SELECT extension_id"
			." FROM #__pi_extensions"
			." WHERE element = ".$db->Quote($element)
			." AND name = ".$db->Quote($name)
			." AND type=".$db->Quote('language')
			." AND client_id=".$db->Quote($client_id)
			;
		//var_export($query.'<br />');
		$db->setQuery($query);
		if (!$db->Query()) 
		{
			//var_export('failed <br />');
			// Install failed, send message
			//$this->abort($type.' Install: '.$db->stderr(true));
			//return false;
		}
		$id = $db->loadResult();
		//var_export('id: '.$id.'<br />');
		if(!$id)
		{
			//only core language will an extra id
			if( ($type == 'component' && $name == $element) ) //|| ($type != 'pilanguage'  && $version == 'integrated' ) ) //$element == 'en-GB')
			{
				//var_export('new id: '.$id.'<br />');
				$where = 'type = '.$db->Quote('language'); //. ' And client_id = '.$db->Quote('0');
			
				$row_extension_id = $row->getNextTypeId($where,'language');
				$db->setQuery( "INSERT INTO #__pi_extensions SET extension_id='$row_extension_id' ");
				if(!$db->query())
				{
					//$this->parent->abort($type.' Install: '.$db->stderr(true));
					//JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_CREATE_DIRECTORY', $newdir));
					//return false;
				}
				$row->extension_id = $row_extension_id;
			}
			$row->store();
		}
		else
		{
			$row->load($id);

		}

		$row->element = $element;
		$row->name = $name;
		$row->type = 'language';
		$row->client_id = $client_id;
		if($type == 'component')
		{
			if($version  && $version  != '' ) //&& $name != $element)
			{
				$row->version = $version;
			}
			/*
			elseif( $name != $element)
			{
				//TODO check over the $element the xml
				//$row->version = 'integrated';
			}
			*/
			$row->protected = '1';
		}
		elseif($type == 'pilanguage')
		{
			if($version  && $version  != '')
			{
				$row->version = $version;
			}
			else
			{
				$row->version = '';
			}
		}
		elseif($version && $version  != '')
		{
			if($check && $id && $name == $element)
			{
				//$row->version = $version;
			}
			elseif(!$check && !$id && $name == $element)
			{
				$row->version = $version;
			}
			else
			{
				$row->version = $version;
			}

			if($version == 'integrated')
			{
				$row->protected = '1';
				//TODO check over the $element the xml
				//ore will we add in #__pi_extensions an field core?
			}
		}
		else
		{
			//if($check && !$id)
			//$row->version = '';
		}
		//define('COM_PAGESANDITEMS_COMPONENT_INSTALL',1);
		if(defined('COM_PAGESANDITEMS_COMPONENT_INSTALL'))
		{
			$row->protected = '1';
		}
		$row->store();
	}


	function removeLanguage($manifest)
	{
		$db = $this->getDBO();
		$row = & JTable::getInstance('piextension', 'PagesAndItemsTable');
		$extensionslanguage = $manifest->extensionslanguage;
		if ($extensionslanguage && count($extensionslanguage->children()))
		{
			$removefiles = array();
			$extensions = $extensionslanguage->children();
			foreach ($extensions as $extension) 
			{
				$tag = (string)$extension->attributes()->tag;
				$fileName = str_replace($tag.'.','',basename((string)trim($extension)) );
				if($fileName && $fileName != '')
				{
				$folders = JFolder::folders(JPATH_COMPONENT_ADMINISTRATOR.DS.'extensions'.DS.'language', '.', false, false);
				foreach($folders as $folder)
				{
					$files = JFolder::files(JPATH_COMPONENT_ADMINISTRATOR.DS.'extensions'.DS.'language'.DS.$folder,$folder.'.'.$fileName, true, true);
					foreach($files as $file)
					{
						JFile::delete($file);
						//and delete from table
					}
					//dump($folder.'.'.$fileName);
					$query = "SELECT extension_id"
					." FROM #__pi_extensions"
					." WHERE name = ".$db->Quote($folder.'.'.$fileName)
					." AND type=".$db->Quote('language')
					;
					$db->setQuery($query);
					if (!$db->Query()) 
					{
						//var_export('failed <br />');
						// Install failed, send message
						//$this->abort($type.' Install: '.$db->stderr(true));
						//return false;
					}
					$id = $db->loadResult();
					//dump($id);
					if($id)
					{
						$row->delete($id);
					}
					
				}
				}
				
				//JPATH_COMPONENT_ADMINISTRATOR.DS.'extensions'.DS.'language'.DS.$tag.DS.basename((string)trim($extension));
				
			}
		}
	}
	
	function checkCoreLanguage($tag)
	{
		// Get the client info
		jimport('joomla.application.helper');
		$cid=0;
		$client = JApplicationHelper::getClientInfo($cid);
		$destination = $client->path;
		// If the language folder is not present, then the core pack hasn't been installed... ignore
		$path = $destination .'/language/' . $tag;
		//dump($path);
		/*
		$dateihandle = fopen("output_PI.txt", "a");
		fwrite($dateihandle,"*******\n\r");
		$out = var_export($path, true);
		fwrite($dateihandle, $out);
		fwrite($dateihandle, "\n\r********");
		fwrite($dateihandle, "\n\r");
		$out = var_export(dirname($path), true);
		fwrite($dateihandle, $out);
		fwrite($dateihandle, "\n\r********");
		$out = var_export(JFolder::exists($path), true);
		fwrite($dateihandle, $out);
		fwrite($dateihandle, "\n\r********");
		fclose($dateihandle);
		
		return false;
		*/
		if (!JFolder::exists($path)) 
		{
			return false;
		}
		return true;
	}
	
	function copyLanguageXML($tag)
	{
		/*
		<filename>extensions/language/en-GB/index.html</filename>
		<filename>extensions/language/en-GB/en-GB.xml</filename>
		*/
		$copyfiles = array();
		$source = JPath::clean($this->getPath('source'));

		
		$pathComponent = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS);
		
		$path['src'] = $source.DS.'extensions'.DS.'language'.DS.$tag.DS.'index.html';
		$path['dest'] = $pathComponent.DS.'extensions'.DS.'language'.DS.$tag.DS.'index.html';
		
		
		/*
		$dateihandle = fopen("output_PI.txt", "a");
		fwrite($dateihandle,"*******\n\r");
		$out = var_export($path, true);
		fwrite($dateihandle, $out);
		fwrite($dateihandle, "\n\r********");
		fclose($dateihandle);
		*/
		/*
		 * Before we can add a file to the copyfiles array we need to ensure
		 * that the folder we are copying our file to exits and if it doesn't,
		 * we need to create it.
		 */
		if (basename($path['dest']) != $path['dest'])
		{
			$newdir = dirname($path['dest']);
			if (!JFolder::create($newdir))
			{
				JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_CREATE_DIRECTORY', $newdir));
				return false;
			}
			//add to table
		}
		$copyfiles[] = $path;
		$path = null;
		$path['src'] = $source.DS.'extensions'.DS.'language'.DS.$tag.DS.$tag.'.xml';
		$path['dest'] = $pathComponent.DS.'extensions'.DS.'language'.DS.$tag.DS.$tag.'.xml';
		
		$copyfiles[] = $path;
		
		$this->copyFiles($copyfiles,true);
		
	}
	
	
	function addLanguage($manifest) //,$parent=null)
	{
		$row = & JTable::getInstance('piextension', 'PagesAndItemsTable');
		$db = $this->getDBO();
		$pathComponent = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS);
		$manifestType = (string)$manifest->attributes()->type;
		$piComponentLanguage = false;
		if($manifestType != 'component')
		{
			//we have an installtype pilanguage
			//and here we install the Pages and Items Language Files
			$this->parseLanguages($manifest->languages);
			$this->parseLanguages($manifest->administration->languages, 1);
			
		}
		//add to table
		if ( $manifest->languages  && count($manifest->languages->children()) )
		{
			
			$languages = $manifest->languages->children();
			foreach ($languages as $language) 
			{
				$piComponentLanguage = true;
				//add to table
				// Get the Language tag [ISO tag, eg. en-GB]
				$tag = (string)$language->attributes()->tag;
				$name = basename(trim((string)$language));
				$version = trim((string)$manifest->version);
				
				
				
				if(!$this->checkCoreLanguage($tag))
				{
					continue;
				}
				
				$this->copyLanguageXML($tag);
				$this->addLanguageToTable($manifestType,$tag,$tag,$version,0);
				$this->addLanguageToTable($manifestType,$name,$tag,$version,0);
			}
		}
		
		
			
		if ( $manifest->administration->languages  && count($manifest->administration->languages->children()) )
		{
			$languages = $manifest->administration->languages->children();
			foreach ($languages as $language) 
			{
				$piComponentLanguage = true;
				//add to table
				// Get the Language tag [ISO tag, eg. en-GB]
				$tag = (string)$language->attributes()->tag;
				$name = basename(trim((string)$language));
				$version = trim((string)$manifest->version);
				
				if(!$this->checkCoreLanguage($tag))
				{
					continue;
				}
				$this->copyLanguageXML($tag);
				//first add to table with name = element 
				$this->addLanguageToTable($manifestType,$tag,$tag,$version,'1');
				$this->addLanguageToTable($manifestType,$name,$tag,$version,'1');
			}
			
		}
		
		if( $piComponentLanguage)
		{
			//we must copy ore create the manifest file?
			$tag = 'en-GB';
			if ((string)$manifest->tag != '')
			{
				$tag = (string)$manifest->tag;
			}
			
			
			
			//$pathComponent = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS);
			$dir = $pathComponent.DS.'extensions'.DS.'language'.DS.$tag;
			if (!file_exists($dir))
			{
				if (!JFolder::create($dir))
				{
					JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_CREATE_DIRECTORY', $dir));
					//return false;
				}
			}
			//check for install.xml only $tag not 'en-GB' ?
			//no the file is copy from the commponent install or from pilanguage install
			
		}
		
		
		$extensionslanguage = $manifest->extensionslanguage;
		
		
		// we must install language in language/$tag or extensinon/language/$tag or extensinon/path to the extension /language/$tag
		if ($extensionslanguage && count($extensionslanguage->children()))
		{
			$copyfiles = array();
			$type = null;
			$name = null;
			$folder = null;
			if($manifestType != 'pilanguage')
			{
				$type = (string)$manifest->attributes()->type;
				$folder = (string)$manifest->attributes()->folder;
				$name = (string)$manifest->name;
				if (count($manifest->files->children()))
				{
					foreach ($manifest->files->children() as $file)
					{
						if ((string)$file->attributes()->$type)
						{
							$name = (string)$file->attributes()->$type;
							break;
						}
					}
				}
			}
			$extensions = $extensionslanguage->children();
			foreach ($extensions as $extension) 
			{
				if($name != '' && $type != '')
				{
					if(isset($folder) && $folder != '')
					{
						$pathfolder = $type.'s'.DS.$folder.DS.$name;
					}
					else
					{
						$pathfolder = $type.'s'.DS.$name;
					}
				}
				else
				{
					$pathfolder = '';
				}
				
				$tag = 'en-GB';
				if ((string)$extension->attributes()->tag != '')
				{
					$tag = (string)$extension->attributes()->tag;
					if(!$this->checkCoreLanguage($tag))
					{
						continue;
					}
				}
				else
				{
					continue;
				}
			
				if($manifestType == 'component')
				{
					$version = 'integrated';
				}
				else
				{
					$version = '';
					if ((string)$extension->attributes()->version != '')
					{
						$version = (string)$extension->attributes()->version;
					}
					elseif((string)$manifest->version != '')
					{
						$version = (string)$manifest->version;
					}
				}
				
				$baseFolder = (string)$extensionslanguage->attributes()->folder;
				
				/*
				if we install over the component install
				we have $this->getPath('source') = eg. .../extensions/fieldtype/calendar/
				and the pathfolder is fieldtype/calendar
				so installSourece = eg. .../extensions/
				*/
				$installSource = JPath::clean(str_replace($pathfolder,'',$this->getPath('source')));
				if ($baseFolder && file_exists($installSource.DS.$baseFolder)) 
				{
					$source = $installSource.DS.$baseFolder;
				}
				else 
				{
					$source = $installSource;
				}
				
				//where we store the language file/s?
				//we need the name from the extension
				//var_export($source.DS.trim($extension).'<br />');
				//var_export($parent.'<br />');
				//if (!$extensionInstaller->install($src.DS.'admin'.DS.'extensions'.DS.$pathfolder)) 
				if (file_exists($source.DS.trim($extension)) ) //&& file_exists(JPATH_COMPONENT_ADMINISTRATOR.DS.'extensions'.DS.$pathfolder) )
				{
					$path['src']	= $source.DS.trim($extension);
					//$path['dest']	= JPATH_COMPONENT_ADMINISTRATOR.DS.'extensions'.DS.$pathfolder.DS.'language'.DS.$tag.DS.trim($extension);
					// in my mind the best place was extensions/language
					//$path['dest']	= JPATH_COMPONENT_ADMINISTRATOR.DS.'extensions'.DS.'language'.DS.$tag.DS.trim($extension);
					$path['dest']	= $pathComponent.DS.'extensions'.DS.'language'.DS.$tag.DS.basename((string)trim($extension));
					/*
					 * Before we can add a file to the copyfiles array we need to ensure
					 * that the folder we are copying our file to exits and if it doesn't,
					 * we need to create it.
					 */
					if (basename($path['dest']) != $path['dest'])
					{
						$newdir = dirname($path['dest']);
	
						if (!JFolder::create($newdir))
						{
							JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_CREATE_DIRECTORY', $newdir));
							return false;
						}
						//add to table
					}

					// Add the file to the copyfiles array
					$copyfiles[] = $path;
					
					//need to add in table #__pi_extensions?
					//first add to table with name = element 
					$this->addLanguageToTable($manifestType,$tag,$tag,$version,1, true);
					$this->addLanguageToTable($manifestType,basename((string)trim($extension)),$tag,$version,1);
					

				}
				

			}
			//dump($copyfiles);
			$this->copyFiles($copyfiles);
		}
	}
	

	/**
	 * Returns the global Installer object, only creating it
	 * if it doesn't already exist.
	 *
	 * @static
	 * @return	object	An installer object
	 * @since 1.5
	 */
	public static function getInstance()
	{
		static $instance;

		if (!isset ($instance)) {
			$instance = new PagesAndItemsInstaller();
		}
		return $instance;
	}



	/**
	 * Refreshes the manifest cache stored in #__extensions
	 *
	 * @param int $eid Extension ID
	 * @return mixed void on success | false on error @todo missing return value ?
	 */
	function refreshManifestCache($eid)
	{
		if ($eid)
		{
			
			//$this->extension = JTable::getInstance('extension');
			$componentPath = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS);
			//var_export('refreshManifestCache'.$componentPath);
			JTable::addIncludePath($componentPath.DS.'tables');
			
			//JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
			$extension = & JTable::getInstance('piextension', 'PagesAndItemsTable');
			
			if (!$extension->load($eid))
			{
				//$this->abort(JText::_('JLIB_INSTALLER_ABORT_LOAD_DETAILS'));
				return false;
			}
			if ($extension->state == -1)
			{
				//$this->abort(JText::_('JLIB_INSTALLER_ABORT_REFRESH_MANIFEST_CACHE'));
				return false;
			}
			if($extension->folder != '')
			{
				$extension_folder = str_replace('/',DS,$extension->folder);
				$folder = $extension->type.'s'.DS.$extension_folder;//.DS;

			}
			else
			{
				$folder = $extension->type.'s';
			}
			//$extensionFolder = JPATH_COMPONENT_ADMINISTRATOR.DS.'extensions'.DS.$folder.DS.$extension->element.DS.$extension->element.'.xml';;
			$extensionFolder = $componentPath.DS.'extensions'.DS.$folder.DS.$extension->element.DS.$extension->element.'.xml';;
			$extension->manifest_cache = serialize($this->parseXMLInstallFile($extensionFolder));
			if (!$extension->store($eid))
			{
				return false;
			}
		}
		else
		{
			$this->abort(JText::_('JLIB_INSTALLER_ABORT_REFRESH_MANIFEST_CACHE_VALID'));
			return false;
		}
	}

	/*
		 * overwrite the JInstaller in J1.5 and 1.6
	*/
	public function generateManifestCache()
	{
		return serialize($this->parseXMLInstallFile($this->getPath('manifest')));
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
	



	public function getParams()
	{
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		
		if($joomlaVersion < '1.6')
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
		else
		{
			return parent::getParams();
		}
	}

	
	
	/**
	 * Is the xml file a valid Joomla installation manifest file
	 * overwrite the JInstaller in J1.6
	 *
	 * @access	private
	 * @param	string	$file	An xmlfile path to check
	 * @return	mixed	A JXMLElement, or null if the file failed to parse
	 * @since	1.6
	 */
	public function isManifest($file)
	{
		// Initialise variables.
		$xml = simplexml_load_file($file);
		//$xml = JFactory::getXML($file);

		// If we cannot load the xml file return null
		if( ! $xml)
		{
			//echo $file;
			return null;
		}

		/*
		 * Check for a valid XML root tag.
		 * @todo: Remove backwards compatability in a future version
		 * Should be 'extension', but for backward compatability we will accept 'extension' or 'install'.
		 */

		// 1.5 uses 'install'
		// 1.6 uses 'extension'
		//if($xml->getName() != 'install' && $xml->getName() != 'extension')
		//if ( !is_object($xml) || (!is_object($xml->extension)) )
		//manifest
		//echo ' TYPE2: '.(string)$xml->attributes()->type;
		//if ( !is_object($xml) || (!is_object($xml->extension) && !is_object($xml->install)) )
		//if ( !is_object($xml) || (!is_object($xml->extension) && !is_object($xml->install)) )
		if($xml->getName() != 'install' && $xml->getName() != 'extension')
		{
			//return $xml;
			//echo 'XXX';
			return null;
		}
		//echo ' TYPE3: '.(string)$xml->attributes()->type;
		// Valid manifest file return the object
		
		return $xml;
	}
	
	/**
	 * Is the xml file a valid PI Joomla installation manifest file
	 * overwrite the JInstaller in J1.5
	 *
	 * @access	private
	 * @param	string	$file	An xmlfile path to check
	 * @return	mixed	A JSimpleXML document, or null if the file failed to parse
	 */
	function &_isManifest($file)
	{
		// Initialize variables
		$null	= null;
		$xml	=& JFactory::getXMLParser('Simple');

		// If we cannot load the xml file return null
		if (!$xml->loadFile($file)) {
			// Free up xml parser memory and return null
			unset ($xml);
			return $null;
		}

		/*
		 * Check for a valid XML root tag.
		 * @todo: Remove backwards compatability in a future version
		 * Should be 'extension', but for backward compatability we will accept 'install'.
		 */
		$root =& $xml->document;
		if (!is_object($root) || ($root->name() != 'extension' && $root->name() != 'install'))
		{
			// Free up xml parser memory and return null
			unset ($xml);
			return $null;
		}

		// Valid manifest file return the object
		return $xml;
	}
}
