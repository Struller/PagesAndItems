<?php
/**
* @version		2.1.5
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

//defined('JPATH_BASE') or die;
defined('_JEXEC') or die;

jimport('joomla.event.event');
jimport('joomla.html.parameter');

abstract class PagesAndItemsExtension extends JEvent
{


	/**
	 * A JRegistry object holding the parameters for the plugin
	 *
	 * @var		A JRegistry object ore JParameter object
	 * @access	public
	 * @since	1.5
	 */
	public $params = null;

	/**
	 * The name of the plugin
	 *
	 * @var		sring
	 * @access	protected
	 */
	protected $_name = null;

	/**
	 * The plugin type
	 *
	 * @var		string
	 * @access	protected
	 */
	protected $_type = null;


	/**
	 * The extension id
	 *
	 * @var		string
	 * @access	protected
	 */
	protected $_extension_id = null;

	/**
	 * The plugin folder
	 *
	 * @var		string
	 * @access	protected
	 */
	protected $_folder = null;
	
	protected $_required_version = null;
	
	//protected $_subject = null;

	//ms: remove?
	//public $controller = null;
	//ms: remove?
	//public $helper = null;
	//ms: remove?
	//public $model = null;

	var $db;
	//ms: remove?
	//var $live_site;
	var $pi_config;
	//var $dirIcons;
	//var $joomlaVersion;


	/**
	 * Constructor
	 *
	 * @param object $subject The object to observe
	 * @param array  $config  An optional associative array of configuration settings.
	 */
	public function __construct(&$subject, $config = array())
	{

		$this->_subject = $subject;

		//$version = new JVersion();
		//$this->joomlaVersion = $version->getShortVersion();

		// Get the plugin type.
		if (isset($config['extension_id']))
		{
			$this->_extension_id = $config['extension_id'];
		}

		// Get the plugin name.
		if (isset($config['name']))
		{
			$this->_name = $config['name'];
		}
		
		// Get the plugin required_version.
		if (isset($config['required_version']))
		{
			$this->_required_version = $config['required_version'];
		}
		
		
		
		// Get the plugin type.
		if (isset($config['type']))
		{
			$this->_type = $config['type'];
		}
		if($this->_type != 'fieldtype')
		{
			/*
			if(!defined('COM_PAGESANDITEMS_DIR_ICONS'))
			{
				require_once(dirname(__FILE__).'/../../helpers/pagesanditems.php');
				$this->dirIcons = PagesAndItemsHelper::getDirIcons();
			}
			else
			{
				$this->dirIcons = COM_PAGESANDITEMS_DIR_ICONS;
			}
			*/
		}
		$this->db =& JFactory::getDBO();
		$this->live_site =JURI::root();

		// Get the plugin folder.
		if (isset($config['folder']))
		{
			$this->_folder = $config['folder'];
		}

		if (isset($config['params']))
		{

			$path = dirname(__FILE__).'/../../extensions';
			if($this->_folder)
			{
				$extension_folder = $this->_folder;
				$path = str_replace('/',DS,$path.DS.$this->_type.'s'.DS.$extension_folder);
			}
			else
			{
				$path = str_replace('/',DS,$path.DS.$this->_type.'s');
			}
			$path = $path.DS.$this->_name.DS.$this->_name.'.xml';
			//echo $path;
			$version = new JVersion();
			$joomlaVersion = $version->getShortVersion();
			if($joomlaVersion < '1.6')
			{
				//joomla 1.5.x
				if ($config['params'] instanceof JParameter)
				{
					$this->params = $config['params'];
				}
				else
				{
					//we will work with json so we must what?
					//$params = json_decode(stripslashes($config['params']));
					$params = json_decode($config['params']);
					if($params && $config['params'] != '')
					{
						//$params = $this->objectToString($params);
						$params = PagesAndItemsHelper::objectToString($params);
						$this->params = new JParameter($params,$path);
					}
					else
					{
						$this->params = new JParameter('',$path);
					}
				}
			}
			else
			{
				if ($config['params'] instanceof JRegistry)
				{
					$this->params = $config['params'];
				}
				else
				{
					$this->params = new JRegistry;
					//$this->params->loadJSON($config['params']); //is deprecated
					$this->params->loadString($config['params']); //format is as standard JSON
					
					/*
					in future Joomla will remove JParameter
					so we can use in Future JRegistry
					only for params->get...
					not for render
					for render : JModelForm JForm
					but the xml file content is more different so we can not use this at this moment?
					if we want use in J1.5 JParameter and in J1.6 JRegistry, JModelForm, JForm
					we must have in the xml-file:

					<params>
						<param name="no_lines_to_breaks" type="radio" default="0" label="COM_PAGESANDITEMS_NO_LINES_TO_BREAKS">
							<option value="0">No</option>
							<option value="1">Yes</option>
						</param>
						<param name="limit_characters" type="text" default="0" label="COM_PAGESANDITEMS_LIMIT_CHARACTERS" description="COM_PAGESANDITEMS_LIMIT_CHARACTERS2">
						</param>
					</params>
					<config>
						<fields name="params">
							<fieldset name="basic">
								<field name="no_lines_to_breaks" type="radio" default="0" label="COM_PAGESANDITEMS_NO_LINES_TO_BREAKS" >
									<option value="0">JNo</option>
									<option value="1">JYES</option>
								</field>
							</fieldset>

							<fieldset name="advanced">
								<field name="field2" type="text" description="Example field 2" label="Example field 2" />
							</fieldset>
						</fields>
					</config>
					*/
				}
			}
		}


		// Set the automatic language loading
		/*
		will not run by itemtypes why?
		*/
		/*
		if (!isset($config['language']) || $config['language'])
		{
			$events = array_diff(get_class_methods($this), get_class_methods('PagesAndItemsExtension'));

			if($this->_type == 'itemtype' || $this->_type == 'fieldtype')
			{

			}
			foreach($events as $event)
			{
				$method = array('event' => $event, 'handler' => array($this, 'onFireEvent'));
				if($this->_type == 'itemtype') // || $this->_type == 'fieldtype' || $this->_type == 'button')
				{


				}
				$subject->attach($method);
			}
		}
		*/
		/*
		*/

		parent::__construct($subject);
	}



	function saveParams()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		//$option = JRequest::getVar('option');
		$db = & JFactory::getDBO();
		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
		$row =& JTable::getInstance('piextension','PagesAndItemsTable');

		//$task = $this->getTask();
		$task = JRequest::getVar('sub_task');
		$row->load($this->_extension_id);
		$client = JRequest::getWord( 'filter_client', 'site' );
		if (!$row->bind(JRequest::get('post')))
		{
			JError::raiseError(500, $row->getError() );
		}
		if (!$row->check()) {
			JError::raiseError(500, $row->getError() );
		}

		if (!$row->store()) {
			JError::raiseError(500, $row->getError() );
		}
		$row->checkin();

		switch ( $task )
		{
			case 'apply':
				$msg = JText::sprintf( 'Successfully Saved changes to Plugin', $row->name );
				return $msg;
				//$this->setRedirect( 'index.php?option=com_pagesanditems&view=manageextension&client='. $client .'&task=edit&cid[]='. $row->extension_id, $msg );
				break;

			case 'save':
			default:
				$msg = JText::sprintf( 'Successfully Saved Plugin', $row->name );
				return $msg;
				//$this->setRedirect( 'index.php?option=com_pagesanditems&view=manage&client='. $client, $msg );
				break;
		}
	}

	function renderParams()
	{
		$html = '';
		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
		$row 	=& JTable::getInstance('piextension','PagesAndItemsTable');

		// load the row from the db table
		$row->load( $this->_extension_id );

		// fail if checked out not by 'me'
		$user =& JFactory::getUser();
		if ($row->isCheckedOut( $user->get('id') ))
		{
			$msg = JText::sprintf( 'DESCBEINGEDITTED', JText::_( 'The plugin' ), $row->title );
			$html = $msg;
			//$this->setRedirect( 'index.php?option='. $option .'&client='. $client, $msg, 'error' );
			//return false;
		}
		else
		{
			$params = $this->params;
			if($params)
			{
				jimport('joomla.html.pane');
				$pane = &JPane::getInstance('sliders', array('allowAllClose' => true));
				$html .= $pane->startPane('plugin-pane');
					$html .= $pane->startPanel(JText :: _('Plugin Parameters'), 'param-page');
					if($output = $params->render('params')) :
						$html .= $output;
					else :
						$html .= "<div style=\"text-align: center; padding: 5px; \">".JText::_('There are no parameters for this item')."</div>";
				endif;
				$html .= $pane->endPanel();
				if ($params->getNumParams('advanced'))
				{
					$html .= $pane->startPanel(JText :: _('Advanced Parameters'), "advanced-page");
					if($output = $params->render('params', 'advanced')) :
						$html .= $output;
					else :
						$html .= "<div  style=\"text-align: center; padding: 5px; \">".JText::_('There are no advanced parameters for this item')."</div>";
					endif;
					$html .= $pane->endPanel();
				}
				if ($params->getNumParams('legacy'))
				{
					$html .= $pane->startPanel(JText :: _('Legacy Parameters'), "legacy-page");
					if($output = $params->render('params', 'legacy')) :
						$html .= $output;
					else :
						$html .= "<div  style=\"text-align: center; padding: 5px; \">".JText::_('There are no legacy parameters for this item')."</div>";
					endif;
					$html .= $pane->endPanel();
				}
				$html .= $pane->endPane();
			}
			else
			{
				$html .= "<div style=\"text-align: center; padding: 5px; \">".JText::_('There are no parameters for this item')."</div>";
			}
		}
		return $html;
	}


	function onDetach($type)
	{

		//if($this->_name != $type) //??
		if($this->_type == $type)
		{
			$this->_subject->detach($this);
		}
		return true;
	}

	//ms: remove?
	/*
	public function setHelper()
	{
		if(!$this->helper)
		{
			require_once(dirname(__FILE__).'/../../helpers'.DS.'pagesanditems.php');
			$this->helper = new PagesAndItemsHelper();
		}
		//		PagesAndItemsHelper::addSubmenu(JRequest::getWord('view', 'pagesanditems'));
	}
	*/
	//ms: remove?
	/*
	public function setModel()
	{
		if(!$this->model)
		{
			//require_once(dirname(__FILE__).'/../../models'.DS.'pagesanditems.php');
			//$this->model = new PagesAndItemsModelPagesAndItems();
			jimport( 'joomla.application.component.model' );
			//JModel::addIncludePath($path);
			// Clean the model name
			$modelName	 = preg_replace( '/[^A-Z0-9_]/i', '', 'Base' );
			$classPrefix = preg_replace( '/[^A-Z0-9_]/i', '', 'PagesAndItemsModel' );

			if ($model =& JModel::getInstance($modelName, $classPrefix, array()))
			{
				$this->model = $model;
			}
		}
	}
	*/
	public function getConfig()
	{
		if(!$this->pi_config)
		{
			$this->setConfig();
		}
		return $this->pi_config;
	}

	public function setConfig()
	{
		if(!$this->pi_config)
		{
			require_once(dirname(__FILE__).'/../../helpers/pagesanditems.php');
			$this->pi_config = PagesAndItemsHelper::getConfig();
			/*
			jimport( 'joomla.application.component.model' );
			//JModel::addIncludePath($path);
			// Clean the model name
			$modelName	 = preg_replace( '/[^A-Z0-9_]/i', '', 'PagesAndItems' );
			$classPrefix = preg_replace( '/[^A-Z0-9_]/i', '', 'PagesAndItemsModel' );

			if ($model =& JModel::getInstance($modelName, $classPrefix, array()))
			{
				$this->pi_config = $model->getConfig();
				$this->model = $model;
			}
			*/
		}
	}

	//ms: remove?
	/*
	public function setController()
	{
		if(!$this->controller)
		{
			//load the base controller
			$className = 'PagesAndItemsController';
			if (!class_exists($className))
			{
				//TODO for J1.6 rename com_pagesanditems to com_pagesanditems
				require_once(dirname(__FILE__).'/../../controller.php');
			}
			$this->controller = new $className();
		}
	}
	*/
	public function onFireEvent()
	{
		$this->loadLanguage(); //, JPATH_ADMINISTRATOR);
	}



	public function getRequired_version($extension = null)//$type = null, $folder = null, $extension = null)
	{
		if($extension && $this->_name == $extension)
		{
			return $this->_required_version;
			return true;
		}
		return -1;
	}

	public function onGetRequired_version(&$required_version,$extension = null)//$type = null, $folder = null, $extension = null)
	{
		if($extension && $this->_name == $extension)
		{
			$required_version = $this->_required_version ? $this->_required_version : 0;
			return true;
		}
		return false;
	}


	public function getParams()//$type = null, $folder = null, $extension = null)
	{
		return $this->params;
	}

	public function onGetParams(&$params, $extension = null)//$type = null, $folder = null, $extension = null)
	{
		if($extension && $this->_name == $extension)
		{
			$params = $this->params;
			return true;
		}
		return false;
		//$this->loadLanguage(null, JPATH_ADMINISTRATOR);
	}


	public function onGetId(&$id, $extension = null)//$type = null, $folder = null, $extension = null)
	{

		//
		if($extension && $this->_name == $extension)
		{
			$id = $this->_extension_id; //_extension_id
			return true; //$this->_extension_id;
		}
		return false;
		//$this->loadLanguage(null, JPATH_ADMINISTRATOR);
	}
	

	public function onGetFolder(&$folder,$extension = '') //, $basePath = JPATH_ADMINISTRATOR)
	{
		if($extension != $this->_name)
		{
			return false;
		}
		$path = realpath(dirname(__FILE__).'/../../extensions');
		if($this->_folder)
		{
			$extension_folder = $this->_folder;
			$path = str_replace('/',DS,$path.DS.$this->_type.'s'.DS.$extension_folder);
		}
		else
		{
			$path = str_replace('/',DS,$path.DS.$this->_type.'s');
		}
		
		$folder = $path.DS.$this->_name;

		$pathExtensions = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'extensions');

		return true;

	}


	public function onAfterParamsSave(&$params, $extension = null)//$type = null, $folder = null, $extension = null)
	{
		if($extension && $this->_name == $extension && $this->_type == 'itemtype')
		{
			$path = realpath(dirname(__FILE__));
			require_once($path.DS.'managerhelper.php');
			ExtensionManagerHelper::importExtension(null, null,true,null,true);
			$dispatcher = &JDispatcher::getInstance();
			$dispatcher->trigger('onItemtypeParamsSave', array($this->params,$extension));

			/*
			//here we will tell the PagesAndItemsHelperLanguage params is save

			$path = realpath(dirname(__FILE__).DS.'..'.DS.'..');
			require_once($path.DS.'helpers'.DS.'language.php');
			PagesAndItemsHelperLanguage::languageItemtypeParamsSave($this->params,$extension);
			*/
			//$translate_html = PagesAndItemsHelperLanguage::languageDisplayItemtypeItemEdit($item_type,$item_id,'pi_subitem_image_gallery',$this->params, $row->id);

			//$params = $this->params;
			/*
			here we will trigger pi_fish
			*/
			/*
			$path = realpath(dirname(__FILE__).DS.'..'.DS.'..');
			require_once($path.DS.'includes'.DS.'extensions'.DS.'fieldtypehelper.php');
			ExtensionFieldtypeHelper::importExtension(null, 'pi_fish',true,null,true);
			$dispatcher = &JDispatcher::getInstance();
			$dispatcher->trigger('onPi_FishItemTypeParamsSave', array($this->params,$this->_name));
			return true;
			*/
		}
		return false;

	}

	public function XonGetParams() //&$params,$type = null, $folder = null, $extension = null)
	{

		$params = $this->params;
		return $params;
		//return true;
		//$this->loadLanguage(null, JPATH_ADMINISTRATOR);
	}

	/**
	 * Loads the plugin language file
	 * first will load the language in Pages and Items Config if not avaible the joomla default language is load
	 *
	 * @access	public
	 * @param	string	$extension	The extension for which a language file should be loaded
	 * @param	string	$basePath	The basepath to use
	 * @return	boolean	True, if the file has successfully loaded.
	 * @since
	 */
	public function loadLanguage($extension = '') //, $basePath = JPATH_ADMINISTRATOR)
	{
		
		PagesAndItemsHelper::loadExtensionLanguage($this->_name,$this->_type,$this->_folder);
		return;

		if (empty($extension))
		{
			if($this->_folder)
			{
				$extension_folder = str_replace('/','_',$this->_folder);
				$prefix = $this->_type.'_'.$extension_folder;//.DS;

			}
			else
			{
				$prefix = $this->_type;
			}
			$extension = 'pi_extension_'.$prefix.'_'.$this->_name;
			//if we have long names make it short only for extensions dir
			$extensionShort = 'pi_extension_'.$this->_name;
		}

		$lang = &JFactory::getLanguage();
		$defaultLang = $lang->getDefault();
		if($defaultLang != 'en-GB')
		{
			$defaultLang = 'en-GB';
		}

		$path = realpath(dirname(__FILE__).'/../../extensions');

		if($this->_folder)
		{
			$extension_folder = $this->_folder;
			$path = str_replace('/',DS,$path.DS.$this->_type.'s'.DS.$extension_folder);
		}
		else
		{
			$path = str_replace('/',DS,$path.DS.$this->_type.'s');
		}


		//$config = $this->getConfig();
		//$defaultLangPI = $config['language'];
		/*
		if(defined('COM_PAGESANDITEMS_DEFAULT_LANG'))
		{
			$defaultLangPI = COM_PAGESANDITEMS_DEFAULT_LANG;
		}
		else
		{
			$path2 = realpath(dirname(__FILE__).DS.'..'.DS.'..');
			require_once($path2.DS.'helpers'.DS.'pagesanditems.php');
			PagesAndItemsHelper::getConfig();
			if(defined('COM_PAGESANDITEMS_DEFAULT_LANG'))
			{
				$defaultLangPI = COM_PAGESANDITEMS_DEFAULT_LANG;
			}
			else
			{
				$defaultLangPI = $defaultLang;
			}
		}
		*/


		//ms: where we have the extensions languages?
		// in my mind the best place was extensions/language
		$pathExtensions = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'extensions');

		/*
		ms: TODO only $pathExtensions in $lang->load
		*/
		$language =
		/*
			$lang->load(strtolower($extensionShort), $path.DS.$this->_name, null, false, false)
		||	$lang->load(strtolower($extensionShort), $path.DS.$this->_name, $defaultLang, false, false)
		||
		*/
		/*
			$lang->load(strtolower($extension), $path.DS.$this->_name, $defaultLangPI, false) //, false)
		||	$lang->load(strtolower($extension), $pathExtensions, $defaultLangPI, false) //ms: add)
		||	$lang->load(strtolower($extension), $basePath, $defaultLangPI, false) //, false)
		||	$lang->load(strtolower($extensionShort), $path.DS.$this->_name, $defaultLangPI, false) //, false)
		*/
			$lang->load(strtolower($extension), $path.DS.$this->_name, null, false) //, false)
		||	$lang->load(strtolower($extension), $pathExtensions, null, false) //ms: add)
		||	$lang->load(strtolower($extension), $basePath, null, false) //, false)
		||	$lang->load(strtolower($extensionShort), $path.DS.$this->_name, null, false) //, false)

		||	$lang->load(strtolower($extension), $path.DS.$this->_name, $defaultLang, false) //, false)
		||	$lang->load(strtolower($extension), $pathExtensions, $defaultLang, false) //ms: add)
		||	$lang->load(strtolower($extension), $basePath, $defaultLang, false) //, false)
		||	$lang->load(strtolower($extensionShort), $path.DS.$this->_name, $defaultLang, false) //, false)
		/*
		||	$lang->load(strtolower($extensionShort), $path.DS.$this->_name.DS.'short', null, false) //, false)
		||	$lang->load(strtolower($extensionShort), $path.DS.$this->_name.DS.'short', $defaultLang, false) //, false)
		*/

		;

		/*
		if($this->_name == 'display_template')

		*/


		return $language;

	}

	protected function _getValueAsINI($value)
	{
		// Initialize variables.
		$string = '';

		switch (gettype($value)) {
			case 'integer':
			case 'double':
				$string = $value;
				break;

			case 'boolean':
				$string = $value ? 'true' : 'false';
				break;

			case 'string':
				// Sanitize any CRLF characters..
				$string = '"'.str_replace(array("\r\n", "\n"), '\\n', $value).'"';
				break;
		}

		return $string;
	}

	public function objectToString($object=null) //, $options = array())
	{
		// Initialize variables.
		$local  = array();
		$global = array();

		if(count(get_object_vars($object)))
		{
			// Iterate over the object to set the properties.
			foreach (get_object_vars($object) as $key => $value)
			{
				// If the value is an object then we need to put it in a local section.
				if (is_object($value)) {
					// Add the section line.
					$local[] = '';
					$local[] = '['.$key.']';

					// Add the properties for this section.
					foreach (get_object_vars($value) as $k => $v)
					{
						$local[] = $k.'='.$this->_getValueAsINI($v);
					}
				}
				else
				{
					// Not in a section so add the property to the global array.
				$global[] = $key.'='.$this->_getValueAsINI($value);
				}
			}
		}
		return implode("\n", array_merge($global, $local));
	}


