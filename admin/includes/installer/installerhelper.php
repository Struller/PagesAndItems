<?php
/**
* @version		2.1.2
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('JPATH_BASE') or die;

jimport('joomla.installer.helper');
/**
	override only _load()
*/
//require_once( dirname(__FILE__).DS.'extensionhelper.php' );




/**
 *
 *
 */
$version = new JVersion();
$joomlaVersion = $version->getShortVersion();
if($joomlaVersion < '1.6')
{
class PagesAndItemsInstallerHelper extends JInstallerHelper
{
	/**
	 * Unpacks a file and verifies it as a Joomla element package
	 * Supports .gz .tar .tar.gz and .zip
	 *
	 * @static
	 * @param string $p_filename The uploaded package filename or install directory
	 * @return Array Two elements - extractdir and packagefile
	 * @since 1.5
	 */
	function unpack($p_filename)
	{
		// Path to the archive
		$archivename = $p_filename;

		// Temporary folder to extract the archive into
		$tmpdir = uniqid('install_');

		// Clean the paths to use for archive extraction
		$extractdir = JPath::clean(dirname($p_filename).DS.$tmpdir);
		$archivename = JPath::clean($archivename);

		// do the unpacking of the archive
		$result = JArchive::extract( $archivename, $extractdir);

		if ( $result === false ) {
			return false;
		}


		/*
		 * Lets set the extraction directory and package file in the result array so we can
		 * cleanup everything properly later on.
		 */
		$retval['extractdir'] = $extractdir;
		$retval['packagefile'] = $archivename;

		/*
		 * Try to find the correct install directory.  In case the package is inside a
		 * subdirectory detect this and set the install directory to the correct path.
		 *
		 * List all the items in the installation directory.  If there is only one, and
		 * it is a folder, then we will set that folder to be the installation folder.
		 */
		$dirList = array_merge(JFolder::files($extractdir, ''), JFolder::folders($extractdir, ''));

		if (count($dirList) == 1)
		{
			if (JFolder::exists($extractdir.DS.$dirList[0]))
			{
				$extractdir = JPath::clean($extractdir.DS.$dirList[0]);
			}
		}

		/*
		 * We have found the install directory so lets set it and then move on
		 * to detecting the extension type.
		 */
		$retval['dir'] = $extractdir;

		/*
		 * Get the extension type and return the directory/type array on success or
		 * false on fail.
		 */
		if ($retval['type'] = PagesAndItemsInstallerHelper::detectType($extractdir))
		{
			return $retval;
		} else
		{
			return false;
		}
	}

	/**
	 * Method to detect the extension type from a package directory
	 *
	 * @static
	 * @param string $p_dir Path to package directory
	 * @return mixed Extension type string or boolean false on fail
	 * @since 1.5
	 */
	function detectType($p_dir)
	{
		// Search the install dir for an xml file
		$files = JFolder::files($p_dir, '\.xml$', 1, true);

		if (count($files) > 0)
		{

			foreach ($files as $file)
			{
				$xmlDoc = & JFactory::getXMLParser();
				$xmlDoc->resolveErrors(true);

				if (!$xmlDoc->loadXML($file, false, true))
				{
					// Free up memory from DOMIT parser
					unset ($xmlDoc);
					continue;
				}
				$root = & $xmlDoc->documentElement;
				if (!is_object($root) || $root->getTagName() != 'extension')
				{
					unset($xmlDoc);
					continue;
				}

				$type = $root->getAttribute('type');
				// Free up memory from DOMIT parser
				unset ($xmlDoc);
				return $type;
			}

			JError::raiseWarning(1, JText::_('ERRORNOTFINDJOOMLAXMLSETUPFILEX'));
			// Free up memory from DOMIT parser
			unset ($xmlDoc);
			return false;
		} else
		{
			JError::raiseWarning(1, JText::_('ERRORNOTFINDXMLSETUPFILEX'));
			return false;
		}
	}



}
}
else
{


class PagesAndItemsInstallerHelper extends JInstallerHelper
{

	/**
	 * Unpacks a file and verifies it as a Joomla element package
	 * Supports .gz .tar .tar.gz and .zip
	 *
	 * @static
	 * @param string $p_filename The uploaded package filename or install directory
	 * @return Array Two elements - extractdir and packagefile
	 * @since 1.5
	 */
	public static function unpack($p_filename)
	{
		// Path to the archive
		$archivename = $p_filename;

		// Temporary folder to extract the archive into
		$tmpdir = uniqid('install_');

		// Clean the paths to use for archive extraction
		$extractdir = JPath::clean(dirname($p_filename).DS.$tmpdir);
		$archivename = JPath::clean($archivename);

		// do the unpacking of the archive
		$result = JArchive::extract($archivename, $extractdir);

		if ($result === false) {
			return false;
		}


		/*
		 * Lets set the extraction directory and package file in the result array so we can
		 * cleanup everything properly later on.
		 */
		$retval['extractdir'] = $extractdir;
		$retval['packagefile'] = $archivename;

		/*
		 * Try to find the correct install directory.  In case the package is inside a
		 * subdirectory detect this and set the install directory to the correct path.
		 *
		 * List all the items in the installation directory.  If there is only one, and
		 * it is a folder, then we will set that folder to be the installation folder.
		 */
		$dirList = array_merge(JFolder::files($extractdir, ''), JFolder::folders($extractdir, ''));

		if (count($dirList) == 1)
		{
			if (JFolder::exists($extractdir.DS.$dirList[0]))
			{
				$extractdir = JPath::clean($extractdir.DS.$dirList[0]);
			}
		}

		/*
		 * We have found the install directory so lets set it and then move on
		 * to detecting the extension type.
		 */
		$retval['dir'] = $extractdir;

		/*
		 * Get the extension type and return the directory/type array on success or
		 * false on fail.
		 */
		//PagesAndItemsInstallerHelper::
		if ($retval['type'] = PagesAndItemsInstallerHelper::detectType($extractdir))  //self::detectType($extractdir))
		{
			return $retval;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to detect the extension type from a package directory
	 *
	 * @static
	 * @param string $p_dir Path to package directory
	 * @return mixed Extension type string or boolean false on fail
	 * @since 1.5
	 */
	public static function detectType($p_dir)
	{
		//echo $p_dir;
		// Search the install dir for an xml file
		$files = JFolder::files($p_dir, '\.xml$', 1, true);
		//$files = JFolder::files($p_dir, '\.xml$', 0, true);

		if ( ! count($files))
		{
			//echo 'no count';
			JError::raiseWarning(1, JText::_('JLIB_INSTALLER_ERROR_NOTFINDXMLSETUPFILE'));
			return false;
		}

		foreach ($files as $file)
		{
			//echo $file;
			if( ! $xml = JFactory::getXML($file))
			{
				continue;
			}
			/*

			0U:\web\In Arbeit\___Joomlas2Go-S1.6.0-FP3.0.5de-J1.6_NewPI\htdocs\joomlas2Go\tmp\install_4d832111771f5\itemtype_image_gallery_v1_5_0\contentelements\pi_subitem_image_gallery.xmlU:\web\In Arbeit\___Joomlas2Go-S1.6.0-FP3.0.5de-J1.6_NewPI\htdocs\joomlas2Go\tmp\install_4d832111771f5\itemtype_image_gallery_v1_5_0\contentelements\pi_subitem_image_gallery_images.xmlU:\web\In Arbeit\___Joomlas2Go-S1.6.0-FP3.0.5de-J1.6_NewPI\htdocs\joomlas2Go\tmp\install_4d832111771f5\itemtype_image_gallery_v1_5_0\image_gallery.xml
			*/
			//echo ' name: '.$xml->getName();
			if($xml->getName() != 'extension' && $xml->getName() != 'install')
			{
				unset($xml);
				continue;
			}
			//echo ' name2: '.$xml->getName();
			$type = (string)$xml->attributes()->type;
			// Free up memory
			unset ($xml);
			return $type;
		}
		//echo 'no file ';
		JError::raiseWarning(1, JText::_('JLIB_INSTALLER_ERROR_NOTFINDJOOMLAXMLSETUPFILE'));
		// Free up memory.
		unset ($xml);
		return false;
	}

}
}