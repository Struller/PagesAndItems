<?php
/**
* @version		2.1.5
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// No direct access
defined('JPATH_BASE') or die;

//jimport('joomla.base.adapterinstance');

/**
 * Language installer
 */

$version = new JVersion();
$joomlaVersion = $version->getShortVersion();
if($joomlaVersion < '1.6')
{
	class PiInstallerLanguage extends JObject
	{
	}
}
else
{
	jimport('joomla.base.adapterinstance');
	class PiInstallerLanguage extends JAdapterInstance
	{
	}
}

class PiInstallerPilanguage extends PiInstallerLanguage
{
	/**
	 * Core language pack flag
	 * @access	private
	 * @var		boolean
	 */
	//protected $_core = false;
	//protected $parentParent = null;

	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param	object	$parent	Parent object [PiInstaller instance]
	 * @return	void
	 * @since	1.5
	 */
	function __construct( & $parent) //, &$parentParent = null)
	{
		$this->parent = & $parent;
		//$this->parentParent = & $parentParent;
	}

	/**
	 * Custom install method
	 * @access	public
	 * @return	boolean	True on success
	 */
	public function install()
	{
		$this->manifest = $this->parent->getManifest();
		$xml = $this->manifest;

		$this->parent->addLanguage($xml);

		$pathComponent = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS);
		// We will copy the manifest file to its appropriate place.
		// but only if pi core

		if ( ($xml->administration->languages  && count($xml->administration->languages->children()) ) || ($xml->languages  && count($xml->languages->children()) ) )
		{
			$path['src'] = $this->parent->getPath('manifest');
			$tag = (string)$xml->tag;
			$path['dest']  = $pathComponent.DS.'extensions'.DS.'language'.DS.$tag.DS.basename($this->parent->getPath('manifest'));

			if(!$this->parent->copyFiles(array ($path), true) )
			{
				// Install failed, rollback changes
				$this->parent->abort(JText::_('JLIB_INSTALLER_ABORT_COMP_INSTALL_COPY_SETUP'));
				return false;
			}
		}
		else
		{
			//
		}
		return true;
	}


	/**
	 * Custom update method
	 *
	 * @return boolean True on success, false on failure
	 * @since 1.6
	 */
	public function update()
	{

	}

	/**
	 * Custom uninstall method
	 *
	 * @param	string	$tag		The tag of the language to uninstall
	 * @param	int		$clientId	The id of the client (unused)
	 * @return	mixed	Return value for uninstall method in component uninstall file
	 * @since	1.5
	 */
	public function uninstall($eid)
	{
		$pathComponent = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS);
		// load up the extension details
		$extension = JTable::getInstance('piextension','PagesAndItemsTable');
		if($extension->load($eid))
		{

			// grab a copy of the client details
			$client = JApplicationHelper::getClientInfo($extension->get('client_id'));

			// check the element isn't blank to prevent nuking the languages directory...just in case
			$tag = $extension->get('element');
			$name = $extension->get('name');
			if (empty($tag))
			{
				JError::raiseWarning(100, JText::_('JLIB_INSTALLER_ERROR_LANG_UNINSTALL_ELEMENT_EMPTY'));
				return false;
			}
			$path = $pathComponent.DS.'extensions'.DS.'language'.DS.$tag.DS;
			//we must search for the extension or componentlanguage
			//and we need an xml
			// and we must check if not $tag en-GB.en-GB is the default and can not uninstall

			if($name == $tag && $name != 'en-GB')
			{
				$files = JFolder::files($path,'.xml$',false,true);
				if(count($files))
				{
					foreach($files as $file)
					{
						$fileName = JFile::getName($file);
						$xml = simplexml_load_file($file);
						$type = (string)$xml->type;
						if($type && $type !='' && $type =='pilanguage')
						{
							//ok here the manifest
							$manifest = $this->parent->isManifest($file);
							//first step uninstall component language
							if($manifest)
							{
								if($extension->get('client_id') )
								{
									$this->parent->removeFiles($manifest->administrator->languages);
								}
								else
								{
									$this->parent->removeFiles($manifest->languages);
								}
							}


							break;

						}
					}
				}

				//next step remove all element = $tag in #__pi_extensions
				$db = $this->parent->getDBO();
				$query = 'DELETE '
					.' FROM #__pi_extensions'
					.' WHERE extension_id = '.$db->Quote($eid)
					;
				$db->setQuery($query);
				if (!$db->Query())
				{
					// Install failed, roll back changes
					//$this->abort($type.' Install: '.$db->stderr(true));
					//return false;
				}
				//$tag


				//next step remove in extensions all folders with language/$tag
				$query = 'SELECT '
					.' FROM #__pi_extensions'
					.' WHERE name = '.$db->Quote($tag)
					.' AND element = '.$db->Quote($tag)
					;
				$db->setQuery($query);
				if (!$db->Query())
				{
					// Install failed, roll back changes
					//$this->abort($type.' Install: '.$db->stderr(true));
					//return false;
				}
				if(count($db->loadObjectList) && $extension->get('client_id'))
				{

					//delete only ini
					$files = JFolder::files($path,'.ini$',false,true);
					if(count($files))
					{
						foreach($files as $file)
						{
							JFile::delete($file);
						}
					}
				}
				elseif(!count($db->loadObjectList) )
				{
					JFolder::delete($path);
				}

			}
			elseif(strpos('com_',$name) !== false && $tag != 'en-GB')
			{
				//it is the component language
				//remove only the component language
				if($extension->get('client_id') )
				{
					$path = JPATH_ADMINISTRATOR;
				}
				else
				{
					$path = JPATH_SITE;
				}
				JFile::delete($path.DS.'language'.DS.$name);
			}
			else
			{
				//only remove the $name from folder $tag
				JFile::delete($path.DS.$name);
			}

			// All done!
			return true;
		}
		return false;
	}
}
