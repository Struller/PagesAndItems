<?php
/**
* @version		2.1.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('JPATH_BASE') or die;

/**
 * Extension Helper class
 *
 */

abstract class ExtensionHelper
{

	/**
	 * Get the extension data of a specific folder if no specific extension is specified
	 * otherwise only the specific extension data is returned.
	 *
	 * @access	public
	 * @param	string	$type		The extension type.
	 * @param	string	$folder	The extension folder, relates to the sub-directory
	 * @param	string	$extension	The extension name.
	 * @return	mixed		An array of extension data objects, or a extension data object.
	 */
	protected static function getExtension($extensions,$folder = null, $extension = null)
	{
		$result		= array();
		//$extensions = self::_load();
		//$extensions = $this->_load();
		//$extensions = self::$_child->_load();
		//$extensions = $child;
		if($extensions)
		{
			for ($i = 0, $t = count($extensions); $i < $t; $i++)
			{
				// Are we loading a single extension or a type ore a group?
				if (is_null($extension))
				{
					// Is this the right extension?
					if (is_null($folder))
					{
						$result[] = $extensions[$i];
					}
					elseif ($extensions[$i]->folder == $folder)
					{
						$result[] = $extensions[$i];
					}
				}
				else
				{
					// Is this extension in the right group?
					if (is_null($folder))
					{
						if ($extensions[$i]->name == $extension)
						{
							//$result[] = $extensions[$i];
							$result = $extensions[$i];
							break;
						}
					}
					else
					{
						if ($extensions[$i]->folder == $folder && $extensions[$i]->name == $extension)
						{
							$result = $extensions[$i];
							break;
						}
					}
				}
			}
		}
		//echo $result;
		if(!$result)
		{
			return false;
		}
		return $result;
	}

	/**
	 * Checks if a extension is enabled.
	 *
	 * @access	public
	 * @param	string		$type		The extension type.
	 * @param	string		$folder		The extension folder, relates to the sub-directory in the extensions directory.
	 * @param	string		$extension		The extension name.
	 * @return	boolean
	 */
	public static function isEnabled($folder=null, $extension = null)
	{
		$result = &self::getExtension($folder, $extension);
		return (!empty($result));
	}

	/**
	 * Loads all the extension files for a particular folder if no specific extension is specified
	 * otherwise only the specific pugin is loaded.
	 *
	 * @access	public
	 * @param	string		$type		The extension type.
	 * @param	string		$folder		The extension folder, relates to the sub-directory in the extensions directory.
	 * @param	mixed string or array		$extensionnames		The extension name.
	 * @return	mixed array or object
	 */
	public static function importExtension($extensions,$type,$folder=null, $extensionnames = null, $autocreate = true, $dispatcher = null, $loadLanguage = false)
	{
		
		static $loaded = array();

		// check for the default args, if so we can optimise cheaply
		$defaults = false;
		$isLoaded = true;
		$nullFolder = true;
		if (is_null($extensionnames) && $autocreate == true && is_null($dispatcher))
		{
			$defaults = true;
		}
		if($folder == '' || is_null($folder))
		{
			//if (!isset($loaded) || empty($loaded) || !$defaults)
			if (!isset($loaded[$type]) || empty($loaded) || $defaults)
			{
				$isLoaded = false;
			}
		}
		else
		{
			$nullFolder = false;
			if (!isset($loaded[$type][$folder]) || empty($loaded) || !$defaults)
			{
				$isLoaded = false;
			}
		}
		$results = null;
		//if (!isset($loaded[$type][$folder]) || !$defaults)
		if(!$isLoaded)
		{
			//$results = 'on';
			//$results = null;

			// Load the extensions from the database.
			//$extensions = self::_load($type,$folder);

			//$extensions = self::_load();
			//$extensions = self::$_child->_load();





			//$results = $extensions;
			// Get the specified extension(s).
			//if(count($extensions) && count($extensions) > 1)

			if($extensions)
			{
				for ($i = 0, $t = count($extensions); $i < $t; $i++)
				{
					if (is_null($folder) || $folder == '')
					{
						if (is_array($extensionnames) || is_object($extensionnames) )
						{
							$names = array();
							foreach($extensionnames as $ext)
							{
								if(!in_array($ext,$names) && $extensions[$i]->name == $ext)
								{
									$names[] = $ext;
								}
							}
							for ($in = 0, $tn = count($names); $in < $tn; $in++)
							{

								if($extensions[$i]->name == $names[$in] )
								{
									$loaded[$type][] = self::_import($extensions[$i], $autocreate, $dispatcher, $loadLanguage);
									$results = true;
								}
							}
						}
						elseif ($extensions[$i]->name == $extensionnames) // || $extensionnames === null )
						{
							$loaded[$type][] = self::_import($extensions[$i], $autocreate, $dispatcher, $loadLanguage);
							$results = true;
						}
						elseif ($extensionnames == null )
						{
							$loaded[$type][] = self::_import($extensions[$i], $autocreate, $dispatcher, $loadLanguage);
							$results = true;
						}
					}
					else
					{
						if ($extensions[$i]->folder == $folder && (is_array($extensionnames) || is_object($extensionnames) ))
						{
							$names = array();
							foreach($extensionnames as $ext)
							{
								if(!in_array($ext,$names) && $extensions[$i]->name == $ext)
								{
									$names[] = $ext;
								}
							}
							for ($in = 0, $tn = count($names); $in < $tn; $in++)
							{

								if($extensions[$i]->name == $names[$in] )
								{
									$loaded[$type][$folder][] = self::_import($extensions[$i], $autocreate, $dispatcher, $loadLanguage);
									$results = true;
								}
							}
						}
						elseif ($extensions[$i]->folder == $folder && ($extensions[$i]->name == $extensionnames ||  $extensionnames === null))
						{
							$loaded[$type][$folder][] = self::_import($extensions[$i], $autocreate, $dispatcher, $loadLanguage);
							$results = true;
						}
					}
				}
				// bail out early if we're not using default args

				if(!$defaults)
				{
					if($nullFolder)
					{
						if($results && $extensionnames && $loaded[$type] && (!is_array($extensionnames) && !is_object($extensionnames)) )
						{
							return $loaded[$type][0];
						}
						else if($results && $loaded)
						{
							return $loaded[$type];
						}
						return $results;
					}
					else
					{
						if($results && $extensionnames && $loaded[$type][$folder] && (!is_array($extensionnames) && !is_object($extensionnames)) )
						{
							return $loaded[$type][$folder][0];
						}
						else if($results && $loaded[$type][$folder])
						{
							return $loaded[$type][$folder];
						}
						return $results;
					}
				}
				//$loaded[$type] = $results;
				if($nullFolder)
				{
					return $loaded[$type];
				}
				return $loaded[$type][$folder];
			}
			return $results;
		}
		if($folder == '' || is_null($folder))
		{

			return $loaded[$type];
		}
		else
		{
			return $loaded[$type][$folder];
		}
	}

