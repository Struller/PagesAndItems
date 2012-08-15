<?php
/**
* @version		2.1.6
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// No direct access
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.error.log');
/**
 * File Media Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @since		1.6
 */
class PagesAndItemsControllerFile extends JController
{
	/**
	 * Upload a file
	 *
	 * @since 1.5
	 */
	function upload()
	{
		// Check for request forgeries
		if (!JRequest::checkToken('request')) {
			$response = array(
				'status' => '0',
				'error' => JText::_('JINVALID_TOKEN')
			);
			echo json_encode($response);
			return;
		}

		// Get the user
		$user		= JFactory::getUser();
		$log		= JLog::getInstance('upload.error.php');
		$app	= JFactory::getApplication();
		$userId = $user->get('id');
		$where = $app->isAdmin() ? 'admin' : 'site';

		$extensionName = JRequest::getVar('extensionName');
		$extensionType = JRequest::getVar('extensionType');
		$extensionFolder = JRequest::getVar('extensionFolder');
		$extension = true;
		$extensionTriggerName = '';
		if($extensionName && $extensionType)
		{
			$extension = true;
			$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'includes'.DS.'extensions';
			require_once($path.DS.$extensionType.'helper.php');
			switch(strtolower($extensionType))
			{
				case 'manager':
					ExtensionManagerHelper::importExtension(null, $extensionName,true,null,false);
					
				break;
					
				case 'itemtype':
					ExtensionItemtypeHelper::importExtension(null, $extensionName,true,null,false);
				break;
					
				case 'fieldtype':
					ExtensionFieldtypeHelper::importExtension(null, $extensionName,true,null,false);
				break;

				case 'html':
					ExtensionHtmlHelper::importExtension($extensionFolde, $extensionName,true,null,false);
				break;

				case 'piplugin':
					ExtensionPipluginHelper::importExtension($extensionFolde, $extensionName,true,null,false);
				break;
			}
			$dispatcher	= JDispatcher::getInstance();
		}
		
		/*	
		//&extensionTask=upload ???
		//&folder='.$folder.'
		//&tmpFolder=1;

		$folder		= JRequest::getVar('folder', '', '', 'path');
		if(!$folder)
		{
			we use from COM_MEDIA
		}
		*/
		$tmpFolder = JRequest::getVar('tmpFolder');
		$folder = JRequest::getVar('folder', '', '', 'path');
		//$field->item_id ? 'item_id_'.$field->item_id.'/': 'item_id_'.uniqid().'/').'field_id_'.$field->id
		$folderContext = str_replace('/','.',str_replace(DS,'/',$folder));
		if($tmpFolder)
		{
			/*
			we create an unique folder in ?
			and add in session this
			*/
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'helpers'.DS.'file.php');
			$tempFolder = PagesAndItemsHelperFile::createTmpDir($folder);
			
			if(!$tempFolder)
			{
				$response = array(
				'status' => '0',
				'error' => JText::_('COM_MEDIA_ERROR_BAD_REQUEST').' cant create Folder: '.$folder
				);

				echo json_encode($response);
				return;
			}
			
			$folder = $tempFolder;
		}
		// Get some data from the request
		$file		= JRequest::getVar('Filedata', '', 'files', 'array');
		$fileRename = JRequest::getVar('fileRename');
		$return		= JRequest::getVar('return-url', null, 'post', 'base64');
		$extensionLang = 'com_media';
		$lang = &JFactory::getLanguage();
		$lang->load(strtolower($extensionLang), JPATH_ADMINISTRATOR, null, false, false) || $lang->load(strtolower($extensionLang), JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		// Make the filename safe
		$file['name']	= JFile::makeSafe($file['name']);
		$type = $file['type'];
		if (isset($file['name']))
		{
			// The request is valid
			$error = null;
			$filepath = JPath::clean($folder . '/' . strtolower($file['name']));
			if($tmpFolder)
			{
				$oldUpload = $app->getUserState("com_pagesanditems.upload.".$where.".user_id_".$userId.".".$folderContext.".tmp_name");
				if($oldUpload)
				{
					//remove the file
					JFile::delete($oldUpload);
					$app->setUserState("com_pagesanditems.upload.".$where.".user_id_".$userId.".".$folderContext.".tmp_name",'');
					$app->setUserState("com_pagesanditems.upload.".$where.".user_id_".$userId.".".$folderContext.".name",'');
					$app->setUserState("com_pagesanditems.upload.".$where.".user_id_".$userId.".".$folderContext.".type",'');
					$app->setUserState("com_pagesanditems.upload.".$where.".user_id_".$userId.".".$folderContext.".size",'');
					
				}
			}

			/*
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'helpers'.DS.'file.php');
			if($extension)
			{
				//we have only the extension in dispatcher
				//
				$error = '';
				//here trigger for eg. the mime-type correct
				$result = $dispatcher->trigger('onFileCheckBeforeUpload', array($extensionName, $file,&$error));
				if($error) //if (in_array(false, $result, true)) 
				{
					// There are some errors in the plugins
					$log->addEntry(array('comment' => 'Invalid: '.$filepath.': '.$error));
					$response = array(
						'status' => '0',
						'error' => JText::_($error)
					);
					echo json_encode($response);
					return;
				}
				
			}
			//must do
			//TODO some params like com_media
			elseif (!PagesAndItemsHelperFile::canUpload($file, $error))
			{
				//here check if eg. the mime-type correct
				$log->addEntry(array('comment' => 'Invalid: '.$filepath.': '.$error));
				$response = array(
					'status' => '0',
					'error' => JText::_($error)
				);
				echo json_encode($response);
				return;
			}
			
			
			*/
			
			
			if($extension)
			{
				//we have only the extension in dispatcher
				//$error = '';
				$result = $dispatcher->trigger('onFileRenameBeforeUpload', array($extensionName, &$filepath,&$fileRename,&$error,$folder));
				if($error) //if (in_array(false, $result, true)) 
				{
					// There are some errors in the plugins
					$log->addEntry(array('comment' => 'Errors before save: '.$filepath.' : '.$error));
					$response = array(
						'status' => '0',
						//TODO own error string
						'error' => JText::plural('COM_MEDIA_ERROR_BEFORE_SAVE', 1, '<br />'.$error)
					);
					echo json_encode($response);
					return;
				}
				
			}
			//rename file if already exist
			if($fileRename)
			{
				if(JFile::exists($filepath))
				{
					$fileName = JFile::stripExt(JFile::getName($filepath));
					$fileExt = JFile::getExt($filepath);
					$folderName = JPath::clean($folder . '/');
					$j = 2;
					while (JFile::exists($folderName.$fileName.'-'.$j.".".$fileExt))
					{
						$j = $j + 1;
					}
					//$new_name = $old_name . "-" . $j;
					$filepath = $folderName.$fileName.'-'.$j.".".$fileExt;
				}
			}
			elseif (JFile::exists($filepath))
			{
				// File exists
				$log->addEntry(array('comment' => 'File exists: '.$filepath.' by user_id '.$user->id));
				$response = array(
					'status' => '0',
					//TODO own error string?
					'error' => JText::_('COM_MEDIA_ERROR_FILE_EXISTS')
				);
				echo json_encode($response);
				return;
			}
			
			//$file = (array) $object_file;
			$type = $file['type'];
			if (function_exists('finfo_open'))
			{
				// We have fileinfo
				$finfo = finfo_open(FILEINFO_MIME);
				$type = finfo_file($finfo, $file['tmp_name']);
				finfo_close($finfo);
			}
			elseif (function_exists('mime_content_type')) 
			{
				// we have mime magic
				$type = mime_content_type($file['tmp_name']);
			}
			$file['type'] = $type;
			
			
			// Trigger the onContentBeforeSave event.
			$object_file = new JObject($file);
			$object_file->filepath = $filepath;
			
			if($extension)
			{
				//we have only the extension in dispatcher
				//the extension can here check for size / mime type ....
				$result = $dispatcher->trigger('onBeforeUpload', array($extensionName, &$object_file));
				if (in_array(false, $result, true)) {
					// There are some errors in the plugins
					$log->addEntry(array('comment' => 'Errors before save: '.$filepath.' : '.implode(', ', $object_file->getErrors())));
					$response = array(
						'status' => '0',
						//TODO own error string
						'error' => JText::plural('COM_MEDIA_ERROR_BEFORE_SAVE', count($errors = $object_file->getErrors()), implode('<br />', $errors))
					);
					echo json_encode($response);
					return;
				}
				
			}
			
			
			$file = (array) $object_file;
			if (!JFile::upload($file['tmp_name'], $file['filepath']))
			{
				// Error in upload
				$log->addEntry(array('comment' => 'Error on upload: '.$filepath));
				$response = array(
					'status' => '0',
					'error' => JText::_('COM_MEDIA_ERROR_UNABLE_TO_UPLOAD_FILE').' '.$file['filepath']
				);
				echo json_encode($response);
				return;
			}
			else
			{
				// Trigger the onContentAfterSave event.
				//$dispatcher->trigger('onContentAfterSave', array('com_media.file', &$object_file, true));
				$tmp = '';
				if($tmpFolder)
				{
					//store it in the session
					/*
					$app	= JFactory::getApplication();
					$userId = $user->get('id');
					$oldUpload = $app->getUserState("com_pagesanditems.upload.user_id".$userId.$folderContext);
					if($oldUpload)
					{
						//remove the file
						JFile::delete($oldUpload);
					}
					*/
					$app->setUserState("com_pagesanditems.upload.".$where.".user_id_".$userId.".".$folderContext.".tmp_name",$file['filepath']);
					$app->setUserState("com_pagesanditems.upload.".$where.".user_id_".$userId.".".$folderContext.".name",$file['name']);
					$app->setUserState("com_pagesanditems.upload.".$where.".user_id_".$userId.".".$folderContext.".type",$file['type']);
					$app->setUserState("com_pagesanditems.upload.".$where.".user_id_".$userId.".".$folderContext.".size",$file['size']);

					
					$tmp = 'UserState: '.$app->getUserState("com_pagesanditems.upload.".$where.".user_id_".$userId.".".$folderContext.".tmp_name");
					
				}
				
				$log->addEntry(array('comment' => $folder));
				$response = array(
					'status' => '1',
					'error' => JText::sprintf('COM_MEDIA_UPLOAD_COMPLETE', JURI::root(true).substr($file['filepath'], strlen(JPATH_ROOT.DS))),
					//.' type: '.$type.' tmp: '.$tmp.' tmpFolder:'.$tmpFolder.' '.($result ? ' result: '.json_encode($result) : ''),
					'filePath' => $file['filepath'],
					'filePathRelative' => JURI::root(true).str_replace(DS,'/',substr($file['filepath'], strlen(JPATH_ROOT)))
				);
				//'error' => JText::sprintf('COM_MEDIA_UPLOAD_COMPLETE', substr($file['filepath'], strlen(COM_MEDIA_BASE)))
				echo json_encode($response);
				return;
			}
			
		}
		else
		{
			$response = array(
				'status' => '0',
				'error' => JText::_('COM_MEDIA_ERROR_BAD_REQUEST')
			);

			echo json_encode($response);
			return;
		}
		
		
		unset($file['name']);
		if (isset($file['name']))
		{
			// The request is valid
			$err = null;

			$filepath = JPath::clean($folder . '/' . strtolower($file['name']));

			if (!MediaHelper::canUpload($file, $err))
			{
				$log->addEntry(array('comment' => 'Invalid: '.$filepath.': '.$err));
				$response = array(
					'status' => '0',
					'error' => JText::_($err)
				);
				echo json_encode($response);
				return;
			}

			/*
			// Trigger the onContentBeforeSave event.
			JPluginHelper::importPlugin('content');
			$dispatcher	= JDispatcher::getInstance();
			$object_file = new JObject($file);
			$object_file->filepath = $filepath;
			$result = $dispatcher->trigger('onContentBeforeSave', array('com_media.file', &$object_file));
			if (in_array(false, $result, true)) {
				// There are some errors in the plugins
				$log->addEntry(array('comment' => 'Errors before save: '.$filepath.' : '.implode(', ', $object_file->getErrors())));
				$response = array(
					'status' => '0',
					'error' => JText::plural('COM_MEDIA_ERROR_BEFORE_SAVE', count($errors = $object_file->getErrors()), implode('<br />', $errors))
				);
				echo json_encode($response);
				return;
			}
			*/
			if (JFile::exists($filepath))
			{
				// File exists
				$log->addEntry(array('comment' => 'File exists: '.$filepath.' by user_id '.$user->id));
				$response = array(
					'status' => '0',
					'error' => JText::_('COM_MEDIA_ERROR_FILE_EXISTS')
				);
				echo json_encode($response);
				return;
			}
			elseif (!$user->authorise('core.create', 'com_media'))
			{
				// File does not exist and user is not authorised to create
				$log->addEntry(array('comment' => 'Create not permitted: '.$filepath.' by user_id '.$user->id));
				$response = array(
					'status' => '0',
					'error' => JText::_('COM_MEDIA_ERROR_CREATE_NOT_PERMITTED')
				);
				echo json_encode($response);
				return;
			}

			$file = (array) $object_file;
			if (!JFile::upload($file['tmp_name'], $file['filepath']))
			{
				// Error in upload
				$log->addEntry(array('comment' => 'Error on upload: '.$filepath));
				$response = array(
					'status' => '0',
					'error' => JText::_('COM_MEDIA_ERROR_UNABLE_TO_UPLOAD_FILE')
				);
				echo json_encode($response);
				return;
			}
			else
			{
				// Trigger the onContentAfterSave event.
				//$dispatcher->trigger('onContentAfterSave', array('com_media.file', &$object_file, true));
				
				$log->addEntry(array('comment' => $folder));
				$response = array(
					'status' => '1',
					'error' => JText::sprintf('COM_MEDIA_UPLOAD_COMPLETE', substr($file['filepath'], strlen($folder)))
				);
				echo json_encode($response);
				return;
			}
		}
		else
		{
			$response = array(
				'status' => '0',
				'error' => JText::_('COM_MEDIA_ERROR_BAD_REQUEST').' folder: '.$folder
			);

			echo json_encode($response);
			return;
		}
	}
}
