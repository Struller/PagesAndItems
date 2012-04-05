<?php
/**
* @version		2.1.5
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// No direct access
defined('_JEXEC') or die;



class PagesAndItemsHelper{

/*
*******************
* config
******************
*/

	function getPathExtensions()
	{
		static $pathExtensions;
		if (isset($pathExtensions))
		{
			return $pathExtensions;
		}
		$pathExtensions = realpath(dirname(__FILE__).DS.'..'.DS.'extensions');
		return $pathExtensions;
	}

	function loadExtensionLanguage($name,$type,$folder = '')
	{
		$pathExtensions = self::getPathExtensions();

		if($folder && $folder != '')
		{
			$extension_folder = str_replace('/','_',$folder);
			$prefix = $type.'_'.$extension_folder;
			$path = $pathExtensions.DS.$type.'s'.DS.$folder;
		}
		else
		{
			$prefix = $type;
			$path = $pathExtensions.DS.$type.'s';

		}
		$extension = 'pi_extension_'.$prefix.'_'.$name;
		$path = $path.DS.$name;

		$lang = &JFactory::getLanguage();
		$defaultLang = $lang->getDefault();
		$lang->load(strtolower($extension), $path, null, false)
		||	$lang->load(strtolower($extension), $pathExtensions, null, false)
		||	$lang->load(strtolower($extension), $path, $defaultLang, false)
		||	$lang->load(strtolower($extension), $pathExtensions, $defaultLang, false)
		;
	}

	function getdTreeIcons($dtree)
	{
		$html = "$dtree.icon = {";
		$html .= "root	: '".PagesAndItemsHelper::getdirIcons()."icon-16-menu.png',\n";
		$html .= "folder	: '".PagesAndItemsHelper::getdirIcons()."folder.gif',\n";
		$html .= "folderOpen	: '".PagesAndItemsHelper::getdirIcons()."folderopen.gif',\n";
		$html .= "node		: '".PagesAndItemsHelper::getdirIcons()."page.gif',\n";
		$html .= "empty		: '".PagesAndItemsHelper::getdirIcons()."empty.gif',\n";
		$html .= "line		: '".PagesAndItemsHelper::getdirIcons()."line.gif',\n";
		$html .= "join		: '".PagesAndItemsHelper::getdirIcons()."join.gif',\n";
		$html .= "joinBottom	: '".PagesAndItemsHelper::getdirIcons()."joinbottom.gif',\n";
		$html .= "plus		: '".PagesAndItemsHelper::getdirIcons()."plus.gif',\n";
		$html .= "plusBottom	: '".PagesAndItemsHelper::getdirIcons()."plusbottom.gif',\n";
		$html .= "minus		: '".PagesAndItemsHelper::getdirIcons()."minus.gif',\n";
		$html .= "minusBottom	: '".PagesAndItemsHelper::getdirIcons()."minusbottom.gif',\n";
		$html .= "nlPlus	: '".PagesAndItemsHelper::getdirIcons()."nolines_plus.gif',\n";
		$html .= "nlMinus	: '".PagesAndItemsHelper::getdirIcons()."nolines_minus.gif'\n";
		$html .= "};\n";
		return $html;
	}

	function getApp()
	{
		static $app;
		if (isset($app))
		{
			return $app;
		}
		$app = &JFactory::getApplication();
		return $app;
	}

	function getDb()
	{
		static $getDb;
		if (isset($getDb))
		{
			return $getDb;
		}
		$getDb = JFactory::getDBO();
		return $getDb;
	}

	function redirect_to_url($url, $message){
		PagesAndItemsHelper::getApp()->redirect($url, $message);
	}

	function checkPlugin($plugin)
	{
		
		$enabled = 0;
		$installed = 0;
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
		if(JPluginHelper::isEnabled( $plugin, 'pagesanditems' ))
		{
			$enabled = 2;
		}
		else
		{
			$db = & JFactory::getDBO();
			$query = 'SELECT *'
				. ' FROM '.$tableName
				. " WHERE element ='pagesanditems' "
				. " AND folder = 'content' "
				. $where;
			$db->setQuery($query);
			$row = $db->loadObject();
			if(!$row)
			{
				$installed = 0;
				JError::raiseWarning( 100, JText::_('COM_PAGESANDITEMS_PLUGIN_CONTENT_NOT_INSTALLED') );
			}
			else
			{
				$installed = 1;
			}
		}
		return $enabled ? $enabled : $installed;

	}
	
	//TODO as version_compare
	/*
	version_compare ( string $version1 , string $version2 [, string $operator ] )
	
	The possible operators are: <, lt, <=, le, >, gt, >=, ge, ==, =, eq, !=, <>, ne respectively.
	This parameter is case-sensitive, so values should be lowercase.
	
	By default, version_compare() returns -1 if the first version is lower than the second, 0 if they are equal, and 1 if the second is lower.
	When using the optional operator argument, the function will return TRUE if the relationship is the one specified by the operator, FALSE otherwise.

	
	
	function getIsJoomlaVersion($typ = '>=',$version = '2.5')
	{
		//$joomlaVersion = PagesAndItemsHelper::getJoomlaVersion();
		switch($typ)
		{
			case '<':
			case 'lt':
			case '<=':
			case 'le':
			case '>':
			case 'gt':
			case '>=':
			case 'ge':
			case '!=':
			case '<>':
			case '==':
			case '=':
			case 'eq':
				return version_compare ( JVERSION, $version, $type );
			break;
			
			default:
				
			break;
		}
	}
	
	
	
	*/
	function getIsJoomlaVersion($typ = '<',$version = '1.6')
	{
		$joomlaVersion = PagesAndItemsHelper::getJoomlaVersion();
		switch($typ)
		{
			case '<':
			case 'lt': //?
				return $joomlaVersion < $version;
			break;
			
			case '<=':
			case 'le': //?
				return $joomlaVersion > $version;
			break;
			
			case '>':
			case 'gt': //?
				return $joomlaVersion > $version;
			break;
			
			case '>=':
			case 'ge': //?
				return $joomlaVersion >= $version;
			break;
			
			case '!=':
				return $joomlaVersion != $version;
			break;
			
			case '<>':
				return $joomlaVersion <> $version;
			break;
			
			case '==':
			case '=':
			case 'eq': //?
				return $joomlaVersion == $version;
			break;
			
		}
	}

	function getJoomlaVersion()
	{
		//return JVERSION
		static $joomlaVersion;
		if (isset($joomlaVersion))
		{
			return $joomlaVersion;
		}
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		return $joomlaVersion;
		}
		
	function getPagesAndItemsVersion()
	{
		static $PagesAndItemsVersion;
		if (isset($PagesAndItemsVersion))
		{
			return $PagesAndItemsVersion;
		}
		
		require_once(realpath(dirname(__FILE__).DS.'..'.DS.'includes').DS.'version.php');
		$version = new PagesAndItemsVersion();
		return $PagesAndItemsVersion = $version->getVersionNr();
	}



	function saveConfig($config = null)
	{
		if(!$config)
		{
			return false;
		}

		if(is_array($config))
		{
			if($config['menus'] && $config['menus'] != '')
			{
				$menus = array();
				foreach($config['menus'] as $menu)
				{
					if($menu = implode(";",$menu))
					{
						if($menu != '')
						$menus[] = $menu; //implode(";",$menu);
					}
				}
				$config['menus'] = implode( ",", $menus);
			}
			if($config['itemtypes'] && $config['itemtypes'] != '')
			{
				$config['itemtypes'] = implode( ",", $config['itemtypes']);
			}
			$configuration = array();
			foreach($config as $key => $value)
			{
				$configuration[] = $key.'='.$value;
			}
			
			$configuration = implode("\n",$configuration);
			//update config
			$db = & JFactory::getDBO();
			$db->setQuery( "UPDATE #__pi_config SET config='$configuration' WHERE id='pi' ");
			$db->query();
			
		}
		
		return false;



	}

	function getUseCheckedOut()
	{
		static $useCheckedOut;
		if (isset($useCheckedOut))
		{
			return $useCheckedOut;
		}
		$config = PagesAndItemsHelper::getConfigAsRegistry();
		$useCheckedOut = $config->get('useCheckedOut',0);
		return $useCheckedOut;
	}

	function getConfigAsRegistry()
	{
		static $configRegistry;
		if (isset($configRegistry))
		{
			return $configRegistry;
		}

		$config = PagesAndItemsHelper::getConfig();
		$menus = $config['menus'] ? $config['menus'] : '';
		$temp1 = $menus ? explode( ",", $menus) : '';
		if($temp1)
		{
			$temp2_2 = array();
			foreach($temp1 as $temp1_1)
			{
				if($temp1_1 != '')
				{
					$temp3 = explode( ";", $temp1_1);
					$temp2_2[$temp3[0]] = $temp3;
				}
			}
			$config['menus'] = $temp2_2; 

		}
		$itemtypes = $config['itemtypes']  ? $config['itemtypes'] : '';
		$temp4 = $itemtypes ? explode( ",", $itemtypes) : '';
		if($temp4)
		{
			$temp6 = array();
			foreach($temp4 as $temp5)
			{
				if($temp5 != '')
				$temp6[$temp5] = $temp5;
			}
			
			$config['itemtypes'] = $temp6;
		}
		
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		if($joomlaVersion < '1.6')
		{
				$params = new JParameter();
				$params->loadArray($config);
		}
		else
		{
			$params = new JRegistry;
			$params->loadArray($config);
		}
		return $params;
	}


	//ms: add
	//TODO ms: remove if getItemtypes changed
	function changeConfigItemtype($config = null, $itemtype = null, $change = 'add')
	{
		if(!$config && !$itemtype || !$itemtype)
		{
			return;
		}
		if(!$config)
		{
			$config = PagesAndItemsHelper::getConfig();
		}
		$save = false;
		$itemtypes = $config['itemtypes'];
		$temp = explode( ",", $itemtypes);
		if(!in_array($itemtype,$temp) && $change == 'add')
		{
			$temp[] = $itemtype;
			$save = true;
		}
		elseif(in_array($itemtype,$temp) && $change == 'remove')
		{
			for($n = 0; $n < count($temp); $n++)
			{
				if($temp[$n] == $itemtype)
				{
					unset($temp[$n]);
					$save = true;
				}
			}

		}
		$value = implode(',',$temp);
		if($save)
		{
			PagesAndItemsHelper::changeConfig($config, 'itemtypes', $value);
		}
	}

	//ms: add
	function changeConfig($config = null, $changeKey = null, $changeValue = null)
	{
		if((!$config && !$changeKey && !$changeValue) || ($config && !$changeKey && !$changeValue) )
		{
			return;
		}

		if(!$config)
		{
			$config = PagesAndItemsHelper::getConfig();
		}

		$configtemp = array();
		foreach($config as $key => $value)
		{
			if($key != '')
			{
				if($key == $changeKey)
				{
					$configtemp[] = $key.'='.$changeValue;
				}
				else
				{
					if($key == 'permissions' && is_array($value) )
					{
						$value = implode(',', $value);
					}
					$configtemp[] = $key.'='.$value;
				}
			}
		}
		$configuration = implode("\n",$configtemp);

		//update config

		$db = & JFactory::getDBO();
		$db->setQuery( "UPDATE #__pi_config SET config='$configuration' WHERE id='pi' ");
		$db->query();
	}

	function getConfig()
	{
		static $config;
		if (isset($config))
		{
			return $config;
		}
		$db = & JFactory::getDBO();

		$db->setQuery("SELECT config "
		."FROM #__pi_config "
		."WHERE id='pi' "
		."LIMIT 1"
		);

		$temp = $db->loadObjectList();
		$temp = $temp[0];
		$raw = $temp->config;

		$params = explode( "\n", $raw);

		for($n = 0; $n < count($params); $n++)
		{
			$var = '';
			$temp = explode('=',$params[$n]);
			$var = trim($temp[0]);
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
			//make sure no empty lines
			if($var != ''){
				$config[$var] = $value;
			}
		}

		//reformat cheatsheet config
		$temp_cheatsheet = $config['plugin_syntax_cheatcheat'];

		$temp_cheatsheet = str_replace('[newline]','',$temp_cheatsheet);
		$temp_cheatsheet = str_replace('[equal]','=',$temp_cheatsheet);
		$config['plugin_syntax_cheatcheat'] = $temp_cheatsheet;

		//reformat item_save_redirect_url
		$temp_item_save_redirect_url = $config['item_save_redirect_url'];
		$temp_item_save_redirect_url = str_replace('[equal]','=',$temp_item_save_redirect_url);
		$config['item_save_redirect_url'] = $temp_item_save_redirect_url;

		return $config;

	}


	function getDirJS($juri = false){
		$juri = $juri ? JURI::root(true).'/' : '';
		return $juri.str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).DS.'..'.DS.'javascript')));
	}

	function getDirCSS($juri = false){
		$juri = $juri ? JURI::root(true).'/' : '';
		return $juri.str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).DS.'..'.DS.'css')));
	}

	function getDirIcons(){

		static $dirIcons;
		if(isset($dirIcons))
		{
			return $dirIcons;
		}
		/*
		$dirIcons = 'components/com_pagesanditems/media/images/icons/';
		//if(!$this->app->isAdmin()){ is causing error when saving a new cat blog ?!!
		$app = JFactory::getApplication();
		if(!$app->isAdmin()){
			$dirIcons = 'administrator/'.$dirIcons;
		}
		*/
		//ms: 04.05.2011 another way to get the correct path this will work also on subdomain (JURI::root(true) return the subdomain)
		$dirIcons = JURI::root(true).'/'.str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).DS.'..'.DS.'media'.DS.'images'.DS.'icons').DS));
		//defined('COM_PAGESANDITEMS_DIR_ICONS') or define('COM_PAGESANDITEMS_DIR_ICONS',$dirIcons);
		return $dirIcons;
	}

	function getDirImages()
	{
		static $dirImages;
		if(isset($dirImages))
		{
			return $dirImages;
		}
		/*
		$dirImages = 'components/com_pagesanditems/images/';
		$app = JFactory::getApplication();
		if($app->isAdmin()){
			$dirImages = 'administrator/'.$dirImages;
		}
		*/
		//ms: 04.05.2011 another way to get the correct path this will work also on subdomain (JURI::root(true) return the subdomain)
		$dirImages = JURI::root(true).'/'.str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).DS.'..'.DS.'images').DS));
		//defined('COM_PAGESANDITEMS_DIR_IMAGES') or define('COM_PAGESANDITEMS_DIR_IMAGES',$dirImages);
		return $dirImages;
	}

	function getDirComponentAdmin()
	{
		//ms: 04.05.2011 another way to get the correct path this will work also on subdomain (JURI::root(true) return the subdomain)
		//return JURI::root(true).'/'.str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).DS.'..')));


		//return str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).DS.'..')));
		//JURI::root(true).'/'.str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).DS.'..').DS));
		return 'administrator/components/com_pagesanditems';
	}

	function getDirComponentSite()
	{
		//ms: 04.05.2011 another way to get the correct path this will work also on subdomain (JURI::root(true) return the subdomain)
		//return JURI::root(true).'/'.str_replace(DS,'/',str_replace(JPATH_ROOT.DS.'administrator'.DS,'',realpath(dirname(__FILE__).DS.'..')));

		//return str_replace(DS,'/',str_replace(JPATH_ROOT.DS.'administrator'.DS,'',realpath(dirname(__FILE__).DS.'..')));
		return 'components/com_pagesanditems';
	}


	function getDirComponent()
	{
		//check if admin
		if(PagesAndItemsHelper::getApp()->isAdmin())
		{
			//return PagesAndItemsHelper::getDirComponentAdmin();

			//return str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).DS.'..')));
			return 'administrator/components/com_pagesanditems';
		}
		else
		{
			//return PagesAndItemsHelper::getDirComponentSite();

			//return str_replace(DS,'/',str_replace(JPATH_ROOT.DS.'administrator'.DS,'',realpath(dirname(__FILE__).DS.'..')));
			return 'components/com_pagesanditems';
		}
	}

	function getIsSuperAdmin()
	{
		static $isSuperAdmin;
		if(isset($isSuperAdmin))
		{
			return $isSuperAdmin;
		}
		/*
		
		*/
		$user =& JFactory::getUser();
		$user_type = $user->get('usertype');
		$user_id = $user->get('id');
		//$this->user_id = $user_id;
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		if($joomlaVersion < '1.6')
		{
			if($user_type == 'Super Administrator')
			{
				$isSuperAdmin = true;
				//return true;
			}
			else
			{
				$isSuperAdmin = false;
				//return false;
			}
		}
		else
		{

			
			//'core.admin' is  Super Admin
			//to the root asset node.
			$isSuperAdmin = $user->authorise('core.admin');

			/*
			$groups = PagesAndItemsHelper::get_usertype($user_id);
			if(in_array(8, $groups))
			{
				//ok? have we the super user?
				return true;
			}
			else
			{
				return false;
			}
			*/
		}
		return $isSuperAdmin;
	}

	function getUserId()
	{
		static $userId;
		if(isset($userId))
		{
			return $userId;
		}
		$user =& JFactory::getUser();
		$userId = $user->get('id');
		return $userId;
	}

	function getUserType()
	{
		$user =& JFactory::getUser();
		$user_type = $user->get('usertype');
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		if($joomlaVersion < '1.6')
		{
			return $user_type;
		}
		else
		{
			$user_id = $user->get('id');
			return PagesAndItemsHelper::get_usertype($user_id);


			/*
$groups =
[array]
	[integer] 0 = 1
	[integer] 1 = 8

	id	parent_id	lft	rgt	title
	1	0		1	20	Public
	2	1		6	17	Registered
	3	2		7	14	Author
	4	3		8	11	Editor
	5	4		9	10	Publisher
	6	1		2	5	Manager
	7	6		3	4	Administrator
	8	1		18	19	Super Users

	12	2		15	16	Customer Group
	10	3		12	13	Shop Suppliers





			if(in_array(8, $groups))
			{
				//ok? have we the super user?
				return true;
			}
			else
			{
				return false;
			}
			*/
		}
	}

	function get_usertype($user_id)
	{
		jimport( 'joomla.access.access' );
		$groups = JAccess::getGroupsByUser($user_id);

		return $groups;
	}

	function getIsAdmin()
	{
		static $isAdmin;
		if(isset($isAdmin))
		{
			return $isAdmin;
		}
		$app = &JFactory::getApplication();
		//check if admin
		$isAdmin = $app->isAdmin();
		return $isAdmin;
		if($app->isAdmin())
		{
			return true;
		}
		else
		{
			return false;
		}
	}



	/**
	 * Displays a calendar control field with optional time
	 *
	 * @param	string	The date value
	 * @param	string	The name of the text field
	 * @param	string	The id of the text field
	 * @param	string	The date format
	 * @param	array	Additional html attributes
	 */
	function calendar($value, $name, $id, $format = '%Y-%m-%d', $attribs = null, $params = array())
	{
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		if($joomlaVersion < '1.6')
		{
			JHTML::_('behavior.calendar'); //load the calendar behavior

			if (is_array($attribs))
			{
				$attribs = JArrayHelper::toString( $attribs );
			}
			// Setup options object
			$opt['showsTime'] = (array_key_exists('showsTime', $params)) ? $params['showsTime'] : 'false';

			//$options = JHTMLBehavior::_getJSObject($opt);

			$document =& JFactory::getDocument();
			$document->addScriptDeclaration('window.addEvent(\'domready\', function() {Calendar.setup({
			inputField	:	 "'.$id.'",			// id of the input field
			ifFormat	:	"'.$format.'",		// format of the input field
			button		:	"'.$id.'_img",		// trigger for the calendar (button ID)
			align		:	"Tl",				// alignment (defaults to "Bl")
			showsTime	:	'.$opt['showsTime'].',
			singleClick	:	true
			});});');

			return '<input type="text" name="'.$name.'" id="'.$id.'" value="'.htmlspecialchars($value, ENT_COMPAT, 'UTF-8').'" '.$attribs.' />'.
				 '<img class="calendar" src="'.JURI::root(true).'/templates/system/images/calendar.png" alt="calendar" id="'.$id.'_img" />';
		}
		else
		{
			static $done;

			if ($done === null)
			{
				$done = array();
			}

			$readonly = isset($attribs['readonly']) && $attribs['readonly'] == 'readonly';
			$disabled = isset($attribs['disabled']) && $attribs['disabled'] == 'disabled';
			if (is_array($attribs))
			{
				$attribs = JArrayHelper::toString($attribs);
			}

			if ((!$readonly) && (!$disabled))
			{
				// Load the calendar behavior
				JHtml::_('behavior.calendar');
				JHtml::_('behavior.tooltip');

				// Only display the triggers once for each control.
				if (!in_array($id, $done))
				{
					// Setup options object
					$opt['showsTime'] = (array_key_exists('showsTime', $params)) ? $params['showsTime'] : 'false';

					$document = JFactory::getDocument();
					$document->addScriptDeclaration('window.addEvent(\'domready\', function() {Calendar.setup({
					inputField: "'.$id.'",		// id of the input field
					ifFormat: "'.$format.'",	// format of the input field
					button: "'.$id.'_img",		// trigger for the calendar (button ID)
					align: "Tl",				// alignment (defaults to "Bl")
					singleClick: true,
					showsTime	:	'.$opt['showsTime'].',
					firstDay: '.JFactory::getLanguage()->getFirstDay().'
					});});');
					$done[] = $id;
				}
			}

			return '<input type="text" title="'.(0!==(int)$value ? JHtml::_('date',$value):'').'" name="'.$name.'" id="'.$id.'" value="'.htmlspecialchars($value, ENT_COMPAT, 'UTF-8').'" '.$attribs.' />'.
				($readonly ? '' : JHTML::_('image','system/calendar.png', JText::_('JLIB_HTML_CALENDAR'), array( 'class' => 'calendar', 'id' => $id.'_img'), true));
		}
	}

