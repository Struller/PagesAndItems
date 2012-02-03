<?php
/**
* @version		2.1.5
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('JPATH_BASE') or die;


class installPlugins
{
	function installUseXML($parent)
	{
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		$database = JFactory::getDBO();
		$status_plugins = array();
		if($joomlaVersion < '1.6')
		{

			$plugins = &$this->manifest->getElementByPath('plugins');
			if (is_a($plugins, 'JSimpleXMLElement') && count($plugins->children()))
			{
				foreach ($plugins->children() as $plugin)
				{
					$pname		= $plugin->attributes('name');
					$pelement	= $plugin->attributes('plugin');
					$pgroup		= $plugin->attributes('group');
					$porder		= $plugin->attributes('order');

					// Set the installation path
					if (!empty($pelement) && !empty($pgroup))
					{
						$parent->setPath('extension_root', JPATH_ROOT.DS.'plugins'.DS.$pgroup);
					}
					else
					{
						$parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.JText::_('No plugin file specified'));
						return false;
					}

					/**
					 * ---------------------------------------------------------------------------------------------
					 * Filesystem Processing Section
					 * ---------------------------------------------------------------------------------------------
					 */

					// If the plugin directory does not exist, lets create it
					$created = false;
					if (!file_exists($parent->getPath('extension_root'))) {
						if (!$created = JFolder::create($parent->getPath('extension_root'))) {
							$parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.JText::_('Failed to create directory').': "'.$parent->getPath('extension_root').'"');
							return false;
						}
					}

					/*
					* If we created the plugin directory and will want to remove it if we
					* have to roll back the installation, lets add it to the installation
					* step stack
					*/
					if ($created) {
						$parent->pushStep(array ('type' => 'folder', 'path' => $parent->getPath('extension_root')));
					}

					// Copy all necessary files
					$element = &$plugin->getElementByPath('files');
					if ($parent->parseFiles($element, -1) === false) {
						// Install failed, roll back changes
						$parent->abort();
						return false;
					}

					// Copy all necessary files
					$element = &$plugin->getElementByPath('languages');
					if ($parent->parseLanguages($element, 1) === false) {
						// Install failed, roll back changes
						$parent->abort();
						return false;
					}

					// Copy media files
					$element = &$plugin->getElementByPath('media');
					if ($parent->parseMedia($element, 1) === false) {
						// Install failed, roll back changes
						$parent->abort();
						return false;
					}

					/**
					 * ---------------------------------------------------------------------------------------------
					 * Database Processing Section
					 * ---------------------------------------------------------------------------------------------
					 */
					//$db = &JFactory::getDBO();

					// Check to see if a plugin by the same name is already installed
					$query = 'SELECT `id`' .
					' FROM `#__plugins`' .
					' WHERE folder = '.$database->Quote($pgroup) .
					' AND element = '.$database->Quote($pelement);
					$database->setQuery($query);
					if (!$database->Query()) {
						// Install failed, roll back changes
						$parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.$database->stderr(true));
						return false;
					}
					$id = $database->loadResult();

					// Was there a plugin already installed with the same name?
					if ($id) {

						if (!$parent->getOverwrite())
						{
							// Install failed, roll back changes
							$parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.JText::_('Plugin').' "'.$pname.'" '.JText::_('already exists!'));
							return false;
						}

					} else {
						$pluginrow =& JTable::getInstance('plugin');
						$pluginrow->name = $pname;
						$pluginrow->ordering = $porder;
						$pluginrow->folder = $pgroup;
						$pluginrow->iscore = 0;
						$pluginrow->access = 0;
						$pluginrow->client_id = 0;
						$pluginrow->element = $pelement;
						$pluginrow->published = 1;
						$pluginrow->params = '';

						if (!$pluginrow->store()) {
							// Install failed, roll back changes
							$parent->abort(JText::_('Plugin').' '.JText::_('Install').': '.$database->stderr(true));
							return false;
						}
					}

					$status_plugins[] = array('name'=>$pname,'group'=>$pgroup,'element'=>$pelement);
				}
			}
		}
		else
		{
			//J1.6
			jimport('joomla.installer.installer');
			// Set the installation path
			$manifest = $parent->getManifest();
			$xml = $manifest;

			if (isset($xml->plugins) && count($xml->plugins->children()))
			{
				foreach ($xml->plugins->children() as $plugin)
				{
					$element = null;
					$name = null;
					$group = null;

					if ((string)$plugin->attributes()->plugin)
					{
						$element = (string)$plugin->attributes()->plugin;
					}

					if ((string)$plugin->attributes()->group)
					{
						$group = (string)$plugin->attributes()->group;
					}

					if ((string)$plugin->attributes()->name)
					{
						$name = (string)$plugin->attributes()->name;
					}

					if (count($plugin->children()))
					{
						foreach ($plugin->children() as $files)
						{
							if ((string)$files->attributes()->folder)
							{
								$folder = (string)$files->attributes()->folder;

								$src = $parent->getPath('source');
								$installer = new JInstaller;
								$result = $installer->install($src.DS.'admin'.DS.$folder);

								$database->setQuery("SELECT extension_id, enabled FROM #__extensions WHERE type='plugin' AND element='pagesanditems' AND folder='$group' LIMIT 1 ");
								$pluginrow = $database->loadObject();
								if(!$pluginrow->enabled)
								{
									//publish plugin
									$database->setQuery( "UPDATE #__extensions SET enabled='1' WHERE extension_id='$pluginrow->extension_id' ");
									$database->query();
								}
								$status_plugins[] = array('name'=>$name,'group'=>$group,'element'=>$element,'installed'=>$result);
								break;
							}
						}
					}
				}
			}
		}
		return $status_plugins;
	}

	function installUseDB($parent)
	{
		$database = JFactory::getDBO();
		//install system plugin
		//ms: we copy the files from
		//$plgSrc = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'plugin_system'.DS;
		$src = $parent->getPath('source');
		$plgSrc = $src.DS.'admin'.DS.'plugin_system'.DS;

		$plgDst = JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'pagesanditems'.DS;
		if(!file_exists($plgDst)){
			mkdir($plgDst);
		}
		$system_plugin_success = 0;
		$system_plugin_success = JFile::copy($plgSrc.'pagesanditems.php', $plgDst.'pagesanditems.php');
		JFile::copy($plgSrc.'pagesanditems.xml', $plgDst.'pagesanditems.xml');
		JFile::copy($plgSrc.'index.html', $plgDst.'index.html');

		if($system_plugin_success){
			echo '<p style="color: #5F9E30;">system plugin installed</p>';
			//enable plugin
			$database->setQuery("SELECT extension_id, enabled FROM #__extensions WHERE type='plugin' AND element='pagesanditems' AND folder='system' LIMIT 1 ");
			$rows = $database->loadObjectList();
			$system_plugin_id = 0;
			$system_plugin_enabled = 0;
			foreach($rows as $row){
				$system_plugin_id = $row->extension_id;
				$system_plugin_enabled = $row->enabled;
			}
			if($system_plugin_id){
				//plugin is already installed
				if(!$system_plugin_enabled){
					//publish plugin
					$database->setQuery( "UPDATE #__extensions SET enabled='1' WHERE extension_id='$system_plugin_id' ");
					$database->query();
				}
			}else{
				//insert plugin and enable it
				$manifest_cache = '{"legacy":false,"name":"System - Pages and Items","type":"plugin","creationDate":"may 2011","author":"Carsten Engel","copyright":"Copyright (C) 2011 Carsten Engel, pages-and-items","authorEmail":"-","authorUrl":"www.pages-and-items.com","version":"2.1.5","description":"Don\'t forget to ENABLE this plugin.","group":""}';
				$manifest_cache = addslashes($manifest_cache);
				$database->setQuery( "INSERT INTO #__extensions SET name='System - Pages and Items', type='plugin', element='pagesanditems', folder='system', enabled='1', manifest_cache='$manifest_cache' ");
				$database->query();
			}
			echo '<p style="color: #5F9E30;">system plugin enabled</p>';
		}else{
			echo '<p style="color: red;">system plugin not installed</p><p><a href="http://www.pages-and-items.com/extensions/pages-and-items/installation" target="_blank">download the system plugin</a> and install with the Joomla installer.</p>';
		}

		//install content plugin
		//$plgSrc = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'plugin_content'.DS;
		$plgSrc = $src.DS.'admin'.DS.'plugin_content'.DS;
		$plgDst = JPATH_ROOT.DS.'plugins'.DS.'content'.DS.'pagesanditems'.DS;
		if(!file_exists($plgDst)){
			mkdir($plgDst);
		}
		$content_plugin_success = 0;
		$content_plugin_success = JFile::copy($plgSrc.'pagesanditems.php', $plgDst.'pagesanditems.php');
		JFile::copy($plgSrc.'pagesanditems.xml', $plgDst.'pagesanditems.xml');
		JFile::copy($plgSrc.'index.html', $plgDst.'index.html');

		if($content_plugin_success){
			echo '<p style="color: #5F9E30;">content plugin installed</p>';
			//enable plugin
			$database->setQuery("SELECT extension_id, enabled FROM #__extensions WHERE type='plugin' AND element='pagesanditems' AND folder='content' LIMIT 1 ");
			$rows = $database->loadObjectList();
			$content_plugin_id = 0;
			$content_plugin_enabled = 0;
			foreach($rows as $row){
				$content_plugin_id = $row->extension_id;
				$content_plugin_enabled = $row->enabled;
			}
			if($content_plugin_id){
				//plugin is already installed
				if(!$content_plugin_enabled){
					//publish plugin
					$database->setQuery( "UPDATE #__extensions SET enabled='1' WHERE extension_id='$content_plugin_id' ");
					$database->query();
				}
			}else{
				//insert plugin and enable it
				$manifest_cache = '{"legacy":false,"name":"Content - Pages and Items","type":"plugin","creationDate":"may 2011","author":"Carsten Engel","copyright":"Copyright (C) 2011 Carsten Engel, pages-and-items","authorEmail":"-","authorUrl":"www.pages-and-items.com","version":"2.1.5","description":"Don\'t forget to ENABLE this plugin.","group":""}';
				$manifest_cache = addslashes($manifest_cache);
				$database->setQuery( "INSERT INTO #__extensions SET name='Content - Pages and Items', type='plugin', element='pagesanditems', folder='content', enabled='1', manifest_cache='$manifest_cache' ");
				$database->query();
			}
			echo '<p style="color: #5F9E30;">content plugin enabled</p>';
		}else{
			echo '<p style="color: red;">content plugin not installed</p><p><a href="http://www.pages-and-items.com/extensions/pages-and-items/installation" target="_blank">download the content plugin</a> and install with the Joomla installer.</p>';
		}
	}
}