/*

must go to fieldtype.php?
*/

	function display_field($field_name, $field_content, $width_left='20', $width_right='70')
	{
		$html = '<div class="pi_form_wrapper">';
		$html .= '<div class="pi_width'.$width_left.'">';
		$has_only_star = strpos($field_name, 'img src=');
		if($field_name!='' && $has_only_star!=1){
			$html .= $field_name.':';
		}elseif($has_only_star==1){
			$html .= $field_name;
		}else{
			$html .= '&nbsp;';
		}
		$html .= '</div>';
		$html .= '<div class="pi_width'.$width_right.'">';
		$html .= $field_content;
		$html .= '</div>';
		$html .= '</div>';
		return $html;
	}

	function display_field_description($field_params){
		//$field_name = strtolower(JText::_('COM_PAGESANDITEMS_DESCRIPTION'));
		//changed on request of Micha for German chapital letters.
		$field_name = JText::_('COM_PAGESANDITEMS_DESCRIPTION');
		$field_content = '<input type="text" class="width200" value="'.$field_params['description'].'" name="field_params[description]" />';
		return $this->display_field($field_name, $field_content);
	}

	function display_field_validation($field_params){
		$field_name = JText::_('COM_PAGESANDITEMS_VALIDATION');
		$field_content = '<input type="checkbox" class="checkbox"';
		//if($field_params['validation']){
		if($this->check_if_field_param_is_present($field_params, 'validation')){
			if($field_params['validation']){
				$field_content .= ' checked="checked"';
			}
		}
		$field_content .= 'name="field_params[validation]" value="not_empty" /> '.JText::_('COM_PAGESANDITEMS_NOT_EMPTY');
		return $this->display_field($field_name, $field_content);
	}

	function check_if_field_param_is_present($field_params, $field_param){
		$param_is_present = false;
		for($n = 0; $n < count($field_params); $n++){
			$row = each($field_params);
			if($row['key']==$field_param){
				$param_is_present = true;
				break;
			}
		}
		return $param_is_present;
	}

	function check_if_plugin_lang_var_is_present($pi_lang_plugin, $var){
		$var_is_present = false;
		for($n = 0; $n < count($pi_lang_plugin); $n++){
			$row = each($pi_lang_plugin);
			if($row['key']==$var){
				$var_is_present = true;
				break;
			}
		}
		return $var_is_present;
	}

	function display_field_validation_message($field_params){
		$field_content = '<input type="text" class="width200" value="'.$field_params['alert_message'].'" name="field_params[alert_message]" />';
		return $this->display_field(JText::_('COM_PAGESANDITEMS_VALIDATION_ALERT_MESSAGE'), $field_content);
	}

	function display_field_default_value($field_params){
		$field_content = '<input type="text" class="width200" value="'.$field_params['default_value'].'" name="field_params[default_value]" />';
		return $this->display_field(JText::_('COM_PAGESANDITEMS_DEFAULT_VALUE'), $field_content);
	}

	function get_field_value($values_string, $property){
		$values_array = explode('[;-)# ]', $values_string);
		$property = substr($property,1);
		$html = '';
		foreach($values_array as $value_set){
			if(strpos($value_set, $property)){
				$temp = explode('-=-', $value_set);
				$html = $temp[1];
				break;
			}
		}
		return $html;
	}

	function get_field_param($values_string, $property)
	{
		return $this->get_field_value($values_string, $property);
	}

	function phpWrapper($content)
	{

		$database = JFactory::getDBO();
		ob_start();
		// eval ?
		eval("?>" . $content);
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	function get_var($name, $default = null, $hash = 'default', $type = 'none', $mask = 0)
	{
		//make sure there is no $type
		if($type!='none' && $type!=''){
			exit('don\'t use $type, it won\'t work in older versions');
		}
		$var = JRequest::getVar($name, $default, $hash, $type, $mask);
		return $var;
	}

}
