<?php
/**
* @version		2.0.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2011 Carsten Engel. All rights reserved.
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

	
	public $app;
	public $db;	
	public $version = '2.0.0';
	public $config;
	public $itemtypes;
	public $dirIcons;
	public $pathPluginsItemtypes; //ms: this can remove
	
	
	function __construct(){
		$this->app = &JFactory::getApplication();
		$this->db = JFactory::getDBO();
		$this->config = $this->getConfig();
		$this->dirIcons = $this->getDirIcons();
		$this->pathPluginsItemtypes = JPATH_PLUGINS.DS.'pages_and_items'.DS.'itemtypes'; //ms: this can remove		
	}
	
	
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
			$prefix = $type.'_'.$extension_folder;//.DS;


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
		/*
		if($defaultLang != 'en-GB')
		{
			$defaultLang = 'en-GB';
		}
		*/
		
		$lang->load(strtolower($extension), $path, null, false) //, false)
		||	$lang->load(strtolower($extension), $pathExtensions, null, false) //ms: add)

		||	$lang->load(strtolower($extension), $path, $defaultLang, false) //, false)
		||	$lang->load(strtolower($extension), $pathExtensions, $defaultLang, false) //ms: add)
		;
		//dump($lang->getStrings());
		//dump($lang->getPaths());
		
	}
	
	/*
	function loadIconsCss()
	{
		$path = str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).DS.'..')));
		return '<link href="'.JURI::root(true).'/'.$path.'/css/pagesanditems_icons.css" rel="stylesheet" type="text/css" />'."\n";
	}
	*/
	
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
	
	function redirect_to_url($url, $message){
		$this->app->redirect($url, $message);
	}	

	function getConfigAsRegistry()
	{
		$config = PagesAndItemsHelper::getConfig();
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
					if($key != 'page_new_attribs')
					{
						$configtemp[] = $key.'='.$value;
					}
		/*
		$pos_start_page_attribs = strpos($raw, 'START_PAGE_NEW_ATTRIBUTES');
		$start_of_vars = $pos_start_page_attribs+26;
		$page_new_attribs = substr($raw, $start_of_vars, 99999);
		$config['page_new_attribs'] = $page_new_attribs;
		*/		
				}
			}
		}
		//dump($configtemp);
		//dump($changeValue);
		$configuration = implode("\n",$configtemp);
		$config_page_new_attribs = '';
		if(isset($config['page_new_attribs']))
		{
			$config_page_new_attribs = $config['page_new_attribs'];
			/*
			dump($config['page_new_attribs']);
			for($n = 0; $n < count($config['page_new_attribs']); $n++)
			{
				$row = each($config['page_new_attribs']);
				$config_page_new_attribs .= "\n$row[key]=$row[value]";
			}
			*/
		}
		$configuration .= '
		START_PAGE_NEW_ATTRIBUTES=';
		$configuration .= $config_page_new_attribs;
		
		//dump($configuration,'configuration');
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
			$config[$var] = $value;
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
		
		//reformat permissions to array
		$temp = $config['permissions'];
		$config['permissions'] = explode(',', $temp);
		
		return $config;
	
	}

	function getDirIcons(){
			
		$dirIcons = 'components/com_pagesanditems/media/images/icons/';
		//if(!$this->app->isAdmin()){ is causing error when saving a new cat blog ?!!
		$app = JFactory::getApplication();
		if(!$app->isAdmin()){
			$dirIcons = 'administrator/'.$dirIcons;
		}
		
		//ms: 04.05.2011 another way to get the correct path this will work also on subdomain (JURI::root(true) return the subdomain)
		$dirIcons = JURI::root(true).'/'.str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).DS.'..'.DS.'media'.DS.'images'.DS.'icons').DS));
		defined('COM_PAGESANDITEMS_DIR_ICONS') or define('COM_PAGESANDITEMS_DIR_ICONS',$dirIcons);
		return $dirIcons;
	}
	
	function getDirImages()
	{		
		$dirImages = 'components/com_pagesanditems/images/';
		$app = JFactory::getApplication();
		if($app->isAdmin()){
			$dirImages = 'administrator/'.$dirImages;
		}
		//ms: 04.05.2011 another way to get the correct path this will work also on subdomain (JURI::root(true) return the subdomain)
		$dirImages = JURI::root(true).'/'.str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).DS.'..'.DS.'images').DS));
		
		defined('COM_PAGESANDITEMS_DIR_IMAGES') or define('COM_PAGESANDITEMS_DIR_IMAGES',$dirImages);
		return $dirImages;
	}
	
	function getDirComponentAdmin()
	{
		//ms: 04.05.2011 another way to get the correct path this will work also on subdomain (JURI::root(true) return the subdomain)
		//return JURI::root(true).'/'.str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).DS.'..')));
		
		
		//return str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).DS.'..')));
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
		if($this->app->isAdmin())
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
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
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
		}
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
dump($groups);
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
		$app = &JFactory::getApplication();
		//check if admin
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
		
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'buttonmaker.php');
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
		//JToolBarHelper::title( JText::_( 'Pages and Items' ).' '.$more, 'content_mvc.png' );
		JToolBarHelper::title( JText::_( 'Pages and Items' ).' '.$more, 'pi.png' );
	}
	
	public static function addSubmenu($vName = 'page')
	{
		$extensionType = JRequest::getVar('extensionType', ''); //is the extensionName
		//$path = str_replace(DS,'/',str_replace(JPATH_SITE,'',JPATH_COMPONENT_ADMINISTRATOR));
		$path = JURI::root(true).str_replace(DS,'/',str_replace(JPATH_ROOT,'',JPATH_COMPONENT_ADMINISTRATOR));
		JSubMenuHelper::addEntry(
			'<img src="'.$path.'/media/images/icons/icon-16-pi.png" alt="" style="vertical-align: middle;top: -2px;position: relative;" />&nbsp;Pages and Items',
			'index.php?option=com_pagesanditems&view=page&layout=root',
			$vName != 'rightsmanager' && $vName != 'config' && $vName != 'config_custom_itemtype' && $vName != 'config_custom_itemtype_field' && $vName != 'config_itemtype' && $vName != 'manage' && $vName != 'install' && $vName != 'manageextension' && $extensionType != 'manager' && $vName != 'managers'
		);
				
		JSubMenuHelper::addEntry(
			'<img src="'.$path.'/media/images/icons/base/icon-16-config.png" alt="" style="vertical-align: middle;top: -2px;position: relative;" />&nbsp;'.JText::_('COM_PAGESANDITEMS_CONFIG'),
			'index.php?option=com_pagesanditems&view=config',
			//'',
			$vName == 'config' || $vName == 'config_custom_itemtype' || $vName == 'config_custom_itemtype_field' || $vName == 'config_itemtype'
			);
		
		/*
		ms:
		// c: I think I like this better 
		JSubMenuHelper::addEntry(
			'<img src="'.$path.'/media/images/icons/extensions/icon-16-plugin_edit.png" alt="" style="vertical-align: middle;top: -2px;position: relative;" />&nbsp;'.JText::_('COM_PAGESANDITEMS_PLUGIN_MANAGER'),
			'index.php?option=com_pagesanditems&view=manage',
			$vName == 'manage' || $vName == 'manageextension'
		);

		JSubMenuHelper::addEntry(
			'<img src="'.$path.'/media/images/icons/extensions/icon-16-plugin_add.png" alt="" style="vertical-align: middle;top: -2px;position: relative;" />&nbsp;'.JText::_('COM_PAGESANDITEMS_PLUGIN_INSTALLER'),
			'index.php?option=com_pagesanditems&view=install',
			$vName == 'install'
		);
		*/
		/*
		ms: 06.05.2011
		here we get the managers
		the submenu-item only display if we have an manager who return onGetManager the template manager have not an onGetManager function
		*/
		/*
		$managers = array();
		require_once(realpath(dirname(__FILE__).DS.'..').DS.'includes'.DS.'extensions'.DS.'managerhelper.php');
		$dispatcher = &JDispatcher::getInstance();
		$typeName = 'ExtensionManagerHelper';
		$typeName::importExtension(null, null,true,null,true);
		$dispatcher->trigger('onGetManager', array ( &$managers));
		if(count($managers))
		{
		*/
			JSubMenuHelper::addEntry(
				'<img src="'.$path.'/media/images/icons/base/icon-16-toolbox.png" alt="" style="vertical-align: middle;top: -2px;position: relative;" />&nbsp;'.JText::_('COM_PAGESANDITEMS_MANAGERS'),
				'index.php?option=com_pagesanditems&view=managers',
				$vName == 'managers' || $extensionType == 'manager'
			);
		//}

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
				JToolBarHelper::save( 'config.config_save', JText::_('COM_PAGESANDITEMS_SAVE') );
				JToolBarHelper::apply( 'config.config_apply', JText::_('COM_PAGESANDITEMS_APPLY') );
				JToolBarHelper::cancel( 'config.cancel', JText::_('COM_PAGESANDITEMS_CANCEL') );
			break;
			
			case 'config_itemtype':
				JToolBarHelper::save( 'itemtype.config_itemtype_save', JText::_('COM_PAGESANDITEMS_SAVE') );
				JToolBarHelper::apply( 'itemtype.config_itemtype_apply', JText::_('COM_PAGESANDITEMS_APPLY') );
				JToolBarHelper::cancel( 'itemtype.cancel', JText::_('COM_PAGESANDITEMS_CANCEL') );
			break;
			
			case 'config_custom_itemtype':
				if(!$sub_task && $sub_task!='new')
				{
					JToolBarHelper::custom( 'config_itemtype_render', 'copy.png', 'copy_f2.png', JText::_('COM_PAGESANDITEMS_RENDER_ITEMTYPES'), false );
					JToolBarHelper::save( 'customitemtype.config_custom_itemtype_save', JText::_('COM_PAGESANDITEMS_SAVE') );
				}
				JToolBarHelper::apply( 'customitemtype.config_custom_itemtype_apply', JText::_('COM_PAGESANDITEMS_APPLY') );
				if(!$sub_task && $sub_task != 'new')
				{
					//TODO visible over extensions/managers/archive ?
					//can we make only an placeholder so we can add over the extensions?
					//ore trigger the extensions/managers/archive to make the button
					//like trigger('onToolbarButton',array('archive','config_custom_itemtype_archive'))
					// ms: at this moment no customitemtype archive
					//JToolBarHelper::custom( 'customitemtype.config_custom_itemtype_archive','archive','archive','archive',false);
					//TODO visible over extensions/managers/trash ?
					// ms: at this moment no customitemtype trash
					//JToolBarHelper::trash( 'customitemtype.config_custom_itemtype_trash','trash','trash','trash',false);
					JToolBarHelper::custom('customitemtype.config_custom_itemtype_delete','delete','delete','delete',false);
				}
				JToolBarHelper::cancel( 'customitemtype.cancel', JText::_('COM_PAGESANDITEMS_CANCEL') );
			break;
			
			case 'config_custom_itemtype_field':
				JToolBarHelper::save( 'customitemtypefield.config_custom_itemtype_field_save', JText::_('COM_PAGESANDITEMS_SAVE') );
				JToolBarHelper::apply( 'customitemtypefield.config_custom_itemtype_field_apply', JText::_('COM_PAGESANDITEMS_APPLY') );
				if(!$sub_task && $sub_task != 'new')
				{
					// ms: at this moment no  archive
					//JToolBarHelper::custom( 'customitemtypefield.config_custom_itemtype_field_archive','archive','archive','archive',false);
					// ms: at this moment no trash
					//JToolBarHelper::trash( 'customitemtypefield.config_custom_itemtype_field_trash','trash','trash','trash',false);
					// ms: at this moment no delete
					//JToolBarHelper::custom('customitemtypefield.config_custom_itemtype_field_delete','delete','delete','delete',false);
				}
				JToolBarHelper::cancel( 'customitemtypefield.cancel', JText::_('COM_PAGESANDITEMS_CANCEL') );
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
		
			default:
				$extensionType = JRequest::getVar('extensionType', '');
				if($extensionType != 'manager')
				{
					JToolBarHelper::apply( 'page.root_save', JText::_('COM_PAGESANDITEMS_SAVE') );
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
	
	function itemtype_select($menu_id)
	{
		$html = '';
		$html .= JText::_('COM_PAGESANDITEMS_ITEMTYPE').': ';
		//get itemtype aliasses in new array
		$itemtypes = array();			
		foreach($this->getItemtypes() as $type)
		{
			/*
			TODO rewrite 
			*/			
			
			$type_array = array($type, $this->translate_item_type($type));
			
			
			array_push($itemtypes, $type_array);			
		}
		
		//sort array on alias
		foreach ($itemtypes as $key => $row) 
		{
			$order[$key]  = strtolower($row[1]);    
		}
		array_multisort($order, SORT_ASC, $itemtypes);

		//print_r($itemtypes);
		$link = '';

		//$html .= '<select name="select_itemtype" id="select_itemtype">';
		$html .= '<select id="select_itemtype" ';
		if(!$menu_id && $this->app->isAdmin()){
			//only when no id AND in the backend
			$link = 'index.php?option=com_pagesanditems'; //.$option;
			//$link .= '&amp;task=item.doExecute';
			//$link .= '&amp;extension=menuitemtypeselect';
			//$link .= '&amp;extensionType=html';
			//$link .= '&amp;extensionFolder=page_childs'; ///menuitemtypeselect';
			$link .= '&amp;view=item';
			$link .= '&amp;sub_task=new';
			$link .= '&amp;tmpl=component';
			$link .= '&amp;pageType=content_article';
			$link .= '&amp;menutype='.$this->pageMenuItem->menutype;
			$link .= '&amp;pageId='.$menu_id;
			//$link .= '&amp;select_itemtype=';
			
			
			//$html .= 'onchange="document.getElementById(\'button_new_itemtype\').href.value = \''.$link.'\'+this.value\';" ';
			
		}
		$html .= 'name="select_itemtype" ';
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
					if(!$menu_id)
					{
						$link .= '&amp;select_itemtype='.$type[0];
					}
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
		if(!$menu_id && $this->app->isAdmin()){
			$html .= '&nbsp;&nbsp;';
	
			$button = $this->getButtonMaker();
			$button->imagePath = $this->dirIcons;
			$button->buttonType = 'input';
			$button->text = JText::_('COM_PAGESANDITEMS_NEW_ITEM');
			$button->alt = JText::_('COM_PAGESANDITEMS_MAKE_NEW_ITEM');
			
			
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
			
			$button->imageName = 'base/icon-16-add.png';
			$html .= $button->makeButton();
		}
		return $html;
	}
	
	function getItemtypes(){
		if(!$this->itemtypes){
			$this->setItemtypes();
		}
		return $this->itemtypes;
	}
	
	function setItemtypes(){		
		$temp_itemtypes = explode(',',$this->config['itemtypes']);
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
		//make unique
		$temp = array_unique($temp);
		$this->itemtypes = $temp;
	}
	
	function translate_item_type($item_type){
		/*
		all itemtypes are extensions so we can add language to each itemtype
		if we want so all itemtype JText can remove from the base language file
		and only the custom_* need extra routine here
		
		*/
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
			$this->db->setQuery("SELECT name FROM #__pi_customitemtypes WHERE id='$type_id' LIMIT 1");
			$rows = $this->db->loadObjectList();
			$row = $rows[0];
			$plugin_name = $row->name;
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
	
	function trashPage($trashPageId){
	
		//check if menuitem is content-category-blog, and if so, get cat_id
		$content_category_blog = false;
		$this->db->setQuery("SELECT link, type FROM #__menu WHERE id='$trashPageId' LIMIT 1");
		$rows = $this->db->loadObjectList();
		$row = $rows[0];
		if((strstr($row->link, 'index.php?option=com_content&view=category&layout=blog') && $row->type!='url'))
		{
			$content_category_blog = true;
			$cat_id = str_replace('index.php?option=com_content&view=category&layout=blog&id=','',$row->link);
		}
		
		//trash mainmenuitem
		$this->db->setQuery( "UPDATE #__menu SET published='-2' WHERE id='$trashPageId'");
		$this->db->query();
		
		//only trash items on page when its a content_blog_category
		if($content_category_blog && $this->config['page_trash_items']){
			//trash all items on the page (category)
			$this->trashItemsCategory($cat_id);
		}
		
		//trash category
		if($content_category_blog && $this->config['page_trash_cat']){
			$this->db->setQuery( "UPDATE #__categories SET published='-2' WHERE id='$cat_id'");
			$this->db->query();
		}
		
		//trash all underlying child-pages
		$this->trashPageChildren($trashPageId);
		
		
	}
	
	function deletePage($deletePageId){
	
		//check if menuitem is content-category-blog, and if so, get cat_id
		$content_category_blog = false;
		$this->db->setQuery("SELECT link, type FROM #__menu WHERE id='$deletePageId' LIMIT 1");
		$rows = $this->db->loadObjectList();
		$row = $rows[0];
		if((strstr($row->link, 'index.php?option=com_content&view=category&layout=blog') && $row->type!='url'))
		{
			$content_category_blog = true;
			$cat_id = str_replace('index.php?option=com_content&view=category&layout=blog&id=','',$row->link);
		}
		
		//only delete items on page when its a content_blog_category
		if($content_category_blog && $this->config['page_delete_items']){
			//trash all items on the page (category)
			$this->deleteItemsCategory($cat_id);
		}
		
		//delete category
		if($content_category_blog && $this->config['page_delete_cat']){
			$this->db->setQuery( "DELETE FROM #__categories WHERE id='$cat_id'");
			$this->db->query();
		}
		
		//delete all underlying child-pages
		$this->deletePageChildren($deletePageId);
		
		//delete mainmenuitem
		$this->db->setQuery( "DELETE FROM #__menu WHERE id='$deletePageId'");
		$this->db->query();		
		
	}
	
	function deleteItemsCategory($cat_id){
   
        //get content id's which are on frontpage
        $this->db->setQuery("SELECT content_id FROM #__content_frontpage");
        $frontpage_items = $this->db->loadResultArray();
       
        //get content-index to know which item has which itemtype
        $this->db->setQuery("SELECT item_id, itemtype FROM #__pi_item_index");
        $index_items = $this->db->loadObjectList();
       
        //trash all items in the category
        $this->db->setQuery("SELECT id FROM #__content WHERE catid='$cat_id'" );
        $rows = $this->db->loadObjectList();
       
        //ms: here we will load all itemtypes
        require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'extensions'.DS.'itemtypehelper.php');
        ExtensionItemtypeHelper::importExtension(null, null,true,null,true);
        $dispatcher = &JDispatcher::getInstance();

        foreach($rows as $row){
       
            $item_id = $row->id;
            $this->db->setQuery( "DELETE FROM #__content WHERE id='$item_id'");
            $this->db->query();
           
            //if item was on frontpage, take it off
            if(in_array($item_id, $frontpage_items)){
                $this->take_item_off_frontpage($item_id);
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
            $this->db->setQuery("SELECT item_id FROM #__pi_item_other_index WHERE other_item_id='$item_id' ");
            $other_items = $this->db->loadObjectList();
            foreach($other_items as $other_item){
                //ms: update_duplicate_item is in models/page.php but i think it must move to extensions/itemtypes/other_item.php
                //old $this->update_duplicate_item($other_item->item_id, $item_id);
                //ms: i am confused see the extensions/itemtypes/other_item.php function update_duplicate_item($item_id, $other_item_id)
              
                $dispatcher->trigger('update_duplicate_item',array($other_item->item_id, $item_id));
                //ms: i think we must write: $dispatcher->trigger('update_duplicate_item',array($item_id,$other_item->item_id));
                $other_item_id = $other_item->item_id;
               
                $this->db->setQuery( "DELETE FROM #__content WHERE id='$other_item_id' ");
                $this->db->query();
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
			
		$this->db->setQuery("SELECT id FROM #__menu WHERE parent_id='$trashPageId'" );
		
		$rows = $this->db->loadObjectList();
		foreach($rows as $row){
			$this->trashPage($row->id);
		}
	}
	
	function deletePageChildren($deletePageId){	
			
		$this->db->setQuery("SELECT id FROM #__menu WHERE parent_id='$deletePageId'" );
		
		$rows = $this->db->loadObjectList();
		foreach($rows as $row){
			$this->deletePage($row->id);
		}
	}
	
	function trashItemsCategory($trashCatId){
		
		//trash all items in the category
		$this->db->setQuery("SELECT id FROM #__content WHERE catid='$trashCatId'" );
		$rows = $this->db->loadObjectList();

		foreach($rows as $row){
		
			$item_id = $row->id;
			
			$this->item_state($item_id, '-2');
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
		$table = $row = & JTable::getInstance('menu');
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
			$this->take_item_off_frontpage($item_id);
			
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
		$this->db->setQuery("DELETE FROM #__content_frontpage WHERE content_id='$item_id' ");
		$this->db->query();
		
		$this->db->setQuery( "UPDATE #__content SET featured='0' WHERE id='$item_id' ");
		$this->db->query();
		
	}	
	
	function keep_item_index_clean(){
	
		//get content id's
		$this->db->setQuery( "SELECT id, state "
		. "FROM #__content "
		);
		$items = $this->db->loadObjectList();
		
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
		$this->db->setQuery( "SELECT id, item_id, itemtype "
		. "FROM #__pi_item_index "
		);
		$index_items = $this->db->loadObjectList();
		
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
				$this->db->setQuery("DELETE FROM #__pi_item_index WHERE id='$index_id'");
				$this->db->query();
			}
		}
		
		/*
		//clean items which where customitemtypes, but have now become normal text-types, from custom itemtype codes
		foreach($from_cit_to_text as $itemid){
			//get item texts
			$this->db->setQuery( "SELECT introtext, fulltext "
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
		$this->db->setQuery("SELECT id, ordering, catid FROM #__content WHERE catid='$catId' AND (state='0' OR state='1') ORDER BY ordering ASC" );
		$rows = $this->db->loadObjectList();
		$counter = 1;
		foreach($rows as $row){
			//reorder to make sure all is well
			$rowId = $row->id;
			$this->db->setQuery( "UPDATE #__content SET ordering='$counter' WHERE id='$rowId'");
			$this->db->query();
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
		if(isset($this->config[$thing])){
			if($this->config[$thing]){
				$return = 1;
			}
		}
		if($this->getIsSuperAdmin() && !$this->config['item_props_hideforsuperadmin']){
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
	
	function to_previous_page_when_no_permission($permission){
		if(!$this->check_pi_acl($permission)){
			//get previous url
			$previous_url = '';
			if(isset($_SERVER['HTTP_REFERER'])){
				$previous_url = $_SERVER['HTTP_REFERER'];							
			}
			
			$message = $this->get_no_permission_message($permission);			
			
			if($previous_url){
				//set message
				JError::raiseWarning(403, $message);
				//redirect
				$this->app->redirect($previous_url);
			}else{
				echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
				echo "<script>alert('".html_entity_decode($message)."'); window.history.go(-1); </script>";
				exit('<html><body><noscript>'.$message.'</noscript></body></html>');
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
		if(!$this->check_pi_acl($permission)){
			$message = $this->get_no_permission_message($permission);
			echo $message;
			exit;
		}
	}
	
		

}
?>