/*
*******************
* date % format
******************
*/

	function get_date_now($with_offset,$format = false)
	{
		$app =& JFactory::getApplication();
		$date = JFactory::getDate();
		jimport('joomla.utilities.date');
		if($with_offset)
		{
			$offset = $app->getCfg('offset');
			//$summertime = date( 'I' ); //this will only check if the actuall time have summertime
			$summertime = date( 'I', $date->toUnix() ); //this will work
			if($summertime)
			{
				$offset = $offset +1;
			}
			$date->setOffset($offset);
		}
		$config = PagesAndItemsHelper::getConfig();
		$date_now = $date->toFormat($format);

		return $date_now;
	}

	function get_date_to_format($date,$format = false)
	{
		//for Joomla 1.6
		$app =& JFactory::getApplication();
		$offset = $app->getCfg('offset');
		$summertime = date( 'I',JFactory::getDate($date)->toUnix() ); //
		if($summertime)
		{
			$offset = $offset +1;
		}
		$date = JFactory::getDate($date,$offset);
		$date = $date->toFormat($format); //deprecated in J1.6
		return $date;


		/*
		// Get some system objects.
		$config = JFactory::getConfig();
		$user	= JFactory::getUser();

		// Convert a date to UTC based on the user timezone.
		if (intval($this->value)) {
			// Get a date object based on the correct timezone.
			$jDate = JFactory::getDate($date, 'UTC');
			$jDate->setTimezone(new DateTimeZone($user->getParam('timezone', $config->get('offset'))));
			// Transform the date string.
			$date = $jDate->toMySQL(true);
		}



		*/
		$test = strtotime('2011-07-03');
		$summertime = date( 'I', $test );
		if($summertime)
		{
			//$yoffset = $yoffset +1;
		}


		$app =& JFactory::getApplication();

		$date = JFactory::getDate($date); //,$offset);

		$offset = $app->getCfg('offset');

		$summertime = date( 'I', $date->toUnix() );
		if($summertime)
		{
			$offset = $offset +1;
		}
		$date->setOffset($offset);
		//$config = PagesAndItemsHelper::getConfig();
		$date = $date->toFormat($format); //before change
		//$date = $date->toFormat($format,true);

		//$date = $date->format($format,true);
		return $date;
	}


	function get_date_ready_for_database($date,$local = false)
	{
		$app =& JFactory::getApplication();
		$offset = $app->getCfg('offset');
		$summertime = date( 'I',JFactory::getDate($date)->toUnix() ); //
		if($summertime)
		{
			$offset = $offset +1;
		}
		$date = JFactory::getDate($date,$offset);
		$date = $date->toMySQL($local);
		return $date;
	}



	function getButtonMaker($type=null,$text=null,$buttonstyle='image')
	{
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();


		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'html'.DS.'buttonmaker.php');
		$button = new ButtonMaker();
		if($type)
		{
			switch($type)
			{
				case 'close':
					if(!$text)
					{
						if($joomlaVersion < '1.6')
						{
							$text = JText::_('CLOSE');
						}
						else
						{
							$text = JText::_('JTOOLBAR_CLOSE');
						}

					}
					$button->text = $text;
					if($buttonstyle == 'image')
					{
						$button->imagePath = PagesAndItemsHelper::getDirIcons();
						$button->imageName = 'base/icon-16-cross.png';
					}
				break;

				case 'cancel':
					if(!$text)
					{
						if($joomlaVersion < '1.6')
						{
							$text = JText::_('COM_PAGESANDITEMS_CANCEL');
						}
						else
						{
							$text = JText::_('JTOOLBAR_CANCEL');
						}

					}
					$button->text = $text;
					if($buttonstyle == 'image')
					{
						$button->imagePath = PagesAndItemsHelper::getDirIcons();
						$button->imageName = 'base/icon-16-cross.png';
					}
				break;

				case 'save':
					if(!$text)
					{
						if($joomlaVersion < '1.6')
						{
							$text = JText::_('SAVE');
						}
						else
						{
							$text = JText::_('JTOOLBAR_APPLY');
						}
					}
					$button->text = $text;
					if($buttonstyle == 'image')
					{
						$button->imagePath = PagesAndItemsHelper::getDirIcons();
						$button->imageName = 'base/icon-16-save.png';
					}
				break;

				case 'saveclose':
					if(!$text)
					{
						if($joomlaVersion < '1.6')
						{
							$text = JText::_('COM_PAGESANDITEMS_SAVE').' & '.JText::_('CLOSE'); //$text = JText::_('SAVE');
						}
						else
						{
							$text = JText::_('COM_PAGESANDITEMS_SAVE');
						}

					}
					$button->text = $text;
					if($buttonstyle == 'image')
					{
						$button->imagePath = PagesAndItemsHelper::getDirIcons();
						$button->imageName = 'base/icon-16-save_close_tick_green.png';
					}
				break;
			}

		}
		return $button;
	}

