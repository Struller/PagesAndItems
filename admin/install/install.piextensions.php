<?php
/**
* @version		2.1.1
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('JPATH_BASE') or die;


class piExtensions
{
	function createTable()
	{
		$database = JFactory::getDBO();
		$query =
		"CREATE TABLE IF NOT EXISTS `#__pi_extensions` "
		."("
		."`extension_id` int(11) NOT NULL auto_increment, "
			."`name` varchar(100) NOT NULL, "
			."`type` varchar(20) NOT NULL, "
			."`element` varchar(100) NOT NULL, "
			."`folder` varchar(100) NOT NULL, "
			."`version` varchar(255) NOT NULL, "
			."`required_version` varchar(255) NOT NULL, "
			."`description` text NOT NULL, "
			."`client_id` tinyint(3) NOT NULL, "
			."`enabled` tinyint(3) NOT NULL default '1', "
			."`access` tinyint(3) unsigned NOT NULL default '1', "
			."`protected` tinyint(3) NOT NULL default '0', "
			."`manifest_cache` text NOT NULL, "
			."`params` text NOT NULL, "
			."`custom_data` text NOT NULL, "
			."`system_data` text NOT NULL, "
			."`checked_out` int(10) unsigned NOT NULL default '0', "
			."`checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00', "
			."`ordering` int(11) default '0', "
			."`state` TINYINT( 3 ) NOT NULL DEFAULT '1', "
			."PRIMARY KEY  (`extension_id`), "
			."KEY `element_clientid` (`element`,`client_id`), "
			."KEY `element_folder_clientid` (`element`,`folder`,`client_id`), "
			."KEY `extension` (`type`,`element`,`folder`,`client_id`) "
		.") "
		."AUTO_INCREMENT=10000 ; ";
		$database->setQuery($query);
		$database->query();
	}


	function installLanguage($parent)
	{
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion(); //can be '1.6.3'
		$componentPath = $parent->getPath('extension_administrator');
		$extension = 'com_installer';
		$lang = &JFactory::getLanguage();
		//$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false);
		$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false) || $lang->load(strtolower($extension), JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);

		require_once( $componentPath.DS.'includes'.DS.'installer'.DS.'installerhelper.php' );
		require_once( $componentPath.DS.'includes'.DS.'installer'.DS.'installer.php');
		jimport('joomla.filesystem.folder');
		JTable::addIncludePath($componentPath.DS.'tables');

		$piinstaller = new PagesAndItemsInstaller();
		/*
		$files = JFolder::files($componentPath.DS.'includes'.DS.'installer'.DS.'adapters','.php$');
		foreach($files as $file)
		{
			$name = JFile::getName($file);
			$name = JFile::stripExt($file);
			require_once($componentPath.DS.'includes'.DS.'installer'.DS.'adapters'.DS.strtolower($name.'.php'));
			$class = 'PiInstaller'.ucfirst($name);
			if (class_exists($class))
			{
				//$adapter = null;
				$adapter = new $class($piinstaller);
				if( $joomlaVersion < '1.6')
				{
					//$adapter->parent =& $piinstaller;
					$adapter->parent = $piinstaller;
				}
				$piinstaller->setAdapter($name, $adapter);
			}
		}
		*/
		$piinstaller->setPath('source',$parent->getPath('source').DS.'admin');
		$piinstaller->addLanguage($parent->getManifest());
	}

	function installExtensions($parent)
	{

		//$status_extensions = array();
		$status_extensions = false;
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion(); //can be '1.6.3'
		$joomlaVersionRELEASE = $version->RELEASE;//can be '1.6'

		$componentPath = $parent->getPath('extension_administrator');
		$extension = 'com_installer';
		$lang = &JFactory::getLanguage();
		//$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false);
		$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false) || $lang->load(strtolower($extension), JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);

		require_once( $componentPath.DS.'includes'.DS.'installer'.DS.'installerhelper.php' );
		require_once( $componentPath.DS.'includes'.DS.'installer'.DS.'installer.php');

		//$installer = PagesAndItemsInstaller::getInstance();
		//we need the files from adapter
		jimport('joomla.filesystem.folder');
		JTable::addIncludePath($componentPath.DS.'tables');

		/*
		foreach($files as $file)
		{
			$name = JFile::getName($file);
			$name = JFile::stripExt($file);
			require_once($componentPath.DS.'includes'.DS.'installer'.DS.'adapters'.DS.strtolower($name.'.php'));
			$class = 'PiInstaller'.ucfirst($name);
			if (class_exists($class))
			{
				$adapter = new $class($installer);
				if( $joomlaVersion < '1.6')
				{
					$adapter->parent =& $installer;
				}
				$installer->setAdapter($name, $adapter);
			}
		}
		*/
		defined('COM_PAGESANDITEMS_INSTALLER_PATH') or define('COM_PAGESANDITEMS_INSTALLER_PATH', $componentPath.DS.'extensions');
		define('COM_PAGESANDITEMS_COMPONENT_INSTALL',1);

		//here we will load from an xml file all piextensions to install
		$file = $componentPath.DS.'install'.DS.'install.piextensions.xml';
		$xml = simplexml_load_file($file);
		if( $xml )
		{
			if (count($xml->children()))
			{
				foreach ($xml->children() as $installExtension)
				{
					$name = (string)$installExtension->name;
					$type = (string)$installExtension->type;
					$folder = (string)$installExtension->folder;
					$extension_installed = false;

					if(isset($folder) && $folder != '')
					{
						$pathfolder = $type.'s'.DS.$folder.DS.$name;
					}
					else
					{
						$pathfolder = $type.'s'.DS.$name;
					}
					$src = $parent->getPath('source');
					$extension_installed = false;
					if (file_exists($src.DS.'admin'.DS.'extensions'.DS.$pathfolder))
					{
						//$extension_installed = true;
						//here we need an new installer so abort not have effects for the other
						$extensionInstaller = new PagesAndItemsInstaller();
						/*
						ms: new method to load the adapter so this we need not here
						$adapterFiles = JFolder::files($componentPath.DS.'includes'.DS.'installer'.DS.'adapters','.php$');
						foreach($adapterFiles as $adapterFile)
						{
							$adapterName = JFile::getName($adapterFile);
							$adapterName = JFile::stripExt($adapterFile);
							require_once($componentPath.DS.'includes'.DS.'installer'.DS.'adapters'.DS.strtolower($adapterName.'.php'));
							$installerClass = 'PiInstaller'.ucfirst($adapterName);
							if (class_exists($installerClass))
							{
								$adapter = null;
								$adapter = new $installerClass($extensionInstaller); //,$parent);
								if( $joomlaVersion < '1.6')
								{
									$adapter->parent = $extensionInstaller; //&$extensionInstaller;
								}
								$extensionInstaller->setAdapter($adapterName, $adapter);
								//problem: all adapters are pilanguage why?

								$dateihandle = fopen("output_adapter.txt", "a");
								fwrite($dateihandle,"*******\n\r");
								$out = var_export(get_class($adapter), true);
								fwrite($dateihandle, $out);
								fwrite($dateihandle, "\n\r");
								$out = var_export($adapterName, true);
								fwrite($dateihandle, $out);
								fwrite($dateihandle, "\n\r********ENDE");
								fwrite($dateihandle, "\n\r");
								fclose($dateihandle);

							}
//
						}
						*/
						if (!$extensionInstaller->install($src.DS.'admin'.DS.'extensions'.DS.$pathfolder))
						{
							// There was an error installing the package
							//$msg = JText::_('COM_PAGESANDITEMS_INSTALLEXT_FAILED');
							$extension_installed = false;
							//$status_extensions[] = $extension;
						}
						else
						{
							$extension_installed = true;
							// Package installed sucessfully
							//$msg = JText::_('COM_PAGESANDITEMS_INSTALLEXT_SUCCESS');
							//$message = $extensionInstaller->message;
							//$extension_message = $extensionInstaller->get('extension.message');
						}

					}
					else
					{
						$extension_installed = false;
					}
					$status_extensions[] = array('name'=>$name,'folder'=>$folder,'type'=>$type,'installed'=>$extension_installed);
				}
			}
		}
		return $status_extensions;
	}

	function insert_into()
	{
		$database = JFactory::getDBO();
		/*
		* check if extensions is empty, if so insert default values
		*/
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		$database->setQuery("SELECT * FROM #__pi_extensions ");
		$rows = $database -> loadObjectList();
		$exist = '';
		if(count($rows) > 0)
		{
			$pirow = $rows[0];
			$exist = true;
			//$row->id;
		}
		else
		{
			$query = 'INSERT INTO `#__pi_extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `version`, `required_version`, `description`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES ';
			$query .= '(1, \'Calendar (Pages and Items fieldtype)\', \'fieldtype\', \'calendar\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:9:"fieldtype";s:4:"name";s:8:"calendar";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:41:"\n		fieldtype Calendar (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'{"date_format":"%d-%m-%Y","only_once":"1","no_pi_fish_table":"1"}\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 1, 1),';
			$query .= '(2, \'Checkbox (Pages and Items fieldtype)\', \'fieldtype\', \'checkbox\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:9:"fieldtype";s:4:"name";s:8:"checkbox";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:41:"\n		fieldtype Checkbox (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'{"values":"","labels":"","default_value":"","number_checkboxes":""}\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 2, 1),';
			$query .= '(3, \'Editor (Pages and Items fieldtype)\', \'fieldtype\', \'editor\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:9:"fieldtype";s:4:"name";s:6:"editor";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:39:"\n		fieldtype Editor (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'""\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 3, 1),';
			$query .= '(4, \'Html (Pages and Items fieldtype)\', \'fieldtype\', \'html\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:9:"fieldtype";s:4:"name";s:4:"html";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:37:"\n		fieldtype Html (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'""\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 4, 1),';
			$query .= '(5, \'Image (Pages and Items fieldtype)\', \'fieldtype\', \'image\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:9:"fieldtype";s:4:"name";s:5:"image";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:38:"\n		fieldtype Image (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'""\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 5, 1),';
			$query .= '(6, \'Image_gallery (Pages and Items fieldtype)\', \'fieldtype\', \'image_gallery\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:9:"fieldtype";s:4:"name";s:13:"image_gallery";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:46:"\n		fieldtype Image_gallery (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'""\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 6, 1),';
			$query .= '(7, \'Image_multisize (Pages and Items fieldtype)\', \'fieldtype\', \'image_multisize\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:9:"fieldtype";s:4:"name";s:15:"image_multisize";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:48:"\n		fieldtype Image_multisize (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'""\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 7, 1),';
			$query .= '(8, \'Item_author (Pages and Items fieldtype)\', \'fieldtype\', \'item_author\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:9:"fieldtype";s:4:"name";s:11:"item_author";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:44:"\n		fieldtype Item_author (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'{"only_once":"1","no_pi_fish_table":"1"}\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 8, 1),';
			$query .= '(9, \'Item_creation_date (Pages and Items fieldtype)\', \'fieldtype\', \'item_creation_date\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:9:"fieldtype";s:4:"name";s:18:"item_creation_date";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:51:"\n		fieldtype Item_creation_date (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'{"only_once":"1","no_pi_fish_table":"1"}\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 9, 1),';
			$query .= '(10, \'Item_modified_date (Pages and Items fieldtype)\', \'fieldtype\', \'item_modified_date\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:9:"fieldtype";s:4:"name";s:18:"item_modified_date";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:51:"\n		fieldtype Item_modified_date (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'{"only_once":"1","no_pi_fish_table":"1"}\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 10, 1),';
			$query .= '(11, \'Item_publish_date (Pages and Items fieldtype)\', \'fieldtype\', \'item_publish_date\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:9:"fieldtype";s:4:"name";s:17:"item_publish_date";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:50:"\n		fieldtype Item_publish_date (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'{"only_once":"1","no_pi_fish_table":"1"}\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 11, 1),';
			$query .= '(12, \'Item_title (Pages and Items fieldtype)\', \'fieldtype\', \'item_title\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:9:"fieldtype";s:4:"name";s:10:"item_title";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:43:"\n		fieldtype Item_title (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'{"only_once":"1","no_pi_fish_table":"1"}\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 12, 1),';
			$query .= '(13, \'Item_version (Pages and Items fieldtype)\', \'fieldtype\', \'item_version\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:9:"fieldtype";s:4:"name";s:12:"item_version";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:45:"\n		fieldtype Item_version (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'""\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 13, 1),';
			$query .= '(14, \'Php (Pages and Items fieldtype)\', \'fieldtype\', \'php\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:9:"fieldtype";s:4:"name";s:3:"php";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:36:"\n		fieldtype Php (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'{"no_pi_fish_table":"1"}\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 14, 1),';
			$query .= '(15, \'Radio (Pages and Items fieldtype)\', \'fieldtype\', \'radio\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:9:"fieldtype";s:4:"name";s:5:"radio";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:38:"\n		fieldtype Radio (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'""\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 15, 1),';
			$query .= '(16, \'Select (Pages and Items fieldtype)\', \'fieldtype\', \'select\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:9:"fieldtype";s:4:"name";s:6:"select";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:39:"\n		fieldtype Select (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'""\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 18, 1),';
			$query .= '(17, \'Text (Pages and Items fieldtype)\', \'fieldtype\', \'text\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:9:"fieldtype";s:4:"name";s:4:"text";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:37:"\n		fieldtype Text (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'""\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 16, 1),';
			$query .= '(18, \'Textarea (Pages and Items fieldtype)\', \'fieldtype\', \'textarea\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:9:"fieldtype";s:4:"name";s:8:"textarea";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:41:"\n		fieldtype Textarea (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'""\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 17, 1),';
			$query .= '(100, \'Content (Pages and Items itemtype)\', \'itemtype\', \'content\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 1, \'a:10:{s:4:"type";s:8:"itemtype";s:4:"name";s:7:"content";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:39:"\n		itemtype Content (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'""\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 1, 1),';
			$query .= '(101, \'Custom (Pages and Items itemtype)\', \'itemtype\', \'custom\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:8:"itemtype";s:4:"name";s:6:"custom";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:38:"\n		itemtype Custom (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'""\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 2, 1),';
			$query .= '(102, \'Html (Pages and Items itemtype)\', \'itemtype\', \'html\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:8:"itemtype";s:4:"name";s:4:"html";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:36:"\n		itemtype Html (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'""\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 3, 1),';
			$query .= '(103, \'Other_item (Pages and Items itemtype)\', \'itemtype\', \'other_item\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:8:"itemtype";s:4:"name";s:10:"other_item";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:42:"\n		itemtype Other_item (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'""\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 4, 1),';
			$query .= '(104, \'Text (Pages and Items itemtype)\', \'itemtype\', \'text\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:8:"itemtype";s:4:"name";s:4:"text";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:36:"\n		itemtype Text (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'""\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 5, 1),';
			$query .= '(300, \'Display_template (Pages and Items Html)\', \'html\', \'display_template\', \'cci_template\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:16:"display_template";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:88:"\n		html Display_template for Template in view config_custom_itemtype (Pages and Items)\n	";s:6:"folder";s:20:"cc_itemtype_template";}\', \'{"editor":"none","size_x":"800","size_y":"690","ok_button":"0","editor_buttons":"0","show_in":"0"}\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 9, 1),';
			$query .= '(301, \'Div (Pages and Items Html)\', \'html\', \'div\', \'cci_template\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:3:"div";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:75:"\n		html Div for Template in view config_custom_itemtype (Pages and Items)\n	";s:6:"folder";s:20:"cc_itemtype_template";}\', \'\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 6, 1),';
			$query .= '(302, \'Hide_in_full_view (Pages and Items Html)\', \'html\', \'hide_in_full_view\', \'cci_template\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:17:"hide_in_full_view";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:89:"\n		html Hide_in_full_view for Template in view config_custom_itemtype (Pages and Items)\n	";s:6:"folder";s:20:"cc_itemtype_template";}\', \'\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 4, 1),';
			$query .= '(303, \'Hide_in_intro_view (Pages and Items Html)\', \'html\', \'hide_in_intro_view\', \'cci_template\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:18:"hide_in_intro_view";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:90:"\n		html Hide_in_intro_view for Template in view config_custom_itemtype (Pages and Items)\n	";s:6:"folder";s:20:"cc_itemtype_template";}\', \'\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 5, 1),';
			$query .= '(304, \'Insert_field_code (Pages and Items Html)\', \'html\', \'insert_field_code\', \'cci_template\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:17:"insert_field_code";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:89:"\n		html Insert_field_code for Template in view config_custom_itemtype (Pages and Items)\n	";s:6:"folder";s:20:"cc_itemtype_template";}\', \'{"show_in":"0"}\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 1, 1),';
			$query .= '(305, \'Insert_if_empty_code (Pages and Items Html)\', \'html\', \'insert_if_empty_code\', \'cci_template\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:20:"insert_if_empty_code";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:92:"\n		html Insert_if_empty_code for Template in view config_custom_itemtype (Pages and Items)\n	";s:6:"folder";s:20:"cc_itemtype_template";}\', \'{"show_in":"0"}\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 2, 1),';
			$query .= '(306, \'Insert_ifnot_empty_code (Pages and Items Html)\', \'html\', \'insert_ifnot_empty_code\', \'cci_template\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:23:"insert_ifnot_empty_code";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:95:"\n		html Insert_ifnot_empty_code for Template in view config_custom_itemtype (Pages and Items)\n	";s:6:"folder";s:20:"cc_itemtype_template";}\', \'{"show_in":"0"}\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 3, 1),';
			$query .= '(307, \'Insert_other_code (Pages and Items Html)\', \'html\', \'insert_other_code\', \'cci_template\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:17:"insert_other_code";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:89:"\n		html Insert_other_code for Template in view config_custom_itemtype (Pages and Items)\n	";s:6:"folder";s:20:"cc_itemtype_template";}\', \'\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 7, 1),';
			$query .= '(308, \'Insert_own_code (Pages and Items Html)\', \'html\', \'insert_own_code\', \'cci_template\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:15:"insert_own_code";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:87:"\n		html Insert_own_code for Template in view config_custom_itemtype (Pages and Items)\n	";s:6:"folder";s:20:"cc_itemtype_template";}\', \'{"own":"value=<div>;label=<\\/div>;title=<div><\\/div>\\r\\nvalue=test;label=;title=test","show_in":"0"}\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 8, 1),';
			$query .= '(309, \'Archive (Pages and Items Html)\', \'html\', \'archive\', \'page_items\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:7:"archive";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:71:"\n		html Archive for pageItems in view page in items (Pages and Items)\n	";s:6:"folder";s:10:"page_items";}\', \'""\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 5, 1),';
			$query .= '(310, \'Convert (Pages and Items Html)\', \'html\', \'convert\', \'page_items\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:7:"convert";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:71:"\n		html Convert for pageItems in view page in items (Pages and Items)\n	";s:6:"folder";s:10:"page_items";}\', \'""\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 4, 1),';
			$query .= '(311, \'Copy (Pages and Items Html)\', \'html\', \'copy\', \'page_items\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:4:"copy";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:68:"\n		html Copy for pageItems in view page in items (Pages and Items)\n	";s:6:"folder";s:10:"page_items";}\', \'\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 2, 1),';
			$query .= '(312, \'Delete (Pages and Items Html)\', \'html\', \'delete\', \'page_items\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:6:"delete";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:70:"\n		html Delete for pageItems in view page in items (Pages and Items)\n	";s:6:"folder";s:10:"page_items";}\', \'""\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 7, 1),';
			$query .= '(313, \'Move (Pages and Items Html)\', \'html\', \'move\', \'page_items\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:4:"move";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:68:"\n		html Move for pageItems in view page in items (Pages and Items)\n	";s:6:"folder";s:10:"page_items";}\', \'\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 3, 1),';
			$query .= '(314, \'Publish (Pages and Items Html)\', \'html\', \'publish\', \'page_items\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:7:"publish";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:81:"\n		html Publish for pageItems in view page in items (Pages and Items)\n	";s:6:"folder";s:10:"page_items";}\', \'\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 1, 1),';
			$query .= '(315, \'Trash (Pages and Items Html)\', \'html\', \'trash\', \'page_items\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:5:"trash";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:69:"\n		html Trash for pageItems in view page in items (Pages and Items)\n	";s:6:"folder";s:10:"page_items";}\', \'\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 6, 1),';
			$query .= '(318, \'Content_archive (Pages and Items Html)\', \'html\', \'content_archive\', \'page_childs\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:15:"content_archive";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:80:"\n		html Content_archive for pageItems in view page in childs (Pages and Items)\n	";s:6:"folder";s:11:"page_childs";}\', \'\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 9, 1),';
			$query .= '(319, \'Content_article (Pages and Items Html)\', \'html\', \'content_article\', \'page_childs\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:15:"content_article";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:80:"\n		html Content_article for pageItems in view page in childs (Pages and Items)\n	";s:6:"folder";s:11:"page_childs";}\', \'\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 1, 1),';
			$query .= '(321, \'Content_category_blog (Pages and Items Html)\', \'html\', \'content_category_blog\', \'page_childs\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:21:"content_category_blog";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:86:"\n		html Content_category_blog for pageItems in view page in childs (Pages and Items)\n	";s:6:"folder";s:11:"page_childs";}\', \'\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 2, 1),';
			$query .= '(324, \'Menuitemtypeselect (Pages and Items Html)\', \'html\', \'menuitemtypeselect\', \'page_childs\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:18:"menuitemtypeselect";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:83:"\n		html Menuitemtypeselect for pageItems in view page in childs (Pages and Items)\n	";s:6:"folder";s:11:"page_childs";}\', \'\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 10, 1),';
			$query .= '(326, \'Separator (Pages and Items Html)\', \'html\', \'separator\', \'page_childs\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:9:"separator";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:74:"\n		html Separator for pageItems in view page in childs (Pages and Items)\n	";s:6:"folder";s:11:"page_childs";}\', \'\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 6, 1),';
			$query .= '(327, \'Url (Pages and Items Html)\', \'html\', \'url\', \'page_childs\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:3:"url";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:68:"\n		html Url for pageItems in view page in childs (Pages and Items)\n	";s:6:"folder";s:11:"page_childs";}\', \'\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 7, 1),';
			$query .= '(328, \'Delete (Pages and Items Html)\', \'html\', \'delete\', \'cci_fields\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:6:"delete";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:70:"\n		html Delete for pageItems in view page in items (Pages and Items)\n	";s:6:"folder";s:10:"cci_fields";}\', \'\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 1, 1),';
			$query .= '(329, \'Trash (Pages and Items Html)\', \'html\', \'trash\', \'cci_fields\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:5:"trash";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:69:"\n		html Trash for pageItems in view page in items (Pages and Items)\n	";s:6:"folder";s:10:"cci_fields";}\', \'\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 2, 1),';
			$query .= '(330, \'Archive (Pages and Items Html)\', \'html\', \'archive\', \'cci_fields\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:7:"archive";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:71:"\n		html Archive for pageItems in view page in items (Pages and Items)\n	";s:6:"folder";s:10:"cci_fields";}\', \'\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 3, 1),';
			$query .= '(331, \'Menuitemtypeselect (Pages and Items Html)\', \'html\', \'menuitemtypeselect\', \'item_base\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:18:"menuitemtypeselect";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:73:"\n		html Menuitemtypeselect for pageItems in view item (Pages and Items)\n	";s:6:"folder";s:9:"item_base";}\', \'\', \'\', \'\', 64, \'2011-01-23 10:22:23\', 1, 1),(400, \'Archive (Pages and Items Manager)\', \'manager\', \'archive\', \'\', \'integrated\', \'\', \'		Manager Archive for Pages and Items\r\n	\', 0, 1, 1, 0, \'a:10:{s:4:"type";s:7:"manager";s:4:"name";s:7:"archive";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:40:"\n		Manager Archive for Pages and Items\n	";s:6:"folder";s:0:"";}\', \'{}\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 1, 1),';
			$query .= '(332, \'Unpublish (Pages and Items Html)\', \'html\', \'unpublish\', \'page_items\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:7:"unpublish";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:81:"\n		html unpublish for pageItems in view page in items (Pages and Items)\n	";s:6:"folder";s:10:"page_items";}\', \'\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 2, 1),';
			$query .= '(401, \'Trash (Pages and Items Manager)\', \'manager\', \'trash\', \'\', \'integrated\', \'\', \'		Manager Trash for Pages and Items\r\n	\', 0, 1, 1, 0, \'a:10:{s:4:"type";s:7:"manager";s:4:"name";s:5:"trash";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:38:"\n		Manager Trash for Pages and Items\n	";s:6:"folder";s:0:"";}\', \'{}\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 2, 1);
			';


			$database->setQuery($query);
			$database->query();

			/*
			pagetype for J1.5 and J1.6
			*/


			$query = 'INSERT INTO `#__pi_extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `version`, `required_version`, `description`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES ';
			$query .= '(200, \'Component (Pages and Items pagetype)\', \'pagetype\', \'component\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:8:"pagetype";s:4:"name";s:9:"component";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:41:"\n		pagetype Component (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'""\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 1, 1),';
			$query .= '(201, \'ContentArchive (Pages and Items pagetype)\', \'pagetype\', \'content_archive\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:8:"pagetype";s:4:"name";s:15:"content_archive";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:46:"\n		pagetype ContentArchive (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'""\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 2, 1),';
			$query .= '(202, \'ContentArticle (Pages and Items pagetype)\', \'pagetype\', \'content_article\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:8:"pagetype";s:4:"name";s:15:"content_article";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:46:"\n		pagetype ContentArticle (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'""\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 3, 1),';
			$query .= '(204, \'ContentCategory (Pages and Items pagetype)\', \'pagetype\', \'content_category\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'b:0;\', \'""\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 5, 1),';
			$query .= '(205, \'ContentCategoryBlog (Pages and Items pagetype)\', \'pagetype\', \'content_category_blog\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:8:"pagetype";s:4:"name";s:21:"content_category_blog";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:51:"\n		pagetype ContentCategoryBlog (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'""\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 6, 1),';
			$query .= '(210, \'Separator (Pages and Items pagetype)\', \'pagetype\', \'separator\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:8:"pagetype";s:4:"name";s:9:"separator";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:41:"\n		pagetype Separator (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'""\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 11, 1),';
			$query .= '(211, \'Url (Pages and Items pagetype)\', \'pagetype\', \'url\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:8:"pagetype";s:4:"name";s:3:"url";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:35:"\n		pagetype Url (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'""\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 12, 1);
			';

			$database->setQuery($query);
			$database->query();

			if($joomlaVersion < '1.6')
			{
				/*
				pagetype for J1.5
				*/

				$query = 'INSERT INTO `#__pi_extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `version`, `required_version`, `description`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES ';
				$query .= '(203, \'ContentArticleForm (Pages and Items pagetype)\', \'pagetype\', \'content_article_form\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:8:"pagetype";s:4:"name";s:20:"content_article_form";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:50:"\n		pagetype ContentArticleForm (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'""\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 4, 1),';
				$query .= '(206, \'ContentFrontpage (Pages and Items pagetype)\', \'pagetype\', \'content_frontpage\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:8:"pagetype";s:4:"name";s:17:"content_frontpage";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:48:"\n		pagetype ContentFrontpage (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'""\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 7, 1),';
				$query .= '(207, \'ContentSection (Pages and Items pagetype)\', \'pagetype\', \'content_section\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:8:"pagetype";s:4:"name";s:15:"content_section";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:46:"\n		pagetype ContentSection (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'""\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 8, 1),';
				$query .= '(208, \'ContentSectionBlog (Pages and Items pagetype)\', \'pagetype\', \'content_section_blog\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:8:"pagetype";s:4:"name";s:20:"content_section_blog";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:50:"\n		pagetype ContentSectionBlog (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'""\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 9, 1),';
				$query .= '(209, \'Menulink (Pages and Items pagetype)\', \'pagetype\', \'menulink\', \'\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:8:"pagetype";s:4:"name";s:8:"menulink";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:40:"\n		pagetype Menulink (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'""\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 10, 1);
				';

				$database->setQuery($query);
				$database->query();

				/*
				page_childs for J1.5
				*/


				$query = 'INSERT INTO `#__pi_extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `version`, `required_version`, `description`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES ';
				$query .= '(320, \'Content_article_form (Pages and Items Html)\', \'html\', \'content_article_form\', \'page_childs\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:20:"content_article_form";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:85:"\n		html Content_article_form for pageItems in view page in childs (Pages and Items)\n	";s:6:"folder";s:11:"page_childs";}\', \'\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 8, 1),';
				$query .= '(322, \'Content_frontpage (Pages and Items Html)\', \'html\', \'content_frontpage\', \'page_childs\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:17:"content_frontpage";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:82:"\n		html Content_frontpage for pageItems in view page in childs (Pages and Items)\n	";s:6:"folder";s:11:"page_childs";}\', \'\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 4, 1),';
				$query .= '(323, \'Content_section_blog (Pages and Items Html)\', \'html\', \'content_section_blog\', \'page_childs\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:20:"content_section_blog";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:85:"\n		html Content_section_blog for pageItems in view page in childs (Pages and Items)\n	";s:6:"folder";s:11:"page_childs";}\', \'\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 3, 1),';
				$query .= '(325, \'Menulink (Pages and Items Html)\', \'html\', \'menulink\', \'page_childs\', \'integrated\', \'\', \'\', 0, 1, 0, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:8:"menulink";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:73:"\n		html Menulink for pageItems in view page in childs (Pages and Items)\n	";s:6:"folder";s:11:"page_childs";}\', \'\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 5, 1);
				';

				$database->setQuery($query);
				$database->query();


			}
			else
			{
				/*
				pagetypes for J1.6
				*/
				$query = 'INSERT INTO `#__pi_extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `version`, `required_version`, `description`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES ';
				$query .= '(203, \'Alias (Pages and Items pagetype)\', \'pagetype\', \'alias\', \'\', \'integrated\', \'\', \'		pagetype Alias (Pages and Items)\r\n	\', 0, 1, 1, 0, \'a:10:{s:4:"type";s:8:"pagetype";s:4:"name";s:5:"alias";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:37:"\n		pagetype Alias (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'{}\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 13, 1),';
				$query .= '(206, \'ContentCategories (Pages and Items pagetype)\', \'pagetype\', \'content_categories\', \'\', \'integrated\', \'\', \'		pagetype ContentCategories (Pages and Items)\r\n	\', 0, 1, 1, 0, \'a:10:{s:4:"type";s:8:"pagetype";s:4:"name";s:18:"content_categories";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:49:"\n		pagetype ContentCategories (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'{}\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 14, 1),';
				$query .= '(207, \'ContentFeatured (Pages and Items pagetype)\', \'pagetype\', \'content_featured\', \'\', \'integrated\', \'\', \'		pagetype ContentFeatured (Pages and Items)\r\n	\', 0, 1, 1, 0, \'a:10:{s:4:"type";s:8:"pagetype";s:4:"name";s:16:"content_featured";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:47:"\n		pagetype ContentFeatured (Pages and Items)\n	";s:6:"folder";s:0:"";}\', \'{}\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 15, 1);
				';

				$database->setQuery($query);
				$database->query();
				/*
				page_childs for J1.6
				*/
				$query = 'INSERT INTO `#__pi_extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `version`, `required_version`, `description`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES ';
				$query .= '(320, \'Alias (Pages and Items Html)\', \'html\', \'alias\', \'page_childs\', \'integrated\', \'\', \'		html Alias for pageItems in view page in childs (Pages and Items)\r\n	\', 0, 1, 1, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:5:"alias";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:70:"\n		html Alias for pageItems in view page in childs (Pages and Items)\n	";s:6:"folder";s:11:"page_childs";}\', \'{}\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 10, 1),';
				$query .= '(322, \'Content_categories (Pages and Items Html)\', \'html\', \'content_categories\', \'page_childs\', \'integrated\', \'\', \'		html Content_categories for pageItems in view page in childs (Pages and Items)\r\n	\', 0, 1, 1, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:18:"content_categories";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:83:"\n		html Content_categories for pageItems in view page in childs (Pages and Items)\n	";s:6:"folder";s:11:"page_childs";}\', \'{}\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 11, 1),';
				$query .= '(323, \'Content_featured (Pages and Items Html)\', \'html\', \'content_featured\', \'page_childs\', \'integrated\', \'\', \'		html Content_featured for pageItems in view page in childs (Pages and Items)\r\n	\', 0, 1, 1, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:16:"content_featured";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:81:"\n		html Content_featured for pageItems in view page in childs (Pages and Items)\n	";s:6:"folder";s:11:"page_childs";}\', \'{}\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 12, 1),';
				$query .= '(325, \'Content_form_edit (Pages and Items Html)\', \'html\', \'content_form_edit\', \'page_childs\', \'integrated\', \'\', \'		html Content_form_edit for pageItems in view page in childs (Pages and Items)\r\n	\', 0, 1, 1, 0, \'a:10:{s:4:"type";s:4:"html";s:4:"name";s:17:"content_form_edit";s:12:"creationdate";s:13:"December 2010";s:6:"author";s:13:"Carsten Engel";s:9:"copyright";s:50:"(Copyright (C) 2009 Engelweb. All rights reserved.";s:11:"authorEmail";s:1:"-";s:9:"authorUrl";s:23:"www.pages-and-items.com";s:7:"version";s:10:"integrated";s:11:"description";s:82:"\n		html Content_form_edit for pageItems in view page in childs (Pages and Items)\n	";s:6:"folder";s:11:"page_childs";}\', \'{}\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 13, 1);
				';

				$database->setQuery($query);
				$database->query();
			}
		}
		/*
		* END check if extensions is empty, if so insert default values
		*/
	}
}
