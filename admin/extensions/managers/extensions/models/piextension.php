<?php
/**
* @version		2.0.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2011 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// No direct access.
defined('_JEXEC') or die;


/**

 */
 
 
jimport('joomla.application.component.modeladmin');


class PagesAndItemsModelPiextension extends JModelAdmin
{
		
			/**
	 * Abstract method for getting the form from the model.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
		/*
		public function getForm($data = array(), $loadData = true)
		{
			
		}
		*/
		/*
		Fatal error: Class PagesAndItemsModelManageextensionBase contains 1 abstract method and must therefore be declared abstract or implement the remaining methods (JModelForm::getForm) in U:\web\In Arbeit\___Joomlas2Go-S1.6.0-FP3.0.5de-J1.6_NewPI\htdocs\joomlas2Go\administrator\components\com_pagesanditems\models\manageextension.php on line 40
		
		Fatal error: Class PagesAndItemsModelManageextensionBase contains 1 abstract method and must therefore be declared abstract or implement the remaining methods (PagesAndItemsModelManageextensionBase::getForm) in U:\web\In Arbeit\___Joomlas2Go-S1.6.0-FP3.0.5de-J1.6_NewPI\htdocs\joomlas2Go\administrator\components\com_pagesanditems\models\manageextension.php on line 55
		
		*/



	/*
	BEGIN adopt from J1.6 
	must TEST
	*/
	protected $_cache;

	function getLanguageItems($item)
	{
		/*
		here we search over 
		$tag = $item->element;
		$pathComponent = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS);
		// load up the extension details
		$languageItems = array();
		$pathLanguage = $pathComponent.DS.'extensions'.DS.'language'.DS.$tag.DS;
		$files = JFolder::files($pathLanguage,'.xml$',false,true);
		if(count($files))
		{
			foreach($files as $file)
			{
				$xml = simplexml_load_file($file);
				$type = (string)$xml->type;
				if($type && $type !='' && $type =='pilanguage')
				{
					//ok here the manifest
					
					if ( $xml->administration->languages  && count($xml->administration->languages->children()) )
					{
						$languages = $xml->administration->languages->children();
						foreach ($languages as $language) 
						{
							$path = JPATH_ADMINISTRATOR.DS.'language'.DS.$tag.DS.$language;
							$languageItem = $this->getLanguageItem($path);
							$languageItem->client_id = 1;
							$languageItems[] = $languageItem;
						}
					}
					
					if ( $xml->languages  && count($xml->languages->children()) )
					{
						$languages = $xml->languages->children();
						foreach ($languages as $language) 
						{
							$path = JPATH_SITE.DS.'language'.DS.$tag.DS.$language;
							$languageItem = $this->getLanguageItem($path);
							$languageItem->client_id = 0;
							$languageItems[] = $languageItem;
						}
					}
					
					break;
				}
			}
		}
		$files = JFolder::files($pathLanguage,'.ini$',false,true);
		if(count($files))
		{
			foreach($files as $file)
			{
				$path = $file;
				$languageItem = $this->getLanguageItem($path);
				$languageItem->client_id = 1;
				$languageItems[] = $languageItem;
			}
		}
		return $languageItems;
		*/
	}