/*
*********
* BEGIN *
*********
Title
toolbar
submenu
*/
	public static function addTitle($more = '')
	{
		define('COM_PAGESANDITEMS_TITLE_IS_SET',true);
		JToolBarHelper::title( JText::_( 'Pages and Items' ).' '.$more, 'pi.png' );
	}


	public static function addSubmenuFirst($vName = 'page')
	{
		$extensionType = JRequest::getVar('extensionType', ''); //is the extensionName
		//$path = JURI::root(true).str_replace(DS,'/',str_replace(JPATH_ROOT,'',JPATH_COMPONENT_ADMINISTRATOR));
		$path = PagesAndItemsHelper::getDirIcons();
		$menutype = JRequest::getVar('menutype',0);
		$menutype = $menutype ? '&menutype='.$menutype : '';
		$categoryId = JRequest::getVar('categoryId',0);
		//$pageId = JRequest::getVar('pageId',0);
		
		$configs = $vName != 'config' && $vName != 'config_custom_itemtype' && $vName != 'config_custom_itemtype_field' && $vName != 'config_itemtype' && $extensionType != 'manager' && $vName != 'managers' ;
		$items = $vName == 'item' || $vName == 'item_move_select';
		
		JSubMenuHelper::addEntry(
			//'<img src="'.$path.'/media/images/icons/icon-16-pi.png" alt="" style="vertical-align: middle;top: -2px;position: relative;" />&nbsp;Pages and Items',
			//'<img src="'.$path.'/media/images/icons/icon-16-pi.png" alt="" style="vertical-align: middle;top: -2px;position: relative;" />&nbsp;'.JText::_('COM_PAGESANDITEMS'),
			'<img src="'.$path.'icon-16-pi.png" alt="" style="vertical-align: middle;top: -2px;position: relative;" />&nbsp;'.JText::_('COM_PAGESANDITEMS'),
			'index.php?option=com_pagesanditems&view=page&layout=root'.$menutype,
			$vName == 'page' 
			//&& $vName != 'categorie'
			&& $vName != 'category'
			&& $configs
			|| ($items && !$categoryId)
		);
		
		//ms: add view categorie
		$config = PagesAndItemsHelper::getConfigAsRegistry();
		//if($config->get('enabled_view_categorie'))
		if($config->get('enabled_view_category'))
		{
			//$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
			//$sub_task = JRequest::getVar('sub_task','');
			$edit = ($categoryId > 1) ? '&sub_task=edit' : '';
			
			$edit .= ($categoryId > 1) ? '&categoryId='.$categoryId : '';
			$edit = '';
		
			JSubMenuHelper::addEntry(
			//'<img src="'.$path.'/media/images/icons/category/icon-16-category.png" alt="" style="vertical-align: middle;top: -2px;position: relative;" />&nbsp;'.JText::_('COM_PAGESANDITEMS_CATEGORIESANDITEMS'),
				'<img src="'.$path.'category/icon-16-category.png" alt="" style="vertical-align: middle;top: -2px;position: relative;" />&nbsp;'.JText::_('COM_PAGESANDITEMS_CATEGORIESANDITEMS'),
				//'index.php?option=com_pagesanditems&view=categorie'.$edit,
				'index.php?option=com_pagesanditems&view=category'.$edit,
				//$vName == 'categorie'
				$vName == 'category'
				&& $vName != 'page' 
				&& $configs
				|| ($items && $categoryId)
			);
		}
	}

	public static function addSubmenu($vName = 'page')
	{
		$extensionType = JRequest::getVar('extensionType', ''); //is the extensionName
		//$path = JURI::root(true).str_replace(DS,'/',str_replace(JPATH_ROOT,'',JPATH_COMPONENT_ADMINISTRATOR));
		$path = PagesAndItemsHelper::getDirIcons();
		PagesAndItemsHelper::addSubmenuFirst($vName);
		JSubMenuHelper::addEntry(
			//'<img src="'.$path.'/media/images/icons/base/icon-16-config.png" alt="" style="vertical-align: middle;top: -2px;position: relative;" />&nbsp;'.JText::_('COM_PAGESANDITEMS_CONFIG'),
			'<img src="'.$path.'base/icon-16-config.png" alt="" style="vertical-align: middle;top: -2px;position: relative;" />&nbsp;'.JText::_('COM_PAGESANDITEMS_CONFIG'),
			'index.php?option=com_pagesanditems&view=config',
			//'',
			$vName == 'config' || $vName == 'config_custom_itemtype' || $vName == 'config_custom_itemtype_field' || $vName == 'config_itemtype'
			);

		JSubMenuHelper::addEntry(
			//'<img src="'.$path.'/media/images/icons/base/icon-16-toolbox.png" alt="" style="vertical-align: middle;top: -2px;position: relative;" />&nbsp;'.JText::_('COM_PAGESANDITEMS_MANAGERS'),
			'<img src="'.$path.'base/icon-16-toolbox.png" alt="" style="vertical-align: middle;top: -2px;position: relative;" />&nbsp;'.JText::_('COM_PAGESANDITEMS_MANAGERS'),
			'index.php?option=com_pagesanditems&view=managers',
			$vName == 'managers' || $extensionType == 'manager'
		);
	}

	//to do move each bit of toolbar script to the view.html.php where it is used
	public static function addToolbar($vName = 'page',$vLayout = 'root') //,$pathPluginsItemtypes = null)
	{
		$sub_task = JRequest::getVar('sub_task', '');
		switch ($vName)
		{
			case 'Xmanage':

				JToolBarHelper::custom('manage.publish', 'publish.png', 'publish_f2.png', 'COM_PAGESANDITEMS_ENABLE', true);
				JToolBarHelper::custom('manage.unpublish', 'unpublish.png', 'unpublish_f2.png', 'COM_PAGESANDITEMS_DISABLE', true);
				JToolBarHelper::divider();
				//JToolBarHelper::divider();
				//$path = str_replace(DS,'/',str_replace(JPATH_SITE,'',JPATH_COMPONENT_ADMINISTRATOR));
				/*
				$path = JURI::root(true).str_replace(DS,'/',str_replace(JPATH_ROOT,'',JPATH_COMPONENT_ADMINISTRATOR));
				//$path.'/media/images/icons/base/icon-32-refresh.png
				$doc =& JFactory::getDocument();

				$style = '
				.icon-32-refresh
				{
					background-image: url("'.$path.'/media/images/icons/icon-32-refresh.png");
				}
				';
				$doc->addStyleDeclaration($style);
				*/


				JToolBarHelper::deleteList('', 'manage.remove','COM_PAGESANDITEMS_UNINSTALL');
				//JToolBarHelper::cancel('manage.cancel', 'COM_PAGESANDITEMS_CANCEL');
				JToolBarHelper::divider();
				//JToolBarHelper::custom('manage.refresh', 'refresh', 'refresh','refresh Cache');
				JToolBarHelper::custom('manage.refresh', 'refresh', 'refresh','JTOOLBAR_REFRESH_CACHE',true);
				JToolBarHelper::divider();
				JToolBarHelper::cancel('managers.cancel', 'COM_PAGESANDITEMS_CANCEL');
				//JToolBarHelper::back();
				// JToolBarHelper::back();

			break;
			case 'manageextension':
				JRequest::setVar('hidemainmenu', true);
				//JToolBarHelper::title(JText::sprintf('COM_PLUGINS_MANAGER_PLUGIN', JText::_($this->item->name)), 'plugin');
				JToolBarHelper::save('manageextension.save', 'COM_PAGESANDITEMS_SAVE');
				JToolBarHelper::apply('manageextension.apply', 'COM_PAGESANDITEMS_APPLY');
				//JToolBarHelper::back();
				JToolBarHelper::divider();
				JToolBarHelper::cancel('manageextension.cancel', 'COM_PAGESANDITEMS_CANCEL');
			break;

			case 'install':
				//JToolBarHelper::back();
				//JToolBarHelper::cancel('managers.cancel', 'COM_PAGESANDITEMS_CANCEL');
				//JToolBarHelper::cancel('install.cancel', 'COM_PAGESANDITEMS_CANCEL');
			break;

			case 'manageinstall':
				//JToolBarHelper::back();
				JToolBarHelper::cancel('managers.cancel', 'COM_PAGESANDITEMS_CANCEL');
				//JToolBarHelper::cancel('install.cancel', 'COM_PAGESANDITEMS_CANCEL');
			break;


			case 'page':
				//ms: move to views/page/view.html.php
				/*
				if($vLayout == 'root')
				{
					if($sub_task=='new')
					{
						JToolBarHelper::save( 'page.root_save', JText::_('COM_PAGESANDITEMS_SAVE') );
						JToolBarHelper::apply( 'page.root_apply', JText::_('COM_PAGESANDITEMS_APPLY') );
						JToolBarHelper::divider();
						JToolBarHelper::cancel( 'page.root_cancel', JText::_('COM_PAGESANDITEMS_CANCEL') );
					}
					else
					{
						JToolBarHelper::apply( 'page.root_save', JText::_('COM_PAGESANDITEMS_SAVE') );
					}
				}
				else
				{
					if($sub_task=='new')
					{
						JToolBarHelper::save( 'page.page_save', JText::_('COM_PAGESANDITEMS_SAVE') );
						JToolBarHelper::apply( 'page.page_apply', JText::_('COM_PAGESANDITEMS_APPLY') );
						JToolBarHelper::divider();
						JToolBarHelper::cancel( 'page.cancel', JText::_('COM_PAGESANDITEMS_CANCEL') );
					}
					else
					{
						JToolBarHelper::save( 'page.page_save', JText::_('COM_PAGESANDITEMS_SAVE') );
						JToolBarHelper::apply( 'page.page_apply', JText::_('COM_PAGESANDITEMS_APPLY') );

						//ms: i have comment out the next lines
						//one problem is if user have change the title... all of this will not save
						//only the state is changed
						//all of this the user can handle in select 'state

						//JToolBarHelper::divider();
						//JToolBarHelper::publish( 'page.page_publish');
						//JToolBarHelper::unpublish( 'page.page_unpublish');
						//JToolBarHelper::trash( 'page.page_trash','JTOOLBAR_TRASH',false);
						//JToolBarHelper::divider();

						JToolBarHelper::custom('page.page_delete','delete','delete','JTOOLBAR_DELETE',false);
						//JToolBarHelper::divider();
						JToolBarHelper::custom( 'page_move_select', 'move.png', 'move_f2.png', JText::_('COM_PAGESANDITEMS_MOVE'), false );
						JToolBarHelper::divider();
						JToolBarHelper::cancel( 'page.cancel', JText::_('COM_PAGESANDITEMS_CANCEL') );
					}

				}
				*/
			break;

			case 'item':
				//ms: move to views/item/view.html.php
				/*
				if($sub_task=='new')
				{
					JToolBarHelper::save( 'item.item_save', JText::_('COM_PAGESANDITEMS_SAVE_ITEM') );
					JToolBarHelper::apply( 'item.item_apply', JText::_('COM_PAGESANDITEMS_APPLY') );
					JToolBarHelper::divider();
					JToolBarHelper::cancel( 'item.cancel', JText::_('COM_PAGESANDITEMS_CANCEL') );
				}
				else
				{
					JToolBarHelper::save( 'item.item_save', JText::_('COM_PAGESANDITEMS_SAVE_ITEM') );
					JToolBarHelper::apply( 'item.item_apply', JText::_('COM_PAGESANDITEMS_APPLY') );
					//JToolBarHelper::divider();

					//ms: i have comment out the next lines
					//one problem is if user have change the title,text... all of this will not save
					//only the state is changed
					//all of this the user can handle in select 'state


					//JToolBarHelper::publish( 'item.item_publish');
					//JToolBarHelper::unpublish( 'item.item_unpublish');
					//JToolBarHelper::custom( 'item.item_archive','archive','archive','archive',false);


					//JToolBarHelper::archive( 'item.item_archive');//,'archive','archive','archive',false);

					//JToolBarHelper::trash( 'item.item_trash');//,'trash','','',false);
					//JToolBarHelper::divider();

					JToolBarHelper::custom( 'item.item_delete','delete','delete','delete',false);
					//JToolBarHelper::divider();
					JToolBarHelper::custom( 'item_move_select', 'move.png', 'move_f2.png', JText::_('COM_PAGESANDITEMS_MOVE'), false );
					//JToolBarHelper::custom( 'item_copy_select', 'copy.png', 'copy_f2.png', JText::_('COM_PAGESANDITEMS_COPY'), $listSelect = false);
					JToolBarHelper::divider();
					JToolBarHelper::cancel( 'item.cancel', JText::_('COM_PAGESANDITEMS_CANCEL') );
				}
				*/
			break;

			case 'item_move_select':
				JToolBarHelper::save( 'item.item_move_save', JText::_('COM_PAGESANDITEMS_SAVE') );
				JToolBarHelper::cancel( 'cancel', JText::_('COM_PAGESANDITEMS_CANCEL') );
			break;

			case 'page_move_select':
				JToolBarHelper::save( 'page.page_move_save', JText::_('COM_PAGESANDITEMS_SAVE') );
				JToolBarHelper::cancel( 'cancel', JText::_('COM_PAGESANDITEMS_CANCEL') );
			break;

			case 'config':

			break;

			case 'config_itemtype':
				JToolBarHelper::save( 'itemtype.config_itemtype_save', JText::_('COM_PAGESANDITEMS_SAVE') );
				JToolBarHelper::apply( 'itemtype.config_itemtype_apply', JText::_('COM_PAGESANDITEMS_APPLY') );
				JToolBarHelper::cancel( 'itemtype.cancel', JText::_('COM_PAGESANDITEMS_CANCEL') );
			break;

			case 'config_custom_itemtype':
				
			break;

			case 'config_custom_itemtype_field':
				
			break;

			case 'extension':
			case 'managers':

			break;
			/*
			case 'pluginX':
				//TODO rename to pluginItemtype?
				$item_type = JRequest::getVar('item_type', '');
				if(!$pathPluginsItemtypes)
				{
					$pathPluginsItemtypes = JPATH_PLUGINS.DS.'pages_and_items'.DS.'itemtypes';
				}
				if(file_exists($pathPluginsItemtypes.DS.$item_type.'/toolbar.php'))
				{
					require_once($pathPluginsItemtypes.DS.$item_type.'/toolbar.php');
				}
			break;
			*/
			case 'instance_select':
				JToolBarHelper::save( 'create_instance', JText::_('COM_PAGESANDITEMS_SAVE') );
				JToolBarHelper::cancel( 'cancel', JText::_('COM_PAGESANDITEMS_CANCEL') );
			break;
			case 'root':
				JToolBarHelper::apply( 'page.root_save', JText::_('COM_PAGESANDITEMS_SAVE') );
			break;
			/*
			case 'categorie':
			break;
			*/
			case 'category':
			break;

			default:
				$extensionType = JRequest::getVar('extensionType', '');
				if($extensionType != 'manager')
				{
					JToolBarHelper::apply( 'page.root_save', JText::_('COM_PAGESANDITEMS_SAVE').'X' );
				}
			break;
		}
	}

	function pi_strtolower($string){
		if(function_exists('mb_strtolower')){
			$string = mb_strtolower($string, 'UTF-8');
		}
		return $string;
	}

