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

/**
 * Media File Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @since		1.5
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
		JRequest::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

		// Get the user
		$user		= JFactory::getUser();

		// Get some data from the request
		$file		= JRequest::getVar('Filedata', '', 'files', 'array');
		$folder		= JRequest::getVar('folder', '', '', 'path');
		$return		= JRequest::getVar('return-url', null, 'post', 'base64');


		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		// Set the redirect
		if ($return) {
			$this->setRedirect(base64_decode($return).'&folder='.$folder);
		}
		// Make the filename safe
		$file['name']	= JFile::makeSafe($file['name']);
		unset($file['name']);
		if (isset($file['name']))
		{
			// The request is valid
			$err = null;
			if (!MediaHelper::canUpload($file, $err))
			{
				// The file can't be upload
				JError::raiseNotice(100, JText::_($err));
				return false;
			}

			$filepath = JPath::clean(COM_MEDIA_BASE . '/' . $folder . '/' . strtolower($file['name']));

			// Trigger the onContentBeforeSave event.
			JPluginHelper::importPlugin('content');
			$dispatcher	= JDispatcher::getInstance();
			$object_file = new JObject($file);
			$object_file->filepath = $filepath;
			$result = $dispatcher->trigger('onContentBeforeSave', array('com_media.file', &$object_file));
			if (in_array(false, $result, true)) {
				// There are some errors in the plugins
				JError::raiseWarning(100, JText::plural('COM_MEDIA_ERROR_BEFORE_SAVE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));
				return false;
			}
			$file = (array) $object_file;

			if (JFile::exists($file['filepath']))
			{
				// File exists
				JError::raiseWarning(100, JText::_('COM_MEDIA_ERROR_FILE_EXISTS'));
				return false;
			}
			elseif (!$user->authorise('core.create', 'com_media'))
			{
				// File does not exist and user is not authorised to create
				JError::raiseWarning(403, JText::_('COM_MEDIA_ERROR_CREATE_NOT_PERMITTED'));
				return false;
			}

			if (!JFile::upload($file['tmp_name'], $file['filepath']))
			{
				// Error in upload
				JError::raiseWarning(100, JText::_('COM_MEDIA_ERROR_UNABLE_TO_UPLOAD_FILE'));
				return false;
			}
			else
			{
				// Trigger the onContentAfterSave event.
				$dispatcher->trigger('onContentAfterSave', array('com_media.file', &$object_file,true));
				$this->setMessage(JText::sprintf('COM_MEDIA_UPLOAD_COMPLETE', substr($file['filepath'], strlen(COM_MEDIA_BASE))));
				return true;
			}
		}
		else
		{
			$this->setRedirect('index.php', JText::_('COM_MEDIA_INVALID_REQUEST'), 'error');
			return false;
		}
	}
}
