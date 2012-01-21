<?php
/**
* @version		2.1.3
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// No direct access
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class PagesAndItemsHelperFile
{
					/*
				Fehlermeldungen erkl‰rt

Seit PHP 4.2.0 gibt PHP zusammen mit dem Datei-Array entsprechende Fehlermeldungen. Die Fehlermeldung befindet sich im Segment ['error'] des Datei-Arrays, welches w‰hrend des Hochladens der Datei erstellt wird. In anderen Worten kann der Fehler in $_FILES['userfile']['error'] gefunden werden.

UPLOAD_ERR_OK
    Wert: 0; Es liegt kein Fehler vor, die Datei wurde erfolgreich hochgeladen.

UPLOAD_ERR_INI_SIZE
    Wert: 1; Die hochgeladene Datei ¸berschreitet die in der Anweisung upload_max_filesize in php.ini festgelegte Grˆﬂe.

UPLOAD_ERR_FORM_SIZE
    Wert: 2; Die hochgeladene Datei ¸berschreitet die in dem HTML Formular mittels der Anweisung MAX_FILE_SIZE angegebene maximale Dateigrˆﬂe.

UPLOAD_ERR_PARTIAL
    Wert: 3; Die Datei wurde nur teilweise hochgeladen.

UPLOAD_ERR_NO_FILE
    Wert: 4; Es wurde keine Datei hochgeladen.

	*/
	function file_upload_error_message($error_code) 
	{
		switch ($error_code) 
		{
			case UPLOAD_ERR_INI_SIZE:
				return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
			case UPLOAD_ERR_FORM_SIZE:
				return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
			case UPLOAD_ERR_PARTIAL:
				return 'The uploaded file was only partially uploaded';
			case UPLOAD_ERR_NO_FILE:
				return 'No file was uploaded';
			case UPLOAD_ERR_NO_TMP_DIR:
				return 'Missing a temporary folder';
			case UPLOAD_ERR_CANT_WRITE:
				return 'Failed to write file to disk';
			case UPLOAD_ERR_EXTENSION:
				return 'File upload stopped by extension';
			default:
				return 'Unknown upload error';
		}
	}

	function checkUploadScript($field_id,$input_id,$allowedExt = array(),$file_upload_max_filesize = 0)
	{
		$html = '';
		//TODO add an filessize element for count(filesize) for all fieldtypes who use upload ?
		$html .= '<script language="javascript"  type="text/javascript">'."\n";
		//function to check extension
		$html .= "window.addEvent('domready', function()"."\n";
		$html .= "{"."\n";
		$html .= "	var element = document.id('$input_id');"."\n";
		$html .= "	var count_upload_max_filesize = document.id('count_upload_max_filesize');"."\n";
		$html .= "	if(!count_upload_max_filesize)"."\n";
		$html .= "	{"."\n";
		$html .= "		count_upload_max_filesize = new Element('input',{type: 'hidden',value:0,id:'count_upload_max_filesize'});"."\n";
		$html .= "		count_upload_max_filesize.inject(element,'before');"."\n";
		$html .= "	}"."\n";
		$html .= "});"."\n";
			
		$html .= 'function check_extension_'.$field_id.'(id)'."\n";
		$html .= '{'."\n";
		//$html .= 'alert(id);'."\n";
		$html .= '	var element = document.id(id);'."\n";
		$html .= '	value = element.value.toLowerCase();'."\n";

		$html .= "		var count_upload_max_filesize = document.id('count_upload_max_filesize');"."\n";
		$html .= "		var storedFilesize = element.retrieve('filesize',0);"."\n";
		$html .= "		var count_upload_max_filesize_value = (parseInt(count_upload_max_filesize.value) - parseInt(storedFilesize) >= 0) ? (parseInt(count_upload_max_filesize.value) - parseInt(storedFilesize)) : 0 ;"."\n";



		$allowedPos = array();
		foreach($allowedExt as $ext)
		{
			$html .= '	pos_'.$ext.' = value.indexOf(".'.$ext.'");'."\n";
			$allowedPos[] = 'pos_'.$ext.'==-1';
		}
		$allowedPos = implode(" && ", $allowedPos);
		$allowedString = implode(", ", $allowedExt);
		//$html .= '	if(element.value && '.$allowedPos.')'."\n";
		
		$html .= '	if('.$allowedPos.')'."\n";
		$html .= '	{'."\n";
		$html .= '		element.value = \'\';'."\n";
		//in IE we can not set the value and other browsers??
		$html .= "		var clone = element.clone(false,true).cloneEvents(element).inject(element,'after');"."\n";
		$html .= "		element.destroy();"."\n";
		$html .= "		count_upload_max_filesize.set('value',count_upload_max_filesize_value);"."\n";
		$html .= '		alert(\'wrong file-type. allowed are: '.$allowedString.'\')'."\n";
		$html .= '	}'."\n";
		$html .= '	else'."\n";
		$html .= '	{'."\n";
		
		//if(!$upload_max_filesize)
		//{
		$upload_max_filesize = ini_get('upload_max_filesize');
		//}
		preg_match("/([0-9]+)(M|K|B|G)/i", $upload_max_filesize, $treffer); 
		if(count($treffer) && isset($treffer[2]))
		{
			switch($treffer[2])
			{
				case 'B':
					$upload_max_filesize = $treffer[1];
				break;
				case 'K':
					$upload_max_filesize = $treffer[1]*1024;
				break;
				case 'M':
					$upload_max_filesize = $treffer[1]*1024*1024;
				break;
				case 'G':
					$upload_max_filesize = $treffer[1]*1024*1024*1024;
				break;
				default:
					$upload_max_filesize = $treffer[1];
				break;
			}
		}
		$html .= "		var upload_max_filesize = ".$upload_max_filesize.";"."\n";
		$html .= "		var file_upload_max_filesize = ".$file_upload_max_filesize.";"."\n";
		$html .= "		if(file_upload_max_filesize > upload_max_filesize || !file_upload_max_filesize) file_upload_max_filesize = upload_max_filesize;"."\n";
		
		//TODO check for current_size
		
		$html .= "		if(element.files)"."\n";
		$html .= "		{"."\n";
		//the browser has an files object
		$html .= "			var size = element.files.item(0).size;"."\n";
		//TODO check for current_size
		$html .= "			if((size + count_upload_max_filesize_value) > file_upload_max_filesize || (size + count_upload_max_filesize_value) > upload_max_filesize)"."\n";
		$html .= "			{"."\n";
		$html .= '				element.value = \'\';'."\n";
		//in IE we can not set the value and other browsers??
		$html .= "				var clone = element.clone(false,true).cloneEvents(element).inject(element,'after');"."\n";
		$html .= "				count_upload_max_filesize.set('value',count_upload_max_filesize_value);"."\n";
		$html .= "				element.destroy();"."\n";
		//TODO check for current size
		$html .= '				alert(\' file-size to big. \')'."\n";
		$html .= "			}"."\n";
		$html .= "			else"."\n";
		$html .= "			{"."\n";
		$html .= "				element.store('filesize', size);"."\n";
		$html .= "				count_upload_max_filesize.value = parseInt(count_upload_max_filesize_value) + size;"."\n";
		//TODO set current_size
		$html .= "			}"."\n";
		$html .= "		}"."\n";

		$html .= "		else"."\n";
		$html .= "		{"."\n";
		//the browser has not an files object
		//function CheckExtension for Browser IE only testet with IE8
		$html .= "			function CheckExtention_".$field_id."()"."\n";
		$html .= "			{"."\n";
		$html .= "				var file;"."\n";
		$html .= "				var path = element.value;"."\n";
		$html .= "				file = objFSO.getFile(path);"."\n";
		$html .= "				if(file)"."\n";
		$html .= "				{"."\n";
		$html .= "					var size;"."\n";
		$html .= "					size = file.size ; // This size will be in Bytes"."\n";
		$html .= "					if((size + count_upload_max_filesize_value) > file_upload_max_filesize || (size + count_upload_max_filesize_value) > upload_max_filesize)"."\n";
		$html .= "					{"."\n";
		//in IE we must replace
		$html .= "						element.value = '';"."\n";
		$html .= "						var clone = element.clone(false,true).cloneEvents(element).inject(element,'after');"."\n";
		$html .= "						count_upload_max_filesize.set('value',count_upload_max_filesize_value);"."\n";
		$html .= "						element.destroy();"."\n";
		$html .= '						alert(\' file-size to big. \')'."\n";
		$html .= "					}"."\n";
		$html .= "					else"."\n";
		$html .= "					{"."\n";
		$html .= "						element.store('filesize', size);"."\n";
		$html .= "						count_upload_max_filesize.value = parseInt(count_upload_max_filesize_value) + size;"."\n";
		$html .= "					}"."\n";
		$html .= "				}"."\n";
		//we cant get the file
		$html .= "			}"."\n";		
		$html .= "			var objFSO ;"."\n";
		$html .= "			try"."\n";
		$html .= "			{"."\n";
		/*
		Lokalen Verzeichnispfad beim Hochladen von Dateien auf einen Server mit einbeziehen 
		Include local directory path when uploading files to a server
		must set to true
		
		and
		ActiveX-Steuerelemente ausf√ºhren, die nicht f√ºr Skripting sicher sind
		Initialize and script ActiveX controls not marked safe for scripting
		must set to 
		0x0 => Aktivieren aktivate
		0x1 => Best√§tigen confirm
		*/
		//check for Browser IE only testet with IE8
		$html .= '				objFSO = new ActiveXObject("Scripting.FileSystemObject");'."\n";
		$html .= '				if(objFSO){throw 1;}else{throw 0;}'."\n";
		$html .= "			}"."\n";
		$html .= "			catch(e)"."\n";
		$html .= "			{"."\n";
		$html .= "				if(e)"."\n";
		$html .= "				{"."\n";
		$html .= "					CheckExtention_".$field_id."();"."\n";
		$html .= "				}"."\n";
		$html .= "				else"."\n";
		$html .= "				{"."\n";
		// we cant check the size no IE or IE is not set to see above
		$html .= "				}"."\n";
		$html .= "			}"."\n";
		$html .= "		}"."\n";
		$html .= '	}'."\n";
		$html .= '}'."\n";

		$html .= '</script>'."\n";
		return $html;
	}

	
	
	/**
	 * Add unobtrusive javascript support for the advanced uploader.
	 *
	 * @param   string  $id
	 * @param   array   $params  An array of options for the uploader.
	 * @param   string  $upload_queue
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public static function uploader($id='file-upload', $params = array(), $upload_queue='upload-queue',$field_id = '')
	{
		// Include MooTools framework
		JHtml::_('behavior.framework');

		$uncompressed	= JFactory::getConfig()->get('debug') ? '-uncompressed' : '';
		JHtml::_('script', 'system/swf'.$uncompressed.'.js', true, true);
		JHtml::_('script', 'system/progressbar'.$uncompressed.'.js', true, true);
		JHtml::_('script', 'system/uploader'.$uncompressed.'.js', true, true);

		$document = JFactory::getDocument();

		static $uploaders;

		if (!isset($uploaders)) {
			$uploaders = array();

			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_FILENAME');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_UPLOAD_COMPLETED');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_ERROR_OCCURRED');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_ALL_FILES');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_PROGRESS_OVERALL');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_CURRENT_TITLE');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_REMOVE');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_REMOVE_TITLE');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_CURRENT_FILE');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_CURRENT_PROGRESS');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_FILE_ERROR');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_FILE_SUCCESSFULLY_UPLOADED');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_VALIDATION_ERROR_DUPLICATE');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_VALIDATION_ERROR_SIZELIMITMIN');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_VALIDATION_ERROR_SIZELIMITMAX');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_VALIDATION_ERROR_FILELISTMAX');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_VALIDATION_ERROR_FILELISTSIZEMAX');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_ERROR_HTTPSTATUS');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_ERROR_SECURITYERROR');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_ERROR_IOERROR');
			JText::script('JLIB_HTML_BEHAVIOR_UPLOADER_ALL_FILES');
		}

		if (isset($uploaders[$id]) && ($uploaders[$id])) {
			return;
		}

		$onFileSuccess = '\\function(file, response) {
			//alert(response);
			if(!JSON.validate(response))
			{
				file.element.addClass(\'file-failed\');
				file.info.set(\'html\', \'<strong>\' +
				Joomla.JText._(\'JLIB_HTML_BEHAVIOR_UPLOADER_ERROR_OCCURRED\', \'An Error Occurred\') + \'<br />\' + response + \'</strong>\');
				return ;
			}
			var json = new Hash(JSON.decode(response, true) || {});

			if (json.get(\'status\') == \'1\') {
				file.element.addClass(\'file-success\');
				file.info.set(\'html\', \'<strong>\' + Joomla.JText._(\'JLIB_HTML_BEHAVIOR_UPLOADER_FILE_SUCCESSFULLY_UPLOADED\') + \'<br />\' + json.get(\'error\') + \'</strong>\');
			} else {
				file.element.addClass(\'file-failed\');
				file.info.set(\'html\', \'<strong>\' +
					Joomla.JText._(\'JLIB_HTML_BEHAVIOR_UPLOADER_ERROR_OCCURRED\', \'An Error Occurred\').substitute({ error: json.get(\'error\') }) + \'</strong>\');
			}
		}';
		
		if (!isset($params['startButton'])) {
			$params['startButton'] = $field_id.'upload-start';
		}

		if (!isset($params['clearButton'])) {
			$params['clearButton'] = $field_id.'upload-clear';
		}
		$onLoad =
			'\\function() {
				document.id(\''.$id.'\').removeClass(\'hide\'); // we show the actual UI
				document.id(\''.$field_id.'upload-noflash\').destroy(); // ... and hide the plain form

				// We relay the interactions with the overlayed flash to the link
				this.target.addEvents({
					click: function() {
						return false;
					},
					mouseenter: function() {
						this.addClass(\'hover\');
					},
					mouseleave: function() {
						this.removeClass(\'hover\');
						this.blur();
					},
					mousedown: function() {
						this.focus();
					}
				});

				// Interactions for the 2 other buttons

				document.id(\''.$params['clearButton'].'\').addEvent(\'click\', function() {
					'.$field_id.'Uploader.remove(); // remove all files
					return false;
				});

				document.id(\''.$params['startButton'].'\').addEvent(\'click\', function() {
					'.$field_id.'Uploader.start(); // start upload
					return false;
				});
			}';

		$onSelect =
			'\\function() {
				this.status.removeClass(\'status-browsing\');
			}';
		
		
		// Setup options object
		$opt['verbose']				= true;
		$opt['url']					= (isset($params['targetURL'])) ? $params['targetURL'] : null ;
		$opt['path']				= (isset($params['swf'])) ? $params['swf'] : JURI::root(true).'/media/system/swf/uploader.swf';
		$opt['height']				= (isset($params['height'])) && $params['height'] ? (int)$params['height'] : null;
		$opt['width']				= (isset($params['width'])) && $params['width'] ? (int)$params['width'] : null;
		$opt['multiple']			= (isset($params['multiple']) && !($params['multiple'])) ? '\\false' : '\\true';
		$opt['queued']				= (isset($params['queued']) && !($params['queued'])) ? (int)$params['queued'] : null;
		$opt['target']				= (isset($params['target'])) ? $params['target'] : '\\document.id(\'upload-browse\')';
		$opt['instantStart']		= (isset($params['instantStart']) && ($params['instantStart'])) ? '\\true' : '\\false';
		$opt['allowDuplicates']		= (isset($params['allowDuplicates']) && !($params['allowDuplicates'])) ? '\\false' : '\\true';
		// limitSize is the old parameter name.  Remove in 1.7
		$opt['fileSizeMax']			= (isset($params['limitSize']) && ($params['limitSize'])) ? (int)$params['limitSize'] : null;
		// fileSizeMax is the new name.  If supplied, it will override the old value specified for limitSize
		$opt['fileSizeMax']			= (isset($params['fileSizeMax']) && ($params['fileSizeMax'])) ? (int)$params['fileSizeMax'] : $opt['fileSizeMax'];
		$opt['fileSizeMin']			= (isset($params['fileSizeMin']) && ($params['fileSizeMin'])) ? (int)$params['fileSizeMin'] : null;
		// limitFiles is the old parameter name.  Remove in 1.7
		$opt['fileListMax']			= (isset($params['limitFiles']) && ($params['limitFiles'])) ? (int)$params['limitFiles'] : null;
		// fileListMax is the new name.  If supplied, it will override the old value specified for limitFiles
		$opt['fileListMax']			= (isset($params['fileListMax']) && ($params['fileListMax'])) ? (int)$params['fileListMax'] : $opt['fileListMax'];
		$opt['fileListSizeMax']		= (isset($params['fileListSizeMax']) && ($params['fileListSizeMax'])) ? (int)$params['fileListSizeMax'] : null;
		// types is the old parameter name.  Remove in 1.7
		$opt['typeFilter']			= (isset($params['types'])) ? '\\'.$params['types'] : '\\{Joomla.JText._(\'JLIB_HTML_BEHAVIOR_UPLOADER_ALL_FILES\'): \'*.*\'}';
		$opt['typeFilter']			= (isset($params['typeFilter'])) ? '\\'.$params['typeFilter'] : $opt['typeFilter'];

		// Optional functions
		$opt['createReplacement'] 	= (isset($params['createReplacement'])) ? '\\'.$params['createReplacement'] : null;
		$opt['onFileComplete'] 		= (isset($params['onFileComplete'])) ? '\\'.$params['onFileComplete'] : null;
		$opt['onBeforeStart'] 		= (isset($params['onBeforeStart'])) ? '\\'.$params['onBeforeStart'] : null;
		$opt['onStart'] 			= (isset($params['onStart'])) ? '\\'.$params['onStart'] : null;
		$opt['onComplete'] 			= (isset($params['onComplete'])) ? '\\'.$params['onComplete'] : null;
		$opt['onFileSuccess'] 		= (isset($params['onFileSuccess'])) ? '\\'.$params['onFileSuccess'] : $onFileSuccess;
		
		$opt['onLoad'] 		= (isset($params['onLoad'])) ? '\\'.$params['onLoad'] : $onLoad;
		$opt['onSelect'] 		= (isset($params['onSelect'])) ? '\\'.$params['onSelect'] : $onSelect;
		
		$opt['onFileRemove'] 		= (isset($params['onFileRemove'])) ? '\\'.$params['onFileRemove'] : null;
		
		$opt['fieldName'] 		= (isset($params['fieldName'])) ? $params['fieldName'] : '\\\'Filedata\'';
		



		$options = self::_getJSObject($opt);
		// Attach tooltips to document
		$uploaderInit =
			'window.addEvent(\'domready\', function(){
				var '.$field_id.'Uploader = new FancyUpload2(document.id(\''.$id.'\'), document.id(\''.$upload_queue.'\'), '.$options.' );
				});';
		$document->addScriptDeclaration($uploaderInit);

		// Set static array
		$uploaders[$id] = true;

		return;
	}
	
	
		/**
	 * Internal method to get a JavaScript object notation string from an array
	 *
	 * @param   array  $array	The array to convert to JavaScript object notation
	 *
	 * @return  string  JavaScript object notation representation of the array
	 *
	 * @since   11.1
	 */
	protected static function _getJSObject($array=array())
	{
		// Initialise variables.
		$object = '{';

		// Iterate over array to build objects
		foreach ((array)$array as $k => $v)
		{
			if (is_null($v)) {
				continue;
			}

			if (is_bool($v)) {
				if ($k === 'fullScreen') {
					$object .= 'size: { ';
					$object .= 'x: ';
					$object .= 'window.getSize().x-80';
					$object .= ',';
					$object .= 'y: ';
					$object .= 'window.getSize().y-80';
					$object .= ' }';
					$object .= ',';
				}
				else {
					$object .= ' '.$k.': ';
					$object .= ($v) ? 'true' : 'false';
					$object .= ',';
				}
			}
			elseif (!is_array($v) && !is_object($v)) {
				$object .= ' '.$k.': ';
				$object .= (is_numeric($v) || strpos($v, '\\') === 0) ? (is_numeric($v)) ? $v : substr($v, 1) : "'".$v."'";
				$object .= ',';
			}
			else {
				$object .= ' '.$k.': '.self::_getJSObject($v).',';
			}
		}

		if (substr($object, -1) == ',') {
			$object = substr($object, 0, -1);
		}

		$object .= '}';

		return $object;
	}
	
	
	public static function getTmpDir()
	{
		$config = JFactory::getConfig();
		$p_filename = JPath::clean($config->get('tmp_path'));
		$documentRoot = JPath::clean(isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] :substr($_SERVER['SCRIPT_FILENAME'], 0, -strlen($_SERVER['SCRIPT_NAME'])));
		if(strpos($p_filename,$documentRoot) === false)
		{
			//an wrong tmp path
			return false;
		}

		
		$user = JFactory::getUser();
		$userId = $user->get('id');
		$app = JFactory::getApplication();
		$where = $app->isAdmin() ? 'admin' : 'site';
		$extractdir = JPath::clean($p_filename. '/pi/'.$where.'/user_id_' . $userId);
		
		return $extractdir;
	}
	
	public static function createTmpDir($folder = '')
	{
		// Build the appropriate paths
		$config = JFactory::getConfig();
		$p_filename = $config->get('tmp_path');
		
		$documentRoot = JPath::clean(isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] :substr($_SERVER['SCRIPT_FILENAME'], 0, -strlen($_SERVER['SCRIPT_NAME'])));
		if(strpos($p_filename,$documentRoot) === false)
		{
			//an wrong tmp path
			return false;
		}
		
		
		$user = JFactory::getUser();
		$userId = $user->get('id');
		$app = JFactory::getApplication();
		$where = $app->isAdmin() ? 'admin' : 'site';
		// Clean the paths
		$extractdir = JPath::clean($p_filename. '/pi/'.$where.'/user_id_' . $userId.'/'.$folder);//$uniquedir);
		if(!JFolder::exists($extractdir))
		{
			if(!JFolder::create($extractdir))
			{
				return false;
			}
		}
		return $extractdir;
	}
	
	/**
	 * Checks if the file can be uploaded
	 *
	 * @param array File information
	 * @param string An error message to be returned
	 * @return boolean
	TODO rewrite
	 */
	public static function canUpload($file, &$error)
	{
		$params = JComponentHelper::getParams('com_media');

		if (empty($file['name'])) {
			$error = 'COM_MEDIA_ERROR_UPLOAD_INPUT';
			return false;
		}

		jimport('joomla.filesystem.file');
		if ($file['name'] !== JFile::makesafe($file['name'])) {
			$error = 'COM_MEDIA_ERROR_WARNFILENAME';
			return false;
		}

		$format = strtolower(JFile::getExt($file['name']));

		$allowable = explode(',', $params->get('upload_extensions'));
		$ignored = explode(',', $params->get('ignore_extensions'));
		if (!in_array($format, $allowable) && !in_array($format,$ignored))
		{
			$error = 'COM_MEDIA_ERROR_WARNFILETYPE';
			return false;
		}

		$maxSize = (int) ($params->get('upload_maxsize', 0) * 1024 * 1024);
		if ($maxSize > 0 && (int) $file['size'] > $maxSize)
		{
			$error = 'COM_MEDIA_ERROR_WARNFILETOOLARGE';
			return false;
		}

		$user = JFactory::getUser();
		$imginfo = null;
		if ($params->get('restrict_uploads',1)) {
			$images = explode(',', $params->get('image_extensions'));
			if (in_array($format, $images)) { // if its an image run it through getimagesize
				// if tmp_name is empty, then the file was bigger than the PHP limit
				if (!empty($file['tmp_name'])) {
					if (($imginfo = getimagesize($file['tmp_name'])) === FALSE) {
						$error = 'COM_MEDIA_ERROR_WARNINVALID_IMG';
						return false;
					}
				} else {
					$error = 'COM_MEDIA_ERROR_WARNFILETOOLARGE';
					return false;
				}
				/*
$size = getimagesize($filename);
$fp = fopen($filename, "rb");
if ($size && $fp) {
    header("Content-type: {$size['mime']}");g
    fpassthru($fp);
    exit;
} else {
    // Fehler
}
				
				
				*/
				
			} elseif (!in_array($format, $ignored)) {
				// if its not an image...and we're not ignoring it
				$allowed_mime = explode(',', $params->get('upload_mime'));
				$illegal_mime = explode(',', $params->get('upload_mime_illegal'));
				if (function_exists('finfo_open') && $params->get('check_mime',1)) {
					// We have fileinfo
					$finfo = finfo_open(FILEINFO_MIME);
					$type = finfo_file($finfo, $file['tmp_name']);
					if (strlen($type) && !in_array($type, $allowed_mime) && in_array($type, $illegal_mime)) {
						$error = 'COM_MEDIA_ERROR_WARNINVALID_MIME';
						return false;
					}
					finfo_close($finfo);
				} elseif (function_exists('mime_content_type') && $params->get('check_mime',1)) {
					// we have mime magic
					$type = mime_content_type($file['tmp_name']);
					if (strlen($type) && !in_array($type, $allowed_mime) && in_array($type, $illegal_mime)) {
						$error = 'COM_MEDIA_ERROR_WARNINVALID_MIME';
						return false;
					}
				} elseif (!$user->authorise('core.manage')) {
					$error = 'COM_MEDIA_ERROR_WARNNOTADMIN';
					return false;
				}
			}
		}

		$xss_check =  JFile::read($file['tmp_name'],false,256);
		$html_tags = array('abbr','acronym','address','applet','area','audioscope','base','basefont','bdo','bgsound','big','blackface','blink','blockquote','body','bq','br','button','caption','center','cite','code','col','colgroup','comment','custom','dd','del','dfn','dir','div','dl','dt','em','embed','fieldset','fn','font','form','frame','frameset','h1','h2','h3','h4','h5','h6','head','hr','html','iframe','ilayer','img','input','ins','isindex','keygen','kbd','label','layer','legend','li','limittext','link','listing','map','marquee','menu','meta','multicol','nobr','noembed','noframes','noscript','nosmartquotes','object','ol','optgroup','option','param','plaintext','pre','rt','ruby','s','samp','script','select','server','shadow','sidebar','small','spacer','span','strike','strong','style','sub','sup','table','tbody','td','textarea','tfoot','th','thead','title','tr','tt','ul','var','wbr','xml','xmp','!DOCTYPE', '!--');
		foreach($html_tags as $tag) {
			// A tag is '<tagname ', so we need to add < and a space or '<tagname>'
			if (stristr($xss_check, '<'.$tag.' ') || stristr($xss_check, '<'.$tag.'>')) {
				$error = 'COM_MEDIA_ERROR_WARNIEXSS';
				return false;
			}
		}
		return true;
	}
	
}
?>