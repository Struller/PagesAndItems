<?php
/**
* @version		2.1.5
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );

class PagesAndItemsModelBase extends JModel
{
	public $_config;

	public $dirComponent;//new MS 12 2010 eg. 'administrator/components/com_pagesanditems'
	public $dirComponentAdmin;//new MS 12 2010 eg. 'administrator/components/com_pagesanditems'
	public $dirComponentSite;//new MS 12 2010 eg. 'components/com_pagesanditems'
	//public $dirImages = '';
	//public $dirIcons = '';
	//public $joomlaVersion = '1.5';
	public $db = null;
	
	/*
	public $pluginSystem = 0;//pluginSystem? replace the $mambot
	public $pluginContent = 0;//pluginContent? replace the $plugin
	public $pluginSystemEnabled = 0;//pluginSystem? replace the $mambot
	public $pluginContentEnabled = 0;//pluginContent? replace the $plugin
	*/
	
	public $dirPlugins;

	public $live_site;

	public $isSite = 0; //same as in JApplication Is site interface
	public $isAdmin = 0; //same as in JApplication Is admin interface

	public $is_admin = 0; //remove if all rename to?

	public $is_super_admin = 0; //remove and use the next
	public $isSuperAdmin = 0;

	public $app;
	//public $version = ''; //'2.1.5 mvc';//
	/**
	 * Overridden constructor
	 * @access	protected
	 */
	function __construct() //$id = null,$edit = null)
	{
		//echo('PagesAndItemsModelPagesAndItems');

		parent::__construct();

		/*
		todo reduce the call from setConfig() if we have more then the model base

		can the childs get the config from this?
		*/
		$this->setConfig();
	}

	function redirect_to_url($url, $message)
	{
		$app = JFactory::getApplication();
		$app->redirect($url, $message);
	}

	
	//remove it
	function &getConfig()
	{
		return $this->_config;
	}
	
	//remove it
	function setConfig()
	{
		/*
		we will get the version over the xml
		have we more than one xml?
		*/
		/*
		jimport('joomla.filesystem.folder');
		$folder = realpath(dirname(__FILE__).'..'.DS.'..'.DS);
		$files = JFolder::files($folder,'.xml',false,true);
		if(count($files))
		{
			foreach($files as $file)
			{
				$xml = simplexml_load_file($file);
				if ($xml)
				{
					//if ( is_object($xml) && is_object($xml->install))
					//if ( is_object($xml) && (is_object($xml->install || is_object($xml->extension)))
					if ( is_object($xml) && (is_object($xml->install) || is_object($xml->extension)))
					{
						//ok we have the install file
						//we will get the version
						$element = (string)$xml->version;
						$this->version = $element ? $element : '';
					}
				}
				//$file = $files[0];
				//ok we get the file
				//$fileName = JFile::getName($file);
			}
		}
		*/
		$db = & JFactory::getDBO();


		/*
		$db->setQuery("SELECT config "
		."FROM #__pi_config "
		."WHERE id='pi' "
		."LIMIT 1"
		);

		$temp = $db->loadObjectList();
		$temp = $temp[0];
		$raw = $temp->config;

		//get page attributes
		$pos_start_page_attribs = strpos($raw, 'START_PAGE_NEW_ATTRIBUTES');
		$start_of_vars = $pos_start_page_attribs+26;
		$page_new_attribs = substr($raw, $start_of_vars, 99999);
		$pi_config['page_new_attribs'] = $page_new_attribs;

		//get just the config vars
		$rest_of_config = substr($raw, 0, $pos_start_page_attribs);

		$params = explode( "\n", $rest_of_config);

		for($n = 0; $n < count($params); $n++){
			$temp = explode('=',$params[$n]);
			$var = $temp[0];
			$value = '';
			if(count($temp)==2){
				$value = trim($temp[1]);
				if($value=='false'){
					$value = false;
				}
				if($value=='true'){
					$value = true;
				}
			}
			$pi_config[$var] = $value;
		}

		//reformat cheatsheet config
		$temp_cheatsheet = $pi_config['plugin_syntax_cheatcheat'];

		$temp_cheatsheet = str_replace('[newline]','',$temp_cheatsheet);
		$temp_cheatsheet = str_replace('[equal]','=',$temp_cheatsheet);
		$pi_config['plugin_syntax_cheatcheat'] = $temp_cheatsheet;

		//reformat item_save_redirect_url
		$temp_item_save_redirect_url = $pi_config['item_save_redirect_url'];
		$temp_item_save_redirect_url = str_replace('[equal]','=',$temp_item_save_redirect_url);
		$pi_config['item_save_redirect_url'] = $temp_item_save_redirect_url;

		$this->_config = $pi_config;
		*/
		$this->_config = PagesAndItemsHelper::getConfig();

		$this->app = &JFactory::getApplication();
		//check if admin
		if($this->app->isAdmin())
		{
			$this->is_admin = 1;
			$this->isAdmin = 1;
		}
		else
		{
			$this->isSite = 1;
		}

		$this->dirComponentAdmin = PagesAndItemsHelper::getDirComponentAdmin();
		$this->dirComponentSite = PagesAndItemsHelper::getDirComponentSite();
		$this->dirComponent = PagesAndItemsHelper::getDirComponent();

		/*
		$this->dirComponentAdmin = str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).DS.'..')));
		$this->dirComponentSite = str_replace('administrator/','',$this->dirComponentAdmin);

		if($this->isSite)
		{
			$this->dirComponent = $this->dirComponentSite;
		}
		else
		{
			$this->dirComponent = $this->dirComponentAdmin;
		}
		*/
		//$this->option = str_replace('components/','',$this->dirComponentSite);

		//$version = new JVersion();
		//$this->joomlaVersion = $version->getShortVersion();
		/*
		if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
		{
			$this->option = 'com_pagesanditems';
		}
		else
		{
			$this->option = 'com_pagesanditems';
		}

		$this->dirComponentAdmin =str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',JPATH_ADMINISTRATOR.DS.'components'.DS.$this->option));//JPATH_COMPONENT_ADMINISTRATOR));
		$this->dirComponentSite = str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',JPATH_SITE.DS.'components'.DS.$this->option));//JPATH_COMPONENT_SITE));
		$this->dirComponent = str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',JPATH_BASE.DS.'components'.DS.$this->option));//JPATH_COMPONENT));
		*/
		//$path = realpath(dirname(__FILE__).DS.'..');

		//$this->dirImages = PagesAndItemsHelper::getDirImages();
		//$this->dirIcons = PagesAndItemsHelper::getDirIcons();

		//$this->dirImages = str_replace(DS,'/',DS.$this->dirComponentAdmin.DS.'images'.DS);
		//$this->dirIcons = str_replace(DS,'/',DS.$this->dirComponentAdmin.DS.'media'.DS.'images'.DS.'icons'.DS); //only for Test

		//$this->dirIcons;
		//defined('COM_PAGESANDITEMS_DIR_ICONS') or define('COM_PAGESANDITEMS_DIR_ICONS',$this->dirIcons);
		//defined('COM_PAGESANDITEMS_DEFAULT_LANG') or define('COM_PAGESANDITEMS_DEFAULT_LANG',$this->_config['language']);
		$this->dirPlugins = str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',JPATH_PLUGINS));



		//$this->user_type = PagesAndItemsHelper::getUserType();

		//set var user_type and user_id
		$user =& JFactory::getUser();
		$this->user_type = $user->get('usertype');
		$user_id = $user->get('id');
		$this->user_id = $user_id;

		if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
		{
			if($this->user_type == 'Super Administrator')
			{
				$this->is_super_admin = 1;
				$this->isSuperAdmin = 1;
			}
		}
		else
		{
			//here we get an array
			$this->user_type = $this->get_usertype();
		}


		$this->live_site =JURI::root(); //$this->app->isAdmin() ? JURI::root() : JURI::base();


		if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
		{
			$this->check_sections();
		}
		/*
		//check if content plugin is installed
		if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
		{
			$tableName = '#__plugins';
			$where = '';
		}
		else
		{
			$tableName = '#__extensions';
			$where = " AND type='plugin' ";
		}

		jimport('joomla.plugin.helper');
		if(JPluginHelper::isEnabled( 'content', 'pagesanditems' ))
		{
			$this->pluginContentEnabled = 1; //to be used again in item.php
			$this->pluginContent = 1;//to be used again in item.php
		}
		else
		{

			$query = 'SELECT *'
				. ' FROM '.$tableName
				. " WHERE element ='pagesanditems' "
				. " AND folder = 'content' "
				. $where;
			$db->setQuery($query);
			$row = $db->loadObject();
			if(!$row)
			{
				JError::raiseWarning( 100, JText::_('COM_PAGESANDITEMS_PLUGIN_CONTENT_NOT_INSTALLED') );
			}
		}
		if(JPluginHelper::isEnabled( 'system', 'pagesanditems' ))
		{
			$this->pluginSystemEnabled = 1;//to be used again in item.php
			$this->pluginSystem = 1;
		}
		else
		{
			$query = 'SELECT *'
				. ' FROM '.$tableName
				. ' WHERE element ='.$db->Quote('pagesanditems')
				. ' AND folder = '.$db->Quote('content')
				. $where;
			$db->setQuery($query);
			$row = $db->loadObject();
			if(!$row)
			{
				JError::raiseWarning( 100, JText::_('COM_PAGESANDITEMS_PLUGIN_SYSTEM_NOT_INSTALLED') );
			}
		}
		*/

	}

	function get_usertype()
	{
		jimport( 'joomla.access.access' );
		$groups = JAccess::getGroupsByUser($this->user_id);
		if(in_array(8, $groups))
		{
			$this->is_super_admin = 1;
			$this->isSuperAdmin = 1;
		}
		return $groups;
	}

	function check_sections()
	{
		//check if sections are configured
		$temp_sections = explode(',',$this->_config['sections']);
		if($temp_sections[0]=='' && !$this->_config['sections_from_db'])
		{

		}
	}


		/*
	function getJoomlaVersion()
	{
		return $this->joomlaVersion;
	}
	*/
	/*
	function getDirIcons()
	{
		return $this->dirIcons;
	}
	*/
}