/**
from model page
BEGIN
*/
	function make_alias($alias)
	{
		$alias = str_replace("'",'',$alias);
		$alias = str_replace('"','',$alias);
		$alias = JFilterOutput::stringURLSafe($alias);
		return $alias;
	}

	function make_alias_unique($alias, $tablename, $exclude_id){

		//get aliasses, except for the current alias-row
		$db = JFactory::getDBO();
		$where = '';
		if($exclude_id)
		{
			$where = "WHERE id<>$exclude_id ";
		}
		$db->setQuery("SELECT alias "
		."FROM #__$tablename "
		.$where
		);
		$rows = $db->loadObjectList();
		$aliasses = array();
		foreach($rows as $row){
			$aliasses[] = $row->alias;
		}

		if(in_array($alias, $aliasses)){
			$j = 2;
			while (in_array($alias."-".$j, $aliasses)){
				$j = $j + 1;
			}
			$alias = $alias."-".$j;
		}

		return $alias;
	}


	function getPageId()
	{
		static $getPageId;
		if(isset($getPageId)){
			return $getPageId;
		}
		if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6')){
			$root_id = 0;
		}else{
			$root_id = 1;
		}
			$getPageId = JRequest::getVar('pageId', $root_id);
		return $getPageId;
	}


	//moved a copy to helper
	//to do: find where else there is a call to this function and make it go to the helper
	function itemtype_select($menu_id)
	{
		$html = '';
		$html .= JText::_('COM_PAGESANDITEMS_ITEMTYPE').': ';
		//get itemtype aliasses in new array
		$itemtypes = array();
		foreach(PagesAndItemsHelper::getItemtypes() as $type) //$this->getItemtypes() as $type)
		{
			/*
			TODO add for custom
			*/
			$type_array = array($type, PagesAndItemsHelper::translate_item_type($type));
			array_push($itemtypes, $type_array);
		}

		//sort array on alias
		foreach ($itemtypes as $key => $row)
		{
			$order[$key]  = strtolower($row[1]);
		}
		array_multisort($order, SORT_ASC, $itemtypes);



		//$html .= '<select name="select_itemtype" id="select_itemtype">';
		$html .= '<select id="select_itemtype" name="select_itemtype"';
		if(!$menu_id)
		{
			/*
			$link = 'index.php?option=com_pagesanditems'; //.$option;
			//$link .= '&amp;task=item.doExecute';
			//$link .= '&amp;extension=menuitemtypeselect';
			//$link .= '&amp;extensionType=html';
			//$link .= '&amp;extensionFolder=page_childs'; ///menuitemtypeselect';
			$link .= '&amp;view=item';
			$link .= '&amp;sub_task=new';
			$link .= '&amp;tmpl=component';
			$link .= '&amp;pageType=content_article';
			$menutype = isset($this->pageMenuItem->menutype) ? $this->pageMenuItem->menutype : '';
			$link .= '&amp;menutype='.$menutype;
			$link .= '&amp;pageId='.$menu_id;
			//$link .= '&amp;select_itemtype=';
			$link .= '&amp;categoryId='.JRequest::getVar('categoryId',null);
			*/
			
			//$html .= 'name="select_itemtype" ';
			
			//$html .= 'onchange="document.getElementById(\'button_new_itemtype\').href.value = \''.$link.'\'+this.value\';" ';

		}
		$html .= '>';

		foreach($itemtypes as $type)
		{

			if($type[1])
			{
				//only show if itemtype is installed
				$html .= '<option value="'.$type[0].'"';
				if($type[0]=='text')
				{
					$html .= ' selected="selected"';
					/*
					if(!$menu_id)
					{
						$link .= '&amp;select_itemtype='.$type[0];
					}
					*/
				}
				$html .= '>'.$type[1];
				if($type[0]=='text')
				{
					$html .= ' ('.JText::_('COM_PAGESANDITEMS_DEFAULT').')';
				}
				$html .= '</option>';
			}

		}
		$html .= '</select>';
		$html .= '&nbsp;&nbsp;';

		$button = PagesAndItemsHelper::getButtonMaker();
		$button->imagePath = PagesAndItemsHelper::getDirIcons();
		$button->buttonType = 'input';
		$button->text = JText::_('COM_PAGESANDITEMS_NEW_ITEM');
		$button->alt = JText::_('COM_PAGESANDITEMS_MAKE_NEW_ITEM');


		/*
		if(!$menu_id)
		{
			//here we make an modal window
			// with
			$size_x = '1024';
			$size_y = '800';
			$size = 'size: { x: '.$size_x.' , y: '.$size_y.'}';
			$options = "handler: 'iframe', ".$size;
			$button->rel = $options;
			$button->href = $link;
			$button->modal = true;
			$button->id = 'button_new_itemtype';
			//$button->onclick = 'alert(\'new_item('.$menu_id.')\');';
		}
		else
		{
			$button->onclick = 'new_item('.$menu_id.');';
		}
		*/
		
		$button->onclick = 'new_item('.$menu_id.');';
		
		$button->imageName = 'base/icon-16-add.png';
		$html .= $button->makeButton();

		return $html;
	}

	function getItemtypes(){
		static $itemtypes;
		if(isset($itemtypes)){
			return $itemtypes;
		}
		$itemtypes = PagesAndItemsHelper::setItemtypes();
		
		return $itemtypes;
		/*
		if(!$this->itemtypes){
			$this->setItemtypes();
		}
		return $this->itemtypes;
		*/
	}

	function setItemtypes(){
		$config = PagesAndItemsHelper::getConfig();
		$database = JFactory::getDBO();
		$temp_itemtypes = explode(',',$config['itemtypes']);
		$temp = array();
		for($n = 0; $n < count($temp_itemtypes); $n++)
		{
			//array_push($this->_itemtypes,$temp_itemtypes[$n]);
			//make type 'content' and 'text' the same
			$type = $temp_itemtypes[$n];
			if($type=='content'){
				$type = 'text';
			}
			$temp[] = $type;
		}
		
		$query = 'SELECT element ';
		$query .='FROM #__pi_extensions ';
		$query .='WHERE type='.$database->Quote('itemtype').' ';
		$query .='AND enabled ='.$database->Quote('1');
		$database->setQuery( $query );
		$itemtypeRows = $database->loadResultArray();
		if(!in_array('custom', $itemtypeRows))
		{
			//the custom itemtype are disabled so we must remove all custom
			$temp = array();
		}
		$temp_test = array();
		if($itemtypeRows)
		{
			foreach($itemtypeRows as $itemtype)
			{
				if($itemtype != 'custom')
				array_push($temp, $itemtype);
			}
		}
		
		
		//make unique
		$temp = array_unique($temp);
		return $temp;
		//$this->itemtypes = $temp;
	}


	function getMenutypes()
	{
		static $menutypes;
		if(isset($menutypes)){
			return $menutypes;
		}
		$menutypes = PagesAndItemsHelper::setMenutypes();
		return $menutypes;
	}

	function setMenutypes() //$id = null, $edit = null)
	{
		//check to see which menutypes we need
		$menutypes = array();
		$config = PagesAndItemsHelper::getConfig();
		
		$db =& JFactory::getDBO();
		/*
		$db->setQuery("SELECT title, menutype FROM #__menu_types WHERE menutype = '' ORDER BY title ASC"  );
		$rows_menu_types = $db->loadAssocList('menutype');
		*/
		if($config['menus'] != '')
		{
			$temp_menus = explode(',',$config['menus']);
			for($n = 0; $n < count($temp_menus); $n++)
			{
				$temp_menutype = explode(';',$temp_menus[$n]);
				/*
				old
				array_push($menutypes,$temp_menutype[0]);
				
				//todo also title and id from db???
				change for menu_types id?
				so title and menutype is change in #__menu_types
				*/
				/*
				if(count($temp_menutype) == 3)
				{
					//we have an id
					$db->setQuery("SELECT title, menutype FROM #__menu_types WHERE id = '$temp_menutype[2]' ORDER BY title ASC"  );
				}
				else
				{
				*/
					$db->setQuery("SELECT title, menutype FROM #__menu_types WHERE menutype = '$temp_menutype[0]' ORDER BY title ASC"  );
				//}
				$menutype = $db->loadObject(); //menutype');
				if(isset($menutype->menutype))
				{
					array_push($menutypes,$menutype->menutype);
				}
			}
		}
		return $menutypes;
	}

	function getMenutypeTitle($menutype)
	{
		$menutype_title = '';
		//we want get the title from #__menu_types not from the config
		$db =& JFactory::getDBO();
		$db->setQuery("SELECT title, menutype FROM #__menu_types WHERE menutype = '$menutype' ORDER BY title ASC"  );
		$row = $db->loadObject();
		if($row)
		{
			$menutype_title = $row->title;
		}
		/*
		$config = PagesAndItemsHelper::getConfig();
		$temp_menus = explode(',',$onfig['menus']);
		for($n = 0; $n < count($temp_menus); $n++)
		{
			$menutype_temp = explode(';',$temp_menus[$n]);
			if($menutype_temp[0]==$menutype){
				$menutype_title = $menutype_temp[1];
				break;
			}
		}
		*/
		return $menutype_title;
		//return strtolower($menutype_title);
	}

	//move to helper
	function getCurrentMenutype()
	{
		static $currentMenutype;
		if(isset($currentMenutype)){
			return $currentMenutype;
		}
		$currentMenutype = PagesAndItemsHelper::setCurrentMenutype();
		return $currentMenutype;
	}

	//move to helper
	function setCurrentMenutype()
	{
		$config = PagesAndItemsHelper::getConfig();
		$temp_menus = explode(',',$config['menus']);
		//get the current pages menutype
		if(count($temp_menus) !=0 && $temp_menus[0] != '')
		{
			//if(!JRequest::getVar('view') || (JRequest::getVar('view') == 'page' && JRequest::getVar('layout') == 'root' || !JRequest::getVar('menutype',0)) )
			if(!JRequest::getVar('view') || (JRequest::getVar('view') == 'page' && JRequest::getVar('layout') == 'root'))
			// || !JRequest::getVar('menutype',0)) ) 
			{
				$menu_in_url = JRequest::getVar('menutype');
				if(!$menu_in_url)
				{
					$menutypes = PagesAndItemsHelper::getMenutypes();
					$menu_in_url = $menutypes[0];
				}
				return $menu_in_url;
			}
			else
			{
				$menuitem = PagesAndItemsHelper::getMenuitem();
				if($menuitem && count($menuitem))
				{
					return $menuitem->menutype;
				}
				$menuitem = null;
				foreach(PagesAndItemsHelper::getMenuitems() as $menuitem)
				{
					if($menuitem->id == JRequest::getVar('pageId'))
					{
						return $menuitem->menutype;
						break;
					}
				}
			}
		}
	}

	function getMenuitem($pageId = null,$state = "(published='0' OR published='1')")
	{
		$db = & JFactory::getDBO();
		$where = array();
		$where[] = $state;
		$where[] = "id='".($pageId ? $pageId :JRequest::getVar('pageId'))."'";
		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		if($joomlaVersion < '1.6')
		{
			$db->setQuery("SELECT * FROM #__menu $where ORDER BY menutype ASC, sublevel ASC, ordering ASC"  );
		}
		else
		{
			$db->setQuery("SELECT *, title as name, parent_id as parent FROM #__menu $where ORDER BY lft ASC "  );
		}
		return $db->loadObject();
	}

	

	function getMenutypeMenuitems($menutype,$state = "(published='0' OR published='1')",$return = 'object')
	{
		$menutypes = "AND (menutype='".$menutype."')";
		$where = array();
		$where[] = $state;
		$where[] = "(menutype='".$menutype."')";
		
		//$app = JFactory::getApplication();
		//$input = $app->input;
		//$language = $input->get('filter_language', -1);
		$language = PagesAndItemsHelper::getLanguageFilter();
		if($language != '-1')
		{
			$where[] = "language='".$language."'";
		
		}
		
		
		
		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		$db = JFactory::getDBO();
		if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
		{
			$db->setQuery("SELECT * FROM #__menu $where ORDER BY menutype ASC, sublevel ASC, ordering ASC"  );
		}
		else
		{
			$db->setQuery("SELECT *, title as name, parent_id as parent FROM #__menu  $where ORDER BY menutype ASC, level ASC, lft ASC"  );
		}
		if($return == 'object')
		{
			return $db->loadObjectList();
		}
		else
		{
			return $db->loadAssocList('id');
		}
	}

	function getMenuitems($state = "(published='0' OR published='1')")
	{
		static $menuitems;
		if(isset($menuitems)){
			return $menuitems;
		}
		$menuitems = PagesAndItemsHelper::setMenuitems();
		return $menuitems;
	}

	function setMenuitems($state = "(published='0' OR published='1')")
	{
		$db =& JFactory::getDBO();
		$where = array();
		$where[] = $state;
		//Where is use view pages and _currentMenutype and...?
		//get menuitems (to be recycled in different functions)
		$temp_menus = PagesAndItemsHelper::getMenutypes();
		$menutypes = '';
		if(count($temp_menus))
		{
			$menutypes = "AND (";
			$where_menutypes = "(";
			for($n = 0; $n < count($temp_menus); $n++)
			{
				if($n!=0)
				{
					$menutypes .= " OR ";
					$where_menutypes .= " OR ";
				}
				//$menutype = explode(';',$temp_menus[$n]); //??????
				//$menutypes .= "menutype='".$menutype[0]."'";
				//$where_menutypes .= "menutype='".$menutype[0]."'";
				
				/*
				$menutypes .= "menutype='".$temp_menus[$n]['menutype']."'";
				$where_menutypes .= "menutype='".$temp_menus[$n]['menutype']."'";
				
				*/
				$menutypes .= "menutype='".$temp_menus[$n]."'";
				$where_menutypes .= "menutype='".$temp_menus[$n]."'";
			}
			$menutypes .= ")";
			$where_menutypes .= ")";
			$where[] = $where_menutypes;
		}


		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		if($joomlaVersion < '1.6')
		{
			$db->setQuery("SELECT * FROM #__menu $where ORDER BY menutype ASC, sublevel ASC, ordering ASC"  );
		}
		else
		{
			$db->setQuery("SELECT *, title as name, parent_id as parent FROM #__menu $where ORDER BY lft ASC "  );
		}
		return $db->loadObjectList();
	}

	function getCurrentPageId()
	{
		static $currentPageId;
		if(isset($currentPageId)){
			return $currentPageId;
		}
		$currentPageId = PagesAndItemsHelper::setCurrentPageId();
		return $currentPageId;
		/*
		if(!$this->_currentPageId)
		{
			$this->setCurrentPageId();
		}
		return $this->_currentPageId;
		*/
	}

	function setCurrentPageId()
	{
		$db =& JFactory::getDBO();
		$menutype = PagesAndItemsHelper::getCurrentMenutype();

		$db->setQuery("SELECT * FROM #__menu WHERE (published='0' OR published='1') AND menutype='$menutype' ORDER BY ordering ASC LIMIT 1" );
		$menuitem = $db->loadObject();
		if($menuitem)
		{
			return $menuitem->id;
		}
	}

	function getMenuItemsTypes()
	{
		static $menuItemsTypes;
		if(isset($menuItemsTypes)){
			return $menuItemsTypes;
		}
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models'.DS.'item.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'menutypes.php');
		$modelMenutypes = new PagesAndItemsModelMenutypes();
		$menuItemsTypes = $modelMenutypes->getTypeListComponents();
		return $menuItemsTypes;
		
	}



/**
from model page
END
*/