	/**
	 * Loads the extension file
	 *
	 * @access	private
	 * @return	boolean		True if success
	 */
	protected static function _import(&$extension, $autocreate = true, $dispatcher = null, $loadLanguage = false)
	{
		static $paths = array();
		$extension->folder = preg_replace('/[^A-Z0-9_\.-]/i', '', $extension->folder);
		$extension->type = preg_replace('/[^A-Z0-9_\.-]/i', '', $extension->type);
		$extension->name = preg_replace('/[^A-Z0-9_\.-]/i', '', $extension->name);
		//we will get the path over dirname(__FILE__).'../../../extensions'
		if($extension->folder)
		{
			$extension_folder = str_replace('/',DS,$extension->folder);
			$folder = $extension->type.'s'.DS.$extension_folder;//.DS;
		}
		else
		{
			$folder = $extension->type.'s';//.DS;
		}


		/*
		 in J1.6
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('extension_id AS "id", element AS "option", params, enabled');
		$query->from('#__extensions');
		$query->where('`type` = '.$db->quote('component'));
		$query->where('`element` = '.$db->quote($option));
		$db->setQuery($query);

		in J1.5
		name not element
		*/
		$path = dirname(__FILE__).'/../../extensions'.DS.$folder.DS.$extension->name.DS.$extension->name.'.php';

		if (!isset( $paths[$path] ) )
		{
			$pathExists = file_exists($path);
			if ($pathExists )
			{
				$path = $pathExists ? $path : false;

				//require_once( dirname(__FILE__).DS.'extension.php' );
				require_once( dirname(__FILE__).DS.$extension->type.'.php' );
				if (!isset($paths[$path]))
				{
					require_once $path;
				}
				//require_once( $path );
				$paths[$path] = true;

				if ($autocreate)
				{
					// Makes sure we have an event dispatcher
					if (!is_object($dispatcher))
					{
						$dispatcher = &JDispatcher::getInstance();
					}

					if($extension->folder && $extension->folder != '')
					{
						$extension_folders = explode('/',$extension->folder);
						if(count($extension_folders))
						{
							$folders = array();
							for($n = 0; $n < (count($extension_folders)); $n++)
							{
								$folders[] = ucfirst($extension_folders[$n]);
							}
							$extension_folder = implode($folders);
						}
						else
						{
							$extension_folder = ucfirst($extension->folder);
						}
						//$extension_folder = str_replace('/','_',$extension->folder);

						$prefix = ucfirst($extension->type).$extension_folder;
					}
					else
					{
						$prefix = ucfirst($extension->type);
						//$folder = $extension->type.DS.$extension_folder.DS;
					}

					if($extension->type == 'pagetype')
					{
						$extension_names = explode('_',$extension->name);
						if(count($extension_names))
						{
							$names = array();
							for($n = 0; $n < (count($extension_names)); $n++)
							{
								$names[] = ucfirst($extension_names[$n]);
							}
							$extension_name = implode($names);
						}
						else
						{
							$extension_name = ucfirst($extension->name);
						}

					}
					else
					{
						$extension_name = ucfirst($extension->name);
					}
					//$className = 'piExtension_'.$prefix.$extension->name;
					$className = 'PagesAndItemsExtension'.$prefix.$extension_name;
					//echo 'class: '.$className;
					if (class_exists($className))
					{
						// Load the extension from the database.
						//echo 'exists';
						//$extension = &self::getExtension($extension->folder, $extension->name);
						$extension = self::getExtension($extension->folder, $extension->name);
						if($loadLanguage)
						{
							$extension->language = true;
						}
						// Instantiate and register the extension.
						$extension = new $className($dispatcher, (array)($extension));
						if($loadLanguage)
						{
							$extension->loadLanguage();
						}
						return $extension;
					}
				}
			}
			else
			{
				$paths[$path] = false;
			}
		}
		//TODO return false;?
	}
	/**
	 * Loads the published extensions
	 *
	 * @access private
	 */
	abstract protected static function _load();

}