	function getLanguageItem($path)
	{
		$item = null;
		if (JFile::exists($path)) 
		{	
					$stream = new JStream();
					$stream->open($path);
					$begin = $stream->read(4);
					$bom = strtolower(bin2hex($begin));
					if ($bom == '0000feff') 
					{
						$item->bom = 'UTF-32 BE';
					}
					else if ($bom == 'feff0000') 
					{
						$item->bom = 'UTF-32 LE';
					}
					else if (substr($bom, 0, 4) == 'feff') 
					{
						$item->bom = 'UTF-16 BE';
					}
					else if (substr($bom, 0, 4) == 'fffe') 
					{
						$item->bom = 'UTF-16 LE';
					}
					$stream->seek(0);
					$continue = true;
					$lineNumber = 0;
					while (!$stream->eof()) 
					{
						$line = $stream->gets();
						$lineNumber++;
						if ($line[0] == '#' || $line[0] == ';') 
						{
							if (preg_match('/^(#|;).*(\$Id.*\$)/', $line, $matches)) 
							{
								$item->svn = $matches[2];
							}
							elseif (preg_match('/(#|;)\s*@?(\pL+):?.*/', $line, $matches)) 
							
							{
								switch (strtolower($matches[2])) 
								{
								case 'note':
									preg_match('/(#|;)\s*@?(\pL+):?\s+(.*)/', $line, $matches2);
									$item->complete = $item->complete || strtolower($matches2[3]) == 'complete';
								break;
								case 'version':
									preg_match('/(#|;)\s*@?(\pL+):?\s+(.*)/', $line, $matches2);
									$item->version = $matches2[3];
								break;
								case 'desc':
								case 'description':
									preg_match('/(#|;)\s*@?(\pL+):?\s+(.*)/', $line, $matches2);
									$item->description = $matches2[3];
								break;
								case 'date':
									preg_match('/(#|;)\s*@?(\pL+):?\s+(.*)/', $line, $matches2);
									$item->creationdate = $matches2[3];
								break;
								case 'author':
									preg_match('/(#|;)\s*@?(\pL+):?\s+(.*)/', $line, $matches2);
									$item->author = $matches2[3];
								break;
								case 'copyright':
									preg_match('/(#|;)\s*@?(\pL+):?\s+(.*)/', $line, $matches2);
									if (empty($item->maincopyright)) 
									{
										$item->maincopyright = $matches2[3];
									}
									else
									{
										$item->additionalcopyright[] = $matches2[3];
									}
								break;
								case 'license':
									preg_match('/(#|;)\s*@?(\pL+):?\s+(.*)/', $line, $matches2);
									$item->license = $matches2[3];
								break;
								case 'package':
									preg_match('/(#|;)\s*@?(\pL+):?\s+(.*)/', $line, $matches2);
									$item->package = $matches2[3];
								break;
								case 'subpackage':
									preg_match('/(#|;)\s*@?(\pL+):?\s+(.*)/', $line, $matches2);
									$item->subpackage = $matches2[3];
								break;
								case 'link':
								break;
								default:
									if (empty($item->author)) 
									{
										preg_match('/(#|;)\s*(.*)/', $line, $matches2);
										$item->author = $matches2[2];
									}
								break;
								}
							}
						}
						else
						{
							break;
						}
					}
					while (!$stream->eof()) 
					{
						$line = $stream->gets();
						$lineNumber++;
						if (!preg_match('/^(|(\[[^\]]*\])|([A-Z][A-Z0-9_\-]*\s*=(\s*(("[^"]*")|(_QQ_)))+))\s*(;.*)?$/', $line)) 
						{
							$item->error[] = $lineNumber;
						}
					}
					$stream->close();
				}
		return $item;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// The folder and element vars are passed when saving the form.
		if (empty($data)) 
		{
			$item		= $this->getItem();
			$folder	= $item->folder;
			$element	= $item->element;
			$type		= $item->type;
		}
		else
		{
			$folder	= JArrayHelper::getValue($data, 'folder', '', 'cmd');
			$element	= JArrayHelper::getValue($data, 'element', '', 'cmd');
			$type		= JArrayHelper::getValue($data, 'type', '', 'cmd');
		}
		
		//echo 'folder: '.$folder;
		// These variables are used to add data from the plugin XML files.
		$this->setState('item.folder',	$folder);
		$this->setState('item.element',	$element);
		$this->setState('item.type',	$type);

		// Get the form.
		$form = $this->loadForm('com_pagesanditems.extension', 'extension', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			//print_r('xxxx');
			return false;
		}

		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data)) 
		{
			// Disable fields for display.
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('enabled', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('enabled', 'filter', 'unset');
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_pagesanditems.edit.extension.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('extension.extension_id');
		//echo '$pk: '.$pk.' :$pk,';
		//echo '$pk state : '.$this->getState('extension.extension_id').' :$pk state,';
		//echo 'X';
		//$this->_cache[$pk]
		if (!isset($this->_cache[$pk])) 
		{
			//echo 'XX';
			$false	= false;
			
			// Get a row instance.
			$table = $this->getTable();

			// Attempt to load the row.
			$return = $table->load($pk);

			// Check for a table object error.
			if ($return === false && $table->getError()) 
			{
				$this->setError($table->getError());
				return $false;
			}

			// Convert to the JObject before adding other data.
			$properties = $table->getProperties(1);
			$this->_cache[$pk] = JArrayHelper::toObject($properties, 'JObject');

			// Convert the params field to an array.
			$registry = new JRegistry;
			$registry->loadJSON($table->params);
			$this->_cache[$pk]->params = $registry->toArray();

			// Get the plugin XML.
			//$path = JPath::clean(JPATH_PLUGINS.'/'.$table->folder.'/'.$table->element.'/'.$table->element.'.xml');
			//$path = realpath(dirname(__FILE__).'/../extensions');
			$path = realpath(dirname(__FILE__).'/../../../');
			if($table->folder)
			{
				$extension_folder = $table->folder;
				$path = str_replace('/',DS,$path.DS.$table->type.'s'.DS.$extension_folder);
			}
			else
			{
				$path = str_replace('/',DS,$path.DS.$table->type.'s');
			}
			$path = JPath::clean($path.DS.$table->element.DS.$table->element.'.xml');
			//dump($path);
			if (file_exists($path)) {
				$this->_cache[$pk]->xml = JFactory::getXML($path);
			} else {
				$this->_cache[$pk]->xml = null;
			}
			//$this->setState('extension.extension_id',$pk);
		}
		//$this->_cache[$pk]->test = 'test';
		return $this->_cache[$pk];
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 //'extension','PagesAndItemsTable'
	*/
	public function getTable($type = 'piextension', $prefix = 'PagesAndItemsTable', $config = array())
	{
		
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState()
	{
		// Execute the parent method.
		parent::populateState();

		$app = JFactory::getApplication('administrator');

		// Load the User state.
		$pk = (int) JRequest::getInt('extension_id');
		$this->setState('extension.extension_id', $pk);
	}

	/**
	 * @param	object	A form object.
	 * @param	mixed	The data expected for the form.
	 * @return	mixed	True if successful.
	 * @throws	Exception if there is an error in the form event.
	 * @since	1.6
	 */
	protected function preprocessForm(JForm $form, $data, $group = '')
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		// Initialise variables.
		$folder		= $this->getState('item.folder');
		$element	= $this->getState('item.element');
		$type	= $this->getState('item.type');
		$lang		= JFactory::getLanguage();
		$client		= JApplicationHelper::getClientInfo(0);

		if (empty($folder) && empty($element)) 
		{
			//echo 'XX';
			$app = JFactory::getApplication();
			//$app->redirect(JRoute::_('index.php?option=com_pagesanditems&view=manage',false));
		}
		// Try 1.6 format: /plugins/folder/element/element.xml
		$path = realpath(dirname(__FILE__).'/../../../../extensions');
		$pathExtensions = $path;
		//print_r($path);
		//print_r(dirname(__FILE__).'/../../../../extensions');
		if($folder)
		{
			$extension_folder = $folder;
			$path = str_replace('/',DS,$path.DS.$type.'s'.DS.$extension_folder);
		}
		else
		{
			if($type == 'language')
			{
				$path = str_replace('/',DS,$path.DS.$type);
			}
			else
			{
				$path = str_replace('/',DS,$path.DS.$type.'s');
			}
			
		}
		//$path = $path.DS.$element.DS.$element.'.xml';
		
		$formFile = JPath::clean($path.DS.$element.DS.$element.'.xml'); //JPATH_PLUGINS.'/'.$folder.'/'.$element.'/'.$element.'.xml');

		//dump($formFile);
		if (!file_exists($formFile) && $type != 'language') 
		{
			if (!file_exists($formFile)) 
			{
				throw new Exception(JText::sprintf('COM_PLUGINS_ERROR_FILE_NOT_FOUND', $element.'.xml'));
				return false;
			}
		}
	
		if($folder)
		{
			$extension_folder = str_replace('/','_',$folder);
			$prefix = $type.'_'.$extension_folder;
		}
		else
		{
			$prefix = $type;
		}
		$extension = 'pi_extension_'.$prefix.'_'.$element;
		//if we have long names make it short only for extensions dir
		$extensionShort = 'pi_extension_'.$element;

		$defaultLang = $lang->getDefault();
		if($defaultLang != 'en-GB')
		{
			$defaultLang = 'en-GB';
		}
		
		/*
		if(defined('COM_PAGESANDITEMS_DEFAULT_LANG'))
		{
			$defaultLangPI = COM_PAGESANDITEMS_DEFAULT_LANG;
		}
		else
		{
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
		$basePath = JPATH_ADMINISTRATOR;
			$lang->load(strtolower($extension), $path.DS.$element, null, false)
		||	$lang->load(strtolower($extension), $basePath, null, false)
		||	$lang->load(strtolower($extension), $pathExtensions, null, false)
		||	$lang->load(strtolower($extensionShort), $path.DS.$element, null, false)
		||	$lang->load(strtolower($extension), $path.DS.$element, $defaultLang, false)
		||	$lang->load(strtolower($extension), $basePath, $defaultLang, false)
		||	$lang->load(strtolower($extension), $pathExtensions, $defaultLang, false)
		||	$lang->load(strtolower($extensionShort), $path.DS.$element, $defaultLang, false);

		/*
			$lang->load(strtolower($extension), $path.DS.$element, $defaultLangPI, false)
		||	$lang->load(strtolower($extension), $basePath, $defaultLangPI, false)
		||	$lang->load(strtolower($extensionShort), $path.DS.$element, $defaultLangPI, false)
		||	$lang->load(strtolower($extension), $path.DS.$element, $defaultLang, false)
		||	$lang->load(strtolower($extension), $basePath, $defaultLang, false)
		||	$lang->load(strtolower($extensionShort), $path.DS.$element, $defaultLang, false);
		*/

/*




		// Load the core and/or local language file(s).
			$lang->load('plg_'.$folder.'_'.$element, JPATH_ADMINISTRATOR, null, false, false)
		||	$lang->load('plg_'.$folder.'_'.$element, JPATH_PLUGINS.'/'.$folder.'/'.$element, null, false, false)
		||	$lang->load('plg_'.$folder.'_'.$element, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
		||	$lang->load('plg_'.$folder.'_'.$element, JPATH_PLUGINS.'/'.$folder.'/'.$element, $lang->getDefault(), false, false);
		*/
		if (file_exists($formFile)) 
		{
			// Get the plugin form.
			if (!$form->loadFile($formFile, false, '//config')) 
			{
				throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
			}
		}

		// Attempt to load the xml file.
		if (!$xml = simplexml_load_file($formFile)) 
		{
			if ($type != 'language') 
			{
				throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
			}
		}
		/*
		// Get the help data from the XML file if present.
		$help = $xml->xpath('/extension/help');
		if (!empty($help)) {
			$helpKey = trim((string) $help[0]['key']);
			$helpURL = trim((string) $help[0]['url']);

			$this->helpKey = $helpKey ? $helpKey : $this->helpKey;
			$this->helpURL = $helpURL ? $helpURL : $this->helpURL;
		}
*/
		// Trigger the default form events.
		parent::preprocessForm($form, $data, $group);
		
	}
	/*
	END adopt from J1.6 
	must TEST
	*/

	/**
	 * Enable/Disable an extension.
	 *
	 * @return	boolean True on success
	 * @since	1.5
	 */
	function publish($id , $value = 1)
	{
		$result = true;

		// Get a database connector
		$db = JFactory::getDBO();

		// Get a table object for the extension type
		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
		$table =& JTable::getInstance('piextension','PagesAndItemsTable');

		$table->load($id);
		$notenable = (($table->type == 'itemtype' && ($table->element == 'content' || $table->element == 'text')) || ($table->type == 'pagetype' && $table->version == 'integrated') || $table->protected);

		if(!$notenable)
		{
			$table->enabled = $value;
		}
		//$table->enabled = $value;
		/*
		if(!$table->protected)
		{
			$table->enabled = $value;
		}
		*/
		if (!$table->store()) 
		{
			$this->setError($table->getError());
			$result = false;
		}

		return $result;

	}

	/**
	 * Refreshes the cached manifest information for an extension.
	 *
	 * @param	int		extension identifier (key in #__extensions)
	 * @return	boolean	result of refresh
	 * @since	1.6
	 */
	function refresh($eid)
	{
		if (!is_array($eid)) 
		{
			$eid = array($eid => 0);
		}

		// Get a database connector
		$db = JFactory::getDBO();

		// Get an installer object for the extension type
		//jimport('joomla.installer.installer');
		//$installer = JInstaller::getInstance();
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'extensions'.DS.'installer.php');
		$installer = PagesAndItemsInstaller::getInstance();
		
		$row = JTable::getInstance('extension');
		$result = 0;

		// refresh the chosen extensions
		foreach($eid as $id) 
		{
			$result |= $installer->refreshManifestCache($id); //refreshManifestCache($id);
		}
		return $result;
	}



	/**
	 * Method to get the database query
	 *
	 * @return	JDatabaseQuery	The database query
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		$enabled= $this->getState('filter.enabled');
		$type = $this->getState('filter.type');
		$client = $this->getState('filter.client_id');
		$group = $this->getState('filter.folder');
		$hideprotected = $this->getState('filter.hideprotected');
		$query = new JDatabaseQuery;
		$query->select('*');
		$query->from('#__pi_extensions');
		$query->where('state=0');
		if ($hideprotected) {
			$query->where('protected!=1');
		}
		if ($enabled != '') {
			$query->where('enabled=' . intval($enabled));
		}
		if ($type) {
			$query->where('type=' . $this->_db->Quote($type));
		}
		if ($client != '') {
			$query->where('client_id=' . intval($client));
		}
		if ($group != '' && in_array($type, array('plugin', 'library', ''))) {

			$query->where('folder=' . $this->_db->Quote($group == '*' ? '' : $group));
		}

		// Filter by search in id
		$search = $this->getState('filter.search');
		if (!empty($search) && stripos($search, 'id:') === 0) {
			$query->where('extension_id = '.(int) substr($search, 3));
		}

		return $query;
	}
}