/*

new for tree
*/
	
	function getTree()
	{
		//PagesAndItemsTree
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'tree'.DS.'tree.php');
		$tree = new PagesAndItemsTree();
		return $tree;
	}

	function translate_item_type($item_type){
		/*
		all itemtypes are extensions so we can add language to each itemtype
		if we want so all itemtype JText can remove from the base language file
		and only the custom_* need extra routine here

		*/
		$database = JFactory::getDBO();
		if($item_type=='text')
		{
			$plugin_name = 'Joomla '.JText::_('COM_PAGESANDITEMS_ITEMTYPE_JOOMLA_ARTICLE');
		}
		elseif($item_type=='html')
		{
			$plugin_name = 'HTML';
			//$plugin_name = 'html';
		}
		elseif($item_type=='Xcontent')
		{
			//but here for test content renamed to Xcontent
			$plugin_name = 'content'; //ADD to see if not an pi item
		}
		elseif($item_type=='other_item')
		{
			$plugin_name = JText::_('COM_PAGESANDITEMS_ITEMTYPE_OTHER_ITEM');
		}
		elseif(strpos($item_type, 'ustom_'))
		{
			//custom itemtype
			$pos = strpos($item_type, 'ustom_');
			$type_id = substr($item_type, $pos+6, strlen($item_type));
			$database->setQuery("SELECT name FROM #__pi_customitemtypes WHERE id='$type_id' LIMIT 1");
			$rows = $database->loadObjectList();
			$row = $rows[0];
			//$plugin_name = $row->name;
			$plugin_name = $row->name.' ('.JText::_('COM_PAGESANDITEMS_CUSTOMITEMTYPE').')';
		}
		else
		{
			//
			/*

			$translated = JText::_('PI_EXTENSION_ITEMTYPE_'.strtoupper($item_type).'_NAME');
			if($translated <> 'PI_EXTENSION_ITEMTYPE_'.strtoupper($item_type).'_NAME')
			{

			}
			*/
			/*
			we will load the extension
			if we have $itemtypeHtml == '' the extension are not installed ore not published?
			*/

			//$itemtype = ExtensionHelper::importExtension('itemtype',null, $item_type,true,null,true);


			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'extensions'.DS.'itemtypehelper.php');
			$itemtype = ExtensionItemtypeHelper::importExtension(null, $item_type,true,null,true);
			$dispatcher = &JDispatcher::getInstance();
			//$itemtypeHtml = & new JObject();
			$itemtypeHtml = ''; //->text = '';

			$results = $dispatcher->trigger('onGetPluginName', array(&$itemtypeHtml,$item_type));
			//$dispatcher->trigger('onDetach',array($item_type));
			//if($itemtypeHtml->text != '')
			if($itemtypeHtml != '')
			{
				//$plugin_name = $itemtypeHtml->text;
				$plugin_name = $itemtypeHtml;
			}
			else
			{
				$plugin_name = false;
				$plugin_name = $item_type;

			}
			//
			//echo ' itemtype: '.$item_type.' itemtypeHtml: '.$itemtypeHtml.' plugin_name: '.$plugin_name.'  </ br>';
		}
		return $plugin_name;
	}



	//move to PagesAndItemsHelper
	function checkItemTypeInstall($item_type)
	{
		//here we call the database #__extensions
		//JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
		//$row = JTable::getInstance('piextension','PagesAndItemsTable');
		$database = JFactory::getDBO();
		if(strpos($item_type, 'ustom_'))
		{
			$item_type = 'custom';
		}
		
		$query = 'SELECT * ';
		$query .='FROM #__pi_extensions ';
		$query .='WHERE type='.$database->Quote('itemtype').' ';
		$query .='AND element='.$database->Quote($item_type);
		$database->setQuery( $query );
		$row = $database->loadObject( );
		return $row;
	}


	function trashPage($trashPageId){

		//check if menuitem is content-category-blog, and if so, get cat_id
		$content_category_blog = false;
		$db = JFactory::getDBO();
		$db->setQuery("SELECT link, type FROM #__menu WHERE id='$trashPageId' LIMIT 1");
		$rows = $db->loadObjectList();
		$row = $rows[0];
		if((strstr($row->link, 'index.php?option=com_content&view=category&layout=blog') && $row->type!='url'))
		{
			$content_category_blog = true;
			$cat_id = str_replace('index.php?option=com_content&view=category&layout=blog&id=','',$row->link);
		}

		//trash mainmenuitem
		$db->setQuery( "UPDATE #__menu SET published='-2' WHERE id='$trashPageId'");
		$db->query();
		$config = PagesAndItemsHelper::getConfig();
		//only trash items on page when its a content_blog_category
		if($content_category_blog && $config['page_trash_items']){
			//trash all items on the page (category)
			PagesAndItemsHelper::trashItemsCategory($cat_id);
		}

		//trash category
		if($content_category_blog && $config['page_trash_cat']){
			$db->setQuery( "UPDATE #__categories SET published='-2' WHERE id='$cat_id'");
			$db->query();
		}

		//trash all underlying child-pages
		//$this->trashPageChildren($trashPageId);
		PagesAndItemsHelper::trashPageChildren($trashPageId);


	}

	function deletePage($deletePageId){

		//check if menuitem is content-category-blog, and if so, get cat_id
		$content_category_blog = false;
		$config = PagesAndItemsHelper::getConfig();
		$db =& JFactory::getDBO();
		$db->setQuery("SELECT link, type FROM #__menu WHERE id='$deletePageId' LIMIT 1");
		$rows = $db->loadObjectList();
		$row = $rows[0];
		if((strstr($row->link, 'index.php?option=com_content&view=category&layout=blog') && $row->type!='url'))
		{
			$content_category_blog = true;
			$cat_id = str_replace('index.php?option=com_content&view=category&layout=blog&id=','',$row->link);
		}

		//only delete items on page when its a content_blog_category
		if($content_category_blog && $config['page_delete_items']){
			//trash all items on the page (category)
			PagesAndItemsHelper::deleteItemsCategory($cat_id);
		}

		//delete category
		if($content_category_blog && $config['page_delete_cat']){
			$db->setQuery( "DELETE FROM #__categories WHERE id='$cat_id'");
			$db->query();
		}

		//delete all underlying child-pages
		PagesAndItemsHelper::deletePageChildren($deletePageId);

		//delete mainmenuitem
		$db->setQuery( "DELETE FROM #__menu WHERE id='$deletePageId'");
		$db->query();

	}

	function deleteItemsCategory($cat_id){
		$db =& JFactory::getDBO();
        //get content id's which are on frontpage
        $db->setQuery("SELECT content_id FROM #__content_frontpage");
        $frontpage_items = $db->loadResultArray();

        //get content-index to know which item has which itemtype
        $db->setQuery("SELECT item_id, itemtype FROM #__pi_item_index");
        $index_items = $db->loadObjectList();

        //trash all items in the category
        $db->setQuery("SELECT id FROM #__content WHERE catid='$cat_id'" );
        $rows = $db->loadObjectList();

        //ms: here we will load all itemtypes
        require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'extensions'.DS.'itemtypehelper.php');
        ExtensionItemtypeHelper::importExtension(null, null,true,null,true);
        $dispatcher = &JDispatcher::getInstance();

        foreach($rows as $row){

            $item_id = $row->id;
            $db->setQuery( "DELETE FROM #__content WHERE id='$item_id'");
            $db->query();

            //if item was on frontpage, take it off
            if(in_array($item_id, $frontpage_items)){
                PagesAndItemsHelper::take_item_off_frontpage($item_id);
            }


            //if item was plugin, delete sub-item rows etc.
            //ms: if item itemtype not 'text'|'html|'other_item'
            foreach($index_items as $index_item){
                if($item_id==$index_item->item_id && $index_item->itemtype!='text' && $index_item->itemtype!='html' && $index_item->itemtype!='other_item')
                {
                    //ms: if the itemtype have a function item_delete the dispatcher call
                    $dispatcher->trigger('item_delete',array($item_id));
                    //old: $this->delete_plugin_items($item_id, $index_item->itemtype);
                }
            }


            //if item had duplicate-items trash those as well
            $db->setQuery("SELECT item_id FROM #__pi_item_other_index WHERE other_item_id='$item_id' ");
            $other_items = $db->loadObjectList();
            foreach($other_items as $other_item){
                //ms: update_duplicate_item is in models/page.php but i think it must move to extensions/itemtypes/other_item.php
                //old $this->update_duplicate_item($other_item->item_id, $item_id);
                //ms: i am confused see the extensions/itemtypes/other_item.php function update_duplicate_item($item_id, $other_item_id)

                $dispatcher->trigger('update_duplicate_item',array($other_item->item_id, $item_id));
                //ms: i think we must write: $dispatcher->trigger('update_duplicate_item',array($item_id,$other_item->item_id));
                $other_item_id = $other_item->item_id;

                $db->setQuery( "DELETE FROM #__content WHERE id='$other_item_id' ");
                $db->query();
            }

            //if item was of itemtype other-item disconnect it from original item by deleting the row in the ohter-item-index
            foreach($index_items as $index_item){
                if($index_item->itemtype=='other_item'){
                    //ms: delete_other_item_entry is in models/page.php but i think it must move to extensions/itemtypes/other_item.php
                    //old: $this->delete_other_item_entry($item_id);
                    //ms:
                    $dispatcher->trigger('delete_other_item_entry',array($item_id));
                }
            }
        }
    }

	function trashPageChildren($trashPageId){
		$db =& JFactory::getDBO();
		$db->setQuery("SELECT id FROM #__menu WHERE parent_id='$trashPageId'" );

		$rows = $db->loadObjectList();
		foreach($rows as $row){
			PagesAndItemsHelper::trashPage($row->id);
		}
	}

	function deletePageChildren($deletePageId){
		$db =& JFactory::getDBO();
		$db->setQuery("SELECT id FROM #__menu WHERE parent_id='$deletePageId'" );

		$rows = $db->loadObjectList();
		foreach($rows as $row){
			PagesAndItemsHelper::deletePage($row->id);
		}
	}

	function trashItemsCategory($trashCatId){
		$db =& JFactory::getDBO();
		//trash all items in the category
		$db->setQuery("SELECT id FROM #__content WHERE catid='$trashCatId'" );
		$rows = $db->loadObjectList();

		foreach($rows as $row){

			$item_id = $row->id;

			PagesAndItemsHelper::item_state($item_id, '-2');
		}

		//clean item-index
		//only delete CCK rows when the item is really being deleted
		//$this->keep_item_index_clean();
	}

	//ms: add
	function page_state($page_id, $new_state){

		switch($new_state)
		{
			case 'delete':
				return PagesAndItemsHelper::deletePage($page_id);
			break;
			case '-2':
				return PagesAndItemsHelper::trashPage($page_id);
			break;

			case '1':
			//publish
				return PagesAndItemsHelper::publishPage(array($page_id),1);
			break;

			case '0':
				return PagesAndItemsHelper::publishPage(array($page_id),0);
			//unpublish
			break;
		}
	}

	//ms: add
	function publishPage($pks, $value = 1)
	{
		// Initialise variables.
		//$table = $row = & JTable::getInstance('menu');
		$table = JTable::getInstance('menu');
		$pks = (array) $pks;
		// Default menu item existence checks.
		if ($value != 1) {
			foreach ($pks as $i => $pk)
			{
				if ($table->load($pk) && $table->home && $table->language == '*') {
					// Prune items that you can't change.
					JError::raiseWarning(403, JText::_('JLIB_DATABASE_ERROR_MENU_UNPUBLISH_DEFAULT_HOME'));
					unset($pks[$i]);
					break;
				}
			}
		}
		$user = JFactory::getUser();

		//ms: fix for publish only parent
		if ($value == 1 ) {
			$tree = $table->getTree($pks[0],true);
			foreach($tree as $pk)
			{
				$pks[] = $pk->id;
			}
		}

		// Attempt to change the state of the records.
		if (!$table->publish($pks, $value, $user->get('id'))) {
			//$this->setError($table->getError());
			return false;
		}
		return true;
	}



	function item_state($item_id, $new_state){

		$database = JFactory::getDBO();

		//get category_id
		$category_id = 0;
		$database->setQuery("SELECT catid "
		." FROM #__content "
		." WHERE id='$item_id' "
		." LIMIT 1 "
		);
		$rows = $database->loadObjectList();
		foreach($rows as $row){
			$category_id = $row->catid;
		}

		//get Joomla ACL for this article
		//include com_content helper
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'content.php');
		$ContentHelper = new ContentHelper;
		$canDo = ContentHelper::getActions($category_id, $item_id);

		//get content-index to know which item has which itemtype
		$database->setQuery("SELECT id, itemtype FROM #__pi_item_index WHERE item_id='$item_id' ");
		$index_items = $database->loadObjectList();
		$itemtype = 'text';
		$index_id = '';
		foreach($index_items as $index_item){
			$itemtype = $index_item->itemtype;
			$index_id = $index_item->id;
		}

		//trigger something
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'extensions'.DS.'itemtypehelper.php');
		//ExtensionItemtypeHelper::importExtension(null, 'other_item',true,null,true);
		//ExtensionItemtypeHelper::importExtension(null, null,true,null,true);
		$type_id = null;
		if(strpos($itemtype, 'ustom_'))
		{
			$pos = strpos($itemtype, 'ustom_');
			$type_id = substr($itemtype, $pos+6, strlen($itemtype));
			$itemtype = 'custom';
		}
		ExtensionItemtypeHelper::importExtension(null, $itemtype,true,null,true);


		$dispatcher = &JDispatcher::getInstance();

		if($new_state != 'delete'){
			//set any other state then deleting
			//check for canDo edit.state
			$database->setQuery( "UPDATE #__content SET state='$new_state' WHERE id='$item_id' ");
			$database->query();
		}

		/*
		//if item was plugin, delete sub-item rows etc.
		foreach($index_items as $index_item){
			if($item_id==$index_item->item_id && $index_item->itemtype!='text' && $index_item->itemtype!='html' && $index_item->itemtype!='other_item'){
				$this->delete_plugin_items($item_id, $index_item->itemtype);
			}
		}
		*/

		//if item had duplicate-items trash those as well
		$database->setQuery("SELECT item_id FROM #__pi_item_other_index WHERE other_item_id='$item_id' ");
		$other_items = $database->loadObjectList();
		foreach($other_items as $other_item){
			//update_duplicate_item is in models/page.php but i think it must move to extensions/itemtypes/other_item.php
			//$this->update_duplicate_item($other_item->item_id, $item_id);
			//TODO
			//here we must load the itemtype other_item
			ExtensionItemtypeHelper::importExtension(null, 'other_item',true,null,true);
			if($new_state!='delete'){
				$dispatcher->trigger('update_duplicate_item',array($other_item->item_id, $item_id));
			}else
			{
				require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'extensions'.DS.'managerhelper.php');
				ExtensionManagerHelper::importExtension(null,null, true,null,true);
				$dispatcher->trigger('onManagerItemtypeItemDelete', array ('other_item',$other_item->item_id));
			}
		}

		//if item was of itemtype other-item disconnect it from original item by deleting the row in the ohter-item-index
		if($itemtype=='other_item' && $new_state=='delete' && $canDo->get('core.delete')){
			//delete_other_item_entry is in models/page.php but i think it must move to extensions/itemtypes/other_item.php
			//$this->delete_other_item_entry($item_id);
			//TODO
			$dispatcher->trigger('delete_other_item_entry',array($item_id));
		}

		//if delete
		if($new_state=='delete' && $canDo->get('core.delete')){
			PagesAndItemsHelper::take_item_off_frontpage($item_id);

			/*
			ms: replaced with $dispatcher->trigger('onItemtypeItemSave
			if($itemtype!='text' && $itemtype!='html' && $itemtype!='other_item')
			{
				require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_pagesanditems'.DS.'models'.DS.'page.php');
				PagesAndItemsModelPage::delete_plugin_items($item_id, $itemtype);
			}
			*/
			if($itemtype == 'custom')
			{
				$itemtype = 'custom_'.$type_id;
			}
			$dispatcher->trigger('onItemtypeItemSave',array($itemtype, 1, $item_id,null));


			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'extensions'.DS.'managerhelper.php');
			ExtensionManagerHelper::importExtension(null,null, true,null,true);

			$dispatcher->trigger('onManagerItemtypeItemDelete', array ($itemtype,$item_id));

			/*
			TODO search all pages with link ? 'option=com_content&view=article&id='$item_id
			$query = "select * from #__menus where link LIKE '%option=com_content&view=article&id=".$item_id."%' ORDER BY id ";
			$database->setQuery( $query );
			$database->query();
			$rows = $database->loadObjectList();
			if(count($rows))
			{
				foreach($rows as $row)
				{
					$query = "UPDATE from #__menus where id=".$row->id." set published = 0 ";
				}
			}
			*/
			
			
			//delete the actual item
			$database->setQuery("DELETE FROM #__content WHERE id='$item_id' ");
			$database->query();
		}
		else
		{
			if($itemtype == 'custom')
			{
				$itemtype = 'custom_'.$type_id;
			}
			$dispatcher->trigger('onItemtypeItemNewState',array($itemtype, $new_state, $item_id));
		}

	}



	function take_item_off_frontpage($item_id){
		$db = JFactory::getDBO();
		$db->setQuery("DELETE FROM #__content_frontpage WHERE content_id='$item_id' ");
		$db->query();

		$db->setQuery( "UPDATE #__content SET featured='0' WHERE id='$item_id' ");
		$db->query();

	}

	function keep_item_index_clean(){
		$db = JFactory::getDBO();
		//get content id's
		$db->setQuery( "SELECT id, state "
		. "FROM #__content "
		);
		$items = $db->loadObjectList();

		//make nice arrays
		$content_ids = array();
		$content_ids_tashed = array();
		foreach($items as $item){
			$content_ids[] = $item->id;
			if($item->state==-2){
				$content_ids_tashed[] = $item->id;
			}
		}

		//get item index data
		$db->setQuery( "SELECT id, item_id, itemtype "
		. "FROM #__pi_item_index "
		);
		$index_items = $db->loadObjectList();

		$from_cit_to_text = array();

		//loop through item index data.
		//delete rows which item in #__content has been deleted and
		foreach($index_items as $index_item)
		{
			$index_id = $index_item->id;
			$index_item_id = $index_item->item_id;

			$delete_index_row = 0;

			//customitemtypes which have been trashed, so delete it from index (makes it a normal item)
			$itemtype = $index_item->itemtype;

			if(strpos($itemtype, 'ustom_')){
				//custom itemtype
				if(in_array($index_item_id, $content_ids_tashed)){
					//trashed
					$delete_index_row = 1;
					//to make it a normal item, take out the custom-itemtype-codes
					$from_cit_to_text[] = $index_item_id;
				}
			}

			//if item is no longer in content table, take it out of index.
			if(!in_array($index_item_id, $content_ids)){
				$delete_index_row = 1;

			}

			//delete the index row if needed
			if($delete_index_row){
				$db->setQuery("DELETE FROM #__pi_item_index WHERE id='$index_id'");
				$db->query();
			}
		}

		/*
		//clean items which where customitemtypes, but have now become normal text-types, from custom itemtype codes
		foreach($from_cit_to_text as $itemid){
			//get item texts
			$db->setQuery( "SELECT introtext, fulltext "
			."FROM #__content "
			."WHERE id='$itemid' "
			);
			$items = $this->db->loadObjectList();

			//take the codes out
			foreach($items as $item){
				echo $item->introtext;
				exit;
				//$introtext = $this->take_cit_codes_out($item->introtext);
				//$fulltext = $this->take_cit_codes_out($item->fulltext);
			}

			//update item
			//$this->db->setQuery( "UPDATE #__content SET introtext='$introtext', fulltext='$fulltext' WHERE id='$itemid'");
			//$this->db->query();
		}
		*/
	}

	function reorderItemsCategory($catId){
		$db = JFactory::getDBO();
		$db->setQuery("SELECT id, ordering, catid FROM #__content WHERE catid='$catId' AND (state='0' OR state='1') ORDER BY ordering ASC" );
		$rows = $db->loadObjectList();
		$counter = 1;
		foreach($rows as $row){
			//reorder to make sure all is well
			$rowId = $row->id;
			$db->setQuery( "UPDATE #__content SET ordering='$counter' WHERE id='$rowId'");
			$db->query();
			$counter = $counter + 1;
		}
		return $counter;
	}

	function get_menutype($menu_id){
		$database = JFactory::getDBO();
		$database->setQuery("SELECT menutype "
		." FROM #__menu "
		." WHERE id='$menu_id' "
		." LIMIT 1 "
		);
		$rows = $database->loadObjectList();
		$menutype = 0;
		foreach($rows as $row){
			$menutype = $row->menutype;
		}
		return $menutype;
	}

	function check_display($thing){
		$return = 0;
		
		//if(isset($this->config[$thing])){

			//if($this->config[$thing]){
			if(PagesAndItemsHelper::getConfigAsRegistry()->get($thing)){
				$return = 1;
			}
		//}
		//if($this->getIsSuperAdmin() && !$this->config['item_props_hideforsuperadmin']){
		if(PagesAndItemsHelper::getIsSuperAdmin() && !PagesAndItemsHelper::getConfigAsRegistry()->get('item_props_hideforsuperadmin')){
			$return = 1;
		}
		return $return;
	}

	function get_all_page_fields(){
		$fields = array();

		//array(name_of_right, element id, language_label, menu-type, field or panel)

		//fields under details
		$fields[] = array('page_props_id','jform_id','JGRID_HEADING_ID','all','field');
		$fields[] = array('page_props_type','jform_type-lbl','COM_MENUS_ITEM_FIELD_TYPE_LABEL','all','field');
		$fields[] = array('page_props_title','jform_title','JGLOBAL_TITLE','all','field');
		$fields[] = array('page_props_alias','jform_alias','COM_PAGESANDITEMS_ALIAS','all','field');
		$fields[] = array('page_props_note','jform_note','JFIELD_NOTE_LABEL','all','field');
		$fields[] = array('page_props_link','jform_link','COM_MENUS_ITEM_FIELD_LINK_LABEL','all','field');
		$fields[] = array('page_props_published','jform_published','JSTATUS','all','field');
		$fields[] = array('page_props_access','jform_access','JGRID_HEADING_ACCESS','all','field');
		$fields[] = array('page_props_menutype','jform_menutype','COM_MENUS_MENU_MENUTYPE_LABEL','all','field');
		$fields[] = array('page_props_parent_id','jform_parent_id','COM_MENUS_ITEM_FIELD_PARENT_LABEL','all','field');
		$fields[] = array('page_props_browserNav','jform_browserNav','COM_MENUS_ITEM_FIELD_BROWSERNAV_LABEL','all','field');
		$fields[] = array('page_props_home','jform_home','COM_MENUS_ITEM_FIELD_HOME_LABEL','all','field');
		$fields[] = array('page_props_language','jform_language','JFIELD_LANGUAGE_LABEL','all','field');
		$fields[] = array('page_props_template_style_id','jform_template_style_id','COM_MENUS_ITEM_FIELD_TEMPLATE_LABEL','all','field');

		//panel Link Type Options
		$fields[] = array('page_props_linktype_options','menu-options-options','COM_MENUS_LINKTYPE_OPTIONS_LABEL','all','panel');
		$fields[] = array('page_props_link_title_attri','jform_params_menu_anchor_title','COM_MENUS_ITEM_FIELD_ANCHOR_TITLE_LABEL','all','field');
		$fields[] = array('page_props_link_css','jform_params_menu_anchor_css','COM_MENUS_ITEM_FIELD_ANCHOR_CSS_LABEL','all','field');
		$fields[] = array('page_props_link_image','jform_params_menu_image-lbl','COM_MENUS_ITEM_FIELD_MENU_IMAGE_LABEL','all','field');
		$fields[] = array('page_props_add_title','jform_params_menu_text-lbl','COM_MENUS_ITEM_FIELD_MENU_TEXT_LABEL','all','field');

		//panel page display Options
		$fields[] = array('page_props_page_display_options','page-options-options','COM_MENUS_PAGE_OPTIONS_LABEL','all','panel');
		$fields[] = array('page_props_browser_page','jform_params_page_title','COM_MENUS_ITEM_FIELD_PAGE_TITLE_LABEL','all','field');
		$fields[] = array('page_props_show_page_heading','jform_params_show_page_heading','COM_MENUS_ITEM_FIELD_SHOW_PAGE_HEADING_LABEL','all','field');
		$fields[] = array('page_props_page_heading','jform_params_page_heading','COM_MENUS_ITEM_FIELD_SHOW_PAGE_HEADING_LABEL','all','field');
		$fields[] = array('page_props_page_class','jform_params_pageclass_sfx','COM_MENUS_ITEM_FIELD_PAGE_CLASS_LABEL','all','field');

		//panel page display Options
		$fields[] = array('page_props_metadata_options','metadata-options','JGLOBAL_FIELDSET_METADATA_OPTIONS','all','panel');
		$fields[] = array('page_props_meta_desc','jform_params_menu_meta_description','JFIELD_META_DESCRIPTION_LABEL','all','field');
		$fields[] = array('page_props_meta_keys','jform_params_menu_meta_keywords','JFIELD_META_KEYWORDS_LABEL','all','field');
		$fields[] = array('page_props_robots','jform_params_robots','JFIELD_METADATA_ROBOTS_LABEL','all','field');
		$fields[] = array('page_props_secure','jform_params_secure','COM_MENUS_ITEM_FIELD_SECURE_LABEL','all','field');

		//panel module assignment
		$fields[] = array('page_props_modules','module-options','COM_MENUS_ITEM_MODULE_ASSIGNMENT','all','panel');

		//label menutype category blog
		$fields[] = array('','','COM_CONTENT_CATEGORY_VIEW_BLOG_TITLE','content_category_blog','menutype');

		//panel required_settings
		$fields[] = array('page_props_required_settings','request-options','COM_MENUS_REQUEST_FIELDSET_LABEL','content_category_blog','panel');

		//panel category options
		$fields[] = array('page_props_category_options','basic-options','JGLOBAL_CATEGORY_OPTIONS','content_category_blog','panel');
		$fields[] = array('page_props_cat_title','jform_params_show_category_title','JGLOBAL_LIST_TITLE_LABEL','content_category_blog','field');
		$fields[] = array('page_props_cat_desc','jform_params_show_description','JGLOBAL_SHOW_CATEGORY_DESCRIPTION_LABEL','content_category_blog','field');
		$fields[] = array('page_props_cat_img','jform_params_show_description_image','JGLOBAL_SHOW_CATEGORY_IMAGE_LABEL','content_category_blog','field');
		$fields[] = array('page_props_cat_levels','jform_params_maxLevel','JGLOBAL_MAXLEVEL_LABEL','content_category_blog','field');
		$fields[] = array('page_props_cat_empty','jform_params_show_empty_categories','JGLOBAL_EMPTY_CATEGORIES_LABEL','content_category_blog','field');
		$fields[] = array('page_props_cat_no_art_mess','jform_params_show_no_articles','COM_CONTENT_NO_ARTICLES_LABEL','content_category_blog','field');
		$fields[] = array('page_props_cat_subcat_desc','jform_params_show_subcat_desc','JGLOBAL_SHOW_SUBCATEGORIES_DESCRIPTION_LABEL','content_category_blog','field');
		$fields[] = array('page_props_cat_artincat','jform_params_show_cat_num_articles','COM_CONTENT_NUMBER_CATEGORY_ITEMS_LABEL','content_category_blog','field');
		$fields[] = array('page_props_cat_subheading','jform_params_page_subheading','JGLOBAL_SUBHEADING_LABEL','content_category_blog','field');

		//panel blog layout options
		$fields[] = array('page_props_blog_options','advanced-options','JGLOBAL_BLOG_LAYOUT_OPTIONS','content_category_blog','panel');
		$fields[] = array('page_props_blog_leading','jform_params_num_leading_articles','JGLOBAL_NUM_LEADING_ARTICLES_LABEL','content_category_blog','field');
		$fields[] = array('page_props_blog_intro','jform_params_num_intro_articles','JGLOBAL_NUM_INTRO_ARTICLES_LABEL','content_category_blog','field');
		$fields[] = array('page_props_blog_cols','jform_params_num_columns','JGLOBAL_NUM_COLUMNS_LABEL','content_category_blog','field');
		$fields[] = array('page_props_blog_links','jform_params_num_links','JGLOBAL_NUM_LINKS_LABEL','content_category_blog','field');
		$fields[] = array('page_props_blog_multicolorder','jform_params_multi_column_order','JGLOBAL_MULTI_COLUMN_ORDER_LABEL','content_category_blog','field');
		$fields[] = array('page_props_blog_incsubcat','jform_params_show_subcategory_content','JGLOBAL_SHOW_SUBCATEGORY_CONTENT_LABEL','content_category_blog','field');
		$fields[] = array('page_props_blog_catorder','jform_params_orderby_pri','JGLOBAL_CATEGORY_ORDER_LABEL','content_category_blog','field');
		$fields[] = array('page_props_blog_artorder','jform_params_orderby_sec','JGLOBAL_ARTICLE_ORDER_LABEL','content_category_blog','field');
		$fields[] = array('page_props_blog_dateorder','jform_params_order_date','JGLOBAL_ORDERING_DATE_LABEL','content_category_blog','field');
		$fields[] = array('page_props_blog_pagination','jform_params_show_pagination','JGLOBAL_PAGINATION_LABEL','content_category_blog','field');
		$fields[] = array('page_props_blog_results','jform_params_show_pagination_results','JGLOBAL_PAGINATION_RESULTS_LABEL','content_category_blog','field');

		//panel article options
		$fields[] = array('page_props_article_options','article-options','COM_CONTENT_ATTRIBS_FIELDSET_LABEL','content_category_blog','panel');
		$fields[] = array('page_props_art_title','jform_params_show_title','JGLOBAL_SHOW_TITLE_LABEL','content_category_blog','field');
		$fields[] = array('page_props_art_linkedtitles','jform_params_link_titles','JGLOBAL_LINKED_TITLES_LABEL','content_category_blog','field');
		$fields[] = array('page_props_art_introtext','jform_params_show_intro','JGLOBAL_SHOW_INTRO_LABEL','content_category_blog','field');
		$fields[] = array('page_props_art_cat','jform_params_show_category','JGLOBAL_SHOW_CATEGORY_LABEL','content_category_blog','field');
		$fields[] = array('page_props_art_catlink','jform_params_link_category','JGLOBAL_LINK_CATEGORY_LABEL','content_category_blog','field');
		$fields[] = array('page_props_art_parent','jform_params_show_parent_category','JGLOBAL_SHOW_PARENT_CATEGORY_LABEL','content_category_blog','field');
		$fields[] = array('page_props_art_parentlink','jform_params_link_parent_category','JGLOBAL_LINK_PARENT_CATEGORY_LABEL','content_category_blog','field');
		$fields[] = array('page_props_art_author','jform_params_show_author','JGLOBAL_SHOW_AUTHOR_LABEL','content_category_blog','field');
		$fields[] = array('page_props_art_authorlink','jform_params_link_author','JGLOBAL_LINK_AUTHOR_LABEL','content_category_blog','field');
		$fields[] = array('page_props_art_create','jform_params_show_create_date','JGLOBAL_SHOW_CREATE_DATE_LABEL','content_category_blog','field');
		$fields[] = array('page_props_art_modify','jform_params_show_modify_date','JGLOBAL_SHOW_MODIFY_DATE_LABEL','content_category_blog','field');
		$fields[] = array('page_props_art_pub','jform_params_show_publish_date','JGLOBAL_SHOW_PUBLISH_DATE_LABEL','content_category_blog','field');
		$fields[] = array('page_props_art_nav','jform_params_show_item_navigation','JGLOBAL_SHOW_NAVIGATION_LABEL','content_category_blog','field');
		$fields[] = array('page_props_art_vote','jform_params_show_vote','JGLOBAL_SHOW_VOTE_LABEL','content_category_blog','field');
		$fields[] = array('page_props_art_read','jform_params_show_readmore','JGLOBAL_SHOW_READMORE_LABEL','content_category_blog','field');
		$fields[] = array('page_props_art_readtitle','jform_params_show_readmore_title','JGLOBAL_SHOW_READMORE_TITLE_LABEL','content_category_blog','field');
		$fields[] = array('page_props_art_icons','jform_params_show_icons','JGLOBAL_SHOW_ICONS_LABEL','content_category_blog','field');
		$fields[] = array('page_props_art_print','jform_params_show_print_icon','JGLOBAL_SHOW_PRINT_ICON_LABEL','content_category_blog','field');
		$fields[] = array('page_props_art_email','jform_params_show_email_icon','JGLOBAL_SHOW_EMAIL_ICON_LABEL','content_category_blog','field');
		$fields[] = array('page_props_art_hits','jform_params_show_hits','JGLOBAL_SHOW_HITS_LABEL','content_category_blog','field');
		$fields[] = array('page_props_art_unauthorised','jform_params_show_noauth','JGLOBAL_SHOW_UNAUTH_LINKS_LABEL','content_category_blog','field');

		//panel article options
		$fields[] = array('page_props_integration_options','integration-options','COM_MENUS_INTEGRATION_FIELDSET_LABEL','content_category_blog','panel');
		$fields[] = array('page_props_int_feed','jform_params_show_feed_link','JGLOBAL_SHOW_FEED_LINK_LABEL','content_category_blog','field');
		$fields[] = array('page_props_int_each','jform_params_feed_summary','JGLOBAL_FEED_SUMMARY_LABEL','content_category_blog','field');

		return $fields;
	}

	function get_usergroups_from_user($user_id){
		$database = JFactory::getDBO();
		$database->setQuery("SELECT m.group_id "
		."FROM #__user_usergroup_map AS m "
		."WHERE m.user_id='$user_id' "
		);
		$rows = $database->loadObjectList();
		$group_ids = array();
		foreach($rows as $row){
			$group_ids[] = $row->group_id;
		}
		return $group_ids;
	}

	function get_usergroups(){
		$database = JFactory::getDBO();
		$database->setQuery("SELECT u.id AS id, u.title AS title, COUNT(DISTINCT u2.id) AS level "
		." FROM #__usergroups AS u "
		." LEFT OUTER JOIN #__usergroups AS u2 ON u.lft > u2.lft AND u.rgt < u2.rgt "
		." GROUP BY u.id "
		." ORDER BY u.lft ASC "
		);
		$usergroups = $database->loadObjectList()or die($database->getErrorMsg());
		return $usergroups;
	}

	/*
	function check_pi_acl($permission){

		$return_permission = false;

		$user =& JFactory::getUser();
		$user_id = $user->get('id');

		//get usergroups
		$groups = $this->get_usergroups_from_user($user_id);

		$permissions = $this->config['permissions'];

		$access_array = array();
		foreach($groups as $group){
			$check_permission = $group.'_'.$permission;
			$access_temp = 'yes';
			if(!in_array($check_permission, $permissions)){
				$access_temp = 'no';
			}
			$access_array[] = $access_temp;
		}

		//check with config if to give access or not
		if($this->config['multigroup_access_requirement']=='every_group'){
			if(in_array('no', $access_array)){
				$return_permission = false;
			}else{
				$return_permission = true;
			}
		}else{
			if(in_array('yes', $access_array)){
				$return_permission = true;
			}else{
				$return_permission = false;
			}
		}

		return $return_permission;
	}
	*/


	function canDoMenus($parent_id = 0)
	{
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'helpers'.DS.'menus.php');
		return MenusHelper::getActions($parent_id);
		
	}

	function canDoContent($categoryId = 0, $articleId = 0)
	{
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'content.php');
		return ContentHelper::getActions($categoryId , $articleId);
	}

	

	//ms: replace for PI ACL?
	function check_acl($permission)
	{
		if($permission == '1')
		{
			/*
			$page_id = JRequest::getVar('pageId', '');
			//get parent_id
			$parent_id = 0;
			$database = JFactory::getDBO();
			$database->setQuery("SELECT parent_id "
			." FROM #__menus "
			." WHERE id='$page_id' "
			." LIMIT 1 "
			);
			$rows = $database->loadObjectList();
			foreach($rows as $row){
				$parent_id = $row->parent_id;
			}
			$canDo = PagesAndItemsHelper::canDoMenus($parent_id);
			if(!$canDo->get('core.create') )
			{
				return false;
			}
			*/
			if(!JFactory::getUser()->authorise('core.create', 'com_menus') )
			{
				//$app->enqueueMessage(JText::_('COM_PAGESANDITEMS_NO_CREATE_PAGE'), 'warning');
				return false;
			}
		}
		elseif($permission == '2')
		{
			//we need parent_id?
			/*
			$page_id = JRequest::getVar('pageId', '');
			//get parent_id
			$parent_id = 0;
			$database = JFactory::getDBO();
			$database->setQuery("SELECT parent_id "
			." FROM #__menus "
			." WHERE id='$page_id' "
			." LIMIT 1 "
			);
			$rows = $database->loadObjectList();
			foreach($rows as $row){
				$parent_id = $row->parent_id;
			}
			$canDo = PagesAndItemsHelper::canDoMenus($parent_id);
			if(!$canDo->get('core.edit') )
			{
				return false;
			}
			*/
			
			if(!JFactory::getUser()->authorise('core.edit', 'com_menus') )
			{
				//$app->enqueueMessage(JText::_('COM_PAGESANDITEMS_NO_CREATE_PAGE'), 'warning');
				return false;
			}
		}
		elseif($permission == '3')
		{
			//get Joomla ACL for this article
			//include com_content helper
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'content.php');
			//$item_id = JRequest::getVar('itemId', JRequest::getVar('id', 0, 'post'));
			$item_id = JRequest::getVar('itemId', '');
			//get category_id
			$cat_id = 0;
			$database = JFactory::getDBO();
			$database->setQuery("SELECT catid "
			." FROM #__content "
			." WHERE id='$item_id' "
			." LIMIT 1 "
			);
			$rows = $database->loadObjectList();
			foreach($rows as $row){
				$cat_id = $row->catid;
			}

			$canDo = ContentHelper::getActions($cat_id, $item_id);
			if(!$canDo->get('core.create')){
				return false;
			}
		}
		elseif($permission == '4')
		{
			//get Joomla ACL for this article
			//include com_content helper
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'content.php');
			//$item_id = JRequest::getVar('itemId', JRequest::getVar('id', 0, 'post'));
			$item_id = JRequest::getVar('itemId', '');
			//get category_id
			$cat_id = 0;
			$created_by = 0;
			$database = JFactory::getDBO();
			$database->setQuery("SELECT * "
			." FROM #__content "
			." WHERE id='$item_id' "
			." LIMIT 1 "
			);
			$row = $database->loadObject();
			//foreach($rows as $row){
			if($row)
			{
				$cat_id = $row->catid;
				$created_by = $row->created_by;
			}
			//}
			$canDoContent = ContentHelper::getActions($cat_id, $item_id);
			
			/*
			if($canDo->get('core.edit') || $canDo->get('core.edit.own'))
			{
				return true;
			}
			else
			{
				return false;
			}
			
			*/
			$user		= JFactory::getUser();
			$userId		= $user->get('id');
			$canEdit	= $canDoContent->get('core.edit'); //$user->authorise('core.edit',			'com_content.article.'.$row->id);
			$canEditOwn	= $canDoContent->get('core.edit.own') && $created_by == $userId;
			
			//if(!$canDo->get('core.edit')){
			if((!$canEdit && !$canEditOwn))
			{
				return false;
			}
		}



		return true;


	}

	function to_previous_page_when_no_permission($permission){
		if(!PagesAndItemsHelper::check_acl($permission)){ //ms: replace for PI ACL?
		//if(!$this->check_pi_acl($permission)){
			//get previous url
			$previous_url = '';
			if(isset($_SERVER['HTTP_REFERER'])){
				$previous_url = $_SERVER['HTTP_REFERER'];
			}

			$message = PagesAndItemsHelper::get_no_permission_message($permission);//$this->get_no_permission_message($permission);

			if($previous_url){
				//set message
				JError::raiseWarning(403, $message);
				//redirect
				//check
				/*
				$pos = strpos($previous_url, 'view=item');
				if($pos === false)
				{
					$this->app->redirect($previous_url);
				}
				else
				{
					$this->app->redirect('index.php?option=com_pagesanditems&view=page&layout=root');
				}
				*/
				
				PagesAndItemsHelper::getApp()->redirect($previous_url); //$this->app->redirect($previous_url);
			}else{

				//set message
				JError::raiseWarning(403, $message);
				//redirect
				PagesAndItemsHelper::getApp()->redirect('index.php?option=com_pagesanditems&view=page&layout=root'); //$this->app->redirect('index.php?option=com_pagesanditems&view=page&layout=root');
				/*
				echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
				echo "<script>alert('".html_entity_decode($message)."'); window.history.go(-1); </script>";
				exit('<html><body><noscript>'.$message.'</noscript></body></html>');
				*/
			}
		}
	}

	function get_no_permission_message($permission){
		$message = 'you have no permission';
		switch ($permission){
		case '1':
			$message = JText::_('COM_PAGESANDITEMS_NO_CREATE_PAGE');
			break;
		case '2':
			$message = JText::_('COM_PAGESANDITEMS_NO_EDIT_PAGE');
			break;
		case '3':
			$message = JText::_('COM_PAGESANDITEMS_NO_PERMISSION_CREATE_NEW_ITEM');
			break;
		case '4':
			$message = JText::_('COM_PAGESANDITEMS_NOEDITITEM');
			break;
		}
		return $message;
	}

	function die_when_no_permission($permission){
		if(!PagesAndItemsHelper::check_acl($permission)){ //ms: replace for PI ACL?
		//if(!$this->check_pi_acl($permission)){
			$message = PagesAndItemsHelper::get_no_permission_message($permission);//$this->get_no_permission_message($permission);
			JError::raiseWarning(403, $message);
			//redirect
			$app = &JFactory::getApplication();
			$app->redirect('index.php?option=com_pagesanditems&view=page&layout=root');

			//echo $message;
			//exit;
		}
	}
	
	function breadcrumb($url = '')
	{
		$html = '';
		$html .= '<table cellspacing="0" cellpadding="0" border="0" width="100%">';
			$html .= '</tbody>';
				$html .= '<tr>';
					$html .= '<td  valign="top" width="20%">';
					$html .= '</td>';
					$html .= '<td valign="top">';
						$html .= '<div id="pi_breadcrumb">';
							$html .= $url;
						$html .= '</div>';
					$html .= '</td>';
				$html .= '</tr>';
			$html .= '</tbody>';
		$html .= '</table>';
		return $html;
	}
	
	function toogleTextPageCategories($text)
	{
		$page_id = intval(JRequest::getVar('pageId', null));
		$categoryId = intval(JRequest::getVar('categoryId', null));
		$menutype = intval(JRequest::getVar('menutype', null));
		$categoryId = intval(JRequest::getVar('categoryId', null));
		if(!$page_id && !$menutype && $categoryId)
		{
			//$text = 'COM_PAGESANDITEMS_CATEGORIE';
			$text = 'JCATEGORY';
		}
		
		return JText::_($text);
		
	}
	
	function toogleModelPageCategories($view = null)
	{
		$page_id = intval(JRequest::getVar('pageId', null));
		$menutype = intval(JRequest::getVar('menutype', null));
		$categoryId = intval(JRequest::getVar('categoryId', null));
		if(!$page_id && !$menutype && $categoryId)
		{
			//require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'models'.DS.'categorie.php');
			//$model = new PagesAndItemsModelCategorie();
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'models'.DS.'category.php');
			$model = new PagesAndItemsModelCategory();
		}
		else
		{
			$model = $view->getModel('Page');
		}
		return $model;
	}

	//ms: ???
	function toogleViewPageCategories($url = null, $amp = '&')
	{
		$page_id = intval(JRequest::getVar('pageId', null));
		$menutype = intval(JRequest::getVar('menutype', null));
		$categoryId = intval(JRequest::getVar('categoryId', null));
		if(!$page_id && !$menutype && $categoryId)
		{
			//$url = str_replace('view=page','view=categorie',$url);
			$url = str_replace('view=page','view=category',$url);
		}
		return $url;
	}
	
	function truncate_string($string, $length)
	{
		$dots='...';
		$string = trim($string);
		if(strlen($string)<=$length){
			return $string;
		}
		if(!strstr($string," ")){
			return substr($string,0,$length).$dots;
		}
		$lengthf = create_function('$string','return substr($string,0,strrpos($string," "));');
		$string = substr($string,0,$length);
		$string = $lengthf($string);
		while(strlen($string)>$length){
			$string=$lengthf($string);
		}
		return $string.$dots;
	}
	/*
	function loadJs($script)
	{
		//
		switch($script)
		{
			case 'pages':
			
			break;
			
			case 'items':
			
			break;
			
			case 'categories':
			
			break;
		}
	}
	*/
	/*
	function htmlReorderRows($name,$itemRows = array(),$header = array() ,$numberOfColumns = null,$hide_arrows = false,$rowColored = true,$addDelete = false,$load_js = true)
	{
		if($load_js){
			JHTML::script('reorder_rows.js', PagesAndItemsHelper::getDirJS().'/',false);
		}
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		$html = '';
		$counter = 0;
		if(count($itemRows)>=1)
		{
			$class[] = 'reorder_rows';
			if($addDelete)
			{
				$class[] = 'reorder_rows_add_delete';
			}
			if(!$hide_arrows)
			{
				$class[] = 'reorder_rows_arrows';
			}
			$class = 'class="'.implode(' ',$class).'"';
			
			
			
			
			
			//print header
			$html .= '<table '.$class.' width="100%" border="0" cellpadding="0" cellspacing="0">';
			if(count($header)>=1)
			{
				$html .= '<tr>';
				for ($k = 1; $k <= $numberOfColumns; $k++)
				{
					$classTd = isset($header['classHeader']['column'.$k]) ? 'class="'.$header['classHeader']['column'.$k].'"' : '';
					$styleTd = isset($header['styleHeader']['column'.$k]) ? 'style="'.$header['styleHeader']['column'.$k].'"' : '';
					$html .= '<td '.$classTd.'  '.$styleTd.'>';
						$html .= '<strong>';
							$html .= $header['column'.$k];
						$html .= '<strong>';
					$html .= '</td>';
				}
				
				//start add/delete columns header
				if($addDelete)
				{
					// $view = strtoupper(JRequest::getVar('view'));
					//'COM_PAGESANDITEMS_ORDERING_SAVE_ON_SAVE_'.strtoupper(JRequest::getVar('view'))
					$title = ''; //'title="'.JText::_('COM_PAGESANDITEMS_ROWS_ADD_DELETE').'"';
					//class="hasTip" '.$title.'
					$html .= '<td class="td_header_reorder_rows_add_delete">';
					if(count($itemRows) !=1)
					{
						//$html .= '<a>';
						//$html .= '<span class="hasTip" '.$title.'>';
						$html .= '<strong>';
							$html .= JText::_('JACTION_CREATE').'<br />'.JText::_('JACTION_DELETE');
						$html .= '</strong>';
						//$html .= '</span>';
						//$html .= '</a>';
					}
					else
					{
						$html .= '&nbsp;';
					}
					if(!$hide_arrows)
					{
						$html .= '<div class="display_none" id="items_'.$name.'_move_up_holder">';
								$html .= '<a title="'.JText::_('JLIB_HTML_MOVE_UP').'" class="jgrid" >'; //href="javascript: reorderItemsRows('.$i.','.($i-1).',\''.$name.'\','.$numberOfColumns.');">';
									$html .= '<span class="state uparrow">';
									$html .= '<span class="text">';
										$html .= JText::_('JLIB_HTML_MOVE_UP');
									$html .= '</span>';
								$html .= '</span>';
							$html .= '</a>';
						$html .= '</div>';
						$html .= '<div style="display:none;" id="items_'.$name.'_move_down_holder">';
							$html .= '<a title="'.JText::_('JLIB_HTML_MOVE_UP').'" class="jgrid" >'; //href="javascript: reorderItemsRows('.$i.','.($i-1).',\''.$name.'\','.$numberOfColumns.');">';
								$html .= '<span class="state uparrow">';
									$html .= '<span class="text">';
										$html .= JText::_('JLIB_HTML_MOVE_UP');
									$html .= '</span>';
								$html .= '</span>';
							$html .= '</a>';
						$html .= '</div>';
					}
					$html .= '</td>';
				}
				
				//start reorder columns header
				if(!$hide_arrows)
				{
					// $view = strtoupper(JRequest::getVar('view'));
					//'COM_PAGESANDITEMS_ORDERING_SAVE_ON_SAVE_'.strtoupper(JRequest::getVar('view'))
					$title = isset($header['column'+($k+1)]) ? 'title="'.$header['column'+($k+1)].'"' :  'title="'.JText::_('COM_PAGESANDITEMS_ORDERING_SAVE_ON_SAVE_'.strtoupper(JRequest::getVar('view'))).'"';
					
					//$html .= '<td colspan="4" class="td_reorder_rows_arrows" >';
					$html .= '<td class="td_header_reorder_rows_arrows" >';
					if(count($itemRows) !=1)
					{
						$html .= '<strong>';
							$html .= JText::_('COM_PAGESANDITEMS_ORDERING');
						$html .= '</strong>';
					}
					else
					{
						$html .= '&nbsp;';
					}
					$html .= '</td>';
				}
				//end reorder columns header
				$html .= '</tr>';
			}
		
			//loop through rows
			$k = 0;
			for ($i = 1; $i <= count($itemRows); $i++)
			{
				//ms: here we set the class row0 ore row1 for different color
				$class = $rowColored ? 'class="row'.$k.'"' : '';
				$html .= '<tr '.$class.'>';
				
				
				//for($n = 0; $n < count($temp_itemtypes); $n++)
				for($j = 1; $j <= $numberOfColumns; $j++)
				{
					$classTd = isset($header['class']['column'.$j]) ? 'class="'.$header['class']['column'.$j].'"' : '';
					//$classTd ='';
					//category_column = "category_column_"+j+"_"+i;			
					//category_column_content = document.getElementById(category_column).innerHTML;
				
					$html .= '<td '.$classTd.' id="items_'.$name.'_column_'.$j.'_'.$i.'">';
						
						//_category_
						$html .= $itemRows[$i]['column'.$j];
					$html .= '</td>';
				}
				
				//start add/delete columns
				if($addDelete)
				{
					$html .= '<td class="td_reorder_rows_add_delete">'; // style="width:50px;">';
						$button = PagesandItemsHelper::getButtonMaker();
						$button->imagePath = PagesandItemsHelper::getDirIcons();
						$button->buttonType = 'input';
						$button->class = 'fltlft button';
						$button->style = 'border:0;background-color:transparent;';
						$button->onclick = 'addRow(\''.$name.'\','.$i.');';
						$button->imageName = 'base/icon-16-plus-small.png';
						$html .= $button->makeButton();

						$button = PagesandItemsHelper::getButtonMaker();
						$button->imagePath = PagesandItemsHelper::getDirIcons();
						$button->buttonType = 'input';
						$button->class = 'fltlft  button';
						$button->style = 'border:0;background-color:transparent;';
						$button->onclick = 'deleteRow(\''.$name.'\','.$i.');';
						$button->imageName = 'base/icon-16-minus-small.png';
						$html .= $button->makeButton();
					$html .= '</td>';
				}
				//end add/delete columns
				
				//start reorder columns categories
				if(!$hide_arrows)
				{
					
					//$html .= '<td width="10">';
					$html .= '<td class="td_reorder_rows_arrows">';
					$html .= '<div class="div_reorder_rows_arrows">';
					$html .= '<span>';
					if($i!=1)
					{
						if($joomlaVersion >= '1.6')
						{
							
							$html .= '<a title="'.JText::_('JLIB_HTML_MOVE_UP').'" class="jgrid" href="javascript: reorderItemsRows('.$i.','.($i-1).',\''.$name.'\','.$numberOfColumns.');">';
								$html .= '<span class="state uparrow">';
									$html .= '<span class="text">';
										$html .= JText::_('JLIB_HTML_MOVE_UP');
									$html .= '</span>';
								$html .= '</span>';
							$html .= '</a>';
							
						}
						else
						{
							$html .= '<a href="javascript: reorderItemsRows('.$i.','.($i-1).',\''.$name.'\','.$numberOfColumns.');"><img src="/administrator/images/uparrow.png" alt="move up" border="0" /></a>';
						}
					}
					else
					{
						$html .= '&nbsp;';
					}
					//$html .= '</span>';
					//$html .= '</td>';
					//$html .= '<td width="1">';
					//$html .= "&nbsp;";
					//$html .= '</td>';
					//$html .= '<td width="10">';
					$html .= '</span>';
					$html .= '<span>';
					
					if($i!=count($itemRows))
					{
						if($joomlaVersion >= '1.6')
						{
							$html .= '<a title="'.JText::_('JLIB_HTML_MOVE_DOWN').'" class="jgrid" href="javascript: reorderItemsRows('.$i.','.($i+1).',\''.$name.'\','.$numberOfColumns.');"><span class="state downarrow"><span class="text">'.JText::_('JLIB_HTML_MOVE_DOWN').'</span></span></a>';
						}
						else
						{
							$html .= '<a href="javascript: reorderItemsRows('.$i.','.($i+1).',\''.$name.'\','.$numberOfColumns.');"><img src="/administrator/images/downarrow.png" alt="move down" border="0" /></a>';
						}
					}
					else
					{
						$html .= '&nbsp;';
					}
					//$html .= '</td>';
					//$html .= '<td width="12">';
					//	$html .= "&nbsp;";
					$html .= '</span>';
					$html .= '</div>';
					$html .= '</td>';
				}
				$html .= '</tr>';
				$k = 1 - $k;
				$counter++;
			}
			
			$html .= '</table>';
			
		}
		//2 hidden fields which are usefull for updating the ordering when submitted
		$html .= '<input name="items_'.$name.'_are_reordered" id="items_'.$name.'_are_reordered" type="hidden" value="false" />';
		$html .= '<input name="items_'.$name.'_total" id="items_'.$name.'_total" type="hidden" value="'.$counter.'" />';
		
		return $html;
	}
	*/
	
	function getHeaderImageTitle($image,$title = '',$class = 'headerIcon32')
	{
		$imgClass = explode("class:",$image);
		$html = (count($imgClass) && count($imgClass) == 2) ? '<div class="'.$imgClass[1].' '.$class.'">' : '<div class="'.$class.'" style="background-image: url('.$image.');">';
			$html .= '<h1 class="pi_h1" >';
				$html .= $title ? $title : JText::_( 'COM_PAGESANDITEMS');
			$html .= '</h1>';
		$html .= '</div>';
		return $html;
	}
	
	function getThImageTitle($image,$title = '',$image2 = null,$iconClass = 'thIcon16',$class = '')
	{
		$imgClass = explode("class:",$image);
		$imageDisplay1 = (count($imgClass) && count($imgClass) == 2) ? '<div class="'.$imgClass[1].' '.$iconClass.'">' : '<div class="'.$iconClass.'" style="background-image: url('.$image.');">';
		$imageDisplay2 = '';
		if($image2)
		{
		$imgClass2 = explode("class:",$image2);
		$imageDisplay2 = (count($imgClass2) && count($imgClass2) == 2) ? '<div class="'.$imgClass2[1].' '.$iconClass.'">' : '<div class="'.$iconClass.'" style="background-image: url('.$image2.');float:left;left: 0;position: relative;">';
		$imageDisplay2 .= '</div>';
		}
		$html = '';
		$html .= $imageDisplay2;
		$html .= $imageDisplay1;
		
			$html .= '<p '.($class ? 'class="'.$class.'"' : '').'>';
				 // ? $title : JText::_( 'COM_PAGESANDITEMS');
			$html .= $title;
			$html .= '</p>';
		$html .= '</div>';
		//
		return $html;
	}
	
	function get_page_id_from_item_id($item_id){	
		$database = JFactory::getDBO();
		//$cat_id = $this->get_cat_id_from_item($item_id);
		$cat_id = PagesAndItemsHelper::get_cat_id_from_item($item_id);
		
		$database->setQuery( "SELECT id, link, type FROM #__menu ");
		$menuitems = $database->loadObjectList();

		$original_page_id = false;
		foreach($menuitems as $menu_item_page){
		
			$temp_cat_id = 0;
			//if category blog
			if( strstr($menu_item_page->link, 'index.php?option=com_content&view=category&layout=blog') && $menu_item_page->type!='url' && $menu_item_page->type=='component'){
				//get the category id of each menu item
				$pos_cat_id = strpos($menu_item_page->link,'id=');
				$temp_cat_id = substr($menu_item_page->link, ($pos_cat_id+3), strlen($menu_item_page->link));
				if($cat_id==$temp_cat_id){
					$original_page_id = $menu_item_page->id;
					break;
				}
			}elseif(strstr($menu_item_page->link, 'index.php?option=com_content&view=article&id='.$item_id) || strstr($menu_item_page->link, 'index.php?option=com_content&task=view&id='.$item_id)){
				//full item layout
				$original_page_id = $menu_item_page->id;
				break;
			}
		}
		return $original_page_id;
	}
	
	function get_cat_id_from_item($item_id){
		$database = JFactory::getDBO();
		$database->setQuery( "SELECT catid FROM #__content WHERE id='$item_id' LIMIT 1 ");
		$items = $database->loadObjectList();
		$catid = false;
		foreach($items as $item){
			$catid = $item->catid;
		}
		return $catid;
	}
	
	function getLanguageFilter()
	{
		$app = JFactory::getApplication();
		//$input = $app->input;
		//$language = $input->get('filter_language', -1);
		$language = $app->getUserStateFromRequest( 'com_pagesanditems.filters.language', 'filter_language',-1,'cmd' );
		return $language;
	}
	
	function makeLanguageSelect()
	{
		/*
		language select and tree is incompatible
		so we need to get what?
		
		so we return '' at this moment
		*/
		
		return '';
		
		$sub_task = JRequest::getVar('sub_task', '');
		$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
		
		if($useCheckedOut && $sub_task != '')
		{
			return '';
		}
		else
		{
	
			//$language = JRequest::getVar('filter_language', -1);
			$language = PagesAndItemsHelper::getLanguageFilter();
			$langList[] = array('value' => '-1', 'text' => JText::_('JOPTION_SELECT_LANGUAGE'));
			$langList = array_merge($langList,JLanguageHelper::createLanguageList('nothing', constant('JPATH_ADMINISTRATOR'), true, true));
			$langList[] = array('value' => '*', 'text' => '*'); //JText::_('JALL_LANGUAGE'));
			$select = JHTML::_('select.genericlist', $langList, 'filter_language', 'class="inputbox" size="1" onchange="Javascript:document.adminForm.submit();"', 'value', 'text', $language );
			return $select;
		}
	}
	
}
?>